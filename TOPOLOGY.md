<!-- SPDX-License-Identifier: PMPL-1.0-or-later -->
<!-- TOPOLOGY.md — Project architecture map and completion dashboard -->
<!-- Last updated: 2026-02-14 (Phase 1 complete) -->

# NUJ LCB Website — Project Topology

## System Architecture

```
                        ┌─────────────────────────────────────────────────────┐
                        │                    VISITORS                         │
                        │              https://nuj-lcb.org.uk                 │
                        └───────────────────────┬─────────────────────────────┘
                                                │
                                                ▼
                        ┌─────────────────────────────────────────────────────┐
                        │                 CLOUDFLARE                          │
                        │  DNS (A records) · WAF · Bot Fight · TLS 1.3       │
                        │  HTTP/3 (QUIC) · Brotli · HSTS · Auto Minify      │
                        │  Full (Strict) SSL · Page Rules (bypass wp-admin)  │
                        └───────────────────────┬─────────────────────────────┘
                                                │
                        ════════════════════════════════════════════════════════
                        ║  PRODUCTION PATH (Verpex)  ║  CONTAINER PATH (Future) ║
                        ════════════════════════════════════════════════════════
                                │                              │
                ┌───────────────▼──────────────┐  ┌────────────▼──────────────┐
                │     VERPEX SHARED HOSTING    │  │    STAPELN PIPELINE       │
                │  cPanel · LiteSpeed Enterprise│  │                          │
                │  PHP 8.4 · AutoSSL           │  │  ┌────────────────────┐  │
                │                              │  │  │  Containerfile     │  │
                │  ┌────────────────────────┐  │  │  │  (wolfi-base)      │  │
                │  │     .htaccess          │  │  │  │  WordPress 6.9     │  │
                │  │  security headers      │  │  │  │  + PHP 8.4         │  │
                │  │  .well-known rewrite   │  │  │  │  + LiteSpeed       │  │
                │  │  AIBDP enforcement     │  │  │  │  + Sinople theme   │  │
                │  │  (HTTP 430 for bots)   │  │  │  └────────┬───────────┘  │
                │  └────────────────────────┘  │  │           │              │
                │                              │  │           ▼              │
                │  ┌────────────────────────┐  │  │  ┌────────────────────┐  │
                │  │   WordPress 6.9        │  │  │  │  cerro-torre       │  │
                │  │                        │  │  │  │  wordpress.ctp     │  │
                │  │  ┌──────────────────┐  │  │  │  │  Ed25519 + PQ     │  │
                │  │  │  Sinople Theme   │  │  │  │  │  SBOM · in-toto   │  │
                │  │  │  + php-aegis     │  │  │  │  └────────┬───────────┘  │
                │  │  │  + WCAG AAA      │  │  │  │           │              │
                │  │  │  + consent hdrs  │  │  │  │           ▼              │
                │  │  └──────────────────┘  │  │  │  ┌────────────────────┐  │
                │  │                        │  │  │  │  selur-compose.yml │  │
                │  │  ┌──────────────────┐  │  │  │  │  svalinn gateway   │  │
                │  │  │  Plugins         │  │  │  │  │  vordr runtime     │  │
                │  │  │  Wordfence       │  │  │  │  │  mariadb + redis   │  │
                │  │  │  LiteSpeed Cache │  │  │  │  └────────────────────┘  │
                │  │  │  bbPress         │  │  │  │                          │
                │  │  │  Members         │  │  │  └──────────────────────────┘
                │  │  │  WP Mail SMTP    │  │  │
                │  │  │  UpdraftPlus     │  │  │
                │  │  │  Contact Form 7  │  │  │
                │  │  │  Yoast/RankMath  │  │  │
                │  │  │  WP Activity Log │  │  │
                │  │  │  GDPR Compliance │  │  │
                │  │  │  Download Monitor│  │  │
                │  │  │  Redirection     │  │  │
                │  │  └──────────────────┘  │  │
                │  │                        │  │
                │  │  ┌──────────────────┐  │  │
                │  │  │  wp-config.php   │  │  │
                │  │  │  security consts │  │  │
                │  │  │  nujlcb_ prefix  │  │  │
                │  │  └──────────────────┘  │  │
                │  └────────────────────────┘  │
                │                              │
                │  ┌────────────────────────┐  │
                │  │   MariaDB (cPanel)     │  │
                │  │   cpaneluser_nujlcb    │  │
                │  └────────────────────────┘  │
                │                              │
                │  ┌────────────────────────┐  │
                │  │   LiteSpeed Cache      │  │
                │  │   TTL 900s (HTML)      │  │
                │  │   TTL 604800s (static) │  │
                │  │   Redis obj cache?     │  │
                │  │   WebP · QUIC.cloud    │  │
                │  └────────────────────────┘  │
                └──────────────────────────────┘

                ┌─────────────────────────────────────────────────────────────┐
                │                    CONTENT / PAGES                          │
                │                                                             │
                │  / (homepage)          /about-us/        /contact/          │
                │  /join/                /members/ (restricted)               │
                │  /news/                /ai-policy/        /legal/           │
                │  /forum/  (bbPress: General, Freelance, Events, Branch)     │
                └─────────────────────────────────────────────────────────────┘

                ┌─────────────────────────────────────────────────────────────┐
                │               .well-known / CONSENT                         │
                │                                                             │
                │  /.well-known/aibdp.json    AIBDP consent policy            │
                │  /.well-known/security.txt  Security contacts               │
                │  /.well-known/ai.txt        AI/bot access rules             │
                │  /robots.txt                AI bot blocks + sitemap          │
                └─────────────────────────────────────────────────────────────┘

                ┌─────────────────────────────────────────────────────────────┐
                │               REPO INFRASTRUCTURE                           │
                │                                                             │
                │  contractiles/must/Mustfile      Invariant checks           │
                │  contractiles/trust/Trustfile.hs Crypto verification        │
                │  contractiles/dust/Dustfile       Rollback/recovery         │
                │  contractiles/lust/Intentfile     Future intent             │
                │  contractiles/k9/                 K9 contractiles           │
                │  .machine_readable/6scm/          6 SCM files              │
                │  .github/workflows/               CI/CD pipelines          │
                │  justfile                         7080-line automation      │
                └─────────────────────────────────────────────────────────────┘
```

