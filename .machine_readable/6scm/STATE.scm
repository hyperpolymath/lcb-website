;; SPDX-License-Identifier: AGPL-3.0-or-later
;; STATE.scm - Project state for lcb-website
;; Media-Type: application/vnd.state+scm

(state
  (metadata
    (version "0.1.0")
    (schema-version "1.0")
    (created "2026-01-19")
    (updated "2026-01-19")
    (project "lcb-website")
    (repo "github.com/hyperpolymath/lcb-website"))

  (project-context
    (name "lcb-website")
    (tagline "Hardened WordPress dogfood built on the verified container stack")
    (tech-stack ("Cerro Torre" "Svalinn" "Vörðr" "Sinople theme" "php-aegis" "consent-aware HTTP")))

  (current-position
    (phase "implementation")
    (overall-completion 65)
    (components ("container baseline" "manifest" "consent docs" "automation" "ci-cd" "docker-compose" "varnish-config"))
    (working-features ("infra/wordpress.ctp" "justfile automation" "GitHub workflows" ".well-known/aibdp.json" "docker-compose.yml" "svalinn-compose.yml" "Varnish consent enforcement")))

  (route-to-mvp
    (milestones
      (("manifest pack/verify" . "ct pack + ct verify with Probate SBOM")
       ("automations" . "feedback-o-tron + hybrid router wired")
       ("consent portal" . "indieweb2 + consent-aware HTTP active")))))

  (blockers-and-issues
    (critical ())
    (high ("DHI WordPress tag not published yet" "Component repos (svalinn, cerro-torre, vordr) need implementation"))
    (medium ("ct binary not built locally" "Checksum TODOs in wordpress.ctp"))
    (low ("Monitoring dashboards need docs" "ASDF varnish/openlitespeed plugins need creation")))

  (critical-next-actions
    (immediate ("Test docker-compose.yml locally" "Fill checksum TODOs in wordpress.ctp"))
    (this-week ("Create stub implementations for svalinn/cerro-torre/vordr" "Set up ASDF plugins for varnish/openlitespeed"))
    (this-month ("Build ct binary (requires alr)" "Wire ZeroTier/Twingate manifests" "Integrate feedback-o-tron")))

  (session-history
    ((date "2026-01-22")
     (accomplishments
       ("Created justfile with validation and build commands"
        "Set up GitHub workflows (CodeQL, security scanning, RSR validation)"
        "Implemented .well-known structure with AIBDP, security.txt, ai.txt"
        "Created docker-compose.yml for development environment"
        "Created svalinn-compose.yml for production verified stack"
        "Implemented Varnish VCL with consent-aware HTTP enforcement"
        "Set up ASDF tooling (.tool-versions)"
        "Created MariaDB configuration"
        "Added .env.example and .gitignore"))
     (next-session "Test compose stack, fill checksums, create component stubs")))))))
