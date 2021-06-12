FROM php:7.3.8-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /var/www/ponydays/
WORKDIR /var/www/ponydays/

RUN pecl install msgpack && docker-php-ext-enable msgpack

RUN apt-get update && apt-get install -y curl git unzip libgmp-dev libonig-dev libpng-dev libjpeg-dev build-essential libssl-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev curl

RUN docker-php-ext-install mysqli

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN docker-php-ext-enable mysqli gd

ENV COMPOSER_ALLOW_SUPERUSER 1