#!/bin/sh
set -e

echo "==> Waiting for Laravel to be ready..."
until [ -f /var/www/html/artisan ]; do
    sleep 2
done

cd /var/www/html

echo "==> Starting Laravel queue worker..."
exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
