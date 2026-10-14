<?php
/**
 * Main Template File (Blog & Archive fallback)
 *
 * @package Wizhy_AG_Theme
 */

if (is_front_page()) {
    get_template_part('front-page');
    return;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/fonts.css">
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.min.css">
</head>
<body <?php body_class(); ?>>
<header class="navbar">
  <div class="container navbar__inner" style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 0;">
    <a href="<?php echo home_url('/'); ?>" class="navbar__logo" style="text-decoration:none; font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:var(--text-1);">
      Wizhy Web Studio
    </a>
    <a href="<?php echo home_url('/'); ?>" class="btn btn--primary btn--sm">Back to Home</a>
  </div>
</header>

<main class="container" style="max-width:960px; margin:4rem auto; padding:0 1.5rem; min-height:60vh;">
  <header style="margin-bottom:3rem; text-align:center;">
    <h1 style="font-family:'Playfair Display',serif; font-size:2.5rem; margin-bottom:0.75rem;">
      <?php single_post_title(); ?>
    </h1>
    <p style="color:var(--text-2);">Articles, Insights & Web Strategy by Wizhy</p>
  </header>

  <?php if (have_posts()) : ?>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:2rem;">
      <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?> style="padding:1.5rem; border:1px solid var(--border-subtle); border-radius:12px; background:var(--bg-surface);">
          <?php if (has_post_thumbnail()) : ?>
            <div style="margin-bottom:1rem; border-radius:8px; overflow:hidden;">
              <?php the_post_thumbnail('medium_large', array('style' => 'width:100%; height:auto; display:block;')); ?>
            </div>
          <?php endif; ?>
          <h2 style="font-size:1.25rem; font-family:'Playfair Display',serif; margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>" style="text-decoration:none; color:inherit;"><?php the_title(); ?></a>
          </h2>
          <div style="font-size:0.875rem; color:var(--text-3); margin-bottom:0.75rem;">
            <?php echo get_the_date(); ?>
          </div>
          <div style="color:var(--text-2); font-size:0.95rem; line-height:1.6; margin-bottom:1rem;">
            <?php the_excerpt(); ?>
          </div>
          <a href="<?php the_permalink(); ?>" style="color:var(--accent-1); font-weight:600; text-decoration:none;">Read More &rarr;</a>
        </article>
      <?php endwhile; ?>
    </div>

    <div style="margin-top:3rem; text-align:center;">
      <?php the_posts_pagination(); ?>
    </div>
  <?php else : ?>
    <p style="text-align:center; color:var(--text-2);">No posts published yet. Stay tuned!</p>
  <?php endif; ?>
</main>

<footer style="padding:3rem 0; border-top:1px solid var(--border-subtle); text-align:center; color:var(--text-3); font-size:0.875rem;">
  <p>&copy; <?php echo date('Y'); ?> Wizhy Web Studio. All rights reserved.</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
