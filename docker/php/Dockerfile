FROM php:8.5-fpm-alpine

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

RUN apk add --no-cache \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

RUN docker-php-ext-install pdo_mysql mbstring xml bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /entrypoint.sh
COPY docker/php/scheduler-entrypoint.sh /scheduler-entrypoint.sh
COPY docker/php/queue-entrypoint.sh /queue-entrypoint.sh
COPY docker/php/stubs /docker/stubs
RUN chmod +x /entrypoint.sh /scheduler-entrypoint.sh /queue-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
