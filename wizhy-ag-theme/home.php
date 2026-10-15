<?php
/**
 * Template Name: Blog Hub (Image 2 Layout)
 *
 * Dedicated blog archive page displaying categories, search,
 * featured hero post, and 2-column post grid.
 *
 * @package Wizhy_AG_Theme
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
$current_category = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// 1. Get all published categories
$categories = get_categories(array(
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
));
$total_posts_count = wp_count_posts()->publish;

// 2. Query Featured Post (Newest or Sticky)
$featured_args = array(
    'posts_per_page'      => 1,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
);
if (!empty($current_category)) {
    $featured_args['category_name'] = $current_category;
}
$featured_query = new WP_Query($featured_args);
$featured_id = 0;
if ($featured_query->have_posts()) {
    $featured_query->the_post();
    $featured_id = get_the_ID();
    $featured_query->rewind_posts();
}

// 3. Query Grid Posts (Paginated, excluding featured)
$grid_args = array(
    'posts_per_page' => 6,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'post__not_in'   => $featured_id ? array($featured_id) : array(),
);
if (!empty($current_category)) {
    $grid_args['category_name'] = $current_category;
}
if (!empty($search_query)) {
    $grid_args['s'] = $search_query;
}
$grid_query = new WP_Query($grid_args);

$default_img = get_template_directory_uri() . '/assets/og-image.webp';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog &amp; Resources | Wizhy Web Studio</title>
  <meta name="description" content="Explore actionable guides on web speed, technical SEO, and conversion-focused web design.">
  <link rel="canonical" href="<?php echo esc_url(home_url('/blog/')); ?>">

  <!-- Fonts & Theme Stylesheets -->
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/fonts.css">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.min.css?v=5.0">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/chat.css">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/blog.css">

  <script>
    (function () {
      try {
        var stored = localStorage.getItem('wizhy-theme');
        var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
      } catch (e) { document.documentElement.setAttribute('data-theme', 'light'); }
    })();
  </script>
  <?php wp_head(); ?>
</head>
<body <?php body_class('blog-hub-page'); ?>>

<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="scroll-progress" id="scrollProgress"></div>

<!-- ============ NAVBAR ============ -->
<header class="navbar" id="navbar">
  <div class="container navbar__inner">
    <nav class="nav-pill" id="navPill" aria-label="Primary Navigation">
      <a href="<?php echo home_url('/'); ?>" class="nav-link">Home</a>
      <a href="<?php echo home_url('/#services'); ?>" class="nav-link">Services</a>
      <a href="<?php echo home_url('/#pricing'); ?>" class="nav-link">Pricing</a>
      <a href="<?php echo home_url('/#why-us'); ?>" class="nav-link">Why Us</a>
      <a href="<?php echo home_url('/blog/'); ?>" class="nav-link active">Blog</a>
      <a href="<?php echo home_url('/contact/'); ?>" class="nav-link">Contact</a>
    </nav>

    <a href="<?php echo home_url('/'); ?>" class="brand brand--center" aria-label="Wizhy Web Studio">
      <span class="brand__mark">
        <picture>
          <source srcset="<?php echo get_template_directory_uri(); ?>/assets/wizhy-studio-logo-sm.avif?v=1" type="image/avif">
          <source srcset="<?php echo get_template_directory_uri(); ?>/assets/wizhy-studio-logo-sm.webp?v=1" type="image/webp">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/wizhy-studio-logo-sm.png" alt="Wizhy Logo" class="brand__logo" width="38" height="38">
        </picture>
      </span>
      <span class="brand__text">Wizhy<em> Web Studio</em></span>
    </a>

    <div class="navbar__actions">
      <a href="<?php echo home_url('/#seo-audit'); ?>" class="nav-audit-pill btn btn--primary btn--sm">
        <span>Free SEO Audit</span>
        <span class="btn__arrow">⚡</span>
      </a>

      <!-- Theme Switcher -->
      <div class="theme-toggle-wrap">
        <div class="theme-toggle" id="themeToggle" role="region" aria-label="Theme selector">
          <div class="theme-toggle__active-icon" aria-hidden="true">
            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"/></svg>
            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.5 14.5A8.5 8.5 0 1 1 9.5 3.5a7 7 0 0 0 11 11Z"/></svg>
            <svg class="icon-coffee" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
            <svg class="icon-icecream" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 11c0-2.8 2.2-5 5-5s5 2.2 5 5a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"/><path d="m8.5 13 3.5 8 3.5-8"/></svg>
          </div>
          <div class="theme-toggle__options" role="radiogroup" aria-label="Select theme">
            <button class="theme-opt-btn" type="button" data-set-theme="light" title="Light" aria-label="Light"></button>
            <button class="theme-opt-btn" type="button" data-set-theme="dark" title="Dark" aria-label="Dark"></button>
            <button class="theme-opt-btn" type="button" data-set-theme="espresso" title="Espresso" aria-label="Espresso"></button>
            <button class="theme-opt-btn" type="button" data-set-theme="lune" title="Luné" aria-label="Luné"></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ============ MAIN CONTENT ============ -->
<main id="main-content">

  <!-- Hero Header (Matches Image 2) -->
  <header class="blog-hub-header">
    <div class="container">
      <span class="blog-hub-header__badge">Read Our Blog</span>
      <h1 class="blog-hub-header__title">Browse Our Resources</h1>
      <p class="blog-hub-header__subtitle">We provide tips, technical speed benchmarks, and growth frameworks from industry specialists.</p>
    </div>
  </header>

  <!-- 2-Column Hub Layout (Matches Image 2) -->
  <div class="container">
    <div class="blog-hub-layout">
      
      <!-- LEFT SIDEBAR: Search & Dynamic Categories -->
      <aside class="blog-hub-sidebar" id="blogSidebar">
        
        <!-- Search Box -->
        <div class="blog-sidebar-widget">
          <label for="wpBlogSearch" class="blog-sidebar-label">Search Article</label>
          <form role="search" method="get" action="<?php echo esc_url(home_url('/blog/')); ?>">
            <div class="blog-search-box">
              <svg class="blog-search-box__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" id="wpBlogSearch" name="s" class="blog-search-box__input" placeholder="Search keywords..." value="<?php echo esc_attr($search_query); ?>" autocomplete="off">
            </div>
          </form>
        </div>

        <!-- Browse By Categories (Dynamically Pulled from WordPress) -->
        <div class="blog-sidebar-widget">
          <span class="blog-sidebar-label">Browse By Categories</span>
          <nav class="blog-cat-list" aria-label="Blog categories">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="blog-cat-item <?php echo empty($current_category) ? 'active' : ''; ?>">
              <span>All Categories</span>
              <span class="blog-cat-item__count"><?php echo esc_html($total_posts_count); ?></span>
            </a>
            <?php foreach ($categories as $cat) : 
              $is_active = ($current_category === $cat->slug);
            ?>
              <a href="<?php echo esc_url(add_query_arg('cat', $cat->slug, home_url('/blog/'))); ?>" class="blog-cat-item <?php echo $is_active ? 'active' : ''; ?>">
                <span><?php echo esc_html($cat->name); ?></span>
                <span class="blog-cat-item__count"><?php echo esc_html($cat->count); ?></span>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>

      </aside>

      <!-- RIGHT MAIN CONTENT: Featured Post Hero + 2-Column Grid -->
      <section class="blog-hub-main" aria-label="Blog Articles">
        
        <!-- Top: Large Featured Article Card (Matches Image 2 Hero Card) -->
        <?php if ($featured_query->have_posts()) : while ($featured_query->have_posts()) : $featured_query->the_post(); 
          $f_cats = get_the_category();
          $f_cat_name = !empty($f_cats) ? $f_cats[0]->name : 'Featured';
          $f_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : $default_img;
          $f_words = str_word_count(strip_tags(get_the_content()));
          $f_read_time = max(1, round($f_words / 200)) . ' min read';
          $f_author_avatar = get_avatar_url(get_the_author_meta('ID'), array('size' => 72));
        ?>
          <a href="<?php the_permalink(); ?>" class="blog-featured-card" aria-label="Read <?php the_title_attribute(); ?>">
            <div class="blog-featured-card__media">
              <img class="blog-featured-card__img" src="<?php echo esc_url($f_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="eager" width="560" height="340">
            </div>
            <div class="blog-featured-card__body">
              <div>
                <span class="blog-featured-badge">Featured • <?php echo esc_html($f_cat_name); ?></span>
                <h2 class="blog-featured-card__title"><?php the_title(); ?></h2>
                <p class="blog-featured-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 24, '...'); ?></p>
              </div>
              <div class="blog-card-author">
                <div class="blog-card-author__profile">
                  <img class="blog-card-author__avatar" src="<?php echo esc_url($f_author_avatar); ?>" alt="<?php the_author(); ?>" width="36" height="36">
                  <div class="blog-card-author__meta">
                    <span class="blog-card-author__name"><?php the_author(); ?></span>
                    <span class="blog-card-author__time"><?php echo get_the_date('M d, Y'); ?> • <?php echo esc_html($f_read_time); ?></span>
                  </div>
                </div>
                <span class="blog-card-arrow" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                </span>
              </div>
            </div>
          </a>
        <?php endwhile; wp_reset_postdata(); endif; ?>

        <!-- Bottom: 2-Column Grid of Blog Cards (Matches Image 2 Grid) -->
        <div class="blog-articles-grid">
          <?php if ($grid_query->have_posts()) : ?>
            <?php while ($grid_query->have_posts()) : $grid_query->the_post(); 
              $g_cats = get_the_category();
              $g_cat_name = !empty($g_cats) ? $g_cats[0]->name : 'Article';
              $g_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium_large') : $default_img;
              $g_words = str_word_count(strip_tags(get_the_content()));
              $g_read_time = max(1, round($g_words / 200)) . ' min read';
              $g_author_avatar = get_avatar_url(get_the_author_meta('ID'), array('size' => 72));
            ?>
              <article class="blog-grid-card">
                <a href="<?php the_permalink(); ?>" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">
                  <div class="blog-grid-card__media">
                    <img class="blog-grid-card__img" src="<?php echo esc_url($g_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="400" height="220">
                  </div>
                  <div class="blog-grid-card__body">
                    <span class="blog-grid-card__tag"><?php echo esc_html($g_cat_name); ?></span>
                    <h3 class="blog-grid-card__title"><?php the_title(); ?></h3>
                    <p class="blog-grid-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
                    <div class="blog-card-author">
                      <div class="blog-card-author__profile">
                        <img class="blog-card-author__avatar" src="<?php echo esc_url($g_author_avatar); ?>" alt="<?php the_author(); ?>" width="32" height="32">
                        <div class="blog-card-author__meta">
                          <span class="blog-card-author__name"><?php the_author(); ?></span>
                          <span class="blog-card-author__time"><?php echo esc_html($g_read_time); ?></span>
                        </div>
                      </div>
                      <span class="blog-card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                      </span>
                    </div>
                  </div>
                </a>
              </article>
            <?php endwhile; wp_reset_postdata(); ?>
          <?php else : ?>
            <div class="blog-empty-state">
              <div class="blog-empty-state__icon">🔍</div>
              <h3 class="blog-empty-state__title">No more articles in this category</h3>
              <p class="blog-empty-state__text">Check back soon or explore other categories in the sidebar.</p>
              <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn--primary btn--sm">View All Categories</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- WordPress Native Pagination -->
        <?php
        $big = 999999999;
        $paginate_links = paginate_links(array(
            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    => '?paged=%#%',
            'current'   => max(1, $paged),
            'total'     => $grid_query->max_num_pages,
            'type'      => 'array',
            'prev_text' => '&larr; Prev',
            'next_text' => 'Next &rarr;',
        ));
        if (!empty($paginate_links)) : ?>
          <nav class="blog-pagination" aria-label="Posts pagination">
            <?php foreach ($paginate_links as $link) {
              $link_class = strpos($link, 'current') !== false ? 'blog-page-btn active' : 'blog-page-btn';
              echo str_replace('page-numbers', $link_class, $link);
            } ?>
          </nav>
        <?php endif; ?>

      </section>

    </div>
  </div>

</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <div class="container footer__grid">
    <div class="footer__col footer__col--brand">
      <a href="<?php echo home_url('/'); ?>" class="brand brand--footer" aria-label="Wizhy Web Studio">
        <span class="brand__text">Wizhy<em> Web Studio</em></span>
      </a>
      <p class="footer__tagline">Websites that don't just look good — they get found, rank on Google, and grow your business.</p>
    </div>

    <div class="footer__col">
      <h3>Quick Links</h3>
      <a href="<?php echo home_url('/'); ?>">Home</a>
      <a href="<?php echo home_url('/#services'); ?>">Services</a>
      <a href="<?php echo home_url('/#pricing'); ?>">Pricing</a>
      <a href="<?php echo home_url('/blog/'); ?>">Blog &amp; Resources</a>
      <a href="<?php echo home_url('/contact/'); ?>">Contact Us</a>
    </div>

    <div class="footer__col footer__col--contact">
      <h3>Contact Details</h3>
      <p>techwizhy@gmail.com</p>
      <p>+91 98707 06956</p>
      <p>Chennai, India</p>
    </div>
  </div>
  <div class="footer__bottom">
    <div class="container footer__bottom-inner">
      <p>&copy; <?php echo date('Y'); ?> Wizhy Web Studio. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="<?php echo get_template_directory_uri(); ?>/js/script.min.js" defer></script>
<?php wp_footer(); ?>
</body>
</html>
