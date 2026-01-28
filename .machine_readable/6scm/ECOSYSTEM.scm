;; SPDX-License-Identifier: PMPL-1.0-or-later
;; ECOSYSTEM.scm - Ecosystem map for lcb-website
;; Media-Type: application/vnd.ecosystem+scm

(ecosystem
  (version "1.0")
  (name "lcb-website")
  (type "verified-container deployment")
  (purpose "Document and orchestrate the hardened LCB WordPress deployment.")

  (position-in-ecosystem
    (category "web-security")
    (subcategory "hardened WordPress + consent stack")
    (unique-value "dogfooding Svalinn/Cerro Torre/Vörðr with documented consent automation"))

  (related-projects ("svalinn" "cerro-torre" "vordr" "consent-aware-http" "feedback-o-tron" "twingate-helm-deploy" "zerotier-k8s-link" "well-known-ecosystem"))

  (what-this-is ("A coordinating repo for the hardened LCB site, linking container manifests, consent policy docs, automation instructions, and machine-readable metadata."))

  (what-this-is-not ("A production release; it is a handover/prototype example waiting for upstream manifests and automation gluing.")))
