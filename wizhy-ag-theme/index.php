<?php
/**
 * Main Template File (Blog & Archive fallback)
 * Automatically renders Image 2 Blog Hub for all archives and blog listings.
 *
 * @package Wizhy_AG_Theme
 */

if (is_front_page()) {
    get_template_part('front-page');
    return;
}

include get_template_directory() . '/page-blog.php';
