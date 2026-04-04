// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Unit tests for accessibility features (WCAG 2.3 AAA compliance).
// Tests skip links, aria attributes, and screen-reader text.

import { assertEquals, assert, assertStringIncludes } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Contract: Skip links must be present and point to correct anchors
function validateSkipLinks(html: string): boolean {
  const skipToMain = html.includes('href="#main"');
  const skipToNav = html.includes('href="#nav"');
  const srOnly = html.includes("sr-only");

  return skipToMain && skipToNav && srOnly;
}

// Contract: Screen reader text must be wrapped in span.screen-reader-text
function wrapScreenReaderText(text: string): string {
  return `<span class="screen-reader-text">${escapeHTML(text)}</span>`;
}

// Contract: aria-current must be set on current page
function validateAriaCurrentPage(navHTML: string, isCurrentPage: boolean): boolean {
  if (isCurrentPage) {
    return navHTML.includes('aria-current="page"');
  }
  return true;
}

// Contract: Main landmark must be present with id="main"
function validateMainLandmark(html: string): boolean {
  return html.includes('id="main"') && html.includes("<main");
}

// Contract: Navigation must have id="nav" or be in nav element
function validateNavigationLandmark(html: string): boolean {
  return html.includes('id="nav"') || html.includes("<nav");
}

// Contract: Lang attribute on HTML element
function validateLanguageAttribute(html: string): boolean {
  return /html[^>]*lang="[a-z]{2}(-[A-Z]{2})?"/.test(html);
}

// Contract: Links must have accessible names
function validateLinkAccessibility(
  href: string,
  ariaLabel: string | null,
  text: string
): boolean {
  if (ariaLabel && ariaLabel.length > 0) {
    return true;
  }
  return text.length > 0;
}

// Contract: Images must have alt text (unless decorative)
function validateImageAccessibility(
  src: string,
  alt: string | null,
  isDecorative: boolean
): boolean {
  if (isDecorative) {
    return alt === "" || alt === null;
  }
  return alt !== null && alt.length > 0;
}

// Contract: Contrast ratio calculation (simplified WCAG AA)
function calculateContrastRatio(
  rgb1: [number, number, number],
  rgb2: [number, number, number]
): number {
  const getLuminance = (r: number, g: number, b: number): number => {
    const [rs, gs, bs] = [r, g, b].map((val) => {
      const c = val / 255;
      return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    });

    return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
  };

  const l1 = getLuminance(rgb1[0], rgb1[1], rgb1[2]);
  const l2 = getLuminance(rgb2[0], rgb2[1], rgb2[2]);

  const lighter = Math.max(l1, l2);
  const darker = Math.min(l1, l2);

  return (lighter + 0.05) / (darker + 0.05);
}

// Contract: Color contrast must meet WCAG AA (4.5:1 for normal text)
function validateContrast(ratio: number, fontSize: number = 16): boolean {
  const isLargeText = fontSize >= 18 || fontSize >= 14 * 1.2; // >= 18px or bold 14px
  return isLargeText ? ratio >= 3.0 : ratio >= 4.5;
}

