# FROM php:8.2-cli
FROM php:8.4-fpm


RUN apt-get update && apt-get install -y \
git unzip curl libzip-dev zip \
libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
libonig-dev libxml2-dev libcurl4-openssl-dev libicu-dev \
&& docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install pdo pdo_mysql zip gd mbstring xml curl intl exif \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/*


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY . .


RUN composer install --no-dev --optimize-autoloader


RUN cp .env.example .env


RUN php artisan key:generate


EXPOSE 10000


# CMD php artisan serve --host=0.0.0.0 --port=10000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
