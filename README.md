# lcb-website

Website project for LCB. Track updates and assets for the hyperpolymath initiative.

## Container Baseline

The verified-container stack for this repo relies on `svalinn`, `cerro-torre`, and `vordr` as the gateway, builder, and runtime, respectively. See `Containerfile` for the immediate plan and references we will dogfood while developing the site.

## Toolchain

We use `asdf` to pin the runtimes that run behind this site, especially the Varnish cache and OpenLiteSpeed server. See `ASDF.md` for which plugins and commands are required so AI handovers and collaborators can stay in sync.

## Hardened WordPress story

See `docs/hardened-wordpress.adoc` for the chosen Debian-based Hardened WordPress base, Sanctify/PHP-Aegis hardening workflow, and how Cerro Torre/Svalinn/Vörðr treat it as the verified container artifact.

## Front-end routing

We will leverage `cadre-router` for the client-side navigation and dashboard experience; refer to `docs/cadre-router.adoc` for the integration notes so the SPA can stay type-safe and aligned with the verified manifests.

## AI/bot consent

Follow `docs/consent-aware-http.adoc` to see how the site enforces the consent-aware HTTP/AIBDP requirements, returns HTTP 430 when bots do not consent, and coordinates the `.well-known/aibdp.json` declaration with the Cedar/Rust proxy layer before Svalinn/Vörðr execute any manifest.

## Feedback pipeline

When something goes wrong (manifest rejection, attestation error, consent failure), feed the incident into `feedback-o-tron`; see `docs/feedback-o-tron.adoc` for how we expose its MCP `submit_feedback` tool and include the audits in the Cerro Torre bundle so the issue is broadcast across every platform.

## Quality automation

This repo is part of the `gitbot-fleet` quality automation suite; see `docs/gitbot-fleet-support.adoc` for the checklist each bot (rhodibot, echidnabot, oikos, glambot, seambot, finishing-bot) uses to validate this project.

## HTTP defense

`http-capability-gateway` acts as the first gate for the hardened site, so see `docs/http-capability-gateway.adoc` for how we author Verb Governance specs, map them to consent-aware HTTP narratives, and forward enforcement logs into the feedback and audit trails.

## Automation router

To coordinate automation (consent audits, feedback reporting, manifest rebuilds), we plug into `hybrid-automation-router`; the new `docs/hybrid-automation-router.adoc` tells you which auth-protected workflows we call and how they pipe events back into the container proofs.

## IndieWeb base

The site’s inbound consent portal and provenance layer follow `indieweb2-bastion`; see `docs/indieweb2-bastion.adoc` for how the bastion’s consent-first GUI, Nickel/SurrealDB provenance, and GraphQL DNS policies seed the hardened WordPress stack and match the consent-aware HTTP specification.

## ZeroTier overlay

Encrypted overlay networking uses `zerotier-k8s-link`; `docs/zerotier-k8s-link.adoc` explains how the ZeroTier DaemonSet joins the yacht/agents to the private mesh, how the overlay routes feed the capability gateway and automation router, and how health commands tie back into the feedback pipeline.

## WordPress theme

The hardened WordPress runtime runs the `wp-sinople-theme`; see `docs/wp-sinople-theme.adoc` for build steps (WASM/ReScript), WCAG/IndieWeb certifications, and how its semantic APIs tie into the `cadre-router` front-end, consent controls, and feedback pipelines.

## Well-known ecosystem

`well-known-ecosystem` is the canonical source for `.well-known/` responses (AIBDP, security.txt, ai.txt, etc.) that our hardened site will eventually publish; we keep it on the roadmap so you can see how the entire stack will link up later (`docs/well-known-ecosystem.adoc`).

## License

This repository is licensed under **PMPL-1.0-or-later** (the Palimpsest-MPL License 1.0 or later). See `LEGAL.txt` for the full legal text from the Palimpsest Stewardship Council.
