# WordPress Deployment Plan - NUJ London Central Branch

Complete step-by-step plan for deploying the NUJ LCB website to VPS-D8 with three services:
- Main site: nuj-lcb.org.uk (WordPress)
- Forum: forum.nuj-lcb.org.uk (Zulip)
- Video: convene.nuj-lcb.org.uk (Jitsi Meet)

## Prerequisites

**VPS-D8 Specs:**
- 4 vCPU
- 8GB RAM
- 160GB NVMe SSD
- Ubuntu 22.04/24.04 LTS
- Root SSH access

**DNS Management:**
- Cloudflare account with domain added
- API token with DNS edit permissions

**Required Tools on VPS:**
- Docker 24.0+
- Docker Compose 2.20+
- Git
- Certbot (or Caddy for auto-HTTPS)

---

## Phase 1: Server Preparation (Day 1)

### 1.1 Initial Server Setup

```bash
# SSH into VPS-D8
ssh root@vps-d8-ip

# Update system
apt update && apt upgrade -y

# Install required packages
apt install -y docker.io docker-compose-v2 git curl ufw fail2ban

# Enable Docker
systemctl enable docker
systemctl start docker

# Create deployment user
useradd -m -s /bin/bash nujdeploy
usermod -aG docker nujdeploy
mkdir -p /home/nujdeploy/.ssh
cp ~/.ssh/authorized_keys /home/nujdeploy/.ssh/
chown -R nujdeploy:nujdeploy /home/nujdeploy/.ssh
chmod 700 /home/nujdeploy/.ssh
chmod 600 /home/nujdeploy/.ssh/authorized_keys
```

### 1.2 Firewall Configuration

```bash
# Configure UFW
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw enable

# Configure fail2ban for SSH protection
systemctl enable fail2ban
systemctl start fail2ban
```

### 1.3 Directory Structure

```bash
# As nujdeploy user
su - nujdeploy

mkdir -p ~/nuj-lcb/{wordpress,zulip,jitsi,caddy,backups}
mkdir -p ~/nuj-lcb/wordpress/{html,db}
mkdir -p ~/nuj-lcb/caddy/{config,data}
```

---

## Phase 2: Cloudflare DNS Setup (Day 1)

### 2.1 Create DNS Records

**In Cloudflare Dashboard:**

| Type | Name | Content | Proxy | TTL |
|------|------|---------|-------|-----|
| A | @ | VPS-D8-IP | ✓ Proxied | Auto |
| A | www | VPS-D8-IP | ✓ Proxied | Auto |
| A | forum | VPS-D8-IP | ✓ Proxied | Auto |
| A | convene | VPS-D8-IP | ✓ Proxied | Auto |
| AAAA | @ | VPS-D8-IPv6 | ✓ Proxied | Auto |
| AAAA | www | VPS-D8-IPv6 | ✓ Proxied | Auto |
| AAAA | forum | VPS-D8-IPv6 | ✓ Proxied | Auto |
| AAAA | convene | VPS-D8-IPv6 | ✓ Proxied | Auto |

### 2.2 Enable Cloudflare Features

**SSL/TLS:**
- Mode: Full (strict)
- Minimum TLS: 1.2
- Always Use HTTPS: ON
- HTTP Strict Transport Security: Enabled
- Automatic HTTPS Rewrites: ON

**Speed:**
- HTTP/3 (with QUIC): ON
- Auto Minify: HTML, CSS, JS
- Brotli: ON

**Security:**
- Security Level: Medium
- Bot Fight Mode: ON
- Challenge Passage: 30 minutes

**Network:**
- DNSSEC: Enabled
- IPv6 Compatibility: ON

---

## Phase 3: Deploy Main WordPress Site (Day 2-3)

### 3.1 Create docker-compose.yml

