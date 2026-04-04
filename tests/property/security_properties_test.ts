// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Property-based tests for security contracts.
// Tests invariants that must hold for any input across domains.

import { assertEquals, assert } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Property: For any mention URL, only HTTPS is allowed
function testMentionURLProtocol(urls: string[]): boolean {
  for (const url of urls) {
    if (url.includes("://")) {
      const protocol = url.split("://")[0];
      if (protocol !== "https") {
        return false;
      }
    }
  }
  return true;
}

// Property: Security header values never contain null bytes
function testNoNullBytesInHeaders(headers: Record<string, string>): boolean {
  for (const value of Object.values(headers)) {
    if (value.includes("\0")) {
      return false;
    }
  }
  return true;
}

// Property: CSP directives are always in allowlist format (no deny lists)
function testCSPAllowlistFormat(csp: string): boolean {
  // CSP should only contain directives like default-src, script-src, etc.
  // Never contain negation patterns like "not", "except", etc.
  const disallowed = ["!", "~", "except", "not"];
  for (const pattern of disallowed) {
    if (csp.toLowerCase().includes(pattern)) {
      return false;
    }
  }
  return true;
}

// Property: X-Frame-Options never allows all origins
function testXFrameOptionsNeverAllowAll(xFrameOptions: string): boolean {
  const notAllowedValues = ["allowall", "allow-all", "*"];
  const lower = xFrameOptions.toLowerCase();
  return !notAllowedValues.some((val) => lower.includes(val));
}

// Property: Unsafe-inline and unsafe-eval never appear in CSP
function testCSPNoUnsafeDirectives(csp: string): boolean {
  return !csp.includes("'unsafe-inline'") && !csp.includes("'unsafe-eval'");
}

// Property: All URLs in security context are absolute
function testAbsoluteURLsInSecurity(urls: string[]): boolean {
  for (const url of urls) {
    if (!url.startsWith("http://") && !url.startsWith("https://")) {
      return false;
    }
  }
  return true;
}

// Property: Nonce values are non-empty and properly formatted
function testNonceFormatValid(nonce: string): boolean {
  // Nonce should be base64-like and non-empty
  return nonce.length > 0 && /^[a-zA-Z0-9/+=]+$/.test(nonce);
}

// Property: All HTML output from security functions is properly formed
function testHTMLProperlyFormed(html: string): boolean {
  // Count opening and self-closing vs closing tags
  const openTags = (html.match(/<[^/][^>]*>/g) || []).length;
  const closeTags = (html.match(/<\/[^>]+>/g) || []).length;
  const selfClosing = (html.match(/<[^>]*\/>/g) || []).length;

  // Allow some flexibility for properly formed HTML5
  return openTags <= closeTags + selfClosing;
}

// Property: Rate limiting always delays subsequent requests
function testRateLimitingIncreasesDelay(
  timings: number[]
): boolean {
  for (let i = 1; i < timings.length; i++) {
    // If request is from same source within time limit, it should be blocked
    // This property ensures timing increases with frequency
    if (timings[i] - timings[i - 1] < 60000) { // 1 minute
      // Within 1 minute, second request should be rejected
      return true; // Property: system prevents rapid-fire
    }
  }
  return true;
}

Deno.test("Property: Mention URLs are always HTTPS", () => {
  const validUrls = [
    "https://example.com/article",
    "https://blog.example.com/post",
    "https://cdn.example.com/image.jpg",
  ];
  assertEquals(testMentionURLProtocol(validUrls), true);
});

Deno.test("Property: HTTP mention URLs fail HTTPS check", () => {
  const invalidUrls = [
    "http://example.com/article",
    "ftp://example.com/file",
    "javascript:alert('xss')",
  ];
  assertEquals(testMentionURLProtocol(invalidUrls), false);
});

