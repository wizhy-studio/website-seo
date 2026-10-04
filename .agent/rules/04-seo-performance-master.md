# ========================================================================
# WIZHY STUDIO — MASTER SEO, PERFORMANCE & WORDPRESS DEPLOYMENT STANDARD
# Rule File: 04-seo-performance-master.md  (v1.0 — 2026-10-05)
# ========================================================================
# This rule governs ALL future modifications to the Wizhy Web Studio website.
# Every agent, developer, or AI making changes MUST read and follow this file.
# ========================================================================

## 0. CONTEXT

- **Stack**: Vanilla HTML/CSS/JS static site, hosted on WordPress (Hostinger)
- **Target URL**: https://web.wizhy.in
- **GitHub Repo**: https://github.com/wizhy-studio/website-seo.git
- **Themes**: light, dark, espresso, lune (CSS custom properties via `data-theme` on `<html>`)
- **Fonts**: Self-hosted WOFF2 variable fonts (ALREADY DONE — never change to CDN)
- **Schema**: ProfessionalService JSON-LD (ALREADY DONE)

---

## 1. AUDIT FINDINGS — CURRENT STATE (2026-10-05)

### Already Correct (Do NOT break these)
| Item | Status |
|------|--------|
| Self-hosted fonts (WOFF2, variable, font-display: swap) | DONE |
| Canonical tag | DONE |
| Meta description, title, OG tags | DONE |
| Twitter Card tags | DONE |
| JSON-LD ProfessionalService schema | DONE |
| robots.txt with Sitemap reference | DONE |
| sitemap.xml with all production URLs | DONE |
| lang="en" on html tag | DONE |
| Viewport meta tag | DONE |
| robots: index, follow meta | DONE |
| Alt text on all img tags | DONE |
| width + height on navbar logo | DONE |
| fetchpriority="high" on navbar logo | DONE |
| loading="lazy" on footer logo | DONE |

### Missing / To Be Implemented (Ordered by Priority)
| Priority | Item | Impact |
|----------|------|--------|
| P0 CRITICAL | Minified CSS (style.min.css) | Page Speed / LCP |
| P0 CRITICAL | Minified JS (script.min.js) | Page Speed / LCP |
| P0 CRITICAL | defer on Supabase, Razorpay & script.js | Render-blocking |
| P0 CRITICAL | link rel preload for critical fonts | LCP |
| P0 CRITICAL | link rel preconnect for CDN domains | DNS latency |
| P0 CRITICAL | Logo as SVG (currently 69KB PNG) | LCP, bandwidth |
| P0 CRITICAL | AVIF + WebP images with picture tags | LCP, bandwidth |
| P1 HIGH | FAQ Schema (FAQPage JSON-LD) | Rich Results |
| P1 HIGH | WebSite schema with SearchAction | Sitelinks Search |
| P1 HIGH | og:locale meta tag | Social sharing |
| P1 HIGH | twitter:image:alt meta tag | Accessibility |
| P1 HIGH | theme-color meta tag | Mobile UX |
| P2 MEDIUM | Critical CSS inlined in head (above-fold) | FCP |
| P2 MEDIUM | .htaccess for compression + caching (Hostinger) | Repeat visits |
| P2 MEDIUM | Security headers via .htaccess | Trust signals |
| P2 MEDIUM | Disallow demo.html in robots.txt | Crawl budget |

---

## 2. IMAGE STANDARDS (MANDATORY for every image added or edited)

### 2.1 Format Priority (Never deviate from this order)
```
SVG    -> Logos, icons, inline vectors, UI badges          (target: < 10 KB)
AVIF   -> Photos, hero visuals, portfolio mockups          (target: < 150 KB hero, < 60 KB card)
WebP   -> Fallback for AVIF on older Safari                (target: < 250 KB hero, < 100 KB card)
PNG    -> Fallback only (transparency-critical assets)
JPEG   -> OG image only (must be 1200x630px, < 100 KB)
```

### 2.2 The picture Pattern (Required for ALL content images)
```html
<!-- CORRECT — Always use this exact structure for content images -->
<picture>
  <source srcset="assets/images/hero-bg.avif" type="image/avif">
  <source srcset="assets/images/hero-bg.webp" type="image/webp">
  <img
    src="assets/images/hero-bg.png"
    alt="Descriptive, keyword-rich alt text"
    width="1440"
    height="800"
    fetchpriority="high"
    decoding="async"
  >
</picture>
```

