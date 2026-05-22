# Laravel (PHP 8.2) production Dockerfile
# - Builds PHP dependencies with Composer
# - Builds Vite assets with Node
# - Runs the app on Apache with DocumentRoot=/public

# 1) Composer dependencies
FROM composer:2 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# 2) Vite build (frontend)
FROM node:20-alpine AS vite_build
WORKDIR /app
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY vite.config.js ./
COPY resources ./resources
# Ensure output directory exists
RUN mkdir -p public
RUN npm run build

# 3) Runtime
FROM php:8.2-apache

# System deps + PHP extensions commonly needed by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Point Apache to Laravel's /public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# App code
COPY . /var/www/html

# Bring in built dependencies/assets
COPY --from=composer_deps /app/vendor /var/www/html/vendor
COPY --from=vite_build /app/public/build /var/www/html/public/build

# Permissions for Laravel runtime
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Note:
# - Provide APP_KEY and DB_* env vars at runtime.
# - Run migrations manually (e.g., docker exec ... php artisan migrate --force).
