# 05: LLMs.txt & Agentic SEO Synchronization Protocol

This protocol enforces automatic synchronization between website content changes in Antigravity (AG) and the machine-readable `/llms.txt` file served to AI search engines (ChatGPT Search, Gemini, Perplexity, Claude, etc.).

---

## 1. Core Rule & Trigger Condition
Whenever any changes are made to:
- **Pricing Packages** (e.g. Starter, Growth, Business package pricing or features).
- **Core Services** (e.g. Web Design, Technical SEO, E-Commerce, Maintenance).
- **Special Offers** (e.g. First 10 Free Websites offer or seasonal promotions).
- **Pages / Templates** (e.g. creating or modifying `page-*.php` files or subpage routes).
- **Agency Information** (e.g. contact phone, WhatsApp, email, hours, guarantees).

**Antigravity (AG) MUST automatically synchronize the changes into `llms.txt` and `wizhy-ag-theme/llms.txt` in the exact same task.**

---

## 2. Standard `llms.txt` Structure
The `llms.txt` file must always adhere to the official markdown specification:

1. `# <Site Name>` with a blockquote summary containing the primary value proposition and performance guarantees.
2. `## About <Agency Name>`: Brief overview, official URL, contact email, WhatsApp/Phone, hours, and location.
3. `## Special Offers`: Active promotions (e.g., First 10 Free Websites offer).
4. `## Pricing Packages`: Itemized listing of packages with current prices (₹ and $) and delivery turnaround times.
5. `## Key Pages & Documentation`: Direct markdown links `[Page Title](https://web.wizhy.in/path/)` with 1-sentence explanations.
6. `## Core Services & Capabilities`: Technical breakdown (Bespoke UI/UX, SEO Schema, 100/100 Core Web Vitals, Custom Integrations).
7. `## AI Assistant`: Description of the built-in assistant (WizAI).

---

## 3. Automated Deployment Enforcement
- The deployment script (`scratch/deploy_to_hostinger.py`) automatically parses `front-page.php` and theme pages to ensure `llms.txt` is updated and validated prior to pushing live to Hostinger WordPress.
- The remote file is always served at `https://web.wizhy.in/llms.txt` with:
  - `Content-Type: text/plain; charset=utf-8`
  - `Access-Control-Allow-Origin: *`
  - `Cache-Control: max-age=86400, public`
