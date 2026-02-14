# Security Implementation Guide

**CRITICAL: Implement BEFORE launching site**

This guide implements the security measures identified in `SECURITY-THREAT-MODEL.md` to protect against far-right doxing, data breaches, and social engineering.

## Priority Level System

🔴 **CRITICAL** - Do TODAY before site goes live
🟡 **HIGH** - Do THIS WEEK
🟢 **MEDIUM** - Do THIS MONTH

---

## 🔴 CRITICAL: Day 1 Security (DO TODAY)

### 1. Install & Configure Wordfence (30 minutes)

**Install:**
```bash
wp plugin install wordfence --activate
```

**Or via WordPress Admin:**
Plugins → Add New → Search "Wordfence" → Install → Activate

**Initial Setup:**

1. **Wordfence → Dashboard**
   - Click "Get Premium" or skip (free tier is fine)
   - Enter email for security alerts

2. **Wordfence → Scan**
   - Click "Start New Scan"
   - Wait for completion (~5 minutes)
   - Fix any HIGH or CRITICAL issues found

3. **Wordfence → Firewall**
   - Click "Optimize Firewall"
   - Select: "Extended Protection" (recommended)
   - Click "Continue"

4. **Wordfence → Login Security**
   ```
   ✅ Enable 2FA (Two-Factor Authentication)
   ✅ Immediately block invalid usernames
   ✅ Lock out after 3 failed logins (20 minutes)
   ✅ Immediately lock out invalid usernames
   ✅ Throttle login attempts
   ```

5. **Wordfence → All Options** (configure these):

   **Firewall Options:**
   ```
   Protection Level: Extended Protection
   Rate Limiting: Enabled
   Block IPs who send POST requests: Yes (with suspicious payloads)
   ```

   **Login Security:**
   ```
   Lock out after: 3 failed logins
   Lock out duration: 20 minutes
   Immediately lock out invalid usernames: Yes
   Don't let WordPress reveal valid users: Yes
   ```

   **Email Alerts:**
   ```
   Alert on critical problems: Yes
   Email: secretary@nuj-lcb.org.uk
   Alert when IP blocked: Yes (daily digest)
   Alert on login from new device: Yes
   ```

**Test Wordfence:**
- Try logging in with wrong password 3 times
- Verify you get locked out
- Check alert email received

### 2. Enable 2FA for ALL Admins (15 minutes)

**Using Wordfence 2FA:**

1. **For Each Admin User:**
   - Users → All Users
   - Click user → Edit
   - Scroll to "Wordfence Login Security"
   - Click "Activate 2FA"

2. **Setup Process:**
   - Install authenticator app:
     - Google Authenticator (mobile)
     - Authy (mobile + desktop)
     - 1Password (if using password manager)
   - Scan QR code with app
   - Enter 6-digit code to confirm
   - Save recovery codes (print/store securely!)

3. **Test 2FA:**
   - Log out
   - Log in with password
   - Enter 6-digit code from authenticator
   - Verify access granted

**Enforce 2FA for All Admins:**

```php
// Add to wp-config.php or functions.php
// Require 2FA for administrators

add_action('init', 'nuj_enforce_2fa_for_admins');

function nuj_enforce_2fa_for_admins() {
  if (current_user_can('administrator')) {
    $user = wp_get_current_user();
    // Check if 2FA is enabled (Wordfence stores this in user meta)
    $two_factor_enabled = get_user_meta($user->ID, 'wordfence_2fa', true);

    if (!$two_factor_enabled && !is_admin()) {
      // Redirect to 2FA setup page
      wp_redirect(admin_url('profile.php#wordfence-2fa'));
      exit;
    }
  }
}
```

### 3. Disable XML-RPC (2 minutes)

**Method 1: Via Plugin**
```bash
wp plugin install disable-xml-rpc --activate
```

**Method 2: Via wp-config.php**
```php
// Add to wp-config.php
add_filter('xmlrpc_enabled', '__return_false');
```

