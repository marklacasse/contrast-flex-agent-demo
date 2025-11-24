# Drupal 11 PHP 8.4 Vulnerable Demo Application - Installation Guide

## Overview

This guide covers the complete setup of a new PHP 8.4 Drupal 11 application with intentional security vulnerabilities for Contrast Security demonstrations.

## What Was Created

### Application Structure

```
DEMO/drupal-app/
├── composer.json                    # Drupal 11 dependencies
├── README.md                        # Application-specific documentation
├── .gitignore                       # Git ignore patterns
├── setup.sh                         # Quick setup script
├── start.sh                         # Application startup script
├── config/                          # Drupal configuration
└── web/                             # Web root (created during install)
    └── modules/custom/contrast_vulnerable/
        ├── contrast_vulnerable.info.yml
        ├── contrast_vulnerable.routing.yml
        └── src/Controller/
            └── VulnerableController.php
```

### Key Features

1. **PHP 8.4 Runtime** - Latest PHP version
2. **Drupal 11 CMS** - Modern Drupal with Composer
3. **Apache 2.4** - Web server with mod_php
4. **Custom Vulnerable Module** - Intentional security flaws for testing

## Installation Steps

### 1. Docker Container Updates

The Dockerfile has been updated to include:

- PHP 8.4 with all required extensions
- Apache 2.4 with mod_php
- Composer for PHP package management
- SQLite for database (simplest option)

### 2. Application Configuration

The `demo-control.sh` script has been updated to include:

```bash
APPS[drupal]="drupal-php:7070:/demos/drupal-app:bash /demos/drupal-app/start.sh"
```

This configures:
- **Name**: drupal
- **Port**: 7070
- **Working Directory**: /demos/drupal-app
- **Start Command**: bash /demos/drupal-app/start.sh

### 3. First Time Setup

When you first start the Drupal application, it will:

1. Install Composer (if not present)
2. Download Drupal 11 and all dependencies via Composer
3. Configure Apache to serve on port 7070
4. Enable required Apache modules (rewrite, php8.4)
5. Set up directory permissions
6. Start Apache web server

## Usage

### Starting the Application

From inside the Docker container:

```bash
# Using demo control script (recommended)
./demo-control.sh start drupal

# Or directly
cd /demos/drupal-app
bash start.sh
```

### Accessing the Application

- **URL**: http://localhost:7070
- **Demo Page**: http://localhost:7070/contrast-demo

### Initial Drupal Setup

On first access, complete the Drupal installation wizard:

1. Navigate to http://localhost:7070
2. Select language: English
3. Select installation profile: Standard
4. Database configuration:
   - Choose **SQLite**
   - Database name: `drupal.sqlite`
5. Site configuration:
   - Site name: "Contrast Security Demo"
   - Site email: demo@contrast.local
   - Username: admin
   - Password: admin123
   - Email: admin@contrast.local
6. Click "Save and continue"

### Enabling the Vulnerable Module

After Drupal installation:

```bash
# From inside the container
cd /demos/drupal-app
./vendor/bin/drush en contrast_vulnerable -y
./vendor/bin/drush cr
```

Or via the Drupal UI:
1. Log in as admin
2. Navigate to **Extend** (admin/modules)
3. Find "Contrast Vulnerable Demo"
4. Check the box and click "Install"
5. Clear cache: Configuration → Development → Performance → Clear all caches

## Vulnerable Endpoints

The custom module provides these intentionally vulnerable endpoints:

### 1. SQL Injection
- **Path**: `/vulnerable/user-search`
- **Method**: GET
- **Parameter**: `username`
- **Example**: `?username=' OR '1'='1`

### 2. Cross-Site Scripting (XSS)
- **Path**: `/vulnerable/comment`
- **Method**: GET
- **Parameter**: `comment`
- **Example**: `?comment=<script>alert('XSS')</script>`

### 3. File Upload
- **Path**: `/vulnerable/file-upload`
- **Method**: POST
- **Parameter**: `file` (multipart form data)
- **Vulnerability**: No file type validation

