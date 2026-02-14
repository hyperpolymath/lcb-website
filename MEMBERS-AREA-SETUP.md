# Members Area Setup Guide

**WordPress Configuration for NUJ LCB Member-Only Content**

## Overview

This guide sets up a secure members area with:
- Member-only content sections
- Private member directory
- Restricted resources/documents
- Integration with Zulip forum
- Privacy-first design

## Required Plugins

### 1. Install Essential Plugins

```bash
# Via WP-CLI (faster) or through WordPress admin
wp plugin install members --activate
wp plugin install download-monitor --activate
wp plugin install user-meta-manager --activate
wp plugin install simple-membership --activate
```

**Or via WordPress Admin:**
1. Plugins → Add New
2. Search for each plugin
3. Install & Activate

### Plugin Purposes

| Plugin | Purpose | Free/Paid |
|--------|---------|-----------|
| **Members** | Role/capability management | Free |
| **Download Monitor** | Secure file downloads (PDFs) | Free |
| **Simple Membership** | Member registration/login | Free |
| **User Meta Manager** | Custom profile fields | Free |

## Step-by-Step Setup

### Step 1: Create Member Role

```php
// Add to functions.php or use Members plugin UI

add_action('init', 'create_nuj_member_role');

function create_nuj_member_role() {
  add_role(
    'nuj_member',
    'NUJ Member',
    array(
      'read' => true,
      'read_private_pages' => true,
      'read_private_posts' => true,
      'level_0' => true,
    )
  );
}
```

**Via Members Plugin:**
1. Users → Roles → Add New
2. Role Name: `NUJ Member`
3. Capabilities:
   - ✅ Read
   - ✅ Read Private Pages
   - ✅ Read Private Posts
   - ❌ Edit Posts
   - ❌ Publish Posts

### Step 2: Create Members Area Page

```
Pages → Add New
Title: "Members Area"
URL: /members
```

**Page Content:**
```
[Paste content from content/pages/members-area.md]
```

**Visibility Settings:**
- In page sidebar: Private (visible only to logged-in members)
- Or use Members plugin: "Restrict to: NUJ Member"

### Step 3: Set Up Member Directory

#### Option A: Manual Directory (Simple)

Create page: "Member Directory"

```html
<!-- Member Directory Page -->
<div class="member-directory">
  <p><strong>Privacy Notice:</strong> Visible to members only. Contact info requires opt-in.</p>

  <!-- Search form -->
  <form class="directory-search">
    <input type="text" placeholder="Search members...">
    <button>Search</button>
  </form>

  <!-- Member cards (populated dynamically or manually) -->
  <div class="member-grid">
    [member-card id="1"]
    [member-card id="2"]
  </div>
</div>
```

#### Option B: Automated Directory (Plugin)

**Install**: BuddyPress or Ultimate Member

```bash
wp plugin install buddypress --activate
```

**Configure BuddyPress:**
1. Settings → BuddyPress
2. Enable: Member Profiles, Activity, Groups
3. Settings → Privacy: Members-only site
4. Appearance: Customize member profile fields

**Privacy Controls:**
1. BuddyPress → Settings → Privacy
2. ✅ Require login to view member profiles
3. ✅ Allow members to hide their profiles
4. ✅ Default profile visibility: Members Only

### Step 4: Secure File Downloads

**Download Monitor Setup:**

1. Downloads → Settings
2. Security:
   - ✅ Require login to download
   - ✅ Block hotlinking
   - ✅ Use unique download URLs
   - ✅ Log all downloads

2. Create protected directory:
```bash
mkdir -p /var/www/html/wp-content/uploads/members-only
chmod 750 /var/www/html/wp-content/uploads/members-only
```

3. Add .htaccess to members-only folder:
```apache
# /wp-content/uploads/members-only/.htaccess
Order Deny,Allow
Deny from all
```

4. Upload files:
- Downloads → Add New
- Upload PDF
- Category: "Member Resources"
- Access: "NUJ Member" role only

### Step 5: Member Registration Form

**Simple Membership Plugin:**

1. Simple Membership → Settings
2. General:
   - Membership Level: "NUJ Member" (£0 - verified offline)
   - Registration: Admin approval required
3. Email Settings:
   - Welcome email template
   - Admin notification on new registration

**Registration Form Shortcode:**
```
[swpm_registration_form]
```

**Add to page:** /join-members-area

**Form Fields:**
- First Name
- Last Name
- Email
- Membership Number (required for verification)
- Workplace (optional)
- Professional Specialism (optional)

### Step 6: Privacy Settings

#### Custom Profile Fields

**Add to functions.php:**

