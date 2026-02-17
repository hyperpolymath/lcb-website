# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell <jonathan.jewell@open.ac.uk>
#
# Verpex Deployment Guide for nuj-lcb.org.uk

## Overview

This guide deploys the NUJ LCB WordPress site to Verpex hosting with:
- cPanel management
- Cloudflare DNS
- Let's Encrypt SSL
- MySQL/MariaDB database

## Current Situation

- **Primary domain:** metadatastician.art (already configured)
- **New domain:** nuj-lcb.org.uk (Cloudflare DNS cutover complete)
- **Hosting:** Verpex with cPanel
- **DNS:** Cloudflare (`A @ -> 65.181.113.13`, proxied; `CNAME www -> nuj-lcb.org.uk`, proxied)
- **Reachability:** `https://nuj-lcb.org.uk` serves live WordPress backend (`/wp-login.php` reachable)
- **Cloudflare SSL mode:** `Full (strict)` active (validated 2026-02-17)
- **WordPress state:** Core installed, db configured, installer completed, Sinople deployed (with activation compatibility patch)

## Fast Path from Current State (2026-02-17)

Use this if you are starting now from the existing Cloudflare cutover:

1. In cPanel, confirm/add domain `nuj-lcb.org.uk` with the account docroot shown by DomainInfo.
   On this account the active docroot is `/home/nujprcor/nuj-lcb.org.uk`.
2. Create MySQL DB + user and grant all privileges.
3. Upload site files and import database.
4. Update `wp-config.php` with final DB credentials and rotate salts.
5. Run `scripts/wp-deploy.sh` for plugins/pages/menu/forum baseline.
6. Run AutoSSL in cPanel for both hostnames.
7. Change Cloudflare SSL from temporary `Full` back to `Full (strict)`.

## Phase 1: Prepare Local Site for Export

### 1.1: Export Database

```bash
cd /var/mnt/eclipse/repos/lcb-website

# Export database from running container
podman exec lcb-mariadb-dev \
  mysqldump -u wordpress -p"${WORDPRESS_DB_PASSWORD:?set WORDPRESS_DB_PASSWORD}" wordpress \
  > nuj-lcb-backup.sql

# Compress for upload
gzip nuj-lcb-backup.sql
```

### 1.2: Export WordPress Files

```bash
# Copy WordPress files from container to local
podman exec lcb-wordpress-ols-dev tar czf /tmp/wordpress-files.tar.gz -C /var/www/vhosts/localhost/html .
podman cp lcb-wordpress-ols-dev:/tmp/wordpress-files.tar.gz ./wordpress-files.tar.gz

echo "Files ready for upload:"
ls -lh nuj-lcb-backup.sql.gz wordpress-files.tar.gz
```

## Phase 2: Configure Verpex cPanel

### 2.1: Access cPanel

1. Go to Verpex client area
2. Find "Login to cPanel" button
3. OR: Direct URL is usually `https://server.verpex.com:2083` (check your Verpex welcome email)

### 2.2: Add nuj-lcb.org.uk as Addon Domain

**Important:** This creates a separate website alongside metadatastician.art

1. In cPanel, search for **"Addon Domains"** (or find in Domains section)
2. Click **"Create A New Domain"** or **"Manage"**
3. Fill in the form:
   - **New Domain Name:** `nuj-lcb.org.uk`
   - **Subdomain:** Leave blank (auto-fills)
   - **Document Root:** Use cPanel default for this account. Current active path is `/home/nujprcor/nuj-lcb.org.uk`.
   - **Create an FTP account:** Optional (not needed)
4. Click **"Add Domain"**

**Result:** You'll now have:
- `~/public_html/` (metadatastician.art files)
- `~/public_html/nuj-lcb.org.uk/` (NEW - NUJ LCB files will go here)

### 2.3: Create MySQL Database

1. In cPanel, search for **"MySQL Databases"**
2. Click it

