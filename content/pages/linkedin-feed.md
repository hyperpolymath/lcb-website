# LinkedIn Updates - NUJ London Central Branch

**Follow our latest news and updates on LinkedIn**

## Recent Updates

<div class="linkedin-feed">
  <!-- LinkedIn embed will go here -->
  <!-- This will auto-update with latest posts from LinkedIn page -->
</div>

## How to Integrate LinkedIn Feed

### Method 1: LinkedIn Page Plugin (Official)

**Via WordPress Plugin:**

1. **Install**: WP LinkedIn Auto Publish
   ```bash
   wp plugin install wp-linkedin-auto-publish --activate
   ```

2. **Configure:**
   - Settings → LinkedIn Auto Publish
   - Connect LinkedIn company page
   - Select: "Display recent posts"
   - Number of posts: 5
   - Layout: Card grid

3. **Add to page:**
   ```
   [linkedin-feed page="nuj-london-central" count="5"]
   ```

### Method 2: RSS Feed (Simple)

**If LinkedIn provides RSS:**

1. **Install**: Feedzy RSS Feeds
   ```bash
   wp plugin install feedzy-rss-feeds --activate
   ```

2. **Add to page:**
   ```
   [feedzy-rss feeds="https://www.linkedin.com/company/nuj-london-central/rss" max="5" feed_title="no" thumb="yes"]
   ```

### Method 3: Manual Embed (Most Control)

**LinkedIn Post Embed Code:**

1. Go to LinkedIn post
2. Click "..." → "Embed this post"
3. Copy embed code
4. Paste in WordPress (HTML block)

**Example:**
```html
<iframe src="https://www.linkedin.com/embed/feed/update/urn:li:share:123456789"
        height="600" width="100%" frameborder="0" allowfullscreen=""
        title="Embedded post"></iframe>
```

### Method 4: Custom API Integration (Advanced)

**Requires LinkedIn Company Page API access**

See: `LINKEDIN-API-INTEGRATION.md` for details

## LinkedIn Highlights on Homepage

### Homepage Section (Top 2 Posts)

**Add to homepage after "Quick Links" section:**

```html
<section class="section">
  <div class="container">
    <h2 class="section-title">Latest from LinkedIn</h2>

    <div class="linkedin-highlights">

      <!-- Post 1 -->
      <div class="linkedin-post-card">
        <div class="post-header">
          <span class="linkedin-icon">in</span>
          <div>
            <strong>NUJ London Central Branch</strong>
            <div class="post-date">2 days ago</div>
          </div>
        </div>
        <div class="post-content">
          <p>[Post text here]</p>
        </div>
        <a href="[LinkedIn post URL]" class="view-on-linkedin" target="_blank">
          View on LinkedIn →
        </a>
      </div>

      <!-- Post 2 -->
      <div class="linkedin-post-card">
        <div class="post-header">
          <span class="linkedin-icon">in</span>
          <div>
            <strong>NUJ London Central Branch</strong>
            <div class="post-date">5 days ago</div>
          </div>
        </div>
        <div class="post-content">
          <p>[Post text here]</p>
        </div>
        <a href="[LinkedIn post URL]" class="view-on-linkedin" target="_blank">
          View on LinkedIn →
        </a>
      </div>

    </div>

    <div style="text-align: center; margin-top: 2rem;">
      <a href="/linkedin-updates" class="btn-secondary">
        View All LinkedIn Updates →
      </a>
    </div>
  </div>
</section>
```

### Styling for LinkedIn Cards

```css
/* Add to theme CSS */

.linkedin-highlights {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.linkedin-post-card {
  background: var(--nuj-white);
  border: 2px solid #0077b5; /* LinkedIn blue */
  border-radius: 8px;
  padding: 1.5rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.linkedin-post-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 119, 181, 0.2);
}

.post-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--nuj-grey-pale);
}

.linkedin-icon {
  width: 40px;
  height: 40px;
  background: #0077b5;
  color: white;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.25rem;
}

.post-date {
  color: var(--nuj-grey-medium);
  font-size: 0.875rem;
}

.post-content {
  color: var(--nuj-grey);
  line-height: 1.6;
  margin-bottom: 1rem;
}

.view-on-linkedin {
  color: #0077b5;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.view-on-linkedin:hover {
  text-decoration: underline;
}
```

## Content Update Workflow

### Manual Updates (Weekly)

**Recommended approach** (simplest, most control):

1. Check LinkedIn page every Monday
2. Copy text from 2 most recent posts
3. Update homepage HTML/WordPress
4. Update full feed page
5. Takes ~10 minutes

### Automated Updates (Plugin)

**If using WP LinkedIn Auto Publish:**
- Auto-fetches every 6 hours
- No manual work needed
- Requires LinkedIn API setup

## Privacy Considerations

**LinkedIn posts are PUBLIC** - make sure nothing sensitive is posted:

✅ Safe to share:
- Public campaign announcements
- Event promotions
- General news
- Training opportunities

❌ Do NOT share on LinkedIn:
- Member names/lists
- Internal strategy
- Workplace negotiations details
- Confidential member issues
- Meeting minutes

## Setup Checklist

- [ ] Create LinkedIn Company Page for NUJ LCB
- [ ] Post 3-5 initial updates
- [ ] Install LinkedIn feed plugin
- [ ] Configure feed settings
- [ ] Add feed to /linkedin-updates page
- [ ] Add highlights to homepage
- [ ] Test auto-refresh working
- [ ] Document update process
- [ ] Train officers on LinkedIn posting

## LinkedIn Page Guidelines

**Post frequency**: 2-3 times per week

**Content types:**
- Event announcements
- Campaign updates
- Training opportunities
- Workplace victories
- Press freedom news
- Member testimonials (with consent)

**Tone**: Professional but passionate

**Hashtags**: #NUJ #Journalism #UnionStrong #PressRights

---

**Questions?** Email: communications@nuj-lcb.org.uk
