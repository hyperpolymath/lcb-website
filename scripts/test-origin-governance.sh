#!/usr/bin/env bash
# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <j.d.a.jewell@open.ac.uk>
#
# Probe the live origin-governance behavior for the site.

set -euo pipefail

SITE_URL="${SITE_URL:-${1:-https://nuj-lcb.org.uk}}"
WRITE_PATH="${WRITE_PATH:-/wp-json/wp/v2/posts}"
BOT_UA="${BOT_UA:-Codex-Origin-Governance-TestBot/1.0}"
ALLOWED_PURPOSE="${ALLOWED_PURPOSE:-security-audit}"
INVALID_PURPOSE="${INVALID_PURPOSE:-training}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TMP_DIR="$(mktemp -d)"

cleanup() {
    rm -rf "${TMP_DIR}"
}
trap cleanup EXIT

fail() {
    echo "FAIL: $*" >&2
    exit 1
}

pass() {
    echo "PASS: $*"
}

request() {
    local name="$1"
    shift
    local body_file="${TMP_DIR}/${name}.body"
    local header_file="${TMP_DIR}/${name}.headers"
    curl -sS -L -D "${header_file}" -o "${body_file}" -w '%{http_code}' "$@"
}

body_contains() {
    local name="$1"
    local pattern="$2"
    grep -Eq "${pattern}" "${TMP_DIR}/${name}.body"
}

header_contains() {
    local name="$1"
    local pattern="$2"
    grep -Eqi "${pattern}" "${TMP_DIR}/${name}.headers"
}

echo "Testing origin governance for ${SITE_URL}"

status="$(request policy "${SITE_URL}/.well-known/aibdp.json")"
[ "${status}" = "200" ] || fail "Expected /.well-known/aibdp.json to return 200, got ${status}"
pass "AIBDP policy is reachable"

status="$(request bot_no_consent -A "${BOT_UA}" "${SITE_URL}/")"
[ "${status}" = "430" ] || fail "Expected bot request without consent to return 430, got ${status}"
header_contains bot_no_consent '^Preference-Required: consent-aware-http' || fail "430 response missing Preference-Required header"
pass "Bot request without consent is rejected with 430"

status="$(request bot_missing_purpose -A "${BOT_UA}" -H 'X-AIBDP-Consent: accepted' "${SITE_URL}/")"
[ "${status}" = "400" ] || fail "Expected consent without purpose to return 400, got ${status}"
body_contains bot_missing_purpose 'aibdp_purpose_required' || fail "400 response missing aibdp_purpose_required code"
pass "Consent without purpose is rejected with 400"

status="$(request bot_invalid_purpose -A "${BOT_UA}" -H 'X-AIBDP-Consent: accepted' -H "X-AIBDP-Purpose: ${INVALID_PURPOSE}" "${SITE_URL}/")"
[ "${status}" = "403" ] || fail "Expected invalid purpose to return 403, got ${status}"
body_contains bot_invalid_purpose 'invalid_aibdp_purpose' || fail "403 response missing invalid_aibdp_purpose code"
pass "Invalid AIBDP purpose is rejected with 403"

status="$(request bot_allowed -A "${BOT_UA}" -H 'X-AIBDP-Consent: accepted' -H "X-AIBDP-Purpose: ${ALLOWED_PURPOSE}" "${SITE_URL}/")"
case "${status}" in
    430|400|403)
        fail "Expected allowed bot request to escape the origin governance gate, got ${status}"
        ;;
    *)
        pass "Allowed AIBDP request escaped the governance gate (HTTP ${status})"
        ;;
esac

status="$(request write_missing_token -X POST -A "${BOT_UA}" -H 'Content-Type: application/json' -H 'X-AIBDP-Consent: accepted' -H "X-AIBDP-Purpose: ${ALLOWED_PURPOSE}" --data '{"title":"Origin Governance Probe"}' "${SITE_URL}${WRITE_PATH}")"
[ "${status}" = "403" ] || fail "Expected state-changing API write without token to return 403, got ${status}"
body_contains write_missing_token 'missing_capability_token' || fail "403 write response missing missing_capability_token code"
pass "State-changing API write without token is rejected"

if [ -n "${SINOPLE_CAPABILITY_SECRET:-}" ]; then
    token="$(
        SINOPLE_CAPABILITY_SECRET="${SINOPLE_CAPABILITY_SECRET}" \
        php "${SCRIPT_DIR}/generate-capability-token.php" \
            --cap=content.write \
            --method=POST \
            --path="${WRITE_PATH}" \
            --subject=origin-governance-test
    )"

    status="$(request write_signed -X POST -A "${BOT_UA}" -H 'Content-Type: application/json' -H 'X-AIBDP-Consent: accepted' -H "X-AIBDP-Purpose: ${ALLOWED_PURPOSE}" -H "X-Capability-Token: ${token}" --data '{"title":"Origin Governance Probe"}' "${SITE_URL}${WRITE_PATH}")"

    if body_contains write_signed 'missing_capability_token|invalid_capability_signature|missing_required_capability|capability_method_mismatch|capability_path_mismatch|capability_secret_not_configured'; then
        fail "Signed write request is still being rejected by the origin governance gate"
    fi

    pass "Signed write request escaped the governance gate (HTTP ${status}); remaining response should come from WordPress/app auth"
else
    echo "SKIP: signed token probe not run because SINOPLE_CAPABILITY_SECRET is not set in the shell"
fi
