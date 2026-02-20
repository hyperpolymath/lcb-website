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
# This script is idempotent and safe to re-run.

set -euo pipefail

# ============================================================================
# CONFIGURATION
# ============================================================================

SITE_URL="${SITE_URL:-https://nuj-lcb.org.uk}"
SOURCE_URL="${SOURCE_URL:-}"
RUN_SEARCH_REPLACE="${RUN_SEARCH_REPLACE:-0}"

SITE_TITLE="${SITE_TITLE:-NUJ London Central Branch}"
SITE_TAGLINE="${SITE_TAGLINE:-The voice of journalists in central London}"
ADMIN_USER="${WP_ADMIN_USER:-nujlcb_admin}"
ADMIN_EMAIL="${WP_ADMIN_EMAIL:-contact@nuj-lcb.org.uk}"
TIMEZONE="${TIMEZONE:-Europe/London}"
DATE_FORMAT="${DATE_FORMAT:-j F Y}"
TIME_FORMAT="${TIME_FORMAT:-H:i}"
UPDATE_EXISTING_CONTENT="${UPDATE_EXISTING_CONTENT:-0}"

# Content source directory (relative to repo root)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
CONTENT_DIR="${CONTENT_DIR:-${REPO_ROOT}/content}"
WP_PATH="${WP_PATH:-$(pwd)}"

WP_ARGS=(--path="${WP_PATH}")
if [ "${WP_ALLOW_ROOT:-0}" = "1" ]; then
    WP_ARGS+=(--allow-root)
fi

wpcli() {
    wp "${WP_ARGS[@]}" "$@"
}

warn() {
    echo "  WARNING: $*"
}

die() {
    echo "ERROR: $*" >&2
    exit 1
}

first_id_for_slug() {
    local post_type="$1"
    local slug="$2"

    wpcli post list \
        --post_type="${post_type}" \
        --name="${slug}" \
        --post_status=publish,private,draft,pending,future,trash \
        --field=ID \
        --format=ids 2>/dev/null | awk '{print $1}'
}

page_in_menu() {
    local menu_id="$1"
    local page_id="$2"

    wpcli menu item list "${menu_id}" --fields=object_id --format=csv 2>/dev/null \
        | tail -n +2 \
        | tr -d '"' \
        | grep -Fxq "${page_id}"
}

echo "=== NUJ LCB WordPress Deployment ==="
echo "Site URL: ${SITE_URL}"
echo "WP path: ${WP_PATH}"
echo "Content: ${CONTENT_DIR}"

if [ "${RUN_SEARCH_REPLACE}" = "1" ] && [ -n "${SOURCE_URL}" ]; then
    echo "URL migration: enabled (${SOURCE_URL} -> ${SITE_URL})"
else
    echo "URL migration: disabled (set RUN_SEARCH_REPLACE=1 and SOURCE_URL=...)"
fi
echo ""

# ============================================================================
# 0. PREFLIGHT
# ============================================================================

echo "--- [0/9] Running preflight checks ---"

command -v wp >/dev/null 2>&1 || die "wp command not found. Install WP-CLI first."
[ -d "${CONTENT_DIR}" ] || die "Content directory not found: ${CONTENT_DIR}"

if ! wpcli core is-installed >/dev/null 2>&1; then
    die "WordPress is not installed or wp-config.php is not configured at ${WP_PATH}."
fi

if wpcli user get "${ADMIN_USER}" --field=ID >/dev/null 2>&1; then
    ADMIN_USER_EXISTS=1
else
    ADMIN_USER_EXISTS=0
    warn "Admin user '${ADMIN_USER}' does not exist yet."
    warn "Create it later with: wp user create ${ADMIN_USER} ${ADMIN_EMAIL} --role=administrator"
fi

echo "  Preflight checks passed."

# ============================================================================
# 1. WORDPRESS SETTINGS
# ============================================================================

echo "--- [1/9] Configuring WordPress settings ---"

wpcli option update home "${SITE_URL}"
wpcli option update siteurl "${SITE_URL}"
wpcli option update blogname "${SITE_TITLE}"
wpcli option update blogdescription "${SITE_TAGLINE}"
wpcli option update timezone_string "${TIMEZONE}"
wpcli option update date_format "${DATE_FORMAT}"
wpcli option update time_format "${TIME_FORMAT}"
wpcli option update start_of_week 1
wpcli option update permalink_structure "/%postname%/"
wpcli option update default_comment_status "closed"
wpcli option update default_ping_status "closed"
wpcli option update blog_public 1
wpcli option update uploads_use_yearmonth_folders 1
wpcli rewrite flush --hard >/dev/null 2>&1 || true

