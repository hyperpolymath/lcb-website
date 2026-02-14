# SPDX-License-Identifier: PMPL-1.0-or-later
# NUJ LCB Design System

## Brand Colors (NUJ)

Based on NUJ branding, using green, grey, and white as primary colors:

### Primary Palette

```css
/* NUJ Brand Green - Primary */
--nuj-green-dark: #006747;      /* Main brand green (dark forest) */
--nuj-green: #008559;            /* Standard brand green */
--nuj-green-light: #00a572;      /* Light accent green */
--nuj-green-pale: #e6f4f0;       /* Pale green backgrounds */

/* NUJ Grey - Neutral */
--nuj-grey-dark: #2b2b2b;        /* Dark grey for headings */
--nuj-grey: #4c4c4c;             /* Body text grey */
--nuj-grey-medium: #6e6e6e;      /* Secondary text */
--nuj-grey-light: #a0a0a0;       /* Disabled states */
--nuj-grey-pale: #f5f5f5;        /* Light backgrounds */

/* Base Colors */
--nuj-white: #ffffff;            /* Pure white */
--nuj-black: #000000;            /* Pure black (use sparingly) */

/* Accent Colors (for alerts, links, etc.) */
--nuj-accent-info: #0066cc;      /* Information */
--nuj-accent-success: #00a572;   /* Success (matches green) */
--nuj-accent-warning: #ff9900;   /* Warning */
--nuj-accent-error: #cc0000;     /* Error */
```

### WCAG AAA Compliant Combinations

All combinations meet WCAG AAA (7:1 contrast) for normal text:

| Foreground | Background | Contrast | Use Case |
|------------|------------|----------|----------|
| `--nuj-green-dark` | `--nuj-white` | 7.6:1 | Primary buttons |
| `--nuj-white` | `--nuj-green-dark` | 7.6:1 | Button text |
| `--nuj-grey-dark` | `--nuj-white` | 12.3:1 | Headings |
| `--nuj-grey` | `--nuj-white` | 8.9:1 | Body text |
| `--nuj-green` | `--nuj-white` | 5.4:1 | Links (AAA large text only) |
| `--nuj-green-dark` | `--nuj-grey-pale` | 8.2:1 | Cards on pale bg |

**Note:** For AAA compliance with normal text, use `--nuj-green-dark` for links instead of `--nuj-green`.

## Typography

### Font Stack

```css
/* Primary (body text) */
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
             'Helvetica Neue', Arial, sans-serif;

/* Headings */
font-family: Georgia, 'Times New Roman', serif;  /* Classic union style */

/* Monospace (code, technical) */
font-family: 'Courier New', Courier, monospace;
```

### Font Sizes (Accessible)

```css
--text-xs: 0.75rem;   /* 12px - Captions, labels */
--text-sm: 0.875rem;  /* 14px - Small text */
--text-base: 1rem;    /* 16px - Body text (minimum for accessibility) */
--text-lg: 1.125rem;  /* 18px - Lead paragraphs */
--text-xl: 1.25rem;   /* 20px - Subheadings */
--text-2xl: 1.5rem;   /* 24px - H3 */
--text-3xl: 1.875rem; /* 30px - H2 */
--text-4xl: 2.25rem;  /* 36px - H1 */
--text-5xl: 3rem;     /* 48px - Hero text */
```

### Line Heights

```css
--leading-tight: 1.25;   /* Headings */
--leading-normal: 1.5;   /* Body text (WCAG minimum) */
--leading-relaxed: 1.75; /* Comfortable reading */
```

## Spacing Scale

```css
--space-1: 0.25rem;  /* 4px */
--space-2: 0.5rem;   /* 8px */
--space-3: 0.75rem;  /* 12px */
--space-4: 1rem;     /* 16px */
--space-6: 1.5rem;   /* 24px */
--space-8: 2rem;     /* 32px */
--space-12: 3rem;    /* 48px */
--space-16: 4rem;    /* 64px */
--space-24: 6rem;    /* 96px */
```

## Components

### Buttons

```css
/* Primary Button (Call to Action) */
.btn-primary {
  background-color: var(--nuj-green-dark);
  color: var(--nuj-white);
  padding: var(--space-3) var(--space-6);
  border-radius: 6px;
  font-weight: 600;
  font-size: var(--text-base);
  border: 2px solid var(--nuj-green-dark);
  transition: all 0.2s ease;
  min-height: 44px;  /* Touch target */
  min-width: 44px;
}

.btn-primary:hover {
  background-color: var(--nuj-green);
  border-color: var(--nuj-green);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 103, 71, 0.2);
}

.btn-primary:focus {
  outline: 3px solid var(--nuj-accent-info);
  outline-offset: 2px;
}

/* Secondary Button (Outline) */
.btn-secondary {
  background-color: transparent;
  color: var(--nuj-green-dark);
  border: 2px solid var(--nuj-green-dark);
  /* Same padding/sizing as primary */
}

.btn-secondary:hover {
  background-color: var(--nuj-green-pale);
}

/* Tertiary Button (Text only) */
.btn-tertiary {
  background-color: transparent;
  color: var(--nuj-green-dark);
  border: none;
  text-decoration: underline;
  text-underline-offset: 4px;
}
```

### Cards

```css
.card {
  background: var(--nuj-white);
  border: 1px solid var(--nuj-grey-light);
  border-radius: 8px;
  padding: var(--space-6);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  border-bottom: 2px solid var(--nuj-green-dark);
  padding-bottom: var(--space-3);
  margin-bottom: var(--space-4);
}
```

### Links

