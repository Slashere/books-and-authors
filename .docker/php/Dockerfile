FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
      vim \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libcurl4-openssl-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql gd curl

WORKDIR /var/www/html