**Method 3: Via .htaccess**
```apache
# Block XML-RPC
<Files xmlrpc.php>
  Order Deny,Allow
  Deny from all
</Files>
```

**Test:**
Visit `https://nuj-lcb.org.uk/xmlrpc.php`
- Should show: Forbidden or 403 error

### 4. Change Default "admin" Username (5 minutes)

**If you have user "admin":**

```bash
# Via WP-CLI
wp user create newadminusername admin@nuj-lcb.org.uk --role=administrator
wp user delete admin --reassign=newadminusername
```

**Or manually:**
1. Create new admin user with different username
2. Log in as new admin
3. Delete old "admin" user
4. Reassign posts to new admin

**Use unpredictable username:** e.g., `nuj_chair_2026` not `admin` or `administrator`

### 5. Strong Password Policy (10 minutes)

**Enforce Strong Passwords:**

```bash
wp plugin install password-policy-manager --activate
```

**Configure:**
1. Settings → Password Policy
2. Set minimum length: 20 characters
3. Require: Uppercase, lowercase, numbers, symbols
4. Prevent: Common passwords, username in password
5. Enforce password rotation: 90 days

**For All Existing Admins:**
1. Users → All Users
2. Send password reset to each admin
3. Verify they set strong password (20+ chars)

**Use Password Manager:**
- Recommend: Bitwarden, 1Password, LastPass
- NEVER reuse passwords
- Store passwords securely

### 6. Disable File Editing (1 minute)

**Add to wp-config.php:**

```php
// Disable file editing from WordPress admin
define('DISALLOW_FILE_EDIT', true);

// Disable plugin/theme installation
define('DISALLOW_FILE_MODS', true); // Remove if you need to update plugins
```

This prevents attackers from editing theme/plugin files if they compromise admin account.

### 7. Force HTTPS (5 minutes)

**Add to wp-config.php:**

```php
// Force HTTPS
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
  $_SERVER['HTTPS'] = 'on';
}
```

**Update Site URL:**
```bash
wp option update home 'https://nuj-lcb.org.uk'
wp option update siteurl 'https://nuj-lcb.org.uk'
```

**Test:**
- Visit `http://nuj-lcb.org.uk` → should redirect to HTTPS

---

## 🟡 HIGH PRIORITY: This Week

### 8. Set Up Encrypted Backups (1 hour)

**Install UpdraftPlus:**

```bash
wp plugin install updraftplus --activate
```

**Configure UpdraftPlus:**

1. **Settings → UpdraftPlus Backups**

2. **Files backup schedule:**
   - Daily at 2:00 AM
   - Keep: 7 days

3. **Database backup schedule:**
   - Daily at 3:00 AM
   - Keep: 7 days

4. **Include in backup:**
   - ✅ Plugins
   - ✅ Themes
   - ✅ Uploads
   - ✅ wp-content
   - ✅ Database

5. **Remote storage:**
   - Recommended: Backblaze B2 (cheapest)
   - Alternative: Dropbox, Google Drive

**Encrypt Backups:**

Free tier: No encryption
**Upgrade to UpdraftPlus Premium** (£70/year) for encryption

**Or: Manual encrypted backup script**

Create `/root/backup-nuj.sh`:

```bash
#!/bin/bash
# SPDX-License-Identifier: PMPL-1.0-or-later
# Encrypted WordPress backup for NUJ LCB

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/nuj-lcb"
WP_DIR="/var/www/html"
DB_NAME="wordpress"
DB_USER="wpuser"
DB_PASS="[FROM_ENV_FILE]"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Database backup
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Files backup
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" -C "$WP_DIR" wp-content/

# Encrypt with GPG
# Use secretary's public key
gpg --encrypt --recipient secretary@nuj-lcb.org.uk "$BACKUP_DIR/db_$DATE.sql.gz"
gpg --encrypt --recipient secretary@nuj-lcb.org.uk "$BACKUP_DIR/files_$DATE.tar.gz"

# Upload to remote storage (e.g., Backblaze)
rclone copy "$BACKUP_DIR/db_$DATE.sql.gz.gpg" backblaze:nuj-backups/
rclone copy "$BACKUP_DIR/files_$DATE.tar.gz.gpg" backblaze:nuj-backups/

# Clean up local files older than 7 days
find "$BACKUP_DIR" -name "*.gpg" -mtime +7 -delete

# Clean up remote files older than 30 days
rclone delete backblaze:nuj-backups/ --min-age 30d

echo "Backup completed: $DATE"
```

