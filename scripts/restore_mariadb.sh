#!/usr/bin/env bash
#
# Rebuild the local ephemeral MariaDB from the latest production dump, loading
# ONLY the `lithharbor` database. The production dump is a --all-databases dump
# that also carries `codelab` and `mysql`; an awk filter forwards just the
# global session SETs (header) plus the `lithharbor` section, so the other two
# databases never reach the server.
#
# Idempotent and disposable: each run wipes the data volume and reloads from
# scratch. This is migration scaffolding, not a permanent dev database.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

COMPOSE=(docker compose -p lithharbor-dev -f compose.dev.yml)
CONTAINER="mariadb"
DB="lithharbor"

set -a
# shellcheck disable=SC1091
. ./.env
set +a

DUMP="$(ls -t data/dumps/all-*.sql | head -1)"
echo ">> Dump: $DUMP"

echo ">> Resetting ephemeral MariaDB (down -v)"
"${COMPOSE[@]}" down -v --remove-orphans 2>/dev/null || true
docker rm -f "$CONTAINER" 2>/dev/null || true
"${COMPOSE[@]}" up -d

echo ">> Waiting for MariaDB to become healthy"
for _ in $(seq 1 60); do
  [ "$(docker inspect -f '{{.State.Health.Status}}' "$CONTAINER" 2>/dev/null)" = "healthy" ] && break
  sleep 2
done
if [ "$(docker inspect -f '{{.State.Health.Status}}' "$CONTAINER" 2>/dev/null)" != "healthy" ]; then
  echo "ERROR: MariaDB did not become healthy within 120s" >&2
  exit 1
fi

echo ">> Loading $DB (codelab/mysql filtered out)"
awk -v target="$DB" '
  BEGIN { header = 1 }
  /^USE `/ {
    header = 0
    db = $0; sub(/^USE `/, "", db); sub(/`.*/, "", db)
    in_target = (db == target)
    if (in_target) print
    next
  }
  /^CREATE DATABASE/ {
    header = 0
    if (match($0, /`[^`]+`/)) {
      db = substr($0, RSTART + 1, RLENGTH - 2)
      if (db == target) print
    }
    next
  }
  { if (header || in_target) print }
' "$DUMP" | docker exec -i -e MYSQL_PWD="$MARIADB_ROOT_PASSWORD" "$CONTAINER" mariadb -uroot

echo ">> Verifying"
scripts/verify_restore.sh
