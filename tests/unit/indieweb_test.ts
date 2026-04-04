// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Unit tests for IndieWeb functionality (webmention, micropub, IndieAuth).
// Tests URL validation, host comparison, and protocol handling.

import { assertEquals, assert, assertFalse } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Contract: Valid URLs must be absolute HTTPS or HTTP with valid host
function validateURL(url: string): boolean {
  try {
    const parsed = new URL(url);
    return parsed.protocol === "http:" || parsed.protocol === "https:";
  } catch {
    return false;
  }
}

// Contract: Local URL must have matching host and scheme/port
function isLocalURL(url: string, siteURL: string): boolean {
  try {
    const urlObj = new URL(url);
    const siteObj = new URL(siteURL);

    // Host must match (case-insensitive)
    if (urlObj.hostname.toLowerCase() !== siteObj.hostname.toLowerCase()) {
      return false;
    }

    // Protocol must match (or upgrade http->https is blocked)
    if (urlObj.protocol !== siteObj.protocol) {
      // Allow https sites to accept https, reject http->https upgrades
      if (siteObj.protocol === "https:" && urlObj.protocol === "http:") {
        return false;
      }
    }

    // Port must match
    const urlPort = urlObj.port || (urlObj.protocol === "https:" ? "443" : "80");
    const sitePort = siteObj.port || (siteObj.protocol === "https:" ? "443" : "80");
    if (urlPort !== sitePort) {
      return false;
    }

    return true;
  } catch {
    return false;
  }
}

// Contract: IndieWeb mentions must be HTTPS (for security)
function validateMentionURL(url: string): boolean {
  try {
    const parsed = new URL(url);
    return parsed.protocol === "https:";
  } catch {
    return false;
  }
}

// Contract: IRI escape prevents Turtle injection
function escapeTurtleIRI(iri: string): string {
  const dangerous = ["<", ">", '"', "{", "}", "|", "^", "`", "\\", " "];
  const encoded = ["%3C", "%3E", "%22", "%7B", "%7D", "%7C", "%5E", "%60", "%5C", "%20"];

  let result = iri;
  for (let i = 0; i < dangerous.length; i++) {
    result = result.replaceAll(dangerous[i], encoded[i]);
  }
  return result;
}

// Contract: Turtle string escape prevents injection
function escapeTurtleString(input: string): string {
  const replacements: Record<string, string> = {
    "\\": "\\\\",
    '"': '\\"',
    "'": "\\'",
    "\n": "\\n",
    "\r": "\\r",
    "\t": "\\t",
  };

  let result = input;
  for (const [from, to] of Object.entries(replacements)) {
    result = result.replaceAll(from, to);
  }

  // Remove control characters
  result = result.replaceAll(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, "");
  return result;
}

// Contract: Safe Turtle literal detection
function isSafeTurtleLiteral(value: string): boolean {
  const dangerousPatterns = [
    /@prefix\s/i,
    /@base\s/i,
    /\.\s*$/m,
    /;\s*$/m,
    />\s*</m,
  ];

  for (const pattern of dangerousPatterns) {
    if (pattern.test(value)) {
      return false;
    }
  }

  return true;
}

Deno.test("URL Validation - Accepts valid HTTPS URL", () => {
  assertEquals(validateURL("https://example.com/path"), true);
});

Deno.test("URL Validation - Accepts valid HTTP URL", () => {
  assertEquals(validateURL("http://example.com/path"), true);
});

Deno.test("URL Validation - Rejects invalid URL", () => {
  assertEquals(validateURL("not-a-url"), false);
});

Deno.test("URL Validation - Rejects URL with no scheme", () => {
  assertEquals(validateURL("example.com"), false);
});

Deno.test("Local URL - Same host and https", () => {
  assertEquals(
    isLocalURL("https://example.com/post/1", "https://example.com/"),
    true
  );
});

