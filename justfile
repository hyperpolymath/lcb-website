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
    @test -f .machine_readable/6scm/STATE.scm || (echo "❌ Missing STATE.scm" && exit 1)
    @test -f .machine_readable/6scm/ECOSYSTEM.scm || (echo "❌ Missing ECOSYSTEM.scm" && exit 1)
    @test -f .machine_readable/6scm/META.scm || (echo "❌ Missing META.scm" && exit 1)
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

# Run development stack (requires Docker Compose or Svalinn Compose)
dev:
    @echo "Starting development stack..."
    @if [ -f "docker-compose.yml" ]; then \
        docker compose up -d; \
    elif [ -f "svalinn-compose.yml" ]; then \
        svalinn-compose up -d; \
    else \
        echo "⚠️  No compose file found (docker-compose.yml or svalinn-compose.yml)"; \
    fi

# Stop development stack
stop:
    @echo "Stopping development stack..."
    @if [ -f "docker-compose.yml" ]; then \
        docker compose down; \
    elif [ -f "svalinn-compose.yml" ]; then \
        svalinn-compose down; \
    fi

# Run security checks
security-check:
    @echo "Running security checks..."
    @echo "Checking for hardcoded secrets..."
    @if command -v trufflehog >/dev/null 2>&1; then \
        trufflehog filesystem . --only-verified; \
    else \
        echo "⚠️  trufflehog not installed"; \
    fi

# Validate .well-known files
validate-wellknown:
    @echo "Validating .well-known files..."
    @test -f .well-known/aibdp.json || (echo "❌ Missing .well-known/aibdp.json" && exit 1)
    @test -f .well-known/security.txt || (echo "❌ Missing .well-known/security.txt" && exit 1)
    @if command -v jq >/dev/null 2>&1; then \
        jq empty .well-known/aibdp.json && echo "✅ aibdp.json is valid JSON"; \
    fi

# Clean build artifacts
clean:
    @echo "Cleaning build artifacts..."
    @rm -rf build/ dist/ target/ .ct-cache/
    @echo "✅ Clean complete"

# Full validation (RSR + well-known + security)
validate: validate-rsr validate-wellknown security-check
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
    @echo -n "  Docker:                "; command -v docker >/dev/null 2>&1 && echo "✅" || echo "❌ Missing"

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
