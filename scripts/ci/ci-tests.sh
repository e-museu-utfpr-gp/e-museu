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

# Setup environment and run tests
echo ">> Setting up environment..."
./run setup -env -y

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