```php
// Add custom profile fields
add_action('show_user_profile', 'nuj_extra_user_profile_fields');
add_action('edit_user_profile', 'nuj_extra_user_profile_fields');

function nuj_extra_user_profile_fields($user) { ?>
  <h3>Privacy Settings</h3>
  <table class="form-table">
    <tr>
      <th><label for="show_full_name">Show full name in directory</label></th>
      <td>
        <input type="checkbox" name="show_full_name" id="show_full_name"
               value="1" <?php checked(get_user_meta($user->ID, 'show_full_name', true), '1'); ?>>
        <p class="description">Default: First name + last initial</p>
      </td>
    </tr>
    <tr>
      <th><label for="show_email">Show email address</label></th>
      <td>
        <input type="checkbox" name="show_email" id="show_email"
               value="1" <?php checked(get_user_meta($user->ID, 'show_email', true), '1'); ?>>
        <p class="description">Allow other members to see your email</p>
      </td>
    </tr>
    <tr>
      <th><label for="show_workplace">Show workplace details</label></th>
      <td>
        <input type="checkbox" name="show_workplace" id="show_workplace"
               value="1" <?php checked(get_user_meta($user->ID, 'show_workplace', true), '1'); ?>>
        <p class="description">Company name visible in directory</p>
      </td>
    </tr>
    <tr>
      <th><label for="allow_directory">Appear in member directory</label></th>
      <td>
        <input type="checkbox" name="allow_directory" id="allow_directory"
               value="1" <?php checked(get_user_meta($user->ID, 'allow_directory', true), '1'); ?>>
        <p class="description">Uncheck to hide your profile completely</p>
      </td>
    </tr>
  </table>
<?php }

// Save custom fields
add_action('personal_options_update', 'nuj_save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'nuj_save_extra_user_profile_fields');

function nuj_save_extra_user_profile_fields($user_id) {
  if (!current_user_can('edit_user', $user_id)) { return false; }

  update_user_meta($user_id, 'show_full_name', $_POST['show_full_name'] ?? '0');
  update_user_meta($user_id, 'show_email', $_POST['show_email'] ?? '0');
  update_user_meta($user_id, 'show_workplace', $_POST['show_workplace'] ?? '0');
  update_user_meta($user_id, 'allow_directory', $_POST['allow_directory'] ?? '0');
}
```

### Step 7: Menu & Navigation

**Create Members Menu:**

1. Appearance → Menus → Create New Menu
2. Name: "Members Area Menu"
3. Add pages:
   - Members Home
   - Member Directory
   - Meeting Minutes
   - Resources
   - Forum (external link)
   - Video Meetings (external link)

4. Menu Settings:
   - Display location: Primary Menu (for logged-in users)

**Conditional Menu (logged in/out):**

```php
// Add to functions.php
// Show different menus based on login status

add_filter('wp_nav_menu_args', 'nuj_menu_by_role');

function nuj_menu_by_role($args) {
  if (is_user_logged_in()) {
    $args['menu'] = 'Members Area Menu';
  } else {
    $args['menu'] = 'Public Menu';
  }
  return $args;
}
```

### Step 8: Login/Logout Pages

**Create Login Page:**

1. Pages → Add New: "Member Login"
2. Add shortcode: `[swpm_login_form]`
3. Customize appearance

**Login Widget in Sidebar:**

```php
// Add to sidebar
if (!is_user_logged_in()) {
  echo do_shortcode('[swpm_login_form]');
} else {
  echo '<p>Welcome, ' . wp_get_current_user()->display_name . '</p>';
  echo '<a href="/members">Members Area</a> | ';
  echo '<a href="' . wp_logout_url(home_url()) . '">Logout</a>';
}
```

### Step 9: Restrict Content

**Page-Level Restriction:**

Use Members plugin content protection:

```
<!-- In page editor -->
[members_access role="nuj_member"]
This content is for members only.
[/members_access]

[members_no_access role="nuj_member"]
<p>This content requires membership. <a href="/join">Join now</a></p>
[/members_no_access]
```

**Post-Level Restriction:**

In post editor sidebar:
- Members → Content Permissions
- Select: "NUJ Member" only

### Step 10: Integration with Forum/Jitsi

**External Links in Members Area:**

```html
<div class="external-platforms">
  <h2>Member Platforms</h2>

  <div class="platform-card">
    <h3>💬 Discussion Forum</h3>
    <p>Join conversations with fellow members on our Zulip forum.</p>
    <a href="https://forum.nuj-lcb.org.uk" class="button" target="_blank">
      Go to Forum →
    </a>
  </div>

  <div class="platform-card">
    <h3>📹 Video Meetings</h3>
    <p>Join branch meetings via our Jitsi platform.</p>
    <a href="https://convene.nuj-lcb.org.uk" class="button" target="_blank">
      Join Meeting →
    </a>
  </div>
</div>
```

**Single Sign-On (Optional):**

For advanced setup, use:
- **miniOrange SSO** plugin for WordPress ↔ Zulip SSO
- Requires: SAML 2.0 configuration on both sides
- Complexity: High (defer to Phase 2)

