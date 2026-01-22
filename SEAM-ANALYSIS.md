# lcb-website Ecosystem - Comprehensive Seam Analysis

**Created:** 2026-01-22
**Purpose:** Identify gaps, rough edges, and integration points across all 14 components
**Status:** Pre-integration analysis

---

## Executive Summary

This document analyzes the "seams" between all components in the lcb-website hardened stack. A **seam** is an interface, integration point, or dependency between two components. Poor seams create friction, bugs, and deployment failures.

**Key Findings:**
- 🔴 **7 critical seams** need immediate attention
- 🟡 **12 medium-priority seams** need testing
- 🟢 **15 working seams** already functional
- ⚪ **8 deferred seams** for post-MVP

---

## Component Matrix

| Component | Role | Completion | Seams In | Seams Out |
|-----------|------|------------|----------|-----------|
| **cerro-torre** | Builder | 90% | 0 (source) | 3 |
| **svalinn** | Gateway | 100% | 1 | 2 |
| **vordr** | Runtime | 90% | 2 | 1 |
| **zerotier-k8s-link** | Network | 90% | 0 | 2 |
| **twingate-helm-deploy** | Network | 85% | 1 | 1 |
| **bunsenite** | Config | 75% | 0 | 5 |
| **indieweb2-bastion** | Consent | 70% | 0 | 3 |
| **hybrid-automation-router** | Automation | 90% | 3 | 2 |
| **cadre-router** | Frontend | 75% | 0 | 2 |
| **vext** | Alerting | 85% | 2 | 1 |
| **feedback-o-tron** | Feedback | 85% | 3 | 4 |
| **http-capability-gateway** | Policy | 30% | 1 | 2 |
| **wp-sinople-theme** | WordPress | 60% | 2 | 1 |
| **sanctify-php** | Hardening | 40% | 0 | 1 |
| **php-aegis** | Security | 65% | 0 | 1 |
| **robot-repo-automaton** | CI/CD | 70% | 1 | 1 |

---

## Critical Seams (🔴 HIGH PRIORITY)

### SEAM-01: Cerro Torre → Svalinn (Container Verification)
**Status:** 🔴 **BROKEN** - Missing manifest format
**Components:** cerro-torre (90%) ↔ svalinn (100%)

**Interface:**
- Cerro Torre outputs: `.ctp` tarball with manifest + SBOM
- Svalinn expects: Verified container spec format
- Gap: Manifest schema mismatch

**Issues:**
- Cerro Torre manifest format not documented for Svalinn consumption
- No verification endpoint in Svalinn for `.ctp` files
- SBOM attachment incomplete (missing CycloneDX/SPDX)

**Resolution:**
1. Document manifest JSON schema in cerro-torre
2. Add `/verify-ctp` endpoint to Svalinn
3. Complete SBOM attachment in OCI exporter
4. Integration test: `ct pack` → svalinn verify

**Blocking:** WordPress manifest creation
**Estimated Effort:** 8-12 hours

---

### SEAM-02: Svalinn → Vörðr (Container Delegation)
**Status:** 🟢 **WORKING** - MCP/JSON-RPC functional
**Components:** svalinn (100%) ↔ vordr (90%)

**Interface:**
- Svalinn sends: JSON-RPC requests via MCP
- Vörðr receives: Container operation commands
- Protocol: Model Context Protocol (MCP)

**Testing:**
- ✅ MCP client in Svalinn complete
- ✅ MCP adapter in Vörðr complete
- ⚠️ Integration test missing

**Resolution:**
1. Create integration test suite
2. Test all container operations (run, stop, inspect)
3. Test error handling and rollback

**Blocking:** None (can test now)
**Estimated Effort:** 4-6 hours

---

### SEAM-03: Vörðr → Podman/Docker (Runtime Execution)
**Status:** 🟡 **PARTIAL** - CLI complete, runtime integration needs testing
**Components:** vordr (90%) ↔ Podman/Docker

**Interface:**
- Vörðr sends: Container lifecycle commands
- Runtime receives: Standard OCI runtime API
- Protocol: CLI subprocess or CRI-O gRPC

**Issues:**
- Rust CLI exists but subprocess invocation not tested
- Idris2 formal proofs incomplete (90% of proofs)
- No health check integration

