# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <jonathan.jewell@open.ac.uk>
#
# NUJ LCB Site Configuration Status

**Last Updated:** 2026-02-17

## Current Phase: Backend LIVE, Content/Plugin Baseline PENDING

See `TOPOLOGY.md` for the full architecture diagram and completion dashboard.

## Target Stack

| Component | Version | Status |
|-----------|---------|--------|
| **WordPress** | 6.9.1 | Installed and live on Verpex |
| **PHP** | 8.4 | Available on Verpex |
| **Theme** | Sinople (+ php-aegis) | Installed and active (activation hook patched for Verpex compatibility) |
| **Database** | MariaDB (cPanel managed) | Created (`nujprcor_lcbwp26`) |
| **Cache** | LiteSpeed Cache 7.7 | Not installed yet |
| **Hosting** | Verpex cPanel + LiteSpeed Enterprise | Account exists |
| **DNS/CDN** | Cloudflare | Configured (A `@` -> `65.181.113.13`, proxied CNAME `www` -> apex) |
| **SSL** | AutoSSL + Cloudflare | `Strict` mode restored and working |
| **URL** | https://nuj-lcb.org.uk | Live (WordPress homepage + `/wp-login.php` reachable) |

## Pages (from content/)

| Page | Source File | Slug | Status |
|------|-----------|------|--------|
| Homepage | `content/mockups/homepage.html` | `/` | Content ready |
| About Us | `content/pages/about-us.md` | `/about-us/` | Content ready |
| Contact | `content/pages/contact.md` | `/contact/` | Content ready |
| Join the NUJ | `content/pages/join-us.md` | `/join/` | Content ready |
| Members Area | `content/pages/members-area.md` | `/members/` | Content ready |
| News & Updates | `content/pages/linkedin-feed.md` | `/news/` | Content ready |
| AI Usage Policy | `content/policies/ai-usage-policy.md` | `/ai-policy/` | Content ready |
| Legal Information | `content/policies/imprint-impressum.md` | `/legal/` | Content ready |

## Plugins (to be installed)

| Plugin | Purpose | Priority |
|--------|---------|----------|
| **Wordfence Security** | WAF, malware scan, 2FA, login security | Day 1 |
| **LiteSpeed Cache** | Server-level cache, image optimisation, CDN | Day 1 |
| **bbPress** | Forum (General, Freelance, Events, Branch) | Day 1 |
| **Members** | Custom roles (nuj_member), content restriction | Day 1 |
| **WP Mail SMTP** | Reliable email delivery | Day 1 |
| **UpdraftPlus** | Encrypted backups | Day 1 |
| **Contact Form 7** | Contact forms | Day 1 |
| **WP Activity Log** | Audit trail for all admin actions | Week 1 |
| **WP GDPR Compliance** | GDPR consent, data requests | Week 1 |
| **Download Monitor** | Secure PDF/file downloads | Week 1 |
| **Redirection** | URL redirects, 404 monitoring | Week 1 |
| **Yoast SEO** | SEO, sitemaps, Open Graph | Week 1 |

## Security Configuration

### Templates Ready (in `templates/`)
- `wp-config-security.php` — 12 security constants (DISALLOW_FILE_EDIT, FORCE_SSL, etc.)
- `htaccess-security` — HSTS, CSP, X-Frame-Options, XML-RPC block, directory listing off
- `htaccess-well-known` — .well-known bypass rules, AIBDP enforcement

### robots.txt
Blocks 11 AI training bots (GPTBot, ChatGPT-User, CCBot, anthropic-ai, Claude-Web, cohere-ai, Google-Extended, Bytespider, PetalBot, Amazonbot, FacebookBot).

### .well-known Files
- `aibdp.json` — AIBDP consent policy (strict mode, HTTP 430 for non-consent)
- `security.txt` — Security contacts (expires 2027-02-14)
- `ai.txt` — AI/bot access rules

## Container Path (Future)

| Component | File | Status |
|-----------|------|--------|
| Build | `Containerfile` | Multi-stage wolfi-base + PHP 8.4 + LiteSpeed |
| Manifest | `infra/wordpress.ctp` | Chainguard base, Ed25519 + Dilithium5 spec |
| Orchestration | `selur-compose.yml` | Svalinn gateway + Vordr runtime + MariaDB + Redis |
| Dev Stack | `docker-compose.yml` | Podman Compose tested locally (OLS + MariaDB + Varnish) |

## Repo Infrastructure

| Component | Status |
|-----------|--------|
| Contractiles (must/trust/dust/lust/k9) | Complete |
| .machine_readable/6scm/ (6 files) | Updated 2026-02-17 |
| .bot_directives/ (8 bots) | Complete |
| .github/workflows/ (17 workflows) | Complete |
| TOPOLOGY.md | Complete |
| justfile (7080 lines) | Complete |
| Git hooks | Complete |

## Deployment Guides

1. **VERPEX-DEPLOYMENT.md** — Step-by-step cPanel deployment
2. **DEPLOYMENT.md** — Local Podman setup
3. **WORDPRESS-DEPLOYMENT-PLAN.md** — Full VPS deployment
4. **SECURITY-IMPLEMENTATION.md** — Security hardening guide

## Next Steps

1. Log in at `https://nuj-lcb.org.uk/wp-login.php` and rotate the initial admin password
2. Install/activate plugin baseline (Wordfence, LiteSpeed Cache, bbPress, Members, SMTP, backups)
3. Run `scripts/wp-deploy.sh` (or equivalent WP-CLI flow) for pages/menu/forum baseline
4. Populate and review content from `content/pages/` and `content/policies/`
5. Configure SMTP + backups + final security hardening and launch checks
