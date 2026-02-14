ctp_version = "1.0"

[metadata]
name = "lcb-wordpress"
version = "6.9.0"
revision = 1
kind = "container_image"
summary = "Hardened LCB WordPress runtime (Sinople + php-aegis)"
description = "Chainguard wolfi-base WordPress package, extended with the Sinople theme, php-aegis helpers, consent-aware HTTP/.well-known assets, and automation hooks."
license = "PMPL-1.0-or-later"
homepage = "https://github.com/hyperpolymath/lcb-website"
maintainer = "lcb-site:ops"
keywords = ["wordpress", "hardened", "sinople", "lcb", "chainguard"]

[upstream]
family = "chainguard"
section = "wolfi-base"
snapshot_service = "cgr.dev"
snapshot_timestamp = 2026-02-14T00:00:00Z

[provenance]
import_date = 2026-02-14T00:00:00Z
sbom = "sbom/spdx/lcb-wordpress.sbom.spdx.json"
provenance_log = "in_toto/lcb-wordpress.provenance.jsonl"

[security]
suite_id = "LCB-WP-2"
payload_binding = "manifest.canonical_bytes_sha256"

[[security.algorithms.hash]]
id = "sha256"
output_bits = 256

[[security.algorithms.hash]]
id = "shake3-512"
output_bits = 512
comment = "Target hash algorithm per user-security-requirements (FIPS 202). Migrate from sha256 when tooling supports it."

[[security.algorithms.signatures]]
id = "ed25519"
required = true

[[security.algorithms.signatures]]
id = "dilithium5-aes"
required = false
comment = "ML-DSA-87 (FIPS 204) hybrid PQ signature. Required when cerro-torre adds PQ support. SPHINCS+ as fallback."

# ==========================================================================
# Inputs
# ==========================================================================

[[inputs.sources]]
id = "wordpress-base"
type = "oci_image"
name = "wolfi-base"
version = "latest"
comment = "Chainguard wolfi-base with PHP 8.4 runtime. WordPress 6.9 installed during build."

[[inputs.sources.artifacts]]
filename = "cgr.dev-chainguard-wolfi-base-latest.oci"
uri = "oci://cgr.dev/chainguard/wolfi-base:latest"
sha256 = "rolling"
comment = "Chainguard images use rolling tags with signed provenance. Verify via cosign: cosign verify cgr.dev/chainguard/wolfi-base:latest"

[[inputs.sources]]
id = "sinople-theme"
type = "local_directory"
name = "wp-sinople-theme"
version = "0.1.0"

[[inputs.sources.artifacts]]
filename = "wp-sinople-theme"
uri = "file://wp-content/themes/sinople"
sha256 = "b358183f45f3232b0f2bf742a5ef3c033347a77fd05c0e6eee0684ddc68b4737"

[[inputs.sources]]
id = "php-aegis-mu"
type = "local_directory"
name = "php-aegis"
version = "1.0.0"

[[inputs.sources.artifacts]]
filename = "php-aegis-mu"
uri = "file://wp-content/mu-plugins"
sha256 = "35017a37f886ef9a139030a57d4dbbab975cd0f0e57afb2e84d961ffef5d019d"

[[inputs.sources]]
id = "well-known-assets"
type = "local_directory"
name = "well-known"
version = "2026-02-14"

[[inputs.sources.artifacts]]
filename = "well-known"
uri = "file://.well-known"
sha256 = "e078a2bd2b181e1e8c31a8982e74aefa7f2d828aa233150103edf4b18ca68231"
comment = "Recalculate after updating security.txt expiry"

# ==========================================================================
# Build
# ==========================================================================

[build]
system = "cerro_image"

[[build.plan]]
step = "import"
using = "oci_image"
sources = ["wordpress-base"]

[[build.plan]]
step = "overlay"
sources = ["sinople-theme", "php-aegis-mu", "well-known-assets"]
description = "Copy the Sinople theme assets, php-aegis mu-plugin, and .well-known definitions into /var/www/html/wp-content and /var/www/html/.well-known before finalizing the image."

[[build.plan]]
step = "assemble_rootfs"

[[build.plan]]
step = "emit_oci_image"

[build.plan.image]
entrypoint = ["/usr/local/lsws/bin/lswsctrl", "start"]
cmd = []
labels = {
  "org.opencontainers.image.title" = "LCB WordPress with LiteSpeed",
  "org.opencontainers.image.description" = "Hardened WordPress 6.9 runtime with Sinople theme, php-aegis, and consent-aware HTTP",
  "org.opencontainers.image.source" = "https://github.com/hyperpolymath/lcb-website",
  "org.opencontainers.image.version" = "6.9.0",
  "org.opencontainers.image.licenses" = "PMPL-1.0-or-later"
}

# ==========================================================================
# Outputs
# ==========================================================================

[outputs]
primary = "lcb-wordpress"

[[outputs.artifacts]]
type = "oci_image"
name = "lcb-wordpress"
tag = "6.9.0"

[[outputs.artifacts]]
type = "sbom_spdx_json"
name = "lcb-wordpress.sbom"

[[outputs.artifacts]]
type = "in_toto_provenance"
name = "lcb-wordpress.provenance"

# ==========================================================================
# Policy
# ==========================================================================

[policy.provenance]
require_source_hashes = true
require_reproducible_build = true

[policy.attestations]
emit = ["in_toto", "sbom_spdx_json", "source-signature"]

[policy.signatures]
require_ed25519 = true
require_dilithium5 = false
comment = "Dilithium5-AES (ML-DSA-87) required when cerro-torre gains PQ support. SPHINCS+ as conservative backup."

[attestations]
require = ["source-signature", "reproducible-build", "sbom-complete"]
recommend = ["security-audit", "pq-signature"]

documentation = "See docs/hardened-wordpress.adoc and infra/wordpress.ctp for how to build, sign, and verify this manifest."
