FROM php:8.2-cli

# Prevent interactive prompts during build
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Install dependencies including libicu-dev for intl (required by Filament)
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    unzip \
    curl \
    git \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_sqlite pcntl sockets zip mbstring xml intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Create the sqlite database file before composer install in case post-autoload scripts need it
RUN mkdir -p database && touch database/database.sqlite

# Run composer install with verbose output to see any hidden errors
RUN composer install --no-dev --optimize-autoloader -v

EXPOSE 8000 8080
