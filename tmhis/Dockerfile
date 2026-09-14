FROM php:8.3-fpm-alpine

# Install system dependencies & Nginx & Supervisor
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    mariadb \
    mariadb-client

# Install PHP extensions required by Laravel & MySQL
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    mbstring \
    gd \
    zip \
    opcache \
    intl

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Set environment for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy Docker configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make entrypoint executable
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Ensure storage and bootstrap/cache directories exist and have proper permissions before composer install
RUN mkdir -p /var/www/html/bootstrap/cache \
             /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Composer dependencies (production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Configure final ownership & permissions for Laravel storage and app
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/app && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Initialize MariaDB directory for standalone/cloud deployment
RUN mkdir -p /run/mysqld /var/lib/mysql && \
    chown -R mysql:mysql /run/mysqld /var/lib/mysql && \
    mysql_install_db --user=mysql --datadir=/var/lib/mysql

# Expose HTTP port
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
