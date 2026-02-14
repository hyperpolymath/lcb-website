# Git Hooks

Git hooks for nuj-lcb-production repository.

## Installation

```bash
./hooks/install.sh
```

This installs all hooks into `.git/hooks/`.

## Available Hooks

### pre-commit
- Validates SPDX headers in workflow files
- Scans for potential secrets/sensitive data
- Checks for EditorConfig presence

### commit-msg
- Validates minimum commit message length (10 chars)
- Warns about WIP/fixup/squash commits
- Suggests Co-Authored-By for substantial changes

### pre-push
- Checks for large files (>10MB)
- Verifies pushing to correct remote
- Confirms branch name for main/master pushes

## Manual Installation

If the install script doesn't work:

```bash
cp hooks/pre-commit .git/hooks/
cp hooks/commit-msg .git/hooks/
cp hooks/pre-push .git/hooks/
chmod +x .git/hooks/*
```

## Bypassing Hooks

If you need to bypass hooks temporarily:

```bash
git commit --no-verify
git push --no-verify
```

**Use with caution** - hooks exist to catch issues before they reach the remote.
