<?php
/**
 * The main template file.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This is the most generic template file in a WordPress theme
 * it is used to display a page when nothing more specific matches a query.
 *
 * Beside the main page output it this template file outputs
 * the next archive listing pages:
 *    – search results list,
 *    – author posts list,
 *    – posts by category listing,
 *    – posts by tab listing,
 *    – posts by date listing
 *    – home page with latest posts
 *    – nothing found page,
 *    – 404 error page,
 *
 * To change design of these listing pages in other themes you need to edit
 * PHP files. In our theme user has total control over archive pages via
 * Live Composer powered pages of specially created content type (lbmn_archive).
 *
 * These lbmn_archive pages are actually Live Composer - powered pages
 * with archive listing module inside. With this approach we provide
 * a theme user with a possibility to edit/create new archive pages
 * the same way they work with normal pages.
 *
 * In the WP admin there is a special section for this:
 * WP admin > Appearance > System Templates.
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Output header.php content
get_header();

// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
if ( LBMN_THEME_DEBUG ) {
	echo '<!-- FILE: ' . __FILE__ . ' -->';
}

$livecomposer_is_ready   = false;
$livecomposer_has_output = false;

// Check if LiveComposer plugin is active and theme configured on install
if ( defined( 'DS_LIVE_COMPOSER_URL' ) && LBMN_THEME_CONFUGRATED ) {
	$livecomposer_is_ready = true;
}

echo '<div id="content" class="site-content" role="main">';
if ( have_posts() ) {

	if ( is_home() ) {
		if ( ! $livecomposer_is_ready || ! $livecomposer_has_output ) {
			echo '<div class="dslc-code-empty-title dslc-modules-section-wrapper dslc-clearfix">';
			echo '<h1 class="blog-description">' . get_bloginfo( 'description' ) . '</h1>';
			echo '</div>';
		}
	}

	while ( have_posts() ) {
		the_post();
		get_template_part( 'content' );
	} // end of the loop.

	if ( ! $livecomposer_is_ready || ! $livecomposer_has_output ) {
		echo lbmn_pagination();
	}
} else {
	// get_template_part( 'no-results', 'index' );
}
echo '</div><!-- #content -->';


// Output footer.php
get_footer();
