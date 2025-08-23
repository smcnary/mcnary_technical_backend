FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    gd-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (with platform requirements ignored for missing extensions)
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create necessary directories
RUN mkdir -p var/cache var/logs var/sessions var/tmp \
    && chown -R www-data:www-data var \
    && chmod -R 777 var

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
