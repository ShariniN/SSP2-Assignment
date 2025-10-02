# -------------------------------
# Stage 1: Build Frontend Assets
# -------------------------------
FROM node:20-alpine AS frontend

WORKDIR /build

# Copy package files
COPY package*.json ./

# Install ALL dependencies (including devDependencies for Vite)
RUN npm ci

# Copy only files needed for Vite build
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

# Build assets
RUN npm run build

# -------------------------------
# Stage 2: Install PHP Dependencies  
# -------------------------------
FROM composer:2 AS composer

# Install MongoDB extension in composer stage
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .build-deps

WORKDIR /build

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (no dev packages)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# -------------------------------
# Stage 3: Final Runtime Image (SLIM)
# -------------------------------
FROM php:8.2-apache

# Install ONLY runtime dependencies (minimal)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4 \
    libpng16-16 \
    libonig5 \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (build deps removed after)
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
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy vendor from composer stage
COPY --from=composer --chown=www-data:www-data /build/vendor ./vendor

# Copy built frontend assets from frontend stage
COPY --from=frontend /build/public/build ./public/build

# Copy application code
COPY --chown=www-data:www-data . .

# Ensure storage and cache directories exist and have correct permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Configure Apache document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf \
    && echo '    Options -Indexes +FollowSymLinks' >> /etc/apache2/apache2.conf \
    && echo '    AllowOverride All' >> /etc/apache2/apache2.conf \
    && echo '    Require all granted' >> /etc/apache2/apache2.conf \
    && echo '</Directory>' >> /etc/apache2/apache2.conf

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Start Apache
CMD ["apache2-foreground"]