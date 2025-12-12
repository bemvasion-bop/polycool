# ============================================================
# 1️⃣ STAGE 1 — BUILD VITE ASSETS WITH NODE
# ============================================================
FROM node:18 AS node_builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# ============================================================
# 2️⃣ STAGE 2 — PHP + APACHE SERVER
# ============================================================
FROM php:8.2-apache

# Update first (separate line — required by Debian Trixie)
RUN apt-get update

# Install PHP extensions needed by Laravel
RUN apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Fix DocumentRoot to /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Copy Laravel app code
COPY . /var/www/html/

# Copy built Vite assets
COPY --from=node_builder /app/public/build /var/www/html/public/build

WORKDIR /var/www/html

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port for Render
EXPOSE 10000

CMD ["apache2-foreground"]