**Create Database:**
1. Under "Create New Database"
   - Database Name: `nujlcb_wordpress` (cPanel will prefix with username, e.g., `metadat_nujlcb_wordpress`)
   - Click **"Create Database"**
   - Note the FULL database name shown (you'll need this)

**Create Database User:**
1. Under "MySQL Users" → "Add New User"
   - Username: `nujlcb_wp` (will be prefixed)
   - Password: Click **"Password Generator"** → Generate strong password
   - **SAVE THIS PASSWORD!** Write it down.
   - Click **"Create User"**

**Add User to Database:**
1. Under "Add User To Database"
   - Select User: `nujlcb_wp`
   - Select Database: `nujlcb_wordpress`
   - Click **"Add"**
2. On next screen, select **"ALL PRIVILEGES"**
3. Click **"Make Changes"**

**Write down these details:**
```
Database Name: metadat_nujlcb_wordpress (example - yours will differ)
Database User: metadat_nujlcb_wp
Database Password: [the password you generated]
Database Host: localhost
```

## Phase 3: Upload Files to Verpex

### Option A: cPanel File Manager (Easiest)

1. In cPanel, open **"File Manager"**
2. Navigate to: `public_html/nuj-lcb.org.uk/`
3. Click **"Upload"** (top toolbar)
4. Upload `wordpress-files.tar.gz`
5. Go back to File Manager
6. Right-click `wordpress-files.tar.gz` → **"Extract"**
7. Delete `wordpress-files.tar.gz` after extraction

### Option B: FTP (If you prefer)

1. In cPanel, create FTP account under "FTP Accounts"
2. Use FileZilla/Cyberduck to connect
3. Navigate to `public_html/nuj-lcb.org.uk/`
4. Upload `wordpress-files.tar.gz`
5. Use cPanel File Manager to extract (see Option A step 6)

## Phase 4: Import Database

### 4.1: Upload Database File

1. In cPanel, open **"phpMyAdmin"**
2. Click on your database name in left sidebar (`metadat_nujlcb_wordpress`)
3. Click **"Import"** tab at top
4. Click **"Choose File"** → select `nuj-lcb-backup.sql.gz`
5. Scroll down, click **"Import"** (phpMyAdmin handles .gz files automatically)
6. Wait for "Import has been successfully finished"

## Phase 5: Configure WordPress

### 5.1: Edit wp-config.php

1. In cPanel File Manager, navigate to `public_html/nuj-lcb.org.uk/`
2. Find `wp-config.php`
3. Right-click → **"Edit"**
4. Find these lines and update:

```php
/** Database name */
define( 'DB_NAME', 'metadat_nujlcb_wordpress' ); // YOUR ACTUAL DB NAME

/** Database username */
define( 'DB_USER', 'metadat_nujlcb_wp' ); // YOUR ACTUAL DB USER

/** Database password */
define( 'DB_PASSWORD', 'YOUR_GENERATED_PASSWORD' ); // THE PASSWORD YOU SAVED

/** Database hostname */
define( 'DB_HOST', 'localhost' ); // Usually localhost on Verpex

/** Database charset */
define( 'DB_CHARSET', 'utf8mb4' );

/** Database collation type */
define( 'DB_COLLATE', '' );
```

5. Scroll down to **Authentication Unique Keys and Salts**
6. Replace the section with fresh keys from: https://api.wordpress.org/secret-key/1.1/salt/
7. Click **"Save Changes"**

### 5.2: Update Site URLs (WP-CLI, no temporary PHP files)

From your Verpex shell (in WordPress document root), run:

```bash
# 1) Dry run first
wp search-replace 'http://localhost:8080' 'https://nuj-lcb.org.uk' \
  --all-tables \
  --precise \
  --recurse-objects \
  --skip-columns=guid \
  --dry-run

# 2) Apply for real once counts look right
wp search-replace 'http://localhost:8080' 'https://nuj-lcb.org.uk' \
  --all-tables \
  --precise \
  --recurse-objects \
  --skip-columns=guid
```

This preserves serialized data correctly and avoids exposing a one-off PHP script in public web root.

### 5.3: Apply NUJ LCB WordPress baseline (scripted)

Use the repository deployment helper to configure plugins, pages, menus, forums, and hardening defaults:

```bash
# Run from a checkout of this repository
WP_PATH="$HOME/public_html/nuj-lcb.org.uk" \
SITE_URL="https://nuj-lcb.org.uk" \
SOURCE_URL="http://localhost:8080" \
RUN_SEARCH_REPLACE=1 \
bash scripts/wp-deploy.sh
```

Optional:
- Set `UPDATE_EXISTING_CONTENT=1` if you want existing pages overwritten with repo content.
- Set `WP_ALLOW_ROOT=1` only if WP-CLI must run as root.

## Phase 6: Configure Cloudflare DNS

### 6.1: Get Verpex Server IP

This is already complete for the current deployment:

- Verpex IP: `65.181.113.13`
- Cloudflare apex A: `nuj-lcb.org.uk -> 65.181.113.13` (proxied)
- Cloudflare www: `www.nuj-lcb.org.uk -> nuj-lcb.org.uk` (proxied CNAME)

### 6.2: Update Cloudflare DNS

1. Log in to Cloudflare (only needed if you must correct records)
2. Select `nuj-lcb.org.uk` domain
3. Go to **DNS** section
4. Add/Update these records:

**A Record (apex):**
- Type: `A`
- Name: `@`
- IPv4 address: `65.181.113.13`
- Proxy status: **Proxied** (orange cloud)
- TTL: Auto

**CNAME Record (www):**
- Type: `CNAME`
- Name: `www`
- Target: `nuj-lcb.org.uk`
- Proxy status: **Proxied** (orange cloud)
- TTL: Auto

5. Click **"Save"**

### 6.3: Wait for DNS Propagation

DNS changes can take 5 minutes to 48 hours. Usually it's quick (5-30 minutes).

Check propagation: https://dnschecker.org/#A/nuj-lcb.org.uk

## Phase 7: Configure SSL (Let's Encrypt)

### 7.1: Enable SSL in cPanel

1. In cPanel, search for **"SSL/TLS Status"**
2. Find `nuj-lcb.org.uk` in the list
3. Check the box next to it
4. Click **"Run AutoSSL"**
5. Wait 2-5 minutes for certificate generation

### 7.2: Force HTTPS in WordPress

1. In cPanel File Manager, navigate to `public_html/nuj-lcb.org.uk/`
2. Edit `wp-config.php`
3. Add these lines BEFORE `/* That's all, stop editing! */`:

```php
/* Force HTTPS */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
define('FORCE_SSL_ADMIN', true);
```

4. Save

### 7.3: Add .htaccess Redirect

1. In `public_html/nuj-lcb.org.uk/`, edit `.htaccess` (create if doesn't exist)
2. Add at the TOP:

```apache
# SPDX-License-Identifier: PMPL-1.0-or-later
# Force HTTPS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# WordPress rules below...
```

3. Save

## Phase 8: Configure Cloudflare SSL

1. In Cloudflare, go to **SSL/TLS** section.
2. During install/migration, keep SSL/TLS mode at temporary **"Full"** if you see error `526`.
3. After AutoSSL is valid for both hostnames, set SSL/TLS mode to **"Full (strict)"**.
3. Under **Edge Certificates**, enable:
   - ✅ Always Use HTTPS
   - ✅ Automatic HTTPS Rewrites
   - ✅ Minimum TLS Version: 1.2

## Phase 9: Test Your Site

### 9.1: Test Access

1. Visit: `https://nuj-lcb.org.uk`
2. Should redirect to HTTPS and load WordPress
3. Login: `https://nuj-lcb.org.uk/wp-admin`
   - Username: use the administrator account created during installation
   - Password: use a unique generated password (rotate immediately if temporary)

### 9.2: Change Admin Password

1. Login to WordPress admin
2. Go to **Users** → **Profile**
3. Scroll to "New Password"
4. Click **"Generate Password"**
5. Save the new strong password
6. Click **"Update Profile"**

### 9.3: Test Theme

1. Visit homepage: `https://nuj-lcb.org.uk`
2. Should show **Sinople** theme
3. In admin: **Appearance** → **Customize**
4. Confirm brand colors are set to NUJ LCB palette:
   - Primary color: `#006747` (NUJ green)

### 9.4: Configure SMTP (for sending emails)

Since you're on shared hosting, you'll need to configure SMTP:

1. Go to **Settings** → **WP Mail SMTP**
2. Configure with Verpex SMTP details:
   - From Email: `noreply@nuj-lcb.org.uk`
   - From Name: `NUJ London Central Branch`
   - Mailer: **Other SMTP**
   - SMTP Host: Check Verpex documentation (usually `mail.nuj-lcb.org.uk` or `mail.metadatastician.art`)
   - SMTP Port: `587` (STARTTLS) or `465` (SSL)
   - Encryption: `TLS` (for port 587) or `SSL` (for port 465)
   - Authentication: ON
   - Username: Create email account in cPanel first (e.g., `noreply@nuj-lcb.org.uk`)
   - Password: Email account password
3. Click **"Save Settings"**
4. Test with **"Email Test"** tab

## Phase 10: Final Checklist

- [ ] Site loads at https://nuj-lcb.org.uk
- [ ] SSL certificate valid (green padlock)
- [ ] WordPress admin accessible
- [ ] Admin password rotated to a generated secret
- [ ] Sinople theme active
- [ ] Colors configured (`#006747` primary)
- [ ] SMTP configured for emails
- [ ] Test email sending works
- [ ] `wp search-replace` completed without errors
- [ ] No plaintext credentials were stored in docs or scripts

## Troubleshooting

### "Database Connection Error"
- Check wp-config.php credentials match cPanel database
- Verify database user has all privileges
- Check database host is `localhost`

### "Too Many Redirects"
- Check Cloudflare SSL mode is "Full (strict)"
- Check .htaccess doesn't have conflicting redirects
- Clear Cloudflare cache: Cloudflare dashboard → Caching → Purge Everything

### "Site Not Found" or DNS Issues
- Wait longer for DNS propagation (up to 48 hours)
- Check Cloudflare DNS records point to correct IP
- Verify addon domain created in cPanel
- If DNS resolves but site is `404`, WordPress files are missing from `public_html/nuj-lcb.org.uk/` or docroot is wrong

### "SSL Certificate Invalid"
- Wait for AutoSSL to complete (can take 5-10 minutes)
- Check domain is verified in cPanel SSL/TLS Status
- Contact Verpex support if AutoSSL fails

### Cloudflare `526 Invalid SSL Certificate`
- This happens when Cloudflare cannot validate the origin cert in strict mode
- Temporary mitigation: set Cloudflare SSL mode to `Full`
- Permanent fix: reissue AutoSSL for `nuj-lcb.org.uk` and `www.nuj-lcb.org.uk`, then switch back to `Full (strict)`

## Security Recommendations

1. **Install Security Plugin:**
   ```
   Wordfence Security (free version)
   ```

2. **Set File Permissions:**
   - In cPanel File Manager, select `public_html/nuj-lcb.org.uk/`
   - Set directories to `755`
   - Set files to `644`
   - Set `wp-config.php` to `600`

3. **Enable Cloudflare Security:**
   - Cloudflare dashboard → Security
   - Set Security Level to "Medium"
   - Enable "Browser Integrity Check"
   - Consider enabling "Under Attack Mode" if experiencing issues

4. **Regular Backups:**
   - Use cPanel **"Backup Wizard"**
   - Create full backup weekly
   - Download and store off-site

## Support

- **Verpex Support:** Via client area ticket system
- **WordPress Issues:** wp-admin → Tools → Site Health
- **Cloudflare Issues:** Cloudflare dashboard → Support

---

**Deployment created:** 2026-01-28
**Guide version:** 1.2
