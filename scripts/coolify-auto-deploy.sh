#!/bin/sh
set -eu
# IMPORTANT: Dont delete this script or move this script, it is used by Coolify to deploy the application. Edit this script can break the auto deployment process.

# Polls GitHub for the tip SHA of a branch; if it changed, calls Coolify deploy API and persists the new SHA.
# Intended for Coolify "Scheduled Tasks" inside the app container. Requires git, curl.
# Environment (set by Coolify or .env injection):
#   COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL  e.g. https://github.com/org/repo.git
#   COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH    e.g. develop or main
#   COOLIFY_AUTO_DEPLOY_DEPLOY_URL       full deploy API URL (quote in Coolify if it contains &)
#   COOLIFY_AUTO_DEPLOY_TOKEN            Bearer token (tokens often contain | — quote in Coolify UI)
#   COOLIFY_AUTO_DEPLOY_TOKEN_FILE       optional; read token from file if TOKEN is empty
#   APP_ENV                             staging | production (state file under storage/coolify/)

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
APP_ROOT="$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd)"
cd "$APP_ROOT"

if [ -n "${COOLIFY_AUTO_DEPLOY_TOKEN_FILE:-}" ] && [ -z "${COOLIFY_AUTO_DEPLOY_TOKEN:-}" ]; then
  _tf="$COOLIFY_AUTO_DEPLOY_TOKEN_FILE"
  case "$_tf" in
    /*) ;;
    *) _tf="$APP_ROOT/$_tf" ;;
  esac
  if [ -r "$_tf" ]; then
    COOLIFY_AUTO_DEPLOY_TOKEN="$(tr -d '\r\n' < "$_tf")"
  fi
fi

if [ -z "${COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL:-}" ] \
  || [ -z "${COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH:-}" ] \
  || [ -z "${COOLIFY_AUTO_DEPLOY_DEPLOY_URL:-}" ] \
  || [ -z "${COOLIFY_AUTO_DEPLOY_TOKEN:-}" ]; then
  echo "coolify-auto-deploy: missing required configuration." >&2
  echo "  Need: COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL, COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH," >&2
  echo "        COOLIFY_AUTO_DEPLOY_DEPLOY_URL, COOLIFY_AUTO_DEPLOY_TOKEN (or COOLIFY_AUTO_DEPLOY_TOKEN_FILE)." >&2
  echo "  In Coolify YAML, unquoted & or | can break parsing — wrap DEPLOY_URL and TOKEN in double quotes if needed." >&2
  exit 1
fi

case "${APP_ENV:-}" in
  staging)
    STATE_BASENAME="last_sha_github_repo_staging"
    ;;
  production)
    STATE_BASENAME="last_sha_github_repo_production"
    ;;
  *)
    echo "coolify-auto-deploy: APP_ENV must be staging or production (got '${APP_ENV:-}')." >&2
    exit 1
    ;;
esac

STATE_DIR="$APP_ROOT/storage/coolify"
STATE_FILE="$STATE_DIR/$STATE_BASENAME"
mkdir -p "$STATE_DIR"

CURRENT_SHA="$(git ls-remote "$COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL" "refs/heads/$COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH" | awk '{print $1; exit}')"
LAST_SHA="$(cat "$STATE_FILE" 2>/dev/null || true)"

if [ -z "$CURRENT_SHA" ]; then
  echo "coolify-auto-deploy: failed to read remote SHA for branch $COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH." >&2
  exit 1
fi

if [ "$CURRENT_SHA" = "$LAST_SHA" ]; then
  echo "coolify-auto-deploy: no change on $COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH ($CURRENT_SHA)."
  exit 0
fi

echo "coolify-auto-deploy: commit changed (${LAST_SHA:-none} -> $CURRENT_SHA). Triggering Coolify deploy..."

curl -fsS --max-time 60 \
  -H "Authorization: Bearer $COOLIFY_AUTO_DEPLOY_TOKEN" \
  "$COOLIFY_AUTO_DEPLOY_DEPLOY_URL"

printf '%s\n' "$CURRENT_SHA" > "$STATE_FILE"
echo "coolify-auto-deploy: deploy requested; saved SHA to $STATE_FILE."
