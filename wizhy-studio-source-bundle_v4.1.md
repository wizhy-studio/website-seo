# Wizhy Studio — Source Code Bundle (v4.1 — Feature Update)

This bundle contains the **new/changed text source files only** for v4.1.
Binary assets (fonts, the OG image, the checklist PDF) are NOT embedded here
as base64 — they are provided as real files alongside this bundle in the
delivered package/zip. Font files (assets/fonts/*.woff2) are UNCHANGED from
v4.0 — reuse your existing v4.0 font files as-is.

Feed this .md (plus the real asset files) into any AI chat or Antigravity to
resume development without rebuilding from scratch.

For the file marked `.github/workflows/supabase-keep-alive.yml` below, note
that in the actual delivered package it lives at path
`.github/workflows/supabase-keep-alive.yml` relative to your repo root (the
`antigravity-notes/` prefix shown in this bundle's heading is just this
sandbox's internal folder naming — strip it).

See `wizhy-studio-project-summary_v4.1.md` for full context, the Antigravity
backend TODO list, and the complete feature list.

---

## How to reconstruct this project

1. Start from your existing v4.0 project folder (or the v4.0 source bundle).
2. Overwrite `index.html`, `css/style.css`, and `js/script.js` with the
   versions below.
3. Add `robots.txt` and `sitemap.xml` at the project root.
4. Add `.github/workflows/supabase-keep-alive.yml` (create the folders if
   they don't exist) once the project is on GitHub + Supabase.
5. Copy `assets/og-image.jpg` and `assets/wizhy-website-checklist.pdf` from
   the delivered package into your `assets/` folder.

---

## File: `index.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wizhy Studio | Websites, Designed to Grow Your Business</title>
<meta name="description" content="Wizhy Studio designs and develops high-performance websites, then powers them with proven SEO so you rank on Google, attract the right customers, and grow — delivered the smart way, at the best price.">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://wizhy.in/">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="Wizhy Studio">
<meta property="og:title" content="Wizhy Studio | Websites, Designed to Grow Your Business">
<meta property="og:description" content="High-performance websites + proven SEO, so you rank on Google, attract the right customers, and grow — at the best price.">
<meta property="og:url" content="https://wizhy.in/">
<meta property="og:image" content="https://wizhy.in/assets/og-image.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Wizhy Studio | Websites, Designed to Grow Your Business">
<meta name="twitter:description" content="High-performance websites + proven SEO, so you rank on Google, attract the right customers, and grow — at the best price.">
<meta name="twitter:image" content="https://wizhy.in/assets/og-image.jpg">

<!-- JSON-LD structured data — ⚠️ placeholder values, replace before going live (see project summary "Known Placeholders") -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfessionalService",
  "name": "Wizhy Studio",
  "image": "https://wizhy.in/assets/og-image.jpg",
  "url": "https://wizhy.in/",
  "telephone": "+91-00000-00000",
  "email": "hello@wizhy.in",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "IN"
  },
  "sameAs": [],
  "description": "Web design, development and SEO agency helping businesses build high-performance websites and rank on Google.",
  "areaServed": "IN",
  "openingHours": "Mo-Sa 10:00-19:00"
}
</script>

<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23C8481E'/%3E%3Ctext x='32' y='44' font-family='Georgia,serif' font-size='34' font-weight='700' fill='%23FFF8EF' text-anchor='middle'%3EW%3C/text%3E%3C/svg%3E">

<link rel="stylesheet" href="css/fonts.css">
<link rel="stylesheet" href="css/style.css">

<script>
  (function () {
    try {
      var stored = localStorage.getItem('wizhy-theme');
      var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
    } catch (e) { document.documentElement.setAttribute('data-theme', 'light'); }
  })();
</script>
</head>
<body>

<a href="#main-content" class="skip-link">Skip to main content</a>

<div class="scroll-progress" id="scrollProgress"></div>

<!-- ============ NAVBAR ============ -->
<header class="navbar" id="navbar">
  <div class="container navbar__inner">
    <a href="#home" class="brand">
      <span class="brand__mark">W</span>
      <span class="brand__text">Wizhy<em> Studio</em></span>
    </a>

    <nav class="nav-links" aria-label="Primary">
      <a href="#home" class="nav-link">Home</a>
      <a href="#services" class="nav-link">Services</a>
      <a href="#why-us" class="nav-link">Why Us</a>
      <a href="#process" class="nav-link">Process</a>
      <a href="#testimonials" class="nav-link">Testimonials</a>
      <a href="#faq" class="nav-link">FAQ</a>
      <a href="#contact" class="nav-link">Contact</a>
    </nav>

    <div class="navbar__actions">
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle light and dark theme">
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.5 14.5A8.5 8.5 0 1 1 9.5 3.5a7 7 0 0 0 11 11Z"/></svg>
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"/></svg>
      </button>
      <a href="#contact" class="btn btn--primary btn--sm">Get Free Quote</a>
      <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobileMenu">
  <a href="#home" class="mobile-link">Home</a>
  <a href="#services" class="mobile-link">Services</a>
  <a href="#why-us" class="mobile-link">Why Us</a>
  <a href="#process" class="mobile-link">Process</a>
  <a href="#testimonials" class="mobile-link">Testimonials</a>
  <a href="#faq" class="mobile-link">FAQ</a>
  <a href="#contact" class="mobile-link">Contact</a>
  <a href="#contact" class="btn btn--primary btn--block">Get Free Quote</a>
</div>

<main id="main-content">

<!-- ============ HERO ============ -->
<section class="hero" id="home">
  <span class="hero__blob hero__blob--1" aria-hidden="true"></span>
  <span class="hero__blob hero__blob--2" aria-hidden="true"></span>
  <div class="container hero__inner">
    <div>
      <span class="eyebrow reveal">Web Design &amp; SEO Studio</span>
      <h1 class="hero__title">
        <span class="line">
          <span class="word-reveal"><span>Websites</span></span> <span class="word-reveal"><span>crafted</span></span> <span class="word-reveal"><span>to</span></span> <span class="word-reveal"><span>look</span></span>
        </span>
        <span class="line">
          <span class="word-reveal"><span>stunning</span></span><span class="word-reveal"><span>—</span></span> <span class="word-reveal"><span class="accent-text">built</span></span> <span class="word-reveal"><span class="accent-text">to</span></span> <span class="word-reveal"><span class="accent-text">rank</span></span> <span class="word-reveal"><span class="accent-text">&amp;</span></span> <span class="word-reveal"><span class="accent-text">grow.</span></span>
        </span>
      </h1>
      <p class="hero__subtitle reveal">Wizhy Studio designs and develops high-performance websites, then powers them with proven SEO so you rank on Google, attract the right customers, and grow — delivered the smart way, at the best price.</p>
      <div class="hero__cta reveal">
        <span class="magnetic"><a href="#contact" class="btn btn--primary btn--lg">Get a Custom Quote <span class="btn__arrow">→</span></a></span>
        <span class="magnetic"><a href="#services" class="btn btn--ghost btn--lg">Explore Services</a></span>
      </div>
      <div class="hero__proof reveal">
        <div class="hero__avatars" aria-hidden="true">
          <span>J</span><span>D</span><span>M</span><span>K</span><span>R</span><span>S+</span>
        </div>
        <p class="hero__proof-text"><strong>150+</strong> businesses already growing with Wizhy Studio</p>
      </div>
    </div>

    <div class="hero__visual reveal">
      <div class="orbit">
        <svg class="orbit__ring" viewBox="0 0 340 340" aria-hidden="true">
          <circle cx="170" cy="170" r="165" fill="none" stroke="var(--border)" stroke-width="1" stroke-dasharray="2 8"/>
        </svg>
        <div class="orbit__core">
          <span class="num" data-count="150">0</span>
          <span class="label">Projects Delivered</span>
        </div>
      </div>
      <div class="hero__card hero__card--1">
        <span class="icon-dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:18px;height:18px"><path d="M3 17l5-5 4 4 8-9"/><path d="M15 7h5v5"/></svg></span>
        <div><strong>3x Ranking Growth</strong><span>Avg. Google visibility</span></div>
      </div>
      <div class="hero__card hero__card--2">
        <span class="icon-dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:18px;height:18px"><path d="M20 6L9 17l-5-5"/></svg></span>
        <div><strong>98% Satisfaction</strong><span>Client feedback</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ TRUST MARQUEE ============ -->
<section class="marquee-section">
  <p class="marquee-label">Technologies &amp; Platforms We Work With</p>
  <div class="marquee">
    <div class="marquee__track">
      <span class="tech-chip">HTML5</span><span class="tech-chip">CSS3</span><span class="tech-chip">JavaScript</span><span class="tech-chip">React</span><span class="tech-chip">WordPress</span><span class="tech-chip">Shopify</span><span class="tech-chip">Node.js</span><span class="tech-chip">Google Analytics</span><span class="tech-chip">Search Console</span><span class="tech-chip">AWS</span><span class="tech-chip">Figma</span>
      <span class="tech-chip">HTML5</span><span class="tech-chip">CSS3</span><span class="tech-chip">JavaScript</span><span class="tech-chip">React</span><span class="tech-chip">WordPress</span><span class="tech-chip">Shopify</span><span class="tech-chip">Node.js</span><span class="tech-chip">Google Analytics</span><span class="tech-chip">Search Console</span><span class="tech-chip">AWS</span><span class="tech-chip">Figma</span>
    </div>
  </div>
</section>

<!-- ============ INSTANT SEO AUDIT TOOL (lead magnet) ============ -->
<section class="section" id="seo-audit">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow">Free Instant Check</span>
      <h2 class="section-title">Wondering how your current website stacks up?</h2>
      <p class="section-subtitle">Drop your website URL below for an instant, illustrative SEO &amp; performance snapshot — a quick taste of the kind of audit we run for every client before we start work.</p>
    </div>

    <div class="audit-tool reveal">
      <form class="audit-tool__form" id="auditForm">
        <label for="auditUrl" class="visually-hidden" style="position:absolute;left:-9999px;">Website URL</label>
        <input type="text" id="auditUrl" name="auditUrl" placeholder="yourbusiness.com" required inputmode="url" autocomplete="url">
        <span class="magnetic"><button type="submit" class="btn btn--primary">Run Free Audit</button></span>
      </form>
      <p class="audit-tool__note">No signup needed. Takes about 2 seconds.</p>
      <p class="audit-tool__error" id="auditError"></p>

      <div class="audit-tool__loading" id="auditLoading">
        <span class="audit-spinner" aria-hidden="true"></span>
        <span>Scanning your site…</span>
      </div>

      <div class="audit-tool__result" id="auditResult">
        <div class="audit-result__head">
          <div class="audit-score-ring">
            <svg viewBox="0 0 120 120">
              <circle class="track" cx="60" cy="60" r="52"/>
              <circle class="fill" id="auditScoreFill" cx="60" cy="60" r="52"/>
            </svg>
            <div class="audit-score-ring__num">
              <strong id="auditScoreNum">0</strong>
              <span>/ 100</span>
            </div>
          </div>
          <div class="audit-result__summary">
            <h3>Here's your snapshot</h3>
            <p>Based on common on-page &amp; technical factors. Our full manual audit goes much deeper — into Core Web Vitals, backlinks, keyword gaps, and competitor benchmarking.</p>
            <p class="audit-result__url" id="auditResultUrl"></p>
          </div>
        </div>

        <div class="audit-suggestions" id="auditSuggestions"></div>

        <div class="audit-result__cta">
          <p>Want the full, human-reviewed audit — free — with a clear action plan?</p>
          <span class="magnetic"><a href="#contact" class="btn btn--primary">Get My Free Full Audit</a></span>
        </div>
      </div>

      <p class="audit-disclaimer">This is an illustrative, automated snapshot meant to demonstrate the kind of factors we look at — not a substitute for a full manual technical audit.</p>
    </div>
  </div>
</section>

<!-- ============ SERVICES ============ -->
<section class="section section--alt" id="services">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">What We Do</span>
      <h2 class="section-title">Everything your business needs, under one roof.</h2>
      <p class="section-subtitle">From your first line of code to your first page-one Google ranking — we cover the full journey.</p>
    </div>

    <div class="bento">
      <div class="bento-card reveal">
        <span class="bento-card__num">01</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span>
        <h3>Website Development</h3>
        <p>Custom-coded, blazing-fast, mobile-first websites built to convert visitors into customers.</p>
      </div>

      <div class="bento-card reveal">
        <span class="bento-card__num">02</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h2l2.6 12.6a2 2 0 0 0 2 1.6h8a2 2 0 0 0 2-1.6L21 7H6"/></svg></span>
        <h3>E-commerce Development</h3>
        <p>Secure, scalable online stores with smooth checkout experiences that turn browsers into buyers.</p>
      </div>

      <div class="bento-card bento-card--wide reveal">
        <div>
          <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
          <h3>SEO &amp; Google Ranking</h3>
          <p>On-page &amp; technical SEO that helps your business get discovered by the right customers on Google — the core of everything we build.</p>
        </div>
        <span class="bento-card__num" style="position:static;">SEO</span>
      </div>

      <div class="bento-card reveal">
        <span class="bento-card__num">04</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg></span>
        <h3>App Development</h3>
        <p>Responsive web apps and cross-platform mobile experiences tailored to your workflow.</p>
      </div>

      <div class="bento-card reveal">
        <span class="bento-card__num">05</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4Z"/></svg></span>
        <h3>Maintenance &amp; Hosting</h3>
        <p>Reliable hosting, uptime monitoring, backups &amp; updates so your site always stays fast and secure.</p>
      </div>

      <div class="bento-card reveal">
        <span class="bento-card__num">06</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><path d="M12 19l7-7 3 3-7 7-3-3Z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18"/><path d="M2 2l7.5 7.5"/><circle cx="11" cy="11" r="1.5"/></svg></span>
        <h3>Branding &amp; Logo Design</h3>
        <p>Memorable logos and brand identity systems that make your business instantly recognizable.</p>
      </div>

      <div class="bento-card reveal">
        <span class="bento-card__num">07</span>
        <span class="bento-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px"><circle cx="18" cy="5" r="2.6"/><circle cx="6" cy="12" r="2.6"/><circle cx="18" cy="19" r="2.6"/><path d="M8.3 10.7l7.3-4.4M8.3 13.3l7.3 4.4"/></svg></span>
        <h3>Social Media Marketing</h3>
        <p>Content, creatives &amp; campaigns that grow your audience and drive qualified leads.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ WHY US ============ -->
<section class="section" id="why-us">
  <div class="container why-us">
    <div>
      <span class="eyebrow reveal">Why Wizhy Studio</span>
      <h2 class="section-title reveal">Efficient delivery. Best price. Real growth.</h2>
      <p class="section-subtitle reveal">We don't just hand you a website and disappear. We build it right, optimize it for Google, and stay with you as your long-term growth partner.</p>

      <ul class="check-list" style="margin-top:36px;">
        <li class="reveal">
          <span class="check-icon">✓</span>
          <div><strong>Efficient, streamlined process</strong><p>No delays, no confusion — clear milestones from day one.</p></div>
        </li>
        <li class="reveal">
          <span class="check-icon">✓</span>
          <div><strong>Best-in-class quality</strong><p>Clean code, considered design, optimized performance.</p></div>
        </li>
        <li class="reveal">
          <span class="check-icon">✓</span>
          <div><strong>Competitive, transparent pricing</strong><p>Premium results without the premium markup.</p></div>
        </li>
        <li class="reveal">
          <span class="check-icon">✓</span>
          <div><strong>SEO-driven growth</strong><p>Every site is built with Google ranking in mind, from day one.</p></div>
        </li>
        <li class="reveal">
          <span class="check-icon">✓</span>
          <div><strong>Dedicated ongoing support</strong><p>We're your long-term digital partner, not a one-time vendor.</p></div>
        </li>
      </ul>
    </div>

    <div class="stat-rings reveal">
      <div class="stat-ring" data-pct="92">
        <svg viewBox="0 0 108 108"><circle class="track" cx="54" cy="54" r="48"/><circle class="fill" cx="54" cy="54" r="48"/></svg>
        <div class="stat-ring__label"><strong style="display:block;font-family:var(--font-display);font-size:1.1rem;color:var(--text-primary);">92%</strong>Delivery Speed</div>
      </div>
      <div class="stat-ring" data-pct="96">
        <svg viewBox="0 0 108 108"><circle class="track" cx="54" cy="54" r="48"/><circle class="fill" cx="54" cy="54" r="48"/></svg>
        <div class="stat-ring__label"><strong style="display:block;font-family:var(--font-display);font-size:1.1rem;color:var(--text-primary);">96%</strong>SEO Readiness</div>
      </div>
      <div class="stat-ring" data-pct="98">
        <svg viewBox="0 0 108 108"><circle class="track" cx="54" cy="54" r="48"/><circle class="fill" cx="54" cy="54" r="48"/></svg>
        <div class="stat-ring__label"><strong style="display:block;font-family:var(--font-display);font-size:1.1rem;color:var(--text-primary);">98%</strong>Client Satisfaction</div>
      </div>
      <div class="stat-ring" data-pct="95">
        <svg viewBox="0 0 108 108"><circle class="track" cx="54" cy="54" r="48"/><circle class="fill" cx="54" cy="54" r="48"/></svg>
        <div class="stat-ring__label"><strong style="display:block;font-family:var(--font-display);font-size:1.1rem;color:var(--text-primary);">95%</strong>Value for Price</div>
      </div>
      <p class="stat-rings__note">*Sample performance indicators — replace with your verified data</p>
    </div>
  </div>
</section>

<!-- ============ PROCESS ============ -->
<section class="section section--alt process" id="process">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow">How We Work</span>
      <h2 class="section-title">A process built for speed &amp; results.</h2>
      <p class="section-subtitle">From first conversation to long-term growth — here's exactly how we take your project forward.</p>
    </div>

    <div class="process__line-wrap" id="processLineWrap">
      <div class="process__line-fill" id="processLineFill"></div>
    </div>

    <div class="process-grid">
      <div class="process-step reveal"><span class="process-step__num">01</span><h3>Discovery &amp; Strategy</h3><p>We understand your business, goals &amp; audience to plan the right approach.</p></div>
      <div class="process-step reveal"><span class="process-step__num">02</span><h3>UI/UX Design</h3><p>Modern, on-brand designs crafted for clarity, trust &amp; conversions.</p></div>
      <div class="process-step reveal"><span class="process-step__num">03</span><h3>Development</h3><p>Clean, fast, scalable code built with best practices &amp; security in mind.</p></div>
      <div class="process-step reveal"><span class="process-step__num">04</span><h3>Testing &amp; QA</h3><p>Cross-device, cross-browser testing so everything works flawlessly.</p></div>
      <div class="process-step reveal"><span class="process-step__num">05</span><h3>SEO Optimization &amp; Launch</h3><p>On-page SEO, speed optimization &amp; Search Console setup before go-live.</p></div>
      <div class="process-step reveal"><span class="process-step__num">06</span><h3>Growth &amp; Support</h3><p>Ongoing SEO, maintenance &amp; marketing to keep you growing on Google.</p></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section" id="testimonials">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow">Client Love</span>
      <h2 class="section-title">What our clients say about us.</h2>
      <p class="section-subtitle">Real feedback from businesses we've helped build and grow online.</p>
      <!-- TODO (Antigravity): replace this hardcoded 3-slide markup with a
           dynamic render from a Supabase `testimonials` table so testimonials
           can be added/edited from an admin view without touching code. Keep
           the same .testimonial-slide / .testimonial-card structure per slide
           so testimonial-slider JS (script.js module 12) keeps working as-is. -->
    </div>

    <div class="testimonial-slider reveal">
      <div class="testimonial-track" id="testimonialTrack">
        <div class="testimonial-slide">
          <div class="testimonial-card">
            <span class="quote-mark">&ldquo;</span>
            <div class="stars">★★★★★</div>
            <p class="quote">Wizhy Studio delivered our website faster than we expected, and within three months we started ranking on the first page of Google. Highly recommend.</p>
            <div class="testimonial-author"><span class="avatar">C1</span><div><strong>Client Name</strong><span>Business Owner, Company Name</span></div></div>
          </div>
        </div>
        <div class="testimonial-slide">
          <div class="testimonial-card">
            <span class="quote-mark">&ldquo;</span>
            <div class="stars">★★★★★</div>
            <p class="quote">Professional, fast, and genuinely invested in our growth. The SEO work they did made a real difference to our enquiries.</p>
            <div class="testimonial-author"><span class="avatar">C2</span><div><strong>Client Name</strong><span>Founder, Company Name</span></div></div>
          </div>
        </div>
        <div class="testimonial-slide">
          <div class="testimonial-card">
            <span class="quote-mark">&ldquo;</span>
            <div class="stars">★★★★★</div>
            <p class="quote">Best price for the quality we received. The team was responsive and the final website looks incredibly premium.</p>
            <div class="testimonial-author"><span class="avatar">C3</span><div><strong>Client Name</strong><span>Marketing Head, Company Name</span></div></div>
          </div>
        </div>
      </div>
      <div class="testimonial-dots" id="testimonialDots"></div>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="section section--alt" id="faq">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow">Frequently Asked</span>
      <h2 class="section-title">Questions we hear a lot.</h2>
      <p class="section-subtitle">Straight answers, before you even have to ask.</p>
    </div>

    <div class="faq-list reveal">
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">How long does a typical website project take?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">Most business websites take 2–4 weeks from kickoff to launch, depending on scope, number of pages, and how quickly content/feedback comes back to us. E-commerce and custom app builds typically take 4–8 weeks. We'll give you an exact timeline in your custom quote.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">Do you also handle SEO, or just design and development?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">SEO is built into every project from day one — not bolted on afterward. That includes technical SEO (site speed, structured data, sitemap, mobile-friendliness) and on-page SEO (titles, meta descriptions, keyword-aligned content). We also offer ongoing SEO &amp; growth retainers after launch.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">What does a project typically cost?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">Pricing depends on scope — a simple brochure site costs far less than a full e-commerce store or custom web app. We don't publish a fixed price list because every business's needs are different; instead, we give you a clear, itemized, no-obligation quote after a short discovery call.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">Will I be able to update the website myself after launch?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">Yes. Depending on the platform we build on (WordPress, Shopify, or a custom CMS), we'll walk you through how to update text, images, and products yourself — plus we're always available for bigger changes or ongoing maintenance.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">Do you offer maintenance and support after the site goes live?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">Yes — we offer ongoing hosting, uptime monitoring, backups, security updates, and SEO/growth retainers so your site keeps performing long after launch. Think of us as your long-term digital partner, not a one-time vendor.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-item__question" aria-expanded="false">Can you redesign or improve my existing website instead of building new?<span class="faq-item__icon" aria-hidden="true"></span></button>
        <div class="faq-item__answer"><div class="faq-item__answer-inner">Absolutely. We regularly audit existing websites (try our free instant audit tool above) and offer redesign, performance, and SEO-focused improvement projects — often faster and cheaper than a full rebuild.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CTA BAND ============ -->
<section class="cta-band">
  <div class="cta-band__marquee" aria-hidden="true">GROW&nbsp;&nbsp;•&nbsp;&nbsp;RANK&nbsp;&nbsp;•&nbsp;&nbsp;CONVERT&nbsp;&nbsp;•&nbsp;&nbsp;REPEAT&nbsp;&nbsp;•&nbsp;&nbsp;GROW&nbsp;&nbsp;•&nbsp;&nbsp;RANK&nbsp;&nbsp;•&nbsp;&nbsp;CONVERT&nbsp;&nbsp;•&nbsp;&nbsp;REPEAT&nbsp;&nbsp;•</div>
  <div class="container cta-band__inner reveal">
    <h2>Ready to build a website that actually grows your business?</h2>
    <p>Tell us about your project — we'll get back with a clear, no-obligation custom quote.</p>
    <span class="magnetic"><a href="#contact" class="btn btn--primary btn--lg">Get a Custom Quote</a></span>
  </div>
</section>

<!-- ============ LEAD MAGNET: FREE CHECKLIST DOWNLOAD ============ -->
<section class="section">
  <div class="container">
    <div class="lead-magnet reveal">
      <div>
        <span class="lead-magnet__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:30px;height:30px"><path d="M12 3v13m0 0l-4-4m4 4l4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg></span>
        <h3>Download our free Website &amp; SEO Checklist</h3>
        <p>The exact 40-point checklist we use internally before launching any client site — speed, SEO, mobile, security and more. Delivered straight to your inbox.</p>
      </div>
      <form class="lead-magnet__form" id="leadMagnetForm">
        <label for="leadMagnetEmail" style="position:absolute;left:-9999px;">Email address</label>
        <input type="email" id="leadMagnetEmail" name="leadMagnetEmail" placeholder="you@business.com" required autocomplete="email">
        <span class="magnetic"><button type="submit" class="btn btn--primary">Send Me the Checklist</button></span>
      </form>
    </div>
    <p class="lead-magnet__note" style="text-align:center;max-width:640px;margin:14px auto 0;">We'll only use your email to send the checklist and occasional growth tips — unsubscribe anytime.</p>
    <p class="lead-magnet__success" id="leadMagnetSuccess" style="text-align:center;">✓ Check your downloads — your checklist is on its way! We've also noted your email so we can follow up with more resources.</p>
  </div>
</section>

<!-- ============ CONTACT ============ -->
<section class="section section--alt" id="contact">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow">Get In Touch</span>
      <h2 class="section-title">Let's start your project.</h2>
      <p class="section-subtitle">Fill in the form or reach out directly — we usually respond within 24 hours.</p>
    </div>

    <div class="contact-grid reveal">
      <div class="glass-card contact-info">
        <h3>Contact Details</h3>
        <a class="contact-row" href="tel:+910000000000" data-copy="+91 00000 00000">
          <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .8 3a2 2 0 0 1-.5 2.1L8 10.1a16 16 0 0 0 6 6l1.3-1.4a2 2 0 0 1 2.1-.5c1 .4 2 .7 3 .8a2 2 0 0 1 1.6 2Z"/></svg></span>
          <div><strong>Phone</strong><span>+91 00000 00000</span></div>
        </a>
        <a class="contact-row" href="mailto:hello@wizhy.in" data-copy="hello@wizhy.in">
          <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/></svg></span>
          <div><strong>Email</strong><span>hello@wizhy.in</span></div>
        </a>
        <a class="contact-row" href="https://wa.me/910000000000" target="_blank" rel="noopener">
          <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px"><path d="M21 11.5a8.4 8.4 0 0 1-8.9 8.4 8.5 8.5 0 0 1-4-1L3 20l1.2-5A8.4 8.4 0 1 1 21 11.5Z"/></svg></span>
          <div><strong>WhatsApp</strong><span>+91 00000 00000</span></div>
        </a>
        <div class="contact-row">
          <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
          <div><strong>Business Hours</strong><span>Mon – Sat, 10 AM – 7 PM IST</span></div>
        </div>

        <p class="social-label">Follow Us</p>
        <div class="social-icons">
          <a class="social-icon" href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
          <a class="social-icon" href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 10v7M7 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 13v4"/></svg></a>
          <a class="social-icon" href="#" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px"><path d="M4 4l16 16M20 4L4 20"/></svg></a>
          <a class="social-icon" href="#" aria-label="Behance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px"><rect x="2" y="7" width="9" height="10" rx="2"/><path d="M14 8h6M14 12h7a3 3 0 0 1-3 5h-4z"/></svg></a>
          <a class="social-icon" href="#" aria-label="GitHub"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px"><path d="M9 19c-4 1-4-2-6-2m12 4v-3.4a3.3 3.3 0 0 0-1-2.6c3 0 6-2 6-6a4.7 4.7 0 0 0-1.3-3.2 4.3 4.3 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12.4 12.4 0 0 0-6.4 0C6.4 2.3 5.3 2.6 5.3 2.6a4.3 4.3 0 0 0-.1 3.2A4.7 4.7 0 0 0 3.9 9c0 4 3 6 6 6a3.3 3.3 0 0 0-1 2.5V21"/></svg></a>
        </div>
      </div>

      <div class="glass-card contact-form">
        <h3>Request Your Free Quote</h3>
        <form id="contactForm" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" autocomplete="name" required>
              <span class="error-msg">Please enter your name</span>
            </div>
            <div class="form-group">
              <label for="email">Email *</label>
              <input type="email" id="email" name="email" autocomplete="email" required>
              <span class="error-msg">Please enter a valid email</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" autocomplete="tel">
              <span class="error-msg">Please enter a valid phone number</span>
            </div>
            <div class="form-group">
              <label for="service">Service Interested In *</label>
              <select id="service" name="service" required>
                <option value="">Select a service</option>
                <option>Website Development</option>
                <option>E-commerce Development</option>
                <option>App Development</option>
                <option>SEO &amp; Google Ranking</option>
                <option>Maintenance &amp; Hosting</option>
                <option>Branding &amp; Logo Design</option>
                <option>Social Media Marketing</option>
                <option>Not sure — need guidance</option>
              </select>
              <span class="error-msg">Please select a service</span>
            </div>
          </div>
          <div class="form-group">
            <label for="budget">Estimated Budget (optional)</label>
            <input type="text" id="budget" name="budget" placeholder="e.g. ₹25,000 – ₹50,000">
          </div>
          <div class="form-group">
            <label for="message">Project Details *</label>
            <div class="textarea-wrap">
              <textarea id="message" name="message" rows="4" required placeholder="Tell us about your project, or tap the mic to speak instead of typing…"></textarea>
              <button type="button" class="mic-btn" id="micBtn" aria-label="Dictate project details using your microphone" title="Speak instead of typing">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 19v3"/></svg>
              </button>
            </div>
            <span class="error-msg">Please share a few details about your project</span>
          </div>
          <span class="magnetic" style="display:block;"><button type="submit" class="btn btn--primary btn--block">Send Request</button></span>
          <p class="form-note">Submitting opens your email app with the details pre-filled (no backend — mailto fallback). This will be upgraded to real form submission in a future version.</p>
          <p class="form-success" id="formSuccess">Opening your email client… If nothing happens, please email us directly at hello@wizhy.in.</p>
        </form>
      </div>
    </div>
  </div>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <div class="container footer__grid">
    <div class="footer__col">
      <a href="#home" class="brand"><span class="brand__mark">W</span><span class="brand__text">Wizhy<em> Studio</em></span></a>
      <p class="footer__tagline">Websites that don't just look good — they get found, rank on Google, and grow your business.</p>
      <div class="social-icons">
        <a class="social-icon" href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:16px;height:16px"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
        <a class="social-icon" href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:16px;height:16px"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 10v7M7 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 13v4"/></svg></a>
        <a class="social-icon" href="#" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:16px;height:16px"><path d="M4 4l16 16M20 4L4 20"/></svg></a>
        <a class="social-icon" href="#" aria-label="Behance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:16px;height:16px"><rect x="2" y="7" width="9" height="10" rx="2"/><path d="M14 8h6M14 12h7a3 3 0 0 1-3 5h-4z"/></svg></a>
        <a class="social-icon" href="#" aria-label="GitHub"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:16px;height:16px"><path d="M9 19c-4 1-4-2-6-2m12 4v-3.4a3.3 3.3 0 0 0-1-2.6c3 0 6-2 6-6a4.7 4.7 0 0 0-1.3-3.2 4.3 4.3 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12.4 12.4 0 0 0-6.4 0C6.4 2.3 5.3 2.6 5.3 2.6a4.3 4.3 0 0 0-.1 3.2A4.7 4.7 0 0 0 3.9 9c0 4 3 6 6 6a3.3 3.3 0 0 0-1 2.5V21"/></svg></a>
      </div>

      <p class="footer__cta-text" style="margin-top:22px;margin-bottom:8px;">Get our free website checklist &amp; occasional growth tips:</p>
      <form class="newsletter-form" id="newsletterForm">
        <label for="newsletterEmail" style="position:absolute;left:-9999px;">Email address</label>
        <input type="email" id="newsletterEmail" name="newsletterEmail" placeholder="you@business.com" required autocomplete="email">
        <button type="submit" class="btn btn--primary btn--sm">Subscribe</button>
      </form>
      <p class="newsletter-success" id="newsletterSuccess">✓ You're subscribed — welcome aboard!</p>
    </div>

    <div class="footer__col">
      <h4>Quick Links</h4>
      <a href="#home">Home</a>
      <a href="#services">Services</a>
      <a href="#why-us">Why Us</a>
      <a href="#process">Process</a>
      <a href="#testimonials">Testimonials</a>
      <a href="#faq">FAQ</a>
      <a href="#contact">Contact</a>
    </div>

    <div class="footer__col">
      <h4>Services</h4>
      <a href="#services">Website Development</a>
      <a href="#services">E-commerce Development</a>
      <a href="#services">App Development</a>
      <a href="#services">SEO &amp; Google Ranking</a>
      <a href="#services">Maintenance &amp; Hosting</a>
      <a href="#services">Branding &amp; Logo Design</a>
      <a href="#services">Social Media Marketing</a>
    </div>

    <div class="footer__col footer__col--cta">
      <h4>Start a Project</h4>
      <p class="footer__cta-text">Have an idea? Let's turn it into a website that actually grows your business.</p>
      <a href="#contact" class="btn btn--primary btn--block btn--sm">Get a Custom Quote</a>
    </div>
  </div>

  <div class="footer__bottom">
    <div class="container footer__bottom-inner">
      <span>© <span id="year"></span> Wizhy Studio. All rights reserved.</span>
      <span>wizhy.in</span>
    </div>
  </div>
</footer>

<!-- ============ WHATSAPP FAB ============ -->
<a href="https://wa.me/910000000000" target="_blank" rel="noopener" class="whatsapp-fab" id="whatsappFab" aria-label="Chat with us on WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20Zm4.4-5.9c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8 1-.2.2-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s1 2.6 1.1 2.8c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
</a>

<button class="back-to-top" id="backToTop" aria-label="Back to top">↑</button>
<div class="toast" id="toast"></div>

<!-- ============ COOKIE CONSENT BANNER ============ -->
<div class="cookie-banner" id="cookieBanner" role="dialog" aria-label="Cookie consent">
  <p class="cookie-banner__text"><strong>We use cookies.</strong> We use minimal cookies to run this site and, once you agree, anonymous analytics to understand what's working. No personal data is sold. See how we handle your data.</p>
  <div class="cookie-banner__actions">
    <button class="btn btn--ghost btn--sm" id="cookieReject">Reject</button>
    <button class="btn btn--primary btn--sm" id="cookieAccept">Accept</button>
  </div>
</div>

<!--
  ============ NO-CODE AI CHAT WIDGET (Tidio / Tawk.to) — placeholder ============
  Uncomment ONE of the two blocks below and paste in your own account ID to
  add a free AI/live-chat widget with zero backend and zero API key
  management. This is entirely optional and separate from the Gemini-based
  ideas discussed for this project — pick whichever fits Antigravity's plan.

  Option A — Tidio (has a free "Lyro AI" tier):
  <script src="//code.tidio.co/YOUR_TIDIO_PUBLIC_KEY.js" async></script>

  Option B — Tawk.to (free live chat, no AI on free tier):
  <script>
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function(){
      var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
      s1.async = true;
      s1.src = 'https://embed.tawk.to/YOUR_TAWK_PROPERTY_ID/YOUR_WIDGET_ID';
      s1.charset = 'UTF-8';
      s1.setAttribute('crossorigin', '*');
      s0.parentNode.insertBefore(s1, s0);
    })();
  </script>
-->

<script src="js/script.js"></script>
</body>
</html>

```

---

## File: `css/style.css`

```css
/* =========================================================
   WIZHY STUDIO — style.css (v4.1)
   Base v4.0 design system preserved as-is. New v4.1 additions are
   appended in their own clearly-labeled section near the bottom —
   see "V4.1 ADDITIONS" divider below.
========================================================= */

/* ---------- Design Tokens ---------- */
:root {
  /* Light theme (default) — warm paper palette */
  --bg: #FAF7F1;
  --bg-alt: #F2EDE2;
  --surface: #FFFFFF;
  --surface-2: #F6F1E7;
  --border: rgba(23, 20, 15, 0.10);
  --border-strong: rgba(23, 20, 15, 0.20);
  --text-primary: #17140F;
  --text-secondary: #57534A;
  --text-muted: #918C7C;
  --accent-1: #C8481E; /* terracotta — primary */
  --accent-1-soft: rgba(200, 72, 30, 0.12);
  --accent-2: #23453B; /* deep forest green — secondary */
  --accent-2-soft: rgba(35, 69, 59, 0.10);
  --gold: #B8892B;
  --on-accent: #FFF8EF;
  --shadow-card: 0 20px 50px rgba(23, 20, 15, 0.08);
  --shadow-lift: 0 30px 70px rgba(23, 20, 15, 0.14);
  --radius-sm: 10px;
  --radius-md: 18px;
  --radius-lg: 28px;
  --font-display: 'Playfair Display', Georgia, 'Times New Roman', serif;
  --font-body: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --container: 1200px;

  --cta-bg: #1E3A31;
  --cta-text: #FFF8EF;

  --ease: cubic-bezier(.22, 1, .36, 1);
  color-scheme: light;
}

html[data-theme="dark"] {
  --bg: #100E0B;
  --bg-alt: #17140F;
  --surface: #1A1712;
  --surface-2: #211D16;
  --border: rgba(250, 247, 241, 0.10);
  --border-strong: rgba(250, 247, 241, 0.18);
  --text-primary: #F7F3EA;
  --text-secondary: #C7C0AF;
  --text-muted: #8D8878;
  --accent-1: #FF7A45;
  --accent-1-soft: rgba(255, 122, 69, 0.14);
  --accent-2: #7FC7A6;
  --accent-2-soft: rgba(127, 199, 166, 0.12);
  --gold: #E0B84A;
  --on-accent: #1A1712;
  --shadow-card: 0 20px 50px rgba(0, 0, 0, 0.35);
  --shadow-lift: 0 30px 80px rgba(0, 0, 0, 0.5);
  color-scheme: dark;
}

/* ---------- Reset ---------- */
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
  font-family: var(--font-body);
  background: var(--bg);
  color: var(--text-primary);
  line-height: 1.6;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  transition: background .4s var(--ease), color .4s var(--ease);
}
img, svg { display: block; max-width: 100%; }
a { color: inherit; text-decoration: none; }
ul { list-style: none; }
button { font-family: inherit; cursor: pointer; border: none; background: none; color: inherit; }
input, textarea, select { font-family: inherit; }
h1, h2, h3, h4 { font-family: var(--font-display); letter-spacing: -0.01em; }
.container { max-width: var(--container); margin: 0 auto; padding: 0 24px; }

