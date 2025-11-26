#!/bin/bash

# Laravel setup script - runs when container starts or when Laravel is first accessed

set -e

LARAVEL_DIR="/demos/laravel-app"
SETUP_MARKER="$LARAVEL_DIR/.setup_complete"

echo "Setting up Laravel..."

cd "$LARAVEL_DIR"

# Create necessary directories and set permissions
mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Check if vendor directory exists and has content
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
    echo "Installing Composer dependencies..."
    # Temporarily disable Contrast agent for composer install
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | grep -v "contrast" || true
fi

# Clear all Laravel caches
echo "Clearing Laravel caches..."
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force 2>&1 | grep -v "contrast" || true
fi

# Create SQLite database if it doesn't exist
if [ ! -f "database/database.sqlite" ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Always configure Apache (in case container restarted)
echo "Configuring Apache..."
if ! grep -q "Listen 5050" /etc/apache2/ports.conf; then
    echo "Listen 5050" >> /etc/apache2/ports.conf
fi

if [ ! -f /etc/apache2/sites-available/laravel.conf ]; then
    cat > /etc/apache2/sites-available/laravel.conf <<'EOF'
<VirtualHost *:5050>
    ServerAdmin webmaster@localhost
    DocumentRoot /demos/laravel-app/public
    
    <Directory /demos/laravel-app/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/laravel_error.log
    CustomLog ${APACHE_LOG_DIR}/laravel_access.log combined
</VirtualHost>
EOF
fi

# Enable the site if not already enabled
a2ensite laravel.conf > /dev/null 2>&1 || true

# Mark setup as complete
touch "$SETUP_MARKER"

echo "Laravel setup complete!"
