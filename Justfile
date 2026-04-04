# SPDX-License-Identifier: PMPL-1.0-or-later
# Justfile for lcb-website

# Default recipe - show available commands
default:
    @just --list

# Validate RSR compliance
validate-rsr:
    @echo "Validating Rhodium Standard Repository compliance..."
    @test -f README.md || (echo "❌ Missing README.md" && exit 1)
    @test -f LEGAL.txt || (echo "❌ Missing LEGAL.txt" && exit 1)
    @test -f .machine_readable/6a2/STATE.a2ml || (echo "❌ Missing .machine_readable/6a2/STATE.a2ml" && exit 1)
    @test -f .machine_readable/6a2/ECOSYSTEM.a2ml || (echo "❌ Missing .machine_readable/6a2/ECOSYSTEM.a2ml" && exit 1)
    @test -f .machine_readable/6a2/META.a2ml || (echo "❌ Missing .machine_readable/6a2/META.a2ml" && exit 1)
    @echo "✅ RSR compliance checks passed"

# Build Cerro Torre manifest
ct-pack:
    @echo "Building Cerro Torre manifest..."
    @if command -v ct >/dev/null 2>&1; then \
        ct pack infra/wordpress.ctp; \
    else \
        echo "⚠️  ct binary not found. Install Alire and build cerro-torre first."; \
        echo "    See: https://alire.ada.dev/"; \
        exit 1; \
    fi

# Verify Cerro Torre manifest
ct-verify:
    @echo "Verifying Cerro Torre manifest..."
    @if command -v ct >/dev/null 2>&1; then \
        ct verify infra/wordpress.ctp; \
    else \
        echo "⚠️  ct binary not found."; \
        exit 1; \
    fi

# Generate checksums for manifest TODOs
generate-checksums:
    @echo "Generating checksums for manifest sources..."
    @if [ -d "../wp-sinople-theme/wordpress" ]; then \
        echo "wp-sinople-theme SHA256:"; \
        find ../wp-sinople-theme/wordpress -type f -exec sha256sum {} \; | sort | sha256sum | cut -d' ' -f1; \
    else \
        echo "⚠️  wp-sinople-theme not found at ../wp-sinople-theme/wordpress"; \
    fi
    @if [ -d "../php-aegis/src" ]; then \
        echo "php-aegis SHA256:"; \
        find ../php-aegis/src -type f -exec sha256sum {} \; | sort | sha256sum | cut -d' ' -f1; \
    else \
        echo "⚠️  php-aegis not found at ../php-aegis/src"; \
    fi
    @if [ -d ".well-known" ]; then \
        echo ".well-known SHA256:"; \
        find .well-known -type f -exec sha256sum {} \; | sort | sha256sum | cut -d' ' -f1; \
    else \
        echo "⚠️  .well-known directory not found"; \
    fi

# Setup ASDF plugins
setup-asdf:
    @echo "Setting up ASDF plugins..."
    @if command -v asdf >/dev/null 2>&1; then \
        asdf plugin add varnish https://github.com/hyperpolymath/asdf-varnish-plugin.git 2>/dev/null || echo "Varnish plugin already added"; \
        asdf plugin add openlitespeed https://github.com/hyperpolymath/asdf-openlitespeed-plugin.git 2>/dev/null || echo "OpenLiteSpeed plugin already added"; \
        echo "✅ ASDF plugins configured"; \
    else \
        echo "⚠️  ASDF not installed. See: https://asdf-vm.com"; \
    fi

# Install ASDF tools
install-tools:
    @echo "Installing ASDF tools..."
    @if command -v asdf >/dev/null 2>&1; then \
        asdf install; \
        echo "✅ Tools installed"; \
    else \
        echo "⚠️  ASDF not installed"; \
    fi

# Start Svalinn gateway (if available)
svalinn-dev:
    @echo "Starting Svalinn development server..."
    @if [ -d "../svalinn" ]; then \
        cd ../svalinn && just dev; \
    else \
        echo "⚠️  Svalinn not found at ../svalinn"; \
        echo "    This gateway is required for verified container operations"; \
    fi

# Start Vörðr runtime (if available)
vordr-start:
    @echo "Starting Vörðr runtime..."
    @if [ -d "../vordr" ]; then \
        cd ../vordr && just start; \
    else \
        echo "⚠️  Vörðr not found at ../vordr"; \
        echo "    This runtime is required for container execution"; \
    fi

