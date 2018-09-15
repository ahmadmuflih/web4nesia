<?php
/**
 * The template for displaying the website header.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 * Outputs all head of the page including notifications and site header
 *    – <head> section
 *    – Warning messages for the website admin
 *    – Notification panel
 *    – Top Bar (menu location: 'topbar' )
 *    – Site header with Mega Menu
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
	exit; // Exit if accessed directly.
} ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

	<?php
	// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
	if ( LBMN_THEME_DEBUG ) {
		echo '<!-- FILE: ' . __FILE__ . ' -->';
	}
	?>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="off-canvas-wrap">
	<div class="site global-container inner-wrap" id="global-container">
		<div class="global-wrapper">
			<?php
			do_action( 'before' );

			/**
			 * Output Live Composer powered headers only when:
			 * – Live Composer installed
			 * – Theme Configuration (basic header/footer import) completed.
			 * In all other cases output simplified version of the header.
			 */
			if ( lbmn_livecomposer_installed() && LBMN_THEME_CONFUGRATED ) {
				if ( function_exists( 'dslc_hf_get_header' ) ) {
					echo dslc_hf_get_header();
				}
			} else {
				// Prepare custom header classes
				$custom_header_classes = '';

				// Prepare header inner wrappers
				$header_inside_before = '';
				$header_inside_after = '';

				$custom_header_classes .= 'mega_main_menu-disabled';
				$header_inside_before = '<div class="default-header-content">';
				$header_inside_after = '</div> <!-- default-header-content -->';
				?>
				<header class="site-header <?php echo $custom_header_classes; ?>" role="banner">
				<?php
				// Show header only if LC isn't active
				if ( defined('DS_LIVE_COMPOSER_ACTIVE') && DS_LIVE_COMPOSER_ACTIVE ) {
					echo 'The header is disabled when editing the page.';
				} else {

					echo $header_inside_before;

					// Add logo if Mega Main Menu plugin is disabled
					// NOTE: normally logo displayed by Mega Main Menu
					echo lbmn_logo();

					/**
					 * ----------------------------------------------------------------------
					 * Site header with Mega Menu
					 * menu location 'header-menu' with Mega Main Menu inside
					 */

					// Disable menu for editing mode in Live Composer.
					if ( has_nav_menu( 'header-menu' ) ) {
						// If 'header-menu' location has a menu assigned.
						wp_nav_menu( array(
							'theme_location' => 'header-menu',
							'container_class' => 'header-top-menu',
						) );
					} else {
						if ( current_user_can( 'install_themes' ) ) {
							echo '<div class="no-menu-set">';
							echo 'Your menu will appear here (Display location: Main Menu)';
							echo '</div>';
						}
					}

					echo $header_inside_after;
				}
				?>
				</header><!-- #masthead -->
				<?php
				// if ( lbmn_updated_from_first_generation() ) {
					// The functions below called only if Live Composer isn't active.
					// lbmn_render_header_mmm();
				// }
			} ?>
			<div class="site-main">
