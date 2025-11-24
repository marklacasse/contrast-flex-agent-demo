# PHP Drupal 11 Demo Application - Quick Reference

## Summary

A new PHP 8.4 Drupal 11 vulnerable application has been added to your Contrast Security demo environment.

## Quick Start

```bash
# Rebuild Docker container (required first time)
./run-demo.sh

# Inside container, start Drupal
./demo-control.sh start drupal

# Access the application
# Visit: http://localhost:7070
```

## Application Details

- **Framework**: Drupal 11
- **Language**: PHP 8.4
- **Web Server**: Apache 2.4
- **Port**: 7070
- **Database**: SQLite
- **Demo URL**: http://localhost:7070/contrast-demo

## Vulnerable Endpoints

All endpoints include intentional vulnerabilities for Contrast Security testing:

| Vulnerability Type | Endpoint | Example |
|-------------------|----------|---------|
| SQL Injection | `/vulnerable/user-search` | `?username=' OR '1'='1` |
| XSS | `/vulnerable/comment` | `?comment=<script>alert('XSS')</script>` |
| File Upload | `/vulnerable/file-upload` | POST with file upload |
| Command Injection | `/vulnerable/ping` | `?host=127.0.0.1; whoami` |
| Path Traversal | `/vulnerable/file-view` | `?filename=../../../etc/passwd` |
| System Info API | `/api/info` | Returns JSON system data |

## Management Commands

```bash
./demo-control.sh start drupal      # Start application
./demo-control.sh stop drupal       # Stop application
./demo-control.sh restart drupal    # Restart application
./demo-control.sh status drupal     # Check status
./demo-control.sh logs drupal       # View logs
```

## What Was Added

### New Files Created

1. `/DEMO/drupal-app/composer.json` - Drupal dependencies
2. `/DEMO/drupal-app/README.md` - Application documentation
3. `/DEMO/drupal-app/start.sh` - Startup script
4. `/DEMO/drupal-app/setup.sh` - Setup helper
5. `/DEMO/drupal-app/.gitignore` - Git ignore rules
6. `/DEMO/drupal-app/web/modules/custom/contrast_vulnerable/` - Vulnerable module
   - `contrast_vulnerable.info.yml`
   - `contrast_vulnerable.routing.yml`
   - `src/Controller/VulnerableController.php`

### Modified Files

1. `/DEMO/Dockerfile` - Added PHP 8.4, Apache, Composer
2. `/DEMO/demo-control.sh` - Added drupal configuration
3. `/README.md` - Updated documentation

## First Time Setup

After starting the Drupal app for the first time:

1. Navigate to http://localhost:7070
2. Complete installation wizard (use SQLite)
3. Create admin account (suggested: admin/admin123)
4. Enable the "Contrast Vulnerable Demo" module

## All Demo Applications

Your demo environment now includes 5 applications:

| Application | Port | Framework | URL |
|------------|------|-----------|-----|
| Python Flask | 9090 | Flask | http://localhost:9090 |
| Node.js | 3030 | Express | http://localhost:3030 |
| .NET Core | 8181 | ASP.NET | http://localhost:8181 |
| Java Tomcat | 8080 | Spring Boot | http://localhost:8080/contrast-demo |
| **PHP Drupal** | **7070** | **Drupal 11** | **http://localhost:7070/contrast-demo** |

## Technical Stack

- **PHP**: 8.4 (with required extensions)
- **Drupal**: 11.x (installed via Composer)
- **Apache**: 2.4 (with mod_php8.4, mod_rewrite)
- **Database**: SQLite 3
- **Composer**: 2.x (for PHP packages)
- **Contrast**: Flex Agent enabled

## Documentation

- Application README: `/DEMO/drupal-app/README.md`
- Installation Guide: `/DEMO/drupal-app/INSTALLATION.md`
- Main README: `/README.md`

## Testing with Contrast

```bash
# Generate SQL Injection vulnerability
curl "http://localhost:7070/vulnerable/user-search?username=' OR '1'='1"

# Generate XSS vulnerability
curl "http://localhost:7070/vulnerable/comment?comment=<script>alert(1)</script>"

# Generate Command Injection
curl "http://localhost:7070/vulnerable/ping?host=127.0.0.1; cat /etc/passwd"

# Generate Path Traversal
curl "http://localhost:7070/vulnerable/file-view?filename=../../../etc/passwd"
```

## Important Notes

⚠️ **Security Warning**: This application contains intentional vulnerabilities. DO NOT use in production!

- All vulnerabilities are for demonstration purposes only
- Contrast Flex Agent will detect and report these vulnerabilities
- Use only in isolated demo environments
- Never expose to public networks

## Support

For detailed setup and troubleshooting, see:
- `/DEMO/drupal-app/INSTALLATION.md`
- `/DEMO/drupal-app/README.md`

---

**Created**: November 24, 2025
**Status**: Ready for testing
**Port**: 7070
