FROM php:8.4-fpm

ARG DEBIAN_FRONTEND=noninteractive

# Make Laravel log to container logs (Render captures stderr)
ENV LOG_CHANNEL=stderr
ENV LOG_LEVEL=debug

# System dependencies
RUN set -eux; \
    apt-get -o Acquire::Retries=3 update; \
    apt-get install -y --no-install-recommends \
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
        libicu-dev; \
    rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN set -eux; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j1 \
        pdo_mysql \
        zip \
        gd \
        mbstring \
        exif \
        bcmath \
        xml \
        dom \
        simplexml \
        xmlreader \
        xmlwriter \
        intl \
        curl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Prepare .env (from example or empty)
RUN set -eux; \
    if [ -f .env.example ]; then cp .env.example .env; else touch .env; fi; \
    # Ensure APP_KEY exists so `php artisan key:generate` can update it (Laravel replaces, it won't add)
    grep -q '^APP_KEY=' .env || echo 'APP_KEY=' >> .env

# Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# Start command: run migrations (optional), then serve
# APP_KEY must be provided via Render Environment variables.
CMD sh -lc 'php artisan migrate --force --no-interaction || true; php artisan serve --host=0.0.0.0 --port=${PORT:-10000}'