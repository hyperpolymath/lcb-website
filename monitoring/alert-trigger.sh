#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
reports_dir="$repo_root/monitoring/reports"
health_files=("$reports_dir"/health-*.json)

if [ "${#health_files[@]}" -eq 0 ]; then
  echo "No health snapshots yet."
  exit 0
fi

latest="$(printf "%s\n" "${health_files[@]}" | sort | tail -n1)"
open5xx=$(jq -r '.openlitespeed.error_5xx // 0' "$latest")
redis_misses=$(jq -r '.redis.misses // 0' "$latest")

incident_name=""
incident_metric=""
incident_value=""
incident_threshold=""
incident_details=""
incident_tags=""

if [ "$open5xx" -gt 5 ]; then
  incident_name="openlitespeed-5xx"
  incident_metric="openlitespeed.error_5xx"
  incident_value="$open5xx"
  incident_threshold="5"
  incident_details="OpenLiteSpeed returned $open5xx 5xx responses (threshold 5) in the latest scrape."
  incident_tags="openlitespeed,error"
elif [ "$redis_misses" -gt 1000 ]; then
  incident_name="redis-misses"
  incident_metric="redis.misses"
  incident_value="$redis_misses"
  incident_threshold="1000"
  incident_details="Redis reported $redis_misses misses in the latest snapshot."
  incident_tags="redis,cache"
else
  echo "No alert thresholds exceeded."
  exit 0
fi

if ! command -v julia >/dev/null 2>&1; then
  echo "julia binary not found; cannot emit incident dustfile." >&2
  exit 1
fi

julia scripts/dust-hypatia.jl \
  --incident-name "$incident_name" \
  --incident-metric "$incident_metric" \
  --incident-value "$incident_value" \
  --incident-threshold "$incident_threshold" \
  --incident-details "$incident_details" \
  --incident-tags "$incident_tags" \
  --incident-source "collect-prometheus"

echo "Incident dustfile emitted for $incident_metric"
