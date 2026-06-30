#!/usr/bin/env bash
#
# Run the official phpBB 3.3.17 updater against the local ephemeral MariaDB
# (issue #3). With the 3.3.17 vanilla files in www/forum pointing at the
# restored 3.0.5 database, this:
#   1. migrates the schema 3.0.5 -> 3.3.17 (db:migrate creates phpbb_migrations
#      and applies every cumulative migration; --safe-mode isolates the core
#      from any leftover MOD/extension code);
#   2. reparses old BBCode/smilies/links into s9e/TextFormatter (separate step,
#      not done by db:migrate);
#   3. purges the cache.
#
# Counters/statistics resync and the search-index rebuild have no core CLI in
# 3.3 and are done from the ACP (see docs/migration/02-upgrade-3.3.17.md).
#
# Idempotent: db:migrate and reparser:reparse are safe to re-run. Requires the
# ephemeral MariaDB from scripts/restore_mariadb.sh to be up and healthy.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

IMAGE="lithharbor-phpbb-cli"
CONTAINER="mariadb"
NETWORK="lithharbor-dev_mariadb"
FORUM="$REPO_ROOT/www/forum"

set -a
# shellcheck disable=SC1091
. ./.env
set +a

if ! docker image inspect "$IMAGE" >/dev/null 2>&1; then
  echo ">> Building $IMAGE (php 7.4 cli, prod extension set)"
  docker build -t "$IMAGE" -f scripts/phpbb-cli.Dockerfile scripts/
fi

# The dump restores only the `lithharbor` database, not the mysql user grants,
# so the app DB user must be (re)created for the phpBB CLI to connect over TCP.
echo ">> Ensuring DB user '$DB_USER' can reach '$DB_NAME'"
docker exec -i -e MYSQL_PWD="$MARIADB_ROOT_PASSWORD" "$CONTAINER" mariadb -uroot <<SQL
CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASSWORD';
ALTER USER '$DB_USER'@'%' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'%';
FLUSH PRIVILEGES;
SQL

run_cli() {
  docker run --rm \
    --network "$NETWORK" \
    -u "$(id -u):$(id -g)" \
    -e DB_HOST="$CONTAINER" -e DB_PORT=3306 \
    -e DB_NAME="$DB_NAME" -e DB_USER="$DB_USER" -e DB_PASSWORD="$DB_PASSWORD" \
    -v "$FORUM":/var/www/html \
    "$IMAGE" php bin/phpbbcli.php "$@"
}

echo ">> [1/3] Migrating schema 3.0.5 -> 3.3.17 (db:migrate --safe-mode)"
run_cli --safe-mode db:migrate

echo ">> [2/3] Reparsing old posts into s9e/TextFormatter (reparser:reparse)"
run_cli reparser:reparse

echo ">> [3/3] Purging cache"
run_cli cache:purge

echo ">> Upgrade complete. Verify with: scripts/verify_upgrade.sh"
