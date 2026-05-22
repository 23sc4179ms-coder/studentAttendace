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

# Build/install required PHP extensions
RUN set -eux; \
	docker-php-ext-configure gd --with-freetype --with-jpeg; \
	docker-php-ext-install -j"$(nproc)" \
		pdo \
		pdo_mysql \
		zip \
		gd \
		mbstring \
		xml \
		dom \
		simplexml \
		xmlreader \
		xmlwriter \
		curl \
		intl \
		exif \
		bcmath; \
	docker-php-ext-enable gd mbstring xml dom simplexml xmlreader xmlwriter curl intl exif bcmath; \
	php -r "foreach(['gd','dom','xmlreader','xmlwriter','zip'] as $e){ if(!extension_loaded($e)){ fwrite(STDERR, 'Missing PHP extension: '.$e.PHP_EOL); exit(1);} }"


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY . .


RUN php --ini && php -m | sort | grep -E "^(gd|dom|xmlreader|xmlwriter|zip)$" 


RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress


RUN cp .env.example .env


RUN php artisan key:generate


EXPOSE 10000


# CMD php artisan serve --host=0.0.0.0 --port=10000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
