<?php
/**
 * Template Name: Single Blog Article
 * Template for displaying all single blog posts.
 * Fully optimized for sub-0.8s LCP, 0.000 CLS, and JSON-LD schema SEO.
 *
 * @package Wizhy_AG_Theme
 */

the_post();

$post_id = get_the_ID();
$title = get_the_title();
$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
$permalink = get_permalink();
$date_iso = get_the_date('c');
$date_display = get_the_date('M d, Y');
$modified_iso = get_the_modified_date('c');
$author_name = get_the_author();
$author_avatar = get_avatar_url(get_the_author_meta('ID'), array('size' => 96));
$cats = get_the_category();
$cat_name = !empty($cats) ? $cats[0]->name : 'Insights';
$cat_link = !empty($cats) ? get_category_link($cats[0]->term_id) : home_url('/blog/');

$words = str_word_count(strip_tags(get_the_content()));
$read_time = max(1, round($words / 200)) . ' min read';

$has_thumb = has_post_thumbnail();
$thumb_url = $has_thumb ? get_the_post_thumbnail_url($post_id, 'large') : get_template_directory_uri() . '/assets/og-image.webp';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo esc_html($title); ?> | Wizhy Web Studio</title>
  <meta name="description" content="<?php echo esc_attr(wp_strip_all_tags($excerpt)); ?>">
  <link rel="canonical" href="<?php echo esc_url($permalink); ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="article">
  <meta property="og:locale" content="en_IN">
  <meta property="og:site_name" content="Wizhy Web Studio">
  <meta property="og:title" content="<?php echo esc_attr($title); ?>">
  <meta property="og:description" content="<?php echo esc_attr(wp_strip_all_tags($excerpt)); ?>">
  <meta property="og:url" content="<?php echo esc_url($permalink); ?>">
  <meta property="og:image" content="<?php echo esc_url($thumb_url); ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
  <meta name="twitter:description" content="<?php echo esc_attr(wp_strip_all_tags($excerpt)); ?>">
  <meta name="twitter:image" content="<?php echo esc_url($thumb_url); ?>">

  <!-- Sub-0.8s LCP: Preload Featured Image -->
  <?php if ($has_thumb) : ?>
    <link rel="preload" as="image" href="<?php echo esc_url($thumb_url); ?>" fetchpriority="high">
  <?php endif; ?>

  <!-- Fonts & Theme Stylesheets -->
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/fonts.css">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.min.css?v=5.0">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/chat.css">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/blog.css">

  <!-- Automated JSON-LD BlogPosting Schema for Google Rich Snippets -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": <?php echo json_encode($title); ?>,
    "description": <?php echo json_encode(wp_strip_all_tags($excerpt)); ?>,
    "datePublished": "<?php echo esc_attr($date_iso); ?>",
    "dateModified": "<?php echo esc_attr($modified_iso); ?>",
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "<?php echo esc_url($permalink); ?>"
    },
    "image": "<?php echo esc_url($thumb_url); ?>",
    "author": {
      "@type": "Person",
      "name": <?php echo json_encode($author_name); ?>
    },
    "publisher": {
      "@type": "Organization",
      "name": "Wizhy Web Studio",
      "logo": {
        "@type": "ImageObject",
        "url": "<?php echo esc_url(get_template_directory_uri() . '/assets/wizhy-studio-logo-sm.png'); ?>"
      }
    }
  }
  </script>

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
<body <?php body_class('single-blog-post'); ?>>

<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="scroll-progress" id="scrollProgress"></div>

<!-- ============ NAVBAR ============ -->
<header class="navbar" id="navbar">
  <div class="container navbar__inner">
    <nav class="nav-pill" id="navPill" aria-label="Primary Navigation">
      <a href="<?php echo home_url('/'); ?>" class="nav-link">Home</a>
      <a href="<?php echo home_url('/#services'); ?>" class="nav-link">Services</a>
      <a href="<?php echo home_url('/#pricing'); ?>" class="nav-link">Pricing</a>
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
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <!-- Post Header -->
    <header class="post-article-header">
      <div class="container">
        
        <!-- Breadcrumb -->
        <nav class="post-breadcrumb" aria-label="Breadcrumb">
          <a href="<?php echo home_url('/'); ?>">Home</a>
          <span>/</span>
          <a href="<?php echo home_url('/blog/'); ?>">Blog</a>
          <span>/</span>
          <a href="<?php echo esc_url($cat_link); ?>"><?php echo esc_html($cat_name); ?></a>
        </nav>

        <span class="blog-featured-badge"><?php echo esc_html($cat_name); ?></span>
        <h1 class="post-title"><?php the_title(); ?></h1>

        <div class="post-meta-row">
          <div class="post-meta-author">
            <img class="post-meta-avatar" src="<?php echo esc_url($author_avatar); ?>" alt="<?php the_author(); ?>" width="36" height="36">
            <strong><?php the_author(); ?></strong>
          </div>
          <span>•</span>
          <time datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date_display); ?></time>
          <span>•</span>
          <span><?php echo esc_html($read_time); ?></span>
        </div>

      </div>
    </header>

    <!-- Featured Cover Image (Zero CLS: width & height attributes guaranteed) -->
    <?php if ($has_thumb) : ?>
      <div class="container">
        <div class="post-cover-wrap">
          <?php the_post_thumbnail('large', array(
              'class' => 'post-cover-img',
              'loading' => 'eager',
              'fetchpriority' => 'high'
          )); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="container">
      <div class="post-content-container">

        <!-- Post Content generated from WordPress Gutenberg / Classic blocks -->
        <div class="article-content" style="font-size:1.1rem; line-height:1.8; color:var(--text-1);">
          <?php the_content(); ?>
        </div>

        <!-- Social Share Bar -->
        <div class="post-share-bar">
          <span style="font-weight:700;font-size:0.95rem;">Share this article:</span>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="https://wa.me/?text=<?php echo urlencode($title . ' - ' . $permalink); ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">WhatsApp</a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">X / Twitter</a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($permalink); ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">LinkedIn</a>
          </div>
        </div>

        <!-- Author Bio Box -->
        <div class="post-author-box">
          <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php the_author(); ?>" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">
          <div>
            <h4 style="font-size:1.1rem;margin-bottom:4px;">Written by <?php the_author(); ?></h4>
            <p style="font-size:0.88rem;color:var(--text-secondary);margin:0;">Engineer &amp; strategist at Wizhy Web Studio. Building lightning-fast websites that rank on Google and convert visitors.</p>
          </div>
        </div>

      </div>
    </div>

  </article>

  <!-- CTA Box Before Footer -->
  <section class="section section--alt" style="padding:60px 0;">
    <div class="container text-center" style="max-width:680px;margin:0 auto;">
      <h3 style="font-family:var(--font-display);font-size:2rem;margin-bottom:12px;">Want a website that loads this fast and ranks?</h3>
      <p style="color:var(--text-secondary);margin-bottom:24px;">Claim one of our 10 Free Custom Website spots or run an instant SEO audit.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="<?php echo home_url('/#pricing'); ?>" class="btn btn--primary">Claim Free Website Offer 🎁</a>
        <a href="<?php echo home_url('/contact/'); ?>" class="btn btn--ghost">Contact Us</a>
      </div>
    </div>
  </section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <div class="container footer__grid">
    <div class="footer__col footer__col--brand">
      <a href="<?php echo home_url('/'); ?>" class="brand brand--footer">
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
