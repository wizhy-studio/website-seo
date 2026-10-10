/* =========================================================
   WIZHY WEB STUDIO (WWS) — script.js (v4.1)
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
    console.warn(`[Wizhy Web Studio] "${label}" module failed to initialize:`, err);
  }
}

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- 1. Theme toggle (light/dark/espresso/lune slider) ---------- */
  safeRun('theme-toggle', () => {
    const toggleEl = document.getElementById('themeToggle');
    if (!toggleEl) return;
    const root = document.documentElement;
    const optButtons = toggleEl.querySelectorAll('.theme-opt-btn');

    function applyTheme(theme) {
      root.setAttribute('data-theme', theme);
      try { localStorage.setItem('wizhy-theme', theme); } catch (e) { /* localStorage unavailable */ }
      optButtons.forEach(btn => {
        if (btn.getAttribute('data-set-theme') === theme) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });
    }

    // Set initial active button based on current theme
    const initialTheme = root.getAttribute('data-theme') || 'light';
    applyTheme(initialTheme);

    // Option buttons direct click
    optButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const selected = btn.getAttribute('data-set-theme');
        if (selected) {
          applyTheme(selected);
          // Collapse slider immediately back to single circle
          toggleEl.classList.remove('expanded');
          toggleEl.classList.add('just-selected');
          btn.blur();
          if (document.activeElement) document.activeElement.blur();
        }
      });
    });

    // Reset just-selected once user moves their mouse away from toggle container
    const wrapEl = toggleEl.closest('.theme-toggle-wrap') || toggleEl;
    wrapEl.addEventListener('mouseleave', () => {
      toggleEl.classList.remove('just-selected');
    });

    // Fallback click on toggle capsule (e.g. mobile tap or quick cycling)
    toggleEl.addEventListener('click', (e) => {
      if (e.target.closest('.theme-opt-btn')) return;
      toggleEl.classList.remove('just-selected');
      // On mobile or narrow screens, toggle the expanded class
      if (window.innerWidth <= 768) {
        toggleEl.classList.toggle('expanded');
      } else {
        const current = root.getAttribute('data-theme') || 'light';
        const themes = ['light', 'dark', 'espresso', 'lune'];
        const nextIdx = (themes.indexOf(current) + 1) % themes.length;
        applyTheme(themes[nextIdx]);
      }
    });

    // Close expanded on click outside (touch devices)
    document.addEventListener('click', (e) => {
      if (!toggleEl.contains(e.target)) {
        toggleEl.classList.remove('expanded');
        toggleEl.classList.remove('just-selected');
      }
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

    const sections = ['home', 'services', 'pricing', 'why-us', 'process', 'faq', 'contact']
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

    // Auto-select corresponding service option when clicking pricing / micro-service buttons
    document.querySelectorAll('[data-select-service]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const serviceName = btn.getAttribute('data-select-service');
        const serviceSelect = document.getElementById('service');
        if (serviceSelect && serviceName) {
          for (let i = 0; i < serviceSelect.options.length; i++) {
            if (serviceSelect.options[i].value === serviceName || serviceSelect.options[i].text.includes(serviceName)) {
              serviceSelect.selectedIndex = i;
              serviceSelect.dispatchEvent(new Event('change'));
              break;
            }
          }
        }
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
  /* ---------- 5. Mobile menu & Drawer ---------- */
  safeRun('mobile-menu', () => {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu') || document.getElementById('mobileDrawer');
    const drawerClose = document.getElementById('drawerClose');
    if (!hamburger) return;

    function closeMobileMenu() {
      hamburger.classList.remove('open');
      if (mobileMenu) mobileMenu.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
    }

    hamburger.addEventListener('click', () => {
      if (!mobileMenu) return;
      const isOpen = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', String(isOpen));
    });

    if (drawerClose) {
      drawerClose.addEventListener('click', closeMobileMenu);
    }

    document.querySelectorAll('.mobile-link, .drawer-link, .mobile-menu .btn, .mobile-drawer .btn').forEach(link => {
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

    const scrollTopBtn = document.getElementById('scrollTop');
    if (scrollTopBtn) {
      scrollTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
      window.addEventListener('scroll', () => {
        scrollTopBtn.classList.toggle('show', window.scrollY > 400);
      }, { passive: true });
    }
  });

  /* ---------- Legal Table of Contents ScrollSpy ---------- */
  safeRun('legal-toc', () => {
    const tocLinks = document.querySelectorAll('.legal-nav-link');
    if (!tocLinks.length) return;
    const headings = Array.from(tocLinks).map(link => {
      const id = link.getAttribute('href')?.replace('#', '');
      return id ? document.getElementById(id) : null;
    }).filter(Boolean);

    function onLegalScroll() {
      const scrollPos = window.scrollY + 140;
      let activeId = '';
      headings.forEach(h => {
        if (scrollPos >= h.offsetTop) activeId = h.id;
      });
      if (activeId) {
        tocLinks.forEach(link => {
          link.classList.toggle('active', link.getAttribute('href') === '#' + activeId);
        });
      }
    }
    window.addEventListener('scroll', onLegalScroll, { passive: true });
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

   /* ---------- 9. Hero Glass Console: Lighthouse ring animation & interactive audit ---------- */
  safeRun('hero-console-interactions', () => {
    const consoleEl = document.getElementById('heroConsole');
    const heroVisual = document.getElementById('heroVisual');
    const auditForm = document.getElementById('heroAuditForm');
    const auditInput = document.getElementById('heroAuditInput');
    const auditStatus = document.getElementById('heroAuditStatus');
    const scanFill = document.getElementById('heroScanFill');
    const scanText = document.getElementById('heroScanText');
    const lhDials = document.querySelectorAll('.hero-lh-dial');

    // 1. Subtle 3D mouse parallax tilt on desktop
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isCoarse = window.matchMedia('(pointer: coarse)').matches;
    if (consoleEl && !prefersReduced && !isCoarse) {
      const maxTilt = 4;
      consoleEl.addEventListener('mousemove', (e) => {
        const rect = consoleEl.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const rx = (0.5 - py) * maxTilt * 2;
        const ry = (px - 0.5) * maxTilt * 2;
        consoleEl.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-4px)`;
      });
      consoleEl.addEventListener('mouseleave', () => {
        consoleEl.style.transform = '';
      });
    }

    // 2. Animate Lighthouse dials when in view
    let animatedDials = false;
    function animateLhDials() {
      if (animatedDials) return;
      animatedDials = true;
      lhDials.forEach((dial, idx) => {
        const circle = dial.querySelector('.hero-lh-circle');
        const valEl = dial.querySelector('.hero-lh-val');
        if (!circle || !valEl) return;
        
        const circumference = 201.06;
        circle.style.strokeDashoffset = String(circumference);
        
        setTimeout(() => {
          circle.style.strokeDashoffset = '0';
          // Count up to 100
          const duration = 1200;
          const startTime = performance.now();
          function tick(now) {
            const p = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            valEl.textContent = Math.round(eased * 100);
            if (p < 1) requestAnimationFrame(tick);
          }
          requestAnimationFrame(tick);
        }, 150 + idx * 120);
      });
    }

    if (heroVisual && 'IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) { animateLhDials(); io.disconnect(); }
        });
      }, { threshold: 0.2 });
      io.observe(heroVisual);
    } else {
      animateLhDials();
    }

    // 3. Interactive In-Hero Audit Form
    if (auditForm && auditInput) {
      auditForm.addEventListener('submit', (e) => {
        e.preventDefault();
        let targetUrl = auditInput.value.trim();
        if (!targetUrl) {
          targetUrl = 'yourbusiness.com';
          auditInput.value = targetUrl;
        }

        if (auditStatus && scanFill && scanText) {
          auditStatus.style.display = 'block';
          scanFill.style.width = '0%';
          scanText.textContent = 'Initiating Core Web Vitals diagnostic for ' + targetUrl + '...';

          setTimeout(() => {
            scanFill.style.width = '45%';
            scanText.textContent = 'Inspecting Day-1 Schema JSON-LD & On-Page SEO...';
          }, 400);

          setTimeout(() => {
            scanFill.style.width = '85%';
            scanText.textContent = 'Evaluating Mobile Performance & Accessibility...';
          }, 850);

          setTimeout(() => {
            scanFill.style.width = '100%';
            scanText.textContent = '✓ Scan Complete! Loading full audit breakdown...';

            // Sync with main audit section
            const mainAuditInput = document.getElementById('auditUrl');
            const mainAuditForm = document.getElementById('auditForm');
            if (mainAuditInput && mainAuditForm) {
              mainAuditInput.value = targetUrl;
              const seoAuditSection = document.getElementById('seo-audit');
              if (seoAuditSection) {
                seoAuditSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => {
                  mainAuditForm.dispatchEvent(new Event('submit', { cancelable: true }));
                }, 600);
              }
            }
          }, 1300);
        }
      });
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

  /* ---------- 11. Stacking Cards Scroll Physics Animation (TND Formula) ---------- */
  safeRun('process-stacking-cards', () => {
    const cards = document.querySelectorAll('.stacking-process-cards .stack-card');
    if (!cards.length) return;

    const totalCards = cards.length;
    let isTicking = false;

    const updateCardTransforms = () => {
      if (window.innerWidth < 992) {
        cards.forEach(c => c.style.transform = '');
        isTicking = false;
        return;
      }

      cards.forEach((card, index) => {
        const rect = card.getBoundingClientRect();
        const nextCard = cards[index + 1];
        if (nextCard) {
          const nextRect = nextCard.getBoundingClientRect();
          const cardHeight = rect.height || 290;
          // Calculate overlap progress
          const overlap = Math.max(0, rect.bottom - nextRect.top);
          const progress = Math.min(1, Math.max(0, overlap / cardHeight));

          // Scale down cards smoothly below subsequent cards
          const maxScaleReduction = (totalCards - 1 - index) * 0.028;
          const currentScale = 1 - progress * maxScaleReduction;

          card.style.transform = `scale(${currentScale.toFixed(4)})`;
        } else {
          card.style.transform = 'scale(1)';
        }
      });
      isTicking = false;
    };

    window.addEventListener('scroll', () => {
      if (!isTicking) {
        window.requestAnimationFrame(updateCardTransforms);
        isTicking = true;
      }
    }, { passive: true });

    window.addEventListener('resize', () => {
      updateCardTransforms();
    }, { passive: true });

    updateCardTransforms();
  });

  /* ---------- 12. Interactive Scattered Testimonials Hover Cards ---------- */
  safeRun('testimonials-scattered', () => {
    const cards = document.querySelectorAll('.testimonial-hover-card');
    if (!cards.length) return;

    cards.forEach(card => {
      card.setAttribute('tabindex', '0');
      card.addEventListener('focus', () => {
        cards.forEach(c => c.style.zIndex = '');
        card.style.zIndex = '50';
      });
    });
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

      const btnText = btnSubmit ? btnSubmit.querySelector('span') : null;
      if (btnSubmit) {
        btnSubmit.disabled = true;
        if (btnText) btnText.textContent = 'Sending Request...';
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
          const { error } = await supabaseClient.from('contact_form_leads').insert([leadData]);
          if (error) {
            console.warn('[Wizhy Web Studio] Supabase lead insert note:', error);
          } else {
            console.info('[Wizhy Web Studio] Lead saved to contact_form_leads successfully!');
          }
        }
      } catch (err) {
        console.warn('[Wizhy Web Studio] Supabase submission fallback:', err);
      } finally {
        if (btnSubmit) {
          btnSubmit.disabled = false;
          if (btnText) btnText.textContent = 'Send Request';
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
          await supabaseClient.from('checklist_leads').insert([{ email }]);
          console.info('[Wizhy Web Studio] Lead magnet download captured in checklist_leads!');
        } catch (err) {
          console.warn('[Wizhy Web Studio] Supabase lead magnet note:', err);
        }
      }

      successEl.classList.add('show');
      form.reset();

      // Trigger instant PDF download
      const link = document.createElement('a');
      link.href = 'assets/wizhy-website-checklist.pdf';
      link.download = 'Wizhy-Web-Studio-Website-SEO-Checklist.pdf';
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
          console.info('[Wizhy Web Studio] Newsletter subscriber saved to Supabase!');
        } catch (err) {
          console.warn('[Wizhy Web Studio] Supabase newsletter note:', err);
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
        name: 'Wizhy Web Studio (WWS)',
        description: 'Project Advance / Retainer (Test Mode)',
        image: 'https://web.wizhy.in/assets/og-image.jpg',
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
        console.warn('[Wizhy Web Studio] Razorpay test trigger note:', err);
      }
    });
  });

  /* ---------- 23. 3D Rotating Spheres Engine (from contact.html) ---------- */
  safeRun('interactive-3d-spheres', () => {
    const c1 = document.getElementById('sphereCanvas1');
    if (!c1) return;

    // Generate seamless equirectangular pastel texture
    const TEX_W = 512;
    const TEX_H = 256;
    const textureCanvas = document.createElement('canvas');
    textureCanvas.width = TEX_W;
    textureCanvas.height = TEX_H;
    const texCtx = textureCanvas.getContext('2d');
    const texImgData = texCtx.createImageData(TEX_W, TEX_H);
    const texData = texImgData.data;

    for (let y = 0; y < TEX_H; y++) {
      const v = y / TEX_H;
      const tyOffset = y * TEX_W * 4;
      for (let x = 0; x < TEX_W; x++) {
        const u = x / TEX_W;
        
        const b1 = Math.sin(u * Math.PI * 6 + Math.cos(v * Math.PI * 4) * 2.2);
        const b2 = Math.sin(u * Math.PI * 10 - Math.sin(v * Math.PI * 5) * 2.6);
        const b3 = Math.cos(u * Math.PI * 4 + v * Math.PI * 8);
        const b4 = Math.sin(v * Math.PI * 12) * 0.25;
        
        const pattern = b1 * 0.45 + b2 * 0.35 + b3 * 0.2 + b4;
        const val = 0.5 + 0.5 * Math.tanh(pattern * 2.4);

        let r, g, b;
        if (val < 0.38) {
          const t = val / 0.38;
          // Deep terracotta & espresso bronze base (130, 45, 20) -> (190, 70, 30)
          r = Math.floor(130 + t * 65);
          g = Math.floor(45 + t * 30);
          b = Math.floor(20 + t * 20);
        } else if (val < 0.72) {
          const t = (val - 0.38) / 0.34;
          // Warm vibrant terracotta -> warm amber gold (195, 75, 40) -> (225, 145, 55)
          r = Math.floor(195 + t * 30);
          g = Math.floor(75 + t * 70);
          b = Math.floor(40 + t * 15);
        } else {
          const t = (val - 0.72) / 0.28;
          // Radiant luminous champagne/ivory crest highlights (225, 145, 55) -> (255, 244, 230)
          r = Math.floor(225 + t * 30);
          g = Math.floor(145 + t * 99);
          b = Math.floor(55 + t * 175);
        }

        const idx = tyOffset + (x * 4);
        texData[idx] = r;
        texData[idx + 1] = g;
        texData[idx + 2] = b;
        texData[idx + 3] = 255;
      }
    }

    class RotatingBall3D {
      constructor(canvasId, config) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        this.ctx = this.canvas.getContext('2d');
        this.tilt = config.tilt || (20 * Math.PI / 180);
        this.speed = config.speed || 0.04;
        this.angle = config.initialAngle || 0;
        this.lightDir = [-0.48, -0.58, 0.65];
        this.size = 0;
        
        const lLen = Math.sqrt(this.lightDir[0]**2 + this.lightDir[1]**2 + this.lightDir[2]**2);
        this.lightDir[0] /= lLen;
        this.lightDir[1] /= lLen;
        this.lightDir[2] /= lLen;

        this.initSize();
        window.addEventListener('resize', () => this.initSize());

        let isDragging = false;
        let lastX = 0;
        this.canvas.addEventListener('pointerdown', (e) => {
          isDragging = true;
          lastX = e.clientX;
          try { this.canvas.setPointerCapture(e.pointerId); } catch(err) {}
        });
        window.addEventListener('pointermove', (e) => {
          if (!isDragging) return;
          const dx = e.clientX - lastX;
          lastX = e.clientX;
          this.angle = (this.angle + dx * 0.004) % 1.0;
        });
        window.addEventListener('pointerup', () => { isDragging = false; });
        window.addEventListener('pointercancel', () => { isDragging = false; });
      }

      initSize() {
        if (!this.canvas) return;
        const rect = this.canvas.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const targetSize = Math.round(rect.width * dpr);
        const size = targetSize > 10 ? targetSize : Math.round(80 * dpr);
        
        if (size === this.size && this.pixels) return;
        this.size = size;
        this.canvas.width = size;
        this.canvas.height = size;
        this.imgData = this.ctx.createImageData(size, size);
        this.data = this.imgData.data;

        this.precomputeSphere();
      }

      precomputeSphere() {
        const size = this.size;
        const radius = size / 2;
        const cx = radius;
        const cy = radius;
        const cosT = Math.cos(this.tilt);
        const sinT = Math.sin(this.tilt);

        this.pixels = [];

        for (let y = 0; y < size; y++) {
          const dy = (y - cy) / radius;
          for (let x = 0; x < size; x++) {
            const dx = (x - cx) / radius;
            const dist2 = dx * dx + dy * dy;

            if (dist2 <= 1.0) {
              const dz = Math.sqrt(1.0 - dist2);
              const rx = dx * cosT - dy * sinT;
              const ry = dx * sinT + dy * cosT;
              const rz = dz;

              const uBase = (Math.atan2(rx, rz) / (2 * Math.PI) + 1.0) % 1.0;
              const v = Math.asin(Math.max(-1.0, Math.min(1.0, ry))) / Math.PI + 0.5;
              const ty = Math.floor(v * (TEX_H - 1));

              const dotL = Math.max(0.0, dx * this.lightDir[0] + dy * this.lightDir[1] + dz * this.lightDir[2]);
              const refZ = 2 * dotL * dz - this.lightDir[2];
              const spec = dotL > 0 ? Math.pow(Math.max(0.0, refZ), 15) * 115 : 0;
              const lit = 0.42 + dotL * 0.58;

              let alpha = 255;
              if (dist2 > 0.92) {
                alpha = Math.floor(255 * (1.0 - dist2) / 0.08);
              }

              const pixelIndex = (y * size + x) * 4;
              this.pixels.push({
                idx: pixelIndex,
                uBase: uBase,
                tyOffset: ty * TEX_W * 4,
                lit: lit,
                spec: spec,
                alpha: alpha
              });
            }
          }
        }
      }

      render(dt) {
        if (!this.pixels || this.pixels.length === 0) {
          this.initSize();
          if (!this.pixels || this.pixels.length === 0) return;
        }
        
        this.angle = (this.angle + this.speed * dt) % 1.0;
        const curAngle = this.angle;
        const targetData = this.data;
        const pLen = this.pixels.length;

        for (let i = 0; i < pLen; i++) {
          const p = this.pixels[i];
          const u = (p.uBase + curAngle) % 1.0;
          const tx = Math.floor(u * TEX_W);
          const texIdx = p.tyOffset + (tx * 4);

          const r = texData[texIdx];
          const g = texData[texIdx + 1];
          const b = texData[texIdx + 2];

          targetData[p.idx] = Math.min(255, Math.floor(r * p.lit + p.spec));
          targetData[p.idx + 1] = Math.min(255, Math.floor(g * p.lit + p.spec));
          targetData[p.idx + 2] = Math.min(255, Math.floor(b * p.lit + p.spec));
          targetData[p.idx + 3] = p.alpha;
        }

        this.ctx.putImageData(this.imgData, 0, 0);
      }
    }

    const sphere1 = new RotatingBall3D('sphereCanvas1', {
      tilt: 24 * Math.PI / 180,
      speed: 0.16,
      initialAngle: 0.1
    });

    const sphere2 = new RotatingBall3D('sphereCanvas2', {
      tilt: 18 * Math.PI / 180,
      speed: 0.22,
      initialAngle: 0.45
    });

    const sphere3 = new RotatingBall3D('sphereCanvas3', {
      tilt: 28 * Math.PI / 180,
      speed: 0.28,
      initialAngle: 0.8
    });

    let lastTime = performance.now();
    function animate(now) {
      const dt = Math.min((now - lastTime) / 1000, 0.1);
      lastTime = now;

      if (sphere1) sphere1.render(dt);
      if (sphere2) sphere2.render(dt);
      if (sphere3) sphere3.render(dt);

      requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);
  });

  /* ---------- 25. Wizhy Studio AI Chat Assistant ---------- */
  safeRun('wizhy-ai-chat-widget', () => {
    const fab = document.getElementById('wizhyChatFab');
    const win = document.getElementById('wizhyChatWindow');
    const closeBtn = document.getElementById('wizhyChatClose');
    const messagesEl = document.getElementById('wizhyChatMessages');
    const form = document.getElementById('wizhyChatForm');
    const input = document.getElementById('wizhyChatInput');
    const suggestions = document.getElementById('wizhyChatSuggestions');

    if (!fab || !win || !form || !input || !messagesEl) return;

    const chatHistory = [];

    function scrollToBottom() {
      messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function toggleChat(open) {
      const isOpen = open !== undefined ? open : !win.classList.contains('open');
      win.classList.toggle('open', isOpen);
      fab.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      win.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      if (isOpen) {
        setTimeout(() => input.focus(), 250);
        scrollToBottom();
      }
    }

    fab.addEventListener('click', () => toggleChat());
    if (closeBtn) closeBtn.addEventListener('click', () => toggleChat(false));

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && win.classList.contains('open')) {
        toggleChat(false);
      }
    });

    function formatText(text) {
      if (!text) return '';
      // Escape HTML
      const escaped = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
      // Format bold (**text** or __text__)
      const bolded = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
      // Format newlines
      return bolded.replace(/\n/g, '<br>');
    }

    function appendMessage(sender, text) {
      const msgDiv = document.createElement('div');
      msgDiv.className = `wizhy-msg wizhy-msg--${sender}`;
      
      const bubble = document.createElement('div');
      bubble.className = 'wizhy-msg__bubble';
      bubble.innerHTML = formatText(text);
      msgDiv.appendChild(bubble);

      const timeSpan = document.createElement('span');
      timeSpan.className = 'wizhy-msg__time';
      const now = new Date();
      timeSpan.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      msgDiv.appendChild(timeSpan);

      messagesEl.appendChild(msgDiv);
      scrollToBottom();
      return msgDiv;
    }

    function showTypingIndicator() {
      const typingDiv = document.createElement('div');
      typingDiv.className = 'wizhy-msg wizhy-msg--bot wizhy-typing-msg';
      typingDiv.innerHTML = `
        <div class="wizhy-typing-indicator">
          <span class="wizhy-typing-dot"></span>
          <span class="wizhy-typing-dot"></span>
          <span class="wizhy-typing-dot"></span>
        </div>
      `;
      messagesEl.appendChild(typingDiv);
      scrollToBottom();
      return typingDiv;
    }

    // Direct leads auto-capture to Supabase
    async function captureLeadFromChat(userText) {
      try {
        const phoneMatch = userText.match(/[6-9]\d{9}/);
        const emailMatch = userText.match(/[\w.-]+@[\w.-]+\.[a-zA-Z]{2,}/);

        if ((phoneMatch || emailMatch) && supabaseClient) {
          await supabaseClient.from('leads').insert([{
            phone: phoneMatch ? phoneMatch[0] : null,
            email: emailMatch ? emailMatch[0] : null,
            message: `[AI Chat Lead]: ${userText}`,
            lead_source: 'ai_chat_assistant'
          }]);
          console.info('[Wizhy Studio] AI Chat lead captured directly to Supabase!');
        }
      } catch (err) {
        console.warn('[Wizhy Studio] AI Chat lead capture note:', err);
      }
    }

    async function handleSendMessage(text) {
      const cleanText = text.trim();
      if (!cleanText) return;

      appendMessage('user', cleanText);
      chatHistory.push({ sender: 'user', text: cleanText });
      input.value = '';

      // Check if lead info was submitted
      captureLeadFromChat(cleanText);

      const typingEl = showTypingIndicator();

      const isLocalHost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
      let endpoint;
      if (isLocalHost) {
        endpoint = window.location.port === '3000' ? '/api/chat' : 'http://localhost:3000/api/chat';
      } else if (window.location.protocol === 'file:') {
        endpoint = 'http://localhost:3000/api/chat';
      } else {
        endpoint = 'https://qfoziccqtyvnjxsjjmds.supabase.co/functions/v1/wizhy-chat';
      }

      try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 18000);

        const res = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ messages: chatHistory }),
          signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!res.ok) {
          const errData = await res.json().catch(() => ({}));
          throw new Error(errData.error || `HTTP ${res.status}`);
        }

        const data = await res.json();
        const reply = data.reply || "I'm here to help! Could you please share a few more details about your project?";

        if (typingEl && typingEl.parentNode) typingEl.remove();
        appendMessage('bot', reply);
        chatHistory.push({ sender: 'model', text: reply });
      } catch (err) {
        if (typingEl && typingEl.parentNode) typingEl.remove();
        console.warn('[Wizhy AI Chat] Fetch error:', err);
        appendMessage('bot', "I'm temporarily experiencing high demand. Please WhatsApp us directly using the green WhatsApp button on the bottom left, or try again in a moment!");
      }
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      handleSendMessage(input.value);
    });

    if (suggestions) {
      suggestions.addEventListener('click', (e) => {
        const chip = e.target.closest('.wizhy-chat-chip');
        if (!chip) return;
        const prompt = chip.getAttribute('data-prompt');
        if (prompt) {
          handleSendMessage(prompt);
        }
      });
    }
  });

});
