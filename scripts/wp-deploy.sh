#!/usr/bin/env bash
# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <jonathan.jewell@open.ac.uk>
#
# WP-CLI deployment helper for NUJ London Central Branch website
# Usage: bash scripts/wp-deploy.sh
#
# Prerequisites:
#   - WP-CLI installed (wp --info)
#   - WordPress installed at target path
#   - Database created and wp-config.php configured
#   - SSH access to Verpex (or run locally in document root)
#
# This script is idempotent — safe to run multiple times.

set -euo pipefail

# ============================================================================
# CONFIGURATION
# ============================================================================

SITE_URL="${SITE_URL:-https://nuj-lcb.org.uk}"
SITE_TITLE="NUJ London Central Branch"
SITE_TAGLINE="The voice of journalists in central London"
ADMIN_USER="${WP_ADMIN_USER:-nujlcb_admin}"
ADMIN_EMAIL="${WP_ADMIN_EMAIL:-contact@nuj-lcb.org.uk}"
TIMEZONE="Europe/London"
DATE_FORMAT="j F Y"
TIME_FORMAT="H:i"

# Content source directory (relative to repo root)
CONTENT_DIR="$(cd "$(dirname "$0")/.." && pwd)/content"

echo "=== NUJ LCB WordPress Deployment ==="
echo "Site: ${SITE_URL}"
echo "Content: ${CONTENT_DIR}"
echo ""

# ============================================================================
# 1. WORDPRESS SETTINGS
# ============================================================================

echo "--- [1/8] Configuring WordPress settings ---"

wp option update blogname "${SITE_TITLE}"
wp option update blogdescription "${SITE_TAGLINE}"
wp option update timezone_string "${TIMEZONE}"
wp option update date_format "${DATE_FORMAT}"
wp option update time_format "${TIME_FORMAT}"
wp option update start_of_week 1
wp option update permalink_structure "/%postname%/"
wp option update default_comment_status "closed"
wp option update default_ping_status "closed"
wp option update blog_public 1
wp option update uploads_use_yearmonth_folders 1

echo "  WordPress settings configured."

# ============================================================================
# 2. PLUGIN INSTALLATION
# ============================================================================

echo "--- [2/8] Installing plugins ---"

PLUGINS=(
    "wordfence"
    "litespeed-cache"
    "bbpress"
    "members"
    "wp-mail-smtp"
    "updraftplus"
    "contact-form-7"
    "wp-security-audit-log"
    "gdpr-cookie-compliance"
    "download-monitor"
    "redirection"
    "wordpress-seo"
)

for plugin in "${PLUGINS[@]}"; do
    if wp plugin is-installed "${plugin}" 2>/dev/null; then
        echo "  ${plugin}: already installed, updating..."
        wp plugin update "${plugin}" || true
    else
        echo "  ${plugin}: installing..."
        wp plugin install "${plugin}" --activate || echo "  WARNING: Failed to install ${plugin}"
    fi
done

echo "  Plugins installed."

# ============================================================================
# 3. THEME ACTIVATION
# ============================================================================

echo "--- [3/8] Activating Sinople theme ---"

if wp theme is-installed sinople 2>/dev/null; then
    wp theme activate sinople
    echo "  Sinople theme activated."
else
    echo "  WARNING: Sinople theme not found in wp-content/themes/"
    echo "  Upload it manually: scp -r wp-content/themes/sinople/ user@server:path/wp-content/themes/"
    echo "  Falling back to default theme."
fi

# ============================================================================
# 4. PAGE CREATION
# ============================================================================

echo "--- [4/8] Creating pages ---"

create_page() {
    local title="$1"
    local slug="$2"
    local content_file="$3"
    local status="${4:-publish}"

    if wp post list --post_type=page --name="${slug}" --format=count 2>/dev/null | grep -q '^0$'; then
        if [ -f "${content_file}" ]; then
            local content
            content=$(cat "${content_file}")
            wp post create --post_type=page \
                --post_title="${title}" \
                --post_name="${slug}" \
                --post_status="${status}" \
                --post_content="${content}"
            echo "  Created: ${title} (/${slug}/)"
        else
            wp post create --post_type=page \
                --post_title="${title}" \
                --post_name="${slug}" \
                --post_status="${status}" \
                --post_content="<p>Content coming soon.</p>"
            echo "  Created: ${title} (/${slug}/) — placeholder content"
        fi
    else
        echo "  Exists: ${title} (/${slug}/)"
    fi
}

# Main pages
create_page "About Us"          "about-us"    "${CONTENT_DIR}/pages/about-us.md"
create_page "Contact"           "contact"     "${CONTENT_DIR}/pages/contact.md"
create_page "Join the NUJ"      "join"        "${CONTENT_DIR}/pages/join-us.md"
create_page "Members Area"      "members"     "${CONTENT_DIR}/pages/members-area.md" "private"
create_page "News & Updates"    "news"        "${CONTENT_DIR}/pages/linkedin-feed.md"

# Policy pages
create_page "AI Usage Policy"   "ai-policy"   "${CONTENT_DIR}/policies/ai-usage-policy.md"
create_page "Legal Information"  "legal"       "${CONTENT_DIR}/policies/imprint-impressum.md"

# Homepage
create_page "Home"              "home"        "${CONTENT_DIR}/mockups/homepage.html"

echo "  Pages created."

# ============================================================================
# 5. STATIC FRONT PAGE
# ============================================================================

