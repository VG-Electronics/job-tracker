#!/bin/sh
set -e

cd /var/www/html

if [ ! -f artisan ]; then
    echo "==> Installing Laravel..."
    composer create-project laravel/laravel /tmp/laravel --prefer-dist --no-interaction --quiet

    echo "==> Copying files..."
    cd /tmp/laravel && cp -rp . /var/www/html/
    rm -rf /tmp/laravel
    cd /var/www/html

    echo "==> Configuring .env..."
    cp .env.example .env
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
    sed -i "s/^# DB_HOST=.*/DB_HOST=${DB_HOST:-db}/" .env
    sed -i "s/^# DB_PORT=.*/DB_PORT=3306/" .env
    sed -i "s/^# DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE:-job_tracker}/" .env
    sed -i "s/^# DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME:-laravel}/" .env
    sed -i "s/^# DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD:-laravel}/" .env
    sed -i "s/^SESSION_DRIVER=.*/SESSION_DRIVER=file/" .env

    php artisan key:generate --quiet

    echo "==> Installing frontend dependencies..."
    npm install --silent
    npm install vue@latest @vitejs/plugin-vue --silent

    echo "==> Applying Job Tracker stubs..."
    cp /docker/stubs/routes/web.php routes/web.php
    cp /docker/stubs/resources/views/app.blade.php resources/views/app.blade.php
    cp /docker/stubs/resources/js/app.js resources/js/app.js
    cp /docker/stubs/resources/js/App.vue resources/js/App.vue
    cp /docker/stubs/vite.config.js vite.config.js
    rm -f resources/views/welcome.blade.php

    echo "==> Building assets..."
    npm run build --silent

    echo "==> Done. Job Tracker is ready at http://localhost:8000"
fi

echo "==> Fixing storage permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec php-fpm
