// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Benchmarks for theme performance-critical operations.
// Establishes baseline for header generation, URL validation, and template rendering.

// Mock implementations for benchmarking
function generateCSPHeader(): string {
  return "default-src 'self'; script-src 'self' 'nonce-" +
    Math.random().toString(36).substring(2, 15) + "'; style-src 'self' https://fonts.googleapis.com; img-src 'self' data: https:;";
}

function generateHSTSHeader(): string {
  return "max-age=31536000; includeSubDomains; preload";
}

function validateIndieWebURL(url: string): boolean {
  try {
    const parsed = new URL(url);
    return parsed.protocol === "https:";
  } catch {
    return false;
  }
}

function escapeHTMLEntity(text: string): string {
  return text
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function calculateReadingTime(wordCount: number): number {
  return Math.max(1, Math.ceil(wordCount / 250));
}

function validateEmailAddress(email: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function formatW3CDatetime(date: Date): string {
  return date.toISOString();
}

function generateSecurityHeaders(): Record<string, string> {
  return {
    "Content-Security-Policy": generateCSPHeader(),
    "Strict-Transport-Security": generateHSTSHeader(),
    "X-Content-Type-Options": "nosniff",
    "X-Frame-Options": "SAMEORIGIN",
    "Referrer-Policy": "no-referrer",
    "Permissions-Policy": "geolocation=(), microphone=(), camera=()",
  };
}

// Benchmarks
Deno.bench("Security Header Generation - CSP", () => {
  generateCSPHeader();
});

Deno.bench("Security Header Generation - HSTS", () => {
  generateHSTSHeader();
});

Deno.bench("Security Header Generation - All Headers", () => {
  generateSecurityHeaders();
});

Deno.bench("URL Validation - HTTPS Check", () => {
  validateIndieWebURL("https://example.com/article");
});

Deno.bench("URL Validation - Batch (10 URLs)", () => {
  const urls = [
    "https://example1.com/article",
    "https://example2.com/post",
    "https://example3.com/page",
    "https://example4.com/content",
    "https://example5.com/post",
    "https://example6.com/article",
    "https://example7.com/blog",
    "https://example8.com/news",
    "https://example9.com/update",
    "https://example10.com/post",
  ];

  for (const url of urls) {
    validateIndieWebURL(url);
  }
});

Deno.bench("HTML Escaping - Simple Text", () => {
  escapeHTMLEntity("<script>alert('xss')</script>");
});

Deno.bench("HTML Escaping - Complex HTML", () => {
  escapeHTMLEntity(
    '<div onclick="malicious()" data="<script>alert(1)</script>">Content & more</div>'
  );
});

Deno.bench("HTML Escaping - Batch (100 items)", () => {
  for (let i = 0; i < 100; i++) {
    escapeHTMLEntity(`User input ${i} with <tags> & entities`);
  }
});

Deno.bench("Reading Time Calculation - Short Post (500 words)", () => {
  calculateReadingTime(500);
});

Deno.bench("Reading Time Calculation - Medium Post (2000 words)", () => {
  calculateReadingTime(2000);
});

Deno.bench("Reading Time Calculation - Long Post (10000 words)", () => {
  calculateReadingTime(10000);
});

Deno.bench("Email Validation - Single Email", () => {
  validateEmailAddress("user@example.com");
});

Deno.bench("Email Validation - Batch (100 emails)", () => {
  for (let i = 0; i < 100; i++) {
    validateEmailAddress(`user${i}@example.com`);
  }
});

Deno.bench("Email Validation - Invalid Emails", () => {
  const invalidEmails = [
    "notanemail",
    "@example.com",
    "user@",
    "user @example.com",
    "user@example",
  ];

  for (const email of invalidEmails) {
    validateEmailAddress(email);
  }
});

Deno.bench("Date Formatting - W3C ISO 8601", () => {
  formatW3CDatetime(new Date());
});

Deno.bench("Date Formatting - Batch (1000 dates)", () => {
  for (let i = 0; i < 1000; i++) {
    formatW3CDatetime(new Date(Date.now() - i * 86400000));
  }
});

Deno.bench("Template Rendering - Posted-on Microformat", () => {
  const date = new Date();
  const datetime = formatW3CDatetime(date);
  const formatted = date.toLocaleDateString();
  `<time class="dt-published" datetime="${datetime}">${formatted}</time>`;
});

Deno.bench("Template Rendering - Posted-by h-card", () => {
  const author = "John Doe";
  const url = "https://example.com/author/john";
  const escaped = escapeHTMLEntity(author);
  `<span class="p-author h-card"><a class="u-url" href="${url}"><span class="p-name">${escaped}</span></a></span>`;
});

Deno.bench("Template Rendering - Batch (10 posts)", () => {
  for (let i = 0; i < 10; i++) {
    const author = `Author ${i}`;
    const date = new Date(Date.now() - i * 86400000);
    const datetime = formatW3CDatetime(date);
    const escaped = escapeHTMLEntity(author);
    const title = escapeHTMLEntity(`Post Title ${i}`);

    `<article>
      <h2>${title}</h2>
      <time class="dt-published" datetime="${datetime}">${date.toLocaleDateString()}</time>
      <span class="p-author h-card">${escaped}</span>
    </article>`;
  }
});

Deno.bench("Turtle Escaping - IRI", () => {
  const iri = "http://example.com/path with spaces";
  let result = iri;
  result = result.replaceAll(" ", "%20");
  result = result.replaceAll("<", "%3C");
  result = result.replaceAll(">", "%3E");
});

Deno.bench("Turtle Escaping - String", () => {
  const text = 'String with "quotes" and \\ backslashes and\nnewlines';
  let result = text;
  result = result.replaceAll("\\", "\\\\");
  result = result.replaceAll('"', '\\"');
  result = result.replaceAll("\n", "\\n");
});

Deno.bench("Turtle Escaping - Batch (50 values)", () => {
  for (let i = 0; i < 50; i++) {
    const text = `Value ${i} with "quotes" and \\ backslashes`;
    let result = text;
    result = result.replaceAll("\\", "\\\\");
    result = result.replaceAll('"', '\\"');
  }
});

Deno.bench("Nonce Generation - Single", () => {
  const nonce = Math.random().toString(36).substring(2, 15) +
    Math.random().toString(36).substring(2, 15);
  nonce;
});

Deno.bench("Nonce Generation - Batch (100 nonces)", () => {
  for (let i = 0; i < 100; i++) {
    const nonce = Math.random().toString(36).substring(2, 15) +
      Math.random().toString(36).substring(2, 15);
    nonce;
  }
});

Deno.bench("CSP Parsing - Extract directives", () => {
  const csp = generateCSPHeader();
  const directives = csp.split(";").map((d) => d.trim());
  directives.length;
});

Deno.bench("CSP Validation - 10 directives", () => {
  const csp = generateCSPHeader();
  const unsafe = ["'unsafe-inline'", "'unsafe-eval'"];
  for (const pattern of unsafe) {
    csp.includes(pattern);
  }
});