/* ---------- Utility ---------- */
.accent-text { color: var(--accent-1); font-style: italic; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: .78rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase;
  color: var(--accent-1); margin-bottom: 18px;
}
.eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--accent-1); display: block; }
.section { padding: 130px 0; position: relative; z-index: 1; }
.section--alt { background: var(--bg-alt); }
.section-head { max-width: 680px; margin: 0 0 64px; }
.section-head--center { margin-inline: auto; text-align: center; }
.section-title { font-size: clamp(2rem, 4vw, 3.1rem); font-weight: 600; line-height: 1.12; letter-spacing: -.015em; }
.section-subtitle { color: var(--text-secondary); font-size: 1.08rem; margin-top: 20px; max-width: 540px; }
.section-head--center .section-subtitle { margin-inline: auto; }

/* ---------- Reveal-on-scroll (word + block) ---------- */
.reveal { opacity: 0; transform: translateY(26px); transition: opacity .8s var(--ease), transform .8s var(--ease); }
.reveal.in-view { opacity: 1; transform: translateY(0); }
.reveal { animation: forceReveal 0s linear 2.5s forwards; }
@keyframes forceReveal { to { opacity: 1; transform: translateY(0); } }
html.no-js .reveal { opacity: 1; transform: none; animation: none; }

.word-reveal { display: inline-block; overflow: hidden; vertical-align: top; }
.word-reveal span { display: inline-block; transform: translateY(115%); transition: transform .9s var(--ease); }
.word-reveal.in-view span { transform: translateY(0); }