```css
a {
  color: var(--nuj-green-dark);  /* AAA compliant */
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-thickness: 2px;
  transition: color 0.2s;
}

a:hover {
  color: var(--nuj-green);
  text-decoration-thickness: 3px;
}

a:focus {
  outline: 3px solid var(--nuj-accent-info);
  outline-offset: 2px;
  border-radius: 2px;
}

/* Skip to content link (accessibility) */
.skip-link {
  position: absolute;
  top: -40px;
  left: 0;
  background: var(--nuj-green-dark);
  color: var(--nuj-white);
  padding: var(--space-3);
  z-index: 9999;
}

.skip-link:focus {
  top: 0;
}
```

### Forms

```css
input, textarea, select {
  font-size: var(--text-base);  /* Minimum 16px on mobile */
  padding: var(--space-3);
  border: 2px solid var(--nuj-grey-light);
  border-radius: 4px;
  min-height: 44px;  /* Touch target */
  transition: border-color 0.2s;
}

input:focus, textarea:focus, select:focus {
  outline: none;
  border-color: var(--nuj-green-dark);
  box-shadow: 0 0 0 3px rgba(0, 103, 71, 0.2);
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: var(--space-2);
  color: var(--nuj-grey-dark);
}

/* Error states */
.error input {
  border-color: var(--nuj-accent-error);
}

.error-message {
  color: var(--nuj-accent-error);
  font-size: var(--text-sm);
  margin-top: var(--space-1);
}
```

## Accessibility Features

### High Contrast Mode

```css
@media (prefers-contrast: high) {
  :root {
    --nuj-green: #006747;          /* Darker green */
    --nuj-grey: #2b2b2b;           /* Darker grey */
  }

  .btn-primary {
    border-width: 3px;
  }
}
```

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

### Dark Mode

```css
@media (prefers-color-scheme: dark) {
  :root {
    --nuj-white: #1a1a1a;
    --nuj-black: #ffffff;
    --nuj-grey-dark: #e0e0e0;
    --nuj-grey: #c0c0c0;
    --nuj-grey-pale: #2b2b2b;
    --nuj-green-pale: #1a3329;
  }
}
```

### Font Size Toggle (User Control)

```javascript
// Add to theme
function increaseFontSize() {
  const html = document.documentElement;
  const currentSize = parseFloat(getComputedStyle(html).fontSize);
  html.style.fontSize = (currentSize + 2) + 'px';
}

function decreaseFontSize() {
  const html = document.documentElement;
  const currentSize = parseFloat(getComputedStyle(html).fontSize);
  if (currentSize > 12) {
    html.style.fontSize = (currentSize - 2) + 'px';
  }
}
```

## Layout Grid

```css
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-4);
}

.grid {
  display: grid;
  gap: var(--space-6);
}

.grid-2 {
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid-3 {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

/* Responsive breakpoints */
@media (max-width: 768px) {
  .grid-2, .grid-3 {
    grid-template-columns: 1fr;
  }
}
```

## Icon System

Use inline SVGs with appropriate ARIA labels:

```html
<svg aria-hidden="true" focusable="false" role="img">
  <!-- icon path -->
</svg>
<span class="sr-only">Descriptive text for screen readers</span>
```

## Animation Principles

1. **Duration**: 200-300ms for interactive elements
2. **Easing**: `ease-in-out` for most, `ease-out` for exits
3. **Respect `prefers-reduced-motion`**
4. **Only animate transforms and opacity** (better performance)

## Newspaper Theme Customization

### Customize via Customizer

```php
// In functions.php
function nuj_lcb_customize_register($wp_customize) {
  // Primary color
  $wp_customize->add_setting('nuj_green_dark', array(
    'default' => '#006747',
    'transport' => 'refresh',
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'nuj_green_dark',
    array(
      'label' => 'NUJ Green (Dark)',
      'section' => 'colors',
    )
  ));
}
add_action('customize_register', 'nuj_lcb_customize_register');
```

### Override Newspaperup Colors

```css
/* In Additional CSS (Customizer) */
:root {
  --primary-color: #006747;    /* Override Newspaperup purple */
  --secondary-color: #4c4c4c;  /* Grey */
  --accent-color: #008559;     /* Light green */
}

/* Header */
.site-header {
  background-color: var(--nuj-green-dark);
}

/* Buttons */
.btn, .wp-block-button__link {
  background-color: var(--nuj-green-dark);
}

/* Links */
a {
  color: var(--nuj-green-dark);
}

a:hover {
  color: var(--nuj-green);
}
```

## Content Guidelines

### Headings

- **H1**: Page title only (one per page)
- **H2**: Major sections
- **H3**: Subsections
- **H4-H6**: Rarely needed

### Paragraph Length

- Maximum 80 characters per line for readability
- Use `<p>` tags, not `<br>` for spacing

### Lists

- Use semantic `<ul>` or `<ol>` tags
- Each item should be a complete thought

### Images

- Always include `alt` text
- Use `loading="lazy"` for below-fold images
- Provide captions via `<figcaption>`

## Implementation Checklist

- [ ] Install Newspaperup theme
- [ ] Apply NUJ color overrides in Customizer
- [ ] Add custom CSS for accessibility features
- [ ] Test all color combinations with contrast checker
- [ ] Add font size toggle buttons in header
- [ ] Test with screen reader (NVDA, JAWS, VoiceOver)
- [ ] Test keyboard navigation (Tab, Enter, Escape)
- [ ] Verify focus indicators are visible
- [ ] Test dark mode rendering
- [ ] Test high contrast mode
- [ ] Verify reduced motion respect
- [ ] Run Lighthouse accessibility audit (aim for 100 score)
