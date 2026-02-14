#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later
# Install git hooks for nuj-lcb-production

set -e

echo "Installing git hooks..."

HOOKS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
GIT_HOOKS_DIR="$(git rev-parse --git-dir)/hooks"

# Install each hook
for hook in pre-commit commit-msg pre-push; do
  if [ -f "$HOOKS_DIR/$hook" ]; then
    echo "Installing $hook..."
    cp "$HOOKS_DIR/$hook" "$GIT_HOOKS_DIR/$hook"
    chmod +x "$GIT_HOOKS_DIR/$hook"
    echo "✓ Installed $hook"
  fi
done

echo "✅ Git hooks installed successfully"
echo ""
echo "Hooks installed:"
echo "  - pre-commit: Validates SPDX headers, scans for secrets"
echo "  - commit-msg: Validates commit message format"
echo "  - pre-push: Checks for large files, verifies remote"
