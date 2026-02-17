#!/usr/bin/env bash
# SPDX-License-Identifier: PMPL-1.0-or-later
#
# Publish a static site snapshot to IPFS via Pinata and update Cloudflare DNSLink.
# Usage:
#   bash scripts/ipfs-publish.sh [path/to/html]
#
# Reads credentials from .env.local by default.

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT_DIR/.env.local}"
INPUT_FILE="${1:-$ROOT_DIR/content/nuj-lcb-shareable-site.html}"

# Preserve incoming environment values so placeholders in .env.local
# do not overwrite explicit variables passed at runtime.
incoming_cloudflare_api_token="${CLOUDFLARE_API_TOKEN-__UNSET__}"
incoming_cloudflare_zone_id="${CLOUDFLARE_ZONE_ID-__UNSET__}"
incoming_pinata_jwt="${PINATA_JWT-__UNSET__}"
incoming_pinata_api_key="${PINATA_API_KEY-__UNSET__}"
incoming_pinata_api_secret="${PINATA_API_SECRET-__UNSET__}"
incoming_ipfs_host="${IPFS_HOST-__UNSET__}"
incoming_pinata_gateway_domain="${PINATA_GATEWAY_DOMAIN-__UNSET__}"

if [[ -f "$ENV_FILE" ]]; then
  # shellcheck disable=SC1090
  source "$ENV_FILE"
fi

if [[ "$incoming_cloudflare_api_token" != "__UNSET__" ]]; then CLOUDFLARE_API_TOKEN="$incoming_cloudflare_api_token"; fi
if [[ "$incoming_cloudflare_zone_id" != "__UNSET__" ]]; then CLOUDFLARE_ZONE_ID="$incoming_cloudflare_zone_id"; fi
if [[ "$incoming_pinata_jwt" != "__UNSET__" ]]; then PINATA_JWT="$incoming_pinata_jwt"; fi
if [[ "$incoming_pinata_api_key" != "__UNSET__" ]]; then PINATA_API_KEY="$incoming_pinata_api_key"; fi
if [[ "$incoming_pinata_api_secret" != "__UNSET__" ]]; then PINATA_API_SECRET="$incoming_pinata_api_secret"; fi
if [[ "$incoming_ipfs_host" != "__UNSET__" ]]; then IPFS_HOST="$incoming_ipfs_host"; fi
if [[ "$incoming_pinata_gateway_domain" != "__UNSET__" ]]; then PINATA_GATEWAY_DOMAIN="$incoming_pinata_gateway_domain"; fi

: "${CLOUDFLARE_API_TOKEN:?Missing CLOUDFLARE_API_TOKEN (set in .env.local)}"
: "${CLOUDFLARE_ZONE_ID:?Missing CLOUDFLARE_ZONE_ID (set in .env.local)}"

if [[ -z "${PINATA_JWT:-}" ]]; then
  : "${PINATA_API_KEY:?Missing PINATA_JWT or PINATA_API_KEY}"
  : "${PINATA_API_SECRET:?Missing PINATA_JWT or PINATA_API_SECRET}"
fi

IPFS_HOST="${IPFS_HOST:-ipfs.nuj-lcb.org.uk}"
PINATA_GATEWAY_DOMAIN="${PINATA_GATEWAY_DOMAIN:-}"

if [[ ! -f "$INPUT_FILE" ]]; then
  echo "Input file not found: $INPUT_FILE" >&2
  exit 1
fi

for bin in curl jq mktemp; do
  if ! command -v "$bin" >/dev/null 2>&1; then
    echo "Missing required tool: $bin" >&2
    exit 1
  fi
done

tmp_dir="$(mktemp -d)"
trap 'rm -rf "$tmp_dir"' EXIT
cp "$INPUT_FILE" "$tmp_dir/index.html"

pinata_auth_headers=()
if [[ -n "${PINATA_JWT:-}" ]]; then
  pinata_auth_headers=(-H "Authorization: Bearer $PINATA_JWT")
else
  pinata_auth_headers=(
    -H "pinata_api_key: $PINATA_API_KEY"
    -H "pinata_secret_api_key: $PINATA_API_SECRET"
  )
fi

pin_name="nuj-lcb-site-$(date -u +%Y%m%dT%H%M%SZ)"
pin_response="$(
  curl -sS -X POST "https://api.pinata.cloud/pinning/pinFileToIPFS" \
    "${pinata_auth_headers[@]}" \
    -F "file=@$tmp_dir/index.html;filename=index.html" \
    -F "pinataMetadata={\"name\":\"$pin_name\"}" \
    -F "pinataOptions={\"cidVersion\":1}"
)"

cid="$(jq -r '.IpfsHash // empty' <<<"$pin_response")"
if [[ -z "$cid" ]]; then
  echo "Failed to pin file to IPFS. Response:" >&2
  echo "$pin_response" >&2
  exit 1
fi

dns_name="_dnslink.$IPFS_HOST"
dns_content="dnslink=/ipfs/$cid"

query_response="$(
  curl -sS \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    "https://api.cloudflare.com/client/v4/zones/$CLOUDFLARE_ZONE_ID/dns_records?type=TXT&name=$dns_name"
)"

record_id="$(jq -r '.result[0].id // empty' <<<"$query_response")"

if [[ -n "$record_id" ]]; then
  dns_response="$(
    curl -sS -X PUT \
      -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
      -H "Content-Type: application/json" \
      "https://api.cloudflare.com/client/v4/zones/$CLOUDFLARE_ZONE_ID/dns_records/$record_id" \
      --data "{\"type\":\"TXT\",\"name\":\"$dns_name\",\"content\":\"$dns_content\",\"ttl\":120}"
  )"
else
  dns_response="$(
    curl -sS -X POST \
      -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
      -H "Content-Type: application/json" \
      "https://api.cloudflare.com/client/v4/zones/$CLOUDFLARE_ZONE_ID/dns_records" \
      --data "{\"type\":\"TXT\",\"name\":\"$dns_name\",\"content\":\"$dns_content\",\"ttl\":120}"
  )"
fi

if [[ "$(jq -r '.success // false' <<<"$dns_response")" != "true" ]]; then
  echo "Failed to update DNSLink. Response:" >&2
  echo "$dns_response" >&2
  exit 1
fi

echo "IPFS publish complete."
echo "CID: $cid"
echo "DNSLink: $dns_name -> $dns_content"
echo "IPNS URL: https://ipfs.io/ipns/$IPFS_HOST/"
echo "Direct CID URL: https://ipfs.io/ipfs/$cid"
if [[ -n "$PINATA_GATEWAY_DOMAIN" ]]; then
  echo "Pinata gateway hint: https://$PINATA_GATEWAY_DOMAIN/ipfs/$cid"
fi
