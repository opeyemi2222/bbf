/**
 * BBF MAIN.JS — Homepage Logic & Shared Site Utilities
 * ============================================================
 * Responsibilities:
 *   1. Navbar — sticky scroll behavior + offcanvas mobile menu
 *   2. Scroll reveal animations (IntersectionObserver)
 *   3. Animated stat counters
 *   4. Render: Core Values (from data.js)
 *   5. Render: Benefits / Why Join BBF (from data.js)
 *   6. Render: Leadership preview cards (from data.js)
 *   7. Render: Events preview (from data.js)
 *   8. Render: News preview cards (from data.js)
 *   9. Render: Gallery teaser (from data.js)
 *  10. Smooth scroll for anchor links
 *  11. Footer newsletter form (simulated)
 *  12. Active nav link detection
 *  13. Hero background parallax (subtle)
 *  14. Utility helpers
 * ============================================================
 */

'use strict';

/* ─── UTILITY HELPERS ───────────────────────────────────────────────────────── */

/**
 * Safely query a single DOM element.
 * @param {string} selector
 * @param {Element} [context=document]
 */
const $ = (selector, context = document) => context.querySelector(selector);

/**
 * Safely query all matching DOM elements.
 * @param {string} selector
 * @param {Element} [context=document]
 */
const $$ = (selector, context = document) => [...context.querySelectorAll(selector)];

/**
 * Format a date string to readable Nigerian format: "15 Oct 2026"
 * @param {string} dateStr  ISO date string "YYYY-MM-DD"
 */
function formatDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' });
}

/**
 * Get abbreviated month from date string.
 * @param {string} dateStr
 */
function getMonth(dateStr) {
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-NG', { month: 'short' }).toUpperCase();
}

/**
 * Get day number from date string.
 * @param {string} dateStr
 */
function getDay(dateStr) {
  return new Date(dateStr + 'T00:00:00').getDate();
}

/**
 * Return the CSS tag class for an event category.
 * @param {string} category
 */
function getCategoryTagClass(category) {
  const map = {
    'Conference':       'tag-conference',
    'Leadership':       'tag-leadership',
    'Fellowship':       'tag-fellowship',
    'Training':         'tag-training',
    'Youth':            'tag-youth',
    'Community Service':'tag-community',
    'Outreach':         'tag-outreach',
  };
  return map[category] || 'tag-conference';
}

/**
 * Truncate a string to a given word count.
 * @param {string} str
 * @param {number} words
 */
function truncateWords(str, words) {
  const arr = str.split(' ');
  return arr.length <= words ? str : arr.slice(0, words).join(' ') + '…';
}

/**
 * Escape HTML to prevent XSS when inserting user-originated data.
 * @param {string} str
 */
function escapeHTML(str) {
  const div = document.createElement('div');
  div.appendChild(document.createTextNode(str));
  return div.innerHTML;
}


/* ─── 1. NAVBAR ─────────────────────────────────────────────────────────────── */

function initNavbar() {
  const navbar   = $('#bbf-navbar');
  const toggle   = $('#nav-toggle');
  const offcanvas = $('#bbf-offcanvas');
  const overlay  = $('#offcanvas-overlay');
  const closeBtn = $('#offcanvas-close');

  if (!navbar) return;

  // ── Scroll behavior
  let lastScroll = 0;
  const SCROLL_THRESHOLD = 60;

  function handleNavScroll() {
    const currentScroll = window.scrollY;

    if (currentScroll > SCROLL_THRESHOLD) {
      navbar.classList.add('nav-scrolled');
      navbar.classList.remove('nav-transparent');
    } else {
      navbar.classList.remove('nav-scrolled');
      navbar.classList.add('nav-transparent');
    }

    lastScroll = currentScroll;
  }

  // Set initial state
  handleNavScroll();
  window.addEventListener('scroll', handleNavScroll, { passive: true });

  // ── Offcanvas open
  function openOffcanvas() {
    offcanvas?.classList.add('open');
    overlay?.classList.add('active');
    toggle?.classList.add('active');
    document.body.style.overflow = 'hidden';
    // Move focus into offcanvas for accessibility
    offcanvas?.querySelector('a, button')?.focus();
  }

  // ── Offcanvas close
  function closeOffcanvas() {
    offcanvas?.classList.remove('open');
    overlay?.classList.remove('active');
    toggle?.classList.remove('active');
    document.body.style.overflow = '';
    toggle?.focus();
  }

  toggle?.addEventListener('click', () => {
    offcanvas?.classList.contains('open') ? closeOffcanvas() : openOffcanvas();
  });

  closeBtn?.addEventListener('click', closeOffcanvas);
  overlay?.addEventListener('click', closeOffcanvas);

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && offcanvas?.classList.contains('open')) {
      closeOffcanvas();
    }
  });

  // Close offcanvas when a nav link is clicked
  $$('.offcanvas-nav a').forEach(link => {
    link.addEventListener('click', closeOffcanvas);
  });

  // ── Active nav link based on current page
  setActiveNavLink();
}

