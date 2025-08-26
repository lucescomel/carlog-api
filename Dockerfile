# Dockerfile
FROM php:8.3-apache

# Linux deps
RUN apt-get update && apt-get install -y \
    libicu-dev libpq-dev git unzip libzip-dev \
 && docker-php-ext-install intl pdo_pgsql opcache zip \
 && a2enmod rewrite headers

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Prod install
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && php bin/console assets:install --no-interaction || true

# Symfony prod env
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Apache vhost (public/ comme docroot)
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
# Script de lancement: migrations, cache, puis Apache
CMD php bin/console doctrine:migrations:migrate --no-interaction || true \
 && php bin/console cache:clear --env=prod \
 && apache2-foreground