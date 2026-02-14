<?php
// SPDX-License-Identifier: PMPL-1.0-or-later
// wp-config-security.php — Security constants for wp-config.php
// Copyright (c) 2026 Jonathan D.A. Jewell (hyperpolymath) <jonathan.jewell@open.ac.uk>
//
// Add these constants to wp-config.php BEFORE the line:
//   /* That's all, stop editing! Happy publishing. */
//
// These follow the hardening guidance in SECURITY-IMPLEMENTATION.md

// === File Security ===
define('DISALLOW_FILE_EDIT', true);       // Prevent theme/plugin editor in admin
define('DISALLOW_FILE_MODS', false);      // Allow plugin installs via admin (set true to lock down)

// === SSL/TLS ===
define('FORCE_SSL_ADMIN', true);          // Force HTTPS for wp-admin
define('FORCE_SSL_LOGIN', true);          // Force HTTPS for wp-login.php

// === Updates ===
define('WP_AUTO_UPDATE_CORE', 'minor');   // Auto-update minor versions only (security patches)

// === Content Management ===
define('WP_POST_REVISIONS', 10);          // Limit post revisions (saves DB space)
define('EMPTY_TRASH_DAYS', 14);           // Auto-delete trash after 14 days

// === Cron ===
define('WP_CRON_LOCK_TIMEOUT', 120);      // Prevent concurrent cron runs

// === Database ===
// Use non-default table prefix for security
// $table_prefix = 'nujlcb_';             // Set in main wp-config.php

// === Debugging (DISABLE in production) ===
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);

// === Performance ===
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');    // Admin area memory limit

// === Cloudflare Compatibility ===
// Detect HTTPS behind Cloudflare proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