echo "  WordPress settings configured."

if [ "${RUN_SEARCH_REPLACE}" = "1" ] && [ -n "${SOURCE_URL}" ] && [ "${SOURCE_URL}" != "${SITE_URL}" ]; then
    echo "  Running URL migration via wp search-replace..."
    wpcli search-replace "${SOURCE_URL}" "${SITE_URL}" \
        --all-tables \
        --precise \
        --recurse-objects \
        --skip-columns=guid \
        --report-changed-only
fi

# ============================================================================
# 2. PLUGIN INSTALLATION
# ============================================================================

echo "--- [2/9] Installing plugins ---"

PLUGINS=(
    "wordfence"
    "litespeed-cache"
    "bbpress"
    "members"
    "wp-mail-smtp"
    "updraftplus"
    "contact-form-7"
    "wp-security-audit-log"
    "wp-gdpr-compliance"
    "download-monitor"
    "redirection"
    "wordpress-seo"
)

for plugin in "${PLUGINS[@]}"; do
    if wpcli plugin is-installed "${plugin}" 2>/dev/null; then
        echo "  ${plugin}: already installed, updating..."
        wpcli plugin update "${plugin}" || true
    else
        echo "  ${plugin}: installing..."
        wpcli plugin install "${plugin}" --activate || warn "Failed to install ${plugin}"
    fi
done

echo "  Plugins installed."

# ============================================================================
# 3. THEME ACTIVATION
# ============================================================================

echo "--- [3/9] Activating Sinople theme ---"

if wpcli theme is-installed sinople 2>/dev/null; then
    wpcli theme activate sinople
    echo "  Sinople theme activated."
else
    warn "Sinople theme not found in wp-content/themes/"
    echo "  Upload it manually: scp -r wp-content/themes/sinople/ user@server:path/wp-content/themes/"
    echo "  Falling back to default theme."
fi

# ============================================================================
# 4. PAGE CREATION
# ============================================================================

echo "--- [4/9] Creating pages ---"

