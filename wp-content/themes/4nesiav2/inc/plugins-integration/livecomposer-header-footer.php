<?php
/**
 * LiveComposer-based footers ( WP Admin > Appearance > Footers )
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * We use the special content type lbmn_footer to make possible for
 * end user to create complex unlimited footer designs. This file
 * register lbmn_footer content type and extend Live Composer
 * to render custom generated CSS for each page were footer displayed.
 *
 *  – Register special lbmn_footer content type
 *  – Extend LiveComposer Footer CSS render
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

// Check if Live Composer installed.
if ( lbmn_livecomposer_installed() ) {

	// Enable header/footer functionality in Live Composer.
	define( 'DS_LIVE_COMPOSER_HF', true );
	define( 'DS_LIVE_COMPOSER_HF_AUTO', false );

	if ( lbmn_updated_from_first_generation() ) {

		/**
		 * Render MegaMainMenu based header in first generation of the theme.
		 */
		function lbmn_render_header_first_generation() {

			/**
			 * Notification panel
			 */

			if ( ! lbmn_livecomposer_editing_mode() ) {

				// Get data from theme customizer.
				$notificationpanel_switch = get_theme_mod( 'lbmn_notificationpanel_switch', 1 );
				// $notificationpanel_icon 			= get_theme_mod( 'lbmn_notificationpanel_icon', LBMN_NOTIFICATIONPANEL_DEFAULT );
				$notificationpanel_message   = get_theme_mod( 'lbmn_notificationpanel_message', LBMN_NOTIFICATIONPANEL_MESSAGE_DEFAULT );
				$notificationpanel_buttonurl = get_theme_mod( 'lbmn_notificationpanel_buttonurl', LBMN_NOTIFICATIONPANEL_BUTTONURL_DEFAULT );

				// WPML dynamic string translation support – Get translation for the strings (via WP > WPML > Strings Translation).
				$notificationpanel_message   = apply_filters( 'wpml_translate_single_string', $notificationpanel_message, 'Theme Customizer', 'Notification panel (before header) – Message' );
				$notificationpanel_buttonurl = apply_filters( 'wpml_translate_single_string', $notificationpanel_buttonurl, 'Theme Customizer', 'Notification panel (before header) – URL' );

				// Generate hash to use with cookie, so visitors don't see the same mesage if they closed it once.
				$npanel_unique_cookie_key = hash( 'md4', $notificationpanel_message . $notificationpanel_buttonurl );
				global $wp_customize;

				// Empty ( $GLOBALS['wp_customize'] ) -- indicates if we are insite WP Theme Customizer @link http://goo.gl/Zj1fz.
				if ( $notificationpanel_switch || isset( $wp_customize ) ) {
					?>
					<div class='notification-panel' data-stateonload='<?php echo $notificationpanel_switch; ?>' data-uniquehash='<?php echo $npanel_unique_cookie_key; ?>'>
					<span class='notification-panel__content'>
						<span class='notification-panel__message'>
							<?php
							// if ( 4 < strlen($notificationpanel_icon) ) {
							// 	echo "<i class='$notificationpanel_icon  notification-panel__icon hide-for-small'></i> ";
							// }
							echo "<span class='notification-panel__message-text'>" . $notificationpanel_message . "</span>&nbsp;&nbsp;&nbsp;<i class='fa-icon-angle-right notification-panel__cta-icon'></i>";
							?>
						</span>
					</span>
						<?php if ( 4 < strlen( $notificationpanel_buttonurl ) ) { ?>
							<a href='<?php echo esc_url( $notificationpanel_buttonurl ); ?>' class='notification-panel__cta-link'></a>
						<?php } ?>
						<a href="#" class='notification-panel__close'>&#10005;</a>
					</div>
					<?php
				}

			}

			/**
			 * Top Bar
			 */

			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) && ! lbmn_livecomposer_editing_mode() ) {
				// if there is menu attached to the 'topbar' area
				if ( has_nav_menu( 'topbar' ) ) {
					// If tobar bar is enabled or in theme customizer
					if ( get_theme_mod( 'lbmn_topbar_switch', 1 ) || isset( $wp_customize ) ) {
						wp_nav_menu( array(
							'theme_location' => 'topbar',
						) );
					}
				}
			}

			/**
			 * Mega Main Menu
			 */
			lbmn_render_header_mmm();
		}
		add_filter( 'dslc_header_before', 'lbmn_render_header_first_generation' );

		/**
		 * Add notification
		 */
		function lbmn_render_header_notification() {
			if ( lbmn_livecomposer_editing_mode() ) {

				$page_id = get_the_id();
				$page_type = get_post_type( $page_id );
				$dslc_hf_for = get_post_meta( $page_id, 'dslc_hf_for', true );
				$dslc_hf_type = get_post_meta( $page_id, 'dslc_hf_type', true );
				$header_switch = get_theme_mod( 'lbmn_headertop_switch', 1 );

				if ( 'dslc_hf' == $page_type && 'header' == $dslc_hf_for && 'default' == $dslc_hf_type && $header_switch ) { ?>
					<div class="dslc-notification dslc-red"><?php _e( 'Your site is using the previous generation of the SEOWP headers. Edit in the WP Admin > Appearance > Customizer.', 'live-composer-page-builder' ); ?></div>
				<?php }
			}
		}
		add_filter( 'dslc_content_before', 'lbmn_render_header_notification' );

		/**
		 * Output Call to action area in the theme of 1st generation.
		 */
		function lbmn_render_calltoaction_first_generation() {

			// Get data from theme customizer.
			$calltoaction_switch  = get_theme_mod( 'lbmn_calltoaction_switch', 1 );
			$calltoaction_message = get_theme_mod( 'lbmn_calltoaction_message', LBMN_CALLTOACTION_MESSAGE_DEFAULT );
			$calltoaction_url     = get_theme_mod( 'lbmn_calltoaction_url', LBMN_CALLTOACTION_URL_DEFAULT );

			// WPML dynamic string translation support – Get translation for the strings (via WP > WPML > Strings Translation).
			$calltoaction_message = apply_filters( 'wpml_translate_single_string', $calltoaction_message, 'Theme Customizer', 'Call to action (before footer) – Message' );
			$calltoaction_url     = apply_filters( 'wpml_translate_single_string', $calltoaction_url, 'Theme Customizer', 'Call to action (before footer) – URL' );

			ob_start();

			global $wp_customize;
			// If call to action panel is active or we are in theme customizer.
			if ( $calltoaction_switch || isset( $wp_customize ) ) {
				?>
				<section class='calltoaction-area' data-stateonload='<?php echo $calltoaction_switch; ?>'>
					<span class='calltoaction-area__content'>
						<?php
						echo $calltoaction_message . "&nbsp;&nbsp;&nbsp;<i class='fa-icon-angle-right calltoaction-area__cta-icon'></i>";
						?>
					</span>
					<?php if ( 4 < strlen( $calltoaction_url ) ) { ?>
						<a href='<?php echo esc_url( $calltoaction_url ); ?>' class='calltoaction-area__cta-link'></a>
					<?php } ?>
				</section>
				<?php
			}

			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		add_filter( 'dslc_footer_before', 'lbmn_render_calltoaction_first_generation' );
	}
}
