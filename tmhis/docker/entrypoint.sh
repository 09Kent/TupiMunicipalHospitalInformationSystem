#!/bin/sh
set -e

# Fix permissions for storage, bootstrap cache, and backups
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/backups \
         /var/www/html/app/Services/backups \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage \
                           /var/www/html/bootstrap/cache \
                           /var/www/html/app/Services
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/app/Services

# Ensure .env file exists
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "Creating .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Configure dynamic PORT for cloud platforms (Render, Railway, Heroku)
if [ -n "$PORT" ] && [ "$PORT" != "80" ]; then
    echo "Configuring Nginx to listen on port $PORT..."
    sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/http.d/default.conf
    sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/g" /etc/nginx/http.d/default.conf
fi

# If APP_KEY is empty, try to generate it
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generating application key..."
    php artisan key:generate --force || true
fi

# Ensure fallback database directory and sqlite file exist
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod 666 /var/www/html/database/database.sqlite

# Determine database target host
DB_TARGET="${DB_HOST:-127.0.0.1}"

# If running standalone on cloud (DB_HOST is local or default), start embedded MariaDB
if [ "$DB_TARGET" = "127.0.0.1" ] || [ "$DB_TARGET" = "localhost" ]; then
    echo "Starting embedded MariaDB database for standalone deployment..."
    mkdir -p /run/mysqld /var/lib/mysql
    chown -R mysql:mysql /run/mysqld /var/lib/mysql

    if [ ! -d "/var/lib/mysql/mysql" ]; then
        mysql_install_db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1
    fi

    /usr/bin/mysqld_safe --user=mysql --datadir=/var/lib/mysql --skip-networking=0 --bind-address=0.0.0.0 >/dev/null 2>&1 &

    echo "Waiting for MariaDB to accept connections..."
    for i in $(seq 1 30); do
        if mysqladmin ping --silent 2>/dev/null; then
            echo "MariaDB is online!"
            break
        fi
        sleep 1
    done

    # Check if MedicalRegistrationDB already exists
    if ! mysql -e "USE MedicalRegistrationDB;" 2>/dev/null; then
        echo "Initializing MedicalRegistrationDB..."
        mysql -e "CREATE DATABASE IF NOT EXISTS MedicalRegistrationDB; \
                  CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY 'root_password'; \
                  CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY 'root_password'; \
                  GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION; \
                  GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; \
                  ALTER USER 'root'@'localhost' IDENTIFIED BY 'root_password'; \
                  ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY 'root_password'; \
                  FLUSH PRIVILEGES;" 2>/dev/null || true

        for sql in /var/www/html/database/sql/register_schema.sql \
                   /var/www/html/database/sql/doctor_schema.sql \
                   /var/www/html/database/sql/tmhis_master_schema.sql \
                   /var/www/html/database/sql/comprehensive_seed.sql; do
            if [ -f "$sql" ]; then
                echo "Importing $(basename "$sql")..."
                mysql -u root -proot_password MedicalRegistrationDB < "$sql" 2>/dev/null || \
                mysql MedicalRegistrationDB < "$sql" 2>/dev/null || true
            fi
        done
        echo "MedicalRegistrationDB schema and seed data loaded successfully!"
    else
        echo "MedicalRegistrationDB already exists, skipping seed."
    fi
else
    echo "Connecting to external database at $DB_TARGET (skipping embedded MariaDB)"
fi

# Run caching if in production
if [ "$APP_ENV" = "production" ]; then
    echo "Running production optimizations..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting Supervisor (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
