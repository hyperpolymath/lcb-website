# Security Threat Model - NUJ LCB Production

**Date**: 2026-01-28
**Status**: CRITICAL - Active far-right doxing threat

## Threat Context

### Real, Immediate Dangers

1. **Far-right groups targeting journalists and union members**
   - Doxing (publishing personal info online)
   - Harassment campaigns
   - Physical threats
   - Coordinated attacks on union organizers

2. **Member data at risk**
   - Names, addresses, phone numbers
   - Email addresses
   - Workplace information
   - Membership status
   - Payment details

3. **Attack vectors**
   - Website breaches (steal member database)
   - Social engineering
   - Phishing attacks on admins
   - Brute force password attacks
   - Exploiting WordPress vulnerabilities

## Regulatory Compliance

### Required Standards

| Authority | Requirement | Status |
|-----------|-------------|--------|
| **Charity Commission** | Data protection, financial transparency | ✅ Planned |
| **Certification Officer** | Member records security, union governance | ✅ Planned |
| **UK GDPR/DPA 2018** | Personal data protection, breach notification | ⚠️ Critical |
| **ICO** | Data security measures, privacy by design | ⚠️ Critical |

## CRITICAL Security Measures (Implement NOW)

### 1. Member Data Protection

#### Database Security

```yaml
CRITICAL - Implement Immediately:
  - ✅ Database encryption at rest (LUKS/dm-crypt on VPS)
  - ✅ Strong database passwords (32+ chars, random)
  - ✅ Database firewall (no external access)
  - ✅ Separate database user per service (WordPress, Zulip, BigBlueButton)
  - ✅ Regular encrypted backups (off-site, different provider)
  - ✅ Database audit logging
```

#### WordPress Hardening

```php
// wp-config.php - CRITICAL SECURITY

// 1. Disable file editing (prevent backdoors)
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);

// 2. Security keys (regenerate monthly)
// Get from: https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY', '[REGENERATE MONTHLY]');
// ... all 8 salts

// 3. Force HTTPS everywhere
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

// 4. Disable XML-RPC (common attack vector)
add_filter('xmlrpc_enabled', '__return_false');

// 5. Limit login attempts
// Install plugin: Limit Login Attempts Reloaded

// 6. Hide WordPress version
remove_action('wp_head', 'wp_generator');

// 7. Disable user enumeration
// In .htaccess or server config
```

#### Password Policy

```
MANDATORY for all admins:
- ✅ Minimum 20 characters
- ✅ Argon2id hashing (if available) or bcrypt
- ✅ 2FA via authenticator app (NOT SMS)
- ✅ Password rotation every 90 days
- ✅ No password reuse (last 24 passwords)
- ✅ Bitwarden/1Password for password management
```

### 2. Access Control

#### Admin Accounts

```yaml
CRITICAL:
  - Limit admin accounts to 3 people maximum
  - Use principle of least privilege
  - Separate accounts for different roles:
    - Super Admin (1 person only)
    - Editor (content only, no plugins)
    - Author (posts only)
  - Review admin users monthly
  - Remove accounts immediately on resignation
```

#### IP Whitelisting

```nginx
# Restrict admin access to known IPs
location /wp-admin {
  allow 1.2.3.4;        # Branch office VPN
  allow 5.6.7.8;        # Chair's home IP
  deny all;
}

# Allow login from anywhere (with 2FA)
location = /wp-login.php {
  # Rate limiting only
  limit_req zone=login burst=5;
}
```

### 3. Data Minimization

#### What NOT to Store

```
❌ NEVER store in WordPress:
  - Full home addresses (store postcode only)
  - Phone numbers (use encrypted contact form)
  - Bank details (use payment processor)
  - National Insurance numbers
  - Sensitive health information
  - Political opinions beyond union membership

✅ Store only essential:
  - Name (first name + last initial for public)
  - Email (encrypted)
  - Membership number
  - Workplace (company name only, not address)
  - Membership status
```

#### Member Privacy Settings

```php
// Default ALL members to private visibility
// Only show publicly with explicit opt-in
update_option('default_comment_status', 'closed');
update_option('default_ping_status', 'closed');

// Hide member list from non-members
// Install plugin: Members (by MemberPress)
// Set member directory to "Members Only"
```

### 4. Attack Surface Reduction

#### Disable Unnecessary Features

