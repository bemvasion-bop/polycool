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

# Copy Laravel app files
COPY . .

# Install PHP dependencies (Production Mode)
RUN composer install --no-dev --optimize-autoloader

# Folder Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# ==========================================
# 4️⃣ NGINX CONFIG
# ==========================================
COPY ./nginx.conf /etc/nginx/nginx.conf

# ==========================================
# 5️⃣ SUPERVISOR CONFIG (IMPORTANT FIX)
# ==========================================
COPY .render/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Debug: Check file is present (shows in logs)
RUN ls -R /etc/supervisor/conf.d

# ==========================================
# 6️⃣ START SERVICES (Nginx + PHP-FPM)
# ==========================================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
