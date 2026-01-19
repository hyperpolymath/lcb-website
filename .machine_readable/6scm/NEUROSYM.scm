;; SPDX-License-Identifier: AGPL-3.0-or-later
;; NEUROSYM.scm - Neurosymbolic config for lcb-website

(define neurosym-config
  `((version . "1.0.0")
    (symbolic-layer
      ((type . "scheme")
       (reasoning . "policy")
       (verification . "documented")))
    (neural-layer
      ((embeddings . true)
       (fine-tuning . false)))
    (integration . ((auto-docs . true)
                    (traceability . true)))))
