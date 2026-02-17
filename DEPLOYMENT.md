# SPDX-License-Identifier: PMPL-1.0-or-later
# NUJ LCB Website - Production Deployment Guide

**Goal:** Get nuj-lcb.org.uk working like nuj-prc.org.uk THIS WEEK.

## Prerequisites

- Podman and Podman Compose installed
- Domain name (nuj-lcb.org.uk) pointing to your server
- SSL certificate (Let's Encrypt recommended)

## Step 1: Clone and Configure

```bash
cd /var/mnt/eclipse/repos/lcb-website

# Copy environment template
cp .env.example .env

# Generate strong passwords
openssl rand -base64 32  # Use for DB_PASSWORD
openssl rand -base64 32  # Use for DB_ROOT_PASSWORD

# Edit .env with your passwords
nano .env
```

## Step 2: Start Services

```bash
# Start WordPress and MariaDB
podman-compose -f docker-compose.yml up -d

# Check everything is running
podman-compose -f docker-compose.yml ps

# View logs
podman-compose -f docker-compose.yml logs -f
```

## Step 3: Install WordPress

1. Open browser to `http://localhost:8080` (or `http://your-server-ip:8080`)
2. Select language: English (UK)
3. Fill in site details:
   - Site Title: **NUJ London Central Branch**
   - Username: (your admin username)
   - Password: (strong password)
   - Email: your-email@nuj-lcb.org.uk
4. Click "Install WordPress"

## Step 4: Install Sinople Theme

### Option A: Via WordPress Admin (Recommended)

1. Login to WordPress admin
2. Go to Appearance → Themes
3. Confirm `Sinople` is present (mounted from `wp-content/themes/sinople/`)
4. Click "Activate"

### Option B: Manual Upload

1. Go to Appearance → Themes → Add New → Upload Theme
2. Upload `wp-content/themes/sinople` as a zip package if needed
4. Activate

## Step 5: Configure Theme

Match nuj-prc.org.uk styling:

1. **Colors:**
   - Go to Appearance → Customize → Colors
   - Primary color: NUJ green (`#006747`)

2. **Typography:**
   - Headings: Lexend Deca (weight 600)
   - Body: Outfit (weight 500)
   - (Theme may handle this automatically)

3. **Logo:**
   - Upload NUJ LCB logo at Appearance → Customize → Site Identity

4. **Menus:**
   - Create main navigation at Appearance → Menus

## Step 6: SSL/HTTPS (Production)

### Option A: Let's Encrypt with Nginx Proxy

```bash
# Install nginx and certbot on host
sudo apt install nginx certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d nuj-lcb.org.uk -d www.nuj-lcb.org.uk

# Configure nginx to proxy to Podman
sudo nano /etc/nginx/sites-available/nuj-lcb.org.uk
```

Nginx config:
```nginx
server {
    listen 80;
    server_name nuj-lcb.org.uk www.nuj-lcb.org.uk;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name nuj-lcb.org.uk www.nuj-lcb.org.uk;

    ssl_certificate /etc/letsencrypt/live/nuj-lcb.org.uk/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/nuj-lcb.org.uk/privkey.pem;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Option B: Traefik (Alternative)

See Traefik documentation for automatic Let's Encrypt.

## Step 7: Backups

### Database Backup

```bash
# Backup database
podman-compose -f docker-compose.yml exec db mysqldump -u wordpress -p wordpress > backup-$(date +%Y%m%d).sql

# Restore database
podman-compose -f docker-compose.yml exec -T db mysql -u wordpress -p wordpress < backup-20260128.sql
```

### Full Backup

```bash
# Backup everything
tar -czf nuj-lcb-backup-$(date +%Y%m%d).tar.gz \
    docker-compose.yml \
    .env \
    wp-content/

# Backup database separately
podman-compose -f docker-compose.yml exec db mysqldump -u wordpress -p wordpress | gzip > db-backup-$(date +%Y%m%d).sql.gz
```

## Step 8: Updates

```bash
# Update WordPress core (via admin dashboard)
# OR pull new images:
podman-compose -f docker-compose.yml pull
podman-compose -f docker-compose.yml up -d
```

## Troubleshooting

### WordPress shows "Error establishing database connection"

Check:
```bash
podman-compose -f docker-compose.yml logs db
podman-compose -f docker-compose.yml exec wordpress ping db
```

### Permission errors

Fix wp-content permissions:
```bash
podman-compose -f docker-compose.yml exec wordpress chown -R www-data:www-data /var/www/html/wp-content
```

### Can't install themes/plugins

Temporary fix:
```bash
podman-compose -f docker-compose.yml exec wordpress chmod -R 777 /var/www/html/wp-content
# (Fix permissions after installation)
```

## Security Checklist

- [ ] Strong passwords set in .env
- [ ] .env is gitignored (never committed)
- [ ] SSL/HTTPS enabled
- [ ] WordPress admin has strong password
- [ ] Regular backups configured
- [ ] WordPress, themes, plugins kept updated
- [ ] Remove unused themes/plugins
- [ ] Disable file editing (add to wp-config.php: `define('DISALLOW_FILE_EDIT', true);`)

## Performance Optimization (Later)

After basic site is working:

1. Enable Varnish cache (uncomment in docker-compose.yml)
2. Install caching plugin (W3 Total Cache or WP Super Cache)
3. Install CDN (Cloudflare free tier)
4. Optimize images (ShortPixel or similar)

## Support

If stuck:
1. Check Podman logs: `podman-compose -f docker-compose.yml logs`
2. Check WordPress debug: Enable WP_DEBUG in .env
3. Search WordPress forums
4. This is STANDARD WordPress - thousands of tutorials exist

## What NOT to Do

- ❌ Don't add experimental tech to production
- ❌ Don't use Vörðr/Svalinn/Cerro Torre (not ready)
- ❌ Don't use custom container builds
- ❌ Don't overcomplicate it

Keep it simple. Site working > fancy tech.