/**
 * Highlight the nav link matching the current page URL.
 */
function setActiveNavLink() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';

  $$('.nav-links a, .offcanvas-nav a').forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    const linkPage = href.split('/').pop();
    if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.html')) {
      link.classList.add('active');
    }
  });
}


/* ─── 2. SCROLL REVEAL ──────────────────────────────────────────────────────── */

function initScrollReveal() {
  const revealEls = $$('.reveal, .reveal-left, .reveal-right');

  if (!revealEls.length) return;

  // Use IntersectionObserver for performance
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        // Unobserve after reveal — no need to re-trigger
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px',
  });

  revealEls.forEach(el => observer.observe(el));
}


/* ─── 3. ANIMATED STAT COUNTERS ─────────────────────────────────────────────── */

/**
 * Animate a number from 0 to target using easeOutExpo.
 * @param {Element} el       The element to update
 * @param {number}  target   End value
 * @param {string}  suffix   e.g. "+"
 * @param {number}  duration ms
 */
function animateCounter(el, target, suffix = '', duration = 2000) {
  let startTime = null;

  function easeOutExpo(t) {
    return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
  }

  function step(timestamp) {
    if (!startTime) startTime = timestamp;
    const elapsed = timestamp - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easedProgress = easeOutExpo(progress);
    const current = Math.round(easedProgress * target);

    el.textContent = current.toLocaleString('en-NG');

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      el.textContent = target.toLocaleString('en-NG');
      // Append suffix span after final number
      const suffixSpan = el.nextElementSibling;
      if (suffixSpan && suffixSpan.classList.contains('suffix')) {
        suffixSpan.style.opacity = '1';
      }
    }
  }

  requestAnimationFrame(step);
}

function initCounters() {
  const statsSection = $('#stats-section');
  if (!statsSection) return;

  let hasAnimated = false;

  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !hasAnimated) {
      hasAnimated = true;

      // TODO [BACKEND]: Replace BBF_STATS with data fetched from GET /api/stats
      BBF_STATS.forEach(stat => {
        const numEl = $(`#${stat.id} .stat-count-num`);
        if (numEl) {
          animateCounter(numEl, stat.value, stat.suffix, 2200);
        }
      });

      observer.disconnect();
    }
  }, { threshold: 0.3 });

  observer.observe(statsSection);
}


/* ─── 4. RENDER: STATS SECTION ──────────────────────────────────────────────── */

function renderStats() {
  const grid = $('#stats-grid');
  if (!grid) return;

  // TODO [BACKEND]: Replace BBF_STATS with GET /api/stats response
  grid.innerHTML = BBF_STATS.map(stat => `
    <div class="stat-card reveal reveal-delay-${BBF_STATS.indexOf(stat) + 1}" id="${escapeHTML(stat.id)}">
      <div class="stat-icon">
        <i class="fas ${escapeHTML(stat.icon)}"></i>
      </div>
      <div class="stat-number">
        <span class="stat-count-num">0</span><span class="suffix" style="opacity:0">${escapeHTML(stat.suffix)}</span>
      </div>
      <div class="stat-label">${escapeHTML(stat.label)}</div>
    </div>
  `).join('');
}


/* ─── 5. RENDER: CORE VALUES ─────────────────────────────────────────────────── */

