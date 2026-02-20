#!/bin/bash

# Script to simulate CI job: code-quality
# This script runs all code quality checks

set -e

echo "=========================================="
echo "Running CI Job: code-quality"
echo "=========================================="
echo ""

# Make run script executable
chmod +x ./run

# Setup environment
echo ">> Setting up environment..."
./run setup -env -y

echo ""
echo ">> See containers status..."
./run ps

echo ""
echo ">> Running code quality checks..."
echo ""

echo "Running PHP CodeSniffer..."
./run phpcs

echo ""
echo "Running Laravel Pint (style check)..."
./run pint-test

echo ""
echo "Running PHP Mess Detector..."
./run phpmd

echo ""
echo "Running PHPStan..."
./run phpstan

echo ""
echo "Running PHP Copy/Paste Detector..."
./run phpcpd

echo ""
echo "Running ESLint..."
./run eslint

echo ""
echo "Running Prettier check..."
./run prettier-check

echo ""
echo "=========================================="
echo "✓ All code quality checks passed!"
echo "=========================================="
echo ""
echo ">> Cleaning up..."
./run down || true

echo ""
echo "✓ Cleanup completed!"