**Resolution:**
1. Test Rust CLI subprocess invocation
2. Add health check monitoring
3. Complete remaining Idris2 lifecycle proofs (optional for MVP)

**Blocking:** None
**Estimated Effort:** 4-6 hours

---

### SEAM-04: ZeroTier ↔ Twingate (Overlay Networking)
**Status:** 🔴 **UNTESTED** - Both components ready, integration untested
**Components:** zerotier-k8s-link (90%) ↔ twingate-helm-deploy (85%)

**Interface:**
- ZeroTier provides: Encrypted mesh overlay (Layer 2)
- Twingate provides: Software-Defined Perimeter (SDP) access control
- Integration: Twingate routes through ZeroTier mesh

**Issues:**
- No integration testing (requires live K8s cluster + Twingate account)
- Network policy coordination unclear
- DNS resolution across both networks needs testing

**Resolution:**
1. Provision Twingate account
2. Deploy both to K8s cluster (local k3s acceptable)
3. Test cross-network connectivity
4. Document network topology

**Blocking:** External account provisioning
**Estimated Effort:** 8-12 hours (including setup)

---

### SEAM-05: wp-sinople-theme → WordPress (Theme Integration)
**Status:** 🟡 **PARTIAL** - PHP structure exists, WASM/ReScript incomplete
**Components:** wp-sinople-theme (60%) ↔ WordPress

**Interface:**
- Theme provides: WASM modules + ReScript components
- WordPress loads: Via PHP template system
- Integration: WASM loaded in browser, ReScript compiled to JS

**Issues:**
- WASM build process broken (2 Rust files incomplete)
- ReScript components partial (2 files, need 10-15)
- Theme registration with WordPress untested

**Resolution:**
1. Fix WASM build pipeline (Rust → wasm32-unknown-unknown)
2. Complete ReScript components for semantic web processing
3. Test theme activation in WordPress
4. WCAG compliance audit

**Blocking:** WASM/ReScript expertise
**Estimated Effort:** 12-16 hours

---

### SEAM-06: sanctify-php + php-aegis → WordPress (Hardening)
**Status:** 🔴 **BROKEN** - sanctify-php incomplete, php-aegis not integrated
**Components:** sanctify-php (40%) + php-aegis (65%) → WordPress

**Interface:**
- sanctify-php transforms: PHP code for safety
- php-aegis provides: Security utilities
- WordPress uses: Hardened PHP codebase

**Issues:**
- sanctify-php core transformer only 45% complete
- php-aegis has 10 PHP files but no WordPress integration guide
- No automated hardening workflow

**Resolution:**
1. Complete sanctify-php core transformer (Safety rules engine)
2. Create php-aegis WordPress plugin wrapper
3. Document hardening workflow: raw PHP → sanctify → aegis → deploy
4. Test with actual WordPress core files

**Blocking:** sanctify-php completion
**Estimated Effort:** 16-20 hours

---

### SEAM-07: http-capability-gateway → Svalinn (Policy Enforcement)
**Status:** 🔴 **BROKEN** - http-capability-gateway not implemented
**Components:** http-capability-gateway (30%) → svalinn (100%)

**Interface:**
- Capability gateway provides: Verb governance, HTTP policy layer
- Svalinn integrates: As first-level policy enforcement
- Protocol: HTTP proxy/middleware

**Issues:**
- Capability gateway in design phase only (no implementation)
- Svalinn has own policy engine (may be redundant)
- Architecture unclear (standalone vs middleware)

**Resolution:**
**Option A:** Defer to post-MVP, use Svalinn's policy engine
**Option B:** Implement as Svalinn middleware (8-12 hours)
**Option C:** Implement standalone gateway (16-24 hours)

**Recommendation:** Option A (defer), use Svalinn policies for MVP

**Blocking:** Architecture decision
**Estimated Effort:** 0 hours (defer) OR 8-24 hours (implement)

---

## Medium-Priority Seams (🟡 TESTING NEEDED)

### SEAM-08: Bunsenite → All Components (Nickel Configuration)
**Status:** 🟡 **PARTIAL** - Parser works, integration untested
**Components:** bunsenite (75%) → 5 consumers

**Consumers:**
1. svalinn (policy config)
2. vordr (runtime config)
3. indieweb2-bastion (consent config)
4. hybrid-automation-router (workflow config)
5. lcb-website (docker-compose replacements)

