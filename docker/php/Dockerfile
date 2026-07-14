FROM php:8.4-fpm

# Install system dependencies and PHP extensions
# libwebp-dev + `gd --with-webp` are what make imagewebp() exist. Without them GD
# builds without WebP support, MediaRepository's conversion silently no-ops, and
# every upload is served in its original (much heavier) format.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    zlib1g-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
       gd pdo pdo_mysql zip mbstring bcmath intl xml dom \
       pcntl opcache curl exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html

# Copy custom PHP settings
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Align www-data UID with host user (avoids permission issues on volume mounts)
RUN usermod -u 1000 www-data || true

EXPOSE 9000
CMD ["php-fpm"]