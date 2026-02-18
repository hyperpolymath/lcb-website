#!/usr/bin/env bash
set -euo pipefail

required_tools=(curl jq nickel)
for tool in "${required_tools[@]}"; do
  if ! command -v "$tool" >/dev/null 2>&1; then
    echo "Error: $tool is required by collect-prometheus.sh" >&2
    exit 1
  fi
done

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
metrics_dir="$repo_root/monitoring/prometheus"
reports_dir="$repo_root/monitoring/reports"
mkdir -p "$metrics_dir" "$reports_dir"

timestamp=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
json_report="$reports_dir/health-$(date -u +"%Y%m%dT%H%M%SZ").json"

varnish_cache_hit_ratio=0
varnish_backend_healthy=0
varnish_uptime_seconds=0
varnish_backend_response_ms=0
if command -v varnishstat >/dev/null 2>&1; then
  varnish_data=$(varnishstat -1 -j)
  hits=$(jq -r '."MAIN.cache_hit".value // 0' <<<"$varnish_data")
  misses=$(jq -r '."MAIN.cache_miss".value // 0' <<<"$varnish_data")
  total=$((hits + misses))
  if [ "$total" -gt 0 ]; then
    varnish_cache_hit_ratio=$(awk "BEGIN {printf \"%.4f\", $hits / $total}")
  fi
  varnish_uptime_seconds=$(jq -r '."MAIN.uptime".value // 0' <<<"$varnish_data")
  varnish_backend_response_ms=$(jq -r '."MAIN.backend_resp".value // 0' <<<"$varnish_data")
  if command -v varnishadm >/dev/null 2>&1; then
    varnish_backend_healthy=$(varnishadm backend.list | grep -c "Healthy" || echo 0)
  fi
fi

ols_rps=0
ols_active_conn=0
ols_ssl_expiry_days=0
ols_errors_5xx=0
ols_status_url="http://127.0.0.1:7080/server-status?auto"
ols_status_cmd=(curl -sS --max-time 5 "$ols_status_url")
if [ -n "${OLS_STATUS_USER:-}" ] && [ -n "${OLS_STATUS_PASS:-}" ]; then
  ols_status_cmd=(curl -sS --max-time 5 -u "$OLS_STATUS_USER:$OLS_STATUS_PASS" "$ols_status_url")
fi
ols_status_output="$({ "${ols_status_cmd[@]}"; } 2>/dev/null || true)"
if [ -n "$ols_status_output" ]; then
  ols_rps=$(awk -F"=" '/ReqPerSec/ {print $2}' <<<"$ols_status_output" | head -n1 || echo 0)
  ols_active_conn=$(awk -F"=" '/ConnsTotal/ {print $2}' <<<"$ols_status_output" | head -n1 || echo 0)
  ols_errors_5xx=$(awk -F"=" '/Error5xx/ {print $2}' <<<"$ols_status_output" | head -n1 || echo 0)
fi
if command -v openssl >/dev/null 2>&1; then
  cert_end=$(openssl s_client -connect nuj-lcb.org.uk:443 -servername nuj-lcb.org.uk </dev/null 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2 || true)
  if [ -n "$cert_end" ]; then
    expiry_ts=$(date -d "$cert_end" +%s)
    now_ts=$(date +%s)
    ols_ssl_expiry_days=$(( (expiry_ts - now_ts) / 86400 ))
  fi
fi
ols_rps=${ols_rps:-0}
ols_active_conn=${ols_active_conn:-0}
ols_errors_5xx=${ols_errors_5xx:-0}
ols_ssl_expiry_days=${ols_ssl_expiry_days:-0}

redis_hits=0
redis_misses=0
redis_evicted=0
redis_used_mb=0
if command -v redis-cli >/dev/null 2>&1; then
  redis_info=$(redis-cli INFO ALL)
  redis_hits=$(awk -F":" '/keyspace_hits/ {print $2}' <<<"$redis_info" | tr -d '\r')
  redis_misses=$(awk -F":" '/keyspace_misses/ {print $2}' <<<"$redis_info" | tr -d '\r')
  redis_evicted=$(awk -F":" '/evicted_keys/ {print $2}' <<<"$redis_info" | tr -d '\r')
  used=$(awk -F":" '/used_memory:/ {print $2}' <<<"$redis_info" | head -n1 | tr -d '\r')
  if [ -n "$used" ]; then
    redis_used_mb=$(awk "BEGIN {printf \"%.2f\", $used/1024/1024}")
  fi
fi

