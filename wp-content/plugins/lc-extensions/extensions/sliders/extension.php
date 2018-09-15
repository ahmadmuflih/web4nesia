<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_sliders_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'sliders',
			'rank' => 25,
			'title' => 'Sliders Integration',
			'details' => 'https://livecomposerplugin.com/downloads/sliders-integration/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=sliders-integration',
			'description' => 'Creates modules for third-party slider plugins. Drag and drop slider module on the page instead of dealing with shortcodes.',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'sliders' ) ) :

		define( 'LCPROEXT_SLIDER_URL', plugin_dir_url( __FILE__ ) );
		define( 'LCPROEXT_SLIDER_ABS', dirname( __FILE__ ) );
		define( 'LCPROEXT_SLIDER_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );

		if ( in_array( 'masterslider/masterslider.php', get_option( 'active_plugins' ) ) && defined( 'DS_LIVE_COMPOSER_URL' ) ) {
			include LCPROEXT_SLIDER_ABS . '/inc/module.php';
		}

	endif; // If is_extension_active.
}

lcproext_sliders_init();