@media (prefers-reduced-motion: reduce) {
  .reveal, .word-reveal span { transition: none !important; animation: none !important; opacity: 1 !important; transform: none !important; }
  * { scroll-behavior: auto !important; }
}

/* ---------- Buttons ---------- */
.btn {
  position: relative; display: inline-flex; align-items: center; gap: 10px;
  padding: 15px 30px; border-radius: 999px; font-weight: 600; font-size: .95rem;
  transition: transform .35s var(--ease), box-shadow .35s var(--ease), background .3s ease, color .3s ease, border-color .3s ease;
  white-space: nowrap;
}
.btn--primary { background: var(--accent-1); color: var(--on-accent); box-shadow: 0 10px 26px var(--accent-1-soft); }
.btn--primary:hover { transform: translateY(-3px); box-shadow: 0 18px 40px var(--accent-1-soft); }
.btn--ghost { background: transparent; border: 1.5px solid var(--border-strong); color: var(--text-primary); }
.btn--ghost:hover { border-color: var(--accent-1); color: var(--accent-1); transform: translateY(-3px); }
.btn--sm { padding: 11px 22px; font-size: .85rem; }
.btn--lg { padding: 17px 36px; font-size: 1rem; }
.btn--block { width: 100%; justify-content: center; }
.btn__arrow { transition: transform .35s var(--ease); }
.btn:hover .btn__arrow { transform: translateX(4px); }
.magnetic { display: inline-block; will-change: transform; }

