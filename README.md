## Wizhy Studio — v4.1 (Feature Update, built on top of v4.0)

Premium, single-page landing site for **Wizhy Studio** (web development + SEO + growth agency), now with a full set of lead-generation, SEO, and accessibility features added on top of the v4.0 editorial redesign.

**v4.1 is additive** — it does not change the v4.0 visual design system (Playfair Display + Instrument Sans, warm paper/terracotta palette, light/dark toggle). See `wizhy-studio-project-summary_v4.1.md` → Section 7 for exactly which files to swap into your existing v4.0 folder.

### What's new in v4.1
- Instant Website/SEO Audit tool (lead magnet)
- Voice input on the contact form (Web Speech API, free, no API key)
- No-code AI chatbot placeholder (Tidio / Tawk.to — just paste an ID)
- FAQ accordion
- WhatsApp floating action button
- Free Website & SEO Checklist — real 40-point PDF, email-gated
- Technical SEO: `robots.txt`, `sitemap.xml`, Open Graph + Twitter Card tags, JSON-LD structured data, a new branded OG share image
- Cookie consent banner
- Newsletter signup (footer)
- Accessibility pass: skip-link, ARIA labels, keyboard navigation
- A GitHub Actions workflow to keep a future Supabase free-tier project from auto-pausing

Full details, the Antigravity backend TODO list, and the headless-browser verification record are in **`wizhy-studio-project-summary_v4.1.md`** — read that first.

### Quick Start
- Double-click `index.html` to open it in your browser. No server, no build step, no internet connection required (fonts are self-hosted).
- Note: this package does **not** include the `assets/fonts/*.woff2` binaries (they're unchanged from v4.0) — copy `index.html`, `css/style.css`, and `js/script.js` into your existing v4.0 folder rather than expecting a font-complete standalone package. See the project summary §7 for exact merge instructions.

### Files in this package
- `index.html` — updated page markup (new sections added)
- `css/style.css` — updated styles (v4.0 system preserved + v4.1 additions appended)
- `css/fonts.css` — unchanged from v4.0
- `js/script.js` — updated scripts (v4.0 modules preserved + v4.1 modules appended)
- `robots.txt`, `sitemap.xml` — new
- `assets/og-image.jpg` — new branded social-share image
- `assets/wizhy-website-checklist.pdf` — new, real 40-point checklist (the lead-magnet download)
- `.github/workflows/supabase-keep-alive.yml` — new, for Antigravity once the project is on GitHub + Supabase
- `wizhy-studio-project-summary_v4.1.md` — **read this first**
- `wizhy-studio-source-bundle_v4.1.md` — all new/changed source in one `.md` for AI hand-off (Antigravity, a new chat, etc.)

### ⚠️ Before Going Live
See `wizhy-studio-project-summary_v4.1.md` → Section 8 for the full placeholder list (JSON-LD contact details, domain references, chat widget account ID).
