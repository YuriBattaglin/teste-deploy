#!/usr/bin/env bash
set -euo pipefail

MAX_RETRIES=${MAX_RETRIES:-60}
RETRY_COUNT=0

echo "Waiting for MySQL (via PDO) at ${DB_HOST}:${DB_PORT}..."
until php -r '
$h=getenv("DB_HOST"); $p=getenv("DB_PORT") ?: 3306; $u=getenv("DB_USERNAME"); $pw=getenv("DB_PASSWORD"); 
try { new PDO("mysql:host={$h};port={$p}", $u, $pw, [PDO::ATTR_TIMEOUT=>2]); exit(0); } 
catch (Throwable $e) { exit(1); }'; do
  RETRY_COUNT=$((RETRY_COUNT+1))
  if [ "$RETRY_COUNT" -ge "$MAX_RETRIES" ]; then
    echo "MySQL is unavailable after ${MAX_RETRIES} attempts, aborting." >&2
    exit 1
  fi
  echo "Waiting for MySQL... (${RETRY_COUNT}/${MAX_RETRIES})"
  sleep 1
done

php artisan config:clear
php artisan migrate --force
php artisan tenants:migrate --force
php artisan setup:admin-tenant || true
php artisan optimize

exec "$@"
