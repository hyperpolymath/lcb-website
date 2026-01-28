# Cryptographic Security Audit - LCB Website Experimental

**Date**: 2026-01-28
**Status**: Gap Analysis vs. Target Specification

## Target Security Specification

This is the **target** cryptographic specification for the experimental lcb-website testbed:

| Category | Algorithm/Standard | NIST/FIPS | Implementation Status |
|----------|-------------------|-----------|----------------------|
| **Password Hashing** | Argon2id (512 MiB, 8 iter, 4 lanes) | — | ❌ NOT IMPLEMENTED |
| **General Hashing** | SHAKE3-512 (512-bit output) | FIPS 202 | ❌ NOT IMPLEMENTED |
| **PQ Signatures** | Dilithium5-AES (hybrid) | ML-DSA-87 (FIPS 204) | ❌ NOT IMPLEMENTED |
| **PQ Key Exchange** | Kyber-1024 + SHAKE256-KDF | ML-KEM-1024 (FIPS 203) | ❌ NOT IMPLEMENTED |
| **Classical Signatures** | Ed448 + Dilithium5 (hybrid) | — | ❌ NOT IMPLEMENTED |
| **Symmetric Encryption** | XChaCha20-Poly1305 (256-bit) | — | ⚠️ PARTIAL (PHP libsodium) |
| **Key Derivation** | HKDF-SHAKE512 | FIPS 202 | ❌ NOT IMPLEMENTED |
| **RNG** | ChaCha20-DRBG (512-bit seed) | SP 800-90Ar1 | ⚠️ PHP `random_bytes()` |
| **User-Friendly Hashes** | Base32(SHAKE256) → Wordlist | — | ❌ NOT IMPLEMENTED |
| **Database Hashing** | BLAKE3 (512-bit) + SHAKE3-512 | — | ❌ NOT IMPLEMENTED |
| **Semantic XML/GraphQL** | Virtuoso (VOS) + SPARQL 1.2 | — | ❌ NOT IMPLEMENTED |
| **VM/Execution** | GraalVM (with formal verification) | — | ❌ N/A (PHP runtime) |
| **Protocol Stack** | QUIC + HTTP/3 + IPv6 only | — | ⚠️ PARTIAL (Varnish HTTP/2) |
| **Accessibility** | WCAG 2.3 AAA + ARIA + Semantic XML | — | ⚠️ PLANNED (not verified) |
| **Fallback Signatures** | SPHINCS+ | — | ❌ NOT IMPLEMENTED |
| **Formal Verification** | Coq/Isabelle (crypto primitives) | — | ⚠️ Vörðr uses Idris2 |

## Current Implementation Status

### ✅ What's Working (Standard Security)

| Component | Current Implementation | Notes |
|-----------|----------------------|-------|
| **TLS** | TLS 1.2/1.3 via Varnish/OpenLiteSpeed | Standard HTTPS |
| **WordPress Hashing** | bcrypt (default) | Not Argon2id yet |
| **PHP Random** | `random_bytes()` (CSPRNG) | Good enough for most use |
| **libsodium** | XChaCha20-Poly1305 available | PHP 7.2+ extension |
| **HTTP/2** | Varnish 7.4 support | Not HTTP/3 yet |
| **IPv6** | Supported but IPv4 not disabled | Dual-stack |

### ⚠️ Partially Implemented

| Component | Status | Gap |
|-----------|--------|-----|
| **Vörðr** | 70% complete (Ada stubs) | Formal proofs incomplete |
| **Cerro Torre** | 0% (specs only) | Doesn't exist yet |
| **Svalinn** | 0% (unknown) | Not verified to work |
| **HTTP/3** | Not deployed | Varnish 7.4 doesn't support QUIC |
| **Accessibility** | Not tested | WCAG 2.3 AAA unverified |

### ❌ Not Implemented (Gap List)

