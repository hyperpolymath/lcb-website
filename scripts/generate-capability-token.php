#!/usr/bin/env php
<?php
/**
 * SPDX-License-Identifier: PMPL-1.0-or-later
 * SPDX-FileCopyrightText: 2026 Jonathan D.A. Jewell (hyperpolymath) <j.d.a.jewell@open.ac.uk>
 *
 * Generate an HS256 capability JWT for the Sinople origin governance gateway.
 */

declare(strict_types=1);

function usage(int $exitCode = 0, string $message = ''): void {
    $help = <<<TXT
Usage:
  php scripts/generate-capability-token.php [options]

Options:
  --secret=VALUE        Capability secret. Falls back to SINOPLE_CAPABILITY_SECRET.
  --cap=VALUE           Capability claim. Repeatable. Default: content.write
  --method=VALUE        HTTP method allowed by the token. Repeatable. Default: POST
  --path=VALUE          Path pattern allowed by the token. Repeatable. Default: /wp-json/wp/v2/posts
  --subject=VALUE       Subject claim. Default: origin-governance-test
  --issuer=VALUE        Issuer claim. Default: sinople-origin-governance
  --ttl=SECONDS         Token lifetime in seconds. Default: 3600
  --not-before=SECONDS  Delay validity by N seconds. Default: 0
  --json                Emit JSON with token + payload instead of token only.
  --help                Show this help.
TXT;

    if ('' !== $message) {
        fwrite(STDERR, $message . PHP_EOL . PHP_EOL);
    }

    $stream = 0 === $exitCode ? STDOUT : STDERR;
    fwrite($stream, $help . PHP_EOL);
    exit($exitCode);
}

/**
 * @param mixed $value
 * @return list<string>
 */
function option_list($value, array $default): array {
    if (null === $value) {
        return $default;
    }

    if (!is_array($value)) {
        $value = array($value);
    }

    $values = array_values(array_filter(array_map(
        static fn($item): string => trim((string) $item),
        $value
    )));

    return array() === $values ? $default : $values;
}

function base64url_encode(string $value): string {
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

/**
 * @param array<string, mixed> $header
 * @param array<string, mixed> $payload
 */
function encode_jwt(array $header, array $payload, string $secret): string {
    $headerSegment = base64url_encode(json_encode($header, JSON_UNESCAPED_SLASHES));
    $payloadSegment = base64url_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
    $signature = hash_hmac('sha256', $headerSegment . '.' . $payloadSegment, $secret, true);

    return $headerSegment . '.' . $payloadSegment . '.' . base64url_encode($signature);
}

$options = getopt('', array(
    'secret:',
    'cap:',
    'method:',
    'path:',
    'subject:',
    'issuer:',
    'ttl:',
    'not-before:',
    'json',
    'help',
));

if (false === $options) {
    usage(1, 'Failed to parse command-line options.');
}

if (isset($options['help'])) {
    usage();
}

$secret = isset($options['secret']) ? trim((string) $options['secret']) : trim((string) getenv('SINOPLE_CAPABILITY_SECRET'));
if ('' === $secret) {
    usage(1, 'Missing capability secret. Supply --secret=... or SINOPLE_CAPABILITY_SECRET.');
}

$caps = option_list($options['cap'] ?? null, array('content.write'));
$methods = array_map('strtoupper', option_list($options['method'] ?? null, array('POST')));
$paths = option_list($options['path'] ?? null, array('/wp-json/wp/v2/posts'));
$ttl = max(1, (int) ($options['ttl'] ?? 3600));
$notBeforeDelay = max(0, (int) ($options['not-before'] ?? 0));
$subject = trim((string) ($options['subject'] ?? 'origin-governance-test'));
$issuer = trim((string) ($options['issuer'] ?? 'sinople-origin-governance'));

$now = time();
$payload = array(
    'iss' => '' === $issuer ? 'sinople-origin-governance' : $issuer,
    'sub' => '' === $subject ? 'origin-governance-test' : $subject,
    'iat' => $now,
    'nbf' => $now + $notBeforeDelay,
    'exp' => $now + $ttl,
    'capabilities' => $caps,
    'methods' => $methods,
    'paths' => $paths,
);

$token = encode_jwt(
    array(
        'alg' => 'HS256',
        'typ' => 'JWT',
    ),
    $payload,
    $secret
);

if (isset($options['json'])) {
    echo json_encode(
        array(
            'token' => $token,
            'payload' => $payload,
        ),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    ) . PHP_EOL;
    exit(0);
}

echo $token . PHP_EOL;
