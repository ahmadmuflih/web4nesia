<?php
/**
 * Functions called from theme when not all required
 * plugins installed to provide a basic theme functionality
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

function lbmn_thumbnail() {
	global $post;
	// Process function only when Live Composer plugin is disabled
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	$output = '';
	if ( ! is_single() ) {
		$posthumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		if ( is_array( $posthumb_url ) && $posthumb_url[0] ) {
			$posthumb_url = $posthumb_url[0];
		} else {
			$posthumb_url = esc_url_raw( get_template_directory_uri() . '/design/images/no-thumbnail.png' );
		}

		$output = '<div class="post-thumb" style="background: #F9F9F9 url(' . $posthumb_url . ') no-repeat center center; background-size:cover;"></div>';
	}

	return $output;
}


function lbmn_posttitle() {
	// Process function only when Live Composer plugin is disabled
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	$output = '';
	if ( is_single() ) {
		echo the_title( '<h1 class="entry-title">', '</h1>', false );
	} else {
		echo the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h1>', false );
	}
}


function lbmn_postdate() {
	// Process function only when Live Composer plugin is disabled
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	return '<div class="blog-post-meta-date">' . get_the_date( 'F j, Y' ) . '</div>';

}

/**
 * Output site logo.
 *
 * @return void
 */
function lbmn_logo() {
	// Process function only when Mega Main Menu plugin is disabled.
	if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	$lbmn_themeoptions = get_option( 'lbmn_theme_options' );

	// If no logo yet set?
	if ( ! isset( $lbmn_themeoptions['lbmn_logo_image'] ) ) {
		$lbmn_themeoptions['lbmn_logo_image'] = LBMN_LOGO_IMAGE_DEFAULT;
	}

	$logo_src = $lbmn_themeoptions['lbmn_logo_image'];
	echo '<h2 class="nomegamenu-logo"><a href="' . get_site_url() . '"><img src="' . $logo_src . '" alt="' . get_bloginfo( 'name' ) . '"/></a></h2>';
}

function lbmn_pagination() {
	// Process function only when Mega Main Menu plugin is disabled
	if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	global $wp_query;

	$output = '';
	if ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) {
		$output .= '<div class="pagination">';
		if ( get_next_posts_link() ) {
			$output .= '<div class="nav-previous">' . get_next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'lbmn' ) ) . '</div>';
		}

		if ( get_previous_posts_link() ) {
			$output .= '<div class="nav-next">' . get_previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'lbmn' ) ) . '</div>';
		}
		$output .= '</div>';
	}

	return $output;
}

/**
 * Backup function to show very basic default footer if no LC installed.
 *
 * @return void
 */
function lbmn_footer_default() {
	// Process function only when Live Composer plugin is disabled.
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && LBMN_THEME_CONFUGRATED ) {
		return;
	}

	echo '<div class="site-footer"><div class="site-footer-inner">';
	lbmn_logo();
	if ( has_nav_menu( 'header-menu' ) ) {
		// If 'header-menu' location has a menu assigned.
		echo wp_nav_menu( array(
			'theme_location'  => 'header-menu',
			'container_class' => 'footer-menu',
			'echo'            => false,
		) );
	} else {
		if ( current_user_can( 'install_themes' ) ) {
			echo '<div class="no-menu-set">';
			echo 'Your menu will appear here (Display location: Main Menu)';
			echo '</div>';
		}
	}
	echo '</div></div>';
}

if ( ! defined( 'DS_LIVE_COMPOSER_URL' ) && defined( 'LBMN_THEME_CONFUGRATED' ) && ! LBMN_THEME_CONFUGRATED ) {
	// Change default excerpt length.
	function lbmn_excerpt_length( $length ) {
		return 40;
	}

	add_filter( 'excerpt_length', 'lbmn_excerpt_length', 999 );
}

function lbmn_render_header_mmm() {

	// Get data from theme customizer.
	$header_switch = get_theme_mod( 'lbmn_headertop_switch', 1 );
	// Prepare custom header classes
	$custom_header_classes = '';

	// Prepare header inner wrappers
	$header_inside_before = '';
	$header_inside_after  = '';

	// Add special class if Mega Main Menu plugin is disabled
	if ( ! is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) || ! LBMN_THEME_CONFUGRATED || DS_LIVE_COMPOSER_ACTIVE ) {
		$custom_header_classes .= 'mega_main_menu-disabled';
		$header_inside_before = '<div class="default-header-content">';
		$header_inside_after  = '</div> <!-- default-header-content -->';
	}

	if ( $header_switch || isset( $wp_customize ) ) {
		?>
		<header class="site-header <?php echo $custom_header_classes; ?>" data-stateonload='<?php echo $header_switch; ?>' role="banner">
			<?php
			// Show header only if LC isn't active
			if ( lbmn_livecomposer_editing_mode() ) {
				echo 'The header is disabled when editing the page.';
			} else {
				echo $header_inside_before;

				// Add logo if Mega Main Menu plugin is disabled
				// NOTE: normally logo displayed by Mega Main Menu
				lbmn_logo();

				/**
				 * ----------------------------------------------------------------------
				 * Site header with Mega Menu
				 * menu location 'header-menu' with Mega Main Menu inside
				 */
				// Disable menu for editing mode in Live Composer.
				if ( has_nav_menu( 'header-menu' ) ) {
					// If 'header-menu' location has a menu assigned.
					wp_nav_menu( array(
						'theme_location'  => 'header-menu',
						'container_class' => 'header-top-menu',
					) );
				}

				echo $header_inside_after;
			}
			?>
		</header><!-- #masthead -->
		<?php
	}
}