```php
// wp-config.php

// Disable comments (reduces spam/XSS)
define('DISALLOW_COMMENTS', true);

// Disable pingbacks/trackbacks
define('WP_DISABLE_PINGBACK', true);

// Disable REST API for non-authenticated users
add_filter('rest_authentication_errors', function($result) {
  if (!is_user_logged_in()) {
    return new WP_Error(
      'rest_disabled',
      'REST API disabled for non-authenticated users',
      ['status' => 401]
    );
  }
  return $result;
});
```

#### Plugin Security

```
ONLY install plugins that are:
  ✅ Actively maintained (updated in last 6 months)
  ✅ 100,000+ active installs OR security-focused
  ✅ 4.5+ star rating
  ✅ Regular security audits

REQUIRED security plugins:
  1. Wordfence Security (free tier sufficient)
  2. Two Factor Authentication
  3. WP Activity Log (audit trail)
  4. UpdraftPlus (encrypted backups)
  5. Really Simple SSL

PROHIBITED plugins:
  ❌ Contact Form 7 (use WPForms with encryption)
  ❌ Any plugin with "nulled" or "premium free"
  ❌ Plugins not updated in 1+ year
```

### 5. Monitoring & Incident Response

#### Real-Time Alerts

```yaml
Configure alerts for:
  - Failed login attempts (>5 in 1 hour)
  - New admin user creation
  - Plugin/theme installation
  - Database exports
  - Large file uploads
  - Unusual traffic patterns

Send alerts to:
  - Branch Secretary (email + SMS)
  - Tech officer (Slack/email)
  - Wordfence security team
```

#### Breach Response Plan

```
IF member data is compromised:

IMMEDIATE (within 1 hour):
  1. Take site offline
  2. Disconnect database
  3. Preserve logs
  4. Notify National NUJ immediately

URGENT (within 24 hours):
  5. Notify ICO (UK GDPR breach reporting)
  6. Notify Certification Officer
  7. Email all members about breach
  8. Offer identity protection services

FOLLOWUP (within 72 hours):
  9. Full security audit
  10. Patch vulnerabilities
  11. Restore from clean backup
  12. Implement additional security measures
  13. Report to police if criminal activity suspected
```

### 6. Encryption Everywhere

#### In Transit

```
✅ TLS 1.3 only (disable TLS 1.0, 1.1, 1.2)
✅ HSTS with preload
✅ Certificate pinning (for admin pages)
✅ Force HTTPS site-wide
✅ Encrypt WordPress → Database connection
```

#### At Rest

```bash
# VPS disk encryption
cryptsetup luksFormat /dev/sda1
cryptsetup luksOpen /dev/sda1 encrypted_disk

# Database encryption
# In my.cnf (MariaDB)
[mysqld]
innodb_file_per_table = 1
innodb_encrypt_tables = ON
innodb_encrypt_log = ON
innodb_encryption_threads = 4
```

#### Backups

```bash
# Encrypted backup script
#!/bin/bash
# Daily encrypted backup to off-site location

# Database dump
mysqldump --all-databases | gzip > backup.sql.gz

# Encrypt with GPG (branch secretary's public key)
gpg --encrypt --recipient secretary@nuj-lcb.org.uk backup.sql.gz

# Upload to off-site (different provider than VPS)
rclone copy backup.sql.gz.gpg backblaze:nuj-backups/

# Rotate (keep 30 days)
rclone delete backblaze:nuj-backups/ --min-age 30d
```

## Compliance Checklist

### UK GDPR/DPA 2018

- [ ] Data Protection Impact Assessment (DPIA) completed
- [ ] Privacy Policy published and accessible
- [ ] Cookie consent (no tracking without consent)
- [ ] Data retention policy (delete after 7 years)
- [ ] Member data access requests process
- [ ] Data breach notification process (<72 hours to ICO)
- [ ] Data Processing Agreement with hosting provider
- [ ] Security measures documented
- [ ] Staff training on data protection

### Certification Officer (Trade Union)

- [ ] Secure member records
- [ ] Financial transparency
- [ ] Election integrity (if online voting)
- [ ] Complaint handling process
- [ ] Member register protection

### Charity Commission

- [ ] Financial records security
- [ ] Trustee information protected
- [ ] Donor privacy (if applicable)
- [ ] Annual returns data security

## WordPress Known Vulnerabilities

### Critical WordPress Security Issues

```
TERMINATED immediately:
  ❌ XML-RPC (disable completely)
  ❌ User enumeration (/?author=1)
  ❌ Old PHP versions (<8.1)
  ❌ Weak password hashing (MD5/SHA-1)
  ❌ Unpatched plugins
  ❌ Default admin username ("admin")
  ❌ Directory listing
  ❌ Version disclosure
```

