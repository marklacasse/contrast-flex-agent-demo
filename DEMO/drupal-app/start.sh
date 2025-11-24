#!/bin/bash

# Drupal Demo Application Startup Script
echo "Starting Drupal 11 Demo Application..."

# Set working directory
cd /demos/drupal-app

# Check if Drupal is installed
if [ ! -d "web/core" ]; then
    echo "Installing Drupal 11 and dependencies..."
    
    # Install Composer if not already installed
    if ! command -v composer &> /dev/null; then
        echo "Installing Composer..."
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php --install-dir=/usr/local/bin --filename=composer
        php -r "unlink('composer-setup.php');"
    fi
    
    # Install Drupal dependencies
    composer install --no-interaction --prefer-dist --optimize-autoloader
    
    echo "Drupal installation complete!"
fi

# Setup Drupal site if not already configured
DRUPAL_INSTALLED=false
if [ -f "web/sites/default/settings.php" ]; then
    # Check if it's our custom settings (not the default one)
    if grep -q "Drupal Vulnerable Demo - Pre-configured Settings" web/sites/default/settings.php; then
        echo "Custom settings.php found..."
        # Check if database exists and is valid
        if [ -f "web/sites/default/files/.ht.sqlite" ]; then
            echo "Drupal appears to be installed, skipping setup..."
            DRUPAL_INSTALLED=true
        fi
    else
        echo "Default settings.php found, replacing with custom settings..."
        rm -f web/sites/default/settings.php
    fi
fi

if [ "$DRUPAL_INSTALLED" = false ]; then
    echo "Setting up Drupal site..."
    
    # Create necessary directories
    mkdir -p web/sites/default/files
    chmod 777 web/sites/default/files
    mkdir -p config/sync
    chmod 777 config/sync
    
    # Copy pre-configured settings
    cp settings.php web/sites/default/settings.php
    chmod 666 web/sites/default/settings.php
    
    # Install Drush if needed
    if [ ! -f "vendor/bin/drush" ]; then
        echo "Installing Drush..."
        composer require drush/drush --no-interaction
    fi
    
    # Install Drupal site
    echo "Installing Drupal with SQLite..."
    cd web
    ../vendor/bin/drush site:install standard \
        --db-url=sqlite://sites/default/files/.ht.sqlite \
        --site-name="Contrast Vulnerable Demo" \
        --account-name=admin \
        --account-pass=admin123 \
        --yes \
        --no-interaction
    
    # Enable the vulnerable module
    echo "Enabling vulnerable demo module..."
    ../vendor/bin/drush en contrast_vulnerable -y
    
    # Clear cache
    ../vendor/bin/drush cache:rebuild
    
    cd ..
    echo "✅ Drupal site installed successfully!"
    echo "   Admin login: admin / admin123"
fi

# Configure Apache to use the Drupal directory
cat > /etc/apache2/sites-available/drupal.conf <<'EOF'
<VirtualHost *:7070>
    DocumentRoot /demos/drupal-app/web
    
    <Directory /demos/drupal-app/web>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/drupal-error.log
    CustomLog ${APACHE_LOG_DIR}/drupal-access.log combined
</VirtualHost>
EOF

# Enable the site and required modules
a2ensite drupal.conf
a2enmod rewrite
a2dissite 000-default.conf

# Update Apache to listen on port 7070
if ! grep -q "Listen 7070" /etc/apache2/ports.conf; then
    echo "Listen 7070" >> /etc/apache2/ports.conf
fi

# Start Apache in background
echo "Starting Apache web server on port 7070..."
service apache2 start

echo "✅ Drupal application started successfully!"
echo "   Access at: http://localhost:7070"