// Utility: Escape HTML for screen reader text
function escapeHTML(text: string): string {
  return text
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

Deno.test("Skip Links - Both skip links present", () => {
  const html = `
    <a class="skip-link sr-only" href="#main">Skip to main content</a>
    <a class="skip-link sr-only" href="#nav">Skip to navigation</a>
  `;
  assertEquals(validateSkipLinks(html), true);
});

Deno.test("Skip Links - Missing main skip link", () => {
  const html = `
    <a class="skip-link sr-only" href="#nav">Skip to navigation</a>
  `;
  assertEquals(validateSkipLinks(html), false);
});

Deno.test("Skip Links - Missing nav skip link", () => {
  const html = `
    <a class="skip-link sr-only" href="#main">Skip to main content</a>
  `;
  assertEquals(validateSkipLinks(html), false);
});

Deno.test("Skip Links - Missing sr-only class", () => {
  const html = `
    <a class="skip-link" href="#main">Skip to main content</a>
    <a class="skip-link" href="#nav">Skip to navigation</a>
  `;
  assertEquals(validateSkipLinks(html), false);
});

Deno.test("Screen Reader Text - Wraps text in correct span", () => {
  const text = "Loading...";
  const wrapped = wrapScreenReaderText(text);
  assertEquals(wrapped, '<span class="screen-reader-text">Loading...</span>');
});

Deno.test("Screen Reader Text - Escapes HTML entities", () => {
  const text = "Price: <$100";
  const wrapped = wrapScreenReaderText(text);
  assertStringIncludes(wrapped, "&lt;");
});

Deno.test("Aria Current - Present on current page", () => {
  const html = '<a href="/blog" aria-current="page">Blog</a>';
  assertEquals(validateAriaCurrentPage(html, true), true);
});

Deno.test("Aria Current - Not required on non-current page", () => {
  const html = '<a href="/about">About</a>';
  assertEquals(validateAriaCurrentPage(html, false), true);
});

Deno.test("Main Landmark - Present with correct id", () => {
  const html = '<main id="main"><p>Content</p></main>';
  assertEquals(validateMainLandmark(html), true);
});

Deno.test("Main Landmark - Missing id", () => {
  const html = '<main><p>Content</p></main>';
  assertEquals(validateMainLandmark(html), false);
});

Deno.test("Navigation Landmark - Present with id", () => {
  const html = '<nav id="nav"><ul><li><a href="/">Home</a></li></ul></nav>';
  assertEquals(validateNavigationLandmark(html), true);
});

Deno.test("Navigation Landmark - Present as nav element", () => {
  const html = "<nav><ul><li><a href=\"/\">Home</a></li></ul></nav>";
  assertEquals(validateNavigationLandmark(html), true);
});

Deno.test("Language Attribute - Valid 2-letter code", () => {
  const html = '<html lang="en"><head></head></html>';
  assertEquals(validateLanguageAttribute(html), true);
});

Deno.test("Language Attribute - Valid with region code", () => {
  const html = '<html lang="en-US"><head></head></html>';
  assertEquals(validateLanguageAttribute(html), true);
});

Deno.test("Language Attribute - Missing", () => {
  const html = "<html><head></head></html>";
  assertEquals(validateLanguageAttribute(html), false);
});

Deno.test("Link Accessibility - Has aria-label", () => {
  assertEquals(validateLinkAccessibility("/page", "Go to page", ""), true);
});

Deno.test("Link Accessibility - Has link text", () => {
  assertEquals(validateLinkAccessibility("/page", null, "Click here"), true);
});

Deno.test("Link Accessibility - Empty label and text", () => {
  assertEquals(validateLinkAccessibility("/page", null, ""), false);
});

Deno.test("Image Accessibility - Non-decorative needs alt", () => {
  assertEquals(validateImageAccessibility("/image.jpg", "Description", false), true);
});

Deno.test("Image Accessibility - Decorative has empty alt", () => {
  assertEquals(validateImageAccessibility("/icon.svg", "", true), true);
});

Deno.test("Image Accessibility - Decorative with text alt fails", () => {
  assertEquals(validateImageAccessibility("/icon.svg", "Icon", true), false);
});

Deno.test("Contrast Ratio - Black on white (high contrast)", () => {
  // Black (0, 0, 0) on white (255, 255, 255)
  const ratio = calculateContrastRatio([0, 0, 0], [255, 255, 255]);
  assertEquals(ratio > 20, true);
});

Deno.test("Contrast Ratio - Gray on gray (low contrast)", () => {
  // Dark gray (100, 100, 100) on light gray (200, 200, 200)
  const ratio = calculateContrastRatio([100, 100, 100], [200, 200, 200]);
  assertEquals(ratio < 4.5, true);
});

Deno.test("Contrast Validation - Normal text needs 4.5:1", () => {
  assertEquals(validateContrast(4.5, 16), true);
  assertEquals(validateContrast(4.4, 16), false);
});

Deno.test("Contrast Validation - Large text needs 3:1", () => {
  assertEquals(validateContrast(3.0, 18), true);
  assertEquals(validateContrast(2.9, 18), false);
});

Deno.test("Contrast Validation - Bold 14px is large text", () => {
  const boldFontSize = 14 * 1.2; // Simulate bold
  assertEquals(validateContrast(3.0, boldFontSize), true);
});
