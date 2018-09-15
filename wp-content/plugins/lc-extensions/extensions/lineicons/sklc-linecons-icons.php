<?php
/**
 * Plugin Name: Live Composer Add-On - Linecons Icons
 * Plugin URI: http://livecomposerplugin.com/downloads/linecons-icons-add-on/
 * Description: Additional icons for the icon options in Live Composer.
 * Version: 1.0.1
 * Author: Slobodan Kustrimovic
 * Author URI: http://livecomposerplugin.com
 *
 *  @package Live Composer Add-On - Linecons Icons
 */

if ( defined( 'DS_LIVE_COMPOSER_URL' ) ) {

	define( 'SKLC_LINECONS_URL', plugin_dir_url( __FILE__ ) );
	define( 'SKLC_LINECONS_ABS', dirname( __FILE__ ) );

	/**
	 * Add icons in the current array
	 *
	 * @param array $icons return array with icons.
	 */
	function sklc_linecons_alter_icons( $icons ) {

		$icons['linecons'] = array( 'linecons-banknote', 'linecons-bubble', 'linecons-bulb', 'linecons-calendar', 'linecons-camera', 'linecons-clip', 'linecons-clock', 'linecons-cloud', 'linecons-cup', 'linecons-data', 'linecons-diamond', 'linecons-display', 'linecons-eye', 'linecons-fire', 'linecons-food', 'linecons-heart', 'linecons-key', 'linecons-lab', 'linecons-like', 'linecons-location', 'linecons-lock', 'linecons-mail', 'linecons-megaphone', 'linecons-music', 'linecons-news', 'linecons-note', 'linecons-paperplane', 'linecons-params', 'linecons-pen', 'linecons-phone', 'linecons-photo', 'linecons-search', 'linecons-settings', 'linecons-shop', 'linecons-sound', 'linecons-stack', 'linecons-star', 'linecons-study', 'linecons-t-shirt', 'linecons-tag', 'linecons-trash', 'linecons-truck', 'linecons-tv', 'linecons-user', 'linecons-vallet', 'linecons-video', 'linecons-vynil', 'linecons-world' );

		return $icons;

	} add_filter( 'dslc_available_icons', 'sklc_linecons_alter_icons' );

	/**
	 * Enqueue Scripts
	 */
	function sklc_linecons_scripts() {

		wp_enqueue_style( 'sklc-linecons-font-css', SKLC_LINECONS_URL . 'css/icons/stylesheet.css', array(), '1.0' );

	}
	add_action( 'wp_enqueue_scripts', 'sklc_linecons_scripts' );
	add_action( 'admin_enqueue_scripts', 'sklc_linecons_scripts' );

}
