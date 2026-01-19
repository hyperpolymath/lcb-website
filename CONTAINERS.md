# Container Baseline for LCB Website

This project will dogfood the same verified-container stack we maintain in the `svalinn`, `cerro-torre`, and `vordr` repositories. Treating those three components as the container + image foundation keeps the website aligned with the practical tooling that actually protects and runs the workloads we care about. Wherever possible we iterate inside this repo by connecting back to their docs, manifests, and commands.

## How the stack fits together
1. **Svalinn** (edge gateway) validates every container request against the `verified-container-spec`, enforces policy/OAuth2 tokens, and exposes a Docker Compose-compatible `svalinn-compose` CLI.
2. **Cerro Torre** (builder) packs images into `.ctp` bundles with Ada/SPARK verification, cryptographic provenance, and exporter hooks before any runtime consumes them.
3. **Vörðr** (runtime) is the formally verified orchestrator that receives verified operations from Svalinn, interacts with attestation hooks, and records a reversible state journal.

The LCB website will ride atop that trio: Cerro Torre will supply the signed images, Vörðr will run them, and Svalinn will guard and expose the controls.

## Component notes

### Svalinn
- Repo: `/mnt/eclipse/repos/svalinn`
- Deno/Hono HTTP gateway with JSON Schema validation and OAuth2/JWT middleware.
- Use `just dev` for hot-reloading, `just serve` for production, and `just build` to package the binary.
- `svalinn-compose` is a policy-aware Compose CLI; see `svalinn-compose.yaml` examples for `x-svalinn` policy blocks and attestation requirements.
- Keeps the runtime boundary: it delegates container lifecycle operations to Vörðr (MCP/JSON-RPC).

### Cerro Torre
- Repo: `/mnt/eclipse/repos/cerro-torre`
- `.ctp` manifest format ensures manifests are Turing incomplete, declarative, and provably verifiable. See `spec/manifest-format.md` for details.
- Packages base images from Debian/Fedora (with optional Alpine/Nix) and exports to OCI/OSTree/.deb/.rpm.
- Build with `alr build` or `gprbuild -P cerro_torre.gpr`; the resulting `ct` binary lives under `bin/`.
- Strong focus on SELinux-enforcing policies, federated transparency logs, and threshold signing.

### Vörðr
- Repo: `/mnt/eclipse/repos/vordr`
- Polyglot runtimes: Elixir (GenStateMachine orchestrator), Rust CLI/eBPF hooks, Idris2 proofs, Ada trust engine.
- MCP JSON-RPC server listens on port 8080 (`src/elixir`); CLI commands like `vordr run`, `vordr ps`, `vordr stop`, `vordr undo`, and `vordr audit` manage containers.
- The Elixir orchestrator maintains a reversible journal and exposes attestation data via `vordr doctor`/`vordr audit`.

## Getting started locally
1. Clone or reference the three repos above; keep their `README` sections handy as a living reference chart in this repo.
2. Run Svalinn (`just dev` or `just serve`) so the website can talk to a verified gateway on `http://localhost:8000`.
3. Use `ct pack`/`ct verify` from Cerro Torre to produce `.ctp` bundles and push attestations into the runtime pipeline.
4. Launch `vordr` (Elixir + Rust) so the gateway’s MCP calls actually execute containers and maintain a journaling proof chain.

## Next steps for the LCB website
- Capture the specific images and services you want to showcase in `/content` or `./services`, and attach `x-svalinn` metadata when posting compose files so we can replicate the same verification path.
- Link to these component repos whenever we describe container flows on the site so readers understand the stack.
- Iterate on simple Compose definitions before we add front-end pages: start with `svalinn-compose` for the base service graph and slowly layer on Cerro/Vörðr proofs.