function renderValues() {
  const grid = $('#values-grid');
  if (!grid) return;

  // TODO [BACKEND]: Replace BBF_VALUES with GET /api/config/values if dynamic
  grid.innerHTML = BBF_VALUES.map((val, i) => `
    <div class="value-card reveal reveal-delay-${Math.min(i + 1, 8)}">
      <div class="value-icon">
        <i class="fas ${escapeHTML(val.icon)}"></i>
      </div>
      <div class="value-title">${escapeHTML(val.title)}</div>
      <p class="value-desc">${escapeHTML(val.description)}</p>
    </div>
  `).join('');
}


/* ─── 6. RENDER: BENEFITS (WHY JOIN BBF) ─────────────────────────────────────── */

function renderBenefits() {
  const grid = $('#benefits-grid');
  if (!grid) return;

  // TODO [BACKEND]: Replace BBF_BENEFITS with GET /api/config/benefits if dynamic
  grid.innerHTML = BBF_BENEFITS.map((benefit, i) => `
    <div class="benefit-card reveal reveal-delay-${Math.min(i + 1, 8)}">
      <div class="benefit-icon">
        <i class="fas ${escapeHTML(benefit.icon)}"></i>
      </div>
      <div class="benefit-title">${escapeHTML(benefit.title)}</div>
      <p class="benefit-desc">${escapeHTML(benefit.description)}</p>
    </div>
  `).join('');
}


/* ─── 7. RENDER: LEADERSHIP PREVIEW ─────────────────────────────────────────── */

function renderLeadershipPreview() {
  const grid = $('#leaders-preview-grid');
  if (!grid) return;

  // Show only featured leaders (max 3 for homepage)
  // TODO [BACKEND]: Replace with GET /api/leaders?featured=true&limit=3
  const featured = BBF_LEADERS.filter(l => l.featured).slice(0, 3);

  grid.innerHTML = featured.map((leader, i) => `
    <div class="leader-card reveal reveal-delay-${i + 1}">
      <div class="leader-photo-wrap">
        <img
          src="${escapeHTML(leader.photo)}"
          alt="Photo of ${escapeHTML(leader.name)}"
          onerror="this.parentElement.classList.add('img-placeholder'); this.style.display='none'; this.parentElement.innerHTML += '<i class=\\'fas fa-user-tie\\'></i>'"
          loading="lazy"
        />
        <div class="leader-photo-overlay">
          <a href="leadership.html#leader-${leader.id}">
            <i class="fas fa-arrow-right"></i> View Profile
          </a>
        </div>
      </div>
      <div class="leader-body">
        <div class="leader-title-tag">${escapeHTML(leader.title)}</div>
        <div class="leader-name">${escapeHTML(leader.name)}</div>
        <div class="leader-state">
          <i class="fas fa-map-marker-alt"></i>
          ${escapeHTML(leader.state)}
        </div>
      </div>
    </div>
  `).join('');
}


/* ─── 8. RENDER: EVENTS PREVIEW ──────────────────────────────────────────────── */

