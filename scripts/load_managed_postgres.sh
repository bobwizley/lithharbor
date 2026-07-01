#!/usr/bin/env bash
#
# One-shot restore of the migrated phpBB dump into the managed PostgreSQL 17
# over an SSH tunnel (issue #8). The managed database is only reachable through
# `ssh codelab`, so this opens a local port-forward to the platform Postgres,
# loads the deterministic dump with psql, then tears the tunnel down.
#
# MANUAL cutover step, run outside CI/CD — it is never part of the recurring
# deploy. Requires DB_PASSWORD set to the managed Postgres password.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

DUMP="${DUMP:-data/migration/lithharbor-pg17.sql}"
SSH_HOST="${SSH_HOST:-codelab}"
LOCAL_PORT="${LOCAL_PORT:-15432}"
DB_TUNNEL_HOST="${DB_TUNNEL_HOST:-postgres}"
DB_TUNNEL_PORT="${DB_TUNNEL_PORT:-5432}"

DB_NAME="${DB_NAME:-lithharbor}"
DB_USER="${DB_USER:-lithharbor}"
: "${DB_PASSWORD:?set DB_PASSWORD to the managed Postgres password}"

[ -f "$DUMP" ] || { echo "ERROR: dump not found at $DUMP — run scripts/dump_migrated_pg.sh first." >&2; exit 1; }
command -v psql >/dev/null || { echo "ERROR: psql client not installed locally." >&2; exit 1; }

CTL="$(mktemp -u -t lithharbor-tunnel-XXXXXX)"
cleanup() { ssh -S "$CTL" -O exit "$SSH_HOST" 2>/dev/null || true; }
trap cleanup EXIT

echo ">> Opening SSH tunnel 127.0.0.1:$LOCAL_PORT -> $DB_TUNNEL_HOST:$DB_TUNNEL_PORT via $SSH_HOST"
ssh -f -N -M -S "$CTL" -o ExitOnForwardFailure=yes \
  -L "127.0.0.1:$LOCAL_PORT:$DB_TUNNEL_HOST:$DB_TUNNEL_PORT" "$SSH_HOST"

echo ">> Restoring $DUMP into managed $DB_NAME"
PGPASSWORD="$DB_PASSWORD" psql \
  -h 127.0.0.1 -p "$LOCAL_PORT" -U "$DB_USER" -d "$DB_NAME" \
  -v ON_ERROR_STOP=1 -f "$DUMP"

echo ">> Restore complete. Verify row counts against the local source before go-live."
