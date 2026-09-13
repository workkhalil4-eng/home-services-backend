FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo_sqlite pcntl sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000 8080
