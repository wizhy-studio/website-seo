# ========================================================================
# WIZHY STUDIO — MASTER IMAGE & SEO OPTIMIZATION STANDARDS
# ========================================================================

1. IMAGE LOADING STRATEGY
------------------------------------------------------------------------
A. Above-the-Fold (Critical Assets / Hero / Main Logos / LCP):
- Use fetchpriority="high"
- Use decoding="async"
- Do NOT use loading="lazy"
- Add <link rel="preload" as="image" href="..."> for primary hero visuals.

B. Below-the-Fold (Cards, Testimonials, Case Studies, Footer):
- Use loading="lazy"
- Use decoding="async"

2. FORMAT PRIORITY & FILE SIZES
------------------------------------------------------------------------
1. SVG: For logos, icons, vector badges, geometric UI elements (Target: < 10 KB).
2. AVIF: Primary modern format for photos, hero visuals, showcase mockups (Target: 100–300 KB for hero; 20–80 KB for cards/portraits).
3. WebP: Secondary fallback for modern browsers.
4. PNG/JPEG: Fallback only.

3. PREVENT CUMULATIVE LAYOUT SHIFT (CLS)
------------------------------------------------------------------------
- ALWAYS define explicit width and height attributes on all <img> and <video> tags.
- Use <picture> tags with responsive srcset and sizes attributes so mobile devices never download desktop-sized assets.
- Target Core Web Vitals: LCP < 2.0s, CLS = 0.00, FID/INP < 100ms.
