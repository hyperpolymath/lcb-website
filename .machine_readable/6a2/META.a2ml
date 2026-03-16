;; SPDX-License-Identifier: PMPL-1.0-or-later
;; META.scm - Meta-level practices for lcb-website
;; Media-Type: application/meta+scheme
;; Updated: 2026-02-14

(meta
  (architecture-decisions
    ("Dual deployment path: Verpex cPanel (production now) + stapeln containers (future)"
     "Use Cerro Torre .ctp as the canonical container manifest with Ed25519 + Dilithium5 signatures"
     "Chainguard wolfi-base for all container images — never Docker Hub debian/ubuntu"
     "Contractiles framework (must/trust/dust/lust/k9) for operational guarantees"
     "TOPOLOGY.md as single-file visual architecture map for human + AI consumption"
     "Document automation and consent flows before the UI launches"
     "Full crypto spec embedded in Trustfile.hs per user-security-requirements"))

  (development-practices
    (code-style ("AsciiDoc" "Bash" "ReScript" "PHP" "Haskell" "YAML" "Nickel"))
    (security
      (principle "Defense in depth")
      (crypto-spec "contractiles/trust/Trustfile.hs")
      (requirements "SHAKE3-512 hashing, Dilithium5+Ed448 hybrid sigs, Kyber-1024 KEM, XChaCha20-Poly1305"))
    (testing ("just test" "just validate" "ct verify" "runhaskell contractiles/trust/Trustfile.hs"))
    (versioning "SemVer")
    (documentation "AsciiDoc for deep docs, Markdown for guides, TOPOLOGY.md for architecture"))

  (design-rationale
    ("Keep the repository ready for handover with machine-readable metadata, manifest prototypes, and contractiles."
     "Separate production deployment (Verpex/cPanel) from container path (stapeln) to ship the site now while building towards verified containers."
     "Consent-aware HTTP enforced at every layer: robots.txt, .htaccess, PHP theme, svalinn gateway.")))
