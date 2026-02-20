<!-- SPDX-License-Identifier: PMPL-1.0-or-later -->
<!-- TOPOLOGY.md — Project architecture map and completion dashboard -->
<!-- Last updated: 2026-02-19 -->

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

## Completion Dashboard

```
COMPONENT                          STATUS              NOTES
─────────────────────────────────  ──────────────────  ─────────────────────────────────
WEBSITE & CONTENT
  Hardened WordPress (Debian)       ██████████ 100%    Production ready (Verpex)
  Sinople Theme (wp-sinople)        ██████████ 100%    WCAG/IndieWeb certified
  Member Area                       ████████░░  80%    Setup verified
  IPFS Static Mirror                ██████████ 100%    DNSLink auto-publish stable

SECURITY & INFRA
  Stapeln Integration               ████████░░  80%    Container dogfooding active
  ZeroTier Overlay                  ██████████ 100%    Private mesh verified
  IndieWeb2 Bastion                 ██████████ 100%    Consent portal active
  HTTP Capability Gateway           ██████████ 100%    Verb governance stable

REPO INFRASTRUCTURE
  Justfile Automation               ██████████ 100%    Standard build/deploy tasks
  .machine_readable/                ██████████ 100%    STATE tracking active
  Cerro Torre Signing               ██████████ 100%    Manifest integrity verified

─────────────────────────────────────────────────────────────────────────────
OVERALL:                            █████████░  ~90%   Production ready, infra maturing
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
