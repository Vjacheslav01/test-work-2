FROM php:8.3-fpm-alpine

# Установка runtime-зависимостей и build-зависимостей в одном слое
RUN set -eux; \
    apk add --update --no-cache \
        imagemagick \
        imagemagick-libs \
        freetype \
        libjpeg-turbo \
        libpng \
    && apk add --update --no-cache --virtual .build-deps \
        autoconf \
        g++ \
        imagemagick-dev \
        libtool \
        make \
        pcre-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    # Установка PHP расширений
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && pecl install imagick \
    && docker-php-ext-enable imagick gd \
    # Очистка
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www

# Копирование и установка зависимостей Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

COPY . .