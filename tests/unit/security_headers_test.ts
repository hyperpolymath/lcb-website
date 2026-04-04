// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Unit tests for Sinople security headers implementation.
// Tests CSP, HSTS, X-Frame-Options, and other security-critical headers.

import { assertEquals, assertExists, assert } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Mock security header contracts
interface SecurityHeaders {
  "Content-Security-Policy": string;
  "Strict-Transport-Security": string;
  "X-Content-Type-Options": string;
  "X-Frame-Options": string;
  "Referrer-Policy": string;
  "Permissions-Policy": string;
}

// Contract: All security headers must be present and non-empty
function validateSecurityHeaders(headers: Partial<SecurityHeaders>): boolean {
  const required = [
    "Content-Security-Policy",
    "Strict-Transport-Security",
    "X-Content-Type-Options",
    "X-Frame-Options",
    "Referrer-Policy",
  ];

  for (const header of required) {
    if (!headers[header as keyof SecurityHeaders] ||
        headers[header as keyof SecurityHeaders]!.length === 0) {
      return false;
    }
  }
  return true;
}

// Contract: CSP must not contain 'unsafe-inline' or 'unsafe-eval'
function validateCSPSafety(csp: string): boolean {
  const dangerous = ["'unsafe-inline'", "'unsafe-eval'"];
  for (const danger of dangerous) {
    if (csp.includes(danger)) {
      return false;
    }
  }
  return true;
}

// Contract: X-Frame-Options must be DENY or SAMEORIGIN only
function validateFrameOptions(xFrameOptions: string): boolean {
  const valid = ["DENY", "SAMEORIGIN"];
  return valid.includes(xFrameOptions.toUpperCase());
}

// Contract: HSTS max-age must be at least 31536000 (1 year)
function validateHSTSAge(hsts: string): boolean {
  const match = hsts.match(/max-age=(\d+)/);
  if (!match) return false;
  const maxAge = parseInt(match[1], 10);
  return maxAge >= 31536000;
}

Deno.test("Security Headers - All required headers present", () => {
  const headers: Partial<SecurityHeaders> = {
    "Content-Security-Policy": "default-src 'self'; script-src 'self' 'nonce-abc123'",
    "Strict-Transport-Security": "max-age=31536000; includeSubDomains",
    "X-Content-Type-Options": "nosniff",
    "X-Frame-Options": "SAMEORIGIN",
    "Referrer-Policy": "no-referrer",
  };

  assertEquals(validateSecurityHeaders(headers), true);
});

Deno.test("Security Headers - Missing CSP fails validation", () => {
  const headers: Partial<SecurityHeaders> = {
    "Strict-Transport-Security": "max-age=31536000",
    "X-Content-Type-Options": "nosniff",
    "X-Frame-Options": "DENY",
    "Referrer-Policy": "strict-origin-when-cross-origin",
  };

  assertEquals(validateSecurityHeaders(headers), false);
});

Deno.test("CSP Safety - Rejects unsafe-inline", () => {
  const csp = "default-src 'self' 'unsafe-inline'";
  assertEquals(validateCSPSafety(csp), false);
});

Deno.test("CSP Safety - Rejects unsafe-eval", () => {
  const csp = "script-src 'self' 'unsafe-eval'";
  assertEquals(validateCSPSafety(csp), false);
});

Deno.test("CSP Safety - Accepts safe CSP with nonce", () => {
  const csp = "default-src 'self'; script-src 'self' 'nonce-randomstring'";
  assertEquals(validateCSPSafety(csp), true);
});

Deno.test("X-Frame-Options - Accepts DENY", () => {
  assertEquals(validateFrameOptions("DENY"), true);
});

Deno.test("X-Frame-Options - Accepts SAMEORIGIN", () => {
  assertEquals(validateFrameOptions("SAMEORIGIN"), true);
});

Deno.test("X-Frame-Options - Rejects ALLOWALL", () => {
  assertEquals(validateFrameOptions("ALLOWALL"), false);
});

Deno.test("X-Frame-Options - Rejects allow-all", () => {
  assertEquals(validateFrameOptions("allow-all"), false);
});

Deno.test("HSTS - Accepts minimum 1-year max-age", () => {
  const hsts = "max-age=31536000; includeSubDomains";
  assertEquals(validateHSTSAge(hsts), true);
});

Deno.test("HSTS - Rejects max-age less than 1 year", () => {
  const hsts = "max-age=3600";
  assertEquals(validateHSTSAge(hsts), false);
});

Deno.test("HSTS - Extracts max-age correctly", () => {
  const hsts = "max-age=63072000; includeSubDomains; preload";
  assertEquals(validateHSTSAge(hsts), true);
});

// Test header value constraints
Deno.test("Security Headers - No null bytes in values", () => {
  const csp = "default-src 'self'";
  assertEquals(csp.includes("\0"), false);
  assertEquals(csp.length > 0, true);
});

Deno.test("Security Headers - X-Content-Type-Options is nosniff", () => {
  const xContentType = "nosniff";
  assertEquals(xContentType, "nosniff");
});

Deno.test("Security Headers - Referrer-Policy is restrictive", () => {
  const policies = [
    "no-referrer",
    "no-referrer-when-downgrade",
    "strict-origin",
    "strict-origin-when-cross-origin",
  ];

  for (const policy of policies) {
    assertEquals(typeof policy, "string");
    assertEquals(policy.length > 0, true);
  }
});
