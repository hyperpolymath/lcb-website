# lcb-website Deployment Blockers and Dependencies

**Last Updated:** 2026-01-22
**Overall Readiness:** 65% (Local dev ready, production needs integration)

## Executive Summary

- **Local Development:** ✅ Ready (Docker Compose works)
- **Cerro Torre Verification:** ⚠️ 90% (needs manifest creation)
- **Full Hardened Stack:** ⚠️ 60-70% (needs integration testing)
- **Production Deployment:** ❌ Blocked by external dependencies

---

## Critical Blockers

### 1. External Dependencies (CRITICAL)

| Dependency | Status | Impact | Owner |
|------------|--------|--------|-------|
| **Debian Hardened Images (DHI) WordPress base** | ❌ Unpublished | Cannot use verified base image | External |
| **Twingate Account** | ⚠️ Needs provisioning | Cannot test SDP mesh | lcb-website team |
| **Live Kubernetes Cluster** | ⚠️ Needs setup | Cannot deploy networked stack | lcb-website team |

**Resolution Path:**
1. DHI WordPress: Monitor https://github.com/debian-hardened-images or use temporary base
2. Twingate: Register account at https://www.twingate.com/
3. K8s Cluster: Set up local k3s or use cloud provider

### 2. Cerro Torre Integration (HIGH)

| Item | Status | Completion | Blocker |
|------|--------|------------|---------|
| Cerro Torre core | ✅ Complete | 90% | None |
| WordPress manifest (`infra/wordpress.ctp`) | ❌ Not created | 0% | Needs DHI base image |
| Debian importer | ⚠️ Partial | 60% | 40% implementation remaining |
| SBOM/in-toto artifacts | ❌ Not generated | 0% | Needs manifest first |

**Resolution Path:**
1. Complete Debian importer (see task 2 below)
2. Create `infra/wordpress.ctp` manifest with current WordPress base
3. Generate SBOM with `ct pack`
4. Update manifest when DHI base becomes available

---

## Dependency Repo Completion Status

### Tier 1: Core Infrastructure (READY - 85-90%)

| Repo | Completion | Status | Notes |
|------|------------|--------|-------|
| **cerro-torre** | 90% | ✅ Ready | HTTP client, OCI export, SELinux all working |
| **svalinn** | 100% | ✅ Complete | Edge gateway, auth, policy engine |
| **vordr** | 90% | ✅ Ready | Orchestrator complete, Idris2 proofs in dev |
| **zerotier-k8s-link** | 90% | ✅ Ready | DaemonSet, scripts, automation |
| **twingate-helm-deploy** | 85% | ✅ Ready | Helm chart ready, needs live cluster test |
| **hybrid-automation-router** | 90% | ✅ Ready | 1077 Elixir files, mature codebase |
| **vext** | 85% | ✅ Ready | IRC daemon complete, needs integration |
| **feedback-o-tron** | 85% | ✅ Ready | MCP server working, 7 Elixir files |

**Blockers:** Live cluster + Twingate account for testing

### Tier 2: Application Layer (PARTIAL - 60-75%)

| Repo | Completion | Status | Notes |
|------|------------|--------|-------|
| **bunsenite** | 75% | ⚠️ Partial | Rust core done, ReScript bindings need work |
| **cadre-router** | 75% | ⚠️ Partial | Routing works, needs WordPress integration |
| **indieweb2-bastion** | 70% | ⚠️ Partial | Consent portal working, GraphQL DNS incomplete |
| **robot-repo-automaton** | 70% | ⚠️ Partial | Execution layer works, gating rules need work |
| **php-aegis** | 65% | ⚠️ Partial | Security utils working, WordPress integration needed |

**Blockers:** Integration testing with lcb-website stack

### Tier 3: WordPress Layer (INCOMPLETE - 40-60%)

| Repo | Completion | Status | Notes |
|------|------------|--------|-------|
| **wp-sinople-theme** | 60% | ⚠️ Incomplete | PHP structure done, WASM/ReScript partial |
| **sanctify-php** | 40% | ⚠️ Incomplete | Transformer foundation only |

**Blockers:**
- wp-sinople-theme: WASM build process, ReScript components
- sanctify-php: Core transformer incomplete

### Tier 4: Planned/Early (NOT READY - 30%)