### WordPress Auto-Updates

```php
// wp-config.php
// Auto-update everything for security
define('WP_AUTO_UPDATE_CORE', true);
add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');
```

## Member Communication Security

### Secure Channels

```
For sensitive union business:

✅ Encrypted email (ProtonMail or Tutanota)
✅ Signal (for real-time chat)
✅ Zulip (self-hosted, encrypted at rest)
✅ BigBlueButton (self-hosted, E2EE)

❌ NEVER use:
  ❌ Unencrypted email for member lists
  ❌ Facebook groups (Meta has access)
  ❌ WhatsApp (Meta-owned)
  ❌ SMS for sensitive info
  ❌ Zoom (unless E2EE enabled)
```

## Physical Security

### VPS Provider Security

```
Choose provider with:
  ✅ UK/EU jurisdiction (GDPR compliant)
  ✅ ISO 27001 certified
  ✅ SOC 2 Type II audit
  ✅ Physical security (data center access logs)
  ✅ DDoS protection
  ✅ Backup locations in different geographic regions

Avoid:
  ❌ US-based (CLOUD Act, Patriot Act)
  ❌ Shared hosting (too many attack vectors)
  ❌ Cheap/unknown providers
```

### Access Controls

```
VPS root access:
  - SSH key only (disable password auth)
  - 2FA for SSH (Google Authenticator)
  - Fail2ban (auto-block brute force)
  - Non-standard SSH port
  - Whitelist IPs if possible
  - Log all access attempts
  - Review logs weekly
```

## Far-Right Threat Specific Measures

### Doxing Prevention

```
Public-facing content:
  - Use first names only (no surnames)
  - No home addresses
  - No personal phone numbers
  - No photos of members without consent
  - Scrub EXIF data from images
  - Use stock photos instead of real offices

Contact forms:
  - Use generic email (contact@nuj-lcb.org.uk)
  - No direct officer emails on public pages
  - Use Cloudflare email protection
```

### DDoS Protection

```
Via Cloudflare:
  - Enable "I'm Under Attack" mode if needed
  - Rate limiting on login pages
  - Bot detection
  - Challenge page for suspicious traffic
  - Cache static content
  - Hide real server IP
```

### Incident Reporting

```
If far-right harassment detected:
  1. Document everything (screenshots, URLs, timestamps)
  2. Report to police (hate crime unit)
  3. Report to hosting provider
  4. Report to Cloudflare abuse
  5. Notify affected members
  6. Consider temporary site lockdown
  7. Increase security monitoring
```

## Security Review Schedule

```
Daily:
  - Check failed login attempts
  - Review traffic for anomalies

Weekly:
  - Review server logs
  - Check for WordPress/plugin updates
  - Scan for malware (Wordfence)

Monthly:
  - Update all passwords
  - Review admin user list
  - Test backups (restore drill)
  - Security audit

Quarterly:
  - Full penetration test
  - Review security policies
  - Staff security training
  - Incident response drill

Annually:
  - External security audit
  - GDPR compliance review
  - Update threat model
```

## Cost Estimate

```
Annual Security Budget:

FREE:
  - Wordfence (free tier)
  - Let's Encrypt SSL
  - Cloudflare (free tier)
  - UpdraftPlus (basic)

PAID:
  - VPS with encryption (~£40/month) = £480/year
  - Password manager (Bitwarden Teams) = £40/year
  - Security plugin pro (Wordfence) = £99/year
  - Backup storage (Backblaze) = £50/year
  - External audit (optional) = £500-2000/year

TOTAL: £669-2169/year ($800-2600)
```

## Immediate Action Items (This Week)

1. **Enable 2FA on all admin accounts** (TODAY)
2. **Change all default passwords** (TODAY)
3. **Install Wordfence** (TODAY)
4. **Disable XML-RPC** (TODAY)
5. **Review admin user list** (TODAY)
6. **Set up encrypted backups** (THIS WEEK)
7. **Configure security headers** (THIS WEEK)
8. **Create breach response plan** (THIS WEEK)

---

**Threat Level**: 🔴 HIGH (active far-right targeting)
**Priority**: 🚨 CRITICAL (protect member data NOW)
**Timeline**: Immediate implementation required

**Questions?** Email: security@nuj-lcb.org.uk (to be set up)
