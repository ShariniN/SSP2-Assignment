# Use multi-stage build for smaller final image
FROM php:8.2-apache as builder

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libonig-dev libcurl4-openssl-dev \
    unzip git nodejs npm \
    && docker-php-ext-install zip pdo_mysql gd \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean

WORKDIR /var/www/html

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy package files
COPY composer.json composer.lock package.json package-lock.json* ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
RUN npm ci && npm run build

# Final stage
FROM php:8.2-apache

# Install only runtime dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install zip pdo_mysql gd \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && apt-get clean

WORKDIR /var/www/html

# Copy from builder
COPY --from=builder /var/www/html/vendor ./vendor
COPY --from=builder /var/www/html/node_modules ./node_modules
COPY --from=builder /var/www/html/public/build ./public/build

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache config
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 8080
CMD ["apache2-foreground"]