```bash
cd ~/nuj-lcb
```

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  # Main WordPress site
  wordpress:
    image: wordpress:6.7-php8.3-apache
    container_name: nujlcb-wordpress
    restart: unless-stopped
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: ${DB_NAME}
      WORDPRESS_TABLE_PREFIX: nujlcb_
      WORDPRESS_CONFIG_EXTRA: |
        define('FORCE_SSL_ADMIN', true);
        define('DISALLOW_FILE_EDIT', true);
        define('WP_AUTO_UPDATE_CORE', 'minor');
    volumes:
      - ./wordpress/html:/var/www/html
      - ./wordpress/uploads.ini:/usr/local/etc/php/conf.d/uploads.ini:ro
    networks:
      - nujlcb
    depends_on:
      - db

  # MariaDB database
  db:
    image: mariadb:11.2
    container_name: nujlcb-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - ./wordpress/db:/var/lib/mysql
    networks:
      - nujlcb

  # Caddy reverse proxy
  caddy:
    image: caddy:2.8-alpine
    container_name: nujlcb-caddy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
      - "443:443/udp"  # HTTP/3 QUIC
    volumes:
      - ./caddy/Caddyfile:/etc/caddy/Caddyfile:ro
      - ./caddy/config:/config
      - ./caddy/data:/data
    networks:
      - nujlcb
    depends_on:
      - wordpress

  # Backup service (runs daily)
  backup:
    image: alpine:latest
    container_name: nujlcb-backup
    restart: "no"
    volumes:
      - ./backups:/backups
      - ./wordpress/html:/var/www/html:ro
      - ./wordpress/db:/var/lib/mysql:ro
    command: |
      sh -c '
        apk add --no-cache tar gzip age
        cd /var/www/html
        tar czf /backups/wordpress-$(date +%Y%m%d).tar.gz .
        cd /var/lib/mysql
        tar czf /backups/database-$(date +%Y%m%d).tar.gz .
        find /backups -name "*.tar.gz" -mtime +30 -delete
      '
    networks:
      - nujlcb

networks:
  nujlcb:
    driver: bridge
```

### 3.2 Create Caddyfile

Create `caddy/Caddyfile`:

```caddyfile
# Main WordPress site
nuj-lcb.org.uk, www.nuj-lcb.org.uk {
	# HTTP/3 enabled by default in Caddy
	reverse_proxy wordpress:80

	# Security headers
	header {
		Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
		X-Content-Type-Options "nosniff"
		X-Frame-Options "SAMEORIGIN"
		X-XSS-Protection "1; mode=block"
		Referrer-Policy "strict-origin-when-cross-origin"
		Permissions-Policy "geolocation=(), microphone=(), camera=()"
	}

	# Logging
	log {
		output file /data/access.log
		format json
	}

	# PHP security
	php_fastcgi wordpress:9000 {
		env SCRIPT_FILENAME /var/www/html{path}
	}
}

# Forum subdomain (Zulip - will add in Phase 4)
forum.nuj-lcb.org.uk {
	# Placeholder - Zulip setup later
	respond "Forum coming soon" 503
}

# Video subdomain (Jitsi - will add in Phase 5)
convene.nuj-lcb.org.uk {
	# Placeholder - Jitsi setup later
	respond "Video conferencing coming soon" 503
}
```

### 3.3 Create Environment File

Create `.env`:

```bash
# Database credentials
DB_NAME=nujlcb_wp
DB_USER=nujlcb_user
DB_PASSWORD=$(openssl rand -base64 32)
DB_ROOT_PASSWORD=$(openssl rand -base64 32)

# WordPress
WP_ADMIN_USER=admin
WP_ADMIN_PASSWORD=$(openssl rand -base64 24)
WP_ADMIN_EMAIL=webmaster@nuj-lcb.org.uk

# Backup encryption
BACKUP_AGE_KEY=$(age-keygen 2>/dev/null | grep "AGE-SECRET-KEY" | cut -d: -f2 | tr -d ' ')
```

**IMPORTANT:** Save these credentials in your password manager immediately!

### 3.4 PHP Configuration

Create `wordpress/uploads.ini`:

```ini
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
memory_limit = 256M
```

### 3.5 Start WordPress

```bash
# Start services
docker compose up -d wordpress db caddy

# Wait for database initialization (2-3 minutes)
sleep 180

# Check logs
docker compose logs -f wordpress

# Verify services running
docker compose ps
```

### 3.6 WordPress Initial Setup

```bash
# Access https://nuj-lcb.org.uk
# Complete WordPress installation wizard:
# - Site Title: NUJ London Central Branch
# - Admin Username: (from .env WP_ADMIN_USER)
# - Admin Password: (from .env WP_ADMIN_PASSWORD)
# - Admin Email: webmaster@nuj-lcb.org.uk
# - Search Engine Visibility: Unchecked (allow indexing)
```

---

## Phase 4: WordPress Configuration (Day 3-4)

### 4.1 Install Required Plugins

**Via WP-CLI:**

```bash
docker exec -it nujlcb-wordpress wp --allow-root plugin install \
  wordfence \
  members \
  two-factor \
  simple-membership \
  download-monitor \
  user-meta-manager \
  wp-mail-smtp \
  updraftplus \
  --activate
