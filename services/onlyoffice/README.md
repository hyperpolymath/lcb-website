# Office — office.nuj-lcb.org.uk

## Quick Start

```bash
# 1. Generate a JWT secret
export ONLYOFFICE_JWT_SECRET="$(openssl rand -hex 32)"
echo "ONLYOFFICE_JWT_SECRET=${ONLYOFFICE_JWT_SECRET}" > .env

# 2. Start the stack
podman-compose -f services/onlyoffice/onlyoffice-compose.yml up -d

# 3. Verify health
curl -sf https://office.nuj-lcb.org.uk/healthcheck

# 4. Configure WordPress plugin (see below)
```

## DNS

Add a Cloudflare DNS record:
- **Type:** A (or CNAME if using a proxy)
- **Name:** office
- **Content:** container host IP
- **Proxy:** Orange cloud (proxied)

## WordPress Integration

1. Install the ONLYOFFICE plugin: `wp plugin install onlyoffice --activate`
2. Go to Settings > ONLYOFFICE in wp-admin
3. Set Document Server URL: `https://office.nuj-lcb.org.uk`
4. Set JWT Secret: same value as `ONLYOFFICE_JWT_SECRET`
5. Save and test by creating a document from the Media Library

## Architecture

```
WordPress (nuj-lcb.org.uk)
    |
    | HTTPS + JWT auth
    v
Caddy (office.nuj-lcb.org.uk:443)
    |
    | HTTP proxy
    v
Office DS (container:8080)
    |
    +-- PostgreSQL (embedded)
    +-- Data volume (documents)
    +-- Logs volume
```

## Updating

```bash
podman-compose -f services/onlyoffice/onlyoffice-compose.yml pull
podman-compose -f services/onlyoffice/onlyoffice-compose.yml up -d
```