echo "--- [5/8] Setting static front page ---"

HOMEPAGE_ID=$(wp post list --post_type=page --name=home --field=ID 2>/dev/null || echo "")
NEWS_ID=$(wp post list --post_type=page --name=news --field=ID 2>/dev/null || echo "")

if [ -n "${HOMEPAGE_ID}" ]; then
    wp option update show_on_front "page"
    wp option update page_on_front "${HOMEPAGE_ID}"
    if [ -n "${NEWS_ID}" ]; then
        wp option update page_for_posts "${NEWS_ID}"
    fi
    echo "  Static front page set to Home, posts page set to News."
else
    echo "  WARNING: Homepage not found, skipping front page setup."
fi

# ============================================================================
# 6. NAVIGATION MENU
# ============================================================================

echo "--- [6/8] Creating navigation menu ---"

if ! wp menu list --format=ids 2>/dev/null | grep -q .; then
    wp menu create "Main Navigation"
fi

MENU_ID=$(wp menu list --fields=term_id,name --format=csv 2>/dev/null | grep "Main Navigation" | cut -d',' -f1 || echo "")

if [ -n "${MENU_ID}" ]; then
    # Add pages to menu (order matters)
    for slug in home about-us news join contact members; do
        PAGE_ID=$(wp post list --post_type=page --name="${slug}" --field=ID 2>/dev/null || echo "")
        if [ -n "${PAGE_ID}" ]; then
            wp menu item add-post "${MENU_ID}" "${PAGE_ID}" 2>/dev/null || true
        fi
    done

    # Assign menu to theme location
    wp menu location assign "${MENU_ID}" primary 2>/dev/null || \
    wp menu location assign "${MENU_ID}" menu-1 2>/dev/null || \
        echo "  WARNING: Could not assign menu to theme location. Check theme menu locations."

    echo "  Navigation menu created and assigned."
else
    echo "  WARNING: Could not create navigation menu."
fi

# ============================================================================
# 7. BBPRESS FORUMS
# ============================================================================

echo "--- [7/8] Creating bbPress forums ---"

if wp plugin is-active bbpress 2>/dev/null; then
    create_forum() {
        local title="$1"
        local slug="$2"
        local desc="$3"

        if wp post list --post_type=forum --name="${slug}" --format=count 2>/dev/null | grep -q '^0$'; then
            wp post create --post_type=forum \
                --post_title="${title}" \
                --post_name="${slug}" \
                --post_status="publish" \
                --post_content="${desc}"
            echo "  Created forum: ${title}"
        else
            echo "  Forum exists: ${title}"
        fi
    }

    create_forum "General Discussion" "general-discussion" \
        "Open discussion for all NUJ London Central Branch members."
    create_forum "Freelance & Rates" "freelance-rates" \
        "Discussion about freelance journalism, rates, contracts, and working conditions."
    create_forum "Events & Training" "events-training" \
        "Upcoming events, training sessions, CPD opportunities, and networking."
    create_forum "Branch Business" "branch-business" \
        "Official branch business, motions, AGM matters, and committee reports."

    echo "  bbPress forums configured."
else
    echo "  SKIP: bbPress not active. Install and activate it first."
fi

# ============================================================================
# 8. SECURITY HARDENING
# ============================================================================

echo "--- [8/8] Security hardening ---"

# Disable pingbacks
wp option update default_pingback_flag 0

# Disable user registration (admin manually promotes members)
wp option update users_can_register 0

# Limit post revisions (matches wp-config-security.php)
wp option update wp_post_revisions 10

# Remove default "Hello World" post and "Sample Page"
DEFAULT_POST=$(wp post list --post_type=post --name="hello-world" --field=ID 2>/dev/null || echo "")
if [ -n "${DEFAULT_POST}" ]; then
    wp post delete "${DEFAULT_POST}" --force
    echo "  Deleted default 'Hello World' post."
fi

SAMPLE_PAGE=$(wp post list --post_type=page --name="sample-page" --field=ID 2>/dev/null || echo "")
if [ -n "${SAMPLE_PAGE}" ]; then
    wp post delete "${SAMPLE_PAGE}" --force
    echo "  Deleted default 'Sample Page'."
fi

# Remove default plugins
for unwanted in akismet hello; do
    if wp plugin is-installed "${unwanted}" 2>/dev/null; then
        wp plugin deactivate "${unwanted}" 2>/dev/null || true
        wp plugin delete "${unwanted}" 2>/dev/null || true
        echo "  Removed unwanted plugin: ${unwanted}"
    fi
done

echo ""
echo "=== Deployment Complete ==="
echo ""
echo "Next manual steps:"
echo "  1. Set admin password: wp user update ${ADMIN_USER} --user_pass=<STRONG_PASSWORD>"
echo "  2. Configure Wordfence: Enable WAF, 2FA, login lockout"
echo "  3. Configure LiteSpeed Cache: Enable cache, browser cache, image optimization"
echo "  4. Configure WP Mail SMTP: Settings > WP Mail SMTP"
echo "  5. Configure UpdraftPlus: Encrypted daily backups to remote storage"
echo "  6. Test contact form: Create form in Contact Form 7, add to Contact page"
echo "  7. Configure Members plugin: Create nuj_member role, restrict Members Area"
echo "  8. Review all pages and update placeholder content"
echo "  9. Run accessibility audit: Lighthouse + aXe"
echo " 10. Verify security headers: securityheaders.com"
