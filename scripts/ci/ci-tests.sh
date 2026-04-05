#!/bin/bash

# Script to simulate CI job: tests
# This script runs unit and feature tests

set -e

echo "=========================================="
echo "Running CI Job: tests"
echo "=========================================="
echo ""

# Make run script executable
chmod +x ./run

# Same as GitHub Actions: stop any stack, then always docker-compose.local.yml (not APP_ENV prod-local).
# shellcheck disable=SC1091
. "$(dirname "$0")/prepare-ci-compose.sh"

# Setup environment and run tests
echo ">> Setting up environment..."
./run setup -y

echo ""
echo ">> Running tests..."
./run test

echo ""
echo ">> See containers status..."
./run ps

echo ""
echo "=========================================="
echo "✓ Tests job completed successfully!"
echo "=========================================="
echo ""
echo ">> Cleaning up..."
./run down || true

echo ""
echo "✓ Cleanup completed!"
