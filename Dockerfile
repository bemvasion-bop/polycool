# ==========================================
# 1️⃣ BASE IMAGE
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

# Copy all app files
COPY . .

# Install optimized dependencies
RUN composer install --no-dev --optimize-autoloader

# ==========================================
# 4️⃣ FIX PHP-FPM RUNNING AS ROOT 🚑
# ==========================================
RUN sed -i "s/user = .*/user = www-data/" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = .*/group = www-data/" /usr/local/etc/php-fpm.d/www.conf

# ==========================================
# 5️⃣ PERMISSIONS (VERY IMPORTANT)
# ==========================================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ==========================================
# 6️⃣ NGINX CONFIG
# ==========================================
COPY ./nginx.conf /etc/nginx/nginx.conf

# ==========================================
# 7️⃣ SUPERVISOR CONFIG
# ==========================================
COPY .render/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Debug: show config presence
RUN ls -R /etc/supervisor/conf.d

# ==========================================
# 8️⃣ LARAVEL CACHE CLEAR (avoids 500 errors)
# ==========================================
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear

# ==========================================
# 9️⃣ START SERVICES
# ==========================================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
