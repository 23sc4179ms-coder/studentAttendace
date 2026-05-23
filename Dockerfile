FROM php:8.2-fpm

ARG DEBIAN_FRONTEND=noninteractive

# Laravel logs to stderr (captured by Render)
ENV LOG_CHANNEL=stderr
ENV LOG_LEVEL=debug

# Install system dependencies (only what's needed for MySQL)
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

# Configure & install gd (with JPEG/PNG support)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install other essential extensions (separate runs for clarity & debugging)
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) mbstring
RUN docker-php-ext-install -j$(nproc) exif bcmath intl curl

# Verify extensions (only those needed)
RUN php -m | grep -E 'gd|zip|pdo_mysql|mbstring'

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install Composer dependencies (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Create .env if missing, ensure APP_KEY placeholder exists
RUN if [ ! -f .env ]; then touch .env; fi && \
    grep -q '^APP_KEY=' .env || echo 'APP_KEY=' >> .env

# Set permissions for Laravel storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# Start command – generate key if missing, run migrations, serve
CMD php artisan key:generate --force --no-interaction && \
    php artisan migrate --force --no-interaction || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}