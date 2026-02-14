# How to Share the Website Preview

## The Easy Way (ONE FILE)

**File to share:**
```
content/nuj-lcb-shareable-site.html
```

### Step 1: Attach to Email

```
To: branch-members@nuj-lcb.org.uk
Subject: NUJ LCB Website Preview - Please Review
Attach: nuj-lcb-shareable-site.html

Body:
Hi everyone,

Please review the proposed new website for NUJ London Central Branch.

TO VIEW:
1. Download the attached HTML file
2. Double-click to open in your browser (Chrome, Firefox, Edge, Safari)
3. Navigate between pages using the menu
4. Click "Send Comment" on any page to give feedback

FEATURES TO REVIEW:
- Homepage layout and content
- Officers page (see officers-page.html for interactive version)
- Members area description
- LinkedIn integration
- Overall design and colors (NUJ green/grey)

IMPORTANT: This is a PREVIEW only. Nothing is live yet.

Your feedback is welcome! Use the comment forms on each page or reply to this email.

Thanks,
[Your Name]
```

### Step 2: They Open It

**Recipients:**
1. Download attachment from Gmail
2. Double-click `nuj-lcb-shareable-site.html`
3. Opens in their default browser
4. **Works completely offline!**

### Step 3: They Browse & Comment

**What they can do:**
- Click navigation (Home, About, Join, Officers, Contact, LinkedIn, Policies)
- Read all content
- Click "Send Comment via Email" on any page
- Their email client opens with pre-filled message to you
- They add their feedback and send

**What they CANNOT do:**
- Break anything (it's just a local file)
- Access real member data (it's a mockup)
- Make changes to live site (nothing is deployed)

## Alternative: Visual Mockups (for specific pages)

**If you want screenshot images to share:**

### Generate Screenshots

```bash
cd ~/Documents/hyperpolymath-repos/nuj-lcb-production/content/mockups

# Open in browser
firefox homepage.html
# Or
google-chrome homepage.html
# Or
microsoft-edge homepage.html

# Take screenshot:

# Firefox:
Shift+Ctrl+S → "Save full page" → Save as homepage.png

# Chrome/Chromium:
F12 → Ctrl+Shift+P → type "screenshot" → "Capture full size screenshot"

# Edge:
F12 → Ctrl+Shift+P → type "screenshot" → "Capture full size screenshot"
```

### Email Screenshots

```
To: branch-members@nuj-lcb.org.uk
Subject: NUJ LCB Website Design Preview
Attach: homepage.png, officers-page.png

Body:
Hi everyone,

Attached are mockups of the proposed new website design.

IMAGE 1: Homepage (with news, events, LinkedIn feed)
IMAGE 2: Officers page (expandable officer profiles)

Full interactive preview: [Attach nuj-lcb-shareable-site.html]

Please reply with:
- What you like
- What needs changing
- Any concerns

Thanks!
```

## What Each File Is For

| File | Purpose | How to Share |
|------|---------|--------------|
| **nuj-lcb-shareable-site.html** | Complete navigable website | Email as attachment |
| **homepage.html** | Homepage only (for screenshot) | Open → screenshot → share image |
| **officers-page.html** | Interactive officers grid | Open → screenshot → share image |

## Collecting Feedback

### Option 1: Email Comments (Built-In)

The shareable site has comment forms. When people click "Send Comment":
- Opens their email client
- Pre-filled with their name, email, comment
- Addressed to: contact@nuj-lcb.org.uk
- They just click Send

### Option 2: Google Form

Create feedback form:

```
Questions:
1. What do you think of the homepage design?
2. Is the navigation clear?
3. Do the colors work well? (NUJ green/grey)
4. Is the officers page useful?
5. What's missing?
6. Any concerns about privacy/security?
7. Other comments?
```

Share form link in email alongside HTML file.

### Option 3: Meeting Discussion

1. Share HTML file before meeting
2. Ask people to review
3. Discuss at branch meeting
4. Take notes on feedback
5. Prioritize changes

## Responding to Feedback

### Common Feedback & Responses

**"Too much green"**
→ Can adjust color balance (more grey/white)

**"Can't find [feature]"**
→ Navigation needs improvement

**"LinkedIn feed seems promotional"**
→ Can reduce prominence or remove

**"Need more about [topic]"**
→ Add content section

**"Worried about member privacy"**
→ Review privacy controls, add more documentation

### Making Changes

After feedback:

1. **Update HTML files** with changes
2. **Re-share** with "Version 2" or "Updated Preview"
3. **Iterate** until consensus
4. **Then**: Build real WordPress site from approved design

## File Locations

```
nuj-lcb-production/
├── content/
│   ├── nuj-lcb-shareable-site.html  ← SHARE THIS (all-in-one)
│   ├── mockups/
│   │   ├── homepage.html            ← Screenshot this
│   │   ├── officers-page.html       ← Screenshot this
│   │   └── README.md                ← Screenshot instructions
│   ├── pages/                       ← Source content
│   └── policies/                    ← Policy documents
```

## Testing Before You Share

1. **Open** `nuj-lcb-shareable-site.html` in your browser
2. **Click** through all pages
3. **Test** comment form (make sure mailto works)
4. **Check** on mobile (responsive design)
5. **Verify** all links work or explain they're placeholders

## Privacy Note for Recipients

**Include in email:**

```
PRIVACY: This preview contains no real member data. All names/emails
are placeholders marked [Name Here]. The actual site will have strict
privacy controls and member-only sections.
```

## After Approval

Once the team approves the design:

1. ✅ Design approved
2. ⏭️ Build real WordPress site
3. ⏭️ Import content
4. ⏭️ Configure member area
5. ⏭️ Implement security (Wordfence, 2FA, etc.)
6. ⏭️ Deploy to VPS-D8
7. ⏭️ Go live!

See: `DEPLOYMENT.md` for full deployment process.

---

**Questions?** Email: contact@nuj-lcb.org.uk (when live) or respond to this email thread.
