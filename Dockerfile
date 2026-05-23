FROM php:8.2-fpm

ARG DEBIAN_FRONTEND=noninteractive

ENV LOG_CHANNEL=stderr
ENV LOG_LEVEL=debug

RUN apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        pkg-config \
        ca-certificates \
        git \
        unzip \
        curl \
        zip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libicu-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j1 gd pdo_mysql zip mbstring exif bcmath intl curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

RUN if [ ! -f .env ]; then touch .env; fi && \
    grep -q '^APP_KEY=' .env || echo 'APP_KEY=' >> .env

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# Start: run migrations then serve
# APP_KEY must be set in Render Environment.
CMD sh -lc 'php artisan config:clear; php artisan migrate --force --no-interaction && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}'