#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
metrics_file="$repo_root/monitoring/prometheus/health.prom"
node_dir="/var/lib/node_exporter/textfile_collector"

if [ ! -f "$metrics_file" ]; then
  echo "Metrics file missing at $metrics_file" >&2
  exit 1
fi

mkdir -p "$node_dir"
ln -sf "$metrics_file" "$node_dir/health.prom"
echo "Symlinked $metrics_file → $node_dir/health.prom"
