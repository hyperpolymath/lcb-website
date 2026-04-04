# TEST-NEEDS.md — lcb-website

## CRG Grade: C — ACHIEVED 2026-04-04

> Generated 2026-03-29 by punishing audit.

## Current State

| Category     | Count | Notes |
|-------------|-------|-------|
| Unit tests   | 0     | None |
| Integration  | 0     | None |
| E2E          | 0     | None |
| Benchmarks   | 0     | None |

**Source modules:** ~51 PHP files in wp-content/themes/sinople/ (functions.php, template files, inc/ modules: accessibility, customizer, indieweb, security, widgets, variants, template-tags, walker-nav) + 1 mu-plugin (php-aegis-loader). Custom WordPress theme with real logic.

## What's Missing

### P2P (Property-Based) Tests
- [ ] Input sanitization: property tests for all user-facing inputs
- [ ] Template tag output: property tests for HTML output well-formedness

### E2E Tests
- [ ] Page load: every template renders without PHP errors
- [ ] IndieWeb: webmention send/receive, micropub endpoint
- [ ] Accessibility: WCAG compliance validation
- [ ] Security headers: CSP, HSTS, X-Frame-Options present and correct
- [ ] Customizer: each customizer option produces correct output

### Aspect Tests
- **Security:** security.php exists but has ZERO tests — CSP bypass, header injection, XSS in theme output, php-aegis-loader integration
- **Performance:** No page load time benchmarks, no database query count monitoring
- **Concurrency:** N/A (WordPress handles this)
- **Error handling:** No 404 template tests, no error page validation, no missing widget handling

### Build & Execution
- [ ] PHP lint for all theme files
- [ ] PHPUnit or Pest test runner setup
- [ ] Container-based WordPress test environment
- [ ] Accessibility audit (axe-core or pa11y)

### Benchmarks Needed
- [ ] Page generation time per template
- [ ] IndieWeb endpoint response time
- [ ] Asset loading budget compliance

### Self-Tests
- [ ] Theme integrity check (all required WordPress files present)
- [ ] Security header presence verification
- [ ] IndieWeb endpoint discovery validation

## Priority

**HIGH.** 51 PHP source files with ZERO tests. A public-facing website with a custom security module (security.php) and IndieWeb integration that has never been tested is a liability. The security module alone justifies immediate test creation.

## FAKE-FUZZ ALERT

- `tests/fuzz/placeholder.txt` is a scorecard placeholder inherited from rsr-template-repo — it does NOT provide real fuzz testing
- Replace with an actual fuzz harness (see rsr-template-repo/tests/fuzz/README.adoc) or remove the file
- Priority: P2 — creates false impression of fuzz coverage

## Session 9 additions (2026-04-04)

### What Was Added

| Area | Tests Added | Location |
|------|-------------|----------|
| CI runner | GitHub Actions workflow for existing test suite (unit, benchmarks) | `.github/workflows/e2e.yml` |

### Updated Test Counts

| Suite | Count | Status |
|-------|-------|--------|
| CI workflows | 21 | Running e2e suite |