create_page() {
    local title="$1"
    local slug="$2"
    local content_file="$3"
    local status="${4:-publish}"
    local page_id=""
    local content="<p>Content coming soon.</p>"

    if [ -f "${content_file}" ]; then
        content=$(cat "${content_file}")
    else
        warn "Content source not found for ${title}: ${content_file}"
    fi

    page_id="$(first_id_for_slug page "${slug}")"
    if [ -n "${page_id}" ]; then
        wpcli post update "${page_id}" --post_title="${title}" --post_status="${status}" >/dev/null
        if [ "${UPDATE_EXISTING_CONTENT}" = "1" ] && [ -f "${content_file}" ]; then
            wpcli post update "${page_id}" --post_content="${content}" >/dev/null
            echo "  Updated: ${title} (/${slug}/) with content"
        else
            echo "  Exists: ${title} (/${slug}/)"
        fi
    else
        wpcli post create --post_type=page \
            --post_title="${title}" \
            --post_name="${slug}" \
            --post_status="${status}" \
            --post_content="${content}" >/dev/null
        echo "  Created: ${title} (/${slug}/)"
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

echo "--- [5/9] Setting static front page ---"

HOMEPAGE_ID="$(first_id_for_slug page home)"
NEWS_ID="$(first_id_for_slug page news)"

if [ -n "${HOMEPAGE_ID}" ]; then
    wpcli option update show_on_front "page"
    wpcli option update page_on_front "${HOMEPAGE_ID}"
    if [ -n "${NEWS_ID}" ]; then
        wpcli option update page_for_posts "${NEWS_ID}"
    fi
    echo "  Static front page set to Home, posts page set to News."
else
    warn "Homepage not found, skipping front page setup."
fi

# ============================================================================
# 6. NAVIGATION MENU
# ============================================================================

echo "--- [6/9] Creating navigation menu ---"

if ! wpcli menu list --fields=name --format=csv 2>/dev/null | tail -n +2 | tr -d '"' | grep -Fxq "Main Navigation"; then
    wpcli menu create "Main Navigation" >/dev/null
fi

MENU_ID="$(wpcli menu list --fields=term_id,name --format=csv 2>/dev/null \
    | awk -F',' '$2 ~ /Main Navigation/ {print $1; exit}' \
    | tr -d '"')"

if [ -n "${MENU_ID}" ]; then
    # Add pages to menu (order matters)
    for slug in home about-us news join contact members; do
        PAGE_ID="$(first_id_for_slug page "${slug}")"
        if [ -n "${PAGE_ID}" ]; then
            if page_in_menu "${MENU_ID}" "${PAGE_ID}"; then
                echo "  Menu has: ${slug}"
            else
                wpcli menu item add-post "${MENU_ID}" "${PAGE_ID}" >/dev/null 2>&1 || true
                echo "  Added to menu: ${slug}"
            fi
        fi
    done

    # Assign menu to primary theme location
    wpcli menu location assign "${MENU_ID}" primary 2>/dev/null || \
    wpcli menu location assign "${MENU_ID}" menu-1 2>/dev/null || \
        warn "Could not assign primary menu to theme location."

    echo "  Primary navigation menu configured."
else
    warn "Could not create navigation menu."
fi

# Footer menu
if ! wpcli menu list --fields=name --format=csv 2>/dev/null | tail -n +2 | tr -d '"' | grep -Fxq "Footer Menu"; then
    wpcli menu create "Footer Menu" >/dev/null
fi

FOOTER_MENU_ID="$(wpcli menu list --fields=term_id,name --format=csv 2>/dev/null \
    | awk -F',' '$2 ~ /Footer Menu/ {print $1; exit}' \
    | tr -d '"')"

if [ -n "${FOOTER_MENU_ID}" ]; then
    for slug in about-us contact join; do
        PAGE_ID="$(first_id_for_slug page "${slug}")"
        if [ -n "${PAGE_ID}" ]; then
            if page_in_menu "${FOOTER_MENU_ID}" "${PAGE_ID}"; then
                echo "  Footer menu has: ${slug}"
            else
                wpcli menu item add-post "${FOOTER_MENU_ID}" "${PAGE_ID}" >/dev/null 2>&1 || true
                echo "  Added to footer menu: ${slug}"
            fi
        fi
    done
    wpcli menu location assign "${FOOTER_MENU_ID}" footer 2>/dev/null || true
    echo "  Footer menu configured."
fi

# Social links menu
if ! wpcli menu list --fields=name --format=csv 2>/dev/null | tail -n +2 | tr -d '"' | grep -Fxq "Social Links"; then
    wpcli menu create "Social Links" >/dev/null
fi

SOCIAL_MENU_ID="$(wpcli menu list --fields=term_id,name --format=csv 2>/dev/null \
    | awk -F',' '$2 ~ /Social Links/ {print $1; exit}' \
    | tr -d '"')"

if [ -n "${SOCIAL_MENU_ID}" ]; then
    wpcli menu location assign "${SOCIAL_MENU_ID}" social 2>/dev/null || true
    echo "  Social links menu configured (add links via Appearance > Menus)."
fi

# ============================================================================
# 7. BBPRESS FORUMS
# ============================================================================

echo "--- [7/9] Creating bbPress forums ---"

if wpcli plugin is-active bbpress 2>/dev/null; then
    create_forum() {
        local title="$1"
        local slug="$2"
        local desc="$3"
        local forum_id=""

        forum_id="$(first_id_for_slug forum "${slug}")"
        if [ -z "${forum_id}" ]; then
            wpcli post create --post_type=forum \
                --post_title="${title}" \
                --post_name="${slug}" \
                --post_status="publish" \
                --post_content="${desc}" >/dev/null
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
# 8. MEMBERS ROLE
# ============================================================================

echo "--- [8/9] Configuring Members role ---"

if wpcli plugin is-active members 2>/dev/null; then
    if [ "$(wpcli eval 'echo get_role("nuj_member") ? "1" : "0";')" = "1" ]; then
        echo "  Role exists: nuj_member"
    else
        wpcli eval 'add_role("nuj_member", "NUJ Member", array("read" => true));'
        echo "  Created role: nuj_member"
    fi
else
    echo "  SKIP: Members plugin not active."
fi

# ============================================================================
# 9. SECURITY HARDENING
# ============================================================================

echo "--- [9/9] Security hardening ---"

# Disable pingbacks
wpcli option update default_pingback_flag 0

# Disable user registration (admin manually promotes members)
wpcli option update users_can_register 0

# Remove default "Hello World" post and "Sample Page"
DEFAULT_POST="$(first_id_for_slug post hello-world)"
if [ -n "${DEFAULT_POST}" ]; then
    wpcli post delete "${DEFAULT_POST}" --force >/dev/null
    echo "  Deleted default 'Hello World' post."
fi

SAMPLE_PAGE="$(first_id_for_slug page sample-page)"
if [ -n "${SAMPLE_PAGE}" ]; then
    wpcli post delete "${SAMPLE_PAGE}" --force >/dev/null
    echo "  Deleted default 'Sample Page'."
fi

# Remove default plugins
for unwanted in akismet hello; do
    if wpcli plugin is-installed "${unwanted}" 2>/dev/null; then
        wpcli plugin deactivate "${unwanted}" 2>/dev/null || true
        wpcli plugin delete "${unwanted}" 2>/dev/null || true
        echo "  Removed unwanted plugin: ${unwanted}"
    fi
done

# ============================================================================
# 10. SEED POSTS
# ============================================================================

echo "--- [10/11] Creating seed posts ---"

create_post() {
    local title="$1"
    local slug="$2"
    local content_file="$3"
    local category="$4"
    local post_id=""
    local content="<p>Content coming soon.</p>"

    if [ -f "${content_file}" ]; then
        content=$(cat "${content_file}")
    fi

    post_id="$(first_id_for_slug post "${slug}")"
    if [ -n "${post_id}" ]; then
        echo "  Post exists: ${title}"
    else
        wpcli post create --post_type=post \
            --post_title="${title}" \
            --post_name="${slug}" \
            --post_status="publish" \
            --post_content="${content}" \
            --post_category="${category}" >/dev/null 2>&1 || true
        echo "  Created post: ${title}"
    fi
}

# Create categories first
for cat_name in "Branch News" "Campaigns" "Events" "Press Freedom" "Freelance" "Industry"; do
    if ! wpcli term list category --field=name --format=csv 2>/dev/null | grep -Fxq "${cat_name}"; then
        wpcli term create category "${cat_name}" >/dev/null 2>&1 || true
        echo "  Created category: ${cat_name}"
    fi
done

# Create seed posts from content/posts/
if [ -d "${CONTENT_DIR}/posts" ]; then
    for post_file in "${CONTENT_DIR}/posts"/*.html; do
        [ -f "${post_file}" ] || continue
        BASENAME="$(basename "${post_file}" .html)"
        TITLE="$(echo "${BASENAME}" | sed 's/-/ /g' | sed 's/\b\(.\)/\u\1/g')"
        create_post "${TITLE}" "${BASENAME}" "${post_file}" "1"
    done
fi

echo "  Seed posts created."

# ============================================================================
# 11. WIDGET SETUP
# ============================================================================

echo "--- [11/11] Configuring widgets ---"

# Sidebar widgets: Search, Recent Posts, Categories, Archives
wpcli widget add search sidebar-1 --title="Search" 2>/dev/null || true
wpcli widget add recent-posts sidebar-1 --title="Latest Posts" --number=5 2>/dev/null || true
wpcli widget add categories sidebar-1 --title="Categories" --count=1 2>/dev/null || true
wpcli widget add archives sidebar-1 --title="Archives" --count=1 2>/dev/null || true

# Footer widgets
wpcli widget add text footer-1 --title="About NUJ LCB" --text="The National Union of Journalists London Central Branch represents journalists working in central London." 2>/dev/null || true
wpcli widget add recent-posts footer-2 --title="Recent News" --number=3 2>/dev/null || true
wpcli widget add categories footer-3 --title="Categories" 2>/dev/null || true
wpcli widget add text footer-4 --title="Contact" --text="Email: contact@nuj-lcb.org.uk" 2>/dev/null || true

echo "  Widgets configured."

echo ""
echo "=== Deployment Complete ==="
echo ""
echo "Next manual steps:"
if [ "${ADMIN_USER_EXISTS}" = "1" ]; then
    echo "  1. Rotate admin password: wp user update ${ADMIN_USER} --user_pass=<STRONG_PASSWORD>"
else
    echo "  1. Create admin user: wp user create ${ADMIN_USER} ${ADMIN_EMAIL} --role=administrator"
    echo "     Then set password: wp user update ${ADMIN_USER} --user_pass=<STRONG_PASSWORD>"
fi
echo "  2. Configure Wordfence: Enable WAF, 2FA, login lockout"
echo "  3. Configure LiteSpeed Cache: Enable cache, browser cache, image optimization"
echo "  4. Configure WP Mail SMTP: Settings > WP Mail SMTP"
echo "  5. Configure UpdraftPlus: Encrypted daily backups to remote storage"
echo "  6. Test contact form: Create form in Contact Form 7, add to Contact page"
echo "  7. Confirm Members restrictions for /members/ and forum access"
echo "  8. Review all pages and update placeholder content"
echo "  9. Run accessibility audit: Lighthouse + aXe"
echo " 10. Verify security headers: securityheaders.com"
