<?php
/**
 * Default Page Template
 *
 * @package Wizhy_AG_Theme
 */
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

<main class="container" style="max-width:860px; margin:4rem auto; padding:0 1.5rem; min-height:60vh;">
  <?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header style="margin-bottom:2rem; text-align:center;">
        <h1 style="font-family:'Playfair Display',serif; font-size:2.5rem; margin-bottom:0.75rem;">
          <?php the_title(); ?>
        </h1>
      </header>
      <div class="page-content" style="font-size:1.05rem; line-height:1.8; color:var(--text-1);">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<footer style="padding:3rem 0; border-top:1px solid var(--border-subtle); text-align:center; color:var(--text-3); font-size:0.875rem;">
  <p>&copy; <?php echo date('Y'); ?> Wizhy Web Studio. All rights reserved.</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
