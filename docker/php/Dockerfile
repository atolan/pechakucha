FROM php:7.3-fpm-alpine

RUN set -eux && \
  apk update && \
  apk add --update --no-cache \
    autoconf \
    gcc \
    g++ \
    make \
    tzdata \
    git && \
  apk add --update --no-cache \
    icu-dev \
    libzip-dev && \
  docker-php-ext-install intl iconv mysqli pdo_mysql mbstring zip bcmath && \
  pecl install redis && \
  docker-php-ext-enable redis

###
### gd
###
RUN set -eux && \
  apk add --update --no-cache \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev
RUN set -eux && \
  docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
  docker-php-ext-install -j$(nproc) gd exif

###
### time zone
###
ARG TZ

RUN set -eux && \
  cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
  echo ${TZ} > /etc/timezone

###
### xdebug
###
ARG APP_ENV

RUN set -eux && \
  if [ ${APP_ENV} == "local" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug \
  ;fi

###
### composer
###
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer

RUN set -eux && \
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
  composer config -g repos.packagist composer https://packagist.jp && \
  composer global require hirak/prestissimo
