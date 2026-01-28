# ⚠️ EXPERIMENTAL TESTBED - NOT PRODUCTION

**This repository is for EXPERIMENTS ONLY. Do NOT use for production.**

## What This Repo Is

This is an **experimental testbed** for testing advanced container infrastructure:

- Vörðr (formally verified runtime) - **70% complete, uses Ada stubs**
- Cerro Torre (build system) - **0% complete, specs only**
- Svalinn (network gateway) - **Not verified to work**
- Consent-aware HTTP - **Bleeding edge, HTTP 430 not standardized**
- ZeroTier overlay networking
- Formal verification with Idris2
- eBPF monitoring
- wp-sinople-theme (custom WordPress theme)

## Status: NOT READY FOR PRODUCTION

- **Job requirement:** Use `nuj-lcb-production` instead
- **Actual deployment:** Use `nuj-lcb-production` instead
- **Reliable site:** Use `nuj-lcb-production` instead

## Purpose

This repo exists to:

1. **Test** container infrastructure before it's production-ready
2. **Experiment** with formal verification and security tools
3. **Dogfood** hyperpolymath projects (when they're ready)
4. **Learn** without risking production

## When To Use This Repo

- ✅ You want to test Vörðr/Svalinn/Cerro Torre
- ✅ You're developing container security tooling
- ✅ You're okay with things breaking
- ✅ You understand this is research, not production

## When NOT To Use This Repo

- ❌ You need a working WordPress site
- ❌ You have a deadline
- ❌ Your job depends on it working
- ❌ You want something proven and stable

## The Production Alternative

**For actual NUJ LCB website deployment:**

Use `nuj-lcb-production` repo:
- ✅ Standard WordPress Docker stack
- ✅ Proven, reliable, documented
- ✅ No experimental dependencies
- ✅ Can deploy within days
- ✅ Thousands of tutorials available

## Relationship to Production

```
nuj-lcb-production/  ← Use this for your job
    │
    │ (completely separate)
    │
lcb-website/         ← This repo (experiments only)
    │
    │ (may feed back if experiments succeed)
    │
    └─→ Future: Maybe some components proven and moved to production
```

## Dependencies Status

| Component | Status | Production Ready? |
|-----------|--------|-------------------|
| Vörðr | 70% (Ada stubs) | ❌ No |
| Cerro Torre | 0% (specs only) | ❌ No |
| Svalinn | Unknown | ❌ No |
| Consent-aware HTTP | Experimental | ❌ No |
| wp-sinople-theme | Design needs work | ❌ No |
| Standard WordPress | Proven | ✅ Yes (in nuj-lcb-production) |

## What Gemini Promised vs Reality

See `../nuj-lcb-production/WHAT-GEMINI-SOLD-VS-REALITY.md` for details on what was promised vs what actually exists.

**Summary:** Gemini created an impressive-looking stack that references many components that don't actually work yet.

## License

PMPL-1.0-or-later

## Questions?

- **"Should I use this for production?"** → NO. Use `nuj-lcb-production`
- **"When will this be ready?"** → Unknown. Maybe 6 months, maybe never
- **"Can I test things here?"** → YES! That's what it's for
- **"Will this break?"** → YES! Expect it to break often
