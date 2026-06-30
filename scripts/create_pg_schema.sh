#!/usr/bin/env bash
#
# Generate the canonical phpBB 3.3.17 PostgreSQL schema by running a clean
# official phpBB installer against the local PostgreSQL 17 container.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

COMPOSE=(docker compose -p lithharbor-dev -f compose.dev.yml)
POSTGRES_CONTAINER="postgres"
IMAGE="lithharbor-phpbb-cli"
PHPBB_PACKAGE_ROOT="${PHPBB_PACKAGE_ROOT:-data/phpbb/phpBB3}"
OUT_DIR="data/migration"
SCHEMA_DUMP="$OUT_DIR/pg17-canonical-schema.sql"

set -a
# shellcheck disable=SC1091
. ./.env
set +a
DB_NAME="${DB_NAME:-lithharbor}"
DB_USER="${DB_USER:-lithharbor}"
DB_PASSWORD="${DB_PASSWORD:-dev}"

if [ ! -f "$PHPBB_PACKAGE_ROOT/install/phpbbcli.php" ]; then
  echo "ERROR: missing official phpBB installer at $PHPBB_PACKAGE_ROOT/install/phpbbcli.php" >&2
  echo "Extract phpBB-3.3.17.zip so PHPBB_PACKAGE_ROOT points at the package's phpBB3 directory." >&2
  exit 1
fi
PHPBB_PACKAGE_ROOT="$(cd "$PHPBB_PACKAGE_ROOT" && pwd)"

echo ">> Building $IMAGE (php 7.4 cli with mysqli and pgsql extensions)"
docker build -t "$IMAGE" -f scripts/phpbb-cli.Dockerfile scripts/

echo ">> Starting PostgreSQL 17"
"${COMPOSE[@]}" up -d postgres

echo ">> Waiting for PostgreSQL to become healthy"
for _ in $(seq 1 60); do
  [ "$(docker inspect -f '{{.State.Health.Status}}' "$POSTGRES_CONTAINER" 2>/dev/null)" = "healthy" ] && break
  sleep 2
done
if [ "$(docker inspect -f '{{.State.Health.Status}}' "$POSTGRES_CONTAINER" 2>/dev/null)" != "healthy" ]; then
  echo "ERROR: PostgreSQL did not become healthy within 120s" >&2
  exit 1
fi

echo ">> Resetting public schema"
docker exec -e PGPASSWORD="$DB_PASSWORD" "$POSTGRES_CONTAINER" \
  psql -U "$DB_USER" -d "$DB_NAME" -v ON_ERROR_STOP=1 \
  -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"

INSTALL_CONFIG="$(mktemp -t lithharbor-install-XXXXXX.yml)"
cat >"$INSTALL_CONFIG" <<YAML
installer:
  admin:
    name: admin
    password: adminadmin
    email: admin@example.org
  board:
    lang: en
    name: Lith Harbor
    description: Local PostgreSQL schema seed
  database:
    dbms: postgres
    dbhost: $POSTGRES_CONTAINER
    dbport: 5432
    dbuser: $DB_USER
    dbpasswd: "$DB_PASSWORD"
    dbname: $DB_NAME
    table_prefix: phpbb_
  email:
    enabled: false
  server:
    cookie_secure: false
    server_protocol: http://
    force_server_vars: false
    server_name: localhost
    server_port: 80
    script_path: /
YAML

echo ">> Running official phpBB installer against PostgreSQL"
docker run --rm \
  --network lithharbor-dev_postgres \
  -u "$(id -u):$(id -g)" \
  -v "$PHPBB_PACKAGE_ROOT":/var/www/html \
  -v "$INSTALL_CONFIG":/tmp/install-config.yml:ro \
  "$IMAGE" php install/phpbbcli.php install /tmp/install-config.yml

phpbb_table_count="$(docker exec -e PGPASSWORD="$DB_PASSWORD" "$POSTGRES_CONTAINER" \
  psql -U "$DB_USER" -d "$DB_NAME" -At -v ON_ERROR_STOP=1 \
  -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND table_name LIKE 'phpbb\\_%' ESCAPE '\\';")"
if [ "$phpbb_table_count" -eq 0 ]; then
  echo "ERROR: phpBB installer completed without creating phpbb_ tables" >&2
  exit 1
fi

mkdir -p "$OUT_DIR"
echo ">> Writing canonical schema dump to $SCHEMA_DUMP"
docker exec -e PGPASSWORD="$DB_PASSWORD" "$POSTGRES_CONTAINER" \
  pg_dump -U "$DB_USER" -d "$DB_NAME" --schema-only --no-owner --no-privileges \
  >"$SCHEMA_DUMP"

echo ">> Canonical PG17 schema ready"
