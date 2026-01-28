;; SPDX-License-Identifier: PMPL-1.0-or-later
;; STATE.scm - Project state for lcb-website
;; Media-Type: application/vnd.state+scm

(state
  (metadata
    (version "0.1.0")
    (schema-version "1.0")
    (created "2026-01-19")
    (updated "2026-01-22")
    (project "lcb-website")
    (repo "github.com/hyperpolymath/lcb-website"))

  (project-context
    (name "lcb-website")
    (tagline "Hardened WordPress dogfood built on the verified container stack")
    (tech-stack ("Cerro Torre" "Svalinn" "Vörðr" "Sinople theme" "php-aegis" "consent-aware HTTP")))

  (current-position
    (phase "implementation")
    (overall-completion 72)
    (components ("container baseline" "manifest" "consent docs" "automation" "ci-cd" "docker-compose" "varnish-config" "asdf-tooling"))
    (working-features ("infra/wordpress.ctp with checksums" "justfile automation" "GitHub workflows" ".well-known/aibdp.json" "docker-compose.yml tested locally" "svalinn-compose.yml" "Varnish consent enforcement" "ASDF varnish+openlitespeed plugins")))

  (route-to-mvp
    (milestones
      (("manifest pack/verify" . "ct pack + ct verify with Probate SBOM")
       ("automations" . "feedback-o-tron + hybrid router wired")
       ("consent portal" . "indieweb2 + consent-aware HTTP active")))))

  (blockers-and-issues
    (critical ())
    (high ("DHI WordPress tag not published yet" "Alire index version mismatch prevents ct binary build"))
    (medium ("feedback-o-tron MCP integration needed" "ZeroTier/Twingate manifests need creation"))
    (low ("Monitoring dashboards need docs" "Varnish container rootless port binding")))

  (critical-next-actions
    (immediate ("Fix Alire index version for ct build" "Fix Varnish container port binding"))
    (this-week ("Wire feedback-o-tron MCP integration" "Create ZeroTier/Twingate K8s manifests"))
    (this-month ("Deploy test stack on Eclipse drive" "Set up OpenLiteSpeed + Varnish via ASDF" "Integrate indieweb2-bastion")))

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
     (next-session "Test compose stack, fill checksums, create component stubs"))
    ((date "2026-01-22-evening")
     (accomplishments
       ("Tested docker-compose.yml locally (WordPress + MariaDB working)"
        "Fixed docker-compose.yml for podman compatibility (fully qualified image names, SELinux contexts)"
        "Filled all checksum TODOs in wordpress.ctp (wp-sinople-theme, php-aegis, .well-known)"
        "Verified svalinn/cerro-torre/vordr repos have substantial implementations"
        "Set up ASDF plugins for varnish and openlitespeed"
        "Attempted cerro-torre ct binary build (blocked by Alire index version 1.4.0 vs 1.3.0 mismatch)"
        "Identified ZeroTier/Twingate repos as template stubs needing implementation"
        "Reviewed feedback-o-tron Elixir MCP integration requirements"))
     (next-session "Fix Alire for ct build, wire feedback-o-tron MCP, create ZeroTier manifests")))))))