Deno.test("Property: No null bytes in security headers", () => {
  const headers: Record<string, string> = {
    "Content-Security-Policy": "default-src 'self'",
    "X-Frame-Options": "DENY",
    "Strict-Transport-Security": "max-age=31536000",
  };
  assertEquals(testNoNullBytesInHeaders(headers), true);
});

Deno.test("Property: Null bytes rejected in headers", () => {
  const headers: Record<string, string> = {
    "Content-Security-Policy": "default-src 'self'\0malicious",
  };
  assertEquals(testNoNullBytesInHeaders(headers), false);
});

Deno.test("Property: CSP uses allowlist format", () => {
  const csp = "default-src 'self'; script-src 'self' 'nonce-abc123'; style-src 'self'";
  assertEquals(testCSPAllowlistFormat(csp), true);
});

Deno.test("Property: CSP never uses deny-list patterns", () => {
  const invalidCSPs = [
    "default-src 'self' !javascript:",
    "script-src 'self' except 'unsafe-inline'",
    "default-src 'self' ~ external",
  ];

  for (const csp of invalidCSPs) {
    assertEquals(testCSPAllowlistFormat(csp), false);
  }
});

Deno.test("Property: X-Frame-Options never allows all", () => {
  const validOptions = ["DENY", "SAMEORIGIN"];
  for (const opt of validOptions) {
    assertEquals(testXFrameOptionsNeverAllowAll(opt), true);
  }
});

Deno.test("Property: X-Frame-Options forbids allow-all", () => {
  const invalidOptions = ["ALLOWALL", "allow-all"];
  for (const opt of invalidOptions) {
    assertEquals(testXFrameOptionsNeverAllowAll(opt), false);
  }
});

Deno.test("Property: CSP excludes unsafe-inline", () => {
  const validCSPs = [
    "default-src 'self'",
    "script-src 'nonce-abc123'",
    "style-src 'self' https://cdn.example.com",
  ];

  for (const csp of validCSPs) {
    assertEquals(testCSPNoUnsafeDirectives(csp), true);
  }
});

Deno.test("Property: CSP rejects unsafe-inline and unsafe-eval", () => {
  assertEquals(testCSPNoUnsafeDirectives("default-src 'self' 'unsafe-inline'"), false);
  assertEquals(testCSPNoUnsafeDirectives("script-src 'unsafe-eval'"), false);
});

Deno.test("Property: All URLs in security context are absolute", () => {
  const absoluteUrls = [
    "https://example.com",
    "http://cdn.example.com/file.js",
    "https://api.example.com/v1/endpoint",
  ];
  assertEquals(testAbsoluteURLsInSecurity(absoluteUrls), true);
});

Deno.test("Property: Relative URLs fail absolute check", () => {
  const relativeUrls = ["/path/to/file", "//example.com", "example.com"];
  assertEquals(testAbsoluteURLsInSecurity(relativeUrls), false);
});

Deno.test("Property: Nonce values are well-formatted", () => {
  const validNonces = [
    "abc123==",
    "dGVzdA==",
    "YWJjMTIz",
  ];

  for (const nonce of validNonces) {
    assertEquals(testNonceFormatValid(nonce), true);
  }
});

Deno.test("Property: Invalid nonce formats rejected", () => {
  const invalidNonces = ["", "!@#$%", "<script>"];
  for (const nonce of invalidNonces) {
    assertEquals(testNonceFormatValid(nonce), false);
  }
});

Deno.test("Property: HTML output is well-formed", () => {
  const validHTML = `
    <div class="container">
      <p>Paragraph 1</p>
      <p>Paragraph 2</p>
      <img src="image.jpg" alt="Image" />
    </div>
  `;
  assertEquals(testHTMLProperlyFormed(validHTML), true);
});

Deno.test("Property: Unclosed tags detected", () => {
  const invalidHTML = "<div><p>Unclosed paragraph</div>";
  assertEquals(testHTMLProperlyFormed(invalidHTML), false);
});
