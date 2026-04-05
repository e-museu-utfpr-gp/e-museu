#!/usr/bin/env bash
# Tear down compose stacks for this repo, then match GitHub Actions jobs (tests + code-quality): docker-compose.local.yml.
# shellcheck disable=SC2034  # E_MUSEU_COMPOSE_FILE is read by ./run get_compose_file
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$REPO_ROOT" || exit 1

echo ">> docker compose down (local + prod-local stacks in this project)…"
docker compose -f docker-compose.local.yml down --remove-orphans 2>/dev/null || true
docker compose -f docker-compose.prod-local.yml down --remove-orphans 2>/dev/null || true

export E_MUSEU_COMPOSE_FILE=docker-compose.local.yml
echo ">> E_MUSEU_COMPOSE_FILE=$E_MUSEU_COMPOSE_FILE (same compose as CI)"
