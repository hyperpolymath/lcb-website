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
family = "docker"
section = "library/wordpress"
snapshot_service = "registry-1.docker.io"
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
id = "wordpress-base"
type = "oci_image"
name = "wordpress"
version = "6.9.0-php8.5-apache"

[[inputs.sources.artifacts]]
filename = "docker.io-wordpress-6.9.0-php8.5-apache.oci"
uri = "oci://docker.io/library/wordpress:6.9.0-php8.5-apache"
sha256 = "b00800d362f90cd1db803eaac8994b5353e9fdbf1d7386baa798e4f63110cdc3"

[[inputs.sources]]
id = "sinople-theme"
type = "local_directory"
name = "wp-sinople-theme"
version = "0.1.0"

[[inputs.sources.artifacts]]
filename = "wp-sinople-theme"
uri = "file://../wp-sinople-theme/wordpress"
sha256 = "b358183f45f3232b0f2bf742a5ef3c033347a77fd05c0e6eee0684ddc68b4737"

[[inputs.sources]]
id = "php-aegis-mu"
type = "local_directory"
name = "php-aegis"
version = "1.0.0"

[[inputs.sources.artifacts]]
filename = "php-aegis-mu"
uri = "file://../php-aegis/src"
sha256 = "35017a37f886ef9a139030a57d4dbbab975cd0f0e57afb2e84d961ffef5d019d"

[[inputs.sources]]
id = "well-known-assets"
type = "local_directory"
name = "well-known"
version = "2026-01-09"

[[inputs.sources.artifacts]]
filename = "well-known"
uri = "file://../.well-known"
sha256 = "e078a2bd2b181e1e8c31a8982e74aefa7f2d828aa233150103edf4b18ca68231"

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