## Security Configuration

### Member Data Protection

```php
// wp-config.php

// Prevent user enumeration
if (!is_admin()) {
  // Redirect /?author=1 attempts
  add_action('template_redirect', 'nuj_prevent_user_enumeration');
}

function nuj_prevent_user_enumeration() {
  if (is_author()) {
    wp_redirect(home_url(), 301);
    exit;
  }
}
```

### GDPR Compliance

**Install**: WP GDPR Compliance plugin

```bash
wp plugin install wp-gdpr-compliance --activate
```

**Configure:**
1. GDPR → Settings
2. ✅ Enable consent checkboxes
3. ✅ Data access requests
4. ✅ Data deletion requests
5. ✅ Cookie consent bar

**Privacy Policy Page:**
- Create page: "Privacy Policy"
- Settings → Privacy: Select page
- Auto-generate GDPR text

### Activity Logging

**Install**: WP Activity Log

```bash
wp plugin install wp-security-audit-log --activate
```

**Monitor:**
- Member logins/logouts
- Profile changes
- File downloads
- Admin actions

## Testing Checklist

### Before Launch

- [ ] Create test member account
- [ ] Verify can't access without login
- [ ] Test file downloads (PDFs)
- [ ] Check privacy settings work
- [ ] Test registration form
- [ ] Verify admin approval process
- [ ] Test password reset
- [ ] Check mobile responsiveness
- [ ] Test forum/Jitsi links
- [ ] Verify GDPR compliance
- [ ] Test activity logging

### User Testing

- [ ] Invite 3-5 members to test
- [ ] Collect feedback on usability
- [ ] Fix confusing elements
- [ ] Verify privacy controls understood
- [ ] Test on different devices/browsers

## Member Onboarding

### Welcome Email Template

```
Subject: Welcome to NUJ LCB Members Area!

Dear [First Name],

Welcome to the NUJ London Central Branch members area! Your account has been approved.

**Your Login Details:**
- Website: https://nuj-lcb.org.uk/member-login
- Username: [username]
- Password: [You set this during registration]

**What's Available:**
✅ Member directory
✅ Meeting minutes archive
✅ Legal & advice resources
✅ Training materials
✅ Discussion forum (forum.nuj-lcb.org.uk)
✅ Video meetings (convene.nuj-lcb.org.uk)

**Set Your Privacy:**
Visit https://nuj-lcb.org.uk/wp-admin/profile.php to control what other members can see.

**Need Help?**
Email: membership@nuj-lcb.org.uk

In solidarity,
NUJ London Central Branch
```

### First-Time User Guide

Create page: "Members Area Guide"

```markdown
# Getting Started with Members Area

## Your First Steps

1. **Update Your Profile**
   - Go to: Profile → Edit
   - Add your workplace, specialism
   - Set privacy preferences

2. **Explore Resources**
   - Download rate cards, contract templates
   - Access legal guides
   - Browse training materials

3. **Join the Forum**
   - Visit: forum.nuj-lcb.org.uk
   - Use same email as website
   - Introduce yourself in #general

4. **Attend a Meeting**
   - Check calendar for next meeting
   - Join via: convene.nuj-lcb.org.uk
   - No software needed!

## Privacy Tips

- Default: Minimal visibility
- You control what others see
- Email/phone hidden by default
- Update anytime in your profile
```

## Maintenance

### Weekly Tasks

- Review new member registrations
- Approve/deny pending members
- Check for spam registrations
- Update meeting minutes

### Monthly Tasks

- Review member activity logs
- Update resources (new guides/templates)
- Check for plugin updates
- Verify backup working
- Review privacy settings compliance

### Quarterly Tasks

- Member survey (satisfaction)
- Security audit
- Remove inactive members (3+ months no login)
- Review access logs for anomalies

## Troubleshooting

### Member Can't Log In

1. Check: Account activated?
2. Check: Email verified?
3. Check: Role assigned? (should be "NUJ Member")
4. Test: Password reset link working?
5. Check: IP not blocked by security plugin

### Can't Download Files

1. Check: User has "NUJ Member" role
2. Check: Download Monitor permissions
3. Check: .htaccess not blocking
4. Check: File actually exists

### Directory Not Showing Members

1. Check: Privacy setting "allow_directory" = 1
2. Check: Role = "nuj_member"
3. Check: User meta saved correctly
4. Check: BuddyPress activated

## Next Steps

1. ✅ Set up member roles and capabilities
2. ✅ Create members area pages
3. ✅ Configure access restrictions
4. ✅ Set up file downloads
5. ✅ Test registration workflow
6. ⏭️ **Next**: Implement security (Wordfence, 2FA, backups)

See: `SECURITY-IMPLEMENTATION.md` for security setup.
