<?php
/**
 * The template for displaying all pages.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This is the template that displays all pages by default.
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
// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
if ( LBMN_THEME_DEBUG ) {
	echo '<!-- FILE: ' . __FILE__ . ' -->';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	// The functions below called only if Live Composer isn't active
	// otherwise LiveComposer outputs everything for us
	echo lbmn_thumbnail();
	echo lbmn_posttitle();
	echo lbmn_postdate(); ?>
	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			global $post;
			$thePostID = $post->ID;
			// Get live composer code for the current post/page
			$livecomposer_code = get_post_meta( $thePostID, 'dslc_code', true );
			// Get the template ID set for the post ( returns false if not set )
			$template  = get_post_meta( $thePostID, 'dslc_post_template', true );
			$post_type = get_post_type( $thePostID );

			// if there is not dslc_code set yet
			// if it's not a post powered by LC template
			// if live composer editing mode isn't active

			if ( ( ! $livecomposer_code && ! $template ) && ! ( defined( 'DS_LIVE_COMPOSER_ACTIVE' ) && DS_LIVE_COMPOSER_ACTIVE ) && ( $post_type != 'post' ) ) {
				// output the page content in standard 'boxed' design
				echo '<div class="dslc-code-empty-title dslc-clearfix">';
				echo the_title( '<h1 class="entry-title dslc-modules-section-wrapper">', '</h1>', false );
				echo '</div>';
				echo '<div class="dslc-code-empty-content dslc-modules-section-wrapper dslc-clearfix">';
				the_content();
				echo '</div>';
			} else {
				the_content();
			}

			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'lbmn' ),
				'after'  => '</div>',
			) );

		} else {
			// Called only if Live Composer isn't active
			echo '<div class="dslc-code-empty-content dslc-modules-section-wrapper dslc-clearfix">';
			the_excerpt();
			echo '</div>';
		}
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->