#!/usr/bin/env bash
set -euo pipefail
repos_dir="$(cd "$(dirname "$0")/.." && pwd)"
cd "$repos_dir"
head=$(git rev-parse HEAD)
printf '{"head":"%s"}' "$head"
