# Infrastructure Experiment Notes

This file records the experimental container infrastructure that this repo
is also used to dogfood. These components are **not required** for production
deployment — the site deploys fine with the standard Docker Compose stack
(see `DEPLOYMENT.md` and `VERPEX-DEPLOYMENT.md`).

## Experimental Components

| Component | Status | Notes |
|-----------|--------|-------|
| Vörðr (verified runtime) | 70% (Ada stubs) | Not blocking deployment |
| Cerro Torre (build system) | 0% (specs only) | Not blocking deployment |
| Svalinn (network gateway) | Unverified | Not blocking deployment |
| Consent-aware HTTP | Experimental | HTTP 430 not standardized yet |
| wp-sinople-theme | Functional PHP | Custom theme, usable now |

## Deployment Without Experimental Stack

The standard path uses:
- `docker-compose.yml` — WordPress 6.9 + MariaDB + Varnish (works today)
- `VERPEX-DEPLOYMENT.md` — cPanel deployment (step-by-step)
- `WORDPRESS-DEPLOYMENT-PLAN.md` — VPS with Docker (3 services)

## When Experimental Components Mature

As Vörðr, Cerro Torre, and Svalinn reach production quality, they can
replace the standard Docker stack. See `svalinn-compose.yml` for the
verified-container compose file (template only, not yet functional).

## What Gemini Sold vs Reality

See `WHAT-GEMINI-SOLD-VS-REALITY.md` for a critical analysis of AI-generated
architecture claims that turned out to be spec-driven fantasy.

## License

PMPL-1.0-or-later
