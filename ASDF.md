# ASDF Toolchain for lcb-website

The LCB website work should stay aligned with the asdf-managed runtimes that gate our container/image flow. Whenever you touch or document components such as the HTTP cache or webserver, prefer installing and pinning the tool via asdf so the same runtimes are used by every collaborator and the AI operator.

## Required plugins
- `varnish` (via `https://github.com/hyperpolymath/asdf-varnish-plugin.git`). This provides Varnish Cache `varnishd`/`varnishadm` binaries and keeps the HTTP cache on a known version.
- `openlitespeed` (via `https://github.com/hyperpolymath/asdf-openlitespeed-plugin.git`). This mirrors the OpenLiteSpeed web server builds we reference in our `.ctp` manifests and documentation.

## Setup steps
1. Install asdf if you don’t already have it (`https://asdf-vm.com`).
2. Add the plugins:
   ```sh
   asdf plugin add varnish https://github.com/hyperpolymath/asdf-varnish-plugin.git
   asdf plugin add openlitespeed https://github.com/hyperpolymath/asdf-openlitespeed-plugin.git
   ```
3. Install the desired versions and make them globally available (or local per-service as needed):
   ```sh
   asdf install varnish latest
   asdf global varnish latest
   asdf install openlitespeed latest
   asdf global openlitespeed latest
   ```
4. Confirm the installation with `varnish --version` and `openlitespeed --version`.

## Keeping the list accurate
- Whenever you add a new backend, proxy, or runtime, add the corresponding asdf plugin entry (plugin name, repo, version hint) into this file so the handover instructions stay current.
- Update this document before every major change so the AI operator can remind you about installing the latest plugin or pinning a specific release.
