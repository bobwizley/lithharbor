#!/usr/bin/env bash
#
# Generate the deterministic, portable pg_dump artifact of the migrated phpBB
# database from the local ephemeral PostgreSQL 17 (issue #8). Run this only
# after scripts/transplant_to_postgres.sh and the parity harness pass.
#
# The dump is role-agnostic (--no-owner --no-privileges) and re-runnable
# (--clean --if-exists), so it restores cleanly into the managed Postgres
# regardless of the connecting role and can be replayed into a dirty target.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

POSTGRES_CONTAINER="postgres"
OUT_DIR="data/migration"
DUMP="$OUT_DIR/lithharbor-pg17.sql"

set -a
# shellcheck disable=SC1091
. ./.env
set +a
DB_NAME="${DB_NAME:-lithharbor}"
DB_USER="${DB_USER:-lithharbor}"
DB_PASSWORD="${DB_PASSWORD:-dev}"

phpbb_table_count="$(docker exec -e PGPASSWORD="$DB_PASSWORD" "$POSTGRES_CONTAINER" \
  psql -U "$DB_USER" -d "$DB_NAME" -At -v ON_ERROR_STOP=1 \
  -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND table_name LIKE 'phpbb\\_%' ESCAPE '\\';")"
if [ "${phpbb_table_count:-0}" -eq 0 ]; then
  echo "ERROR: no phpbb_ tables in $DB_NAME — run scripts/transplant_to_postgres.sh first." >&2
  exit 1
fi

mkdir -p "$OUT_DIR"
echo ">> Dumping migrated $DB_NAME ($phpbb_table_count phpbb_ tables) from $POSTGRES_CONTAINER"
docker exec -e PGPASSWORD="$DB_PASSWORD" "$POSTGRES_CONTAINER" \
  pg_dump -U "$DB_USER" -d "$DB_NAME" \
  --format=plain --no-owner --no-privileges --clean --if-exists --encoding=UTF8 \
  >"$DUMP"

echo ">> Wrote $DUMP ($(wc -l <"$DUMP") lines)"
echo ">> Load it into the managed Postgres with scripts/load_managed_postgres.sh"
