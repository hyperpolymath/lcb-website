;; SPDX-License-Identifier: PMPL-1.0-or-later
;; NEUROSYM.scm - Neurosymbolic config for lcb-website
;; Updated: 2026-02-14

(define neurosym-config
  `((version . "1.1.0")
    (symbolic-layer
      ((type . "scheme")
       (reasoning . "policy + consent enforcement")
       (verification . "contractiles (must/trust/dust/lust)")
       (facts . ("Mustfile invariant checks" "Trustfile crypto verification" "AIBDP consent policy"))))
    (neural-layer
      ((embeddings . true)
       (fine-tuning . false)
       (model-interaction . "Claude Opus via .bot_directives and AGENTIC.scm")))
    (integration
      ((auto-docs . true)
       (traceability . true)
       (topology-map . "TOPOLOGY.md")
       (hypatia-scan . true)
       (gitbot-fleet . true)))))
