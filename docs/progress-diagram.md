# Progress Diagram — NUJ LCB Website

This document maps the live deployment, the statically generated surface, the IPFS fallback, and the Prometheus/Hypatia monitoring loop. The goal is to keep everyone aligned on subdomains, recovery coverage, and what data feeds drive alerts.

## System Snapshot

```mermaid
flowchart TB
  Visitor["Visitor"] --> Cloudflare[/"Cloudflare Zone\nTLS + WAF + Bot Fight/]
  Cloudflare -->|proxy| Verpex[/"Verpex cPanel\nLiteSpeed + WP + LiteSpeed Cache/"]
  Cloudflare -->|A + CNAME| Static["Static SSG\n`ddraig-ssg` + IPFS fallback"]
  Static --> IPFS["IPFS gateway\n`ipfs.nuj-lcb.org.uk` (Pinata + DNSLink)"]
  Verpex --> WordPress["WordPress 6.9 + Sinople + Plugins"]
  WordPress -->|Varnish/Redis| Cache["LiteSpeed Cache + Redis + Varnish"]
  Cache -->|metrics| Monitoring["Prometheus + Nickel exporter"]
  Monitoring --> Hypatia["Hypatia workflow\n`dust-hypatia.json` + alert-trigger"]
  Hypatia -->|enrich| Alerting["Grafana / Alertmanager / Hypatia comments"]

  subgraph Subdomains
    Conference[conference.nuj-lcb.org.uk\nBigBlueButton]
    Chat[chat.nuj-lcb.org.uk\nZulip]
    API[api.nuj-lcb.org.uk\nGraphQL / REST / gRPC]
    STFP[stfp.nuj-lcb.org.uk\nSecure file service]
    Office[office.nuj-lcb.org.uk\nOffice collaboration]
  end

  Visitor --> Conference
  Visitor --> Chat
  Visitor --> API
  Visitor --> STFP
  Visitor --> Office
```

## Key Notes

1. **Cloudflare Zone** — `nuj-lcb.org.uk` uses Cloudflare for DNS, TLS (Full Strict), WAF (Bot Fight Mode), and HTTP/3 with Brotli. All subdomains (Conference, Chat, API, STFP, Office, `www`, `mail`) are proxied through the same zone so we get consistent headers (`HSTS`, `X-Frame-Options`, etc.) and rate limiting.
2. **Verpex cPanel** — The legacy WordPress stack (PHP 8.4, MariaDB, LiteSpeed Enterprise) sits in the Verpex docroot. `Containerfile`/`selur-compose` describe the future container path, but today the site is live on Verpex with `wp-config` security constants and `.htaccess` rewrites sourced from `templates/`.
3. **Static/SSG + IPFS fallback** — `ddraig-ssg` content mirrors the shareable preview and is deployable via GitHub Pages or Pinata. `content/nuj-lcb-shareable-site.html` is published through `scripts/ipfs-publish.sh`, which updates the Cloudflare DNSLink record for `ipfs.nuj-lcb.org.uk`.
4. **Subdomains** — All `Conference`, `Chat`, `API`, `STFP`, and `Office` CNAME/A records should point to the same Cloudflare-managed IP (or proxied host). Each service (Jitsi, Zulip, GraphQL/REST/gRPC API, secure file transfer, Office) is described in `content/` and `docs/` for operators. `Members` area can live under `/members/` or `members.nuj-lcb.org.uk` depending on where access controls require isolation.
5. **Monitoring + Hypatia** — `scripts/collect-prometheus.sh` pulls telemetry from the cache, OLS, Redis, WordPress, and Cloudflare. `monitoring/exporter.ncl` converts the metric schema into Prometheus text format. Alerts from `prometheus/alerts.yml` hit `monitoring/alert-trigger.sh`, which triggers Hypatia and attaches `monitoring/reports/health-*.json` and `monitoring/reports/dust-hypatia.json` for context.
6. **Progress/status** — `TOPOLOGY.md` tracks component readiness. Update it when plugins, headers, or subdomain work moves forward. The diagram above is considered the “public-facing” map—if a subdomain is not ready yet, mark it as “pending” in `TOPOLOGY.md` and `SITE-STATUS.md`.

## Monitoring Hooks

- `monitoring/reports/dust-hypatia.json` now covers rollback/handler instructions derived from `contractiles/dust/Dustfile`. Feed this into Hypatia comments so alerts can reference which command to execute next.
- `monitoring/reports/incidents/incident-<timestamp>.json` captures each alert’s incident context (metric, tags, details) plus the filtered recovery entries selected by `scripts/dust-hypatia.jl`. Attach these files to Hypatia/Grafana comments or LLM prompts so the remediation story is available wherever the alert surfaces.
- Node exporter should consume `monitoring/prometheus/health.prom` (symlinked through `monitoring/setup-exporter.sh`). Hook Grafana to the dashboard at `monitoring/prometheus/grafana-dashboard.json` for the five-course view plus a Hypatia-status panel.
- When the Prometheus alert stack notices degradation (cache TTL drops, high WP errors, or cleared redis), Hypatia reruns, and `monitoring/reports/dust-hypatia.json` is part of the evidence payload.

## Next Steps

1. Keep the diagram in sync with `TOPOLOGY.md` whenever a subdomain, header, or monitoring update happens.
2. Document the `conference`, `chat`, `api`, `stfp`, and `office` services in `/content/pages/` with REST/GraphQL endpoints, API touchpoints, and embedded widgets.
3. Automate the `dust-hypatia` run via `just dust-hypatia` and include it in the Prometheus collection cron so Hypatia always receives fresh recovery metadata.