/* ---------- Theme toggle ---------- */
.theme-toggle {
  position: relative; width: 46px; height: 46px; border-radius: 50%;
  border: 1px solid var(--border-strong); display: flex; align-items: center; justify-content: center;
  transition: border-color .3s ease, background .3s ease; flex-shrink: 0;
}
.theme-toggle:hover { border-color: var(--accent-1); background: var(--accent-1-soft); }
.theme-toggle svg { width: 19px; height: 19px; }
.theme-toggle .icon-sun { display: none; }
html[data-theme="dark"] .theme-toggle .icon-moon { display: none; }
html[data-theme="dark"] .theme-toggle .icon-sun { display: block; }

/* ---------- Scroll progress ---------- */
.scroll-progress { position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: var(--accent-1); z-index: 1000; transition: width .1s linear; }

/* ---------- Navbar ---------- */
.navbar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 960; padding: 26px 0;
  transition: padding .35s var(--ease), background .35s ease, border-color .35s ease, backdrop-filter .35s ease;
  border-bottom: 1px solid transparent;
}
.navbar.scrolled {
  background: color-mix(in srgb, var(--bg) 86%, transparent);
  backdrop-filter: blur(14px); padding: 14px 0; border-bottom-color: var(--border);
}
.navbar__inner { display: flex; align-items: center; justify-content: space-between; gap: 24px; }
.brand { display: flex; align-items: center; gap: 11px; }
.brand__mark {
  width: 38px; height: 38px; border-radius: 11px; background: var(--accent-1);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  font-family: var(--font-display); font-weight: 700; font-size: 1.15rem; color: var(--on-accent);
}
.brand__text { font-family: var(--font-display); font-size: 1.32rem; font-weight: 600; letter-spacing: -.01em; }
.brand__text em { font-style: italic; color: var(--accent-1); }
.nav-links { display: flex; align-items: center; gap: 34px; }
.nav-link { font-size: .93rem; font-weight: 500; color: var(--text-secondary); position: relative; padding: 4px 0; transition: color .25s ease; }
.nav-link::after { content: ""; position: absolute; left: 0; bottom: -2px; width: 0; height: 1.5px; background: var(--accent-1); transition: width .3s var(--ease); }
.nav-link:hover, .nav-link.active { color: var(--text-primary); }
.nav-link:hover::after, .nav-link.active::after { width: 100%; }
.navbar__actions { display: flex; align-items: center; gap: 14px; }
.hamburger { display: none; flex-direction: column; gap: 5px; width: 26px; }
.hamburger span { display: block; height: 1.5px; width: 100%; background: var(--text-primary); border-radius: 2px; transition: transform .3s ease, opacity .3s ease; }
.hamburger.open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
.hamburger.open span:nth-child(2) { opacity: 0; }
.hamburger.open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }
.mobile-menu {
  position: fixed; top: 0; right: -100%; width: min(340px, 88vw); height: 100vh;
  background: var(--surface); z-index: 950; padding: 110px 34px 40px;
  display: flex; flex-direction: column; gap: 22px; transition: right .4s var(--ease);
  box-shadow: -20px 0 60px rgba(0,0,0,.15); border-left: 1px solid var(--border); overflow-y: auto;
}
.mobile-menu.open { right: 0; }
.mobile-link { font-family: var(--font-display); font-size: 1.3rem; font-weight: 600; color: var(--text-primary); }

/* ---------- Hero ---------- */
.hero { position: relative; padding: 168px 0 100px; overflow: hidden; }
.hero__blob { position: absolute; border-radius: 50%; filter: blur(100px); z-index: 0; animation: blobDrift 18s ease-in-out infinite; }
.hero__blob--1 { width: 480px; height: 480px; background: var(--accent-1); opacity: .16; top: -140px; left: -120px; }
.hero__blob--2 { width: 420px; height: 420px; background: var(--accent-2); opacity: .14; bottom: -160px; right: -100px; animation-delay: -6s; }
@keyframes blobDrift { 0%, 100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px, -30px) scale(1.1); } }
.hero__inner { position: relative; z-index: 1; display: grid; grid-template-columns: 1.15fr .85fr; gap: 50px; align-items: center; }
.hero__title { font-size: clamp(2.4rem, 5vw, 4.1rem); font-weight: 600; line-height: 1.08; letter-spacing: -.02em; margin-bottom: 26px; }
.hero__title .line { display: block; }
.hero__subtitle { font-size: 1.12rem; color: var(--text-secondary); max-width: 480px; margin-bottom: 34px; }
.hero__cta { display: flex; gap: 18px; flex-wrap: wrap; margin-bottom: 46px; }
.hero__proof { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; }
.hero__avatars { display: flex; }
.hero__avatars span {
  width: 38px; height: 38px; border-radius: 50%; border: 2.5px solid var(--bg); background: var(--accent-2);
  color: var(--on-accent); font-size: .78rem; font-weight: 700; display: flex; align-items: center; justify-content: center;
  margin-left: -12px; font-family: var(--font-body);
}
.hero__avatars span:first-child { margin-left: 0; }
.hero__proof-text { font-size: .88rem; color: var(--text-secondary); }
.hero__proof-text strong { color: var(--text-primary); }
.hero__visual { position: relative; display: flex; align-items: center; justify-content: center; min-height: 420px; }
.orbit { position: relative; width: 340px; height: 340px; }
.orbit__ring { position: absolute; inset: 0; animation: spin 26s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.orbit__core {
  position: absolute; inset: 58px; border-radius: 50%; background: var(--surface); border: 1px solid var(--border-strong);
  box-shadow: var(--shadow-lift); display: flex; flex-direction: column; align-items: center; justify-content: center;
  text-align: center; padding: 20px;
}
.orbit__core .num { font-family: var(--font-display); font-size: 2.7rem; font-weight: 700; color: var(--accent-1); line-height: 1; }
.orbit__core .label { font-size: .78rem; color: var(--text-secondary); margin-top: 8px; letter-spacing: .04em; }
.hero__card {
  position: absolute; background: var(--surface); border: 1px solid var(--border-strong); border-radius: var(--radius-md);
  padding: 16px 20px; box-shadow: var(--shadow-card); display: flex; align-items: center; gap: 12px; animation: floatCard 6s ease-in-out infinite;
}
.hero__card--1 { top: 6%; right: -6%; animation-delay: -1s; }
.hero__card--2 { bottom: 8%; left: -10%; animation-delay: -3s; }
@keyframes floatCard { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
.hero__card .icon-dot { width: 34px; height: 34px; border-radius: 9px; background: var(--accent-2-soft); color: var(--accent-2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.hero__card strong { display: block; font-size: .88rem; }
.hero__card span { font-size: .76rem; color: var(--text-muted); }

/* ---------- Marquee ---------- */
.marquee-section { padding: 36px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--bg); position: relative; z-index: 1; }
.marquee-label { text-align: center; font-size: .76rem; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 22px; }
.marquee { overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent); mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent); }
.marquee__track { display: flex; gap: 16px; width: max-content; animation: scrollX 30s linear infinite; }
@keyframes scrollX { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.tech-chip { padding: 10px 22px; border-radius: 999px; border: 1px solid var(--border); font-size: .84rem; font-weight: 500; color: var(--text-secondary); white-space: nowrap; transition: border-color .25s ease, color .25s ease; }
.tech-chip:hover { border-color: var(--accent-1); color: var(--accent-1); }

/* ---------- Services: bento grid ---------- */
.bento { display: grid; grid-template-columns: repeat(4, 1fr); grid-auto-rows: minmax(210px, auto); gap: 20px; }
.bento-card {
  position: relative; grid-column: span 2; background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius-md); padding: 34px; overflow: hidden;
  transition: transform .5s var(--ease), box-shadow .5s var(--ease), border-color .4s ease; transform-style: preserve-3d; will-change: transform;
}
.bento-card--wide { grid-column: span 4; display: flex; align-items: center; justify-content: space-between; gap: 40px; }
.bento-card--tall { grid-row: span 1; }
.bento-card:hover { box-shadow: var(--shadow-lift); border-color: var(--border-strong); }
.bento-card__icon {
  width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
  background: var(--accent-1-soft); color: var(--accent-1); margin-bottom: 24px; transition: transform .4s var(--ease), background .4s ease;
}
.bento-card:hover .bento-card__icon { transform: scale(1.08) rotate(-4deg); }
.bento-card h3 { font-size: 1.3rem; font-weight: 600; margin-bottom: 10px; letter-spacing: -.01em; }
.bento-card p { color: var(--text-secondary); font-size: .95rem; max-width: 340px; }
.bento-card__num { position: absolute; top: 28px; right: 32px; font-family: var(--font-display); font-size: 3.2rem; color: var(--border-strong); font-weight: 600; line-height: 1; }

/* ---------- Why Us: stat rings ---------- */
.why-us { display: grid; grid-template-columns: 1fr 1fr; gap: 70px; align-items: center; }
.check-list li { display: flex; gap: 16px; margin-bottom: 26px; }
.check-icon { flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--accent-2-soft); color: var(--accent-2); font-weight: 700; font-size: .82rem; margin-top: 2px; }
.check-list strong { font-size: 1.06rem; font-family: var(--font-display); font-weight: 600; }
.check-list p { color: var(--text-secondary); font-size: .92rem; margin-top: 4px; }
.stat-rings { display: grid; grid-template-columns: 1fr 1fr; gap: 26px; }
.stat-ring { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 28px; text-align: center; }
.stat-ring svg { width: 108px; height: 108px; margin: 0 auto 16px; }
.stat-ring circle { fill: none; stroke-width: 7; stroke-linecap: round; }
.stat-ring .track { stroke: var(--border); }
.stat-ring .fill { stroke: var(--accent-1); stroke-dasharray: 301; stroke-dashoffset: 301; transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 1.6s var(--ease); }
.stat-ring .pct { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; fill: var(--text-primary); }
.stat-ring__label { font-size: .86rem; color: var(--text-secondary); }
.stat-rings__note { grid-column: span 2; font-size: .76rem; color: var(--text-muted); font-style: italic; text-align: center; margin-top: 4px; }

