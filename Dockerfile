FROM yiisoftware/yii2-php:8.3-fpm-nginx

USER root

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    libevent-dev

RUN install-php-extensions intl http

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app