**Issues:**
- Bunsenite Rust parser works (20 files)
- Deno bindings exist (3 JS files)
- ReScript bindings incomplete (1 file, needs more)
- No consumers actually using Nickel yet (all use YAML/TOML)

**Resolution:**
1. Complete ReScript bindings
2. Convert 1-2 config files per consumer to Nickel
3. Test parsing in each consumer
4. Document migration path from YAML/TOML

**Blocking:** None (can start migration)
**Estimated Effort:** 12-16 hours

---

### SEAM-09: IndieWeb2-Bastion → WordPress (Consent Portal)
**Status:** 🟡 **PARTIAL** - Consent portal exists, WordPress integration unclear
**Components:** indieweb2-bastion (70%) → WordPress

**Interface:**
- Bastion provides: Consent GUI, provenance graph (SurrealDB)
- WordPress integrates: Via consent-aware HTTP
- Protocol: HTTP API + GraphQL DNS

**Issues:**
- GraphQL DNS APIs only 60% complete
- WordPress integration not documented
- SurrealDB provenance graph exists but no WordPress connector

**Resolution:**
1. Complete GraphQL DNS APIs
2. Create WordPress plugin for consent integration
3. Test consent flow: user → bastion → WordPress
4. Document AIBDP compliance

**Blocking:** GraphQL DNS completion
**Estimated Effort:** 10-14 hours

---

### SEAM-10: Hybrid-Automation-Router → feedback-o-tron (Event Pipeline)
**Status:** 🟢 **WORKING** - Both components functional, integration untested
**Components:** hybrid-automation-router (90%) ↔ feedback-o-tron (85%)

**Interface:**
- Router triggers: Automation workflows
- feedback-o-tron receives: Incident reports
- Protocol: Elixir process communication or HTTP API

**Testing:**
- ✅ Both are Elixir-based (process communication possible)
- ✅ feedback-o-tron has MCP server
- ⚠️ Integration test missing

**Resolution:**
1. Test Router → feedback event flow
2. Test feedback-o-tron multi-platform submission
3. Document event schema

**Blocking:** None
**Estimated Effort:** 4-6 hours

---

### SEAM-11: vext → ZeroTier + feedback-o-tron (IRC Notifications)
**Status:** 🟡 **PARTIAL** - vext complete, integrations untested
**Components:** vext (85%) → zerotier-k8s-link + feedback-o-tron

**Interface:**
- vext monitors: Git commits, system events
- vext sends: IRC notifications
- vext integrates: ZeroTier overlay for connectivity, feedback pipeline for incidents

**Issues:**
- vext Rust daemon complete (13 files)
- ReScript bindings exist (5 files)
- No integration tests with ZeroTier or feedback pipeline

**Resolution:**
1. Test vext with ZeroTier overlay (IRC over mesh)
2. Test vext → feedback-o-tron incident reporting
3. Configure IRC channel for lcb-website

**Blocking:** ZeroTier deployment
**Estimated Effort:** 4-6 hours

---

### SEAM-12: cadre-router → wp-sinople-theme (Frontend Routing)
**Status:** 🟡 **PARTIAL** - Router works, WordPress integration unclear
**Components:** cadre-router (75%) → wp-sinople-theme (60%)

**Interface:**
- cadre-router provides: ReScript-first routing (28 files)
- wp-sinople-theme uses: For SPA navigation
- Integration: JavaScript bundle loaded in theme

