---
title: NUJ London Central Branch
nuj_root: https://nuj-lcb.org.uk
---
<style>
:root {
  --nuj-green-dark: #006747;
  --nuj-green: #008559;
  --nuj-green-light: #00a572;
  --nuj-green-pale: #e6f4f0;
  --nuj-grey-dark: #2b2b2b;
  --nuj-grey: #4c4c4c;
  --nuj-grey-light: #a0a0a0;
  --nuj-grey-pale: #f5f5f5;
  --nuj-white: #ffffff;
}
* { box-sizing: border-box; margin: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: var(--nuj-grey-pale); color: var(--nuj-grey); }
.site-header { background: var(--nuj-green-dark); color: var(--nuj-white); padding: 1rem 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
.header-container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; }
.site-logo { display: flex; align-items: center; gap: 1rem; }
.logo-circle { width: 55px; height: 55px; border-radius: 50%; background: var(--nuj-white); display: flex; align-items: center; justify-content: center; color: var(--nuj-green-dark); font-weight: bold; font-size: 1.3rem; }
.main-nav ul { display: flex; gap: 1.5rem; list-style: none; flex-wrap: wrap; }
.main-nav a { color: var(--nuj-white); text-decoration: none; padding: 0.5rem; font-weight: 500; }
.main-nav a.active, .main-nav a:hover { border-bottom: 2px solid var(--nuj-green-light); }
.container-block { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
.page-content { background: var(--nuj-white); border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); padding: 2.5rem; }
.alert { padding: 1rem; border-radius: 6px; border-left: 4px solid; margin-bottom: 1.5rem; }
.alert-info { background: #e3f2fd; border-color: #2196f3; color: #0d47a1; }
.btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; padding: 0.75rem 2rem; font-weight: 600; text-decoration: none; transition: transform 0.2s ease; }
.btn-primary { background: var(--nuj-green-dark); color: var(--nuj-white); border: 2px solid var(--nuj-green-dark); }
.btn-primary:hover { transform: translateY(-2px); }
.page { display: none; }
.page.active { display: block; }
.comment-section { margin-top: 2rem; }
.comment-form { background: var(--nuj-grey-pale); padding: 1.5rem; border-radius: 6px; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.35rem; font-weight: 600; }
.form-group input, .form-group textarea { width: 100%; padding: 0.65rem; border: 1px solid var(--nuj-grey-light); border-radius: 4px; }
.site-footer { background: var(--nuj-grey-dark); color: var(--nuj-white); padding: 2rem 1rem; margin-top: 3rem; }
.footer-text { text-align: center; font-size: 0.9rem; }
.page-nav { display: flex; align-items: center; gap: 1rem; }
@media (max-width: 768px) { .main-nav ul { flex-direction: column; width: 100%; } .page-content { padding: 1.5rem; } }
</style>
<header class="site-header" role="banner">
  <div class="header-container">
    <div class="site-logo">
      <div class="logo-circle">NUJ</div>
      <div>
        <strong style="font-size:1.25rem;">NUJ London Central Branch</strong>
        <div style="font-size:0.9rem; opacity:0.8;">Journalists' rights across central London</div>
      </div>
    </div>
    <nav class="main-nav" role="navigation" aria-label="Primary navigation">
      <ul>
        <li><a href="#" data-page="home" class="active" aria-current="page">Home</a></li>
        <li><a href="#" data-page="about">About</a></li>
        <li><a href="#" data-page="join">Join</a></li>
        <li><a href="#" data-page="officers">Officers</a></li>
        <li><a href="#" data-page="contact">Contact</a></li>
        <li><a href="#" data-page="social">LinkedIn</a></li>
        <li><a href="#" data-page="policies">Policies</a></li>
      </ul>
    </nav>
  </div>
