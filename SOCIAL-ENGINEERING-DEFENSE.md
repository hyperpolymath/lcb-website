# Social Engineering Defense - NUJ LCB

**Reality Check**: Social engineering is the #1 way unions get breached. Perfect crypto can't save you from "Hi, I'm the new IT support, what's your password?"

## The Real Threats

### 1. Phishing (Most Common)

```
Attacker pretends to be:
  - National NUJ IT support
  - Certification Officer
  - Your webhost
  - Another branch officer
  - A journalist requesting info

Goal: Get you to:
  - Click a malicious link
  - Download malware
  - Give up passwords
  - Transfer money
  - Share member data
```

### 2. Pretexting

```
Attacker creates elaborate scenario:
  - "I'm from the Daily Mail, doing story on union membership"
  - "I'm a member who lost access, can you email me the list?"
  - "I'm from HMRC investigating your finances"
  - "Emergency! Website hacked! Click this link to fix!"
```

### 3. Strategy Leaks

```
Ways your plans get exposed:
  - Member forwarding "confidential" emails
  - Someone screenshots meeting notes to Twitter
  - Employer spy in member meetings
  - Metadata in leaked documents
  - Unsecured Zoom recordings
  - Public Google Doc links shared accidentally
```

## You Can't Stop Human Nature, But...

### Defense in Depth (Make it Harder)

```yaml
Layer 1 - Technical:
  - 2FA (even if password leaked)
  - IP whitelisting for admin
  - Rate limiting on logins
  - Security keys (not just app)

Layer 2 - Process:
  - Verbal verification for financial requests
  - Callback to known number for urgent requests
  - "Challenge questions" for sensitive data
  - Delay on high-risk actions (24hr wait)

Layer 3 - Culture:
  - "It's OK to be suspicious"
  - "It's OK to say no"
  - "It's OK to double-check"
  - "Nobody will be mad if you verify"
```

## Practical Social Engineering Defenses

### Rule 1: Verify EVERYTHING Urgent

```
If someone says "URGENT" or "EMERGENCY":
  1. STOP
  2. Take a breath
  3. Verify via different channel:
     - Phone call to KNOWN number (not one they gave you)
     - Signal message to person directly
     - In-person verification
  4. Never act on email alone

Remember: Real emergencies are rare. Fake ones are common.
```

### Rule 2: The Password Rule

```
NEVER GIVE PASSWORDS:
  - Not to IT support
  - Not to your manager
  - Not to National NUJ
  - Not to "security audit"
  - Not to ANYONE

Real IT: "Please reset your password via [official link]"
Fake IT: "What's your current password so I can check?"

If someone asks for password: IT'S FAKE. Report it.
```

### Rule 3: The Money Rule

```
FINANCIAL TRANSFERS require:
  1. Written request (email)
  2. Verbal confirmation (phone to KNOWN number)
  3. Meeting minute approval (documented)
  4. Two signatories
  5. 24-hour waiting period for new payees

No exceptions. Even if "urgent." Especially if "urgent."
```

### Rule 4: Member Data Requests

```
Someone requests member list/data:

BEFORE SHARING:
  1. Who are you? (verify identity)
  2. Why do you need it? (legitimate purpose?)
  3. What's the legal basis? (GDPR Article 6)
  4. Have members consented? (check records)
  5. What will you do with it? (data processing agreement)
  6. When will you delete it? (retention schedule)

If ANY answer is unclear: DON'T SHARE

NEVER SHARE:
  - Via email (unencrypted)
  - To personal email addresses
  - Without data processing agreement
  - To anyone outside UK/EU
  - Without telling members first
```

### Rule 5: The Metadata Problem

```
Documents leak info in metadata:

BEFORE SHARING ANY FILE:
  - Strip EXIF from photos (location, camera, date)
  - Remove Word doc author/company info
  - Remove tracked changes/comments
  - Remove PDF annotations
  - Use "Export PDF" not "Save as PDF"
  - Or: Use Dangerzone (convert to image and back)

Google Docs:
  - Don't share "Anyone with link"
  - Always "Specific people"
  - Review permission list monthly
  - Don't use for sensitive strategy docs
```

## Strategy Leak Prevention (It's Hard)

### Classification System

```
PUBLIC - Anyone can see
  - Event announcements
  - Public campaigns
  - General union news

MEMBERS ONLY - NUJ LCB members
  - Meeting agendas (general)
  - Training materials
  - Member benefits info

OFFICERS ONLY - Branch executive
  - Strategy discussions
  - Employer negotiations prep
  - Budget details

CONFIDENTIAL - Need-to-know only
  - Member personal data
  - Legal advice
  - Disciplinary matters
  - Whistleblower reports
```

### Meeting Security

```
For strategy discussions:

DON'T:
  ❌ Post Zoom links publicly
  ❌ Record without warning
  ❌ Share meeting notes in group chat
  ❌ Discuss on public Twitter
  ❌ Use venue with eavesdropping risk

DO:
  ✅ Password-protect Jitsi rooms
  ✅ Verbal-only for most sensitive items
  ✅ "No recordings" rule enforced
  ✅ Meeting notes: "Chatham House Rule"
  ✅ Check everyone on call before starting
```

### The Spy Problem

```
Reality: Employers sometimes plant spies in unions.

You can't prevent this 100%, but:

  1. Trust, but verify
     - New members: Vet before sharing sensitive info
     - Unusual questions: "Why do you need to know?"
     - Too eager: "That's helpful, but..."

  2. Need-to-know basis
     - Not everyone needs full strategy docs
     - Compartmentalize sensitive info
     - Critical decisions: Officers only

  3. Watch for red flags
     - Always asking about employer negotiations
     - Sharing info back to management
     - Unusually keen on member lists
     - Shows up, takes notes, never volunteers

  4. When in doubt
     - Slow-walk information
     - Don't act on suspicion alone
     - Document concerning behavior
     - Discuss with trusted officers
```