#### Post-Quantum Cryptography
- **Dilithium5-AES** - Not available in PHP ecosystem
- **Kyber-1024** - Not available in PHP ecosystem
- **SPHINCS+** - Not available in PHP ecosystem
- **ML-DSA/ML-KEM** - NIST standards too new (2024)

#### Advanced Hashing
- **SHAKE3-512** - SHA-3 XOF not in PHP yet
- **BLAKE3** - Requires C extension or FFI
- **HKDF-SHAKE512** - Not standardized in PHP

#### Infrastructure
- **GraalVM** - PHP not compatible
- **Virtuoso** - Semantic database not integrated
- **ChaCha20-DRBG** - Specific CSPRNG not exposed in PHP

#### Formal Verification
- **Coq/Isabelle** - Research-level, not practical for web stack

## Feasibility Assessment

### 🟢 Can Implement (with effort)

| Item | How | Effort | Timeline |
|------|-----|--------|----------|
| **Argon2id** | PHP extension (`sodium_crypto_pwhash`) | Low | 1 day |
| **HTTP/3** | Switch to Caddy (has QUIC support) | Medium | 1 week |
| **IPv4 disable** | Server config (NOT RECOMMENDED) | Low | 1 day |
| **BLAKE3** | PHP FFI or C extension | Medium | 2 weeks |
| **Ed448** | OpenSSL 1.1.1+ PHP binding | Medium | 1 week |
| **WCAG 2.3 AAA** | Testing + fixes | High | 4-6 weeks |

### 🟡 Difficult but Possible

| Item | Challenge | Effort | Timeline |
|------|-----------|--------|----------|
| **XChaCha20-Poly1305** | Already in libsodium, needs integration | Medium | 1-2 weeks |
| **Wordlist hashes** | Custom implementation | Medium | 2 weeks |
| **SPARQL integration** | Requires Virtuoso setup | High | 4-8 weeks |

### 🔴 Not Feasible (for PHP/WordPress stack)

| Item | Why Not |
|------|---------|
| **Post-quantum crypto** | No PHP libraries; ecosystem not ready; NIST standards just finalized |
| **GraalVM** | Java-based, incompatible with PHP runtime |
| **ChaCha20-DRBG** | PHP's `random_bytes()` already CSPRNG-quality |
| **Coq/Isabelle** | Academic formal verification, not practical for CMS |
| **SHAKE3** | Bleeding edge; not in OpenSSL/PHP yet |

## Recommended Implementation Plan

### Phase 1: Quick Wins (1-2 weeks)

```bash
# 1. Enable Argon2id (if PHP compiled with libsodium)
# In wp-config.php:
define('WP_PASSWORD_ARGON2', true);

# 2. Test libsodium availability
php -m | grep sodium

# 3. Configure HTTP/3 via Caddy (replaces Varnish)
# Caddyfile with automatic QUIC
```

### Phase 2: Infrastructure Upgrades (1 month)

1. **Replace Varnish with Caddy**
   - Caddy 2.x has native HTTP/3 (QUIC) support
   - Automatic HTTPS, easier config
   - Drop-in replacement for reverse proxy

2. **Implement BLAKE3 hashing**
   - Rust library with PHP FFI bindings
   - Use for content hashing, not passwords

3. **Ed448 signature support**
   - OpenSSL 1.1.1+ has Ed448
   - PHP 7.4+ can use it via `openssl_*` functions

4. **WCAG 2.3 AAA audit**
   - Automated testing (Lighthouse, axe)
   - Manual testing with assistive tech
   - Fix violations

### Phase 3: Advanced Features (2-3 months)

1. **Virtuoso + SPARQL** (if needed)
   - Install Virtuoso Open Source
   - Integrate with WordPress
   - Semantic web queries

2. **Wordlist hash names**
   - Custom PHP implementation
   - Base32(SHAKE256(hash)) → dictionary lookup
   - For human-readable artifact names

