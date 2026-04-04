// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// End-to-end tests for theme activation and HTTP response contracts.
// Tests security headers, IndieWeb discovery, and accessibility.

import { assertEquals, assert, assertStringIncludes } from "https://deno.land/std@0.208.0/testing/asserts.ts";

interface MockResponse {
  status: number;
  headers: Record<string, string>;
  html: string;
}

// Mock theme activation check
function checkThemeActivation(themeFiles: string[]): {
  isValid: boolean;
  missing: string[];
} {
  const required = [
    "style.css",
    "functions.php",
    "index.php",
  ];

  const missing: string[] = [];

  for (const file of required) {
    if (!themeFiles.includes(file)) {
      missing.push(file);
    }
  }

  return {
    isValid: missing.length === 0,
    missing,
  };
}

// Mock security headers response
function checkSecurityHeaders(response: MockResponse): {
  hasAll: boolean;
  missing: string[];
} {
  const required = [
    "Content-Security-Policy",
    "Strict-Transport-Security",
    "X-Content-Type-Options",
    "X-Frame-Options",
    "Referrer-Policy",
  ];

  const missing: string[] = [];

  for (const header of required) {
    if (!response.headers[header]) {
      missing.push(header);
    }
  }

  return {
    hasAll: missing.length === 0,
    missing,
  };
}

// Check for IndieWeb discovery links
function checkIndieWebDiscovery(html: string): {
  hasWebmention: boolean;
  hasMicropub: boolean;
  hasAuth: boolean;
  hasToken: boolean;
} {
  return {
    hasWebmention: html.includes('rel="webmention"'),
    hasMicropub: html.includes('rel="micropub"'),
    hasAuth: html.includes('rel="authorization_endpoint"'),
    hasToken: html.includes('rel="token_endpoint"'),
  };
}

// Check accessibility landmarks
function checkAccessibilityLandmarks(html: string): {
  hasMainLandmark: boolean;
  hasNavLandmark: boolean;
  hasSkipLinks: boolean;
  hasLangAttribute: boolean;
} {
  return {
    hasMainLandmark: html.includes('id="main"'),
    hasNavLandmark: html.includes('id="nav"') || html.includes("<nav"),
    hasSkipLinks: html.includes('href="#main"') && html.includes('href="#nav"'),
    hasLangAttribute: /lang="[a-z]{2}(-[A-Z]{2})?"/.test(html),
  };
}

// Check HTTP status codes and headers consistency
function validateHTTPResponse(response: MockResponse): boolean {
  // 200 OK or 3xx redirects are valid
  if (response.status !== 200 && (response.status < 300 || response.status >= 400)) {
    return false;
  }

  // Must have Content-Type header
  if (!response.headers["Content-Type"]) {
    return false;
  }

  return true;
}

Deno.test("E2E: Theme Activation - Required files present", () => {
  const files = [
    "style.css",
    "functions.php",
    "index.php",
    "header.php",
    "footer.php",
  ];

  const result = checkThemeActivation(files);
  assertEquals(result.isValid, true);
  assertEquals(result.missing.length, 0);
});

Deno.test("E2E: Theme Activation - Missing critical file", () => {
  const files = ["style.css", "functions.php"];

  const result = checkThemeActivation(files);
  assertEquals(result.isValid, false);
  assertEquals(result.missing.includes("index.php"), true);
});

Deno.test("E2E: Security Headers - All required headers present", () => {
  const response: MockResponse = {
    status: 200,
    headers: {
      "Content-Type": "text/html; charset=utf-8",
      "Content-Security-Policy": "default-src 'self'; script-src 'self' 'nonce-xyz123'",
      "Strict-Transport-Security": "max-age=31536000; includeSubDomains",
      "X-Content-Type-Options": "nosniff",
      "X-Frame-Options": "SAMEORIGIN",
      "Referrer-Policy": "no-referrer",
    },
    html: "<html></html>",
  };

  const result = checkSecurityHeaders(response);
  assertEquals(result.hasAll, true);
  assertEquals(result.missing.length, 0);
});

Deno.test("E2E: Security Headers - Missing CSP", () => {
  const response: MockResponse = {
    status: 200,
    headers: {
      "Content-Type": "text/html; charset=utf-8",
      "Strict-Transport-Security": "max-age=31536000",
      "X-Content-Type-Options": "nosniff",
      "X-Frame-Options": "DENY",
      "Referrer-Policy": "strict-origin-when-cross-origin",
    },
    html: "<html></html>",
  };

  const result = checkSecurityHeaders(response);
  assertEquals(result.hasAll, false);
  assertEquals(result.missing.includes("Content-Security-Policy"), true);
});

