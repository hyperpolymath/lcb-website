# Contributing to LCB Website

Thank you for your interest in contributing to the LCB Website project! This document provides guidelines for contributing to this hardened WordPress deployment.

## Prerequisites

Before contributing, ensure you have the following installed:

- **Git** - Version control
- **ASDF** - Runtime version management (https://asdf-vm.com)
- **Deno** - JavaScript/TypeScript runtime
- **Docker** and **Docker Compose** - Container runtime (for development)
- **Just** - Command runner (https://just.systems)

### Optional (for full verified stack):
- **Alire** - Ada package manager (for Cerro Torre)
- **GNAT/Ada compiler** - For building Cerro Torre manifests
- **Rust** - For Vörðr and Svalinn components

## Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/hyperpolymath/lcb-website.git
   cd lcb-website
   ```

2. Install ASDF plugins (if using ASDF):
   ```bash
   just setup-asdf
   just install-tools
   ```

3. Copy environment variables:
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```

4. Check project status:
   ```bash
   just status
   ```

## Development Workflow

### Local Development

Start the development stack:
```bash
just dev
```

This starts WordPress, MariaDB, and Varnish using Docker Compose. Access:
- WordPress: http://localhost:8080
- Varnish: http://localhost:8081

### Validation

Run all validation checks:
```bash
just validate
```

This runs:
- RSR compliance checks
- .well-known file validation
- Security scanning

### Building Manifests

If you have Cerro Torre installed:
```bash
just ct-pack     # Build the manifest
just ct-verify   # Verify the manifest
```

## Code Standards

### Language Policy

This project follows the hyperpolymath language standards:

**Allowed:**
- ReScript (primary application code)
- Rust (systems code, performance-critical)
- Deno (runtime, replacing Node.js)
- Elixir/Gleam (backend services)
- Bash/POSIX shell (minimal scripts only)
- Ada/SPARK (Cerro Torre manifests)

**Not Allowed:**
- TypeScript (use ReScript instead)
- Node.js/npm (use Deno instead)
- Go (use Rust instead)
- Python (use Julia/Rust/ReScript instead)

### File Headers

All source files must include SPDX license identifier:
```
# SPDX-License-Identifier: PMPL-1.0-or-later
```

### Commit Messages

Follow conventional commit format:
```
type(scope): brief description

Longer description if needed.

Co-Authored-By: Name <email>
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

## Security

### Reporting Vulnerabilities

Report security issues to: security@hyperpolymath.org

See `.well-known/security.txt` for full security policy.

### Security Checklist

Before submitting:
- [ ] No hardcoded secrets or credentials
- [ ] All dependencies are pinned to specific versions
- [ ] SPDX headers present in all files
- [ ] No personally identifiable information (PII)
- [ ] Follows consent-aware HTTP requirements

## Testing

### Manual Testing

1. Start the development stack: `just dev`
2. Test WordPress functionality
3. Verify Varnish caching: Check `X-Cache` headers
4. Test consent enforcement: Send requests without AIBDP headers

### Automated Testing

Run security scans:
```bash
just security-check
```

## Pull Request Process

1. **Fork** the repository
2. **Create a branch** for your feature/fix:
   ```bash
   git checkout -b feature/my-feature
   ```
3. **Make your changes** following code standards
4. **Run validation**:
   ```bash
   just validate
   ```
5. **Commit your changes** with clear commit messages
6. **Push to your fork**:
   ```bash
   git push origin feature/my-feature
   ```
7. **Open a Pull Request** on GitHub

### PR Requirements

- [ ] All validation checks pass
- [ ] No merge conflicts with main branch
- [ ] Clear description of changes
- [ ] Related issue linked (if applicable)
- [ ] Documentation updated (if needed)
- [ ] STATE.scm updated (if significant changes)

## Documentation

### Updating Documentation

Documentation lives in:
- `docs/*.adoc` - Component integration guides
- `README.md` - Project overview
- `ROADMAP.adoc` - Project roadmap
- `.machine_readable/6scm/` - Machine-readable metadata

When adding new integrations or features, update:
1. Relevant `docs/*.adoc` file
2. `README.md` (if user-facing)
3. `STATE.scm` in `.machine_readable/6scm/`

### Documentation Standards

- Use AsciiDoc for technical documentation
- Use Markdown for user-facing docs
- Include code examples where applicable
- Reference related projects/components

## Project Structure

```
lcb-website/
├── .github/workflows/      # CI/CD workflows
├── .machine_readable/6scm/ # Machine-readable metadata
├── .well-known/            # Consent and security policies
├── docs/                   # Integration documentation
├── infra/                  # Infrastructure manifests
│   └── wordpress.ctp       # Cerro Torre manifest
├── services/               # Service configurations
│   ├── varnish/           # Varnish VCL files
│   └── mariadb/           # MariaDB configuration
├── docker-compose.yml      # Development environment
├── svalinn-compose.yml     # Production verified stack
└── justfile                # Command automation
```

## Component Repositories

This project integrates with:
- **svalinn** - Verified container gateway
- **cerro-torre** - Ada/SPARK manifest builder
- **vordr** - Formally verified runtime
- **wp-sinople-theme** - WordPress theme (WASM/ReScript)
- **php-aegis** - PHP security library
- **consent-aware-http** - AIBDP protocol implementation

These are separate repositories that need to be cloned to `../` relative to this repo.

## Questions?

- Open an issue on GitHub
- Read the documentation in `docs/`
- Check `justfile` for available commands (`just --list`)
- Review `.machine_readable/6scm/STATE.scm` for project status

## License

This project is licensed under **PMPL-1.0-or-later** (Palimpsest-MPL License).

See `LEGAL.txt` for full license text.

---

Thank you for contributing to a more secure and consent-aware web!
