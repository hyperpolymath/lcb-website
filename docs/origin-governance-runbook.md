# Origin Governance Runbook for NUJ LCB

This site now treats the WordPress origin as the canonical decision point for bot consent and capability checks.

## What must exist on the live site

- `wp-content/mu-plugins/origin-governance-gateway.php`
- `.well-known/aibdp.json`
- `SINOPLE_CAPABILITY_SECRET`
- `SINOPLE_CAPABILITY_MODE` set to `enforce` unless you are doing a temporary report-only test
- `templates/wp-config-security.php` constants applied in `wp-config.php`
- `templates/htaccess-well-known` applied if you want Apache/LiteSpeed fast-fail behavior before WordPress

## Verpex provisioning

Add the security template contents from [templates/wp-config-security.php](/var/mnt/eclipse/repos/lcb-website/templates/wp-config-security.php) to `wp-config.php`, then add this immediately above `/* That's all, stop editing! Happy publishing. */`:

```php
$sinople_capability_secret = 'REPLACE_WITH_A_LONG_RANDOM_SECRET';

putenv('SINOPLE_CAPABILITY_SECRET=' . $sinople_capability_secret);
$_ENV['SINOPLE_CAPABILITY_SECRET'] = $sinople_capability_secret;
$_SERVER['SINOPLE_CAPABILITY_SECRET'] = $sinople_capability_secret;

putenv('SINOPLE_CAPABILITY_MODE=enforce');
$_ENV['SINOPLE_CAPABILITY_MODE'] = 'enforce';
$_SERVER['SINOPLE_CAPABILITY_MODE'] = 'enforce';
```

Use at least 64 random characters for the secret. Keep it only in the live server copy of `wp-config.php` or another private runtime secret source.

## Token generation

Generate a write token from a repo checkout on any trusted machine:

```bash
cd /var/mnt/eclipse/repos/lcb-website

SINOPLE_CAPABILITY_SECRET='your-live-secret' \
php scripts/generate-capability-token.php \
  --cap=content.write \
  --method=POST \
  --path=/wp-json/wp/v2/posts
```

Add `--json` if you want to inspect the payload as well as the token.

## Live verification

Run the full probe suite:

```bash
cd /var/mnt/eclipse/repos/lcb-website

SINOPLE_CAPABILITY_SECRET='your-live-secret' \
bash scripts/test-origin-governance.sh https://nuj-lcb.org.uk
```

Expected outcomes:

- `/.well-known/aibdp.json` returns `200`
- a bot-like request without consent returns `430`
- consent without purpose returns `400`
- invalid purpose returns `403`
- valid AIBDP headers escape the governance gate
- a state-changing API write without a capability token returns `403` with `missing_capability_token`
- the same write with a valid token escapes the governance gate and is then handled by normal WordPress/app auth

## Cloudflare rule

Keep `enable_consent_gate=false` and `enable_capability_gate=false` until the origin probe passes cleanly. If you later enable a Cloudflare worker, treat it as a prefilter only; it must mirror the origin policy instead of becoming the only enforcement point.
