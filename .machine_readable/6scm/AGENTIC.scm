;; SPDX-License-Identifier: PMPL-1.0-or-later
;; AGENTIC.scm - AI agent interaction guidelines for lcb-website

(define agentic-config
  `((version . "1.0.0")
    (claude-code
      ((model . "claude-opus-4-5-20251101")
       (tools . ("read" "edit" "bash" "rg" "perl" "python"))
       (permissions . "read-all")))
    (patterns
      ((code-review . "thorough")
       (refactoring . "conservative")
       (testing . "automated")))
    (constraints
      ((languages . ("ReScript" "Dirty" "Rust" "Deno" "Shell"))
       (banned . ("typescript" "go" "python"))
       (comments . "Explain security implications when editing manifests")))))
