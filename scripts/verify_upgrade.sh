#!/usr/bin/env bash
#
# Verify the phpBB 3.0.5 -> 3.3.17 upgrade on the local ephemeral MariaDB
# (issue #3). Asserts the upgrade invariants that can be checked from the
# database; the behavioural checks (boot/render, legacy login, ACP, search,
# stats resync) are the manual HITL checklist in
# docs/migration/02-upgrade-3.3.17.md.
#
# Assertions:
#   - board version is 3.3.17 and the final v3317 migration is recorded;
#   - the default style is prosilver and no user points at a missing style
#     (the discarded AeroBlack/subsilver2 styles are gone);
#   - every post was reparsed into s9e/TextFormatter (post_text is XML);
#   - posts and topics still match the dump exactly (the migration leaves
#     member content untouched);
#   - referential integrity holds: no private message without a recipient and
#     no post without a topic.
#
# Private-message and user counts legitimately differ from the dump and are
# reported, not asserted: phpBB's release_3_0_11_rc1 migration purges orphaned
# PMs (no recipient) and the bot_update migrations refresh the bot list. Both
# only touch already-broken or bot rows, never real member content.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

CONTAINER="mariadb"
DB="lithharbor"
EXPECTED_VERSION="3.3.17"

set -a
# shellcheck disable=SC1091
. ./.env
set +a

DUMP="${DUMP:-$(ls -t data/dumps/all-*.sql | head -1)}"

query() {
  docker exec -e MYSQL_PWD="$MARIADB_ROOT_PASSWORD" "$CONTAINER" mariadb -uroot -N -B "$DB" -e "$1"
}

dump_rows() { python3 scripts/count_dump_rows.py "$DUMP" "$1"; }

fail=0
check() { # label actual expected
  if [ "$2" = "$3" ]; then echo "OK:   $1 = $2"; else echo "FAIL: $1 = $2, expected $3"; fail=1; fi
}

check "board version" "$(query "SELECT config_value FROM phpbb_config WHERE config_name='version';")" "$EXPECTED_VERSION"
check "final migration v3317 recorded" "$(query "SELECT COUNT(*) FROM phpbb_migrations WHERE migration_name LIKE '%v3317';")" "1"

prosilver_id="$(query "SELECT style_id FROM phpbb_styles WHERE style_name='prosilver';")"
check "default style is prosilver" "$(query "SELECT config_value FROM phpbb_config WHERE config_name='default_style';")" "$prosilver_id"
check "users referencing a missing style" "$(query "SELECT COUNT(*) FROM phpbb_users WHERE user_style NOT IN (SELECT style_id FROM phpbb_styles);")" "0"

total_posts="$(query "SELECT COUNT(*) FROM phpbb_posts;")"
check "posts reparsed into s9e/TextFormatter" "$(query "SELECT COUNT(*) FROM phpbb_posts WHERE post_text LIKE '<r>%' OR post_text LIKE '<t>%';")" "$total_posts"

check "posts preserved vs dump"  "$total_posts"                                  "$(dump_rows phpbb_posts)"
check "topics preserved vs dump" "$(query "SELECT COUNT(*) FROM phpbb_topics;")" "$(dump_rows phpbb_topics)"

check "private messages without a recipient" "$(query "SELECT COUNT(*) FROM phpbb_privmsgs p LEFT JOIN phpbb_privmsgs_to t ON p.msg_id=t.msg_id WHERE t.msg_id IS NULL;")" "0"
check "posts without a topic" "$(query "SELECT COUNT(*) FROM phpbb_posts p LEFT JOIN phpbb_topics t ON p.topic_id=t.topic_id WHERE t.topic_id IS NULL;")" "0"

echo "INFO: members (user_type<>2) = $(query "SELECT COUNT(*) FROM phpbb_users WHERE user_type<>2;"), bots = $(query "SELECT COUNT(*) FROM phpbb_users WHERE user_type=2;") — bot list refreshed by bot_update migrations"
echo "INFO: private messages = $(query "SELECT COUNT(*) FROM phpbb_privmsgs;") (dump had $(dump_rows phpbb_privmsgs); orphans without a recipient purged by release_3_0_11_rc1)"

if [ "$fail" -ne 0 ]; then
  echo ">> UPGRADE VERIFICATION FAILED"
  exit 1
fi
echo ">> UPGRADE VERIFICATION PASSED"
