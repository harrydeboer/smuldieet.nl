FROM php:8.4-apache

ARG APP_ENV

RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install \
    git \
    sudo \
    nano \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    wget

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod headers rewrite ssl expires

RUN docker-php-ext-configure gd --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install bcmath curl exif mbstring xml intl zip pdo_mysql

RUN docker-php-ext-enable opcache

RUN if [ "$APP_ENV" = "dev" ] ; then pecl install xdebug && docker-php-ext-enable xdebug; fi

COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

COPY ./docker /var/www/html/docker
COPY ./config/preload.php /var/www/html/config/preload.php
COPY ./docker/web/apache2.conf /etc/apache2/apache2.conf
COPY ./docker/web/000-default.${APP_ENV}.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/web/php.ini /usr/local/etc/php/conf.d/php.ini

RUN if [ "$APP_ENV" = "dev" ] ; then cp ./docker/web/php-dev.ini /usr/local/etc/php/conf.d/php-dev.ini; else \
  cp ./docker/web/php-live.ini /usr/local/etc/php/conf.d/php-live.ini; fi

RUN if [ "$APP_ENV" = "dev" ] ; then \
    cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini;  else \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini; fi

RUN if [ "$APP_ENV" = "dev" ] ;  \
    then cp /var/www/html/docker/web/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; fi

RUN chown www-data:www-data -R /var/www/html/

RUN mkdir /var/www/.composer
RUN chown www-data:www-data -R /var/www/.composer