## Training Your Officers

### Monthly Security Reminders

```
Branch meeting agenda item: "Security Minute"

Rotate topics:
  - Month 1: Phishing recognition
  - Month 2: Password hygiene
  - Month 3: Document metadata
  - Month 4: Social engineering stories
  - Month 5: Member data handling
  - Month 6: Incident reporting

Make it:
  - 5 minutes max
  - One specific tip
  - Recent real example
  - No tech jargon
```

### Phishing Drill (Quarterly)

```
Send fake phishing email to officers:

Example:
  From: nuj-support@nuj-0rg.uk (note: 0 not o)
  Subject: URGENT: Security Update Required
  Body: "Click here to update your password or account will be locked"

Track who clicks. Then:
  - No punishment (this is training!)
  - Explain what gave it away
  - Praise those who reported it
  - Discuss as group
```

### "War Stories" Session

```
Once a year: Share social engineering stories

Invite someone who:
  - Got phished (no shame!)
  - Spotted a pretexting attack
  - Had data leaked accidentally
  - Recovered from breach

Learn from real experiences.
```

## When Social Engineering Succeeds

### You WILL Get Phished Eventually

```
When (not if) someone clicks the link:

IMMEDIATE:
  1. Don't panic
  2. Disconnect device from network
  3. Change all passwords (from different device)
  4. Notify IT officer
  5. Run malware scan
  6. Check for unauthorized access

FOLLOWUP:
  7. What data was exposed?
  8. What accounts were accessed?
  9. Notify affected people
  10. Report to ICO if member data involved
  11. Learn from incident (no blame!)
  12. Update training
```

### Post-Incident Review

```
Ask (no blame):
  - What made this convincing?
  - What could have prevented it?
  - How do we update training?
  - What technical controls would help?

DON'T ask:
  - "How could you fall for this?"
  - "Didn't you know better?"
  - "This is so obvious!"

Remember: Attackers are professionals. Being fooled is human.
```

## The Uncomfortable Truth

### You Can't Win, But You Can Lose Slower

```
Reality:
  - Determined attacker will find a way
  - Social engineering works
  - Humans are the weakness
  - Perfect security is impossible

BUT:
  - Make it harder (they move to easier target)
  - Make it slower (buys time to detect)
  - Make it cost more (they have budgets too)
  - Make recovery faster (limit damage)

Goal: Not to be unhackable. Goal: Not to be the easiest target.
```

### Risk Acceptance

```
Things you probably can't prevent:
  - Member forwarding emails
  - Someone getting phished eventually
  - Employer finding out general campaign plans
  - Metadata leaking some info
  - Insider threat (if determined)

Things you CAN limit:
  - How much data is exposed
  - How fast you detect it
  - How much it spreads
  - How quickly you recover
  - How much damage it does
```

## Practical Checklist

### For Every Officer

- [ ] I use unique passwords (via password manager)
- [ ] I have 2FA on email and website
- [ ] I verify financial requests by phone
- [ ] I don't share member data without legal basis
- [ ] I strip metadata from documents before sharing
- [ ] I report suspicious emails
- [ ] I know who to call if something goes wrong
- [ ] I've read the phishing examples
- [ ] I understand it's OK to be suspicious
- [ ] I know breach response process

### For Secretary/Chair

- [ ] Security training at onboarding
- [ ] Security reminder at each meeting
- [ ] Phishing drill conducted quarterly
- [ ] Incident response plan exists and tested
- [ ] Member data handling policy documented
- [ ] Data protection training completed
- [ ] Breach notification process understood
- [ ] Contact list for incidents (ICO, police, NUJ)
- [ ] Backup decision-maker identified
- [ ] Annual security review scheduled

## Resources

### Useful Links

- **Action Fraud**: https://www.actionfraud.police.uk/ (report scams)
- **ICO Guidance**: https://ico.org.uk/for-organisations/ (GDPR)
- **NCSC Advice**: https://www.ncsc.gov.uk/section/advice-guidance/all-topics (UK cyber security)
- **Phishing Quiz**: https://phishingquiz.withgoogle.com/ (practice spotting phishing)

### Internal Contacts

- **Security Issues**: secretary@nuj-lcb.org.uk
- **Data Breach**: dpo@nuj.org.uk (National NUJ)
- **ICO Report**: casework@ico.org.uk (personal data breach)
- **Police (Non-Emergency)**: 101
- **Police (Emergency)**: 999

### Report Suspicious Activity

```
If you receive suspicious email:
  1. Don't click links
  2. Don't reply
  3. Forward to: phishing@nuj-lcb.org.uk
  4. Delete original

If you clicked link:
  1. Disconnect network
  2. Call: [Branch Secretary Phone]
  3. Change passwords (from different device)
  4. Don't hide it (we can only help if we know!)
```

## The Bottom Line

```
Social engineering defense is about:
  ✅ Culture of healthy skepticism
  ✅ Making it OK to verify
  ✅ Multiple layers of defense
  ✅ Fast detection and response
  ✅ Learning from incidents
  ✅ Accepting you're human

NOT about:
  ❌ Perfect security
  ❌ Never making mistakes
  ❌ Blaming victims
  ❌ Paranoia
  ❌ Making work impossible
```

**Remember**: Attackers are professionals. Being fooled doesn't make you stupid. But learning from it makes you stronger.

---

*"The only truly secure system is one that is powered off, cast in a block of concrete and sealed in a lead-lined room with armed guards. And even then I have my doubts." - Gene Spafford*
