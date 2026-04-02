#!/usr/bin/env bash
set -euo pipefail
shopt -s nullglob

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
reports_dir="$repo_root/monitoring/reports"
health_files=("$reports_dir"/health-*.json)

if [ "${#health_files[@]}" -eq 0 ]; then
  echo "No health snapshots yet."
  exit 0
fi

if ! command -v jq >/dev/null 2>&1; then
  echo "jq binary not found; cannot evaluate health snapshots." >&2
  exit 1
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

julia "$repo_root/scripts/dust-hypatia.jl" \
  --incident-name "$incident_name" \
  --incident-metric "$incident_metric" \
  --incident-value "$incident_value" \
  --incident-threshold "$incident_threshold" \
  --incident-details "$incident_details" \
  --incident-tags "$incident_tags" \
  --incident-source "collect-prometheus"

echo "Incident dustfile emitted for $incident_metric"

dispatch_hypatia_workflow() {
  local repository="${GITHUB_REPOSITORY:-}"
  local ref_name="${GITHUB_REF_NAME:-${HYPATIA_REF:-main}}"
  local workflow_file="${HYPATIA_WORKFLOW_FILE:-hypatia-scan.yml}"
  local api_url="${GITHUB_API_URL:-https://api.github.com}"

  if [ -z "${GITHUB_TOKEN:-}" ] || [ -z "$repository" ]; then
    echo "GitHub workflow dispatch not configured; keeping the local incident dustfile only."
    return 0
  fi

  if ! command -v curl >/dev/null 2>&1; then
    echo "curl binary not found; cannot dispatch Hypatia workflow." >&2
    return 1
  fi

  local body_file
  body_file="$(mktemp)"
  local status_code
  status_code="$(
    curl -sS -o "$body_file" -w "%{http_code}" \
      -X POST \
      -H "Authorization: Bearer $GITHUB_TOKEN" \
      -H "Accept: application/vnd.github+json" \
      -H "Content-Type: application/json" \
      "$api_url/repos/$repository/actions/workflows/$workflow_file/dispatches" \
      --data "$(jq -n --arg ref "$ref_name" '{ref:$ref}')"
  )"

  if [ "$status_code" != "204" ]; then
    echo "Failed to dispatch GitHub workflow $workflow_file for $repository (HTTP $status_code)." >&2
    cat "$body_file" >&2
    rm -f "$body_file"
    return 1
  fi

  rm -f "$body_file"
  echo "GitHub workflow dispatched: $repository $workflow_file @ $ref_name"
}

dispatch_hypatia_workflow
