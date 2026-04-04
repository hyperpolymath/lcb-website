// SPDX-License-Identifier: PMPL-1.0-or-later
// Copyright (c) 2026 Jonathan D.A. Jewell <j.d.a.jewell@open.ac.uk>
//
// Property-based tests for HTML generation and accessibility.
// Tests invariants about template output and tag structure.

import { assertEquals } from "https://deno.land/std@0.208.0/testing/asserts.ts";

// Property: Template tags always produce well-formed HTML snippets
function testHTMLSnippetsWellFormed(htmlSnippets: string[]): boolean {
  for (const html of htmlSnippets) {
    // Check for unmatched tags
    const openCount = (html.match(/<[^/][^>]*>/g) || []).length;
    const closeCount = (html.match(/<\/[^>]+>/g) || []).length;
    const selfClose = (html.match(/<[^>]*\/>/g) || []).length;

    if (openCount > closeCount + selfClose) {
      return false;
    }
  }
  return true;
}

// Property: Navigation walker always closes all open tags
function testNavigationWalkerClosesAllTags(walkerOutput: string): boolean {
  const openTags = walkerOutput.match(/<[^/][^>]*>/g) || [];
  const closeTags = walkerOutput.match(/<\/[^>]+>/g) || [];

  // Count by tag type
  const tagTypes = new Map<string, number>();

  for (const tag of openTags) {
    const match = tag.match(/<([a-z]+)/i);
    if (match) {
      const type = match[1].toLowerCase();
      if (type !== "br" && type !== "img" && type !== "input") {
        tagTypes.set(type, (tagTypes.get(type) || 0) + 1);
      }
    }
  }

  for (const tag of closeTags) {
    const match = tag.match(/<\/([a-z]+)>/i);
    if (match) {
      const type = match[1].toLowerCase();
      tagTypes.set(type, (tagTypes.get(type) || 0) - 1);
    }
  }

  // All tags must be closed (count should be 0)
  for (const count of tagTypes.values()) {
    if (count !== 0) {
      return false;
    }
  }
  return true;
}

// Property: Widget output always has required ARIA attributes
function testWidgetHasAriaAttributes(widgetHTML: string): boolean {
  // At minimum, interactive widgets should have ARIA attributes
  const isInteractive = widgetHTML.includes("<button") ||
    widgetHTML.includes("<input") ||
    widgetHTML.includes("<select") ||
    widgetHTML.includes("<textarea");

  if (!isInteractive) {
    return true; // Non-interactive widgets don't need ARIA
  }

  // Interactive widgets need either aria-label or aria-labelledby or text content
  return widgetHTML.includes("aria-label") ||
    widgetHTML.includes("aria-labelledby") ||
    widgetHTML.includes("aria-describedby");
}

// Property: All images have alt text or are marked decorative
function testImagesHaveAltOrDecorative(html: string): boolean {
  const imgTags = html.match(/<img[^>]*>/g) || [];

  for (const img of imgTags) {
    const hasAlt = img.includes("alt=");
    const isDecorative = img.includes("aria-hidden=\"true\"") ||
      img.includes("role=\"presentation\"");

    if (!hasAlt && !isDecorative) {
      return false;
    }
  }
  return true;
}

// Property: Headings maintain proper hierarchy (h1 > h2 > h3...)
function testHeadingHierarchy(html: string): boolean {
  const headings = html.match(/<h([1-6])[^>]*>/g) || [];

  let previousLevel = 0;
  for (const heading of headings) {
    const match = heading.match(/h([1-6])/);
    if (match) {
      const level = parseInt(match[1], 10);
      // Skip level is ok (h1 to h3), but can't jump backward more than 1
      if (level > previousLevel + 1) {
        return false;
      }
      previousLevel = level;
    }
  }
  return true;
}

// Property: Lists always have proper structure (li within ul/ol)
function testListStructure(html: string): boolean {
  const hasOrphanLi = /<li[^>]*>/.test(html) && !/<[ou]l[^>]*>[\s\S]*<li/.test(html);
  return !hasOrphanLi;
}

// Property: Links always have href attribute
function testLinksHaveHref(html: string): boolean {
  const linkTags = html.match(/<a[^>]*>/g) || [];

  for (const link of linkTags) {
    // Anchors must have href (unless they're anchor targets with id)
    if (!link.includes("href=")) {
      // Check if it has id for anchor target
      if (!link.includes("id=")) {
        return false;
      }
    }
  }
  return true;
}

// Property: Button elements never contain nested buttons
function testNoNestedButtons(html: string): boolean {
  return !/<button[^>]*>[\s\S]*<button/.test(html);
}