| Repo | Completion | Status | Notes |
|------|------------|--------|-------|
| **http-capability-gateway** | 30% | ❌ Not ready | Design phase only, no implementation |

**Blockers:** Architecture not finalized, implementation not started

---

## Integration Dependencies

### Required for Minimal Deployment

1. ✅ **Docker Compose stack** - WordPress/MariaDB/Varnish working
2. ✅ **Svalinn gateway** - Complete and functional
3. ✅ **Vörðr runtime** - Core complete
4. ⚠️ **Cerro Torre manifest** - Needs creation
5. ⚠️ **wp-sinople-theme build** - WASM/ReScript incomplete

### Required for Hardened Stack

6. ⚠️ **ZeroTier overlay** - Ready but needs cluster
7. ⚠️ **Twingate SDP** - Ready but needs account + cluster
8. ⚠️ **IndieWeb2 consent portal** - 70% complete
9. ⚠️ **feedback-o-tron pipeline** - Ready but needs integration
10. ⚠️ **hybrid-automation-router** - Ready but needs integration
11. ❌ **http-capability-gateway** - Not implemented
12. ⚠️ **bunsenite Nickel parsing** - 75% complete
13. ⚠️ **sanctify-php hardening** - 40% complete
14. ⚠️ **php-aegis security** - 65% complete

---

## Timeline Estimates

### Phase 1: Minimal Viable (1-2 weeks)
- Complete Cerro Torre Debian importer
- Create WordPress manifest
- Build wp-sinople-theme (basic version)
- Test Svalinn → Vörðr → WordPress flow

### Phase 2: Network Integration (2-3 weeks)
- Set up K8s cluster (local k3s)
- Deploy ZeroTier DaemonSet
- Test overlay networking
- Integrate vext IRC notifications

### Phase 3: Hardened Stack (4-6 weeks)
- Complete wp-sinople-theme WASM/ReScript
- Finish sanctify-php transformer
- Integrate php-aegis security
- Complete IndieWeb2 consent portal
- Implement http-capability-gateway
- Get Twingate account and test SDP

### Phase 4: Production Ready (6-8 weeks)
- Wait for DHI WordPress base
- Switch manifest to DHI base
- End-to-end security audit
- Performance testing
- Documentation completion

---

## Immediate Action Items (This Week)

1. **Finish Cerro Torre Debian importer** (40% remaining)
   - Complete Parse_Dsc validation
   - Implement Import_From_Apt_Source
   - Test with actual .dsc files

2. **Create WordPress manifest** (`infra/wordpress.ctp`)
   - Define WordPress container structure
   - List all dependencies
   - Generate with `ct pack`

3. **Test Svalinn/Vörðr pipeline**
   - Deploy both locally
   - Send test container operation
   - Verify delegation works

4. **Fix wp-sinople-theme WASM build**
   - Complete ReScript components
   - Fix Rust WASM compilation
   - Document build process

5. **Complete sanctify-php transformer**
   - Finish core transformation engine
   - Implement safety rules
   - Test with WordPress code

---

## Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| DHI base never published | Medium | High | Use alternative hardened base (Alpine/Wolfi) |
| Integration testing reveals incompatibilities | High | Medium | Allocate 2 weeks for fixes |
| wp-sinople-theme WASM issues | Medium | Medium | Fall back to pure PHP/JS version |
| sanctify-php incomplete | Medium | Low | Use php-aegis only, defer sanctify |
| http-capability-gateway delays | Low | Medium | Defer to Phase 4, use Svalinn policies |

---

## Success Criteria

### Minimal Deployment ✅ When:
- [x] Docker Compose stack running
- [ ] Cerro Torre manifest created and verified
- [ ] Svalinn → Vörðr pipeline tested
- [ ] Basic WordPress theme working

### Hardened Stack ✅ When:
- [ ] ZeroTier overlay functional
- [ ] IndieWeb2 consent portal integrated
- [ ] All Tier 1 repos tested together
- [ ] Security audit passed

### Production Ready ✅ When:
- [ ] DHI WordPress base in use
- [ ] Twingate SDP tested
- [ ] All 13 repos integrated
- [ ] Load testing passed
- [ ] Documentation complete

---

## Contact / Escalation

**For DHI WordPress base:** Monitor https://github.com/debian-hardened-images
**For Twingate access:** https://www.twingate.com/contact
**For integration issues:** Review individual repo STATE.scm files
