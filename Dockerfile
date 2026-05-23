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

# Create all required Laravel directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Set the view compiled path explicitly in .env to avoid realpath issues
RUN echo "VIEW_COMPILED_PATH=/var/www/storage/framework/views" >> .env

# Clear any cached configuration that might point to the wrong path
RUN php artisan config:clear || true

EXPOSE 10000

# Runtime: ensure the view cache path is set and clear view cache before serving
CMD php artisan key:generate --force --no-interaction && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force --no-interaction || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}