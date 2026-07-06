FROM php:8.4-fpm

# install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg2 \
    lsb-release \
    autoconf \
    make \
    gcc \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
# the rm -rf [etc] command will delete the package cache to keep image smaller

# install php extensions
RUN docker-php-ext-install pdo_mysql zip

# install composer -- this is a multi-stage copy which just copies the Composer binary from the official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 1. Copy ONLY the dependency manifest files
COPY composer.json composer.lock ./

# 2. Install dependencies (this layer is cached until composer files change)
RUN composer install --no-scripts --no-autoloader

# 3. Copy the rest of code
COPY . .

# 4. Finish composer setup
RUN composer dump-autoload --optimize