#!/usr/bin/env bash
#
# Seed phpBB user content (attachments + avatars) into the managed VPS
# bind-mount over SSH (issue #8). Targets the layout compose.yml binds:
# data/files and data/avatars under /opt/codelab/apps/lithharbor/data.
#
# Re-runnable: rsync --delete makes the remote reflect the local migration
# workspace. cache/ and store/ are regenerable and not seeded. MANUAL go-live
# step, not part of CI/CD. deploy-stack chowns the data dir to uid 1000 on
# deploy, so run a deploy after seeding (or chown 1000 on the VPS) so php-fpm
# owns the files.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

SOURCE_ROOT="${1:-www/forum}"
SSH_HOST="${SSH_HOST:-codelab}"
REMOTE_DATA="${REMOTE_DATA:-/opt/codelab/apps/lithharbor/data}"

for sub in files images/avatars/upload; do
  [ -d "$SOURCE_ROOT/$sub" ] || { echo "ERROR: missing source directory: $SOURCE_ROOT/$sub" >&2; exit 1; }
done

echo ">> Seeding attachments -> $SSH_HOST:$REMOTE_DATA/files/"
rsync -a --delete -e ssh "$SOURCE_ROOT/files/" "$SSH_HOST:$REMOTE_DATA/files/"

echo ">> Seeding avatars -> $SSH_HOST:$REMOTE_DATA/avatars/"
rsync -a --delete -e ssh "$SOURCE_ROOT/images/avatars/upload/" "$SSH_HOST:$REMOTE_DATA/avatars/"

echo ">> Seed complete. Ensure the VPS data dir is owned by uid 1000 (deploy-stack does this on deploy)."
