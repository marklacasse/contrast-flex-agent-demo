# Drupal 11 Vulnerable Demo Application

This is a Drupal 11 application with intentional vulnerabilities for Contrast Security demonstration purposes.

## Features

- **PHP 8.4** runtime
- **Drupal 11** CMS
- **Apache** web server
- **Intentional vulnerabilities** for security testing and demonstration

## Running the Application

From the DEMO directory, use the demo-control.sh script:

```bash
# Start the Drupal application
./demo-control.sh start drupal

# Check status
./demo-control.sh status drupal

# View logs
./demo-control.sh logs drupal

# Stop the application
./demo-control.sh stop drupal
```

## Accessing the Application

Once started, the application will be available at:
- **URL**: http://localhost:7070
- **Port**: 7070

## Initial Setup

On first run, you'll need to complete the Drupal installation:

1. Navigate to http://localhost:7070
2. Follow the installation wizard
3. Use SQLite for the database (simplest option)
4. Default admin credentials:
   - Username: admin
   - Password: admin123

## Vulnerable Endpoints

This application includes intentional vulnerabilities for demonstration:

### SQL Injection
- `/vulnerable/user-search` - User search with SQL injection vulnerability

### XSS (Cross-Site Scripting)
- `/vulnerable/comment` - Unfiltered comment submission

### File Upload
- `/vulnerable/file-upload` - File upload without proper validation

### Command Injection
- `/vulnerable/ping` - Ping utility with command injection

### Path Traversal
- `/vulnerable/file-view` - File viewer with directory traversal

## Warning

⚠️ **DO NOT deploy this application in production!** ⚠️

This application contains intentional security vulnerabilities for educational and demonstration purposes only.

## Technology Stack

- PHP 8.4
- Drupal 11
- Apache 2.4
- SQLite (default database)
- Contrast Flex Agent (for security monitoring)
