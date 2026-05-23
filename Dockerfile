FROM php:8.2-fpm

ARG DEBIAN_FRONTEND=noninteractive

# Install system dependencies
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

# Install Node.js (required for building assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j1 gd pdo_mysql zip mbstring exif bcmath intl curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Install Node dependencies and build frontend assets
RUN npm install && npm run build

# Create necessary directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Runtime: ensure view cache directory exists, then start server
CMD mkdir -p /var/www/storage/framework/views && \
    export VIEW_COMPILED_PATH=/var/www/storage/framework/views && \
    php artisan key:generate --force --no-interaction && \
    php artisan view:clear && \
    php artisan migrate --force --no-interaction || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}