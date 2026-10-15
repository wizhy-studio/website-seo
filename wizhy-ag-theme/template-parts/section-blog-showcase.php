<?php
/**
 * Template Part: Blog & Insights Showcase (Image 1 Layout)
 * Displays on the Landing Page between FAQ and Contact.
 * Dynamically queries published WordPress posts with fallback.
 *
 * @package Wizhy_AG_Theme
 */

// 1. Query top 3 posts for Left Spotlight Tall Cards
$tall_query = new WP_Query(array(
    'posts_per_page'      => 3,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'no_found_rows'       => true,
));

// 2. Query next 3 posts for Sidebar Featured
$featured_query = new WP_Query(array(
    'posts_per_page'      => 3,
    'offset'              => 3,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'no_found_rows'       => true,
));

// 3. Query next 2 posts for Sidebar Latest
$latest_query = new WP_Query(array(
    'posts_per_page'      => 2,
    'offset'              => 6,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'no_found_rows'       => true,
));

$default_img = get_template_directory_uri() . '/assets/og-image.webp';
?>

<!-- ============ BLOG & INSIGHTS (IMAGE 1 LAYOUT) ============ -->
<section class="blog-section section" id="blog">
  <div class="container">
    
    <!-- Header: Badge + Title + Subtitle -->
    <div class="blog-section__header reveal">
      <span class="blog-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Our Blog
      </span>
      <h2 class="blog-section__title">Discover our latest news &amp; insights</h2>
      <p class="blog-section__subtitle">
        Actionable breakdowns on technical speed, conversion design, and search ranking strategies straight from the engineers crafting our high-performance websites.
      </p>

      <!-- Instant Search & Quick Jump to /blog/ -->
      <form class="blog-jump-bar" action="<?php echo esc_url(home_url('/blog/')); ?>" method="GET" role="search">
        <div class="blog-jump-bar__input-wrap">
          <svg class="blog-jump-bar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" name="q" class="blog-jump-bar__input" placeholder="Search guides, speed tactics, SEO..." autocomplete="off">
        </div>
        <button type="submit" class="btn btn--primary" style="height:48px;padding:0 24px;">Find Guides</button>
      </form>
    </div>

    <!-- Subdivider Row -->
    <div class="blog-subdivider reveal">
      <h3 class="blog-subdivider__title">Featured Industry Insights</h3>
      <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="blog-subdivider__link">
        Explore All Articles
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
      </a>
    </div>

    <!-- Main Showcase Grid (Left: 3 Tall Cards | Right: Featured & Latest Lists) -->
    <div class="blog-showcase__grid reveal">
      
      <!-- LEFT: 3 Tall Spotlight Visual Cards -->
      <div class="blog-tall-cards">
        <?php if ($tall_query->have_posts()) : ?>
          <?php while ($tall_query->have_posts()) : $tall_query->the_post(); 
            $cats = get_the_category();
            $cat_name = !empty($cats) ? $cats[0]->name : 'Insights';
            $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : $default_img;
            $words = str_word_count(strip_tags(get_the_content()));
            $read_time = max(1, round($words / 200)) . ' min read';
          ?>
            <a href="<?php the_permalink(); ?>" class="blog-tall-card" aria-label="<?php the_title_attribute(); ?>">
              <img class="blog-tall-card__bg" src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="400" height="500">
              <div class="blog-tall-card__overlay" aria-hidden="true"></div>
              <div class="blog-tall-card__top">
                <span class="blog-tag-pill"><?php echo esc_html($cat_name); ?></span>
              </div>
              <div class="blog-tall-card__content">
                <div class="blog-tall-card__meta">
                  <span><?php echo get_the_date('M d, Y'); ?></span>
                  <span>•</span>
                  <span><?php echo esc_html($read_time); ?></span>
                </div>
                <h4 class="blog-tall-card__title"><?php the_title(); ?></h4>
                <p class="blog-tall-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
              </div>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
          <!-- Fallback when no posts published yet -->
          <div class="blog-tall-card">
            <img class="blog-tall-card__bg" src="<?php echo esc_url($default_img); ?>" alt="Wizhy Performance" loading="lazy">
            <div class="blog-tall-card__overlay"></div>
            <div class="blog-tall-card__top"><span class="blog-tag-pill">Performance</span></div>
            <div class="blog-tall-card__content">
              <h4 class="blog-tall-card__title">How We Make Client Sites Load Under 0.8s</h4>
              <p class="blog-tall-card__excerpt">Publish your first article in WordPress Admin to replace this placeholder!</p>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Featured & Latest Lists (Sidebar) -->
      <aside class="blog-sidebar">
        
        <!-- Featured Group -->
        <div class="blog-sidebar__group">
          <h4 class="blog-sidebar__heading">
            <span>Featured</span>
            <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted);">Editor's Pick</span>
          </h4>
          <div class="blog-sidebar__list">
            <?php if ($featured_query->have_posts()) : ?>
              <?php while ($featured_query->have_posts()) : $featured_query->the_post(); 
                $side_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : $default_img;
              ?>
                <a href="<?php the_permalink(); ?>" class="blog-compact-card">
                  <div class="blog-compact-card__thumb">
                    <img class="blog-compact-card__img" src="<?php echo esc_url($side_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="78" height="78">
                  </div>
                  <div class="blog-compact-card__info">
                    <span class="blog-compact-card__date"><?php echo get_the_date('M d, Y'); ?></span>
                    <span class="blog-compact-card__title"><?php the_title(); ?></span>
                  </div>
                </a>
              <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
              <p style="font-size:0.85rem;color:var(--text-muted);">More featured guides coming soon.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Latest Group -->
        <div class="blog-sidebar__group">
          <h4 class="blog-sidebar__heading">
            <span>Latest</span>
            <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted);">Fresh Insights</span>
          </h4>
          <div class="blog-sidebar__list">
            <?php if ($latest_query->have_posts()) : ?>
              <?php while ($latest_query->have_posts()) : $latest_query->the_post(); 
                $side_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : $default_img;
              ?>
                <a href="<?php the_permalink(); ?>" class="blog-compact-card">
                  <div class="blog-compact-card__thumb">
                    <img class="blog-compact-card__img" src="<?php echo esc_url($side_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="78" height="78">
                  </div>
                  <div class="blog-compact-card__info">
                    <span class="blog-compact-card__date"><?php echo get_the_date('M d, Y'); ?></span>
                    <span class="blog-compact-card__title"><?php the_title(); ?></span>
                  </div>
                </a>
              <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
              <p style="font-size:0.85rem;color:var(--text-muted);">Check back soon for fresh articles.</p>
            <?php endif; ?>
          </div>
        </div>

      </aside>

    </div>

    <!-- Bottom Action Bar linking to Full 50+ Blog Archive -->
    <div class="blog-all-cta reveal">
      <div class="blog-all-cta__text">
        <h4>Looking for more in-depth web design &amp; growth guides?</h4>
        <p>Explore our complete knowledge base of tutorials, case studies, and code breakdowns.</p>
      </div>
      <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn--primary" style="display:inline-flex;align-items:center;gap:8px;">
        <span>Browse All Articles</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>

  </div>
</section>