**Make executable and schedule:**

```bash
chmod +x /root/backup-nuj.sh

# Add to crontab (daily at 4:00 AM)
crontab -e
# Add line:
0 4 * * * /root/backup-nuj.sh >> /var/log/nuj-backup.log 2>&1
```

**Test backup:**
```bash
/root/backup-nuj.sh
# Check backup files created
ls -lh /var/backups/nuj-lcb/
```

### 9. Configure Security Headers (30 minutes)

**Via Caddy (recommended):**

Edit `Caddyfile`:

```caddyfile
nuj-lcb.org.uk {
  # Force HTTPS
  redir http://{host}{uri} https://{host}{uri} permanent

  # Security headers
  header {
    # HSTS (force HTTPS for 1 year)
    Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"

    # Prevent MIME sniffing
    X-Content-Type-Options "nosniff"

    # Prevent clickjacking
    X-Frame-Options "SAMEORIGIN"

    # XSS protection
    X-XSS-Protection "1; mode=block"

    # Referrer policy
    Referrer-Policy "strict-origin-when-cross-origin"

    # Permissions policy (disable unnecessary features)
    Permissions-Policy "geolocation=(), microphone=(), camera=(), payment=()"

    # Content Security Policy (start with report-only)
    Content-Security-Policy-Report-Only "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self'; frame-ancestors 'self';"
  }

  # Reverse proxy to WordPress
  reverse_proxy wordpress:80
}
```

**Test Headers:**
Visit: https://securityheaders.com/?q=https://nuj-lcb.org.uk

**Target Grade**: A or A+

### 10. Database Security (15 minutes)

**Create separate DB user per service:**

```sql
-- MariaDB shell

-- WordPress user (read/write to wordpress DB only)
CREATE USER 'wpuser'@'localhost' IDENTIFIED BY '[STRONG_PASSWORD]';
GRANT SELECT, INSERT, UPDATE, DELETE ON wordpress.* TO 'wpuser'@'localhost';

-- Backup user (read-only)
CREATE USER 'backup'@'localhost' IDENTIFIED BY '[STRONG_PASSWORD]';
GRANT SELECT, LOCK TABLES ON *.* TO 'backup'@'localhost';

FLUSH PRIVILEGES;
```

**Enable MariaDB encryption at rest:**

Edit `/etc/my.cnf.d/server.cnf`:

```ini
[mysqld]
# Encryption at rest
plugin-load-add=file_key_management
file_key_management_filename=/etc/mysql/encryption/keyfile
innodb_encrypt_tables=ON
innodb_encrypt_log=ON
innodb_encryption_threads=4
innodb_encryption_rotate_key_age=1
```

**Generate encryption key:**

```bash
mkdir -p /etc/mysql/encryption
openssl rand -base64 32 > /etc/mysql/encryption/keyfile
chmod 600 /etc/mysql/encryption/keyfile
chown mysql:mysql /etc/mysql/encryption/keyfile

systemctl restart mariadb
```

### 11. Activity Monitoring (20 minutes)

**Install WP Activity Log:**

```bash
wp plugin install wp-security-audit-log --activate
```

**Configure:**

1. **Activity Log → Settings**
2. **Events to Log:**
   - ✅ User logins/logouts
   - ✅ User profile changes
   - ✅ User role changes
   - ✅ Plugin/theme installs
   - ✅ File changes
   - ✅ Post/page changes
   - ✅ Widget changes
   - ✅ Database changes

