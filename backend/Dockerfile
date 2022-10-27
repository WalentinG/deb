FROM arm64v8/php:8.1-cli-alpine3.16

RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-install \
        pcntl \
        pdo_mysql

RUN apk update \
    && apk add --no-cache autoconf build-base \
    && pecl install redis \
    && docker-php-ext-enable redis

# Setup GD extension
RUN apk add --no-cache \
  freetype \
  libjpeg-turbo \
  libpng \
  freetype-dev \
  libjpeg-turbo-dev \
  libpng-dev \
&& docker-php-ext-configure gd \
  --with-freetype=/usr/include/ \
  # --with-png=/usr/include/ \ # No longer necessary as of 7.4; https://github.com/docker-library/php/pull/910#issuecomment-559383597
  --with-jpeg=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd \
&& docker-php-ext-enable gd \
&& apk del --no-cache \
  freetype-dev \
  libjpeg-turbo-dev \
  libpng-dev \
&& rm -rf /tmp/*