### 2.3 Responsive Images with srcset (Required for hero/large images)
```html
<picture>
  <source
    type="image/avif"
    srcset="assets/images/hero-bg-480.avif 480w,
            assets/images/hero-bg-768.avif 768w,
            assets/images/hero-bg-1200.avif 1200w"
    sizes="(max-width: 600px) 480px, (max-width: 900px) 768px, 1200px"
  >
  <source
    type="image/webp"
    srcset="assets/images/hero-bg-480.webp 480w,
            assets/images/hero-bg-768.webp 768w,
            assets/images/hero-bg-1200.webp 1200w"
    sizes="(max-width: 600px) 480px, (max-width: 900px) 768px, 1200px"
  >
  <img src="assets/images/hero-bg.jpg" alt="..." width="1200" height="800" fetchpriority="high">
</picture>
```

### 2.4 Loading Strategy Table
| Position | loading attr | fetchpriority | decoding |
|----------|-------------|---------------|----------|
| Above fold / hero / navbar logo | omit (eager default) | "high" | "async" |
| Below fold (cards, team, portfolio, footer) | "lazy" | omit | "async" |

### 2.5 Conversion Process (for all new images)
- Use Squoosh (https://squoosh.app) or Sharp CLI to generate AVIF and WebP
- Store all images in: assets/images/
- Naming convention: [descriptive-slug]-[width].[format] (e.g. hero-bg-1200.avif)
- NEVER commit raw uncompressed PNG/JPEG as production image (og-image.jpg is the only exception)

### 2.6 Logo — Convert PNG to SVG (Priority: P0 Critical)
- assets/wizhy-studio-logo.png is 69KB — this is unacceptable for an above-fold asset
- Replace with assets/wizhy-studio-logo.svg
- Reference in HTML as: <img src="assets/wizhy-studio-logo.svg" alt="..." width="38" height="38">
- No picture tag needed for SVG logos
- Target file size: < 5 KB

---

## 3. CSS/JS MINIFICATION STANDARD

### 3.1 Source vs. Production Files
- Source files (edit these): css/style.css, js/script.js
- Production files (serve these): css/style.min.css, js/script.min.js
- NEVER edit the .min.* files directly

### 3.2 Minification Commands
```bash
# Install tools globally (one-time setup)
npm install -g clean-css-cli uglify-js

# After every edit to style.css:
cleancss -o css/style.min.css css/style.css --level 2

# After every edit to script.js:
uglifyjs js/script.js -c -m -o js/script.min.js
```

### 3.3 What to Reference in HTML (Always)
```html
<!-- In <head> -->
<link rel="stylesheet" href="css/fonts.css">
<link rel="stylesheet" href="css/style.min.css">

<!-- Before </body> with defer -->
<script src="js/script.min.js" defer></script>
```

### 3.4 Expected File Size Reduction
| File | Unminified | Target Minified |
|------|-----------|-----------------|
| style.css | 136 KB | 85-95 KB |
| script.js | 49 KB | 22-28 KB |

---

## 4. SCRIPT LOADING STRATEGY (MANDATORY — Never break this)

### 4.1 All External Script Tags MUST Use defer or async
```html
<!-- WRONG — render-blocking, hurts LCP -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<!-- CORRECT — non-blocking -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2" defer></script>
<script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
<script src="js/script.min.js" defer></script>
```

Rules:
- Use defer for scripts that need the DOM (Supabase, Razorpay, main script)
- Use async for analytics, tracking pixels that do not need DOM order
- The inline theme anti-FOUC script in head is the ONLY allowed render-blocking script

### 4.2 Preconnect for External CDN Domains
Add in head before any external resource reference:
```html
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preconnect" href="https://checkout.razorpay.com" crossorigin>
```

---

## 5. CORRECT head TAG ORDERING (MANDATORY)

Follow this exact order — critical for performance scoring:

```html
<head>
  <!-- Step 1: Charset and viewport FIRST -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Step 2: Core SEO meta -->
  <title>...</title>
  <meta name="description" content="...">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://web.wizhy.in/">
  <meta name="author" content="Wizhy Web Studio">
  <meta name="theme-color" content="#C8481E">

  <!-- Step 3: Preconnect CDN domains (resolve DNS early) -->
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="preconnect" href="https://checkout.razorpay.com" crossorigin>

  <!-- Step 4: Preload critical assets (hero image if there is one) -->
  <!-- Only add preload for the ACTUAL Largest Contentful Paint element -->
  <!-- <link rel="preload" as="image" href="assets/images/hero-bg.avif" type="image/avif"> -->

  <!-- Step 5: Preload fonts (only Latin subset — first request is fastest) -->
  <link rel="preload" as="font" type="font/woff2" href="assets/fonts/instrument-latin.woff2" crossorigin>
  <link rel="preload" as="font" type="font/woff2" href="assets/fonts/playfair-latin.woff2" crossorigin>

  <!-- Step 6: Stylesheets -->
  <link rel="stylesheet" href="css/fonts.css">
  <link rel="stylesheet" href="css/style.min.css">

  <!-- Step 7: OG / Twitter / Schema tags -->
  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_IN">
  <meta property="og:site_name" content="Wizhy Web Studio (WWS)">
  <meta property="og:title" content="...">
  <meta property="og:description" content="...">
  <meta property="og:url" content="https://web.wizhy.in/">
  <meta property="og:image" content="https://web.wizhy.in/assets/og-image.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Wizhy Web Studio — Web Design and SEO Agency">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="...">
  <meta name="twitter:description" content="...">
  <meta name="twitter:image" content="https://web.wizhy.in/assets/og-image.jpg">
  <meta name="twitter:image:alt" content="Wizhy Web Studio — Web Design and SEO Agency">

  <!-- Step 8: JSON-LD schemas -->
  <script type="application/ld+json">{ ... ProfessionalService ... }</script>
  <script type="application/ld+json">{ ... FAQPage ... }</script>
  <script type="application/ld+json">{ ... WebSite ... }</script>

  <!-- Step 9: Favicon -->
  <link rel="icon" href="...">

  <!-- Step 10: Theme anti-FOUC inline script (ONLY allowed blocking script) -->
  <script>(function(){ try { var t=localStorage.getItem('wizhy-theme'); document.documentElement.setAttribute('data-theme', t || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')); } catch(e){} })();</script>
</head>

<!-- Scripts go before </body> — ALWAYS with defer -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2" defer></script>
<script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
<script src="js/script.min.js" defer></script>
```

---

## 6. STRUCTURED DATA / SCHEMA (JSON-LD) STANDARDS

### 6.1 Current Schemas
- ProfessionalService — DONE

### 6.2 FAQPage Schema to Add (Match actual FAQ section content)
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How long does it take to build a website?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Typically 2-4 weeks depending on complexity..."
      }
    }
  ]
}
```

### 6.3 WebSite Schema to Add
```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Wizhy Web Studio",
  "url": "https://web.wizhy.in/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://web.wizhy.in/?s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
