# ============================
# 1️⃣ BASE IMAGE - PHP + FPM
# ============================
FROM php:8.2-fpm

# Install essential packages
RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx supervisor \
    libpng-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# ============================
# 2️⃣ COMPOSER INSTALL
# ============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install dependencies (Prod, NO Dev)
RUN composer install --no-dev --optimize-autoloader

# Laravel storage & cache folder permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# ============================
# 3️⃣ NGINX CONFIG
# ============================
COPY ./nginx.conf /etc/nginx/nginx.conf

# ============================
# 4️⃣ SUPERVISOR START COMMAND
# ============================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
 
