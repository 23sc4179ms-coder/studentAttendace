FROM php:8.2-fpm

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
        libsqlite3-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libicu-dev; \
    rm -rf /var/lib/apt/lists/*

# Install all required PHP extensions (including XML ones) in one step to avoid conflicts
RUN set -eux; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j1 \
        pdo_mysql \
        pdo_sqlite \
        zip \
        gd \
        mbstring \
        exif \
        bcmath \
        intl \
        curl \
        dom \
        xml \
        xmlreader \
        xmlwriter

# Verify required extensions are available
RUN set -eux; \
    php -m | sort; \
    php -r "foreach(['gd','zip','pdo_mysql','pdo_sqlite','dom','xmlreader','xmlwriter','mbstring'] as $e){ if(!extension_loaded($e)){ fwrite(STDERR, 'Missing PHP extension: '.$e.PHP_EOL); exit(1);} }"

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Prepare .env (ensure it exists and has APP_KEY placeholder)
RUN set -eux; \
    if [ -f .env.example ]; then cp .env.example .env; else touch .env; fi; \
    grep -q '^APP_KEY=' .env || echo 'APP_KEY=' >> .env

# SQLite fallback file (only used if DB_CONNECTION=sqlite)
RUN set -eux; \
    mkdir -p /var/www/database; \
    if [ ! -f /var/www/database/database.sqlite ]; then touch /var/www/database/database.sqlite; fi; \
    chown -R www-data:www-data /var/www/database

# Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# Start command: generate key if missing, run migrations, then serve
CMD php artisan key:generate --force --no-interaction && \
    php artisan migrate --force --no-interaction || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}