### 4. Command Injection
- **Path**: `/vulnerable/ping`
- **Method**: GET
- **Parameter**: `host`
- **Example**: `?host=127.0.0.1; cat /etc/passwd`

### 5. Path Traversal
- **Path**: `/vulnerable/file-view`
- **Method**: GET
- **Parameter**: `filename`
- **Example**: `?filename=../../../etc/passwd`

### 6. System Information API
- **Path**: `/api/info`
- **Method**: GET
- **Returns**: JSON with system information

## Testing with Contrast

The application is automatically instrumented with Contrast Flex Agent when the Docker container is built with an agent token.

### Generating Vulnerabilities

```bash
# SQL Injection
curl "http://localhost:7070/vulnerable/user-search?username=' OR '1'='1"

# XSS
curl "http://localhost:7070/vulnerable/comment?comment=<script>alert('XSS')</script>"

# Command Injection
curl "http://localhost:7070/vulnerable/ping?host=127.0.0.1; whoami"

# Path Traversal
curl "http://localhost:7070/vulnerable/file-view?filename=../../../etc/passwd"

# File Upload
curl -X POST -F "file=@malicious.php" http://localhost:7070/vulnerable/file-upload
```

## Management Commands

```bash
# Start the application
./demo-control.sh start drupal

# Stop the application
./demo-control.sh stop drupal

# Restart the application
./demo-control.sh restart drupal

# Check status
./demo-control.sh status drupal

# View logs
./demo-control.sh logs drupal
```

## Troubleshooting

### Application Won't Start

```bash
# Check Apache logs
tail -f /var/log/apache2/drupal-error.log

# Check if port 7070 is in use
lsof -i :7070

# Verify PHP installation
php --version

# Check Apache modules
apache2ctl -M | grep php
```

### Composer Issues

```bash
# Clear Composer cache
composer clear-cache

# Reinstall dependencies
rm -rf vendor
composer install
```

### Permission Issues

```bash
# Fix directory permissions
chmod 777 web/sites/default/files
chmod 666 web/sites/default/settings.php
```

### Module Not Found

```bash
# Rebuild Drupal cache
cd /demos/drupal-app
./vendor/bin/drush cr

# Verify module is present
ls -la web/modules/custom/contrast_vulnerable/
```

## Technology Stack

- **PHP**: 8.4 (latest)
- **Drupal**: 11.x (latest)
- **Web Server**: Apache 2.4
- **Database**: SQLite 3
- **Package Manager**: Composer 2
- **Contrast Agent**: Flex Agent (latest)

## Security Warnings

⚠️ **IMPORTANT**: This application contains intentional security vulnerabilities:

- SQL Injection
- Cross-Site Scripting (XSS)
- Command Injection
- Path Traversal
- Unrestricted File Upload

**DO NOT deploy this application in production environments!**

This application is designed solely for:
- Security testing
- Contrast Security demonstrations
- Educational purposes
- Vulnerability detection training

## Files Modified/Created

1. **DEMO/Dockerfile** - Added PHP 8.4, Apache, and Composer installation
2. **DEMO/demo-control.sh** - Added drupal application configuration
3. **DEMO/drupal-app/** - Complete new application directory
4. **README.md** - Updated with Drupal application information

## Next Steps

1. Rebuild the Docker container to include PHP/Apache:
   ```bash
   ./run-demo.sh
   ```

2. Start the Drupal application:
   ```bash
   ./demo-control.sh start drupal
   ```

3. Complete Drupal installation at http://localhost:7070

4. Enable the vulnerable module

5. Test vulnerabilities with Contrast Flex Agent monitoring

## Support

For issues or questions:
- Check application logs: `./demo-control.sh logs drupal`
- Review Apache logs: `/var/log/apache2/drupal-error.log`
- Verify module installation: `./vendor/bin/drush pm:list --type=module`

---

**Created**: November 24, 2025
**Drupal Version**: 11.x
**PHP Version**: 8.4
**Port**: 7070
