# LCB Website WordPress Setup - Fixes Applied

Date: 2026-01-22

## Issues Fixed

### 1. Database Connection Error ✓
**Problem:** WordPress showed "Error establishing a database connection"

**Root Cause:** Environment variables (`WORDPRESS_DB_HOST`, etc.) were not being passed to PHP when accessed through the OpenLiteSpeed web server.

**Solution:** Created `/var/www/vhosts/localhost/html/wp-config.php` with hardcoded database credentials:
```php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', 'wordpress_password' );
define( 'DB_HOST', 'db:3306' );
```

### 2. Missing WP-Sinople Theme ✓
**Problem:** wp-sinople-theme not found in WordPress themes directory

**Solution:**
- Found theme in `~/Documents/hyperpolymath-repos/wp-sinople-theme`
- Copied to WordPress: `wordpress/` subdirectory → `/wp-content/themes/sinople`
- Set proper ownership: `www-data:www-data`
- Activated theme via WP-CLI

### 3. WordPress Health Check Issues ✓
**Problems:**
- REST API encountered errors
- Loopback requests failed
- Debug log in public location

**Solutions Added to wp-config.php:**
```php
// Fix loopback requests in Docker
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('ALTERNATE_WP_CRON', true);

// Allow filesystem access for theme/plugin management
define('FS_METHOD', 'direct');

// Move debug log to non-public location
define('WP_DEBUG_LOG', '/var/log/wordpress-debug.log');
```

### 4. Missing Uploads Directory ✓
**Problem:** No uploads directory for media files

**Solution:**
```bash
mkdir -p /var/www/vhosts/localhost/html/wp-content/uploads
chown -R www-data:www-data /var/www/vhosts/localhost/html/wp-content/uploads
chmod -R 755 /var/www/vhosts/localhost/html/wp-content/uploads
```

### 5. Theme Upload Permissions ✓
**Problem:** Users couldn't see theme upload option in WordPress admin

**Solution:**
- Created uploads directory with proper permissions
- Added `define('FS_METHOD', 'direct');` to wp-config.php
- Fixed themes directory ownership

## Current Status

### Installed Themes
- **Sinople** (active) - v1.0.0 - Custom theme from hyperpolymath/wp-sinople-theme
- **Twenty Twenty-Four** (inactive) - v1.4 - WordPress default theme

### Access Points
- **Main Site:** http://localhost:8080
- **Admin Panel:** http://localhost:8080/wp-admin
- **Varnish Cache:** http://localhost:8080 8081
- **OLS Admin Console:** http://localhost:7080

### Container Configuration
- WordPress Container: `lcb-wordpress-ols-dev` (OpenLiteSpeed 1.8.3 + PHP 8.4)
- Database Container: `lcb-mariadb-dev` (MariaDB 11.2)
- Cache Container: `lcb-varnish-dev` (Varnish 7.4)

### Database Configuration
- Host: `db:3306` (container network)
- Database: `wordpress`
- User: `wordpress`
- Password: `wordpress_password` (development only - change for production!)

## Remaining Items

### WordPress Health Check (Non-Critical)
These warnings are expected in a Docker development environment:
- Loopback requests may show warnings (normal for containerized WordPress)
- Scheduled events may appear late (use WP-Cron alternatives in production)
- Inactive plugins warnings (remove if not needed)

### Production Deployment
When deploying to production:
1. Change all database passwords
2. Generate unique WordPress salts: https://api.wordpress.org/secret-key/1.1/salt/
3. Set `WP_DEBUG` to `false`
4. Remove `WP_DEBUG_LOG` or ensure it's in secure location
5. Configure proper SSL/TLS certificates
6. Set up proper backup procedures
7. Use svalinn-compose.yml instead of docker-compose.yml

## Files Modified
- `/var/www/vhosts/localhost/html/wp-config.php` - Database credentials and Docker fixes
- `/var/www/vhosts/localhost/html/wp-content/themes/` - Added Sinople theme
- `/var/www/vhosts/localhost/html/wp-content/uploads/` - Created directory

## References
- Theme Repository: https://github.com/hyperpolymath/wp-sinople-theme
- Docker Compose: `docker-compose.yml` (OpenLiteSpeed stack)
- Backup Config: `docker-compose-apache-backup.yml` (Apache stack)
- Documentation: `WEB-SERVER-CONFIGS.md`