**Issues:**
- cadre-router has 28 ReScript + 26 JS files (substantial codebase)
- wp-sinople-theme only has 2 ReScript files (incomplete)
- Integration mechanism unclear (WordPress isn't typically SPA)

**Resolution:**
1. Clarify architecture: Is WordPress becoming headless SPA?
2. Complete wp-sinople-theme ReScript components
3. Test routing in WordPress context
4. Document frontend architecture

**Blocking:** Architecture decision
**Estimated Effort:** 8-12 hours

---

### SEAM-13: robot-repo-automaton → lcb-website (Deployment Gating)
**Status:** 🟡 **PARTIAL** - Automaton works, lcb-website rules missing
**Components:** robot-repo-automaton (70%) → lcb-website

**Interface:**
- Automaton provides: Deployment gating, policy enforcement
- lcb-website uses: CI/CD automation policies
- Protocol: Git hooks + GitHub Actions

**Issues:**
- Automaton has 12 Rust files (execution layer complete)
- No deployment gating rules defined for lcb-website
- Integration with lcb-website CI/CD not configured

**Resolution:**
1. Define deployment gating rules for lcb-website
2. Add robot-repo-automaton to lcb-website CI/CD
3. Test gating logic (block bad commits, allow good ones)

**Blocking:** None
**Estimated Effort:** 4-6 hours

---

## Working Seams (🟢 FUNCTIONAL)

These seams are already functional and just need validation testing:

1. **SEAM-14:** Svalinn auth layer (OAuth2/JWT) ✅
2. **SEAM-15:** Vörðr GenStateMachine reversibility ✅
3. **SEAM-16:** cerro-torre crypto (SHA-256, Ed25519) ✅
4. **SEAM-17:** cerro-torre manifest parser ✅
5. **SEAM-18:** cerro-torre tar archives ✅
6. **SEAM-19:** zerotier-k8s-link DaemonSet ✅
7. **SEAM-20:** twingate-helm-deploy Helm chart ✅
8. **SEAM-21:** hybrid-automation-router Elixir core ✅
9. **SEAM-22:** vext IRC daemon ✅
10. **SEAM-23:** feedback-o-tron MCP server ✅
11. **SEAM-24:** bunsenite Rust parser ✅
12. **SEAM-25:** bunsenite C ABI (Zig) ✅
13. **SEAM-26:** cadre-router routing engine ✅
14. **SEAM-27:** php-aegis security utils ✅
15. **SEAM-28:** Docker Compose stack (WordPress/MariaDB/Varnish) ✅

---

## Deferred Seams (⚪ POST-MVP)

These seams can be addressed after initial deployment:

1. **SEAM-29:** cerro-torre PQ crypto → All (post-quantum signatures)
2. **SEAM-30:** cerro-torre transparency → Logs (certificate transparency)
3. **SEAM-31:** cerro-torre Alpine importer → Alpine packages
4. **SEAM-32:** cerro-torre Fedora importer → Fedora packages
5. **SEAM-33:** cerro-torre OSTree exporter → rpm-ostree
6. **SEAM-34:** Vörðr eBPF monitor → syscalls (runtime monitoring)
7. **SEAM-35:** Vörðr Idris2 proofs → lifecycle (formal verification)
8. **SEAM-36:** http-capability-gateway (full implementation)

---

## Smoothing Plan (Remove Rough Edges)

### Rough Edge #1: Inconsistent Error Handling
**Issue:** Different components use different error formats
**Fix:** Standardize on JSON error schema across all HTTP APIs
**Effort:** 2-4 hours

### Rough Edge #2: Missing Health Checks
**Issue:** Not all components expose `/health` endpoints
**Fix:** Add health endpoints to: cerro-torre, bunsenite, vext
**Effort:** 2-3 hours

### Rough Edge #3: Configuration Sprawl
**Issue:** Mix of YAML, TOML, Nickel, JSON configs
**Fix:** Migrate to Nickel (via bunsenite) across all components
**Effort:** 12-16 hours

### Rough Edge #4: Documentation Gaps
**Issue:** Integration docs missing for most seams
**Fix:** Create `/docs/integration/` guides for each seam
**Effort:** 8-12 hours

### Rough Edge #5: No Unified Logging
**Issue:** Each component logs differently
**Fix:** Standardize on structured JSON logging
**Effort:** 4-6 hours

### Rough Edge #6: Duplicate Policy Engines
**Issue:** Svalinn and http-capability-gateway both do policies
**Fix:** Consolidate into Svalinn (defer http-capability-gateway)
**Effort:** 0 hours (decision only)

---

## Sealing Plan (Close Vulnerabilities)

### Vulnerability #1: Missing Input Validation
**Issue:** Not all HTTP endpoints validate input schemas
**Fix:** Add JSON Schema validation to all APIs
**Effort:** 6-8 hours

### Vulnerability #2: Incomplete Authentication
**Issue:** Some internal APIs lack authentication
**Fix:** Add JWT validation to internal service-to-service calls
**Effort:** 4-6 hours

### Vulnerability #3: No Rate Limiting
**Issue:** APIs vulnerable to DoS
**Fix:** Add rate limiting middleware to Svalinn and routers
**Effort:** 3-4 hours

### Vulnerability #4: Unverified Container Images
**Issue:** Can run containers without Cerro Torre verification
**Fix:** Enforce verification in Vörðr (reject unverified images)
**Effort:** 2-3 hours

### Vulnerability #5: Missing Audit Logs
**Issue:** No centralized audit trail
**Fix:** Send all operations to feedback-o-tron for logging
**Effort:** 6-8 hours

### Vulnerability #6: Secret Management
**Issue:** Secrets in environment variables and files
**Fix:** Integrate with Kubernetes Secrets or HashiCorp Vault
**Effort:** 8-12 hours

---

## Shining Plan (Polish & Finalize)

### Polish #1: Performance Optimization
- [ ] Profile Svalinn gateway (target: <10ms request latency)
- [ ] Profile Vörðr orchestrator (target: <100ms container start)
- [ ] Optimize bunsenite parser (target: <1ms config parse)
**Effort:** 8-12 hours

### Polish #2: Monitoring Dashboard
- [ ] Prometheus metrics from all components
- [ ] Grafana dashboard for lcb-website stack
- [ ] Alerts for critical failures
**Effort:** 6-8 hours

### Polish #3: Developer Experience
- [ ] One-command local deployment (`just dev`)
- [ ] One-command testing (`just test`)
- [ ] One-command production deploy (`just prod`)
**Effort:** 4-6 hours

### Polish #4: End-to-End Tests
- [ ] Test: WordPress page load through full stack
- [ ] Test: Container deployment with verification
- [ ] Test: Network overlay connectivity
- [ ] Test: Consent flow with all components
**Effort:** 12-16 hours

### Polish #5: Documentation Polish
- [ ] Architecture diagrams (all seams visualized)
- [ ] API reference docs (OpenAPI specs)
- [ ] Deployment guide (step-by-step)
- [ ] Troubleshooting guide (common issues)
**Effort:** 8-12 hours

### Polish #6: Release Artifacts
- [ ] Tag v1.0.0 across all repos
- [ ] Generate release notes
- [ ] Create container images for all components
- [ ] Publish to package registries (crates.io, hex.pm, npm)
**Effort:** 4-6 hours

---

## Total Effort Estimates

| Phase | Effort (hours) |
|-------|----------------|
| **Critical Seams** | 48-76 |
| **Medium Seams** | 56-78 |
| **Smoothing** | 32-45 |
| **Sealing** | 29-41 |
| **Shining** | 42-60 |
| **TOTAL** | **207-300 hours** |

**Team of 3:** 7-10 weeks
**Solo:** 5-8 months (part-time)

---

## Recommended Approach

### Week 1-2: Critical Seams (Focus on lcb-website deployment)
1. Complete Cerro Torre Debian importer
2. Test Svalinn → Vörðr pipeline
3. Create WordPress manifest
4. Test container deployment flow

### Week 3-4: Medium Seams (Integration testing)
5. Complete Bunsenite integration
6. Test all network overlays
7. Integrate consent portal
8. Connect feedback pipeline

### Week 5-6: Smoothing & Sealing (Production hardening)
9. Standardize error handling and logging
10. Add security controls (auth, rate limiting)
11. Implement audit logging
12. Secret management

### Week 7-8: Shining (Polish & launch)
13. Performance optimization
14. Monitoring and alerting
15. Documentation completion
16. End-to-end testing
17. v1.0 release

---

## Success Metrics

**Deployment succeeds when:**
- ✅ WordPress loads through full stack (Svalinn → Vörðr → WordPress)
- ✅ All containers verified by Cerro Torre
- ✅ ZeroTier + Twingate overlay functional
- ✅ Consent portal operational
- ✅ Feedback pipeline captures incidents
- ✅ Monitoring dashboard shows all green
- ✅ No critical security vulnerabilities
- ✅ Performance targets met (<100ms p95 latency)

**Production-ready when:**
- ✅ All critical + medium seams functional
- ✅ All smoothing + sealing complete
- ✅ Shining at 80%+ (polish ongoing)
- ✅ Documentation complete
- ✅ v1.0 tagged and released
