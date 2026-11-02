FROM php:8.5-fpm

# unzip is used by composer during dependency installation
# pdo_mysqli and mysqli extensions needed for database connection
RUN apt update \
    && apt install -y unzip \
    && docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

# clean up apt cache to reduce image size
RUN apt clean && rm -rf /var/lib/apt/lists/*

# compoy composer to image to be able to install dependencies
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer