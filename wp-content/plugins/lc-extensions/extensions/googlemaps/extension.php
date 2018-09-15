<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_googlemaps_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'googlemaps',
			'rank' => 20,
			'title' => 'Google Maps Module',
			'details' => 'https://livecomposerplugin.com/downloads/google-maps-add-on/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=google-maps',
			'description' => 'Fast and easy way to display a Google map on your Live Composer powered website. The extension adds a new module.',				
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'googlemaps' ) ) :

		define( 'LC_GOOGLEMAPS_URL', plugin_dir_url( __FILE__ ) );
		define( 'LC_GOOGLEMAPS_ABS', dirname( __FILE__ ) );
		define( 'LC_GOOGLEMAPS_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
		define( 'LC_GOOGLEMAPS_VER', '1.1.7' );
		define( 'LC_GOOGLEMAPS_DEFAULT_ADDR', '14 High St, Newmarket CB8 8LB, United Kingdom' );

		include LC_GOOGLEMAPS_ABS . '/inc/functions.php';
		include LC_GOOGLEMAPS_ABS . '/inc/module.php';

	endif; // If is_extension_active.

	/**
	 * Add CSS
	 */
	// function lcgooglemaps_style() {
	// 	wp_enqueue_style( 'sklc-gmaps-css', LC_GOOGLEMAPS_URL . 'css/main.css' );
	// }
	// add_action( 'wp_enqueue_scripts', 'lcgooglemaps_style' );
}

lcproext_googlemaps_init();

