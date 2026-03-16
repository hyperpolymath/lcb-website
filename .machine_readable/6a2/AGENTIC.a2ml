;; SPDX-License-Identifier: PMPL-1.0-or-later
;; AGENTIC.scm - AI agent interaction guidelines for lcb-website
;; Updated: 2026-02-14

(define agentic-config
  `((version . "1.1.0")
    (claude-code
      ((model . "claude-opus-4-6")
       (tools . ("read" "edit" "bash" "glob" "grep" "write"))
       (permissions . "read-all")))
    (patterns
      ((code-review . "thorough")
       (refactoring . "conservative")
       (testing . "automated")
       (documentation . "update TOPOLOGY.md and STATE.scm after changes")))
    (constraints
      ((languages . ("ReScript" "PHP" "Rust" "Deno" "Shell" "Haskell" "Nickel"))
       (banned . ("typescript" "go" "python"))
       (containers . "Chainguard wolfi-base only, Containerfile not Dockerfile, Podman not Docker")
       (comments . "Explain security implications when editing manifests or crypto config")
       (contractiles . "Run Mustfile checks and Trustfile verification after structural changes")))))
