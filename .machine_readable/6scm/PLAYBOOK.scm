;; SPDX-License-Identifier: AGPL-3.0-or-later
;; PLAYBOOK.scm - Operational playbook for lcb-website

(define playbook
  `((version . "1.0.0")
    (procedures
      ((deploy
         (("build" . "just build")
          ("document" . "just docs")
          ("package" . "ct pack infra/wordpress.ctp")
          ("verify" . "ct verify infra/output/lcb-wordpress.ocibundle")))
       (automation
         (("feedback" . "feedback-o-tron submit_feedback")
          ("consent" . "consent-aware-http validate")))
       (debug . ("just logs"))))
    (alerts . (("manifest" . "ct verify")))
    (contacts . (("ops" . "devops@hyperpolymath")))))