## Completion Dashboard

```
COMPONENT                          STATUS              NOTES
─────────────────────────────────  ──────────────────  ─────────────────────────────────
REPO LAYER
  .machine_readable/6scm/          ██████████ 100%    6 SCM files, STATE updated 2026-02-14
  .well-known/ (aibdp, ai, sec)    ██████████ 100%    3 files, security.txt expiry 2027-02-14
  justfile                          ██████████ 100%    7080 lines, tested
  .github/workflows/                ██████████ 100%    17 workflows (all standard present)
  0-AI-MANIFEST.a2ml                ██████████ 100%    Universal AI entry point
  hooks/                            ██████████ 100%    pre-commit, pre-push, commit-msg
  content/ (md source files)        ██████████ 100%    All pages + policies written
  wp-content/themes/sinople         ██████████ 100%    Theme with php-aegis + WCAG AAA
  .bot_directives/                  ██████████ 100%    8 gitbot-fleet bot directives
  robots.txt                        ██████████ 100%    Blocks 11 AI training bots
  contractiles/                     ██████████ 100%    must/trust/dust/lust/k9 all present
  templates/ (wp-config, htaccess)  ██████████ 100%    3 templates: security, well-known, headers
  TOPOLOGY.md                       ██████████ 100%    Architecture map + completion dashboard

CONTAINER PATH (stapeln)
  Containerfile                     ██████████ 100%    Multi-stage wolfi-base + PHP 8.4 + LiteSpeed
  infra/wordpress.ctp               █████████░  90%    Chainguard base, Dilithium5 spec, rolling hash
  selur-compose.yml                 ██████████ 100%    Stapeln: svalinn + vordr + mariadb + redis
  docker-compose.yml (dev)          ██████████ 100%    Works locally, tested

VERPEX DEPLOYMENT
  VERPEX-DEPLOYMENT.md              ████████░░  80%    427 lines, covers full cPanel workflow
  Cloudflare DNS                    ░░░░░░░░░░   0%    Not configured
  cPanel setup                      ░░░░░░░░░░   0%    Domain not added
  WordPress install                 ░░░░░░░░░░   0%    Not installed on Verpex
  SSL/TLS                           ░░░░░░░░░░   0%    Waiting on DNS + cPanel
  Plugin installation               ░░░░░░░░░░   0%    Waiting on WP install
  Security hardening                ░░░░░░░░░░   0%    Waiting on WP install
  LiteSpeed Cache config            ░░░░░░░░░░   0%    Waiting on WP install
  Content creation (WP pages)       ░░░░░░░░░░   0%    Source ready, needs WP entry
  Members area + roles              ░░░░░░░░░░   0%    Waiting on WP + plugins
  bbPress forum                     ░░░░░░░░░░   0%    Waiting on WP + plugins
  Email/SMTP                        ░░░░░░░░░░   0%    Waiting on WP + plugins

SECURITY DOCS
  SECURITY-IMPLEMENTATION.md        ██████████ 100%    Complete hardening guide
  SECURITY-THREAT-MODEL.md          ██████████ 100%    Full threat model
  CRYPTO-SECURITY-AUDIT.md          ██████████ 100%    Crypto audit complete
  SOCIAL-ENGINEERING-DEFENSE.md     ██████████ 100%    Social engineering defenses

─────────────────────────────────────────────────────────────────────────────
OVERALL REPO READINESS:             ██████████  ~98%   Phase 1 complete
OVERALL DEPLOYMENT:                 █░░░░░░░░░  ~10%   All server-side work pending
OVERALL PROJECT:                    █████░░░░░  ~50%   Repo done, deployment next
```

## Key Dependencies

```
Cloudflare DNS ──────► cPanel setup ──────► WordPress install
                                                  │
                                    ┌─────────────┼─────────────┐
                                    ▼             ▼             ▼
                              Plugin setup   Content entry   SSL/TLS
                                    │             │             │
                                    ▼             ▼             ▼
                              Security      Members area    LiteSpeed
                              hardening     + bbPress       Cache config
                                    │             │             │
                                    └─────────────┼─────────────┘
                                                  ▼
                                            LAUNCH READY
```

## Update Protocol

This file is maintained by both humans and AI agents. When updating:

1. **After completing a component**: Change its bar and percentage
2. **After adding a component**: Add a new row in the appropriate section
3. **After architectural changes**: Update the ASCII diagram
4. **Date**: Update the `Last updated` comment at the top of this file

Progress bars use: `█` (filled) and `░` (empty), 10 characters wide.
Percentages: 0%, 10%, 20%, ... 100% (in 10% increments).
