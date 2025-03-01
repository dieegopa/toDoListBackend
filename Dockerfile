# Usa una imagen oficial de PHP con extensiones necesarias
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

ENV TZ=UTC

USER root

RUN apk update && apk add \
    nginx \
    zlib-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    imagemagick-dev

RUN docker-php-ext-install \
    gd \
    pcntl \
    bcmath \
    mysqli \
    pdo_mysql

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN pecl install zip && docker-php-ext-enable zip \
    && pecl install igbinary && docker-php-ext-enable igbinary

RUN pecl install redis && docker-php-ext-enable redis

RUN pecl install imagick && docker-php-ext-enable imagick

EXPOSE 80

COPY . /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-interaction --optimize-autoloader

RUN rm /usr/local/etc/php-fpm.d/*
COPY ./docker/php-fpm-www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/default.conf /etc/nginx/conf.d/default.conf

COPY ./docker/supervisord.conf /etc/supervisord.conf

CMD ["./start.sh"]
