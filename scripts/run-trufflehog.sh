#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "$0")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"
allow_file="$repo_root/.trufflehog/allowlist.json"

if [ ! -d "$repo_root/.git" ]; then
  echo "Skipping trufflehog: no .git directory at $repo_root" >&2
  exit 0
fi

allow_args=()
allow_label="no allowlist"
if [ -f "$allow_file" ]; then
  allow_args=(--allow "$allow_file")
  allow_label="$(basename "$allow_file")"
fi

bare_dir="$(mktemp -d /tmp/lcb-website-trufflehog-bare-XXXXXX.git)"
work_dir="$(mktemp -d /tmp/lcb-website-trufflehog-XXXXXX)"
snapshot_dir=""
trap 'rm -rf "$bare_dir" "$work_dir" "$snapshot_dir"' EXIT

if git -C "$repo_root" rev-parse --verify HEAD >/dev/null 2>&1; then
  git clone --quiet --bare "$repo_root" "$bare_dir"
  git clone --quiet "$repo_root" "$work_dir"
else
  snapshot_dir="$(mktemp -d /tmp/lcb-website-trufflehog-snapshot-XXXXXX)"
  git init --quiet "$snapshot_dir"
  rsync -a --exclude='.git/' "$repo_root/" "$snapshot_dir/"
  (
    cd "$snapshot_dir"
    git add -A
    git -c user.name='trufflehog snapshot' -c user.email='trufflehog@local.invalid' \
      commit --quiet -m 'Temporary snapshot for trufflehog scan'
  )
  git clone --quiet --bare "$snapshot_dir" "$bare_dir"
  git clone --quiet "$snapshot_dir" "$work_dir"
fi

(
  cd "$work_dir"
  git remote set-url origin "file://$bare_dir"
)

echo "Running trufflehog ($allow_label) on $work_dir with repo_path set to $work_dir"
trufflehog "${allow_args[@]}" --entropy False --repo_path "$work_dir" "file://$bare_dir"