function renderEventsPreview() {
  const featuredWrap = $('#event-featured-wrap');
  const sideList     = $('#event-side-list');
  if (!featuredWrap || !sideList) return;

  // TODO [BACKEND]: Replace with GET /api/events?status=upcoming&limit=4
  const upcoming = BBF_EVENTS.filter(e => e.status === 'upcoming');
  const featured = upcoming.find(e => e.featured) || upcoming[0];
  const sideEvents = upcoming.filter(e => e.id !== featured?.id).slice(0, 3);

  if (!featured) {
    featuredWrap.innerHTML = '<p class="text-muted-bbf">No upcoming events at this time.</p>';
    return;
  }

  // ── Featured large card
  featuredWrap.innerHTML = `
    <div class="event-card-featured reveal-left">
      <div class="event-card-featured-img" style="background-image:url('${escapeHTML(featured.image)}')"></div>
      <div class="event-card-featured-body">
        <span class="event-category-tag ${getCategoryTagClass(featured.category)}">
          ${escapeHTML(featured.category)}
        </span>
        <div class="event-card-featured-title">${escapeHTML(featured.title)}</div>
        <div class="event-meta">
          <div class="event-meta-item">
            <i class="fas fa-calendar-alt"></i>
            ${formatDate(featured.date)}
          </div>
          <div class="event-meta-item">
            <i class="fas fa-map-marker-alt"></i>
            ${escapeHTML(featured.location)}
          </div>
        </div>
        <p style="color:rgba(255,255,255,0.65);font-size:0.875rem;line-height:1.65;margin-bottom:1.5rem;">
          ${escapeHTML(truncateWords(featured.description, 25))}
        </p>
        <a href="${escapeHTML(featured.registrationLink)}" class="btn-bbf-primary btn-sm-bbf">
          <i class="fas fa-ticket-alt"></i> Register Now
        </a>
      </div>
    </div>
  `;

  // ── Side mini event cards
  sideList.innerHTML = sideEvents.map((ev, i) => `
    <div class="event-card-mini reveal-right reveal-delay-${i + 1}">
      <div class="event-date-badge">
        <span class="day">${getDay(ev.date)}</span>
        <span class="month">${getMonth(ev.date)}</span>
      </div>
      <div class="event-card-mini-body">
        <span class="event-category-tag ${getCategoryTagClass(ev.category)}" style="font-size:0.6rem;padding:0.15rem 0.6rem;margin-bottom:0.4rem;display:inline-block;">
          ${escapeHTML(ev.category)}
        </span>
        <div class="event-mini-title">${escapeHTML(ev.title)}</div>
        <div class="event-mini-location">
          <i class="fas fa-map-marker-alt"></i>
          ${escapeHTML(ev.location)}
        </div>
      </div>
    </div>
  `).join('');
}


/* ─── 9. RENDER: NEWS PREVIEW ────────────────────────────────────────────────── */

function renderNewsPreview() {
  const grid = $('#news-preview-grid');
  if (!grid) return;

  // TODO [BACKEND]: Replace with GET /api/news?limit=3&sort=date_desc
  const latest = [...BBF_NEWS]
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 3);

  grid.innerHTML = latest.map((item, i) => `
    <article class="news-card reveal reveal-delay-${i + 1}">
      <div class="news-card-img-wrap">
        <div class="img-placeholder" style="height:200px;">
          <i class="fas fa-newspaper" style="font-size:2.5rem;opacity:0.2;"></i>
        </div>
        <div class="news-card-category">
          <span class="badge-gold">${escapeHTML(item.category)}</span>
        </div>
      </div>
      <div class="news-card-body">
        <div class="news-date">
          <i class="fas fa-clock"></i>
          ${formatDate(item.date)}
        </div>
        <h3 class="news-title">
          <a href="news.html#article-${item.id}">${escapeHTML(item.title)}</a>
        </h3>
        <p class="news-summary">${escapeHTML(truncateWords(item.summary, 28))}</p>
        <a href="news.html#article-${item.id}" class="news-read-more">
          Read More <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </article>
  `).join('');
}


/* ─── 10. RENDER: GALLERY TEASER ─────────────────────────────────────────────── */

function renderGalleryTeaser() {
  const mosaic = $('#gallery-mosaic');
  if (!mosaic) return;

  // Show 6 items for the mosaic
  // TODO [BACKEND]: Replace with GET /api/gallery?limit=6
  const items = BBF_GALLERY.slice(0, 6);

  mosaic.innerHTML = items.map(item => `
    <div class="gallery-item" role="button" tabindex="0"
         aria-label="Gallery photo: ${escapeHTML(item.title)}"
         onclick="window.location='gallery.html'"
         onkeydown="if(event.key==='Enter')window.location='gallery.html'">
      <div class="img-placeholder" style="width:100%;height:100%;">
        <i class="fas fa-image" style="font-size:2rem;opacity:0.15;"></i>
      </div>
      <div class="gallery-item-overlay">
        <div class="gallery-item-caption">${escapeHTML(item.title)}</div>
      </div>
    </div>
  `).join('');
}


/* ─── 11. FOOTER NEWSLETTER FORM ────────────────────────────────────────────── */

