# AI Usage and Consent Policy

**Effective Date**: 28 January 2026
**Last Updated**: 28 January 2026

## Our Commitment

NUJ London Central Branch respects the rights of content creators and maintains strict boundaries around AI usage of our content. This policy aligns with the **Consent-Aware HTTP** standards and **AI Boundary Declaration Protocol (AIBDP)**.

## Content Usage Boundaries

### What AI Systems MAY Do

✅ **Permitted Uses:**
- Index our content for search engines (Google, Bing, etc.)
- Generate short summaries or snippets (under 100 words)
- Accessibility features (text-to-speech, translation for disabled users)
- Archive our content for historical preservation

### What AI Systems MAY NOT Do

❌ **Prohibited Uses:**
- Train language models (LLMs) on our content
- Generate derivative works mimicking our content
- Scrape content for commercial AI training datasets
- Create synthetic content claiming to be from our branch
- Use our content to generate competing union materials

## Technical Implementation

### HTTP 430: Consent Required

Our server implements **HTTP Status Code 430 (Consent Required)** as defined in `draft-jewell-http-430-consent-required`. Systems that attempt unauthorized AI usage will receive:

```
HTTP/1.1 430 Consent Required
Content-Type: text/plain

This content requires explicit consent for AI usage.
See: https://nuj-lcb.org.uk/.well-known/aibdp.json
```

### AI Boundary Declaration

Our AI usage boundaries are machine-readable at:

**`/.well-known/aibdp.json`**

```json
{
  "version": "1.0",
  "policies": {
    "training": "prohibited",
    "generation": "prohibited",
    "indexing": "allowed",
    "summarization": "allowed-short",
    "accessibility": "allowed"
  },
  "contact": "contact@nuj-lcb.org.uk",
  "effective": "2026-01-28"
}
```

### robots.txt Configuration

Our `robots.txt` includes AI-specific directives:

```
# Search engine indexing: Allowed
User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

# AI Training crawlers: Disallowed
User-agent: GPTBot
Disallow: /

User-agent: ChatGPT-User
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: anthropic-ai
Disallow: /

User-agent: Claude-Web
Disallow: /

User-agent: cohere-ai
Disallow: /

# Default: Disallow AI training
User-agent: *
Disallow: /wp-admin/
Disallow: /wp-content/uploads/
```

## User Rights

### As a Content Creator

If your content appears on our site (articles, comments, forum posts), you retain:

1. **Copyright** - You own your words
2. **Attribution** - You must be credited
3. **Control** - You can request removal
4. **AI Protection** - Your content shares our AI boundaries

### Requesting Content Removal

To remove your content from our site:

1. Email **contact@nuj-lcb.org.uk**
2. Include: URL, your name, reason for removal
3. We'll respond within 7 days
4. Content removed within 14 days

## AI Usage by NUJ LCB

### Our Own Use of AI

We may use AI tools for:

- **Accessibility**: Text-to-speech, captions, alt-text generation
- **Translation**: Making content available in other languages
- **Administration**: Spelling/grammar checking, meeting transcription
- **Moderation**: Detecting spam or abusive content

We do NOT use AI to:
- Write articles or content for us
- Replace journalists or editors
- Make editorial decisions
- Generate fake member testimonials

### Transparency

When we use AI tools, we:
- Disclose their use clearly
- Maintain human oversight
- Verify accuracy before publication
- Credit human authors, not AI systems

## Third-Party AI Systems

### Reporting Violations

If you find our content being used by AI systems in violation of this policy:

1. **Email**: contact@nuj-lcb.org.uk
2. **Include**: URL of violation, system name, evidence
3. **We will**: Investigate and take action (DMCA, legal notice, etc.)

### Known Violations

We maintain a list of AI systems that have violated our boundaries:

- [To be updated as violations are identified]

## Legal Basis

This policy is grounded in:

1. **Copyright Law** - Our content is protected by copyright
2. **Database Rights** - Our structured content is protected
3. **Terms of Service** - Binding agreement for site use
4. **Consent-Aware HTTP** - Emerging web standard for AI boundaries

Unauthorized AI training on our content may constitute:
- Copyright infringement
- Breach of terms of service
- Violation of database rights
- Misappropriation of trade secrets (where applicable)

## Enforcement

### Technical Enforcement

- **HTTP 430 responses** for unauthorized AI crawlers
- **IP blocking** for repeated violations
- **Rate limiting** to prevent scraping
- **Honeypot pages** to detect AI crawlers

### Legal Enforcement

We may pursue:
- DMCA takedown notices
- Cease and desist letters
- Legal action for persistent violations
- Damages for commercial AI training use

## Ethical AI Use

We believe AI can benefit journalism when used ethically. We support:

✅ **Ethical AI practices:**
- Transparency about AI use
- Compensation for content creators
- Opt-in (not opt-out) training data collection
- Attribution and credit for human creators
- Accessibility applications of AI

❌ **We oppose:**
- Training on content without consent
- Replacing journalists with AI-generated content
- Synthetic journalism presented as human-written
- Exploitation of creators' work without compensation

## Updates to This Policy

We may update this policy as:
- AI technology evolves
- New standards emerge (e.g., AIBDP adoption)
- Legal frameworks develop
- Community needs change

**Last updated**: 28 January 2026

Changes will be posted with 30 days' notice. Continued use of our site after changes constitutes acceptance.

## Questions and Concerns

### Contact Us

- **Email**: contact@nuj-lcb.org.uk
- **Subject line**: "AI Policy Question"
- **Response time**: Within 7 days

### Resources

- **Consent-Aware HTTP**: [https://consent-aware-http.org](https://consent-aware-http.org)
- **AIBDP Specification**: See draft-jewell-aibdp
- **NUJ AI Guidelines**: [www.nuj.org.uk/ai](https://www.nuj.org.uk/ai)

## Related Policies

- [Privacy Policy](/privacy-policy) - How we handle personal data
- [Cookie Policy](/cookie-policy) - Our use of cookies
- [Terms of Service](/terms-of-service) - Binding site usage terms
- [Copyright Notice](/copyright) - Content licensing and attribution

---

*This policy aligns with the Consent-Aware HTTP initiative and implements HTTP Status Code 430 (Consent Required) as specified in draft-jewell-http-430-consent-required-00.*

**Enforcement begins**: 28 January 2026

**Questions?** Email contact@nuj-lcb.org.uk with "AI Policy" in the subject line.
