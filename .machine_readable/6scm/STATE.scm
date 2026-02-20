;; SPDX-License-Identifier: PMPL-1.0-or-later
;; STATE.scm - Project state for lcb-website
;; Media-Type: application/vnd.state+scm

(state
  (metadata
    (version "0.3.0")
    (schema-version "1.0")
    (created "2026-01-19")
    (updated "2026-02-20")
    (project "lcb-website")
    (repo "github.com/hyperpolymath/lcb-website"))

  (project-context
    (name "lcb-website")
    (tagline "NUJ London Central Branch website — hardened WordPress on Verpex with stapeln container path")
    (tech-stack ("WordPress 6.9" "PHP 8.4" "Sinople theme" "php-aegis" "LiteSpeed Cache"
                 "Cerro Torre" "Svalinn" "Vörðr" "consent-aware HTTP" "Chainguard wolfi-base")))

  (current-position
    (phase "phase-3-content-and-launch")
    (overall-completion 95)
    (components ("container baseline" "manifest" "consent docs" "automation" "ci-cd"
                 "docker-compose" "contractiles" "templates" "robots-txt" "selur-compose"
                 "well-known" "security-docs" "content" "bot-directives" "topology"
                 "cloudflare-dns-cutover" "wordpress-live-backend"
                 "sinople-v2-theme" "seed-posts" "featured-images" "block-patterns"
                 "minified-assets" "deploy-script"))
    (working-features
      ("infra/wordpress.ctp with Chainguard wolfi-base + Dilithium5 signature spec"
       "Containerfile multi-stage build (wolfi-base + PHP 8.4 + LiteSpeed)"
       "selur-compose.yml stapeln orchestration (svalinn + vordr + redis + mariadb)"
       "contractiles framework (must/trust/dust/lust/k9)"
       "templates (wp-config-security, htaccess-well-known, htaccess-security)"
       "robots.txt blocks AI training bots"
       ".well-known/aibdp.json consent policy"
       ".bot_directives for gitbot-fleet (8 bots)"
       "17 GitHub workflows including hypatia-scan"
       "TOPOLOGY.md architecture map + completion dashboard"
       "justfile automation (7080 lines)"
       "docker-compose.yml tested locally"
       "All content pages written in markdown"
       "Sinople v2.0.0 theme: Newspaperup-style news-magazine layout"
       "  20 PHP templates, 9 includes, 18 CSS, 5 JS files"
       "  NUJ green colour scheme (#006747) with dark mode"
       "  CSS Grid layout (no Bootstrap), self-hosted fonts/icons/Swiper"
       "  WCAG AAA: 9/10 PASS + custom searchform.php"
       "  IndieWeb Level 4: webmention + micropub + IndieAuth"
       "  PhpAegis security (CSP, HSTS, Permissions-Policy)"
       "  Block patterns: event, campaign, call-to-action, member notice"
       "  Minified production CSS/JS bundles"
       "8 seed posts with featured images (1200x800 JPG)"
       "wp-deploy.sh: menus, widgets, posts, featured image import"
       "Full security documentation suite"
       "Cloudflare DNS cutover complete (A apex -> 65.181.113.13, proxied CNAME www -> apex)"
       "WordPress 6.9.1 backend installed on Verpex and reachable at /wp-login.php"
       "Cloudflare strict SSL restored successfully after AutoSSL cert validation"
       "Sinople activation flow patched for Verpex compatibility (avoid delete_plugins fatal path)")))

  (route-to-mvp
    (milestones
      (("verpex-deployment" . "Deploy WordPress 6.9 on Verpex cPanel with Sinople theme")
       ("cloudflare-dns" . "Configure DNS, SSL, WAF, bot fight mode")
       ("content-entry" . "Create all WordPress pages from content/ markdown files")
       ("security-hardening" . "Wordfence, 2FA, security headers, encrypted backups")
       ("members-forum" . "bbPress forum + Members plugin + nuj_member role")
       ("stapeln-path" . "Container build via cerro-torre sign + selur-compose deploy"))))

  (blockers-and-issues
    (critical ())
    (high ("Plugin baseline not yet installed (Wordfence, LiteSpeed Cache, bbPress, Members, SMTP, backups)"
           "Content pages/policies still need import or authoring in WordPress"))
    (medium ("security.txt well-known hash in wordpress.ctp needs recalculation after expiry update"
             "Container path blocked: cerro-torre ct binary needs Alire index fix"
             "Dark mode inline script relies on unsafe-inline CSP — consider nonce-based approach"))
    (low ("svalinn-compose.yml kept for reference — can delete after selur-compose.yml verified"
          "Initial admin credentials were generated during automated install and must be rotated immediately")))

  (critical-next-actions
    (immediate ("Run wp-deploy.sh against Verpex to deploy menus, widgets, posts, and images"
                "Rotate WordPress admin password and verify administrator login flow"
                "Install and configure plugin baseline (Wordfence, LiteSpeed Cache, bbPress, Members, SMTP, backups)"))
    (this-week ("Import/create WordPress pages from content/ markdown"
                "Security hardening (2FA, headers, encrypted backups)"
                "Run live accessibility audit (axe-core/pa11y) against running instance"))
    (this-month ("Configure LiteSpeed Cache (TTLs, Redis, WebP)"
                 "Set up members area with nuj_member role"
                 "Set up bbPress forum (4 forums)"
                 "Configure WP Mail SMTP"
                 "Launch and verify (securityheaders.com A+, ssllabs.com A+)")))

  (session-history
    ((date "2026-02-20")
     (accomplishments
       ("Rebuilt Sinople theme v2.0.0 as Newspaperup-style news magazine (11 phases)"
        "New layout: topbar, header, featured carousel, card grid, sidebar, missed posts, footer"
        "NUJ green colour scheme (#006747) with full dark mode (localStorage + OS preference)"
        "CSS design token system (variables.css) with data-scheme/data-theme attributes"
        "Self-hosted vendor assets: Lexend Deca + Outfit fonts, Font Awesome 6 subset, Swiper 11"
        "CSS Grid layout (no Bootstrap) with responsive breakpoints at 1200/992/768/575px"
        "WCAG AAA: 9/10 PASS, added custom searchform.php with explicit labels"
        "IndieWeb Level 4 retained: webmention + micropub + IndieAuth endpoints"
        "PhpAegis security: deduplicated CSP headers (security.php now no-op for headers)"
        "Created 8 seed posts with featured images (1200x800 JPG, NUJ green/red/navy gradients)"
        "Updated wp-deploy.sh: featured image import, footer/social menus, widget setup"
        "Generated new 1200x900 theme screenshot showing news-magazine layout"
        "Minified CSS/JS production bundles"
        "Registered 4 Gutenberg block patterns (event, campaign, CTA, member notice)"
        "Updated TOPOLOGY.md, 0-AI-MANIFEST.a2ml, STATE.scm for post-refactor state"))
     (next-session "Deploy to Verpex via wp-deploy.sh, install plugin baseline, launch hardening"))
    ((date "2026-02-17")
     (accomplishments
       ("Configured Cloudflare zone + DNS cutover for nuj-lcb.org.uk"
        "Set apex A to 65.181.113.13 and proxied www CNAME to apex"
        "Enabled Cloudflare HTTPS hardening settings (Always Use HTTPS, automatic rewrites, min TLS 1.2, HTTP/3, Brotli)"
        "Verified external reachability and diagnosed Cloudflare 526 root cause as origin TLS mismatch"
        "Set temporary Cloudflare SSL mode to Full to unblock WordPress deployment path"
        "Provisioned cPanel API deployment identity and automated Verpex setup"
        "Created dedicated WordPress database/user (nujprcor_lcbwp26 / nujprcor_lcbu26)"
        "Deployed WordPress 6.9.1 core, generated wp-config.php, and completed installer flow"
        "Published .well-known policy files and robots.txt to live docroot"
        "Patched Sinople activation logic to avoid shared-host filesystem credential fatal"
        "Re-deployed Sinople patch and verified homepage + wp-login with strict TLS"
        "Restored Cloudflare SSL mode to strict successfully"
        "Updated deployment docs and topology/status dashboards to reflect live Cloudflare state and remaining WordPress work"))
     (next-session "Install plugin baseline, import content pages, configure SMTP/backups, and complete launch hardening"))
    ((date "2026-02-14")
     (accomplishments
       ("Created contractiles framework (must/trust/dust/lust/k9) customised for lcb-website"
        "Rewrote Containerfile: multi-stage Chainguard wolfi-base + PHP 8.4 + LiteSpeed"
        "Updated infra/wordpress.ctp: Chainguard base, Dilithium5 sig spec, SHAKE3-512 hash spec"
        "Created selur-compose.yml: stapeln orchestration with svalinn gateway, vordr runtime, redis, mariadb"
        "Created templates: wp-config-security.php, htaccess-well-known, htaccess-security"
        "Created robots.txt blocking AI training bots"
        "Added 11 missing GitHub workflows from rsr-template-repo (now 17 total)"
        "Added .bot_directives for gitbot-fleet (8 bots)"
        "Created TOPOLOGY.md architecture map + completion dashboard"
        "Updated rsr-template-repo Trustfile.hs with full user-security-requirements spec"
        "Created TOPOLOGY.md template + TOPOLOGY-GUIDE.adoc in rsr-template-repo"))
     (next-session "Verpex cPanel setup, DNS, WordPress install, content entry, security hardening"))
    ((date "2026-01-22-evening")
     (accomplishments
       ("Tested docker-compose.yml locally (WordPress + MariaDB working)"
        "Fixed docker-compose.yml for podman compatibility"
        "Filled all checksum TODOs in wordpress.ctp"
        "Set up ASDF plugins for varnish and openlitespeed"))
     (next-session "Phase 1 repo preparation (completed 2026-02-14)"))))
