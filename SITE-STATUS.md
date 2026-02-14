# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2025 Jonathan D.A. Jewell <jonathan.jewell@open.ac.uk>
#
# NUJ LCB Site Configuration Status

**Last Updated:** 2026-01-28

## ✅ WordPress Installation

- **Version:** 6.9 (latest)
- **PHP:** 8.3.20
- **Database:** MariaDB 11.2
- **URL (local):** http://localhost:8080
- **URL (production):** https://nuj-lcb.org.uk

## ✅ Theme

- **Active Theme:** Newspaperup 1.6.1
- **Status:** Activated and ready
- **Design:** Professional news/magazine layout (same as nuj-prc.org.uk)

**Theme Customization Needed:**
- [ ] Set primary color to #461cfb (purple) - do via Appearance → Customize in admin
- [ ] Upload NUJ LCB logo
- [ ] Configure typography if needed

## ✅ Pages Created

| Page | Slug | Purpose |
|------|------|---------|
| Home | `/` | Front page - welcome message |
| About | `/about` | Branch information and officers |
| News | `/news` | Blog posts and updates |
| Events & Training | `/events-training` | Meetings, workshops, training |
| Contact | `/contact` | Contact details and address |
| Member Resources | `/member-resources` | Resources for members |

## ✅ Navigation Menu

**Main Navigation** (assigned to primary location):
1. Home
2. About
3. News
4. Events & Training
5. Member Resources
6. Contact

## ✅ Plugins Installed

| Plugin | Version | Purpose | Status |
|--------|---------|---------|--------|
| **Wordfence Security** | 8.1.4 | Firewall, malware scanning | ✅ Active |
| **UpdraftPlus** | 1.26.1 | Automated backups | ✅ Active |
| **Contact Form 7** | 6.1.4 | Contact forms | ✅ Active |
| **WP Mail SMTP** | 4.7.1 | Email configuration | ✅ Active |
| **Redirection** | 5.6.1 | 301 redirects management | Inactive (activate if needed) |

## ✅ Settings Configured

- **Site Title:** NUJ London Central Branch
- **Tagline:** National Union of Journalists - London Central Branch
- **Timezone:** Europe/London
- **Date Format:** j F Y (e.g., "28 January 2026")
- **Time Format:** H:i (24-hour, e.g., "14:30")
- **Permalinks:** /%postname%/ (SEO-friendly)
- **Comments:** Disabled by default
- **User Registration:** Disabled (security)
- **Front Page:** Static page (Home)
- **Posts Page:** News

## 🔧 Configuration To Do After Deployment

### In WordPress Admin:

1. **Change Admin Password:**
   - Current: `ChangeMeAfterSetup123!`
   - Go to Users → Your Profile → Generate new password

2. **Customize Theme Colors:**
   - Appearance → Customize → Colors
   - Primary: #461cfb (purple, matching nuj-prc.org.uk)

3. **Upload Logo:**
   - Appearance → Customize → Site Identity
   - Upload NUJ LCB logo

4. **Configure SMTP Email:**
   - Settings → WP Mail SMTP
   - Use Verpex SMTP details:
     - Host: `mail.nuj-lcb.org.uk` (or Verpex SMTP server)
     - Port: `587` (TLS) or `465` (SSL)
     - Create email: `noreply@nuj-lcb.org.uk` in cPanel first

5. **Configure Wordfence:**
   - Wordfence → Dashboard
   - Run initial scan
   - Set firewall to "Enabled and Protecting"

6. **Configure UpdraftPlus:**
   - Settings → UpdraftPlus Backups
   - Set backup schedule (recommended: Daily database, Weekly files)
   - Configure remote storage (optional: Dropbox, Google Drive)

7. **Create Contact Form:**
   - Contact Form 7 → Add New
   - Create simple contact form
   - Add shortcode to Contact page

### In cPanel (After Upload):

8. **Set File Permissions:**
   - wp-config.php: 600 (read/write owner only)
   - Directories: 755
   - Files: 644

9. **Cron Jobs:**
   - Add WordPress cron: `*/15 * * * * wget -q -O - https://nuj-lcb.org.uk/wp-cron.php?doing_wp_cron >/dev/null 2>&1`

## 🔒 Security Status

- ✅ Strong database passwords generated
- ✅ Admin username is "admin" (⚠️ consider changing to something less obvious)
- ✅ Comments disabled by default (prevents spam)
- ✅ User registration disabled
- ✅ Wordfence installed (firewall + malware scanner)
- ✅ UpdraftPlus installed (backup recovery)
- ✅ HTTPS will be enforced (via .htaccess)
- ⏳ File edit disabled - add to wp-config.php: `define('DISALLOW_FILE_EDIT', true);`

## 📊 Site Statistics

- **Pages:** 6 pages created
- **Posts:** 0 (clean slate for news)
- **Menu Items:** 6 items in Main Navigation
- **Themes:** 1 (Newspaperup only - no bloat)
- **Plugins:** 5 active, 1 inactive (Redirection - optional)
- **Database Size:** ~2-3 MB (minimal, clean install)

## 🚀 Ready for Deployment

The site is configured and ready to export to Verpex. Follow these guides:

1. **Export:** Run `./export-for-verpex.sh`
2. **Deploy:** Follow `VERPEX-DEPLOYMENT.md` step-by-step
3. **Test:** Verify all pages load correctly
4. **Finalize:** Complete post-deployment configurations above

## Admin Access

**Local (Development):**
- URL: http://localhost:8080/wp-admin
- Username: `admin`
- Password: `ChangeMeAfterSetup123!`

**Production (After Deployment):**
- URL: https://nuj-lcb.org.uk/wp-admin
- Username: `admin`
- Password: **CHANGE IMMEDIATELY AFTER FIRST LOGIN**

---

**Configured by:** Claude Sonnet 4.5
**Date:** 2026-01-28
