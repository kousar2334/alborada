FROM php:8.4-fpm


# Install system dependencies and PHP extensions commonly used by Laravel
RUN apt-get update && apt-get install -y \
git \
unzip \
libzip-dev \
libonig-dev \
libpng-dev \
libjpeg-dev \
libfreetype6-dev \
libicu-dev \
libxml2-dev \
zlib1g-dev \
libssl-dev \
&& docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mbstring bcmath intl xml


# Install composer (if you prefer to use composer image you can skip this)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Set working dir
WORKDIR /var/www/html


# Copy a php.ini if you want custom settings
COPY php.ini /usr/local/etc/php/conf.d/custom.ini


# Create www-data user permissions (optional)
RUN usermod -u 1000 www-data || true


# Expose
EXPOSE 9000
CMD ["php-fpm"]