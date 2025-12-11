#Use Official Php with Apache
FROM php:8.2-apache

#Install required Php extensions for Larave
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

#Enable Apache mod_rewrite (needed for laravel routes)
RUN a2enmod rewrite

#SET Apache DocumentRoot ot /var/www/html/public
RUN sed -i 's|var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf\
    && sed -i 's|var/www/html|public|g' /etc/apache2/apache2.conf

#COPY APP code
COPY . /var/www/html/


#CREATE uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

#Set working dir
WORKDIR /var/www/html

#install composer //changes
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

#SET Permission for laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

#Expose Render's required port
EXPOSE 10000


# Start Apache
CMD ["apache2-foreground"]
