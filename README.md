# ⚠️ lcb-website - EXPERIMENTAL TESTBED

**WARNING: This is an EXPERIMENTAL repository. For production deployment, use `nuj-lcb-production` instead.**

See [EXPERIMENTAL-TESTBED.md](EXPERIMENTAL-TESTBED.md) for full details.

---

This repo tests advanced container infrastructure (Vörðr, Cerro Torre, Svalinn) for the hyperpolymath initiative. Components are incomplete and NOT production-ready.

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

`well-known-ecosystem` is the canonical source for `.well-known/` responses (AIBDP, security.txt, ai.txt, etc.) that our hardened site publishes; `docs/well-known-ecosystem-integration.adoc` explains how we pull its validated files into the site’s `.well-known/` directory before Cerro Torre packages the bundle.

## IRC alerting

`vext` powers our IRC alerting channel, feeding consent/capability events into `feedback-o-tron` and offering operators a Hybrid Automation Router hook for manual overrides; see `docs/vext-irc-support.adoc` for the workflow details and tie-ins with ZeroTier and capability logs.

## K8s & Twingate SDP

The hardened stack will eventually run behind the Twingate SDP mesh; `docs/twingate-k8s-integration.adoc` records the plan to align the K8s manifests with `twingate-helm-deploy` so the ZeroTier overlay, capability gateway, and consent portal are all behind the SDP path.

## RSR template

The repo follows the `rsr-template-repo`/`RSR_OUTLINE.adoc` scheme so the directory layout, `.well-known` assets, `justfile`, and `RSR_COMPLIANCE.adoc` structure remain compliant with the Rhodium Standard Repository template; see `docs/rsr-template-plan.adoc` for how we keep in sync and where to run `just validate-rsr`.

## Machine-readable metadata

Machine-readable metadata now lives under `.machine_readable/6scm`. These files (AGENTIC, ECOSYSTEM, META, NEUROSYM, PLAYBOOK, STATE) encode the AI agent config, ecosystem position, architectural practices, neurosymbolic hints, operational playbook, and project state so the next handover can load the repo context automatically.

## Roadmap

See `ROADMAP.adoc` for the quarterly plan that covers manifest/automation/consent/overlay milestones.

## License

This repository is licensed under **PMPL-1.0-or-later** (the Palimpsest-MPL License 1.0 or later). See `LEGAL.txt` for the full legal text from the Palimpsest Stewardship Council.

## Robot Repo Automaton

`robot-repo-automaton` orchestrates the automation policies and deployment gating for this repo; see `docs/robot-repo-automaton.adoc` to understand how its scripts hook into `just validate`, the ZeroTier stack, consent reports, and the feedback/automation workflows before the hardened WordPress bundle is released.
