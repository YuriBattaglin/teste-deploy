FROM php:8.1-cli

RUN apt-get update \
 && apt-get install -y git unzip libzip-dev libonig-dev libpng-dev libxml2-dev default-mysql-client \
 && docker-php-ext-install pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY . .
COPY .env .env

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
