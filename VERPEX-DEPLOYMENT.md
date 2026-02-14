# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2025 Jonathan D.A. Jewell <jonathan.jewell@open.ac.uk>
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
- **New domain:** nuj-lcb.org.uk (needs to be added as addon domain)
- **Hosting:** Verpex with cPanel
- **DNS:** Cloudflare

## Phase 1: Prepare Local Site for Export

### 1.1: Export Database

```bash
cd ~/Documents/hyperpolymath-repos/nuj-lcb-production

# Export database from running container
docker exec nuj-lcb-mariadb mysqldump -u wordpress -p'3SK2u+kvr8XvytTBTBKIlnAFZEwGHNiHpZXkHmh70AM=' wordpress > nuj-lcb-backup.sql

# Compress for upload
gzip nuj-lcb-backup.sql
```

### 1.2: Export WordPress Files

```bash
# Copy WordPress files from container to local
docker exec nuj-lcb-wordpress tar czf /tmp/wordpress-files.tar.gz -C /var/www/html .
docker cp nuj-lcb-wordpress:/tmp/wordpress-files.tar.gz ./wordpress-files.tar.gz

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
   - **Document Root:** `public_html/nuj-lcb.org.uk` (or just accept default)
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

### 5.2: Update Site URLs

Since we're moving from localhost to production:

1. In cPanel File Manager, navigate to `public_html/nuj-lcb.org.uk/`
2. Create a file called `fix-urls.php` with this content:

```php
<?php
// Temporary URL fixer - DELETE THIS FILE after running once!
define( 'DB_NAME', 'metadat_nujlcb_wordpress' );
define( 'DB_USER', 'metadat_nujlcb_wp' );
define( 'DB_PASSWORD', 'YOUR_GENERATED_PASSWORD' );
define( 'DB_HOST', 'localhost' );

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$old_url = 'http://localhost:8080';
$new_url = 'https://nuj-lcb.org.uk';

$tables = ['options', 'posts', 'postmeta'];
foreach ($tables as $table) {
    $sql = "UPDATE wp_${table} SET option_value = REPLACE(option_value, '$old_url', '$new_url') WHERE option_value LIKE '%$old_url%'";
    if ($table == 'posts') {
        $sql = "UPDATE wp_posts SET post_content = REPLACE(post_content, '$old_url', '$new_url'), guid = REPLACE(guid, '$old_url', '$new_url')";
    }
    if ($table == 'postmeta') {
        $sql = "UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, '$old_url', '$new_url')";
    }
    $conn->query($sql);
}

echo "URLs updated from $old_url to $new_url<br>";
echo "<strong>DELETE THIS FILE NOW!</strong>";
$conn->close();
?>
```

3. Visit: `https://nuj-lcb.org.uk/fix-urls.php` (or without https if SSL not ready yet)
4. You should see "URLs updated"
5. **IMMEDIATELY DELETE `fix-urls.php`** via File Manager (security risk if left)

## Phase 6: Configure Cloudflare DNS

### 6.1: Get Verpex Server IP

1. In Verpex client area, find your server IP address
2. OR: In cPanel, look for "Server Information" or contact Verpex support
3. Write down the IP (e.g., `123.45.67.89`)

### 6.2: Update Cloudflare DNS

1. Log in to Cloudflare
2. Select `nuj-lcb.org.uk` domain
3. Go to **DNS** section
4. Add/Update these records:

**A Record:**
- Type: `A`
- Name: `@` (root domain)
- IPv4 address: `YOUR_VERPEX_IP`
- Proxy status: **Proxied** (orange cloud)
- TTL: Auto

**A Record (www):**
- Type: `A`
- Name: `www`
- IPv4 address: `YOUR_VERPEX_IP`
- Proxy status: **Proxied** (orange cloud)
- TTL: Auto

**CNAME Record (if you prefer for www):**
- Type: `CNAME`
- Name: `www`
- Target: `nuj-lcb.org.uk`
- Proxy status: **Proxied**
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

1. In Cloudflare, go to **SSL/TLS** section
2. Set SSL/TLS encryption mode to: **"Full (strict)"**
3. Under **Edge Certificates**, enable:
   - ✅ Always Use HTTPS
   - ✅ Automatic HTTPS Rewrites
   - ✅ Minimum TLS Version: 1.2

## Phase 9: Test Your Site

### 9.1: Test Access

1. Visit: `https://nuj-lcb.org.uk`
2. Should redirect to HTTPS and load WordPress
3. Login: `https://nuj-lcb.org.uk/wp-admin`
   - Username: `admin`
   - Password: `ChangeMeAfterSetup123!` (CHANGE THIS!)

### 9.2: Change Admin Password

1. Login to WordPress admin
2. Go to **Users** → **Profile**
3. Scroll to "New Password"
4. Click **"Generate Password"**
5. Save the new strong password
6. Click **"Update Profile"**

### 9.3: Test Theme

1. Visit homepage: `https://nuj-lcb.org.uk`
2. Should show Newspaperup theme
3. In admin: **Appearance** → **Customize**
4. Set colors to match nuj-prc.org.uk:
   - Primary color: `#461cfb` (purple)

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
- [ ] Admin password changed from default
- [ ] Newspaperup theme active
- [ ] Colors configured (purple #461cfb)
- [ ] SMTP configured for emails
- [ ] Test email sending works
- [ ] Delete `fix-urls.php` file (security)
- [ ] Remove local database credentials from this guide (security)

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

### "SSL Certificate Invalid"
- Wait for AutoSSL to complete (can take 5-10 minutes)
- Check domain is verified in cPanel SSL/TLS Status
- Contact Verpex support if AutoSSL fails

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
**Guide version:** 1.0