# Run development stack (prefers Podman Compose; falls back to Docker Compose)
dev:
    @echo "Starting development stack..."
    @if [ -f "docker-compose.yml" ]; then \
        if command -v podman-compose >/dev/null 2>&1; then \
            podman-compose -f docker-compose.yml up -d; \
        elif command -v podman >/dev/null 2>&1; then \
            podman compose -f docker-compose.yml up -d; \
        elif command -v docker >/dev/null 2>&1; then \
            docker compose up -d; \
        else \
            echo "⚠️  No Podman/Docker compose runtime found"; \
            exit 1; \
        fi; \
    elif [ -f "selur-compose.yml" ]; then \
        selur-compose up -d; \
    else \
        echo "⚠️  No compose file found (docker-compose.yml or selur-compose.yml)"; \
    fi

# Stop development stack
stop:
    @echo "Stopping development stack..."
    @if [ -f "docker-compose.yml" ]; then \
        if command -v podman-compose >/dev/null 2>&1; then \
            podman-compose -f docker-compose.yml down; \
        elif command -v podman >/dev/null 2>&1; then \
            podman compose -f docker-compose.yml down; \
        elif command -v docker >/dev/null 2>&1; then \
            docker compose down; \
        else \
            echo "⚠️  No Podman/Docker compose runtime found"; \
            exit 1; \
        fi; \
    elif [ -f "selur-compose.yml" ]; then \
        selur-compose down; \
    fi

# Run security checks
security-check:
    @echo "Running security checks..."
    @echo "Checking for hardcoded secrets..."
    @if command -v trufflehog >/dev/null 2>&1; then \
        scripts/run-trufflehog.sh; \
    else \
        echo "⚠️  trufflehog not installed"; \
    fi

dust-hypatia:
    @echo "Extracting recovery events for Hypatia..."
    @julia scripts/dust-hypatia.jl

sanctify-analyze:
    @echo "Running sanctify-php against the Sinople theme..."
    @bash scripts/run-sanctify.sh

validate-monitoring:
    @echo "Validating monitoring assets..."
    @test -f monitoring/exporter.ncl || (echo "❌ Missing monitoring/exporter.ncl" && exit 1)
    @test -f monitoring/metrics.schema.json || (echo "❌ Missing monitoring/metrics.schema.json" && exit 1)
    @test -f monitoring/prometheus/alerts.yml || (echo "❌ Missing monitoring/prometheus/alerts.yml" && exit 1)
    @test -f monitoring/prometheus/grafana-dashboard.json || (echo "❌ Missing monitoring/prometheus/grafana-dashboard.json" && exit 1)
    @echo "✅ Monitoring assets present"

# Validate .well-known files
validate-wellknown:
    @echo "Validating .well-known files..."
    @test -f .well-known/aibdp.json || (echo "❌ Missing .well-known/aibdp.json" && exit 1)
    @test -f .well-known/security.txt || (echo "❌ Missing .well-known/security.txt" && exit 1)
    @if command -v jq >/dev/null 2>&1; then \
        jq empty .well-known/aibdp.json && echo "✅ aibdp.json is valid JSON"; \
    fi

# Publish static mirror to IPFS and update DNSLink
ipfs-publish file='content/nuj-lcb-shareable-site.html':
    @echo "Publishing {{file}} to IPFS and updating DNSLink..."
    @bash scripts/ipfs-publish.sh {{file}}

# Clean build artifacts
clean:
    @echo "Cleaning build artifacts..."
    @rm -rf build/ dist/ target/ .ct-cache/
    @echo "✅ Clean complete"

# Full validation (RSR + well-known + security)
validate: validate-rsr validate-wellknown security-check validate-monitoring
    @echo "✅ All validations passed"

# Show project status
status:
    @echo "📊 LCB Website Status"
    @echo "===================="
    @echo ""
    @echo "Required Components:"
    @echo -n "  Svalinn (gateway):     "; [ -d "../svalinn" ] && echo "✅" || echo "❌ Missing"
    @echo -n "  Cerro Torre (builder): "; command -v ct >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"
    @echo -n "  Vörðr (runtime):       "; [ -d "../vordr" ] && echo "✅" || echo "❌ Missing"
    @echo ""
    @echo "Optional Components:"
    @echo -n "  wp-sinople-theme:      "; [ -d "../wp-sinople-theme" ] && echo "✅" || echo "❌ Missing"
    @echo -n "  php-aegis:             "; [ -d "../php-aegis" ] && echo "✅" || echo "❌ Missing"
    @echo ""
    @echo "Tools:"
    @echo -n "  ASDF:                  "; command -v asdf >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"
    @echo -n "  Deno:                  "; command -v deno >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"
    @echo -n "  Podman:                "; command -v podman >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"
    @echo -n "  Podman Compose:        "; command -v podman-compose >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"

