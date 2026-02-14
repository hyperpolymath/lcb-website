;; SPDX-License-Identifier: PMPL-1.0-or-later
;; PLAYBOOK.scm - Operational playbook for lcb-website
;; Updated: 2026-02-14

(define playbook
  `((version . "1.1.0")
    (procedures
      ((validate
         (("mustfile" . "bash -c 'cd /var/mnt/eclipse/repos/lcb-website && for check in $(yq -r \".checks[].run\" contractiles/must/Mustfile); do eval \"$check\"; done'")
          ("trustfile" . "runhaskell contractiles/trust/Trustfile.hs")
          ("topology" . "test -f TOPOLOGY.md")))
       (deploy-verpex
         (("export" . "./export-for-verpex.sh")
          ("guide" . "Follow VERPEX-DEPLOYMENT.md step-by-step")
          ("verify" . "Check securityheaders.com and ssllabs.com")))
       (deploy-stapeln
         (("build" . "podman build -f Containerfile -t lcb-wordpress:6.9.0 .")
          ("sign" . "cerro-torre sign lcb-wordpress:6.9.0")
          ("seal" . "selur seal lcb-wordpress:6.9.0")
          ("deploy" . "selur-compose up -d")
          ("verify" . "ct verify infra/output/lcb-wordpress.ocibundle")))
       (dev-local
         (("start" . "docker compose up -d")
          ("stop" . "docker compose down")
          ("test" . "just test")))
       (automation
         (("feedback" . "feedback-o-tron submit_feedback")
          ("consent" . "consent-aware-http validate")
          ("scan" . "panic-attack assail . --output /tmp/lcb-scan.json")))
       (recovery
         (("docs" . "See contractiles/dust/Dustfile for full recovery procedures")
          ("db-restore" . "wp db import wp-content/updraft/latest-backup.sql")
          ("theme-fallback" . "wp theme activate twentytwentyfour")
          ("plugin-emergency" . "wp plugin deactivate --all")))))
    (alerts . (("manifest" . "ct verify")
               ("security" . "Wordfence email alerts")
               ("uptime" . "UptimeRobot")))
    (contacts . (("ops" . "lcb-site:ops")
                 ("security" . "security@hyperpolymath.org")))))