cloudflare_ssl_status="unknown"
cloudflare_records_file=$(mktemp)
echo "[]" > "$cloudflare_records_file"
if [ -n "${CF_API_TOKEN:-}" ] && [ -n "${CF_ZONE_ID:-}" ]; then
  zone_resp=$(curl -sS -H "Authorization: Bearer $CF_API_TOKEN" -H "Content-Type: application/json" "https://api.cloudflare.com/client/v4/zones/$CF_ZONE_ID")
  cloudflare_ssl_status=$(jq -r '.result.status // "unknown"' <<<"$zone_resp")
  records_resp=$(curl -sS -H "Authorization: Bearer $CF_API_TOKEN" -H "Content-Type: application/json" "https://api.cloudflare.com/client/v4/zones/$CF_ZONE_ID/dns_records?per_page=100")
  jq -c '.result[]' <<<"$records_resp" | while IFS= read -r record; do
    name=$(jq -r '.name' <<<"$record")
    type=$(jq -r '.type' <<<"$record")
    ttl=$(jq -r '.ttl' <<<"$record")
    proxied=$(jq -r '.proxied' <<<"$record")
    proxied_bool=$([ "$proxied" = "true" ] && echo true || echo false)
    record_json=$(jq -n \
      --arg name "$name" \
      --arg type "$type" \
      --arg ttl "$ttl" \
      --argjson proxied "$proxied_bool" \
      '{name:$name,type:$type,ttl:($ttl|tonumber|if . == null then 0 else . end),proxied:$proxied}')
    jq ". + [$record_json]" "$cloudflare_records_file" > "$cloudflare_records_file.tmp"
    mv "$cloudflare_records_file.tmp" "$cloudflare_records_file"
  done
fi

wp_cron_minutes=0
wp_plugins_active=0
wp_php_errors=0
if command -v wp >/dev/null 2>&1; then
  wp_cron_output=$(wp cron event list --fields=hook,next_run --format=json 2>/dev/null || true)
  if [ -n "$wp_cron_output" ] && [ "$wp_cron_output" != "[]" ]; then
    next_run=$(jq -r '.[0].next_run' <<<"$wp_cron_output" 2>/dev/null || true)
    if [ -n "$next_run" ] && [ "$next_run" != "null" ]; then
      now_epoch=$(date +%s)
      next_epoch=$(date -d "$next_run" +%s)
      wp_cron_minutes=$(( (next_epoch - now_epoch) / 60 ))
    fi
  fi
  wp_plugins_active=$(wp plugin list --status=active --field=name 2>/dev/null | wc -l)
  wp_php_errors=$(grep -c "PHP" /var/www/html/error_log 2>/dev/null || true)
fi

cloudflare_records_json=$(cat "$cloudflare_records_file")
jq -n \
  --arg ts "$timestamp" \
  --argjson varnish "{\"cache_hit\":$varnish_cache_hit_ratio,\"backend_healthy\":$varnish_backend_healthy,\"uptime_seconds\":$varnish_uptime_seconds,\"backend_response_time_ms\":$varnish_backend_response_ms}" \
  --argjson ols "{\"requests_per_second\":${ols_rps:-0},\"active_connections\":${ols_active_conn:-0},\"ssl_expiry_days\":${ols_ssl_expiry_days:-0},\"error_5xx\":${ols_errors_5xx:-0}}" \
  --argjson redis "{\"hits\":${redis_hits:-0},\"misses\":${redis_misses:-0},\"evicted_keys\":${redis_evicted:-0},\"used_memory_mb\":${redis_used_mb:-0}}" \
  --arg cloudflare_ssl_status "${cloudflare_ssl_status:-unknown}" \
  --argjson cloudflare_records "$cloudflare_records_json" \
  --argjson wordpress "{\"cron_last_run_minutes\":${wp_cron_minutes:-0},\"plugins_active\":${wp_plugins_active:-0},\"php_errors_last_hour\":${wp_php_errors:-0}}" \
  '{timestamp:$ts,varnish:$varnish,openlitespeed:$ols,redis:$redis,cloudflare:{ssl_status:$cloudflare_ssl_status,records:$cloudflare_records},wordpress:$wordpress}' > "$json_report"

metrics_output="$metrics_dir/health.prom"
metrics_json=$(jq -c '.' "$json_report")
nickel monitoring/exporter.ncl --arg data "$metrics_json" > "$metrics_output"

rm -f "$cloudflare_records_file"

if [ -x "$repo_root/monitoring/alert-trigger.sh" ]; then
  bash "$repo_root/monitoring/alert-trigger.sh"
fi
