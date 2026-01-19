# Multi-stage Dockerfile for MPWA (Laravel + Node.js)
FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    nginx \
    supervisor \
    nodejs \
    npm \
    bash \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libavif-dev \
    icu-dev \
    icu-libs \
    icu-data-full

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-avif \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mysqli zip gd exif pcntl bcmath opcache intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm ci --production

# Copy application files
COPY . .

# Copy .env.example as .env if .env doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/credentials \
    && chown -R www-data:www-data /var/www/html/credentials \
    && chmod -R 755 /var/www/html/credentials \
    && mkdir -p /var/log/supervisor

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy PHP-FPM configuration
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports
EXPOSE 80 3100

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
