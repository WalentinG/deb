# syntax=docker/dockerfile:1
FROM arm64v8/php:8.1-cli-alpine3.16 AS base
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

FROM base AS deps
WORKDIR /var/www/backend
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
COPY . .
RUN composer install

FROM base AS runner
WORKDIR /var/www/backend
COPY --from=deps /var/www/backend .
