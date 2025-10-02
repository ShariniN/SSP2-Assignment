# -------------------------------
# Base PHP + Apache image
# -------------------------------
FROM php:8.2-apache

# -------------------------------
# Install system dependencies
# -------------------------------
RUN apt-get update && apt-get install -y \
        libssl-dev pkg-config libcurl4-openssl-dev \
        libpng-dev libonig-dev unzip git curl zip \
        libzip-dev nodejs npm \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# -------------------------------
# Set working directory
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# Copy composer binary
# -------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------------
# Install PHP dependencies (cache)
# -------------------------------
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# -------------------------------
# Install Node dependencies (cache)
# -------------------------------
COPY package*.json ./
RUN npm install && npm run build

# -------------------------------
# Copy rest of project
# -------------------------------
COPY . .

# -------------------------------
# Fix permissions (only storage + cache)
# -------------------------------
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# -------------------------------
# Apache config for Laravel
# -------------------------------
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf \
    && echo '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf

# -------------------------------
# Expose Railway port
# -------------------------------
ENV PORT 8080
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
EXPOSE ${PORT}

# -------------------------------
# Run Apache
# -------------------------------
CMD ["apache2-foreground"]
