let fmt_labels = λlabels.
  if labels == []
  then ""
  else
    let components = std.map (λentry. entry.key ++ "=\"" ++ std.toString entry.value ++ "\"") labels
    in "{" ++ std.join(",", components) ++ "}"

let metric = λname help typ value labels.
  let label_str = fmt_labels labels
  in "# HELP " ++ name ++ " " ++ help ++ "\n# TYPE " ++ name ++ " " ++ typ ++ "\n" ++ name ++ label_str ++ " " ++ std.toString value

let data = std.parseJson data

let base_metrics = [
  metric "nuj_varnish_cache_hit_ratio" "Varnish cache hit ratio" "gauge" data.varnish.cache_hit []
  metric "nuj_varnish_backend_healthy" "Healthy Varnish backends" "gauge" data.varnish.backend_healthy []
  metric "nuj_varnish_uptime_seconds" "Varnish uptime" "counter" data.varnish.uptime_seconds []
  metric "nuj_varnish_backend_response_time_milliseconds" "Varnish backend response (ms)" "gauge" data.varnish.backend_response_time_ms []
  metric "nuj_openlitespeed_requests_per_second" "OpenLiteSpeed requests/sec" "gauge" data.openlitespeed.requests_per_second []
  metric "nuj_openlitespeed_active_connections" "OpenLiteSpeed active connections" "gauge" data.openlitespeed.active_connections []
  metric "nuj_openlitespeed_ssl_expiry_days" "Days until TLS expiry" "gauge" data.openlitespeed.ssl_expiry_days []
  metric "nuj_openlitespeed_error_5xx" "OpenLiteSpeed 5xx count" "counter" data.openlitespeed.error_5xx []
  metric "nuj_redis_hits" "Redis keyspace hits" "counter" data.redis.hits []
  metric "nuj_redis_misses" "Redis keyspace misses" "counter" data.redis.misses []
  metric "nuj_redis_evicted_keys" "Redis evicted keys" "counter" data.redis.evicted_keys []
  metric "nuj_redis_used_memory_mb" "Redis memory (MB)" "gauge" data.redis.used_memory_mb []
  metric "nuj_cloudflare_ssl_status" "Cloudflare SSL status indicator (1=active)" "gauge" 1 [{key="status", value=data.cloudflare.ssl_status}]
  metric "nuj_wordpress_wp_cron_minutes" "Minutes until next WP cron" "gauge" data.wordpress.cron_last_run_minutes []
  metric "nuj_wordpress_active_plugins" "Active WP plugins" "gauge" data.wordpress.plugins_active []
  metric "nuj_wordpress_php_errors_last_hour" "PHP errors (last hour)" "counter" data.wordpress.php_errors_last_hour []
]

let record_metrics =
  std.map (λrecord.
    metric "nuj_cloudflare_dns_record_ttl" "DNS record TTL" "gauge" record.ttl [
      {key="name", value=record.name};
      {key="type", value=record.type};
      {key="proxied", value=record.proxied}
    ])
  data.cloudflare.records

std.join("\n", std.concat(base_metrics, record_metrics))