function initNewsletterForm() {
  const form  = $('#newsletter-form');
  const input = $('#newsletter-email');
  const msg   = $('#newsletter-msg');

  if (!form) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const email = input?.value.trim();

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showNewsletterMessage('Please enter a valid email address.', 'error');
      return;
    }

    // POST /api/newsletter.php
    fetch('api/newsletter.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email })
    })
    .then(r => r.json())
    .then(res => {
      input.value = '';
      showNewsletterMessage(res.message || 'Thank you! You\'re subscribed to BBF updates.', 'success');
    })
    .catch(() => {
      showNewsletterMessage('Could not subscribe. Please try again later.', 'error');
    });
  });

  function showNewsletterMessage(text, type) {
    if (!msg) return;
    msg.textContent = text;
    msg.style.cssText = `
      display: block;
      margin-top: 0.5rem;
      font-size: 0.75rem;
      color: ${type === 'success' ? '#4CC88A' : '#F07080'};
      font-family: var(--font-body);
    `;
    setTimeout(() => { msg.textContent = ''; msg.style.display = 'none'; }, 5000);
  }
}


/* ─── 12. SMOOTH SCROLL FOR ANCHOR LINKS ─────────────────────────────────────── */

function initSmoothScroll() {
  $$('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const targetId = anchor.getAttribute('href');
      if (targetId === '#') return;

      const target = $(targetId);
      if (!target) return;

      e.preventDefault();
      const navHeight = parseInt(getComputedStyle(document.documentElement)
        .getPropertyValue('--navbar-height')) || 76;

      const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}


/* ─── 13. HERO PARALLAX (subtle) ─────────────────────────────────────────────── */

function initHeroParallax() {
  const heroBg = $('.hero-bg');
  if (!heroBg) return;

  // Only enable on devices that can handle it
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) return;

  // Trigger the subtle zoom-out on load
  setTimeout(() => heroBg.classList.add('loaded'), 100);

  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const heroHeight = heroBg.closest('.hero')?.offsetHeight || window.innerHeight;
    if (scrolled > heroHeight) return; // No-op once hero is out of view

    const shift = scrolled * 0.3;
    heroBg.style.transform = `scale(1) translateY(${shift}px)`;
  }, { passive: true });
}


/* ─── 14. YEAR IN FOOTER ─────────────────────────────────────────────────────── */

function setFooterYear() {
  const el = $('#footer-year');
  if (el) el.textContent = new Date().getFullYear();
}


/* ─── 15. BACK TO TOP BUTTON ─────────────────────────────────────────────────── */

function initBackToTop() {
  const btn = $('#back-to-top');
  if (!btn) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 400) {
      btn.style.opacity = '1';
      btn.style.pointerEvents = 'auto';
      btn.style.transform = 'translateY(0)';
    } else {
      btn.style.opacity = '0';
      btn.style.pointerEvents = 'none';
      btn.style.transform = 'translateY(10px)';
    }
  }, { passive: true });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}


/* ─── 16. TOOLTIP INIT (Bootstrap) ───────────────────────────────────────────── */

function initTooltips() {
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    $$('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }
}


/* ─── 17. PRELOADER ───────────────────────────────────────────────────────────── */

function initPreloader() {
  const preloader = $('#bbf-preloader');
  if (!preloader) return;

  window.addEventListener('load', () => {
    preloader.style.transition = 'opacity 0.5s ease';
    preloader.style.opacity = '0';
    setTimeout(() => {
      preloader.style.display = 'none';
    }, 500);
  });
}


/* ─── DOM READY — BOOTSTRAP ALL MODULES ─────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {

  // ── Core site utilities (run on every page)
  initNavbar();
  initScrollReveal();
  initSmoothScroll();
  setFooterYear();
  initBackToTop();
  initTooltips();
  initPreloader();

  // ── Homepage-specific renders (guarded by element presence)
  renderStats();
  initCounters();        // must run after renderStats()
  renderValues();
  renderBenefits();
  renderLeadershipPreview();
  renderEventsPreview();
  renderNewsPreview();
  renderGalleryTeaser();

  // ── Shared across pages
  initNewsletterForm();
  initHeroParallax();

  // Re-run scroll reveal after dynamic content is injected
  // (elements added by render functions need to be observed)
  setTimeout(initScrollReveal, 50);
});
