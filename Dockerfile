# -------------------------------
# Stage 1: Build Frontend Assets
# -------------------------------
FROM node:20-alpine AS frontend

WORKDIR /build

COPY package*.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# -------------------------------
# Stage 2: Install PHP Dependencies  
# -------------------------------
FROM composer:2 AS composer

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .build-deps

WORKDIR /build

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative

# -------------------------------
# Stage 3: Final Runtime Image
# -------------------------------
FROM php:8.2-apache

# Install PHP extensions (this will pull in necessary runtime libraries)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get purge -y --auto-remove libssl-dev pkg-config libcurl4-openssl-dev libpng-dev libonig-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable Apache modules
RUN a2enmod rewrite headers

WORKDIR /var/www/html

# Copy vendor and built assets
COPY --from=composer --chown=www-data:www-data /build/vendor ./vendor
COPY --from=frontend /build/public/build ./public/build

# Copy application code
COPY --chown=www-data:www-data . .

# Create necessary directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Configure Apache for Laravel
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Create Apache config that uses PORT variable
RUN echo '<VirtualHost *:${PORT}>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create startup script that handles PORT
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Get PORT from environment or use 80\n\
export PORT=${PORT:-80}\n\
\n\
echo "Starting Apache on port $PORT"\n\
\n\
# Update Apache to listen on PORT\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
\n\
# Replace PORT variable in VirtualHost\n\
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose port (Railway will override)
EXPOSE 80

# Start with our custom script
CMD ["/start.sh"]