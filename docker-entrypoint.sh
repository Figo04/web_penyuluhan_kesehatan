#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Starting server on port ${PORT:-8080}..."
exec php -S 0.0.0.0:${PORT:-8080} -t public