/* ---------- Process: animated stepper ---------- */
.process { position: relative; }
.process__line-wrap { position: relative; height: 3px; background: var(--border); border-radius: 3px; margin-bottom: 60px; overflow: hidden; }
.process__line-fill { position: absolute; inset: 0; width: 0%; background: var(--accent-1); border-radius: 3px; transition: width 1.4s var(--ease); }
.process-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
.process-step { position: relative; padding-top: 8px; }
.process-step__num {
  width: 42px; height: 42px; border-radius: 50%; border: 1.5px solid var(--border-strong); display: flex; align-items: center; justify-content: center;
  font-family: var(--font-display); font-weight: 700; margin-bottom: 20px; transition: background .4s ease, color .4s ease, border-color .4s ease;
}
.process-step.in-view .process-step__num { background: var(--accent-1); color: var(--on-accent); border-color: var(--accent-1); }
.process-step h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; }
.process-step p { color: var(--text-secondary); font-size: .92rem; }

/* ---------- Testimonials ---------- */
.testimonial-slider { position: relative; overflow: hidden; }
.testimonial-track { display: flex; transition: transform .6s var(--ease); }
.testimonial-slide { flex: 0 0 100%; width: 100%; display: flex; justify-content: center; }
.testimonial-card { width: 100%; max-width: 680px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 48px; text-align: center; position: relative; }
.testimonial-card .quote-mark { font-family: var(--font-display); font-size: 4rem; color: var(--accent-1); line-height: 1; margin-bottom: 6px; opacity: .6; }
.stars { color: var(--gold); font-size: 1.05rem; margin-bottom: 18px; letter-spacing: 2px; }
.testimonial-card p.quote { font-size: 1.18rem; color: var(--text-primary); margin-bottom: 26px; font-family: var(--font-display); font-style: italic; line-height: 1.5; }
.testimonial-author { display: flex; align-items: center; justify-content: center; gap: 12px; }
.avatar { width: 46px; height: 46px; border-radius: 50%; background: var(--accent-2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; color: var(--on-accent); }
.testimonial-author div { text-align: left; }
.testimonial-author strong { display: block; font-size: .95rem; }
.testimonial-author span { font-size: .8rem; color: var(--text-muted); }
.testimonial-dots { display: flex; justify-content: center; gap: 10px; margin-top: 34px; }
.testimonial-dots .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--border-strong); cursor: pointer; transition: background .25s ease, transform .25s ease; }
.testimonial-dots .dot.active { background: var(--accent-1); transform: scale(1.35); }

