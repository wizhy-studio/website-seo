// =========================================================
// WIZHY WEB STUDIO — BLOG HUB ENGINE (IMAGE 2)
// Live Search, Category Filtering, Dynamic Count & Pagination
// =========================================================

(function () {
  'use strict';

  // Make sure blog data is loaded
  if (typeof WIZHY_BLOGS === 'undefined' || !Array.isArray(WIZHY_BLOGS)) {
    console.warn('WIZHY_BLOGS data not found.');
    return;
  }

  // DOM Elements
  const searchInput = document.getElementById('blogSearchInput');
  const sortSelect = document.getElementById('blogSortSelect');
  const categoryListEl = document.getElementById('blogCategoryList');
  const featuredContainer = document.getElementById('blogFeaturedContainer');
  const gridContainer = document.getElementById('blogGridContainer');
  const paginationContainer = document.getElementById('blogPagination');

  // State
  let activeCategory = 'all';
  let searchQuery = '';
  let activeSort = 'newest';
  let currentPage = 1;
  const POSTS_PER_PAGE = 6;

  // Initialize from URL Params (e.g. blog.html?q=seo or blog.html?cat=performance)
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('q')) {
    searchQuery = urlParams.get('q').trim();
    if (searchInput) searchInput.value = searchQuery;
  }
  if (urlParams.has('category') || urlParams.has('cat')) {
    activeCategory = (urlParams.get('category') || urlParams.get('cat')).trim().toLowerCase();
  }

  // Extract all categories & calculate counts
  function getCategoryCounts() {
    const counts = { all: WIZHY_BLOGS.length };
    WIZHY_BLOGS.forEach(post => {
      const catKey = (post.category || 'General').trim();
      counts[catKey] = (counts[catKey] || 0) + 1;
    });
    return counts;
  }

  // Render Category Navigation in Left Sidebar (Image 2)
  function renderCategories() {
    if (!categoryListEl) return;
    const counts = getCategoryCounts();
    const categories = Object.keys(counts).filter(c => c !== 'all');

    let html = `
      <button type="button" class="blog-cat-item ${activeCategory === 'all' ? 'active' : ''}" data-category="all">
        <span>All Categories</span>
        <span class="blog-cat-item__count">${counts.all}</span>
      </button>
    `;

    categories.forEach(cat => {
      const isActive = activeCategory.toLowerCase() === cat.toLowerCase();
      html += `
        <button type="button" class="blog-cat-item ${isActive ? 'active' : ''}" data-category="${cat}">
          <span>${cat}</span>
          <span class="blog-cat-item__count">${counts[cat]}</span>
        </button>
      `;
    });

    categoryListEl.innerHTML = html;

    // Attach click events
    categoryListEl.querySelectorAll('.blog-cat-item').forEach(btn => {
      btn.addEventListener('click', () => {
        activeCategory = btn.getAttribute('data-category');
        currentPage = 1;
        categoryListEl.querySelectorAll('.blog-cat-item').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        render();
      });
    });
  }

  // Filter & Sort Logic
  function getFilteredPosts() {
    let list = WIZHY_BLOGS.slice();

    // 1. Category filter
    if (activeCategory && activeCategory !== 'all') {
      list = list.filter(p => (p.category || '').toLowerCase() === activeCategory.toLowerCase());
    }

    // 2. Search query filter
    if (searchQuery) {
      const q = searchQuery.toLowerCase();
      list = list.filter(p => {
        const titleMatch = (p.title || '').toLowerCase().includes(q);
        const excerptMatch = (p.excerpt || '').toLowerCase().includes(q);
        const catMatch = (p.category || '').toLowerCase().includes(q);
        return titleMatch || excerptMatch || catMatch;
      });
    }

    // 3. Sorting
    if (activeSort === 'newest') {
      list.sort((a, b) => new Date(b.date) - new Date(a.date));
    } else if (activeSort === 'oldest') {
      list.sort((a, b) => new Date(a.date) - new Date(b.date));
    } else if (activeSort === 'read-time') {
      list.sort((a, b) => parseInt(a.readTime || '5') - parseInt(b.readTime || '5'));
    }

    return list;
  }

  // Render Main Hub View
  function render() {
    const filtered = getFilteredPosts();

    // If no posts match
    if (filtered.length === 0) {
      if (featuredContainer) featuredContainer.innerHTML = '';
      if (gridContainer) {
        gridContainer.innerHTML = `
          <div class="blog-empty-state">
            <div class="blog-empty-state__icon">🔍</div>
            <h3 class="blog-empty-state__title">No articles found</h3>
            <p class="blog-empty-state__text">We couldn't find any articles matching "<strong>${escapeHtml(searchQuery || activeCategory)}</strong>".</p>
            <button type="button" class="btn btn--primary btn--sm" id="btnResetFilters">Reset Filters</button>
          </div>
        `;
        const resetBtn = document.getElementById('btnResetFilters');
        if (resetBtn) {
          resetBtn.addEventListener('click', () => {
            activeCategory = 'all';
            searchQuery = '';
            if (searchInput) searchInput.value = '';
            renderCategories();
            render();
          });
        }
      }
      if (paginationContainer) paginationContainer.innerHTML = '';
      return;
    }

    // Top: Featured Card (Matches Image 2 Featured Hero Card)
    // Find first featured post or fallback to the first post in filtered list
    let featuredPost = filtered.find(p => p.featured) || filtered[0];
    const remainingPosts = filtered.filter(p => p.id !== featuredPost.id);

    if (featuredContainer) {
      featuredContainer.innerHTML = `
        <a href="${featuredPost.slug}" class="blog-featured-card" aria-label="Read ${escapeHtml(featuredPost.title)}">
          <div class="blog-featured-card__media">
            <img class="blog-featured-card__img" src="${featuredPost.image}" alt="${escapeHtml(featuredPost.title)}" loading="lazy">
          </div>
          <div class="blog-featured-card__body">
            <div>
              <span class="blog-featured-badge">Featured • ${escapeHtml(featuredPost.category || 'Guide')}</span>
              <h2 class="blog-featured-card__title">${escapeHtml(featuredPost.title)}</h2>
              <p class="blog-featured-card__excerpt">${escapeHtml(featuredPost.excerpt)}</p>
            </div>
            <div class="blog-card-author">
              <div class="blog-card-author__profile">
                <img class="blog-card-author__avatar" src="${featuredPost.author?.avatar || 'assets/wizai-avatar-sm.webp'}" alt="${escapeHtml(featuredPost.author?.name || 'Wizhy')}">
                <div class="blog-card-author__meta">
                  <span class="blog-card-author__name">${escapeHtml(featuredPost.author?.name || 'Wizhy Studio')}</span>
                  <span class="blog-card-author__time">${featuredPost.date} • ${featuredPost.readTime}</span>
                </div>
              </div>
              <span class="blog-card-arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
              </span>
            </div>
          </div>
        </a>
      `;
    }

    // Bottom: 2-Column Grid (Matches Image 2)
    // Apply pagination to remaining posts
    const totalRemaining = remainingPosts.length;
    const totalPages = Math.ceil(totalRemaining / POSTS_PER_PAGE) || 1;
    const startIndex = (currentPage - 1) * POSTS_PER_PAGE;
    const paginatedList = remainingPosts.slice(startIndex, startIndex + POSTS_PER_PAGE);

    if (gridContainer) {
      if (paginatedList.length === 0) {
        gridContainer.innerHTML = '';
      } else {
        gridContainer.innerHTML = paginatedList.map(post => `
          <article class="blog-grid-card">
            <a href="${post.slug}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">
              <div class="blog-grid-card__media">
                <img class="blog-grid-card__img" src="${post.image}" alt="${escapeHtml(post.title)}" loading="lazy">
              </div>
              <div class="blog-grid-card__body">
                <span class="blog-grid-card__tag">${escapeHtml(post.category || 'Article')}</span>
                <h3 class="blog-grid-card__title">${escapeHtml(post.title)}</h3>
                <p class="blog-grid-card__excerpt">${escapeHtml(post.excerpt)}</p>
                <div class="blog-card-author">
                  <div class="blog-card-author__profile">
                    <img class="blog-card-author__avatar" src="${post.author?.avatar || 'assets/wizai-avatar-sm.webp'}" alt="${escapeHtml(post.author?.name || 'Wizhy')}">
                    <div class="blog-card-author__meta">
                      <span class="blog-card-author__name">${escapeHtml(post.author?.name || 'Wizhy')}</span>
                      <span class="blog-card-author__time">${post.readTime}</span>
                    </div>
                  </div>
                  <span class="blog-card-arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                  </span>
                </div>
              </div>
            </a>
          </article>
        `).join('');
      }
    }

    // Render Pagination Controls
    if (paginationContainer) {
      if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
      } else {
        let pagHtml = `
          <button type="button" class="blog-page-btn" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}" aria-label="Previous page">
            ← Prev
          </button>
        `;
        for (let i = 1; i <= totalPages; i++) {
          pagHtml += `
            <button type="button" class="blog-page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}" aria-label="Page ${i}">
              ${i}
            </button>
          `;
        }
        pagHtml += `
          <button type="button" class="blog-page-btn" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}" aria-label="Next page">
            Next →
          </button>
        `;
        paginationContainer.innerHTML = pagHtml;

        paginationContainer.querySelectorAll('.blog-page-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const pageNum = parseInt(btn.getAttribute('data-page'));
            if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages) {
              currentPage = pageNum;
              render();
              window.scrollTo({ top: featuredContainer.offsetTop - 100, behavior: 'smooth' });
            }
          });
        });
      }
    }
  }

  // Helper: Escape HTML
  function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function (m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
  }

  // Setup Event Listeners
  if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        searchQuery = e.target.value.trim();
        currentPage = 1;
        render();
      }, 200);
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', (e) => {
      activeSort = e.target.value;
      currentPage = 1;
      render();
    });
  }

  // Initial Boot
  renderCategories();
  render();

})();
