# -------------------------------
# Stage 1: Base PHP + Apache + Extensions
# -------------------------------
FROM php:8.2-apache

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libssl-dev pkg-config libcurl4-openssl-dev \
    libpng-dev libonig-dev unzip git curl zip \
    libzip-dev nodejs npm \
 && pecl install mongodb \
 && docker-php-ext-enable mongodb \
 && docker-php-ext-install pdo_mysql zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy all project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Install Node dependencies and build frontend
RUN npm install && npm run build

# Fix permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/public \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache to serve from /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n    </Directory>' >> /etc/apache2/apache2.conf

# Expose port (Railway will map it automatically)
EXPOSE 8080

# Run Apache in foreground
CMD ["apache2-foreground"]