```

**Or via WordPress admin:**
- Settings → Plugins → Add New → Search & Install

### 4.2 Install Theme

```bash
# Install Newspaperup theme
docker exec -it nujlcb-wordpress wp --allow-root theme install newspaperup --activate

# Or upload manually through Appearance → Themes
```

### 4.3 Import Content from Preview

**Copy content from shareable site:**

1. Create pages for each section (Home, About, Join, Officers, Contact, Members Area)
2. Copy HTML content from `content/pages/*.md` files
3. Set homepage as static page (Settings → Reading)
4. Create menu structure (Appearance → Menus)

**Automated import script:**

```bash
# On local machine
cd ~/Documents/hyperpolymath-repos/nuj-lcb-production

# Create WordPress import file
./scripts/export-to-wordpress.sh

# SCP to VPS
scp wordpress-import.xml nujdeploy@vps-d8:~/nuj-lcb/

# On VPS
docker exec -it nujlcb-wordpress wp --allow-root import wordpress-import.xml --authors=create
```

### 4.4 Configure Security (CRITICAL)

**Follow SECURITY-IMPLEMENTATION.md:**

1. **Wordfence Setup:**
   - Enable firewall (Extended Protection)
   - Enable 2FA for all admins
   - Configure malware scans (daily)
   - Set up email alerts

2. **User Roles:**
   ```bash
   docker exec -it nujlcb-wordpress wp --allow-root role create branch_member "Branch Member" --clone=subscriber
   ```

3. **Privacy Settings:**
   - Install privacy plugin for GDPR compliance
   - Create privacy policy page
   - Configure cookie consent banner

4. **SSL/HTTPS:**
   - Already handled by Caddy + Cloudflare
   - Verify: https://www.ssllabs.com/ssltest/

### 4.5 Configure Members Area

**Follow MEMBERS-AREA-SETUP.md:**

1. Create member directory page (requires login)
2. Set up custom profile fields (show_full_name, show_email, etc.)
3. Create download area for resources
4. Configure member registration workflow

---

## Phase 5: Deploy Zulip Forum (Day 5)

### 5.1 Add Zulip to docker-compose.yml

Add to `docker-compose.yml`:

```yaml
  # Zulip chat/forum
  zulip:
    image: zulip/docker-zulip:latest
    container_name: nujlcb-zulip
    restart: unless-stopped
    environment:
      SETTING_EXTERNAL_HOST: forum.nuj-lcb.org.uk
      SETTING_ZULIP_ADMINISTRATOR: admin@nuj-lcb.org.uk
      SETTING_EMAIL_HOST: smtp.gmail.com  # Or your SMTP
      SETTING_EMAIL_PORT: 587
      SETTING_EMAIL_USE_TLS: "true"
      SECRETS_email_password: ${SMTP_PASSWORD}
      SECRETS_secret_key: ${ZULIP_SECRET_KEY}
      SECRETS_postgres_password: ${ZULIP_DB_PASSWORD}
      SECRETS_rabbitmq_password: ${ZULIP_RABBITMQ_PASSWORD}
    volumes:
      - ./zulip/data:/data
    networks:
      - nujlcb
    ports:
      - "8080:80"  # Internal only, accessed via Caddy

  zulip-db:
    image: postgres:15-alpine
    container_name: nujlcb-zulip-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: zulip
      POSTGRES_USER: zulip
      POSTGRES_PASSWORD: ${ZULIP_DB_PASSWORD}
    volumes:
      - ./zulip/db:/var/lib/postgresql/data
    networks:
      - nujlcb

  zulip-redis:
    image: redis:7-alpine
    container_name: nujlcb-zulip-redis
    restart: unless-stopped
    networks:
      - nujlcb
```

### 5.2 Update Caddyfile for Zulip

Replace Zulip placeholder in `caddy/Caddyfile`:

```caddyfile
forum.nuj-lcb.org.uk {
	reverse_proxy zulip:80

	header {
		Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
		X-Content-Type-Options "nosniff"
		X-Frame-Options "SAMEORIGIN"
	}

	log {
		output file /data/zulip-access.log
		format json
	}
}
```

### 5.3 Deploy Zulip

```bash
# Generate secrets for .env
echo "ZULIP_SECRET_KEY=$(openssl rand -base64 32)" >> .env
echo "ZULIP_DB_PASSWORD=$(openssl rand -base64 32)" >> .env
echo "ZULIP_RABBITMQ_PASSWORD=$(openssl rand -base64 32)" >> .env
echo "SMTP_PASSWORD=your-smtp-password" >> .env

# Start Zulip
docker compose up -d zulip zulip-db zulip-redis

# Wait for initialization
sleep 120

# Create admin account
docker exec -it nujlcb-zulip /home/zulip/deployments/current/manage.py createsuperuser
```

### 5.4 Configure Zulip

**Access https://forum.nuj-lcb.org.uk**

1. Complete organization setup
2. Set organization name: "NUJ London Central Branch"
3. Configure authentication:
   - Email/password
   - Optional: SAML SSO with WordPress
4. Create channels:
   - #general - General discussion
   - #campaigns - Campaign coordination
   - #events - Event planning
   - #officers - Officers only (private)
   - #help - Member support

5. Invite members from WordPress user list

---

## Phase 6: Deploy Jitsi Meet (Day 6)

### 6.1 Add Jitsi to docker-compose.yml

Add to `docker-compose.yml`:

```yaml
  # Jitsi Meet video conferencing
  jitsi-web:
    image: jitsi/web:stable
    container_name: nujlcb-jitsi-web
    restart: unless-stopped
    environment:
      - ENABLE_HSTS=1
      - ENABLE_HTTP_REDIRECT=1
      - DISABLE_HTTPS=1  # Caddy handles HTTPS
      - PUBLIC_URL=https://convene.nuj-lcb.org.uk
      - XMPP_SERVER=jitsi-prosody
      - JICOFO_COMPONENT_SECRET=${JICOFO_SECRET}
      - JVB_TCP_HARVESTER_DISABLED=true
    volumes:
      - ./jitsi/web:/config
      - ./jitsi/web/crontabs:/var/spool/cron/crontabs
      - ./jitsi/transcripts:/usr/share/jitsi-meet/transcripts
    networks:
      - nujlcb
    ports:
      - "8000:80"  # Internal only

  jitsi-prosody:
    image: jitsi/prosody:stable
    container_name: nujlcb-jitsi-prosody
    restart: unless-stopped
    environment:
      - XMPP_DOMAIN=convene.nuj-lcb.org.uk
      - JICOFO_COMPONENT_SECRET=${JICOFO_SECRET}
      - JVB_AUTH_PASSWORD=${JVB_PASSWORD}
      - JICOFO_AUTH_PASSWORD=${JICOFO_PASSWORD}
      - PUBLIC_URL=https://convene.nuj-lcb.org.uk
    volumes:
      - ./jitsi/prosody:/config
    networks:
      - nujlcb

  jitsi-jicofo:
    image: jitsi/jicofo:stable
    container_name: nujlcb-jitsi-jicofo
    restart: unless-stopped
    environment:
      - XMPP_SERVER=jitsi-prosody
      - JICOFO_COMPONENT_SECRET=${JICOFO_SECRET}
      - JICOFO_AUTH_PASSWORD=${JICOFO_PASSWORD}
    networks:
      - nujlcb
    depends_on:
      - jitsi-prosody

  jitsi-jvb:
    image: jitsi/jvb:stable
    container_name: nujlcb-jitsi-jvb
    restart: unless-stopped
    environment:
      - XMPP_SERVER=jitsi-prosody
      - JVB_AUTH_PASSWORD=${JVB_PASSWORD}
      - JVB_STUN_SERVERS=stun.l.google.com:19302
      - JVB_TCP_HARVESTER_DISABLED=true
    ports:
      - "10000:10000/udp"  # Video RTP
    networks:
      - nujlcb
    depends_on:
      - jitsi-prosody
```

### 6.2 Update Caddyfile for Jitsi

Replace Jitsi placeholder:

```caddyfile
convene.nuj-lcb.org.uk {
	reverse_proxy jitsi-web:80

	header {
		Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
		X-Content-Type-Options "nosniff"
	}

	log {
		output file /data/jitsi-access.log
		format json
	}
}
```

### 6.3 Deploy Jitsi

```bash
# Generate secrets
echo "JICOFO_SECRET=$(openssl rand -hex 16)" >> .env
echo "JVB_PASSWORD=$(openssl rand -base64 24)" >> .env
echo "JICOFO_PASSWORD=$(openssl rand -base64 24)" >> .env

# Open UDP port for video
ufw allow 10000/udp

# Start Jitsi
docker compose up -d jitsi-web jitsi-prosody jitsi-jicofo jitsi-jvb

# Wait for startup
sleep 60

# Test
curl -I https://convene.nuj-lcb.org.uk
```

### 6.4 Configure Jitsi

**Create config file `jitsi/web/interface_config.js`:**

```javascript
var interfaceConfig = {
    APP_NAME: 'NUJ LCB Video Meetings',
    DEFAULT_BACKGROUND: '#006747',  // NUJ green
    DEFAULT_LOGO_URL: 'images/nuj-logo.png',
    DEFAULT_WELCOME_PAGE_LOGO_URL: 'images/nuj-logo.png',
    JITSI_WATERMARK_LINK: 'https://nuj-lcb.org.uk',
    SHOW_JITSI_WATERMARK: false,
    SHOW_WATERMARK_FOR_GUESTS: false,
    BRAND_WATERMARK_LINK: '',
    SHOW_BRAND_WATERMARK: false,
};
```

**Optional: Add authentication** (require WordPress login to create meetings)

---

## Phase 7: Backup & Monitoring (Day 7)

### 7.1 Automated Backups

Create `scripts/backup.sh`:

```bash
#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later
# Automated backup script

set -e

BACKUP_DIR=~/nuj-lcb/backups
DATE=$(date +%Y%m%d-%H%M%S)
AGE_KEY=$(cat ~/.backup_age_key)

# Backup WordPress files
cd ~/nuj-lcb/wordpress/html
tar czf - . | age -r "$AGE_KEY" > "$BACKUP_DIR/wordpress-$DATE.tar.gz.age"

# Backup database
docker exec nujlcb-db mysqldump -u root -p"$DB_ROOT_PASSWORD" --all-databases | \
  gzip | age -r "$AGE_KEY" > "$BACKUP_DIR/database-$DATE.sql.gz.age"

# Backup Zulip data
cd ~/nuj-lcb/zulip/data
tar czf - . | age -r "$AGE_KEY" > "$BACKUP_DIR/zulip-$DATE.tar.gz.age"

# Backup Jitsi config
cd ~/nuj-lcb/jitsi
tar czf - . | age -r "$AGE_KEY" > "$BACKUP_DIR/jitsi-$DATE.tar.gz.age"

# Delete backups older than 30 days
find "$BACKUP_DIR" -name "*.age" -mtime +30 -delete

# Upload to remote storage (optional)
# rclone copy "$BACKUP_DIR" remote:nuj-lcb-backups/

echo "✅ Backup completed: $DATE"
```

### 7.2 Cron Job for Backups

```bash
# Add to nujdeploy crontab
crontab -e

# Daily backup at 2 AM
0 2 * * * /home/nujdeploy/scripts/backup.sh >> /home/nujdeploy/logs/backup.log 2>&1
```

### 7.3 Monitoring Setup

**Install monitoring tools:**

```bash
# Docker stats
docker stats --no-stream > ~/logs/docker-stats.log

# Disk usage alert
df -h | awk '$5+0 > 80 {print "⚠️  Disk usage: " $5 " on " $6}' | mail -s "Disk Alert" admin@nuj-lcb.org.uk
```

**Optional: Uptime monitoring**
- Use UptimeRobot (free tier: 50 monitors)
- Add monitors for all three domains
- Set alert email to webmaster@nuj-lcb.org.uk

---

## Phase 8: Testing & Go-Live (Day 8)

### 8.1 Pre-Launch Checklist

- [ ] All three services accessible (WordPress, Zulip, Jitsi)
- [ ] SSL certificates valid (A+ rating on SSL Labs)
- [ ] HTTP/3 enabled and working
- [ ] WordPress admin login working
- [ ] Wordfence configured and scanning
- [ ] 2FA enabled for all admins
- [ ] Member registration workflow tested
- [ ] Zulip invites sent to founding members
- [ ] Jitsi video call tested (2+ participants)
- [ ] Backups running and encrypted
- [ ] Monitoring alerts configured
- [ ] Privacy policy published
- [ ] GDPR compliance verified
- [ ] Mobile responsiveness tested
- [ ] Accessibility (WCAG AA minimum)

### 8.2 Performance Testing

```bash
# Test HTTP/3 support
curl -I --http3 https://nuj-lcb.org.uk

# Load test (simple)
ab -n 100 -c 10 https://nuj-lcb.org.uk/

# Or use k6
k6 run load-test.js
```

### 8.3 Security Audit

```bash
# Run security scan
docker run --rm -it aquasec/trivy image wordpress:6.7
docker run --rm -it aquasec/trivy image jitsi/web:stable
docker run --rm -it aquasec/trivy image zulip/docker-zulip:latest

# Check headers
curl -I https://nuj-lcb.org.uk | grep -E "(Strict-Transport|X-Content|X-Frame)"
```

### 8.4 Go-Live Steps

1. **DNS propagation**: Wait 24-48 hours after DNS changes
2. **Test from multiple networks**: WiFi, mobile data, VPN
3. **Announce to members**: Email with links to all three services
4. **Monitor logs**: Watch for errors in first 48 hours
5. **Gradual rollout**: Invite officers first, then general members

---

## Maintenance Schedule

### Daily
- Automated backups (2 AM)
- Log review (errors/warnings)

### Weekly
- Security updates (Docker images)
- Wordfence scan review
- Disk space check

### Monthly
- Full security audit
- Plugin updates
- Performance review
- Backup restoration test

### Quarterly
- SSL certificate renewal check (Caddy auto-renews)
- Access audit (remove inactive users)
- Disaster recovery drill

---

## Troubleshooting

### WordPress Won't Start

```bash
# Check logs
docker compose logs wordpress

# Reset permissions
docker exec -it nujlcb-wordpress chown -R www-data:www-data /var/www/html

# Restart
docker compose restart wordpress
```

### Zulip Not Accessible

```bash
# Check Zulip logs
docker compose logs zulip

# Verify database connection
docker exec -it nujlcb-zulip-db psql -U zulip -c "\l"

# Restart Zulip
docker compose restart zulip zulip-db zulip-redis
```

### Jitsi Video Not Working

```bash
# Check firewall
ufw status | grep 10000

# Verify JVB is running
docker compose logs jitsi-jvb

# Test UDP port
nc -u -z -v vps-d8-ip 10000
```

### SSL Certificate Issues

```bash
# Caddy auto-renews, but check:
docker exec -it nujlcb-caddy caddy list-certificates

# Force renewal
docker exec -it nujlcb-caddy caddy reload --config /etc/caddy/Caddyfile
```

---

## Rollback Plan

If deployment fails:

1. **Stop all services**:
   ```bash
   docker compose down
   ```

2. **Restore from backup**:
   ```bash
   cd ~/nuj-lcb/backups
   age -d -i ~/.backup_age_key wordpress-YYYYMMDD.tar.gz.age | tar xzf - -C ../wordpress/html/
   age -d -i ~/.backup_age_key database-YYYYMMDD.sql.gz.age | gunzip | docker exec -i nujlcb-db mysql -u root -p
   ```

3. **Restart with previous config**:
   ```bash
   git checkout HEAD~1 docker-compose.yml
   docker compose up -d
   ```

4. **Update DNS back to old host** (if migrating from elsewhere)

---

## Cost Estimates

**VPS-D8 Hosting**: ~$20-40/month (depending on provider)
**Cloudflare**: Free (Pro plan $20/month for extra features)
**Domain**: ~$15/year (.org.uk)
**Backup Storage** (optional remote): ~$5-10/month (1TB Backblaze B2)

**Total**: ~$30-60/month

---

## Next Steps After Deployment

1. **Content Migration**: Import all pages from static preview
2. **Member Onboarding**: Send welcome emails with login instructions
3. **Training Sessions**: Show officers how to use WordPress/Zulip/Jitsi
4. **LinkedIn Integration**: Set up auto-posting from WordPress to LinkedIn
5. **Custom Development**: Add any unique features requested by members

---

**Questions or issues?** See TROUBLESHOOTING.md or contact webmaster@nuj-lcb.org.uk
