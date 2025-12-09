# ==========================================
# 1️⃣ BASE IMAGE: PHP 8.2 with FPM
# ==========================================
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx supervisor \
    libpng-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# ==========================================
# 2️⃣ INSTALL COMPOSER
# ==========================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================================
# 3️⃣ WORKDIR + COPY FILES
# ==========================================
WORKDIR /var/www/html
COPY . .

# ==========================================
# 4️⃣ INSTALL DEPENDENCIES (Prod only)
# ==========================================
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ==========================================
# 5️⃣ FIX PERMISSIONS FOR Laravel
# ==========================================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ==========================================
# 6️⃣ CLEAR ALL CACHES BEFORE BUILD
# ==========================================
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan cache:clear \
    && php artisan view:clear \
    && php artisan optimize:clear

# ==========================================
# 7️⃣ NGINX CONFIG
# ==========================================
COPY ./nginx.conf /etc/nginx/nginx.conf

# ==========================================
# 8️⃣ SUPERVISOR CONFIG
# ==========================================
COPY .render/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================
# 9️⃣ RUN SUPERVISOR ON STARTUP
# ==========================================
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
