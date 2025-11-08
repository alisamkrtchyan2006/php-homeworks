# syntax=docker/dockerfile:1

FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y libxml2-dev \
    libzip-dev \
    libicu-dev \
    librabbitmq-dev\
    zlib1g-dev \
    libpng-dev\
    linux-headers-generic\
    libpq-dev \
    &&  rm -rf /tmp/pear \
    && docker-php-ext-enable opcache\
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql\
    && docker-php-ext-install -j$(nproc)  intl zip;

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["docker-php-entrypoint"]
WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]