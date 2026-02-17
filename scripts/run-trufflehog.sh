#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "$0")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"
allow_file="$repo_root/.trufflehog/allowlist.json"

if [ ! -d "$repo_root/.git" ]; then
  echo "TruffleHog scan must run from the repository root ($repo_root)" >&2
  exit 1
fi

allow_args=()
allow_label="no allowlist"
if [ -f "$allow_file" ]; then
  allow_args=(--allow "$allow_file")
  allow_label="$(basename "$allow_file")"
fi

bare_dir="$(mktemp -d /tmp/lcb-website-trufflehog-bare-XXXXXX.git)"
work_dir="$(mktemp -d /tmp/lcb-website-trufflehog-XXXXXX)"
trap 'rm -rf "$bare_dir" "$work_dir"' EXIT

git clone --quiet --bare "$repo_root" "$bare_dir"
git clone --quiet "$repo_root" "$work_dir"
(
  cd "$work_dir"
  git remote set-url origin "file://$bare_dir"
)

echo "Running trufflehog ($allow_label) on $work_dir with repo_path set to $work_dir"
trufflehog "${allow_args[@]}" --entropy False --repo_path "$work_dir" "file://$bare_dir"
