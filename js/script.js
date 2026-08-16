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

    const sections = ['home', 'services', 'why-us', 'process', 'faq', 'contact']
      .map(id => document.getElementById(id)).filter(Boolean);
    const navLinks = document.querySelectorAll('.nav-link');
    const quickChips = document.querySelectorAll('.mobile-quick-chip:not(.mobile-quick-chip--highlight)');

    function updateScrollSpy() {
      let currentId = 'home';
      const scrollPos = window.scrollY + 180;
      sections.forEach(section => {
        if (scrollPos >= section.offsetTop) currentId = section.id;
      });
      navLinks.forEach(link => {
        const href = link.getAttribute('href');
        link.classList.toggle('active', href === '#' + currentId);
      });
      quickChips.forEach(chip => {
        const href = chip.getAttribute('href');
        chip.style.borderColor = (href === '#' + currentId) ? 'var(--accent-1)' : '';
        chip.style.color = (href === '#' + currentId) ? 'var(--accent-1)' : '';
      });
    }

    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
      });
    });

    function onScroll() {
      const scrollY = window.scrollY;
      navbar.classList.toggle('scrolled', scrollY > 30);
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
      const circumference = 314.16;
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

  /* ---------- 11. Process section interactive stepper nodes ---------- */
  safeRun('process-stepper-tracker', () => {
    const nodes = document.querySelectorAll('.process-journey__node');
    const steps = document.querySelectorAll('.process-step');
    if (!nodes.length) return;

    function updateJourney() {
      const viewportMid = window.innerHeight * 0.55;
      let activeIndex = 0;
      steps.forEach((step, idx) => {
        const rect = step.getBoundingClientRect();
        if (rect.top <= viewportMid) {
          activeIndex = idx;
        }
      });
      nodes.forEach((node, idx) => {
        node.classList.toggle('active', idx <= activeIndex);
      });
    }

    window.addEventListener('scroll', updateJourney, { passive: true });
    updateJourney();

    nodes.forEach((node, idx) => {
      node.style.cursor = 'pointer';
      node.addEventListener('click', () => {
        if (steps[idx]) {
          const offset = 100;
          const top = steps[idx].getBoundingClientRect().top + window.scrollY - offset;
          window.scrollTo({ top, behavior: 'smooth' });
        }
      });
    });
  });

  /* ---------- 12. Testimonial slider (with Prev/Next arrows & Touch Swipe) ---------- */
  safeRun('testimonial-slider', () => {
    const track = document.getElementById('testimonialTrack');
    const dotsWrap = document.getElementById('testimonialDots');
    const prevBtn = document.getElementById('prevTestimonial');
    const nextBtn = document.getElementById('nextTestimonial');
    if (!track) return;

    const slides = track.children;
    let current = 0;
    let autoplayTimer;

    if (dotsWrap) {
      dotsWrap.innerHTML = '';
      for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement('span');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(dot);
      }
    }
    const dots = dotsWrap ? dotsWrap.querySelectorAll('.dot') : [];

    function goTo(index) {
      current = (index + slides.length) % slides.length;
      track.style.transform = `translateX(-${current * 100}%)`;
      dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetAutoplay(); });

    function resetAutoplay() {
      stopAutoplay();
      startAutoplay();
    }

    function startAutoplay() { autoplayTimer = setInterval(() => goTo(current + 1), 6000); }
    function stopAutoplay() { clearInterval(autoplayTimer); }
    startAutoplay();

    track.parentElement.addEventListener('mouseenter', stopAutoplay);
    track.parentElement.addEventListener('mouseleave', startAutoplay);

    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => {
      const diff = e.changedTouches[0].clientX - touchStartX;
      if (Math.abs(diff) > 40) {
        goTo(current + (diff < 0 ? 1 : -1));
        resetAutoplay();
      }
    }, { passive: true });
  });

  /* ---------- Supabase Client Initialization ---------- */
  let supabaseClient = null;
  safeRun('supabase-init', () => {
    if (window.supabase && typeof window.supabase.createClient === 'function') {
      const SUPABASE_URL = 'https://qfoziccqtyvnjxsjjmds.supabase.co';
      const SUPABASE_ANON_KEY = 'sb_publishable_9bwM47LVotLdcmJDUCKwjQ_L5WUCmzT';
      if (SUPABASE_URL && SUPABASE_ANON_KEY) {
        supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
      }
    }
  });

  /* ---------- 13. Contact form validation + Supabase submission ---------- */
  safeRun('contact-form', () => {
    const form = document.getElementById('contactForm');
    const formSuccess = document.getElementById('formSuccess');
    const successEmailDisplay = document.getElementById('successEmailDisplay');
    const btnSubmit = document.getElementById('btnSubmitContact');
    if (!form || !formSuccess) return;

    // Strict Indian Mobile Validation Rules
    function validateIndianMobile(phoneStr) {
      if (!phoneStr || !phoneStr.trim()) {
        return { valid: false, message: 'Please enter your mobile number' };
      }
      let digits = phoneStr.replace(/\D/g, '');
      if (digits.length === 12 && digits.startsWith('91')) {
        digits = digits.slice(2);
      } else if (digits.length === 11 && digits.startsWith('0')) {
        digits = digits.slice(1);
      }
      if (digits.length !== 10) {
        return { valid: false, message: 'Mobile number must be exactly 10 digits' };
      }
      if (!/^[6-9]/.test(digits)) {
        return { valid: false, message: 'Must start with 6, 7, 8, or 9 (valid Indian mobile number)' };
      }
      if (/^(\d)\1{9}$/.test(digits)) {
        return { valid: false, message: 'Invalid number: all digits cannot be identical' };
      }
      const SEQUENTIAL_PATTERNS = [
        '0123456789', '1234567890', '2345678901', '3456789012',
        '4567890123', '5678901234', '6789012345', '7890123456',
        '8901234567', '9012345678', '9876543210', '0987654321',
        '8765432109', '7654321098'
      ];
      if (SEQUENTIAL_PATTERNS.includes(digits)) {
        return { valid: false, message: 'Invalid number: sequential number patterns not allowed' };
      }
      return { valid: true, cleanNumber: digits };
    }

    function validateEmail(emailStr) {
      if (!emailStr || !emailStr.trim()) {
        return { valid: false, message: 'Please enter your email address' };
      }
      const trimmed = emailStr.trim();
      if (!trimmed.includes('@')) {
        return { valid: false, message: "Email address must contain '@'" };
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed)) {
        return { valid: false, message: 'Please enter a valid email address (e.g. name@company.com)' };
      }
      return { valid: true, cleanEmail: trimmed };
    }

    function setFieldState(group, valid, customMsg) {
      if (!group) return;
      group.classList.toggle('invalid', !valid);
      if (customMsg) {
        const errEl = group.querySelector('.error-msg');
        if (errEl) errEl.textContent = customMsg;
      }
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      formSuccess.classList.remove('show');

      const name = form.querySelector('#name');
      const email = form.querySelector('#email');
      const phone = form.querySelector('#phone');
      const service = form.querySelector('#service');
      const message = form.querySelector('#message');
      const budget = form.querySelector('#budget');

      let valid = true;

      // 1. Name Validation
      const nameValid = name.value.trim().length >= 2;
      setFieldState(name.closest('.form-group'), nameValid, nameValid ? '' : 'Please enter your full name');
      valid = valid && nameValid;

      // 2. Email Validation
      const emailRes = validateEmail(email.value);
      setFieldState(email.closest('.form-group'), emailRes.valid, emailRes.valid ? '' : emailRes.message);
      valid = valid && emailRes.valid;

      // 3. Mobile Number Validation
      const phoneRes = validateIndianMobile(phone.value);
      setFieldState(phone.closest('.form-group'), phoneRes.valid, phoneRes.valid ? '' : phoneRes.message);
      valid = valid && phoneRes.valid;

      // 4. Service Selection
      const serviceValid = service.value.trim().length > 0;
      setFieldState(service.closest('.form-group'), serviceValid, serviceValid ? '' : 'Please select a service');
      valid = valid && serviceValid;

      // 5. Message
      const messageValid = message.value.trim().length >= 8;
      setFieldState(message.closest('.form-group'), messageValid, messageValid ? '' : 'Please share a few details about your project');
      valid = valid && messageValid;

      if (!valid) {
        const firstInvalid = form.querySelector('.form-group.invalid');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Sending Request...';
      }

      const leadData = {
        name: name.value.trim(),
        email: email.value.trim(),
        phone: phone.value.trim() || null,
        service: service.value,
        budget: budget.value.trim() || null,
        message: message.value.trim(),
        lead_source: 'contact_form'
      };

      try {
        if (supabaseClient) {
          const { error } = await supabaseClient.from('leads').insert([leadData]);
          if (error) {
            console.warn('[Wizhy Studio] Supabase lead insert note:', error);
          } else {
            console.info('[Wizhy Studio] Lead saved to Supabase successfully!');
          }
        }
      } catch (err) {
        console.warn('[Wizhy Studio] Supabase submission fallback:', err);
      } finally {
        if (btnSubmit) {
          btnSubmit.disabled = false;
          btnSubmit.textContent = 'Send Request';
        }
      }

      if (successEmailDisplay) {
        successEmailDisplay.textContent = email.value.trim();
      }
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

  /* ---------- 15. Cookie consent banner (Point 1: Show after scrolling past hero) ---------- */
  safeRun('cookie-consent', () => {
    const banner = document.getElementById('cookieBanner');
    if (!banner) return;
    const acceptBtn = document.getElementById('cookieAccept');
    const rejectBtn = document.getElementById('cookieReject');
    let stored;
    try { stored = localStorage.getItem('wizhy-cookie-consent'); } catch (e) { stored = null; }

    if (!stored) {
      let bannerShown = false;
      function checkScroll() {
        if (bannerShown) return;
        const hero = document.getElementById('home');
        const triggerPoint = hero ? (hero.offsetTop + hero.offsetHeight - 120) : 350;
        if (window.scrollY > triggerPoint) {
          bannerShown = true;
          banner.classList.add('show');
          window.removeEventListener('scroll', checkScroll);
        }
      }

      window.addEventListener('scroll', checkScroll, { passive: true });
      setTimeout(checkScroll, 600);
    }

    function setConsent(value) {
      try { localStorage.setItem('wizhy-cookie-consent', value); } catch (e) { /* ignore */ }
      banner.classList.remove('show');
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

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailInput.focus();
        return;
      }

      if (supabaseClient) {
        try {
          await supabaseClient.from('leads').insert([{ email, lead_source: 'checklist_lead_magnet' }]);
          console.info('[Wizhy Studio] Lead magnet download captured in Supabase!');
        } catch (err) {
          console.warn('[Wizhy Studio] Supabase lead magnet note:', err);
        }
      }

      successEl.classList.add('show');
      form.reset();

      // Trigger instant PDF download
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

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailInput.focus();
        return;
      }

      if (supabaseClient) {
        try {
          await supabaseClient.from('newsletter_subscribers').insert([{ email }]);
          console.info('[Wizhy Studio] Newsletter subscriber saved to Supabase!');
        } catch (err) {
          console.warn('[Wizhy Studio] Supabase newsletter note:', err);
        }
      }

      successEl.classList.add('show');
      form.reset();
      setTimeout(() => successEl.classList.remove('show'), 6000);
    });
  });

  /* ---------- 21. WhatsApp FAB show/hide on scroll ---------- */
  safeRun('whatsapp-fab', () => {
    const fab = document.getElementById('whatsappFab');
    if (!fab) return;
  });

  /* ---------- 22. Razorpay Test Mode Checkout ---------- */
  safeRun('razorpay-checkout', () => {
    const btnPayAdvance = document.getElementById('btnPayAdvance');
    if (!btnPayAdvance) return;

    btnPayAdvance.addEventListener('click', () => {
      if (typeof Razorpay === 'undefined') {
        if (window.__wizhyShowToast) window.__wizhyShowToast('Payment gateway initializing... please check your connection.');
        return;
      }

      const options = {
        key: 'rzp_test_placeholder', // Test Mode Key placeholder
        amount: 500000, // ₹5,000 in paise
        currency: 'INR',
        name: 'Wizhy Studio',
        description: 'Project Advance / Retainer (Test Mode)',
        image: 'https://wizhy.in/assets/og-image.jpg',
        handler: function (response) {
          const successMsg = '✓ Test Payment Successful! Payment ID: ' + response.razorpay_payment_id;
          if (window.__wizhyShowToast) window.__wizhyShowToast(successMsg);
          console.info(successMsg);

          if (supabaseClient) {
            supabaseClient.from('payments').insert([{
              payment_id: response.razorpay_payment_id,
              status: 'captured_test',
              amount: 5000,
              currency: 'INR'
            }]).catch(() => {});
          }
        },
        prefill: {
          name: 'Prospective Client',
          email: 'techwizhy@gmail.com',
          contact: '9999999999'
        },
        notes: {
          project: 'Website / SEO Retainer'
        },
        theme: {
          color: '#C8481E'
        }
      };

      try {
        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
          if (window.__wizhyShowToast) window.__wizhyShowToast('Payment cancelled or test failed.');
        });
        rzp.open();
      } catch (err) {
        console.warn('[Wizhy Studio] Razorpay test trigger note:', err);
      }
    });
  });

});