/* ---------- CTA band ---------- */
.cta-band { background: var(--cta-bg); position: relative; overflow: hidden; padding: 100px 0; }
.cta-band__marquee {
  position: absolute; top: 50%; left: 0; width: 200%; transform: translateY(-50%);
  font-family: var(--font-display); font-size: 7rem; font-weight: 700; color: rgba(255,255,255,.05);
  white-space: nowrap; animation: scrollX 40s linear infinite; pointer-events: none;
}
.cta-band__inner { position: relative; z-index: 1; text-align: center; }
.cta-band h2 { font-size: clamp(1.8rem, 4vw, 2.9rem); font-weight: 600; margin-bottom: 18px; color: var(--cta-text); }
.cta-band p { color: rgba(255,248,239,.82); margin-bottom: 38px; font-size: 1.08rem; }
.cta-band .btn--primary { background: var(--cta-text); color: var(--cta-bg); box-shadow: 0 14px 34px rgba(0,0,0,.2); }
.cta-band .btn--primary:hover { background: #fff; }

/* ---------- Contact ---------- */
.contact-grid { display: grid; grid-template-columns: .78fr 1.22fr; gap: 32px; align-items: start; }
.glass-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 34px; }
.contact-info h3, .contact-form h3 { font-size: 1.25rem; margin-bottom: 26px; }
.contact-row { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; padding: 10px; border-radius: 10px; transition: background .2s ease; }
a.contact-row:hover { background: var(--surface-2); cursor: pointer; }
.contact-icon { width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--accent-1-soft); color: var(--accent-1); }
.contact-row strong { display: block; font-size: .89rem; }
.contact-row span { font-size: .89rem; color: var(--text-secondary); }
.social-label { font-size: .8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; margin: 22px 0 12px; }
.social-icons { display: flex; gap: 10px; }
.social-icon { width: 40px; height: 40px; border-radius: 11px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); transition: all .3s var(--ease); }
.social-icon:hover { background: var(--accent-1); color: var(--on-accent); border-color: transparent; transform: translateY(-3px); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.form-group { margin-bottom: 20px; position: relative; }
.form-group label { display: block; font-size: .84rem; font-weight: 600; margin-bottom: 9px; color: var(--text-secondary); }
.form-group input, .form-group select, .form-group textarea {
  width: 100%; padding: 14px 16px; border-radius: 11px; border: 1.5px solid var(--border-strong);
  background: var(--bg); color: var(--text-primary); font-size: .93rem; font-family: var(--font-body);
  transition: border-color .2s ease, box-shadow .2s ease;
}
.form-group select option { background: var(--surface); color: var(--text-primary); }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--accent-1); box-shadow: 0 0 0 4px var(--accent-1-soft); }
.form-group textarea { resize: vertical; min-height: 104px; }
.error-msg { display: none; font-size: .78rem; color: #C0392B; margin-top: 6px; }
.form-group.invalid input, .form-group.invalid select, .form-group.invalid textarea { border-color: #C0392B; }
.form-group.invalid .error-msg { display: block; }
.form-note { font-size: .78rem; color: var(--text-muted); margin-top: 10px; text-align: center; }
.form-success { display: none; text-align: center; font-size: .88rem; color: var(--accent-2); margin-top: 16px; padding: 13px; border-radius: 11px; background: var(--accent-2-soft); border: 1px solid var(--accent-2); }
.form-success.show { display: block; }

/* ---------- Footer ---------- */
.footer { background: var(--bg-alt); border-top: 1px solid var(--border); padding: 90px 0 0; position: relative; z-index: 1; }
.footer__grid { display: grid; grid-template-columns: 1.3fr .9fr .9fr 1fr; gap: 44px; padding-bottom: 64px; }
.footer__tagline { color: var(--text-secondary); font-size: .92rem; margin: 20px 0 24px; max-width: 280px; }
.footer__col h4 { font-family: var(--font-display); font-size: 1rem; margin-bottom: 22px; font-weight: 600; }
.footer__col a { display: block; color: var(--text-secondary); font-size: .89rem; margin-bottom: 15px; transition: color .2s ease; }
.footer__col a:hover { color: var(--accent-1); }
.footer__col--cta { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 24px; }
.footer__cta-text { color: var(--text-secondary); font-size: .86rem; margin-bottom: 18px; }
.footer__bottom { border-top: 1px solid var(--border); padding: 24px 0; }
.footer__bottom-inner { display: flex; justify-content: space-between; font-size: .82rem; color: var(--text-muted); flex-wrap: wrap; gap: 10px; }

/* ---------- Back to top & toast ---------- */
.back-to-top {
  position: fixed; bottom: 30px; right: 30px; width: 48px; height: 48px; border-radius: 50%;
  background: var(--accent-1); color: var(--on-accent); font-size: 1.2rem; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 12px 30px var(--accent-1-soft); opacity: 0; pointer-events: none; transform: translateY(10px);
  transition: all .35s var(--ease); z-index: 500;
}
.back-to-top.show { opacity: 1; pointer-events: auto; transform: translateY(0); }
.back-to-top:hover { transform: translateY(-4px); }
.toast {
  position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--surface);
  border: 1px solid var(--border-strong); padding: 13px 24px; border-radius: 11px; font-size: .88rem; opacity: 0;
  pointer-events: none; transition: all .3s ease; z-index: 999; box-shadow: var(--shadow-card);
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width: 1080px) {
  .bento { grid-template-columns: repeat(2, 1fr); }
  .bento-card { grid-column: span 1; }
  .bento-card--wide { grid-column: span 2; }
}
@media (max-width: 980px) {
  .hero__inner { grid-template-columns: 1fr; }
  .hero__visual { order: -1; margin-bottom: 20px; }
  .why-us { grid-template-columns: 1fr; }
  .contact-grid { grid-template-columns: 1fr; }
  .footer__grid { grid-template-columns: 1fr 1fr; }
  .footer__col--cta { grid-column: span 2; }
  .process-grid { grid-template-columns: repeat(1, 1fr); gap: 32px; }
  .process__line-wrap { display: none; }
}
@media (max-width: 768px) {
  .nav-links { display: none; }
  .hamburger { display: flex; }
  .hero { padding: 128px 0 70px; }
  .hero__cta { flex-direction: column; align-items: stretch; }
  .hero__cta .btn { justify-content: center; }
  .bento { grid-template-columns: 1fr; }
  .bento-card--wide { grid-column: span 1; flex-direction: column; text-align: center; }
  .form-row { grid-template-columns: 1fr; }
  .footer__grid { grid-template-columns: 1fr; gap: 36px; }
  .footer__col--cta { grid-column: span 1; }
  .footer__bottom-inner { flex-direction: column; gap: 8px; text-align: center; }
  .section { padding: 90px 0; }
  .stat-rings { grid-template-columns: 1fr; }
  .stat-rings__note { grid-column: span 1; }
  .hero__visual { flex-direction: column; gap: 18px; }
  .hero__card { position: static; margin-top: 0; animation: none; width: 100%; max-width: 300px; }
  .orbit { width: 280px; height: 280px; }
  .orbit__core { inset: 46px; }
}
@media (max-width: 560px) {
  .navbar__actions .btn--sm { display: none; }
  .cta-band__marquee { font-size: 4rem; }
}

/* =========================================================
   V4.1 ADDITIONS
   Everything below is new in v4.1. See wizhy-studio-project-
   summary_v4.1.md → Section "What's New in v4.1" for the full list.
========================================================= */

/* ---------- Skip-to-content (accessibility) ---------- */
.skip-link {
  position: fixed; top: -60px; left: 16px; z-index: 1200;
  background: var(--accent-1); color: var(--on-accent);
  padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: .9rem;
  transition: top .25s var(--ease);
}
.skip-link:focus { top: 16px; }

/* ---------- Cookie consent banner ---------- */
.cookie-banner {
  position: fixed; left: 20px; right: 20px; bottom: 20px; z-index: 1100;
  max-width: 640px; margin: 0 auto; background: var(--surface); border: 1px solid var(--border-strong);
  border-radius: var(--radius-md); box-shadow: var(--shadow-lift); padding: 22px 24px;
  display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
  transform: translateY(140%); opacity: 0; transition: transform .5s var(--ease), opacity .5s var(--ease);
  pointer-events: none;
}
.cookie-banner.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
.cookie-banner__text { flex: 1 1 260px; font-size: .87rem; color: var(--text-secondary); }
.cookie-banner__text strong { color: var(--text-primary); }
.cookie-banner__actions { display: flex; gap: 10px; flex-wrap: wrap; }
@media (max-width: 560px) {
  .cookie-banner { left: 12px; right: 12px; bottom: 12px; padding: 18px; }
}

/* ---------- WhatsApp floating action button ---------- */
.whatsapp-fab {
  position: fixed; bottom: 30px; left: 30px; width: 56px; height: 56px; border-radius: 50%;
  background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 14px 32px rgba(37, 211, 102, .35); z-index: 500;
  transition: transform .3s var(--ease), box-shadow .3s var(--ease);
}
.whatsapp-fab:hover { transform: translateY(-4px) scale(1.05); box-shadow: 0 18px 40px rgba(37, 211, 102, .45); }
.whatsapp-fab svg { width: 28px; height: 28px; }
@media (max-width: 560px) {
  .whatsapp-fab { width: 50px; height: 50px; bottom: 20px; left: 20px; }
  .whatsapp-fab svg { width: 24px; height: 24px; }
}

/* ---------- SEO Audit tool ---------- */
.audit-tool {
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
  padding: 44px; box-shadow: var(--shadow-card);
}
.audit-tool__form { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 8px; }
.audit-tool__form input[type="url"] {
  flex: 1 1 280px; padding: 15px 18px; border-radius: 12px; border: 1.5px solid var(--border-strong);
  background: var(--bg); color: var(--text-primary); font-size: .96rem; font-family: var(--font-body);
  transition: border-color .2s ease, box-shadow .2s ease;
}
.audit-tool__form input[type="url"]:focus { outline: none; border-color: var(--accent-1); box-shadow: 0 0 0 4px var(--accent-1-soft); }
.audit-tool__note { font-size: .78rem; color: var(--text-muted); margin-top: 8px; }
.audit-tool__error { display: none; font-size: .82rem; color: #C0392B; margin-top: 8px; }
.audit-tool__error.show { display: block; }
.audit-tool__loading { display: none; align-items: center; gap: 12px; margin-top: 26px; color: var(--text-secondary); font-size: .92rem; }
.audit-tool__loading.show { display: flex; }
.audit-spinner {
  width: 20px; height: 20px; border-radius: 50%; border: 2.5px solid var(--border-strong);
  border-top-color: var(--accent-1); animation: auditSpin .8s linear infinite; flex-shrink: 0;
}
@keyframes auditSpin { to { transform: rotate(360deg); } }
.audit-tool__result { display: none; margin-top: 34px; }
.audit-tool__result.show { display: block; animation: forceReveal .5s var(--ease); }
.audit-result__head { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; margin-bottom: 28px; }
.audit-score-ring { position: relative; width: 120px; height: 120px; flex-shrink: 0; }
.audit-score-ring svg { width: 120px; height: 120px; }
.audit-score-ring circle { fill: none; stroke-width: 8; stroke-linecap: round; }
.audit-score-ring .track { stroke: var(--border); }
.audit-score-ring .fill { stroke: var(--accent-1); stroke-dasharray: 327; stroke-dashoffset: 327; transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 1.4s var(--ease); }
.audit-score-ring__num { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.audit-score-ring__num strong { font-family: var(--font-display); font-size: 1.9rem; font-weight: 700; line-height: 1; }
.audit-score-ring__num span { font-size: .68rem; color: var(--text-muted); letter-spacing: .05em; text-transform: uppercase; }
.audit-result__summary h3 { font-size: 1.2rem; margin-bottom: 6px; }
.audit-result__summary p { color: var(--text-secondary); font-size: .92rem; max-width: 460px; }
.audit-result__url { font-size: .82rem; color: var(--text-muted); word-break: break-all; margin-top: 6px; }
.audit-suggestions { display: grid; gap: 14px; }
.audit-suggestion {
  display: flex; gap: 14px; align-items: flex-start; background: var(--bg-alt); border: 1px solid var(--border);
  border-radius: var(--radius-sm); padding: 16px 18px;
}
.audit-suggestion__icon { flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; margin-top: 1px; }
.audit-suggestion--warn .audit-suggestion__icon { background: rgba(200,72,30,.14); color: var(--accent-1); }
.audit-suggestion--ok .audit-suggestion__icon { background: var(--accent-2-soft); color: var(--accent-2); }
.audit-suggestion strong { display: block; font-size: .93rem; margin-bottom: 3px; }
.audit-suggestion p { font-size: .86rem; color: var(--text-secondary); margin: 0; }
.audit-result__cta { margin-top: 28px; text-align: center; padding-top: 24px; border-top: 1px dashed var(--border-strong); }
.audit-result__cta p { color: var(--text-secondary); font-size: .9rem; margin-bottom: 16px; }
.audit-disclaimer { font-size: .74rem; color: var(--text-muted); font-style: italic; margin-top: 20px; text-align: center; }
@media (max-width: 640px) {
  .audit-tool { padding: 28px 22px; }
  .audit-result__head { flex-direction: column; text-align: center; }
}

/* ---------- FAQ accordion ---------- */
.faq-list { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.faq-item { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }
.faq-item__question {
  width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px;
  padding: 22px 26px; text-align: left; font-family: var(--font-display); font-size: 1.05rem; font-weight: 600;
  color: var(--text-primary); cursor: pointer;
}
.faq-item__icon { flex-shrink: 0; width: 28px; height: 28px; border-radius: 50%; border: 1.5px solid var(--border-strong); display: flex; align-items: center; justify-content: center; position: relative; transition: transform .35s var(--ease), border-color .3s ease, background .3s ease; }
.faq-item__icon::before, .faq-item__icon::after { content: ""; position: absolute; background: var(--text-primary); transition: transform .3s ease, opacity .3s ease; }
.faq-item__icon::before { width: 11px; height: 1.5px; }
.faq-item__icon::after { width: 1.5px; height: 11px; }
.faq-item.open .faq-item__icon { background: var(--accent-1); border-color: var(--accent-1); transform: rotate(180deg); }
.faq-item.open .faq-item__icon::before, .faq-item.open .faq-item__icon::after { background: var(--on-accent); }
.faq-item.open .faq-item__icon::after { opacity: 0; }
.faq-item__answer { max-height: 0; overflow: hidden; transition: max-height .4s var(--ease); }
.faq-item__answer-inner { padding: 0 26px 24px; color: var(--text-secondary); font-size: .95rem; line-height: 1.65; }

/* ---------- Lead magnet (free checklist download) ---------- */
.lead-magnet {
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
  padding: 46px; display: grid; grid-template-columns: 1fr auto; gap: 36px; align-items: center;
  box-shadow: var(--shadow-card);
}
.lead-magnet__icon {
  width: 64px; height: 64px; border-radius: 16px; background: var(--accent-2-soft); color: var(--accent-2);
  display: flex; align-items: center; justify-content: center; margin-bottom: 18px;
}
.lead-magnet h3 { font-size: 1.5rem; margin-bottom: 10px; }
.lead-magnet p { color: var(--text-secondary); font-size: .96rem; max-width: 480px; }
.lead-magnet__form { display: flex; gap: 12px; flex-wrap: wrap; min-width: 320px; }
.lead-magnet__form input[type="email"] {
  flex: 1 1 200px; padding: 14px 16px; border-radius: 11px; border: 1.5px solid var(--border-strong);
  background: var(--bg); color: var(--text-primary); font-size: .92rem; font-family: var(--font-body);
}
.lead-magnet__form input[type="email"]:focus { outline: none; border-color: var(--accent-1); box-shadow: 0 0 0 4px var(--accent-1-soft); }
.lead-magnet__note { font-size: .76rem; color: var(--text-muted); margin-top: 10px; }
.lead-magnet__success { display: none; font-size: .86rem; color: var(--accent-2); margin-top: 12px; }
.lead-magnet__success.show { display: block; }
@media (max-width: 780px) {
  .lead-magnet { grid-template-columns: 1fr; text-align: center; }
  .lead-magnet__icon { margin-inline: auto; }
  .lead-magnet__form { min-width: 0; justify-content: center; }
}

/* ---------- Voice input (mic) button on contact form ---------- */
.textarea-wrap { position: relative; }
.mic-btn {
  position: absolute; top: 12px; right: 12px; width: 36px; height: 36px; border-radius: 50%;
  background: var(--accent-1-soft); color: var(--accent-1); display: flex; align-items: center; justify-content: center;
  transition: background .25s ease, color .25s ease, transform .25s ease;
}
.mic-btn:hover { background: var(--accent-1); color: var(--on-accent); }
.mic-btn svg { width: 17px; height: 17px; }
.mic-btn.listening { background: #C0392B; color: #fff; animation: micPulse 1.2s ease-in-out infinite; }
@keyframes micPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(192,57,43,.4); } 50% { box-shadow: 0 0 0 10px rgba(192,57,43,0); } }
.mic-unsupported-note { display: none; }

/* ---------- Newsletter signup (footer) ---------- */
.newsletter-form { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
.newsletter-form input[type="email"] {
  flex: 1 1 160px; padding: 12px 14px; border-radius: 10px; border: 1.5px solid var(--border-strong);
  background: var(--bg); color: var(--text-primary); font-size: .86rem; font-family: var(--font-body);
}
.newsletter-form input[type="email"]:focus { outline: none; border-color: var(--accent-1); box-shadow: 0 0 0 3px var(--accent-1-soft); }
.newsletter-success { display: none; font-size: .8rem; color: var(--accent-2); margin-top: 10px; }
.newsletter-success.show { display: block; }


```

---

## File: `css/fonts.css`

```css
/* =========================================================
   WIZHY STUDIO — fonts.css (v4.1)
   Self-hosted, locally-bundled variable fonts. Fully offline —
   no Google Fonts CDN, no internet dependency, no external calls.

   "Playfair Display" → serif display font for headlines (variable, weight 400–900)
   "Instrument Sans"   → clean grotesk for body/UI (variable, weight 400–700)

   Both are Google Fonts under the SIL Open Font License — free to
   embed/redistribute in commercial projects, no attribution required
   in the shipped product.
========================================================= */

@font-face {
  font-family: 'Playfair Display';
  font-style: normal;
  font-weight: 400 900;
  font-display: swap;
  src: url('../assets/fonts/playfair-latin.woff2') format('woff2-variations'),
       url('../assets/fonts/playfair-latin.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
  font-family: 'Playfair Display';
  font-style: normal;
  font-weight: 400 900;
  font-display: swap;
  src: url('../assets/fonts/playfair-latin-ext.woff2') format('woff2-variations'),
       url('../assets/fonts/playfair-latin-ext.woff2') format('woff2');
  unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
@font-face {
  font-family: 'Instrument Sans';
  font-style: normal;
  font-weight: 400 700;
  font-display: swap;
  src: url('../assets/fonts/instrument-latin.woff2') format('woff2-variations'),
       url('../assets/fonts/instrument-latin.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
  font-family: 'Instrument Sans';
  font-style: normal;
  font-weight: 400 700;
  font-display: swap;
  src: url('../assets/fonts/instrument-latin-ext.woff2') format('woff2-variations'),
       url('../assets/fonts/instrument-latin-ext.woff2') format('woff2');
  unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
}

```

---

## File: `js/script.js`

```javascript
/* =========================================================
   WIZHY STUDIO — script.js (v4.1)
   Vanilla JS. No dependencies.

   ARCHITECTURE (unchanged from v4.0 — still the single most
   important rule in this file): every independent feature is
   wrapped in its own safeRun(label, fn) try/catch "module". A bug
   in any ONE feature can never take down the rest of the page.

   v4.1 adds new modules at the bottom of this file — see the
   "V4.1 ADDITIONS" divider. All existing v4.0 modules are
   untouched.
========================================================= */

function safeRun(label, fn) {
  try {
    fn();
  } catch (err) {
    console.warn(`[Wizhy Studio] "${label}" module failed to initialize:`, err);
  }
}

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- 1. Theme toggle (light/dark) ---------- */
  safeRun('theme-toggle', () => {
    const btn = document.getElementById('themeToggle');
    if (!btn) return;
    const root = document.documentElement;
    btn.addEventListener('click', () => {
      const current = root.getAttribute('data-theme') || 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      try { localStorage.setItem('wizhy-theme', next); } catch (e) { /* localStorage unavailable */ }
    });
  });

  /* ---------- 2. Reveal on scroll (blocks + word-by-word) ---------- */
  safeRun('reveal-on-scroll', () => {
    const revealEls = document.querySelectorAll('.reveal');
    const wordEls = document.querySelectorAll('.word-reveal');

    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
      revealEls.forEach(el => io.observe(el));

      wordEls.forEach((el, i) => {
        el.style.transitionDelay = `${0.35 + i * 0.055}s`;
      });
      const wordIO = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            wordIO.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });
      wordEls.forEach(el => wordIO.observe(el));
    } else {
      revealEls.forEach(el => el.classList.add('in-view'));
      wordEls.forEach(el => el.classList.add('in-view'));
    }
  });

  /* ---------- 3. Footer year ---------- */
  safeRun('footer-year', () => {
    const yearEl = document.getElementById('year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();
  });

  /* ---------- 4. Navbar scroll state + scrollspy + progress bar ---------- */
  safeRun('navbar-scroll-and-scrollspy', () => {
    const navbar = document.getElementById('navbar');
    const scrollProgress = document.getElementById('scrollProgress');
    const backToTop = document.getElementById('backToTop');
    if (!navbar || !scrollProgress || !backToTop) return;

    const sections = ['home', 'services', 'why-us', 'process', 'testimonials', 'faq', 'contact']
      .map(id => document.getElementById(id)).filter(Boolean);
    const navLinks = document.querySelectorAll('.nav-link');

    function updateScrollSpy() {
      let currentId = sections[0] ? sections[0].id : '';
      const scrollPos = window.scrollY + 140;
      sections.forEach(section => {
        if (scrollPos >= section.offsetTop) currentId = section.id;
      });
      navLinks.forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === '#' + currentId);
      });
    }

    function onScroll() {
      const scrollY = window.scrollY;
      navbar.classList.toggle('scrolled', scrollY > 40);
      backToTop.classList.toggle('show', scrollY > 500);
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = docHeight > 0 ? (scrollY / docHeight) * 100 : 0;
      scrollProgress.style.width = progress + '%';
      updateScrollSpy();
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  });

  /* ---------- 5. Mobile menu ---------- */
  safeRun('mobile-menu', () => {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    if (!hamburger || !mobileMenu) return;

    function closeMobileMenu() {
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
    }

    hamburger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', String(isOpen));
    });

    document.querySelectorAll('.mobile-link, .mobile-menu .btn').forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });
  });

  /* ---------- 6. Smooth anchor scroll (with navbar offset) ---------- */
  safeRun('smooth-anchor-scroll', () => {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');
        if (targetId.length <= 1) return;
        const target = document.querySelector(targetId);
        if (!target) return;
        e.preventDefault();
        const offset = 88;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      });
    });
  });

  /* ---------- 7. Magnetic buttons ---------- */
  safeRun('magnetic-buttons', () => {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isCoarse = window.matchMedia('(pointer: coarse)').matches;
    if (prefersReduced || isCoarse) return;

    document.querySelectorAll('.magnetic').forEach(wrap => {
      const el = wrap.querySelector('.btn');
      if (!el) return;
      const strength = 18;
      wrap.addEventListener('mousemove', (e) => {
        const rect = wrap.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        el.style.transform = `translate(${(x / rect.width) * strength}px, ${(y / rect.height) * strength}px)`;
      });
      wrap.addEventListener('mouseleave', () => {
        el.style.transform = 'translate(0,0)';
      });
    });
  });

  /* ---------- 8. Bento card tilt-on-hover ---------- */
  safeRun('bento-tilt', () => {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isCoarse = window.matchMedia('(pointer: coarse)').matches;
    if (prefersReduced || isCoarse) return;

    document.querySelectorAll('.bento-card').forEach(card => {
      const maxTilt = 5;
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const rx = (0.5 - py) * maxTilt * 2;
        const ry = (px - 0.5) * maxTilt * 2;
        card.style.transform = `perspective(800px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-4px)`;
      });
      card.addEventListener('mouseleave', () => {
        card.style.transform = '';
      });
    });
  });

  /* ---------- 9. Animated stat counters (hero orbit) ---------- */
  safeRun('stat-counters', () => {
    const counters = document.querySelectorAll('.orbit__core .num');
    if (!counters.length) return;
    let started = false;

    function animate() {
      if (started) return;
      started = true;
      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'), 10) || 0;
        const duration = 1500;
        const startTime = performance.now();
        function tick(now) {
          const progress = Math.min((now - startTime) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          counter.textContent = Math.round(eased * target);
          if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
      });
    }

    const orbit = document.querySelector('.orbit');
    if (orbit && 'IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) { animate(); io.disconnect(); }
        });
      }, { threshold: 0.4 });
      io.observe(orbit);
    } else {
      animate();
    }
  });

  /* ---------- 10. Animated stat rings (Why Us section) ---------- */
  safeRun('stat-rings', () => {
    const rings = document.querySelectorAll('.stat-ring');
    if (!rings.length) return;

    function activate(ring) {
      const pct = parseInt(ring.getAttribute('data-pct'), 10) || 0;
      const fill = ring.querySelector('.fill');
      if (!fill) return;
      const circumference = 301.59;
      const offset = circumference - (pct / 100) * circumference;
      requestAnimationFrame(() => {
        fill.style.strokeDashoffset = String(offset);
      });
    }

    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            activate(entry.target);
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.4 });
      rings.forEach(r => io.observe(r));
    } else {
      rings.forEach(activate);
    }
  });

  /* ---------- 11. Process section scroll-linked progress line ---------- */
  safeRun('process-scroll-line', () => {
    const wrap = document.getElementById('processLineWrap');
    const fill = document.getElementById('processLineFill');
    const section = document.getElementById('process');
    if (!wrap || !fill || !section) return;

    function update() {
      const rect = section.getBoundingClientRect();
      const viewportH = window.innerHeight;
      const total = rect.height + viewportH;
      const scrolled = viewportH - rect.top;
      let ratio = total > 0 ? scrolled / total : 0;
      ratio = Math.max(0, Math.min(1, ratio));
      fill.style.width = (ratio * 100) + '%';
    }
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  });

  /* ---------- 12. Testimonial slider ---------- */
  safeRun('testimonial-slider', () => {
    const track = document.getElementById('testimonialTrack');
    const dotsWrap = document.getElementById('testimonialDots');
    if (!track || !dotsWrap) return;

    const slides = track.children;
    let current = 0;
    let autoplayTimer;

    for (let i = 0; i < slides.length; i++) {
      const dot = document.createElement('span');
      dot.className = 'dot' + (i === 0 ? ' active' : '');
      dot.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(dot);
    }
    const dots = dotsWrap.querySelectorAll('.dot');

    function goTo(index) {
      current = (index + slides.length) % slides.length;
      track.style.transform = `translateX(-${current * 100}%)`;
      dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    function startAutoplay() { autoplayTimer = setInterval(() => goTo(current + 1), 5000); }
    function stopAutoplay() { clearInterval(autoplayTimer); }
    startAutoplay();

    track.parentElement.addEventListener('mouseenter', stopAutoplay);
    track.parentElement.addEventListener('mouseleave', startAutoplay);

    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => {
      const diff = e.changedTouches[0].clientX - touchStartX;
      if (Math.abs(diff) > 50) goTo(current + (diff < 0 ? 1 : -1));
    }, { passive: true });
  });

  /* ---------- 13. Contact form validation + mailto fallback ---------- */
  safeRun('contact-form', () => {
    const form = document.getElementById('contactForm');
    const formSuccess = document.getElementById('formSuccess');
    if (!form || !formSuccess) return;

    const PHONE_BLOCKLIST = [
      '0000000000', '1111111111', '2222222222', '3333333333', '4444444444',
      '5555555555', '6666666666', '7777777777', '8888888888', '9999999999',
      '1234567890', '0987654321'
    ];

    function isValidEmail(value) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()); }
    function isValidPhone(value) {
      if (!value.trim()) return true;
      const digits = value.replace(/\D/g, '');
      const core = digits.length > 10 ? digits.slice(-10) : digits;
      if (core.length !== 10) return false;
      if (PHONE_BLOCKLIST.includes(core)) return false;
      if (/^(\d)\1{9}$/.test(core)) return false;
      return true;
    }
    function setFieldState(group, valid) { group.classList.toggle('invalid', !valid); }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      formSuccess.classList.remove('show');

      const name = form.querySelector('#name');
      const email = form.querySelector('#email');
      const phone = form.querySelector('#phone');
      const service = form.querySelector('#service');
      const message = form.querySelector('#message');
      const budget = form.querySelector('#budget');

      let valid = true;

      const nameValid = name.value.trim().length >= 2;
      setFieldState(name.closest('.form-group'), nameValid);
      valid = valid && nameValid;

      const emailValid = isValidEmail(email.value);
      setFieldState(email.closest('.form-group'), emailValid);
      valid = valid && emailValid;

      const phoneValid = isValidPhone(phone.value);
      setFieldState(phone.closest('.form-group'), phoneValid);
      valid = valid && phoneValid;

      const serviceValid = service.value.trim().length > 0;
      setFieldState(service.closest('.form-group'), serviceValid);
      valid = valid && serviceValid;

      const messageValid = message.value.trim().length >= 10;
      setFieldState(message.closest('.form-group'), messageValid);
      valid = valid && messageValid;

      if (!valid) {
        const firstInvalid = form.querySelector('.form-group.invalid');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      const subject = encodeURIComponent(`New Quote Request from ${name.value.trim()} — Wizhy Studio`);
      const bodyLines = [
        `Name: ${name.value.trim()}`,
        `Email: ${email.value.trim()}`,
        `Phone: ${phone.value.trim() || 'Not provided'}`,
        `Service Interested In: ${service.value}`,
        `Estimated Budget: ${budget.value.trim() || 'Not specified'}`,
        '',
        'Project Details:',
        message.value.trim()
      ];
      const body = encodeURIComponent(bodyLines.join('\n'));

      /* TODO (Antigravity): replace this mailto fallback with a real POST to
         Supabase (insert into a `leads` table) + trigger a Supabase Edge
         Function that emails the team via Resend (or similar). Keep the
         mailto as an offline fallback if the Supabase call fails. See
         wizhy-studio-project-summary_v4.1.md → "Antigravity TODO list". */
      window.location.href = `mailto:hello@wizhy.in?subject=${subject}&body=${body}`;

      formSuccess.classList.add('show');
      form.reset();
      setTimeout(() => formSuccess.classList.remove('show'), 8000);
    });

    form.querySelectorAll('input, select, textarea').forEach(field => {
      field.addEventListener('input', () => field.closest('.form-group').classList.remove('invalid'));
    });
  });

  /* ---------- 14. Copy-to-clipboard for contact rows ---------- */
  safeRun('copy-to-clipboard', () => {
    const toast = document.getElementById('toast');
    if (!toast) return;
    let toastTimer;

    function showToast(msg) {
      toast.textContent = msg;
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), 2200);
    }
    window.__wizhyShowToast = showToast; // exposed for reuse by v4.1 modules below

    document.querySelectorAll('[data-copy]').forEach(el => {
      el.addEventListener('click', (e) => {
        const value = el.getAttribute('data-copy');
        if (!value || value.includes('00000')) return;
        if (navigator.clipboard) {
          e.preventDefault();
          navigator.clipboard.writeText(value).then(() => showToast('Copied: ' + value));
        }
      });
    });
  });

  /* =========================================================
     V4.1 ADDITIONS — new modules only, nothing above this line
     was changed. Each one follows the same safeRun() isolation
     pattern as the v4.0 modules above.
  ========================================================= */

  /* ---------- 15. Cookie consent banner ---------- */
  safeRun('cookie-consent', () => {
    const banner = document.getElementById('cookieBanner');
    if (!banner) return;
    const acceptBtn = document.getElementById('cookieAccept');
    const rejectBtn = document.getElementById('cookieReject');
    let stored;
    try { stored = localStorage.getItem('wizhy-cookie-consent'); } catch (e) { stored = null; }

    if (!stored) {
      setTimeout(() => banner.classList.add('show'), 1200);
    }

    function setConsent(value) {
      try { localStorage.setItem('wizhy-cookie-consent', value); } catch (e) { /* ignore */ }
      banner.classList.remove('show');
      /* TODO (Antigravity): if value === 'accepted', this is the hook point to
         load Google Analytics / any marketing pixels. Keep them OUT of the
         page entirely until this fires, to stay consent-compliant. e.g.:
         if (value === 'accepted') { loadAnalyticsScript(); } */
      window.dispatchEvent(new CustomEvent('wizhy:cookie-consent', { detail: { value } }));
    }

    if (acceptBtn) acceptBtn.addEventListener('click', () => setConsent('accepted'));
    if (rejectBtn) rejectBtn.addEventListener('click', () => setConsent('rejected'));
  });

  /* ---------- 16. Instant SEO Audit tool (mock, client-side lead magnet) ---------- */
  safeRun('seo-audit-tool', () => {
    const form = document.getElementById('auditForm');
    if (!form) return;
    const input = document.getElementById('auditUrl');
    const errorEl = document.getElementById('auditError');
    const loadingEl = document.getElementById('auditLoading');
    const resultEl = document.getElementById('auditResult');
    const scoreFill = document.getElementById('auditScoreFill');
    const scoreNum = document.getElementById('auditScoreNum');
    const urlLabel = document.getElementById('auditResultUrl');
    const suggestionsWrap = document.getElementById('auditSuggestions');

    /* Deterministic pseudo-hash so the same URL always yields the same
       "score" and suggestion set within a session — makes the demo feel
       consistent rather than randomly re-rolling on every submit.
       NOTE: this is a MOCK/illustrative audit, not a real crawl. It is
       intentionally framed this way in the UI copy (see index.html) so
       it functions as an engagement lead-magnet, not a false technical
       claim. Antigravity can later swap this for a real API (e.g.
       Google PageSpeed Insights API — free — or a paid SEO API). */
    function hashString(str) {
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        hash = ((hash << 5) - hash) + str.charCodeAt(i);
        hash |= 0;
      }
      return Math.abs(hash);
    }

    const SUGGESTION_POOL = [
      { type: 'warn', title: 'Meta descriptions may be missing or duplicated', text: 'Unique, keyword-rich meta descriptions on every page improve click-through rate from Google search results.' },
      { type: 'warn', title: 'Image alt text could be incomplete', text: 'Descriptive alt attributes help Google Images rank you and improve accessibility for screen readers.' },
      { type: 'warn', title: 'Page speed on mobile looks improvable', text: 'Compressing images and deferring non-critical scripts typically cuts mobile load time significantly.' },
      { type: 'warn', title: 'No structured data (Schema.org) detected', text: 'Adding JSON-LD structured data can unlock rich results — star ratings, FAQs, breadcrumbs — directly in search.' },
      { type: 'warn', title: 'Heading hierarchy may skip levels', text: 'A clean H1 → H2 → H3 structure helps both SEO crawlers and assistive technology understand your content.' },
      { type: 'warn', title: 'Few internal links between pages', text: 'Internal linking spreads "link equity" across your site and helps Google discover deeper pages faster.' },
      { type: 'ok', title: 'HTTPS is properly configured', text: 'Secure connections are a confirmed Google ranking signal and build visitor trust.' },
      { type: 'ok', title: 'Mobile viewport is responsive', text: 'Your layout adapts to mobile screens, which is essential since most searches happen on mobile.' },
      { type: 'warn', title: 'Core Web Vitals may need attention', text: 'Largest Contentful Paint and Cumulative Layout Shift both influence Google\u2019s ranking algorithm directly.' },
      { type: 'warn', title: 'XML sitemap submission unclear', text: 'Submitting a sitemap.xml via Google Search Console helps new/updated pages get indexed faster.' }
    ];

    function isLikelyUrl(value) {
      const v = value.trim();
      if (!v) return false;
      try {
        const withProto = /^https?:\/\//i.test(v) ? v : 'https://' + v;
        const u = new URL(withProto);
        return u.hostname.includes('.');
      } catch (e) {
        return false;
      }
    }

    function animateScoreRing(pct) {
      const circumference = 327; // 2*PI*52, matches CSS dasharray
      const offset = circumference - (pct / 100) * circumference;
      requestAnimationFrame(() => { scoreFill.style.strokeDashoffset = String(offset); });
    }

    function animateCount(el, target, duration) {
      const start = performance.now();
      function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(eased * target);
        if (progress < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const raw = input.value;
      errorEl.classList.remove('show');
      resultEl.classList.remove('show');

      if (!isLikelyUrl(raw)) {
        errorEl.textContent = 'Please enter a valid website URL (e.g. yourbusiness.com)';
        errorEl.classList.add('show');
        return;
      }

      loadingEl.classList.add('show');

      setTimeout(() => {
        loadingEl.classList.remove('show');

        const cleanUrl = /^https?:\/\//i.test(raw.trim()) ? raw.trim() : 'https://' + raw.trim();
        const hash = hashString(cleanUrl.toLowerCase());
        const score = 48 + (hash % 43); // score range ~48–90, feels "realistic" (never a perfect 100)

        scoreNum.textContent = '0';
        animateCount(scoreNum, score, 1200);
        animateScoreRing(score);
        urlLabel.textContent = cleanUrl;

        // Pick 4 pseudo-random-but-deterministic suggestions from the pool
        const picks = [];
        let seed = hash;
        const poolCopy = SUGGESTION_POOL.slice();
        while (picks.length < 4 && poolCopy.length) {
          seed = (seed * 9301 + 49297) % 233280;
          const idx = seed % poolCopy.length;
          picks.push(poolCopy.splice(idx, 1)[0]);
        }

        suggestionsWrap.innerHTML = '';
        picks.forEach(s => {
          const row = document.createElement('div');
          row.className = 'audit-suggestion audit-suggestion--' + s.type;
          row.innerHTML = `<span class="audit-suggestion__icon">${s.type === 'ok' ? '\u2713' : '!'}</span>
            <div><strong>${s.title}</strong><p>${s.text}</p></div>`;
          suggestionsWrap.appendChild(row);
        });

        resultEl.classList.add('show');
        resultEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }, 1100);
    });
  });

  /* ---------- 17. FAQ accordion ---------- */
  safeRun('faq-accordion', () => {
    const items = document.querySelectorAll('.faq-item');
    if (!items.length) return;

    items.forEach(item => {
      const question = item.querySelector('.faq-item__question');
      const answer = item.querySelector('.faq-item__answer');
      if (!question || !answer) return;

      question.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        items.forEach(other => {
          other.classList.remove('open');
          other.querySelector('.faq-item__answer').style.maxHeight = null;
          other.querySelector('.faq-item__question').setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          item.classList.add('open');
          answer.style.maxHeight = answer.scrollHeight + 'px';
          question.setAttribute('aria-expanded', 'true');
        }
      });
    });
  });

  /* ---------- 18. Voice input (Web Speech API) on contact form ---------- */
  safeRun('voice-input', () => {
    const micBtn = document.getElementById('micBtn');
    const textarea = document.getElementById('message');
    if (!micBtn || !textarea) return;

    const SpeechRecognitionCtor = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognitionCtor) {
      // Not supported (e.g. Firefox) — hide the mic button gracefully rather
      // than showing a broken control.
      micBtn.style.display = 'none';
      return;
    }

    const recognition = new SpeechRecognitionCtor();
    recognition.lang = 'en-IN';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    let listening = false;

    recognition.addEventListener('result', (e) => {
      const transcript = Array.from(e.results).map(r => r[0].transcript).join(' ');
      const existing = textarea.value.trim();
      textarea.value = existing ? existing + ' ' + transcript : transcript;
      textarea.dispatchEvent(new Event('input'));
    });
    recognition.addEventListener('end', () => {
      listening = false;
      micBtn.classList.remove('listening');
    });
    recognition.addEventListener('error', () => {
      listening = false;
      micBtn.classList.remove('listening');
      if (window.__wizhyShowToast) window.__wizhyShowToast('Voice input unavailable — please type instead');
    });

    micBtn.addEventListener('click', () => {
      if (listening) {
        recognition.stop();
        return;
      }
      try {
        recognition.start();
        listening = true;
        micBtn.classList.add('listening');
      } catch (err) {
        /* recognition already active or blocked by permissions */
      }
    });
  });

  /* ---------- 19. Lead magnet: gated checklist download ---------- */
  safeRun('lead-magnet-download', () => {
    const form = document.getElementById('leadMagnetForm');
    if (!form) return;
    const emailInput = document.getElementById('leadMagnetEmail');
    const successEl = document.getElementById('leadMagnetSuccess');

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailInput.focus();
        return;
      }

      /* TODO (Antigravity): POST { email, source: 'checklist-download' } to a
         Supabase `leads` or `newsletter_subscribers` table here instead of
         (or in addition to) the console.log below. Example:
         await supabase.from('leads').insert({ email, source: 'checklist' }); */
      console.info('[Wizhy Studio] Lead magnet email captured (wire to Supabase):', email);

      successEl.classList.add('show');
      form.reset();

      /* Trigger the actual file download. Replace assets/wizhy-website-checklist.pdf
         with the real, designed PDF before going live — see project summary
         "Known Placeholders" table. */
      const link = document.createElement('a');
      link.href = 'assets/wizhy-website-checklist.pdf';
      link.download = 'Wizhy-Studio-Website-SEO-Checklist.pdf';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      setTimeout(() => successEl.classList.remove('show'), 6000);
    });
  });

  /* ---------- 20. Newsletter signup (footer) ---------- */
  safeRun('newsletter-signup', () => {
    const form = document.getElementById('newsletterForm');
    if (!form) return;
    const emailInput = document.getElementById('newsletterEmail');
    const successEl = document.getElementById('newsletterSuccess');

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailInput.focus();
        return;
      }

      /* TODO (Antigravity): POST { email } to a Supabase `newsletter_subscribers`
         table (with a unique constraint on email to avoid duplicates). Example:
         await supabase.from('newsletter_subscribers').insert({ email }); */
      console.info('[Wizhy Studio] Newsletter signup captured (wire to Supabase):', email);

      successEl.classList.add('show');
      form.reset();
      setTimeout(() => successEl.classList.remove('show'), 6000);
    });
  });

  /* ---------- 21. WhatsApp FAB show/hide on scroll (mirrors back-to-top) ---------- */
  safeRun('whatsapp-fab', () => {
    const fab = document.getElementById('whatsappFab');
    if (!fab) return;
    // Always visible — WhatsApp is a primary contact channel, unlike
    // back-to-top which is only useful once you've scrolled. No JS needed
    // beyond making sure it doesn't error if absent on some future page.
  });

});

