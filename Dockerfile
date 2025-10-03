# Используем ваш образ как основу
FROM yiisoftware/yii2-php:8.3-fpm-nginx

# Переключаемся на пользователя root для установки пакетов
USER root

# Копируем скрипт для установки расширений PHP из официального образа
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Устанавливаем системные зависимости, которые могут понадобиться скрипту
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libicu-dev \
    # зависимости для http
    libcurl4-openssl-dev \
    libevent-dev

# Запускаем скрипт для установки нужных расширений
# Он сам разберется с версиями propro, raphf и pecl_http
RUN install-php-extensions intl http

# --- УСТАНОВКА COMPOSER ---
# Скачиваем официальный установщик Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- КОНЕЦ УСТАНОВКИ COMPOSER ---

# Очищаем кэш
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# (Опционально) Устанавливаем рабочую директорию
WORKDIR /app