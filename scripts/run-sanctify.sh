#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "$0")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"
sanctify_repo="$(realpath "$repo_root/../sanctify-php")"
theme_dir="$repo_root/wp-content/themes/sinople"
reports_dir="$repo_root/monitoring/reports"
cabal_dir="$repo_root/.cabal"

if [ ! -d "$sanctify_repo" ]; then
  echo "Missing sanctify-php repository at $sanctify_repo" >&2
  exit 1
fi

mkdir -p "$reports_dir" "$cabal_dir"
export CABAL_DIR="$cabal_dir"
export CABAL_STORE_DIR="$cabal_dir/store"
export CABAL_LOG_DIR="$cabal_dir/logs"
cabal_home="$repo_root/.cabal-home"
mkdir -p "$cabal_home"

cd "$sanctify_repo"
HOME="$cabal_home" cabal --store-dir="$cabal_dir/store" build

output="$reports_dir/sanctify-theme.json"
summary="$reports_dir/sanctify-theme-summary.txt"

echo "Running sanctify analysis on $theme_dir ..."
HOME="$cabal_home" cabal --store-dir="$cabal_dir/store" run sanctify -- analyze "$theme_dir" --format json --severity=critical,high --use-aegis "$@" > "$output"

cat <<EOF > "$summary"
sanctify analysis performed at $(date -u +"%Y-%m-%dT%H:%M:%SZ")
Theme: $theme_dir
Output: $output
EOF

echo "Sanctify JSON written to $output"
echo "Summary written to $summary"
