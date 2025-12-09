# ==========================================
# 1️⃣ BASE IMAGE: PHP 8.2 with FPM
# ==========================================
FROM php:8.2-fpm

# Install required system packages
RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx supervisor \
    libpng-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# ==========================================
# 2️⃣ INSTALL COMPOSER
# ==========================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================================
# 3️⃣ APP SETUP
# ==========================================
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# ==========================================
# 4️⃣ NGINX CONFIG
# ==========================================
COPY ./nginx.conf /etc/nginx/nginx.conf

# ==========================================
# 5️⃣ SUPERVISOR CONFIG
# ==========================================
COPY .render/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================
# 6️⃣ RUN SUPERVISOR AT STARTUP
#    ⚠ Clear caches *after* DB exists
# ==========================================
CMD bash -c "\
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan optimize:clear && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf \
    "
