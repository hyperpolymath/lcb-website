<!-- SPDX-License-Identifier: PMPL-1.0-or-later -->
<!-- SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <jonathan.jewell@open.ac.uk> -->
# Sinople Theme — Required Fixes Before Launch

**Status:** COMPLETE (2026-02-17) — required fixes have been applied in `wp-content/themes/sinople/`.

Note: A read-only backup directory (`wp-content/themes_ro/`) may exist locally from recovery work. It is not part of the tracked theme path.

## 1. style.css — Version Corrections (DONE)

**File:** `wp-content/themes/sinople/style.css`

Change:
```
Requires PHP: 7.4
```
To:
```
Requires PHP: 8.1
```

Change:
```
Tested up to: 6.4
```
To:
```
Tested up to: 6.9
```

## 2. Missing: editor-style.css (DONE)

**File:** `wp-content/themes/sinople/assets/css/editor-style.css`

Referenced on line 84 of `functions.php` but does not exist. Create with WCAG AAA compliant block editor styles:

```css
/* SPDX-License-Identifier: GPL-2.0-or-later */
/* Editor styles for Sinople theme — WCAG AAA compliant */

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    font-size: 18px;
    line-height: 1.8;
    color: #1a1a1a;
    max-width: 720px;
    margin: 0 auto;
    padding: 1rem;
}

/* WCAG AAA: 7:1 contrast ratio minimum */
h1, h2, h3, h4, h5, h6 { color: #0d0d0d; font-weight: 700; line-height: 1.3; }
h1 { font-size: 2.4em; margin: 1em 0 0.5em; }
h2 { font-size: 1.8em; margin: 0.8em 0 0.4em; }
h3 { font-size: 1.4em; margin: 0.6em 0 0.3em; }
h4 { font-size: 1.2em; margin: 0.5em 0 0.25em; }

a { color: #004080; text-decoration: underline; }
a:hover, a:focus { color: #002040; outline: 3px solid #004080; outline-offset: 2px; }

p { margin: 0 0 1.5em; }

blockquote {
    border-left: 4px solid #004080;
    margin: 1.5em 0;
    padding: 0.5em 1.5em;
    background: #f5f5f5;
    color: #1a1a1a;
}

code, pre {
    font-family: "JetBrains Mono", "Fira Code", "Cascadia Code", monospace;
    background: #f0f0f0;
    color: #1a1a1a;
}

pre { padding: 1em; overflow-x: auto; border: 1px solid #ccc; border-radius: 4px; }
code { padding: 0.15em 0.3em; border-radius: 3px; }

img { max-width: 100%; height: auto; }

table { border-collapse: collapse; width: 100%; margin: 1.5em 0; }
th, td { border: 1px solid #666; padding: 0.75em; text-align: left; }
th { background: #e8e8e8; font-weight: 700; }

/* Focus indicators — WCAG AAA */
*:focus { outline: 3px solid #004080; outline-offset: 2px; }

/* Lists */
ul, ol { margin: 0 0 1.5em; padding-left: 2em; }
li { margin-bottom: 0.5em; }

/* WordPress block editor specifics */
.wp-block { max-width: 720px; }
.wp-block[data-align="wide"] { max-width: 1080px; }
.wp-block[data-align="full"] { max-width: none; }
```

## 3. Missing: semantic_processor.js (DONE)

**File:** `wp-content/themes/sinople/assets/wasm/semantic_processor.js`

Referenced on line 433 of `functions.php` but does not exist. The directory `assets/wasm/` also does not exist. Create both:

```bash
mkdir -p wp-content/themes/sinople/assets/wasm/
```

```javascript
// SPDX-License-Identifier: GPL-2.0-or-later
// Semantic processor WASM initialization stub for Sinople theme
// This file will be replaced with the real WASM module when proven/idris2-zig-php
// bindings are available. For now it provides a no-op interface.

(function() {
    'use strict';

    var SemanticProcessor = {
        ready: false,
        version: '0.1.0-stub',

        init: function() {
            if (typeof console !== 'undefined' && console.log) {
                console.log('[SemanticProcessor] Stub loaded (WASM module not yet available)');
            }
            this.ready = true;
            return Promise.resolve(this);
        },

        processNode: function(node) {
            // No-op: real implementation will add ARIA attributes
            // based on semantic analysis from Idris2 ABI proofs
            return node;
        },

        validateAccessibility: function(element) {
            // No-op: real implementation will check WCAG AAA compliance
            // using formally verified rules from proven library
            return { valid: true, warnings: [], errors: [] };
        }
    };

    // Export for WordPress enqueue
    if (typeof window !== 'undefined') {
        window.SemanticProcessor = SemanticProcessor;
    }
})();
```

## 4. accessibility.php — Stub Needs Implementation (DONE)

**File:** `wp-content/themes/sinople/inc/accessibility.php`

Currently 18 lines with no real WCAG utilities. Before launch this needs:
- Skip-to-content link injection
- ARIA landmark attributes
- Focus management for menus
- Screen reader text helpers
- High contrast mode toggle

**Priority:** Medium — the CSS and theme structure provide baseline accessibility, but the PHP helpers would improve it significantly.

## 5. composer.json — PHP Version Constraint (VERIFIED)

**File:** `wp-content/themes/sinople/composer.json`

Verify `"php": ">=8.1"` in require section. The theme uses php-aegis which requires 8.1+.

## 6. Activation Fatal on Verpex — `delete_plugins()` Flow (DONE)

**File:** `wp-content/themes/sinople/functions.php`

Observed on Verpex during `after_switch_theme`:
- Fatal: `Call to undefined function request_filesystem_credentials()`
- Trigger path: `sinople_on_theme_activation()` -> `delete_plugins()` in non-admin/runtime context

Applied fix:
- Keep deactivation of default plugins
- Remove plugin deletion during theme activation
- Reason: shared hosting and non-admin contexts may not have filesystem credential helpers loaded

Result:
- Sinople activates without 500 on Verpex
- Homepage and `wp-login.php` stay reachable under Cloudflare strict TLS

---

**After fixing, commit with:**
```bash
git add wp-content/themes/sinople/
git commit -m "fix: update Sinople theme versions and add missing files

- Update PHP requirement to 8.1 (was 7.4)
- Update 'Tested up to' WordPress version to 6.9 (was 6.4)
- Add editor-style.css (WCAG AAA block editor styles)
- Add semantic_processor.js (WASM stub for future proven integration)

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>"
```
