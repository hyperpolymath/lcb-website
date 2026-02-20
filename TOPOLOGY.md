<!-- SPDX-License-Identifier: PMPL-1.0-or-later -->
<!-- TOPOLOGY.md — Project architecture map and completion dashboard -->
<!-- Last updated: 2026-02-20 -->

# lcb-website (NUJ London Central Branch) — Project Topology

## System Architecture

```
                        ┌─────────────────────────────────────────┐
                        │              INTERNET USERS             │
                        │        (Member Area, Public Site)       │
                        └───────────────────┬─────────────────────┘
                                            │ HTTPS (Cloudflare)
                                            ▼
                        ┌─────────────────────────────────────────┐
                        │           INDIEWEB2 BASTION             │
                        │    (Consent Portal, oDNS, mTLS Gate)    │
                        └───────────────────┬─────────────────────┘
                                            │
                                            ▼
                        ┌─────────────────────────────────────────┐
                        │           STAPELN CONTAINER STACK       │
                        │  ┌───────────┐  ┌───────────────────┐  │
                        │  │  Svalinn  │──►   Vörðr Runtime   │  │
                        │  │ (Gateway) │  │ (Hardened WP Core)│  │
                        │  └─────┬─────┘  └────────┬──────────┘  │
                        └────────│─────────────────│──────────────┘
                                 │                 │
                                 ▼                 ▼
                        ┌─────────────────────────────────────────┐
                        │           SERVICES & STORAGE            │
                        │  ┌───────────┐  ┌───────────┐  ┌───────┐│
                        │  │ OpenLite- │  │ MariaDB   │  │ Varnish││
                        │  │ Speed     │  │ (SQL)     │  │ Cache  ││
                        │  └───────────┘  └───────────┘  └───────┘│
                        └───────────────────┬─────────────────────┘
                                            │
                                            ▼
                        ┌─────────────────────────────────────────┐
                        │           NETWORKING OVERLAY            │
                        │    (ZeroTier Mesh, Twingate SDP)        │
                        └─────────────────────────────────────────┘

                        ┌─────────────────────────────────────────┐
                        │          REPO INFRASTRUCTURE            │
                        │  Cerro-Torre Manifest .machine_readable/│
                        │  Justfile Automation  IPFS Publish      │
                        └─────────────────────────────────────────┘
```

### Sinople Theme Layout (v2.0.0 — Newspaperup Style)

```
┌──────────────────────────────────────────────────────┐
│ TOP BAR: date │ breaking news ticker │ social icons  │  topbar.php
├──────────────────────────────────────────────────────┤
│ HEADER: logo │ primary nav │ dark toggle │ search    │  main-header.php
├──────────────────────────────────────────────────────┤
│ FEATURED: sidebar │ swiper carousel │ sidebar        │  content-featured.php
├────────────────────────────┬─────────────────────────┤
│ MAIN CONTENT (2fr)         │ SIDEBAR (var)           │  front-page.php
│  post cards grid           │  search, latest posts   │  content-card.php
│  pagination                │  categories, archives   │  sidebar.php
├────────────────────────────┴─────────────────────────┤
│ MISSED POSTS: 4-column recent post grid              │  missed-posts.php
├──────────────────────────────────────────────────────┤
│ FOOTER: 4 widget columns │ copyright │ social        │  footer-widgets.php
└──────────────────────────────────────────────────────┘
│ OFFCANVAS (mobile): slide-in drawer navigation       │  offcanvas.php
│ SEARCH MODAL: full-screen overlay (Ctrl+K)           │  search-modal.php
```

## Completion Dashboard

```
COMPONENT                          STATUS              NOTES
─────────────────────────────────  ──────────────────  ─────────────────────────────────
WEBSITE & CONTENT
  Hardened WordPress (Verpex)       ██████████ 100%    WP 6.9.1, PHP 8.4, live
  Sinople Theme v2.0.0             ██████████ 100%    Newspaperup layout, NUJ green
    Layout & Templates              ██████████ 100%    20 templates, 8 includes
    CSS Design Tokens               ██████████ 100%    18 CSS files, variables.css
    Dark Mode                       ██████████ 100%    localStorage + OS pref
    WCAG AAA Accessibility          ██████████ 100%    9/10 PASS, searchform fixed
    IndieWeb (webmention/micropub)  ██████████ 100%    Level 4 compliant
    Responsive (1200/992/768/375)   ██████████ 100%    CSS Grid, no Bootstrap
    Block Patterns                  ██████████ 100%    4 NUJ content patterns
    Minified Production Assets      ██████████ 100%    CSS/JS concatenated
    Vendor Assets                   ██████████ 100%    Fonts, FA6, Swiper self-hosted
    Seed Content (8 posts)          ██████████ 100%    Featured images generated
  Member Area                       ████████░░  80%    Setup verified
  IPFS Static Mirror                ██████████ 100%    DNSLink auto-publish stable

SECURITY & INFRA
  php-aegis + CSP Headers           ██████████ 100%    PhpAegis authoritative
  Stapeln Integration               ████████░░  80%    Container dogfooding active
  ZeroTier Overlay                  ██████████ 100%    Private mesh verified
  IndieWeb2 Bastion                 ██████████ 100%    Consent portal active
  HTTP Capability Gateway           ██████████ 100%    Verb governance stable

REPO INFRASTRUCTURE
  Justfile Automation               ██████████ 100%    Standard build/deploy tasks
  .machine_readable/                ██████████ 100%    STATE tracking active
  Cerro Torre Signing               ██████████ 100%    Manifest integrity verified

─────────────────────────────────────────────────────────────────────────────
OVERALL:                            █████████░  ~95%   Theme complete, deploy pending
```

## Key Dependencies

```
Consent Portal ───► Svalinn Gate ───► Vörðr Runtime ───► WP Theme
     │                 │                 │                  │
     ▼                 ▼                 ▼                  ▼
SurrealDB Prov ───► ZeroTier Mesh ──► MariaDB SQL ──────► User HUD
```

## Update Protocol

This file is maintained by both humans and AI agents. When updating:

1. **After completing a component**: Change its bar and percentage
2. **After adding a component**: Add a new row in the appropriate section
3. **After architectural changes**: Update the ASCII diagram
4. **Date**: Update the `Last updated` comment at the top of this file

Progress bars use: `█` (filled) and `░` (empty), 10 characters wide.
Percentages: 0%, 10%, 20%, ... 100% (in 10% increments).
