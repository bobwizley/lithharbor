#!/usr/bin/env bash
set -euo pipefail

SOURCE_ROOT="${1:-www/forum}"
TARGET_ROOT="${2:-data/forum-state}"

require_dir() {
  local path="$1"
  if [[ ! -d "$path" ]]; then
    echo "ERROR: missing source directory: $path" >&2
    exit 1
  fi
}

require_dir "$SOURCE_ROOT/files"
require_dir "$SOURCE_ROOT/images/avatars/upload"

mkdir -p "$TARGET_ROOT/files" "$TARGET_ROOT/images/avatars/upload" "$TARGET_ROOT/cache" "$TARGET_ROOT/store"

rsync -a --delete "$SOURCE_ROOT/files/" "$TARGET_ROOT/files/"
rsync -a --delete "$SOURCE_ROOT/images/avatars/upload/" "$TARGET_ROOT/images/avatars/upload/"

echo "Seeded phpBB user content into $TARGET_ROOT"
echo "Regenerable writable directories are present: cache, store"
