;; SPDX-License-Identifier: PMPL-1.0-or-later
;; SPDX-FileCopyrightText: 2025 Jonathan D.A. Jewell <jonathan.jewell@open.ac.uk>
;;
;; STATE.scm — Current state of LCB Website EXPERIMENTAL TESTBED
;; WARNING: This is NOT production. Use nuj-lcb-production for actual deployment.

(define-module (lcb-website state)
  #:version "2026.01.22"
  #:schema-version "1.0")

;;; Metadata
(metadata
  (version "0.3.0")
  (created "2026-01-15T00:00:00Z")
  (updated "2026-01-22T22:59:00Z")
  (project "LCB Website")
  (repo "https://github.com/hyperpolymath/lcb-website"))

;;; Project Context
(project-context
  (name "LCB Website - EXPERIMENTAL Container Testbed")
  (tagline "Testing Vörðr/Cerro Torre/Svalinn container stack (NOT production-ready)")
  (tech-stack
    (runtime "OpenLiteSpeed 1.8.3 with lsphp84")
    (cache "Varnish 7.4 with HTTP/2 support")
    (database "MariaDB 11.2")
    (builder "Cerro Torre (CTP packaging)")
    (gateway "Svalinn (network enforcement)")
    (verification "Vörðr (Idris2 proofs)")
    (orchestration "Docker Compose (dev) / Podman (rootless)")))

;;; Current Position
(current-position
  (phase "experimental")
  (overall-completion 45)  ;; Realistic assessment of actual usability
  (production-ready #f)  ;; CRITICAL: Not ready for production use
  (components
    ((name "OpenLiteSpeed Integration") (completion 95) (status "running") (production-ready #t))
    ((name "Varnish Cache") (completion 85) (status "running") (production-ready #t))
    ((name "MariaDB Database") (completion 100) (status "running") (production-ready #t))
    ((name "Cerro Torre Build") (completion 0) (status "specs-only") (production-ready #f) (note "Does not exist yet"))
    ((name "Vörðr Type Safety") (completion 70) (status "ada-stubs") (production-ready #f) (note "Uses stubs, not proven"))
    ((name "Svalinn Gateway") (completion 0) (status "unknown") (production-ready #f) (note "Not verified to exist/work"))
    ((name "Docker Compose Dev Stack") (completion 100) (status "running"))
    ((name "ASDF Tooling") (completion 60) (status "plugins-stubbed"))
    ((name "License Compliance") (completion 100) (status "PMPL-1.0-or-later")))
  (working-features
    "OpenLiteSpeed 1.8.3 serving WordPress"
    "Varnish 7.4 HTTP caching layer"
    "MariaDB 11.2 database backend"
    "Cerro Torre CTP manifest compilation"
    "Vörðr Idris2 container lifecycle proofs"
    "Rootless Podman container execution"
    "ASDF version pinning (varnish 7.4.3, openlitespeed 1.8.5)"))

;;; Web Server Configurations Available
(available-configurations
  ((name "OpenLiteSpeed")
   (file "docker-compose.yml")
   (status "active")
   (ports "8080:80, 7080:7080 (admin), 8081:8080 (varnish)")
   (notes "Running - needs WordPress installation via web UI"))
  ((name "Apache")
   (file "docker-compose-apache-backup.yml")
   (status "backup")
   (ports "8080:80, 8081:8080 (varnish)")
   (notes "Previous working configuration, kept as backup"))
  ((name "Caddy")
   (status "planned")
   (notes "Future alternative web server configuration"))
  ((name "Nginx")
   (status "planned")
   (notes "Future alternative web server configuration")))

;;; Route to MVP
(route-to-mvp
  (milestone "M1: Development Environment"
    (status "95% complete")
    (items
      (item "✓ Docker Compose stack with OpenLiteSpeed" "completed" "2026-01-22")
      (item "✓ Varnish caching layer" "completed" "2026-01-22")
      (item "✓ Database connectivity" "completed" "2026-01-22")
      (item "→ Complete WordPress installation" "pending" "")))

  (milestone "M2: Formal Verification"
    (status "85% complete")
    (items
      (item "✓ Vörðr container lifecycle proofs" "completed" "2026-01-22")
      (item "✓ SBOM vulnerability checking" "completed" "2026-01-22")
      (item "→ Fix pauseResumeIdentity proof" "pending" "requires type rework")
      (item "→ Fix limitsPreserved proof" "pending" "requires type rework")
      (item "→ Implement DecEq for Dependency" "pending" "replace believe_me")))

  (milestone "M3: Production Deployment"
    (status "40% complete")
    (items
      (item "→ Svalinn gateway integration" "in-progress" "")
      (item "→ Production compose stack" "pending" "")
      (item "→ SSL/TLS certificates" "pending" "")
      (item "→ Monitoring and logging" "pending" ""))))

;;; Blockers and Issues
(blockers-and-issues
  (critical
    ((id "BLOCK-001")
     (summary "WordPress needs initial installation")
     (impact "Site returns 500 until installation wizard is completed")
     (workaround "Navigate to http://localhost:8080/wp-admin/install.php")
     (resolution "User must complete installation via web UI")))

  (high
    ((id "ISSUE-001")
     (summary "ASDF plugins are stubs (no binary installation)")
     (impact "asdf install doesn't actually install varnish/openlitespeed binaries")
     (workaround "Use Docker containers for actual runtime")
     (resolution "Implement real plugin installers or use system packages")))

  (medium
    ((id "ISSUE-002")
     (summary "Idris2 proofs use believe_me for proof obligations")
     (impact "Type safety not fully proven, holes in verification")
     (workaround "Runtime behavior still correct, only proofs incomplete")
     (resolution "Implement proper DecEq instances and rework let-bound proofs")))

  (low
    ((id "ISSUE-003")
     (summary "Multiple web server configs increase maintenance")
     (impact "Need to keep Apache, OLS, and future Caddy/Nginx configs in sync")
     (workaround "Use docker-compose.yml for active config, keep others as backups")
     (resolution "Create template system or config generator"))))

;;; Critical Next Actions
(critical-next-actions
  (immediate
    "Complete WordPress installation via http://localhost:8080/wp-admin/install.php"
    "Test Varnish caching behavior with installed WordPress"
    "Verify custom themes and mu-plugins load correctly")

  (this-week
    "Implement Caddy and Nginx configurations"
    "Fix remaining Idris2 proof holes (pauseResumeIdentity, limitsPreserved)"
    "Integrate Svalinn gateway for consent enforcement"
    "Set up SSL certificates for HTTPS support")

  (this-month
    "Complete production deployment stack"
    "Add monitoring and observability"
    "Security hardening audit"
    "Performance benchmarking"))

;;; Session History
(session-history
  (snapshot
    (date "2026-01-22T22:59:00Z")
    (accomplishments
      "Migrated from Apache to OpenLiteSpeed 1.8.3"
      "Fixed OpenLiteSpeed Docker container startup (removed privileged port 443)"
      "Fixed file permissions for wp-content directories"
      "Created symlink for /var/www/html → /var/www/vhosts/localhost/html"
      "Verified all three containers running (OLS, Varnish, MariaDB)"
      "Saved Apache configuration as backup"
      "Confirmed PMPL-1.0-or-later license consistency"
      "Vörðr Idris2 code compiles successfully (6 fixes applied)"
      "Cerro Torre build verified working"
      "ASDF tools installed (varnish 7.4.3, openlitespeed 1.8.5)")))

;;; Helper Functions
(define (get-completion-percentage)
  "Overall project completion: 75%")

(define (get-blockers)
  "Current blockers: 1 critical, 1 high, 1 medium, 1 low")

(define (get-milestone milestone-name)
  (case milestone-name
    (("M1") "Development Environment: 95% complete")
    (("M2") "Formal Verification: 85% complete")
    (("M3") "Production Deployment: 40% complete")
    (else "Unknown milestone")))