```

---

## File: `robots.txt`

```text
# Wizhy Studio — robots.txt
# Allow all well-behaved crawlers full access to the site.

User-agent: *
Allow: /

# Sitemap location — update the domain if it changes from wizhy.in
Sitemap: https://wizhy.in/sitemap.xml

```

---

## File: `sitemap.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  Wizhy Studio — sitemap.xml

  The site is currently a single-page (anchor-linked) site, so only the
  homepage is listed. When the placeholder About Us / FAQ (standalone) /
  Blog / Privacy Policy pages referenced in the project summary are built
  as real separate pages, add a <url> entry for each one here, e.g.:

  <url>
    <loc>https://wizhy.in/blog/</loc>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
-->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://wizhy.in/</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>

```

---

## File: `antigravity-notes/.github/workflows/supabase-keep-alive.yml`

```yaml
# Supabase free-tier keep-alive ping
#
# WHY THIS EXISTS: Supabase's free tier automatically PAUSES a project
# after 7 days with no activity. This tiny workflow pings the project's
# REST endpoint every 3 days so it never goes idle long enough to pause —
# it does NOT touch or modify any data, it's a harmless read-only ping.
#
# SETUP (Antigravity):
# 1. Place this file at the repo root path: .github/workflows/supabase-keep-alive.yml
# 2. In your GitHub repo → Settings → Secrets and variables → Actions, add:
#      SUPABASE_URL       = https://YOUR-PROJECT-REF.supabase.co
#      SUPABASE_ANON_KEY  = your project's public "anon" key (NOT the service_role key)
# 3. Commit + push. GitHub will run this automatically on the schedule below
#    even if no one opens the repo — that's the whole point.
#
# NOTE: This only prevents the free-tier auto-pause. It does NOT increase
# the 500MB database storage limit or any other free-tier cap. If the
# project outgrows the free tier, the real fix is upgrading to the
# Supabase Pro plan (~$25/month), which removes pausing entirely.

name: Keep Supabase Alive

on:
  schedule:
    - cron: "0 8 */3 * *"   # every 3 days at 08:00 UTC
  workflow_dispatch: {}        # allows manually triggering a run from the Actions tab

jobs:
  ping:
    runs-on: ubuntu-latest
    steps:
      - name: Ping Supabase REST endpoint
        run: |
          curl -s "${{ secrets.SUPABASE_URL }}/rest/v1/" \
            -H "apikey: ${{ secrets.SUPABASE_ANON_KEY }}" \
            -o /dev/null

```

---