3. **Email Notifications:**
   - Send to: secretary@nuj-lcb.org.uk
   - Alert on:
     - New admin user created
     - Plugin installed/activated
     - Theme changed
     - Critical security events

4. **Log Retention:**
   - Keep logs: 180 days
   - Prune old logs: Yes

**Review Logs Weekly:**
- Activity Log → Activity Log
- Filter: Last 7 days
- Look for suspicious activity

---

## 🟢 MEDIUM PRIORITY: This Month

### 12. Malware Scanning (ongoing)

**Wordfence Scheduled Scans:**

1. Wordfence → Scan Options
2. Schedule: Daily at 1:00 AM
3. Scan type: High Sensitivity
4. Email results: Yes (if issues found)

**Manual Scan After Any Changes:**
- After plugin updates
- After theme changes
- After WordPress core updates
- After suspicious activity

### 13. IP Whitelisting for /wp-admin (optional)

**If admins have static IPs:**

Via Caddy:

```caddyfile
nuj-lcb.org.uk {
  @admin {
    path /wp-admin*
    not remote_ip 1.2.3.4 5.6.7.8
  }
  respond @admin "Forbidden" 403
}
```

Via WordPress plugin:

**Install**: Limit Login Attempts Reloaded

1. Settings → Limit Login
2. "Trusted IP Addresses": Add admin IPs
3. Trusted IPs bypass rate limiting

### 14. SSL Certificate Monitoring (automated)

**Caddy handles this automatically** via Let's Encrypt.

**Verify renewal working:**

```bash
# Check certificate expiry
echo | openssl s_client -connect nuj-lcb.org.uk:443 2>/dev/null | openssl x509 -noout -dates

# Caddy auto-renews at 30 days before expiry
```

**Set up external monitoring:**

Free services:
- https://www.ssllabs.com/ssltest/ (manual)
- https://uptimerobot.com/ (automated checks)

### 15. WordPress Hardening Checklist

```php
// wp-config.php - Complete hardened configuration

<?php
// SPDX-License-Identifier: PMPL-1.0-or-later

// ===== SECURITY HARDENING =====

// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Disable plugin/theme installation (remove if you need to update)
// define('DISALLOW_FILE_MODS', true);

// Force HTTPS
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

// Security keys (regenerate monthly from https://api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// Database config
define('DB_NAME', 'wordpress');
define('DB_USER', 'wpuser');
define('DB_PASSWORD', '[FROM_ENV]');
define('DB_HOST', 'database:3306');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// Change database prefix from default 'wp_' to something unique
$table_prefix = 'nujlcb_';

// Disable debug mode in production
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// Limit post revisions
define('WP_POST_REVISIONS', 5);

// Autosave interval (10 minutes)
define('AUTOSAVE_INTERVAL', 600);

// Empty trash after 7 days
define('EMPTY_TRASH_DAYS', 7);

// Disable automatic updates (manual control)
define('AUTOMATIC_UPDATER_DISABLED', false);
define('WP_AUTO_UPDATE_CORE', 'minor'); // Auto-update minor versions only

// ===== END SECURITY HARDENING =====

/* That's all, stop editing! Happy publishing. */

if (!defined('ABSPATH')) {
  define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
```

---

## Deployment Checklist

### Before Going Live

**Security:**
- [ ] Wordfence installed and configured
- [ ] 2FA enabled for all admin users
- [ ] Strong passwords enforced (20+ chars)
- [ ] XML-RPC disabled
- [ ] File editing disabled
- [ ] HTTPS forced site-wide
- [ ] Security headers configured
- [ ] SSL certificate valid
- [ ] Backups working (test restore!)
- [ ] Activity logging enabled

**WordPress:**
- [ ] WordPress updated to latest version
- [ ] All plugins updated
- [ ] All themes updated
- [ ] Unused plugins deleted
- [ ] Unused themes deleted
- [ ] Default "Hello World" post deleted
- [ ] Default "Sample Page" deleted
- [ ] Permalink structure set (/post-name/)
- [ ] Timezone set correctly
- [ ] Comment moderation enabled

