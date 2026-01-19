# lcb-website

Website project for LCB. Track updates and assets for the hyperpolymath initiative.

## Container Baseline

The verified-container stack for this repo relies on `svalinn`, `cerro-torre`, and `vordr` as the gateway, builder, and runtime, respectively. See `Containerfile` for the immediate plan and references we will dogfood while developing the site.

## Toolchain

We use `asdf` to pin the runtimes that run behind this site, especially the Varnish cache and OpenLiteSpeed server. See `ASDF.md` for which plugins and commands are required so AI handovers and collaborators can stay in sync.

## Hardened WordPress story

See `docs/hardened-wordpress.adoc` for the chosen Debian-based Hardened WordPress base, Sanctify/PHP-Aegis hardening workflow, and how Cerro Torre/Svalinn/Vörðr treat it as the verified container artifact.

## License

This repository is licensed under **PMPL-1.0-or-later** (the Palimpsest-MPL License 1.0 or later). See `LEGAL.txt` for the full legal text from the Palimpsest Stewardship Council.
