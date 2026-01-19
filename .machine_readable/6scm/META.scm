;; SPDX-License-Identifier: AGPL-3.0-or-later
;; META.scm - Meta-level practices for lcb-website
;; Media-Type: application/meta+scheme

(meta
  (architecture-decisions
    ("Use Cerro Torre .ctp as the canonical deployment manifest"
     "Document automation and consent flows before the UI launches"))

  (development-practices
    (code-style ("AsciiDoc" "Bash" "ReScript"))
    (security
      (principle "Defense in depth"))
    (testing ("just test" "ct verify"))
    (versioning "SemVer")
    (documentation "AsciiDoc"))

  (design-rationale
    ("Keep the repository ready for handover with machine-readable metadata and manifest prototypes.")))
