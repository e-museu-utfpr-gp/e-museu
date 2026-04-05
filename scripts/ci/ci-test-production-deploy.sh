#!/bin/bash

# Script to simulate CI job: test-production-deploy
# This script tests the production deployment

set -e

echo "=========================================="
echo "Running CI Job: test-production-deploy"
echo "=========================================="
echo ""

# Export environment variables
export APP_DEBUG=false
export APP_ENV=production
export APP_KEY=base64:p3uYMRjeoU/RPjt+wMOklJIn744PftwasNCVRwjfY60=
export APP_NAME=E-Museu
export APP_URL=http://emuseu.com
export DB_DATABASE=db_production
export DB_PASSWORD=password_production
export DB_ROOT_PASSWORD=password_root_production
export DB_USERNAME=emuseu_user
export DATABASE_URL="mysql://${DB_USERNAME}:${DB_PASSWORD}@db-production:3306/${DB_DATABASE}"

# Build and start production containers
echo ">> Building production Docker images..."
docker compose -f docker-compose.production.test.yml build

echo ""
echo ">> Starting production containers..."
docker compose -f docker-compose.production.test.yml up -d

echo ""
echo ">> Waiting for containers to be ready..."
sleep 10

echo ""
echo ">> Checking container status..."
docker compose -f docker-compose.production.test.yml ps

echo ""
echo ">> See containers status..."
docker compose -f docker-compose.production.test.yml ps

echo ""
echo ">> Validating containers are running..."
if docker compose -f docker-compose.production.test.yml ps | grep -q "Up"; then
  echo "✓ All containers are running"
else
  echo "ERROR: Containers are not running"
  exit 1
fi

echo ""
echo ">> Waiting for database to be ready..."
max_attempts=30
attempt=1
while [ $attempt -le $max_attempts ]; do
  if docker compose -f docker-compose.production.test.yml exec -T app-production php -r '$url = getenv("DATABASE_URL"); if ($url === false || $url === "") exit(1); $p = parse_url($url); if ($p === false || ($p["scheme"] ?? "") !== "mysql") exit(1); $host = $p["host"] ?? "127.0.0.1"; $port = (int) ($p["port"] ?? 3306); $user = rawurldecode($p["user"] ?? ""); $pass = rawurldecode($p["pass"] ?? ""); $dsn = "mysql:host=" . $host . ";port=" . $port; try { $pdo = new PDO($dsn, $user, $pass); $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); exit(0); } catch (Throwable $e) { exit(1); }' 2>/dev/null; then
    echo "✓ Database is ready!"
    break
  fi
  if [ $attempt -lt $max_attempts ]; then
    echo "Waiting for database... (attempt $attempt/$max_attempts)"
    sleep 2
  fi
  attempt=$((attempt + 1))
done

if [ $attempt -gt $max_attempts ]; then
  echo "ERROR: Database did not become ready after $max_attempts attempts"
  exit 1
fi

echo ""
echo ">> Running Laravel deployment commands..."
docker compose -f docker-compose.production.test.yml exec -T app-production php artisan config:clear
docker compose -f docker-compose.production.test.yml exec -T app-production php artisan config:cache
docker compose -f docker-compose.production.test.yml exec -T app-production php artisan route:cache
docker compose -f docker-compose.production.test.yml exec -T app-production php artisan migrate --force

echo ""
echo "=========================================="
echo "✓ Production deployment test completed successfully!"
echo "=========================================="
echo ""
echo ">> Cleaning up..."
docker compose -f docker-compose.production.test.yml down -v

echo ""
echo "✓ Cleanup completed!"
