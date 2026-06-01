#!/bin/sh
set -e

echo "==> Waiting for Laravel to be ready..."
until [ -f /var/www/html/artisan ]; do
    sleep 2
done

cd /var/www/html

echo "==> Starting Laravel scheduler..."
exec php artisan schedule:work
