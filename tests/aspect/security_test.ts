// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Security aspect tests for cross-cutting security concerns.
// Tests XSS prevention, header injection prevention, and access control.

import { assertEquals, assert, assertFalse } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Aspect: CSP Bypass Prevention
function testCSPBypassPrevention(csp: string): boolean {
  const unsafePatterns = [
    "'unsafe-inline'",
    "'unsafe-eval'",
    "data:",
    "blob:",
  ];

  for (const pattern of unsafePatterns) {
    if (csp.includes(pattern)) {
      return false;
    }
  }

  return true;
}

// Aspect: Header Injection Prevention
function testHeaderInjectionPrevention(headerValue: string): boolean {
  // Header values must not contain newline characters (CRLF injection)
  // Also check for null bytes and other control characters
  const dangerousChars = ["\n", "\r", "\0", "\x1a"];

  for (const char of dangerousChars) {
    if (headerValue.includes(char)) {
      return false;
    }
  }

  return true;
}

// Aspect: XSS Prevention in Navigation
function testNavXSSPrevention(navHTML: string): boolean {
  // Check for unescaped HTML entities in nav items
  const xssPatterns = [
    "<script",
    "javascript:",
    "onerror=",
    "onload=",
    "onclick=",
  ];

  for (const pattern of xssPatterns) {
    if (navHTML.toLowerCase().includes(pattern)) {
      return false;
    }
  }

  return true;
}

// Aspect: Proper HTML Entity Escaping
function testHTMLEntityEscaping(userInput: string, escaped: string): boolean {
  if (userInput.includes("<")) {
    if (!escaped.includes("&lt;")) {
      return false;
    }
  }

  if (userInput.includes(">")) {
    if (!escaped.includes("&gt;")) {
      return false;
    }
  }

  if (userInput.includes('"')) {
    if (!escaped.includes("&quot;")) {
      return false;
    }
  }

  if (userInput.includes("&")) {
    if (!escaped.includes("&amp;")) {
      return false;
    }
  }

  return true;
}

// Aspect: Authentication Token Security
function testTokenSecurity(token: string): boolean {
  // Token must be:
  // 1. Non-empty
  // 2. Sufficiently long (>= 32 chars)
  // 3. No spaces or newlines
  // 4. Ideally base64 or hex encoded

  if (token.length === 0) {
    return false;
  }

  if (token.length < 32) {
    return false;
  }

  if (token.includes(" ") || token.includes("\n")) {
    return false;
  }

  // Check if it looks like a valid token (alphanumeric, dash, underscore, etc.)
  return /^[a-zA-Z0-9_\-\.]+$/.test(token);
}

// Aspect: URL Scheme Validation
function testURLSchemeValidation(url: string): boolean {
  // Only allow http and https schemes
  if (!url.startsWith("http://") && !url.startsWith("https://")) {
    return false;
  }

  return true;
}

// Aspect: Path Traversal Prevention
function testPathTraversalPrevention(path: string): boolean {
  // Block directory traversal attempts
  if (path.includes("..")) {
    return false;
  }

  if (path.includes(".\\")) {
    return false;
  }

  // Block null bytes
  if (path.includes("\0")) {
    return false;
  }

  return true;
}

// Aspect: CORS Policy Enforcement
function testCORSPolicy(corsHeader: string, allowedOrigins: string[]): boolean {
  // Parse CORS header (simplified)
  if (corsHeader === "*") {
    // Wildcard is only safe for public APIs without credentials
    return true;
  }

  // For specific origins, verify they're in allowlist
  const origins = corsHeader.split(",").map((s) => s.trim());

  for (const origin of origins) {
    if (!allowedOrigins.includes(origin)) {
      return false;
    }
  }

  return true;
}

// Aspect: Rate Limiting
function testRateLimitingAspect(
  requestTimestamps: number[],
  windowMs: number = 60000,
  maxRequests: number = 10
): boolean {
  if (requestTimestamps.length === 0) {
    return true;
  }

  // Check if we exceed max requests in window
  const now = requestTimestamps[requestTimestamps.length - 1];
  const recentRequests = requestTimestamps.filter((ts) => now - ts < windowMs);

  return recentRequests.length <= maxRequests;
}

Deno.test("Security: CSP Bypass - unsafe-inline rejected", () => {
  const csp = "default-src 'self' 'unsafe-inline'";
  assertEquals(testCSPBypassPrevention(csp), false);
});

Deno.test("Security: CSP Bypass - unsafe-eval rejected", () => {
  const csp = "script-src 'self' 'unsafe-eval'";
  assertEquals(testCSPBypassPrevention(csp), false);
});

Deno.test("Security: CSP Bypass - data: scheme rejected", () => {
  const csp = "default-src data: blob:";
  assertEquals(testCSPBypassPrevention(csp), false);
});

Deno.test("Security: CSP Bypass - Safe CSP passes", () => {
  const csp = "default-src 'self'; script-src 'nonce-abc123'";
  assertEquals(testCSPBypassPrevention(csp), true);
});

Deno.test("Security: Header Injection - CRLF rejected", () => {
  const headerValue = "value\r\nX-Injected: malicious";
  assertEquals(testHeaderInjectionPrevention(headerValue), false);
});