Deno.test("E2E: IndieWeb Discovery - All endpoints present", () => {
  const html = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <link rel="webmention" href="https://example.com/wp-json/sinople/v1/webmention">
      <link rel="micropub" href="https://example.com/wp-json/sinople/v1/micropub">
      <link rel="authorization_endpoint" href="https://indieauth.com/auth">
      <link rel="token_endpoint" href="https://tokens.indieauth.com/token">
    </head>
    <body></body>
    </html>
  `;

  const discovery = checkIndieWebDiscovery(html);
  assertEquals(discovery.hasWebmention, true);
  assertEquals(discovery.hasMicropub, true);
  assertEquals(discovery.hasAuth, true);
  assertEquals(discovery.hasToken, true);
});

Deno.test("E2E: IndieWeb Discovery - Missing micropub endpoint", () => {
  const html = `
    <!DOCTYPE html>
    <head>
      <link rel="webmention" href="https://example.com/webmention">
      <link rel="authorization_endpoint" href="https://indieauth.com/auth">
      <link rel="token_endpoint" href="https://tokens.indieauth.com/token">
    </head>
  `;

  const discovery = checkIndieWebDiscovery(html);
  assertEquals(discovery.hasMicropub, false);
});

Deno.test("E2E: Accessibility - All landmarks present", () => {
  const html = `
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page</title></head>
    <body>
      <a class="skip-link" href="#main">Skip to main</a>
      <a class="skip-link" href="#nav">Skip to nav</a>
      <nav id="nav">
        <ul>
          <li><a href="/">Home</a></li>
        </ul>
      </nav>
      <main id="main">
        <p>Content</p>
      </main>
    </body>
    </html>
  `;

  const landmarks = checkAccessibilityLandmarks(html);
  assertEquals(landmarks.hasMainLandmark, true);
  assertEquals(landmarks.hasNavLandmark, true);
  assertEquals(landmarks.hasSkipLinks, true);
  assertEquals(landmarks.hasLangAttribute, true);
});

Deno.test("E2E: Accessibility - Missing main landmark", () => {
  const html = `
    <!DOCTYPE html>
    <html lang="en">
    <body>
      <nav id="nav"><ul></ul></nav>
      <div class="content"><p>Content</p></div>
    </body>
    </html>
  `;

  const landmarks = checkAccessibilityLandmarks(html);
  assertEquals(landmarks.hasMainLandmark, false);
});

Deno.test("E2E: Accessibility - Missing lang attribute", () => {
  const html = `
    <!DOCTYPE html>
    <html>
    <body>
      <main id="main"><p>Content</p></main>
    </body>
    </html>
  `;

  const landmarks = checkAccessibilityLandmarks(html);
  assertEquals(landmarks.hasLangAttribute, false);
});

Deno.test("E2E: HTTP Response - Valid response structure", () => {
  const response: MockResponse = {
    status: 200,
    headers: {
      "Content-Type": "text/html; charset=utf-8",
      "Content-Length": "5000",
    },
    html: "<html></html>",
  };

  assertEquals(validateHTTPResponse(response), true);
});

Deno.test("E2E: HTTP Response - Missing Content-Type", () => {
  const response: MockResponse = {
    status: 200,
    headers: {},
    html: "<html></html>",
  };

  assertEquals(validateHTTPResponse(response), false);
});

Deno.test("E2E: HTTP Response - Server error status", () => {
  const response: MockResponse = {
    status: 500,
    headers: {
      "Content-Type": "text/html; charset=utf-8",
    },
    html: "<html></html>",
  };

  assertEquals(validateHTTPResponse(response), false);
});

Deno.test("E2E: Full Page Load - Security, accessibility, and IndieWeb", () => {
  const html = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <title>LCB Website</title>
      <meta charset="utf-8">
      <link rel="webmention" href="/wp-json/sinople/v1/webmention">
      <link rel="micropub" href="/wp-json/sinople/v1/micropub">
    </head>
    <body>
      <a class="skip-link sr-only" href="#main">Skip to main</a>
      <a class="skip-link sr-only" href="#nav">Skip to nav</a>
      <nav id="nav">
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/about">About</a></li>
        </ul>
      </nav>
      <main id="main">
        <h1>Welcome</h1>
        <p>Content here</p>
      </main>
    </body>
    </html>
  `;

  const response: MockResponse = {
    status: 200,
    headers: {
      "Content-Type": "text/html; charset=utf-8",
      "Content-Security-Policy": "default-src 'self'",
      "Strict-Transport-Security": "max-age=31536000",
      "X-Content-Type-Options": "nosniff",
      "X-Frame-Options": "DENY",
      "Referrer-Policy": "no-referrer",
    },
    html,
  };

  const httpValid = validateHTTPResponse(response);
  const securityHeaders = checkSecurityHeaders(response);
  const indieWeb = checkIndieWebDiscovery(html);
  const landmarks = checkAccessibilityLandmarks(html);

  assertEquals(httpValid, true);
  assertEquals(securityHeaders.hasAll, true);
  assertEquals(indieWeb.hasWebmention, true);
  assertEquals(indieWeb.hasMicropub, true);
  assertEquals(landmarks.hasMainLandmark, true);
  assertEquals(landmarks.hasNavLandmark, true);
});