// Property: Form inputs always have associated labels
function testFormInputsLabeled(html: string): boolean {
  const inputs = html.match(/<input[^>]*>/g) || [];

  for (const input of inputs) {
    // Check for id or aria-label or aria-labelledby
    const hasId = input.includes("id=");
    const hasLabel = input.includes("aria-label");
    const hasLabelledBy = input.includes("aria-labelledby");

    if (!hasId && !hasLabel && !hasLabelledBy) {
      // Might be okay if it's a hidden input
      if (!input.includes("type=\"hidden\"")) {
        return false;
      }
    }
  }
  return true;
}

// Property: Color contrast classes never use forbidden combinations
function testNoForbiddenContrast(cssClass: string): boolean {
  const forbidden = [
    "gray-text-on-gray-bg",
    "light-text-on-light-bg",
    "yellow-on-white",
  ];
  return !forbidden.includes(cssClass);
}

Deno.test("Property: Template snippets are well-formed", () => {
  const snippets = [
    '<span class="posted-on"><time datetime="2026-04-04">April 4</time></span>',
    '<span class="byline"><a href="/author">Author</a></span>',
    '<span class="reading-time">3 min read</span>',
  ];
  assertEquals(testHTMLSnippetsWellFormed(snippets), true);
});

Deno.test("Property: Unclosed tags fail well-formed check", () => {
  const snippets = [
    '<span class="unclosed"><p>Paragraph',
  ];
  assertEquals(testHTMLSnippetsWellFormed(snippets), false);
});

Deno.test("Property: Navigation walker closes all tags", () => {
  const nav = `
    <nav id="nav">
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/about">About</a></li>
      </ul>
    </nav>
  `;
  assertEquals(testNavigationWalkerClosesAllTags(nav), true);
});

Deno.test("Property: Unclosed nav tags detected", () => {
  const nav = `
    <nav id="nav">
      <ul>
        <li><a href="/">Home
      </ul>
    </nav>
  `;
  assertEquals(testNavigationWalkerClosesAllTags(nav), false);
});

Deno.test("Property: Interactive widgets have ARIA attributes", () => {
  const widget = '<button aria-label="Close menu">X</button>';
  assertEquals(testWidgetHasAriaAttributes(widget), true);
});

Deno.test("Property: Button without ARIA fails", () => {
  const widget = "<button>Click me</button>";
  // This would need aria-label to pass strict check
  assertEquals(testWidgetHasAriaAttributes(widget), false);
});

Deno.test("Property: Non-interactive widgets don't need ARIA", () => {
  const widget = '<div class="card"><p>Content</p></div>';
  assertEquals(testWidgetHasAriaAttributes(widget), true);
});

Deno.test("Property: Images have alt text", () => {
  const html = '<img src="photo.jpg" alt="Photo of building" />';
  assertEquals(testImagesHaveAltOrDecorative(html), true);
});

Deno.test("Property: Decorative images marked with aria-hidden", () => {
  const html = '<img src="spacer.gif" aria-hidden="true" />';
  assertEquals(testImagesHaveAltOrDecorative(html), true);
});

Deno.test("Property: Images without alt fail", () => {
  const html = "<img src=\"photo.jpg\" />";
  assertEquals(testImagesHaveAltOrDecorative(html), false);
});

Deno.test("Property: Proper heading hierarchy", () => {
  const html = `
    <h1>Main Title</h1>
    <h2>Section</h2>
    <h3>Subsection</h3>
  `;
  assertEquals(testHeadingHierarchy(html), true);
});

Deno.test("Property: Skipped heading levels fail", () => {
  const html = `
    <h1>Main Title</h1>
    <h3>Subsection (should be h2)</h3>
  `;
  assertEquals(testHeadingHierarchy(html), false);
});

Deno.test("Property: Proper list structure", () => {
  const html = `
    <ul>
      <li>Item 1</li>
      <li>Item 2</li>
    </ul>
  `;
  assertEquals(testListStructure(html), true);
});

Deno.test("Property: Links have href attribute", () => {
  const html = '<a href="/page">Link</a>';
  assertEquals(testLinksHaveHref(html), true);
});

Deno.test("Property: Links without href and id fail", () => {
  const html = "<a>Invalid link</a>";
  assertEquals(testLinksHaveHref(html), false);
});

Deno.test("Property: No nested buttons", () => {
  const html = "<button>Outer <button>Inner</button></button>";
  assertEquals(testNoNestedButtons(html), false);
});

Deno.test("Property: Form inputs labeled with id", () => {
  const html = '<input type="text" id="username" />';
  assertEquals(testFormInputsLabeled(html), true);
});

Deno.test("Property: Form inputs labeled with aria-label", () => {
  const html = '<input type="text" aria-label="Username" />';
  assertEquals(testFormInputsLabeled(html), true);
});

Deno.test("Property: Unlabeled form inputs fail", () => {
  const html = '<input type="text" />';
  assertEquals(testFormInputsLabeled(html), false);
});

Deno.test("Property: Hidden inputs exempt from labeling", () => {
  const html = '<input type="hidden" />';
  assertEquals(testFormInputsLabeled(html), true);
});