Deno.test("Security: Header Injection - Newline rejected", () => {
  const headerValue = "value\nX-Injected: malicious";
  assertEquals(testHeaderInjectionPrevention(headerValue), false);
});

Deno.test("Security: Header Injection - Null byte rejected", () => {
  const headerValue = "value\0malicious";
  assertEquals(testHeaderInjectionPrevention(headerValue), false);
});

Deno.test("Security: Header Injection - Clean value passes", () => {
  const headerValue = "max-age=31536000; includeSubDomains";
  assertEquals(testHeaderInjectionPrevention(headerValue), true);
});

Deno.test("Security: XSS in Navigation - Script tag rejected", () => {
  const nav = '<a href="/page"><script>alert("xss")</script></a>';
  assertEquals(testNavXSSPrevention(nav), false);
});

Deno.test("Security: XSS in Navigation - Event handler rejected", () => {
  const nav = '<a href="/page" onerror="alert(\'xss\')">Link</a>';
  assertEquals(testNavXSSPrevention(nav), false);
});

Deno.test("Security: XSS in Navigation - javascript: rejected", () => {
  const nav = '<a href="javascript:alert(\'xss\')">Link</a>';
  assertEquals(testNavXSSPrevention(nav), false);
});

Deno.test("Security: XSS in Navigation - Safe nav passes", () => {
  const nav = '<a href="/page">Safe Link</a>';
  assertEquals(testNavXSSPrevention(nav), true);
});

Deno.test("Security: HTML Entity Escaping - Angle brackets", () => {
  const input = "<script>";
  const escaped = "&lt;script&gt;";
  assertEquals(testHTMLEntityEscaping(input, escaped), true);
});

Deno.test("Security: HTML Entity Escaping - Quotes", () => {
  const input = 'He said "hello"';
  const escaped = "He said &quot;hello&quot;";
  assertEquals(testHTMLEntityEscaping(input, escaped), true);
});

Deno.test("Security: HTML Entity Escaping - Ampersand", () => {
  const input = "Tom & Jerry";
  const escaped = "Tom &amp; Jerry";
  assertEquals(testHTMLEntityEscaping(input, escaped), true);
});

Deno.test("Security: Token Security - Long token", () => {
  const token = "abcd1234efgh5678ijkl9012mnop3456";
  assertEquals(testTokenSecurity(token), true);
});

Deno.test("Security: Token Security - Too short", () => {
  const token = "shorttoken";
  assertEquals(testTokenSecurity(token), false);
});

Deno.test("Security: Token Security - Contains spaces", () => {
  const token = "abcd 1234 efgh 5678 ijkl 9012 mnop";
  assertEquals(testTokenSecurity(token), false);
});

Deno.test("Security: Token Security - Contains newline", () => {
  const token = "abcd1234efgh5678\nijkl9012mnop3456";
  assertEquals(testTokenSecurity(token), false);
});

Deno.test("Security: URL Scheme - HTTPS valid", () => {
  const url = "https://example.com/page";
  assertEquals(testURLSchemeValidation(url), true);
});

Deno.test("Security: URL Scheme - HTTP valid", () => {
  const url = "http://example.com/page";
  assertEquals(testURLSchemeValidation(url), true);
});

Deno.test("Security: URL Scheme - javascript: rejected", () => {
  const url = "javascript:alert('xss')";
  assertEquals(testURLSchemeValidation(url), false);
});

Deno.test("Security: URL Scheme - data: rejected", () => {
  const url = "data:text/html,<script>alert('xss')</script>";
  assertEquals(testURLSchemeValidation(url), false);
});

Deno.test("Security: Path Traversal - Directory traversal rejected", () => {
  const path = "../../etc/passwd";
  assertEquals(testPathTraversalPrevention(path), false);
});

Deno.test("Security: Path Traversal - Safe path passes", () => {
  const path = "/uploads/image.jpg";
  assertEquals(testPathTraversalPrevention(path), true);
});

Deno.test("Security: Path Traversal - Null byte rejected", () => {
  const path = "/file\0.jpg";
  assertEquals(testPathTraversalPrevention(path), false);
});

Deno.test("Security: CORS - Specific origin in allowlist", () => {
  const corsHeader = "https://example.com";
  const allowlist = ["https://example.com", "https://trusted.com"];
  assertEquals(testCORSPolicy(corsHeader, allowlist), true);
});

Deno.test("Security: CORS - Origin not in allowlist", () => {
  const corsHeader = "https://evil.com";
  const allowlist = ["https://example.com"];
  assertEquals(testCORSPolicy(corsHeader, allowlist), false);
});

Deno.test("Security: Rate Limiting - Within limits", () => {
  const now = Date.now();
  const timestamps = [now - 50000, now - 40000, now - 30000, now];
  assertEquals(testRateLimitingAspect(timestamps, 60000, 10), true);
});

Deno.test("Security: Rate Limiting - Exceeds limit", () => {
  const now = Date.now();
  const timestamps = Array.from({ length: 15 }, (_, i) => now - (14 - i) * 1000);
  assertEquals(testRateLimitingAspect(timestamps, 60000, 10), false);
});