3. **XChaCha20-Poly1305 encryption**
   - Use libsodium's `sodium_crypto_aead_xchacha20poly1305_ietf_encrypt()`
   - For database field encryption

### Phase 4: Post-Quantum Readiness (2026+)

**Wait for ecosystem maturity:**
- PHP bindings for liboqs (Open Quantum Safe)
- NIST PQC standards stabilization
- WordPress/PHP community adoption

**Track these projects:**
- **liboqs-php** - PHP bindings for post-quantum crypto
- **NIST PQC** - ML-DSA (Dilithium), ML-KEM (Kyber) finalization
- **OpenSSL 3.2+** - Will include PQC support

## Current Security Posture

### What We Have (Good Enough for Now)

✅ **TLS 1.3** - Strong transport encryption
✅ **bcrypt** - Acceptable password hashing (upgrade to Argon2id planned)
✅ **CSRF protection** - WordPress built-in
✅ **SQL injection protection** - Prepared statements
✅ **XSS protection** - WordPress escaping functions
✅ **Security headers** - Can configure via Caddy
✅ **Regular updates** - WordPress auto-updates

### What We're Missing (vs. spec)

❌ **Post-quantum crypto** - Not critical until quantum computers exist (2030+)
❌ **Formal verification** - Vörðr incomplete, not production-ready
❌ **HTTP/3** - Can upgrade to Caddy
❌ **Advanced hashing** - SHAKE3/BLAKE3 nice-to-have, not critical
❌ **Semantic database** - Virtuoso overkill for CMS

## Risk Assessment

### Threat Timeline

| Threat | Risk Level | Mitigation Timeframe |
|--------|-----------|---------------------|
| **Classical attacks** (XSS, SQLi, etc.) | HIGH | NOW (standard hardening) |
| **Weak passwords** | MEDIUM | NOW (Argon2id upgrade) |
| **HTTP/2 limitations** | LOW | 3-6 months (Caddy upgrade) |
| **Quantum computers** | VERY LOW | 2030+ (monitor NIST PQC) |

### Recommendation

**Focus on practical security NOW:**

1. ✅ Standard WordPress hardening
2. ✅ Argon2id password hashing
3. ✅ HTTP/3 via Caddy
4. ✅ Security monitoring
5. ✅ Regular patching

**Defer until needed:**

- ⏸️ Post-quantum crypto (wait for PHP ecosystem)
- ⏸️ Formal verification (Vörðr not ready)
- ⏸️ Semantic database (not needed for CMS)

## Next Steps

### Immediate Actions (This Week)

1. **Test PHP libsodium availability**
   ```bash
   php -i | grep -i sodium
   php -i | grep -i argon2
   ```

2. **Enable Argon2id if available**
   ```php
   // wp-config.php
   define('WP_PASSWORD_ARGON2', true);
   ```

3. **Plan Caddy migration** (Varnish → Caddy for HTTP/3)

### Short-Term (1 Month)

4. **Deploy Caddy with HTTP/3**
5. **WCAG 2.3 AAA audit**
6. **Security header configuration**

### Long-Term (6-12 Months)

7. **Monitor post-quantum crypto ecosystem**
8. **Test Vörðr when 100% complete**
9. **Evaluate BLAKE3 integration**

## Questions?

- **Is post-quantum crypto urgent?** No. Quantum computers capable of breaking RSA/ECDSA don't exist yet (2030+ estimate).
- **Should we disable IPv4?** No. Breaks accessibility for ~30% of users.
- **Is formal verification necessary?** Not for web CMS. Useful for critical systems (medical, aviation).
- **What about WCAG 2.3 AAA?** This IS achievable and important. Should be priority.

---

**Status**: ❌ 15% of spec implemented
**Realistic Target**: 🟡 60% of spec achievable (dropping PQC, formal verification)
**Timeline**: 3-6 months for practical 60% implementation

**Next Review**: 2026-03-01