</header>
<main class="container-block" role="main">
  <div id="home" class="page active">
    <div class="page-content">
      <div class="alert alert-info"><strong>Shareable Preview:</strong> This multi-tab experience showcases the LCB narrative for your team.</div>
      <h1>Welcome to NUJ London Central Branch</h1>
      <p style="color:var(--nuj-green-dark); font-weight:600;">Standing together for independent media and journalists' protection across central London.</p>
      <h2>What We Do</h2>
      <ul>
        <li>Workplace support including legal advice, contract negotiation, and dispute resolution.</li>
        <li>Training &amp; development across AI journalism, data reporting, and multimedia skills.</li>
        <li>Campaigns for fair pay, safety, and press freedom.</li>
        <li>Community of 500+ journalists spanning national, broadcast, and freelance media.</li>
      </ul>
      <div class="page-nav">
        <a class="btn btn-primary" href="#" data-page="join">Join NUJ LCB</a>
        <a class="btn" href="#" data-page="policies" style="border:2px solid var(--nuj-green); color:var(--nuj-green);">See Policies</a>
      </div>
    </div>
  </div>

  <div id="about" class="page">
    <div class="page-content">
      <h1>About Us</h1>
      <p>NUJ LCB is the union home for central London journalists, photographers, and media workers. We advocate for fairness, diversity, and safety.</p>
      <h2>Structure</h2>
      <p>Elected officers meet monthly, host hybrid sessions, and keep decisions transparent. The AGM elects officers, and any member can propose motions.</p>
    </div>
  </div>

  <div id="join" class="page">
    <div class="page-content">
      <h1>Join NUJ LCB</h1>
      <p>Membership brings collective strength, legal aid, training, insurance, and a vibrant forum.</p>
      <h2>Membership rates</h2>
      <table style="width:100%; border-collapse:collapse;">
        <tr><th style="border:1px solid var(--nuj-grey-light); padding:0.5rem">Income</th><th>Monthly</th><th>Annual</th></tr>
        <tr><td style="border:1px solid var(--nuj-grey-light); padding:0.5rem">Under £15k</td><td>£7.50</td><td>£90</td></tr>
        <tr><td>£15k-£19,999</td><td>£11.92</td><td>£143</td></tr>
        <tr><td>£20k-£24,999</td><td>£14.17</td><td>£170</td></tr>
        <tr><td>£25k-£29,999</td><td>£16.42</td><td>£197</td></tr>
        <tr><td>£30k-£39,999</td><td>£20.92</td><td>£251</td></tr>
        <tr><td>£40k+</td><td>£25.42</td><td>£305</td></tr>
      </table>
      <p>Students get reduced dues.</p>
    </div>
  </div>

  <div id="officers" class="page">
    <div class="page-content">
      <h1>Branch Officers</h1>
      <p>Our officers are all elected volunteers helping members.</p>
      <ul>
        <li>Chair</li><li>Vice Chair</li><li>Treasurer</li><li>Secretary</li><li>Welfare Officer</li><li>Learning &amp; Training Officer</li>
      </ul>
    </div>
  </div>

  <div id="contact" class="page">
    <div class="page-content">
      <h1>Contact</h1>
      <p>Email: <a href="mailto:contact@nuj-lcb.org.uk">contact@nuj-lcb.org.uk</a></p>
      <p>Forum: <a href="https://chat.nuj-lcb.org.uk" target="_blank">chat.nuj-lcb.org.uk</a></p>
      <p>Virtual meetings: <a href="https://conference.nuj-lcb.org.uk" target="_blank">conference.nuj-lcb.org.uk</a></p>
    </div>
  </div>

  <div id="social" class="page">
    <div class="page-content">
      <h1>LinkedIn Updates</h1>
      <p>Follow us on <a href="https://www.linkedin.com/company/108317869/" target="_blank">LinkedIn</a> for news, wins, and events.</p>
    </div>
  </div>

  <div id="policies" class="page">
    <div class="page-content">
      <h1>Policies &amp; Security</h1>
      <p>Our full policy set lives at <a href="https://nuj-lcb.org.uk/policies/security-policy" target="_blank">/policies/security-policy</a>.</p>
      <p>Need to upload securely? Visit <a href="https://stfp.nuj-lcb.org.uk/security-report" target="_blank">stfp.nuj-lcb.org.uk/security-report</a>.</p>
      <div style="margin-top:1rem;">
        <strong>Highlighted policy</strong>
        <p>We comply with UK GDPR, UK Data Protection Act, Equality Act, and Consent-Aware HTTP. Report issues to the contact email above.</p>
      </div>
    </div>
  </div>
</main>
<footer class="site-footer">
  <div class="footer-text">
    <p>&copy; 2026 NUJ London Central Branch. Part of the NUJ.</p>
  </div>
</footer>
<script>
const pages = document.querySelectorAll('.main-nav a');
const sections = document.querySelectorAll('.page');
pages.forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const target = link.getAttribute('data-page');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(target)?.classList.add('active');
    pages.forEach(p => p.classList.remove('active'));
    link.classList.add('active');
  });
});
</script>