```

### 6.4 Schema Rules
- Validate all schemas at https://validator.schema.org before committing
- Never have duplicate @type schemas on same page (one FAQPage max)
- FAQ answers must match visible on-page content exactly

---

## 7. .HTACCESS FOR HOSTINGER (Create before WordPress deployment)

Save as .htaccess in the WordPress/site root:

```apache
# ============================================================
# WIZHY STUDIO — .htaccess Performance and Security Config
# ============================================================

# 1. Enable Gzip Compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/css text/javascript
  AddOutputFilterByType DEFLATE application/javascript application/json
  AddOutputFilterByType DEFLATE image/svg+xml font/woff2
</IfModule>

# 2. Browser Caching
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/html                   "access plus 1 hour"
  ExpiresByType text/css                    "access plus 1 year"
  ExpiresByType application/javascript      "access plus 1 year"
  ExpiresByType image/avif                  "access plus 1 year"
  ExpiresByType image/webp                  "access plus 1 year"
  ExpiresByType image/png                   "access plus 1 year"
  ExpiresByType image/jpeg                  "access plus 1 year"
  ExpiresByType image/svg+xml               "access plus 1 year"
  ExpiresByType font/woff2                  "access plus 1 year"
</IfModule>

# 3. Cache-Control Headers
<IfModule mod_headers.c>
  <FilesMatch "\.(css|js|avif|webp|png|jpg|jpeg|svg|woff2)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
  </FilesMatch>
  <FilesMatch "\.(html|htm)$">
    Header set Cache-Control "public, max-age=3600, must-revalidate"
  </FilesMatch>

  # Security Headers
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set X-Content-Type-Options "nosniff"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# 4. HTTPS Redirect
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## 8. WORDPRESS + HOSTINGER DEPLOYMENT CHECKLIST

### Phase A — Prepare files locally before upload
- [ ] Run: cleancss -o css/style.min.css css/style.css --level 2
- [ ] Run: uglifyjs js/script.js -c -m -o js/script.min.js
- [ ] Update all HTML: change style.css to style.min.css
- [ ] Update all HTML: change script.js to script.min.js and add defer
- [ ] Add defer to Supabase and Razorpay script tags
- [ ] Add preconnect for CDN domains in all HTML head sections
- [ ] Add font preload hints in all HTML head sections
- [ ] Add og:locale, og:image:alt, twitter:image:alt, theme-color meta tags
- [ ] Convert logo PNG to SVG — update all HTML references
- [ ] Add AVIF + WebP versions of any images used
- [ ] Wrap all content img tags in picture tags with AVIF/WebP sources
- [ ] Add FAQPage JSON-LD schema
- [ ] Add WebSite JSON-LD schema
- [ ] Validate all schemas at validator.schema.org
- [ ] Create .htaccess with compression, caching, and security headers
- [ ] Update robots.txt to Disallow: /demo.html and /dynamic_parallax.html