Deno.test("Local URL - Same host, different path", () => {
  assertEquals(
    isLocalURL("https://example.com/different/path", "https://example.com/"),
    true
  );
});

Deno.test("Local URL - Different host", () => {
  assertEquals(
    isLocalURL("https://other.com/post", "https://example.com/"),
    false
  );
});

Deno.test("Local URL - Host bypass attempt with query param", () => {
  assertEquals(
    isLocalURL("https://evil.com?https://example.com", "https://example.com/"),
    false
  );
});

Deno.test("Local URL - Case-insensitive host matching", () => {
  assertEquals(
    isLocalURL("https://EXAMPLE.COM/post", "https://example.com/"),
    true
  );
});

Deno.test("Local URL - Rejects http->https downgrade", () => {
  assertEquals(
    isLocalURL("http://example.com/post", "https://example.com/"),
    false
  );
});

Deno.test("Local URL - Accepts http->http same protocol", () => {
  assertEquals(
    isLocalURL("http://example.com/post", "http://example.com/"),
    true
  );
});

Deno.test("Mention URL - HTTPS required for mentions", () => {
  assertEquals(validateMentionURL("https://other-site.com/article"), true);
});

Deno.test("Mention URL - HTTP mentions rejected", () => {
  assertEquals(validateMentionURL("http://other-site.com/article"), false);
});

Deno.test("Turtle IRI Escape - Escapes angle brackets", () => {
  const iri = "http://example.com/path<script>";
  const escaped = escapeTurtleIRI(iri);
  assertFalse(escaped.includes("<"));
  assertFalse(escaped.includes(">"));
});

Deno.test("Turtle IRI Escape - Escapes quotes", () => {
  const iri = 'http://example.com/path"xss"';
  const escaped = escapeTurtleIRI(iri);
  assertFalse(escaped.includes('"'));
});

Deno.test("Turtle IRI Escape - Escapes spaces", () => {
  const iri = "http://example.com/my path";
  const escaped = escapeTurtleIRI(iri);
  assertFalse(escaped.includes(" "));
});

Deno.test("Turtle String Escape - Escapes newlines", () => {
  const input = "line1\nline2";
  const escaped = escapeTurtleString(input);
  assertEquals(escaped, "line1\\nline2");
});

Deno.test("Turtle String Escape - Escapes backslashes", () => {
  const input = "path\\to\\file";
  const escaped = escapeTurtleString(input);
  assertEquals(escaped, "path\\\\to\\\\file");
});

Deno.test("Turtle String Escape - Escapes quotes", () => {
  const input = 'text with "quotes"';
  const escaped = escapeTurtleString(input);
  assertEquals(escaped, 'text with \\"quotes\\"');
});

Deno.test("Turtle String Escape - Removes control characters", () => {
  const input = "normal\x00text\x01more";
  const escaped = escapeTurtleString(input);
  assertFalse(escaped.includes("\x00"));
  assertFalse(escaped.includes("\x01"));
});

Deno.test("Safe Turtle Literal - Rejects @prefix injection", () => {
  assertEquals(isSafeTurtleLiteral("@prefix ex: <http://example.com/>"), false);
});

Deno.test("Safe Turtle Literal - Rejects @base injection", () => {
  assertEquals(isSafeTurtleLiteral("@base <http://evil.com/>"), false);
});

Deno.test("Safe Turtle Literal - Rejects statement terminator injection", () => {
  assertEquals(isSafeTurtleLiteral("text ."), false);
});

Deno.test("Safe Turtle Literal - Rejects IRI injection", () => {
  assertEquals(isSafeTurtleLiteral("><injection"), false);
});

Deno.test("Safe Turtle Literal - Accepts safe literals", () => {
  assertEquals(isSafeTurtleLiteral("This is a safe literal"), true);
});

Deno.test("Safe Turtle Literal - Accepts literals with punctuation", () => {
  assertEquals(isSafeTurtleLiteral("Hello, world! How are you?"), true);
});
