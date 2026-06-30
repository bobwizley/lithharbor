#!/usr/bin/env bash
#
# Verify the local MariaDB restore against the production dump:
#   - the `codelab` database is absent (only `lithharbor` was loaded)
#   - `lithharbor` has the 62 core phpBB 3.0.x tables
#   - high-value table row counts match the dump (counted SQL-aware, not by load)
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

CONTAINER="mariadb"
DB="lithharbor"
EXPECTED_PHPBB_TABLES=62
HIGH_VALUE_TABLES=(phpbb_users phpbb_posts phpbb_topics phpbb_privmsgs)

set -a
# shellcheck disable=SC1091
. ./.env
set +a

DUMP="$(ls -t data/dumps/all-*.sql | head -1)"

query() {
  docker exec -e MYSQL_PWD="$MARIADB_ROOT_PASSWORD" "$CONTAINER" mariadb -uroot -N -B -e "$1"
}

fail=0

if [ -n "$(query "SHOW DATABASES LIKE 'codelab';")" ]; then
  echo "FAIL: database 'codelab' is present (should have been filtered out)"
  fail=1
else
  echo "OK:   database 'codelab' absent"
fi

phpbb_tables="$(query "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB' AND table_name LIKE 'phpbb\\_%';")"
if [ "$phpbb_tables" -eq "$EXPECTED_PHPBB_TABLES" ]; then
  echo "OK:   $DB has $phpbb_tables phpbb_ core tables"
else
  echo "FAIL: $DB has $phpbb_tables phpbb_ core tables, expected $EXPECTED_PHPBB_TABLES"
  fail=1
fi

# The production DB also carries leftover flarum_ tables from an abandoned forum
# (out of scope for the phpBB migration); report them but do not fail on them.
flarum_tables="$(query "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB' AND table_name LIKE 'flarum\\_%';")"
echo "INFO: $DB also has $flarum_tables leftover flarum_ tables (out of scope)"

for t in "${HIGH_VALUE_TABLES[@]}"; do
  loaded="$(query "SELECT COUNT(*) FROM \`$DB\`.\`$t\`;")"
  expected="$(python3 scripts/count_dump_rows.py "$DUMP" "$t")"
  if [ "$loaded" -eq "$expected" ]; then
    echo "OK:   $t = $loaded (matches dump)"
  else
    echo "FAIL: $t loaded=$loaded dump=$expected"
    fail=1
  fi
done

if [ "$fail" -ne 0 ]; then
  echo ">> VERIFICATION FAILED"
  exit 1
fi
echo ">> VERIFICATION PASSED"
