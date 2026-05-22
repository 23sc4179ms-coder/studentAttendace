# FROM php:8.2-cli
FROM php:8.4-fpm

ARG DEBIAN_FRONTEND=noninteractive

# OS packages needed to compile PHP extensions required by composer deps
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

# Install all required PHP extensions in one step
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
        curl; \
    # Quick verification (optional, but helpful)
    php -m | grep -E "gd|zip|dom|xmlreader|xmlwriter|mbstring"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install Composer dependencies (without dev, optimised)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Set up environment and generate app key
RUN cp .env.example .env
RUN php artisan key:generate

EXPOSE 10000

# Start PHP built-in server with migrations (database must be available at runtime)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT