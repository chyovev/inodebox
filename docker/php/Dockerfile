FROM php:8.5-fpm

# unzip is used by composer during dependency installation
RUN apt update && apt install -y unzip

# clean up apt cache to reduce image size
RUN apt clean && rm -rf /var/lib/apt/lists/*

# compoy composer to image to be able to install dependencies
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer