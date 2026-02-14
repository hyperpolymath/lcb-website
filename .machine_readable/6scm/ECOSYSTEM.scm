;; SPDX-License-Identifier: PMPL-1.0-or-later
;; ECOSYSTEM.scm - Ecosystem map for lcb-website
;; Media-Type: application/vnd.ecosystem+scm
;; Updated: 2026-02-14

(ecosystem
  (version "1.1")
  (name "lcb-website")
  (type "hardened-wordpress-deployment")
  (purpose "Production NUJ London Central Branch website with hardened WordPress, consent-aware HTTP, and a parallel stapeln container build path.")

  (position-in-ecosystem
    (category "web-security")
    (subcategory "hardened WordPress + consent stack + verified containers")
    (unique-value "Real-world dogfood of Svalinn/Cerro Torre/Vörðr with documented consent automation, shipping on Verpex cPanel while building verified container path"))

  (related-projects
    ;; Container stack
    ("svalinn" "cerro-torre" "vordr" "selur"
    ;; Consent and security
     "consent-aware-http" "well-known-ecosystem" "sanctify-php" "http-capability-gateway"
    ;; Automation
     "feedback-o-tron" "gitbot-fleet" "hypatia" "robot-repo-automaton"
    ;; Networking
     "twingate-helm-deploy" "zerotier-k8s-link"
    ;; Data
     "verisimdb" "vql" "vql-dt"
    ;; Frontend
     "cadre-tea-router" "rescript-dom-mounter"
    ;; Security scanning
     "panic-attacker" "echidna" "proven"))

  (what-this-is
    ("The production website repo for NUJ London Central Branch (nuj-lcb.org.uk). Contains WordPress theme (Sinople), page content, deployment guides, security docs, contractiles, templates, and the full stapeln container pipeline (Containerfile + .ctp manifest + selur-compose)."))

  (what-this-is-not
    ("Not yet deployed to production — Phase 1 (repo preparation) is complete, Phase 2 (Verpex deployment) is next. The container path (stapeln) is a parallel future target, not the immediate deployment mechanism.")))