# Help
help:
    @echo "LCB Website - Hardened WordPress Deployment"
    @echo "==========================================="
    @echo ""
    @echo "Core Commands:"
    @echo "  just validate       - Run all validation checks"
    @echo "  just ct-pack        - Build Cerro Torre manifest"
    @echo "  just ct-verify      - Verify Cerro Torre manifest"
    @echo "  just dev            - Start development stack"
    @echo "  just status         - Show project component status"
    @echo ""
    @echo "Setup:"
    @echo "  just setup-asdf     - Configure ASDF plugins"
    @echo "  just install-tools  - Install ASDF tools"
    @echo ""
    @echo "See 'just --list' for all available commands"

# Run panic-attacker pre-commit scan
assail:
    @command -v panic-attack >/dev/null 2>&1 && panic-attack assail . || echo "panic-attack not found — install from https://github.com/hyperpolymath/panic-attacker"

# Self-diagnostic — checks dependencies, permissions, paths
doctor:
    @echo "Running diagnostics for lcb-website..."
    @echo "Checking required tools..."
    @command -v just >/dev/null 2>&1 && echo "  [OK] just" || echo "  [FAIL] just not found"
    @command -v git >/dev/null 2>&1 && echo "  [OK] git" || echo "  [FAIL] git not found"
    @echo "Checking for hardcoded paths..."
    @grep -rn '$HOME\|$ECLIPSE_DIR' --include='*.rs' --include='*.ex' --include='*.res' --include='*.gleam' --include='*.sh' . 2>/dev/null | head -5 || echo "  [OK] No hardcoded paths"
    @echo "Diagnostics complete."

# Auto-repair common issues
heal:
    @echo "Attempting auto-repair for lcb-website..."
    @echo "Fixing permissions..."
    @find . -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true
    @echo "Cleaning stale caches..."
    @rm -rf .cache/stale 2>/dev/null || true
    @echo "Repair complete."

# Guided tour of key features
tour:
    @echo "=== lcb-website Tour ==="
    @echo ""
    @echo "1. Project structure:"
    @ls -la
    @echo ""
    @echo "2. Available commands: just --list"
    @echo ""
    @echo "3. Read README.adoc for full overview"
    @echo "4. Read EXPLAINME.adoc for architecture decisions"
    @echo "5. Run 'just doctor' to check your setup"
    @echo ""
    @echo "Tour complete! Try 'just --list' to see all available commands."

# Open feedback channel with diagnostic context
help-me:
    @echo "=== lcb-website Help ==="
    @echo "Platform: $(uname -s) $(uname -m)"
    @echo "Shell: $SHELL"
    @echo ""
    @echo "To report an issue:"
    @echo "  https://github.com/hyperpolymath/lcb-website/issues/new"
    @echo ""
    @echo "Include the output of 'just doctor' in your report."


# Print the current CRG grade (reads from READINESS.md '**Current Grade:** X' line)
crg-grade:
    @grade=$$(grep -oP '(?<=\*\*Current Grade:\*\* )[A-FX]' READINESS.md 2>/dev/null | head -1); \
    [ -z "$$grade" ] && grade="X"; \
    echo "$$grade"

# Generate a shields.io badge markdown for the current CRG grade
# Looks for '**Current Grade:** X' in READINESS.md; falls back to X
crg-badge:
    @grade=$$(grep -oP '(?<=\*\*Current Grade:\*\* )[A-FX]' READINESS.md 2>/dev/null | head -1); \
    [ -z "$$grade" ] && grade="X"; \
    case "$$grade" in \
      A) color="brightgreen" ;; B) color="green" ;; C) color="yellow" ;; \
      D) color="orange" ;; E) color="red" ;; F) color="critical" ;; \
      *) color="lightgrey" ;; esac; \
    echo "[![CRG $$grade](https://img.shields.io/badge/CRG-$$grade-$$color?style=flat-square)](https://github.com/hyperpolymath/standards/tree/main/component-readiness-grades)"
