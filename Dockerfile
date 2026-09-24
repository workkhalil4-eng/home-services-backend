FROM php:8.2-cli

ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    libpq-dev \
    unzip \
    curl \
    git \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    dos2unix \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_sqlite pdo_pgsql pcntl sockets zip mbstring xml intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Fix Windows line endings on shell scripts
RUN dos2unix start.sh start-reverb.sh && chmod +x start.sh start-reverb.sh

RUN mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --optimize-autoloader -v

EXPOSE 8000 8080
