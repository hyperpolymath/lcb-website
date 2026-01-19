ctp_version = "1.0"

[metadata]
name = "lcb-wordpress"
version = "6.9.0"
revision = 0
kind = "container_image"
summary = "Hardened LCB WordPress runtime (Sinople + php-aegis)"
description = "Debian-based Hardened Images WordPress package, extended with the Sinople theme, php-aegis helpers, consent-aware HTTP/.well-known assets, and automation hooks."
license = "MPL-2.0 OR LicenseRef-Palimpsest-0.4"
homepage = "https://github.com/hyperpolymath/lcb-website"
maintainer = "lcb-site:ops"
keywords = ["wordpress", "hardened", "sinople", "lcb"]

[upstream]
family = "dhi.io"
section = "wordpress"
snapshot_service = "dhi.io/hardened-images"
snapshot_timestamp = 2026-01-09T00:00:00Z

[provenance]
import_date = 2026-01-10T00:00:00Z
sbom = "sbom/spdx/lcb-wordpress.sbom.spdx.json"
provenance_log = "in_toto/lcb-wordpress.provenance.jsonl"

[security]
suite_id = "LCB-WP-1"
payload_binding = "manifest.canonical_bytes_sha256"

[[security.algorithms.hash]]
id = "sha256"
output_bits = 256

[[security.algorithms.signatures]]
id = "ed25519"
required = true

[[inputs.sources]]
id = "wordpress-dhi"
type = "oci_image"
name = "wordpress"
version = "6.9-php8.5"

[[inputs.sources.artifacts]]
filename = "dhi.io-wordpress-6.9-php8.5.oci"
uri = "oci://dhi.io/wordpress:6.9-php8.5"
sha256 = "TODO: fill in digest from DHI catalog"

[[inputs.sources]]
id = "sinople-theme"
type = "local_directory"
name = "wp-sinople-theme"
version = "0.1.0"

[[inputs.sources.artifacts]]
filename = "wp-sinople-theme"
uri = "file://../wp-sinople-theme/wordpress"
sha256 = "TODO: record checksum of theme tree"

[[inputs.sources]]
id = "php-aegis-mu"
type = "local_directory"
name = "php-aegis"
version = "1.0.0"

[[inputs.sources.artifacts]]
filename = "php-aegis-mu"
uri = "file://../php-aegis/src"
sha256 = "TODO: record checksum of php-aegis helper files"

[[inputs.sources]]
id = "well-known-assets"
type = "local_directory"
name = "well-known"
version = "2026-01-09"

[[inputs.sources.artifacts]]
filename = "well-known"
uri = "file://../.well-known"
sha256 = "TODO: checksum the .well-known tree"

[build]
system = "cerro_image"

[[build.plan]]
step = "import"
using = "oci_image"
sources = ["wordpress-dhi"]

[[build.plan]]
step = "overlay"
sources = ["sinople-theme", "php-aegis-mu", "well-known-assets"]

description = "Copy the Sinople theme assets, php-aegis mu-plugin, and .well-known definitions into /var/www/html/wp-content and /var/www/html/.well-known before finalizing the image."

[[build.plan]]
step = "assemble_rootfs"

[[build.plan]]
step = "emit_oci_image"

[build.plan.image]
entrypoint = ["/usr/local/bin/php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
cmd = ["-d", "variables_order=EGPCS"]
labels = { "org.opencontainers.image.title" = "LCB WordPress" }

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

[policy.provenance]
require_source_hashes = true
require_reproducible_build = true

[policy.attestations]
emit = ["in_toto", "sbom_spdx_json", "source-signature"]

[attestations]
require = ["source-signature", "reproducible-build", "sbom-complete"]
recommend = ["security-audit"]

documentation = "See docs/hardened-wordpress.adoc and infra/wordpress.ctp for how to build, sign, and verify this manifest."
