# LCB Website Monitoring Playbook

## Overview
- `scripts/collect-prometheus.sh` gathers Varnish, OpenLiteSpeed, Redis, Cloudflare, and WordPress metrics, writes a structured JSON snapshot into `monitoring/reports/health-<timestamp>.json`, then renders Prometheus metrics through Nickel (`monitoring/exporter.ncl`) into `monitoring/prometheus/health.prom`.
- `scripts/dust-hypatia.jl` parses `contractiles/dust/Dustfile` and emits `monitoring/reports/dust-hypatia.json`, turning each recovery/compliance block into a reusable JSON event for Hypatia or dashboards. When invoked with `--incident-*` metadata, it also writes incident-specific dustfiles into `monitoring/reports/incidents/incident-<timestamp>.json` so alerts carry the precise remediation steps.
- `monitoring/exporter.ncl` drives the schema so every metric you see in dashboards or Hypatia matches the same data model (see `monitoring/metrics.schema.json`).
- `monitoring/prometheus/alerts.yml` defines the alert rules; Alertmanager should load this file so outages trigger `monitoring/alert-trigger.sh`, which dispatches the Hypatia workflow with context on the failing metric.
- `monitoring/prometheus/grafana-dashboard.json` is an importable Grafana dashboard showing the five “courses” (cache, TLS, Cloudflare, Redis, WordPress errors) plus a Hypatia-status panel.

## Running the exporter

1. Install prerequisites: `curl`, `jq`, `nickel`, plus `varnishstat`, `redis-cli`, and `wp` if available.  
2. Export credentials: `CF_API_TOKEN`, `CF_ZONE_ID`, `OLS_STATUS_USER`, `OLS_STATUS_PASS`, and `GITHUB_TOKEN` (with `repo` + `workflow` scope so Hypatia dispatch works).  
3. Execute:
```
CF_ZONE_ID=... CF_API_TOKEN=... OLS_STATUS_USER=... OLS_STATUS_PASS=... GITHUB_TOKEN=... ./scripts/collect-prometheus.sh
```
4. The script drops `monitoring/prometheus/health.prom` (for Prometheus/node_exporter) and `monitoring/reports/health-*.json` (for Hypatia/archival).

## Deployment helpers
- `monitoring/setup-exporter.sh` symlinks the latest `health.prom` into `/var/lib/node_exporter/textfile_collector/health.prom`; run it on whichever host runs node_exporter to keep the exporter in sync.
- `monitoring/alert-trigger.sh` checks the latest JSON snapshot for failing thresholds and calls the GitHub workflow dispatch API (using `GITHUB_TOKEN`) so Hypatia reruns when the site misbehaves.

## Alert trigger
- `monitoring/alert-trigger.sh` also evaluates the latest health JSON and, when necessary, calls `julia scripts/dust-hypatia.jl --incident-*` so an incident dustfile is emitted under `monitoring/reports/incidents/`. Attach that file to any Hypatia/Grafana alerts so LLMs get the curated remediation steps.

## Prometheus/Alertmanager setup
- Add `monitoring/prometheus/health.prom` to node_exporter via a symlink or direct copy.  
- Import `monitoring/prometheus/grafana-dashboard.json` into Grafana to render the core 6-panel view.  
- Add `monitoring/prometheus/alerts.yml` to your Prometheus `rule_files`; Alertmanager should send alerts (and optionally trigger Hypatia via `monitoring/alert-trigger.sh`).

## Automation note
- Schedule `scripts/collect-prometheus.sh` through cron/Ansible/toolbox (make sure the environment exports the required tokens).  
- After updating `contractiles/dust/Dustfile`, rerun `scripts/dust-hypatia.jl` (or hook it into `just dust-hypatia`) so `monitoring/reports/dust-hypatia.json` remains current and discoverable by Hypatia.  
- `monitoring/alert-trigger.sh` should run after `collect-prometheus` (already included) so Hypatia notices the failure, generates `monitoring/reports/incidents/incident-<timestamp>.json`, and includes that file in any alert comments for LLM-guided remediation.
- On alert, Hypatia will re-run and include the latest `health-*.json` snapshot; the workflow comment will capture the violating metrics.
- Run `just sanctify-analyze` (or `bash scripts/run-sanctify.sh`) before packaging so `monitoring/reports/sanctify-theme.json` and `sanctify-theme-summary.txt` are available for Hypatia/CT manifests. The script builds the local `sanctify-php` (requires Cabal/GHC access to Hackage) and targets the `wp-content/themes/sinople` tree.