**Content:**
- [ ] Privacy Policy published
- [ ] Cookie Policy published
- [ ] AI Usage Policy published
- [ ] Terms of Service published
- [ ] About Us page complete
- [ ] Contact page complete
- [ ] Members area configured

**Testing:**
- [ ] Test registration/login flow
- [ ] Test password reset
- [ ] Test 2FA login
- [ ] Test member area access
- [ ] Test file downloads
- [ ] Test on mobile devices
- [ ] Test in different browsers
- [ ] Verify emails sending
- [ ] Test form submissions

**Compliance:**
- [ ] GDPR compliance verified
- [ ] Cookie consent working
- [ ] Privacy policy accurate
- [ ] Data retention policy set
- [ ] Breach response plan ready

### Launch Day

1. **Final backup** (before DNS cutover)
2. **Change DNS** to point to new server
3. **Monitor for 4 hours**:
   - Check error logs
   - Watch Wordfence alerts
   - Test key features
4. **Announce to members** once stable

### Post-Launch (Week 1)

- [ ] Daily: Check Wordfence alerts
- [ ] Daily: Review activity log
- [ ] Daily: Check backup ran successfully
- [ ] End of week: Full security audit
- [ ] End of week: Member feedback survey

---

## Incident Response

**IF Security Breach Detected:**

1. **IMMEDIATE (within 1 hour):**
   - Take site offline (maintenance mode)
   - Disconnect database
   - Preserve logs (don't delete anything!)
   - Notify National NUJ
   - Change all passwords

2. **URGENT (within 24 hours):**
   - Determine scope of breach
   - Identify compromised data
   - Notify ICO (UK GDPR requirement)
   - Email all affected members
   - Contact police if criminal activity

3. **RECOVERY (within 72 hours):**
   - Restore from clean backup
   - Patch vulnerabilities
   - Re-scan for malware
   - Implement additional security
   - Document incident for review

**Breach Notification Template:**

```
Subject: URGENT: Security Incident Notification

Dear [Member Name],

We are writing to inform you of a security incident affecting the NUJ London Central Branch website on [DATE].

WHAT HAPPENED:
[Brief description]

WHAT DATA WAS AFFECTED:
[List specific data types: names, emails, etc.]

WHAT WE ARE DOING:
- Site has been secured
- Vulnerability patched
- Additional security measures implemented
- ICO and police notified

WHAT YOU SHOULD DO:
- Change your website password immediately
- Watch for suspicious emails/calls
- Monitor for identity theft
- We can provide identity protection assistance

We sincerely apologize for this incident. Member security is our highest priority.

Contact: security@nuj-lcb.org.uk
Phone: [Contact number]

In solidarity,
NUJ London Central Branch Executive
```

---

## Monthly Security Review

**Last Sunday of each month:**

1. Update WordPress, plugins, themes
2. Review Wordfence scan results
3. Review activity logs for anomalies
4. Test backup restore
5. Rotate security keys (wp-config.php salts)
6. Review admin user list (remove if left branch)
7. Check SSL certificate expiry
8. Review failed login attempts
9. Update security documentation
10. Brief officers on any issues

**Annual Security Audit:**

Hire external security firm to:
- Penetration test
- Social engineering test (phishing drill)
- Code review
- Infrastructure audit

Budget: £500-2000

---

## Support Resources

**WordPress Security:**
- https://wordpress.org/support/article/hardening-wordpress/
- https://www.wordfence.com/learn/

**UK Cyber Security:**
- https://www.ncsc.gov.uk/section/advice-guidance/all-topics
- https://www.actionfraud.police.uk/

**Report Issues:**
- ICO (data breach): https://ico.org.uk/for-organisations/report-a-breach/
- Action Fraud: https://www.actionfraud.police.uk/

**Internal Contacts:**
- Security issues: secretary@nuj-lcb.org.uk
- Data protection: dpo@nuj.org.uk
- Emergency: [Branch Secretary Phone]

---

**Security is ongoing, not one-time. Stay vigilant!**
