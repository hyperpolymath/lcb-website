# Web Server Configurations

This repository maintains multiple web server configurations for flexibility and comparison.

## Currently Active: OpenLiteSpeed

**File:** `docker-compose.yml`

**Services:**
- OpenLiteSpeed 1.8.3 with lsphp84
- Varnish 7.4 cache layer
- MariaDB 11.2 database

**Access Points:**
- **WordPress**: http://localhost:8080
- **Varnish Cache**: http://localhost:8081
- **OLS Admin Console**: http://localhost:7080

**Status:** Running - needs WordPress installation via web UI

**Start:** `docker compose up -d`
**Stop:** `docker compose down`
**Logs:** `docker logs lcb-wordpress-ols-dev`

### Initial Setup

1. Start the stack: `docker compose up -d`
2. Navigate to: http://localhost:8080/wp-admin/install.php
3. Complete the WordPress installation wizard
4. Access your site at: http://localhost:8080

### OpenLiteSpeed Features

- **HTTP/2 Support**: Enabled by default
- **Admin Console**: Port 7080 for server management
- **lsphp84**: PHP 8.4 with LiteSpeed SAPI for performance
- **Zero-downtime restarts**: Graceful reload capability

## Available Backups

### Apache Configuration

**File:** `docker-compose-apache-backup.yml`

**Services:**
- Apache 2.4 with PHP 8.5
- Varnish 7.4 cache layer
- MariaDB 11.2 database

**Access Points:**
- **WordPress**: http://localhost:8080
- **Varnish Cache**: http://localhost:8081

**Status:** Backup - working configuration saved

**Switch to Apache:**
```bash
docker compose -f docker-compose-apache-backup.yml up -d
```

## Planned Configurations

### Caddy (Future)

**Status:** Not yet implemented
**Target File:** `docker-compose-caddy.yml`
**Features:**
- Automatic HTTPS with Let's Encrypt
- HTTP/2 and HTTP/3 (QUIC) support
- Modern, simpler configuration

### Nginx (Future)

**Status:** Not yet implemented
**Target File:** `docker-compose-nginx.yml`
**Features:**
- Battle-tested stability
- Extensive ecosystem
- Well-documented configuration

## Configuration Management

All configurations share:
- Same database backend (MariaDB 11.2)
- Same Varnish cache layer (7.4)
- Same volume mounts for WordPress content
- Same network configuration

### Volume Mounts

- `wordpress_data`: Main WordPress installation
- `./wp-content/themes`: Custom themes (bind mount)
- `./wp-content/mu-plugins`: Must-use plugins (bind mount)
- `./.well-known`: ACME challenge directory (bind mount, read-only)

### ASDF Version Pinning

The `.tool-versions` file pins:
- `varnish 7.4.3`
- `openlitespeed 1.8.5`
- `deno 2.1.5`

Note: ASDF plugins are currently stubs. Actual runtime uses Docker containers.

## Switching Between Configurations

1. Stop current stack: `docker compose down`
2. Switch compose file: `docker compose -f docker-compose-<name>.yml up -d`
3. Wait for services to start (check with `docker ps`)
4. Verify access at http://localhost:8080

**Important:** All configurations use the same database volume, so WordPress data persists across switches.

## Troubleshooting

### Port 443 Binding Error

If you see `permission denied` for port 443:
- Remove port 443 mapping (privileged port in rootless Podman)
- Or map to 8443: `"8443:443"`

### OpenLiteSpeed Won't Start

Check logs: `docker logs lcb-wordpress-ols-dev`
Common issues:
- File permissions (need `www-data:www-data` ownership)
- Missing symlink `/var/www/html` → `/var/www/vhosts/localhost/html`

### Database Connection Error

Verify MariaDB is running: `docker ps | grep mariadb`
Check environment variables: `docker exec lcb-wordpress-ols-dev env | grep WORDPRESS`

### Varnish Not Caching

Check VCL configuration: `cat services/varnish/default.vcl`
Verify backend connection: `docker logs lcb-varnish-dev`

## License

All configurations in this repository are licensed under PMPL-1.0-or-later (Palimpsest-MPL).
Third-party software (WordPress, OpenLiteSpeed, Apache, etc.) retains its original license.
