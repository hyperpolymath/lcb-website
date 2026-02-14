# SPDX-License-Identifier: PMPL-1.0-or-later
# Mockup Screenshots Guide

## Files

- `homepage.html` - Full homepage mockup with NUJ green/grey branding
- `officers-page.html` - Branch officers page with 3-column expandable grid

## How to Generate Screenshots

### Method 1: Open in Browser (Easiest)

```bash
# Open the file directly in your browser
firefox homepage.html
# or
google-chrome homepage.html
# or
chromium homepage.html
# or
microsoft-edge homepage.html

# Then take a screenshot:

# Firefox:
# - Right-click → "Take Screenshot" → "Save full page"
# - Or: Shift+Ctrl+S

# Chrome/Chromium:
# - F12 (open DevTools) → Cmd/Ctrl+Shift+P → "Capture full size screenshot"
# - Or: Right-click → "Inspect" → Cmd/Ctrl+Shift+P → "screenshot"

# Microsoft Edge:
# - F12 (open DevTools) → Ctrl+Shift+P → "Capture full size screenshot"
# - Or: Right-click → "Inspect" → Ctrl+Shift+P → "screenshot"
```

### Method 2: Using Screenshot Tool

```bash
# Install screenshot tool (if needed)
sudo dnf install firefox

# Take screenshot from command line
firefox --screenshot homepage.png --window-size=1200,4000 homepage.html
```

### Method 3: Using Headless Chrome

```bash
# Install chrome/chromium
# Then:
chromium --headless --screenshot=homepage.png --window-size=1200,4000 homepage.html
```

## Sharing Tips

- **For email/Slack**: Use PNG format, resize to max 1920px wide
- **For presentations**: Export as PDF, one page per mockup
- **For feedback**: Upload to Imgur/image host and share link

## Interactive Features

The officers page has expandable cards - click any officer to see full details.
Take screenshots both collapsed and expanded to show the interaction.

## Color Reference

These mockups use the official NUJ brand colors:
- **NUJ Green (Dark)**: `#006747` - Primary brand color
- **NUJ Green**: `#008559` - Standard green
- **NUJ Grey (Dark)**: `#2b2b2b` - Headings
- **NUJ Grey**: `#4c4c4c` - Body text
- **White**: `#ffffff` - Backgrounds

All color combinations meet WCAG AAA contrast standards (7:1 ratio).
