# Use official PHP with Apache
FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpg-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

#Set Apache DocumentRoot to /var/www/html/public (laravel entry point)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www.html|/var/www/html/public|g' /etc/apache2/apache2.conf


# Copy application code
COPY . /var/www/html/


#Create uploads folder and set permissions
RUN mkdir -p /var/wwww/html/public/uploads \
    && chown -R www-data:www-data /var/wwww/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads


# Set working directory
WORKDIR /var/www/html

# Copy composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

#Set permissions for laravel storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Render port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
