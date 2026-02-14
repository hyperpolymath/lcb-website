;; SPDX-License-Identifier: PMPL-1.0-or-later
;; STATE.scm - Project state for lcb-website
;; Media-Type: application/vnd.state+scm

(state
  (metadata
    (version "0.2.0")
    (schema-version "1.0")
    (created "2026-01-19")
    (updated "2026-02-14")
    (project "lcb-website")
    (repo "github.com/hyperpolymath/lcb-website"))

  (project-context
    (name "lcb-website")
    (tagline "NUJ London Central Branch website — hardened WordPress on Verpex with stapeln container path")
    (tech-stack ("WordPress 6.9" "PHP 8.4" "Sinople theme" "php-aegis" "LiteSpeed Cache"
                 "Cerro Torre" "Svalinn" "Vörðr" "consent-aware HTTP" "Chainguard wolfi-base")))

  (current-position
    (phase "phase-1-repo-preparation")
    (overall-completion 80)
    (components ("container baseline" "manifest" "consent docs" "automation" "ci-cd"
                 "docker-compose" "contractiles" "templates" "robots-txt" "selur-compose"
                 "well-known" "security-docs" "content" "bot-directives" "topology"))
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
       "Sinople theme with php-aegis + WCAG AAA"
       "Full security documentation suite")))

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
    (high ("Verpex cPanel setup not started — needs domain addon + database creation"
           "Cloudflare DNS not configured — A records needed"))
    (medium ("security.txt well-known hash in wordpress.ctp needs recalculation after expiry update"
             "Container path blocked: cerro-torre ct binary needs Alire index fix"))
    (low ("svalinn-compose.yml kept for reference — can delete after selur-compose.yml verified")))

  (critical-next-actions
    (immediate ("Configure Verpex cPanel (addon domain, MySQL database, PHP 8.4)"
                "Configure Cloudflare DNS (A records → Verpex IP)"
                "Install WordPress 6.9 on Verpex"))
    (this-week ("Upload Sinople theme and .well-known files"
                "Install and configure plugins (Wordfence, LiteSpeed Cache, bbPress, Members)"
                "Create WordPress pages from content/ markdown"
                "Security hardening (2FA, headers, encrypted backups)"))
    (this-month ("Configure LiteSpeed Cache (TTLs, Redis, WebP)"
                 "Set up members area with nuj_member role"
                 "Set up bbPress forum (4 forums)"
                 "Configure WP Mail SMTP"
                 "Launch and verify (securityheaders.com A+, ssllabs.com A+)")))

  (session-history
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