### Phase B — Files to Upload to Hostinger (Only these)
```
index.html
contact.html
cookie-policy.html
delivery-policy.html
privacy-policy.html
refund-policy.html
terms.html
css/fonts.css
css/style.min.css      <- production CSS (minified)
js/script.min.js       <- production JS (minified)
assets/fonts/          <- all 4 WOFF2 font files
assets/images/         <- AVIF, WebP, PNG fallback images
assets/wizhy-studio-logo.svg
assets/og-image.jpg
assets/wizhy-website-checklist.pdf
robots.txt
sitemap.xml
.htaccess
```

### Phase B — DO NOT Upload
```
Archive/
temp_files/
can be deleted/
demo.html
dynamic_parallax.html
headless_qa.js
create_backup.ps1
css/style.css          <- source only, not needed live
js/script.js           <- source only, not needed live
*.md summary files
wizhy-studio-v4_1-package/
.git/
.github/
.agent/
node_modules/
```

### Phase C — Post-Upload Steps
- [ ] Verify live site loads at https://web.wizhy.in
- [ ] Run PageSpeed Insights (target: 90+ mobile, 95+ desktop)
- [ ] Set up Cloudflare free tier as CDN in front of Hostinger
- [ ] Submit sitemap to Google Search Console
- [ ] Verify indexing via Google Search Console URL Inspection tool
- [ ] Test all 4 themes on live site
- [ ] Test contact form submission on live site
- [ ] Test all policy page links

---

## 9. CORE WEB VITALS TARGETS

| Metric | Target | Primary Blocker (Current) |
|--------|--------|--------------------------|
| LCP (Largest Contentful Paint) | < 2.0s | Render-blocking scripts, unminified CSS |
| CLS (Cumulative Layout Shift) | 0.00 | Already done — width/height set on images |
| INP (Interaction to Next Paint) | < 100ms | script.js not deferred — fix with defer |
| FCP (First Contentful Paint) | < 1.5s | Blocking CSS + no preload font hints |
| TTFB (Time To First Byte) | < 600ms | Hostinger server response + no CDN |

---

## 10. SEO CONTENT RULES

### 10.1 Heading Hierarchy (Never skip levels)
- ONE h1 per page — must contain primary keyword
- h2 for section titles — include secondary keywords naturally
- h3 for card or item titles within sections
- Never jump from h1 to h3 without h2

### 10.2 Alt Text Formula
```
[What the image shows] + [Context/Purpose] + [Brand if relevant]

GOOD: alt="Web design and SEO services overview — Wizhy Web Studio"
BAD:  alt="image1" or alt="" (empty is only allowed for decorative SVG icons)
```

### 10.3 Internal Linking Rules
- Every policy page must link back to index.html
- Footer must have links to all key sections
- Anchor text must be descriptive — never use "click here" or "read more"

### 10.4 Page Speed Budget (Hard Limits)
| Asset Category | Max Size (Total, gzipped) |
|---------------|--------------------------|
| HTML | < 30 KB |
| CSS (minified + gzipped) | < 50 KB |
| JS (minified + gzipped) | < 35 KB |
| Above-fold images | < 200 KB |
| Total first-load transfer | < 600 KB |

---

## 11. THINGS THAT WILL HURT SEO — NEVER DO THESE

1. Add Google Fonts CDN link — fonts are already self-hosted, keep it that way
2. Load JavaScript synchronously without defer or async
3. Reference unminified CSS or JS in production HTML
4. Add large uncompressed images without AVIF/WebP conversion
5. Remove alt attribute from any img tag
6. Add multiple h1 tags on a single page
7. Remove the canonical tag from any page
8. Leave demo.html or dynamic_parallax.html accessible on the live server
9. Use display:none to hide large content blocks (Google may skip indexing)
10. Mix http and https resources (breaks security headers)

---

## 12. TOOLS REFERENCE

| Tool | Purpose | URL |
|------|---------|-----|
| PageSpeed Insights | Core Web Vitals audit | https://pagespeed.web.dev |
| Schema Validator | Validate JSON-LD | https://validator.schema.org |
| Squoosh | AVIF/WebP conversion | https://squoosh.app |
| Google Search Console | Submit sitemap, monitor indexing | https://search.google.com/search-console |
| GTmetrix | Performance analysis | https://gtmetrix.com |
| Cloudflare | CDN + caching + DDoS | https://cloudflare.com |

---

*Last Updated: 2026-10-05*
*Rule Version: v1.0*
*Applies To: All files in c:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1*
