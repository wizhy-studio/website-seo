// =========================================================
// WIZHY WEB STUDIO — BLOG DATA ENGINE
// All blog metadata is centralized here.
// When you publish a new article:
// 1. Save your article HTML in /blogs/your-post-title.html
// 2. Add an entry at the top of this array!
// =========================================================

const WIZHY_BLOGS = [
  {
    id: 1,
    title: "How We Make Client Websites Load Under 0.8s (And Score 100/100)",
    slug: "blogs/how-we-make-websites-load-under-0-8-seconds.html",
    category: "Performance",
    badge: "Speed Guide",
    date: "Oct 15, 2026",
    readTime: "5 min read",
    author: {
      name: "Wizhy Dev Team",
      role: "Lead Systems Architect",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=900&q=80",
    excerpt: "A deep dive into sub-second asset delivery, AVIF/WebP image pipelines, critical CSS inlining, and why zero-framework vanilla builds beat heavy stacks.",
    featured: true,
    spotlight: true // Top visual card on landing page
  },
  {
    id: 2,
    title: "Why 73% of Business Websites Fail to Convert Visitors Into Paying Leads",
    slug: "blogs/why-business-websites-fail-to-convert.html",
    category: "Conversion",
    badge: "Conversion Audit",
    date: "Oct 12, 2026",
    readTime: "6 min read",
    author: {
      name: "Wizhy Strategy",
      role: "CRO Specialist",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=900&q=80",
    excerpt: "The subtle psychological leaks killing your conversion rate: cognitive load, weak hero value propositions, and missing micro-commitments.",
    featured: true,
    spotlight: true // Top visual card on landing page
  },
  {
    id: 3,
    title: "The 2026 Technical SEO Checklist Every Founder Needs Before Launch",
    slug: "blogs/2026-technical-seo-checklist.html",
    category: "SEO & Traffic",
    badge: "SEO Framework",
    date: "Oct 08, 2026",
    readTime: "7 min read",
    author: {
      name: "Wizhy Search Team",
      role: "Technical SEO Lead",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?auto=format&fit=crop&w=900&q=80",
    excerpt: "From JSON-LD schema graphs and crawl budget management to canonical integrity and mobile indexing nuances that guarantee Day-1 search visibility.",
    featured: true,
    spotlight: true // Top visual card on landing page
  },
  {
    id: 4,
    title: "Why We Chose Vanilla Web Standards Over Heavy React Frameworks",
    slug: "blogs/vanilla-standards-over-heavy-frameworks.html",
    category: "Architecture",
    badge: "Engineering",
    date: "Sep 28, 2026",
    readTime: "4 min read",
    author: {
      name: "Wizhy Engineering",
      role: "Frontend Architect",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80",
    excerpt: "How modern CSS and native ES modules eliminated 400KB of runtime bloat while outperforming SPA hydration on mobile devices.",
    featured: true,
    spotlight: false
  },
  {
    id: 5,
    title: "The Secret Psychology of High-Converting Hero Sections",
    slug: "blogs/psychology-of-high-converting-hero-sections.html",
    category: "Web Design",
    badge: "UI/UX Design",
    date: "Sep 22, 2026",
    readTime: "5 min read",
    author: {
      name: "Wizhy Design",
      role: "Creative Director",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=600&q=80",
    excerpt: "How eye-tracking studies, F-shaped scan patterns, and 3-second comprehension rules dictate whether a visitor bounces or reads on.",
    featured: true,
    spotlight: false
  },
  {
    id: 6,
    title: "Core Web Vitals in 2026: INP, LCP & What Google Ranks For",
    slug: "blogs/core-web-vitals-inp-lcp-guide.html",
    category: "SEO & Traffic",
    badge: "Google Search",
    date: "Sep 18, 2026",
    readTime: "6 min read",
    author: {
      name: "Wizhy Search Team",
      role: "Technical SEO Lead",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80",
    excerpt: "Interaction to Next Paint (INP) is now a decisive ranking factor. Here's how to debug JavaScript event loop delays and secure green scores.",
    featured: false,
    spotlight: false
  },
  {
    id: 7,
    title: "How to Redesign Your Website Without Losing Existing Google Rankings",
    slug: "blogs/redesign-website-without-losing-seo.html",
    category: "SEO & Traffic",
    badge: "Site Migration",
    date: "Sep 10, 2026",
    readTime: "8 min read",
    author: {
      name: "Wizhy SEO Lead",
      role: "Site Migration Architect",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?auto=format&fit=crop&w=600&q=80",
    excerpt: "Step-by-step 301 URL redirect mapping, XML sitemap verification, and staging environments to protect your organic traffic during an upgrade.",
    featured: false,
    spotlight: false
  },
  {
    id: 8,
    title: "Typography Rules That Instantly Make Your Brand Look 10x More Premium",
    slug: "blogs/typography-rules-for-premium-websites.html",
    category: "Web Design",
    badge: "Typography",
    date: "Sep 02, 2026",
    readTime: "4 min read",
    author: {
      name: "Wizhy Design",
      role: "UI Stylist",
      avatar: "assets/wizai-avatar-sm.webp"
    },
    image: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80",
    excerpt: "Font pairing harmony, fluid clamp() scales, optical kerning, and line-height ratios that establish editorial prestige.",
    featured: false,
    spotlight: false
  }
];

if (typeof module !== 'undefined' && module.exports) {
  module.exports = WIZHY_BLOGS;
}
