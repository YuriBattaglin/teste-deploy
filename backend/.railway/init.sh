#!/bin/bash
set -e

echo "🏗️ Rodando setup do Laravel..."

# Gera caches sem travar se não houver views
php artisan config:cache
php artisan route:cache
php artisan view:cache || true

# Rodar migrations se quiser
php artisan migrate --force || true

echo "✅ Setup finalizado!"
