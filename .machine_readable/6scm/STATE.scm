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
    (overall-completion 35)
    (components ("container baseline" "manifest" "consent docs" "automation"))
    (working-features ("infra/wordpress.ctp" "ASDF toolchain" "ZeroTier/Twingate notes")))

  (route-to-mvp
    (milestones
      (("manifest pack/verify" . "ct pack + ct verify with Probate SBOM")
       ("automations" . "feedback-o-tron + hybrid router wired")
       ("consent portal" . "indieweb2 + consent-aware HTTP active")))))

  (blockers-and-issues
    (critical ())
    (high ("DHI WordPress tag not published yet"))
    (medium ("ct binary not built locally"))
    (low ("Monitoring dashboards need docs")))

  (critical-next-actions
    (immediate ("Build ct (requires alr)" "Capture SBOM/in-toto outputs"))
    (this-week ("Wire ZeroTier/Twingate manifests" "Refresh well-known assets"))
    (this-month ("Formalize automation hooks" "Hand over manifest to CT")))

  (session-history ()))))
