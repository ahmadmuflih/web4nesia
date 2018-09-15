<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_lineicons_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'lineicons',
			'rank' => 35,
			'title' => 'Linecons Icons',
			'details' => 'https://livecomposerplugin.com/downloads/linecons-icons-add-on/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=lineicons',
			'description' => 'This add-on adds 48 additional icons that will be available in the icon options for all modules that have icons option.',
		)
	);

}

lcproext_lineicons_init();

/**
 * Then, check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'lineicons' ) ) :

	define( 'LINECONS_URL', plugin_dir_url( __FILE__ ) );
	define( 'LINECONS_ABS', dirname( __FILE__ ) );

	/**
	 * Add icons in the current array
	 *
	 * @param array $icons return array with icons.
	 */
	function linecons_alter_icons( $icons ) {

		$icons['linecons'] = array( 'linecons-banknote', 'linecons-bubble', 'linecons-bulb', 'linecons-calendar', 'linecons-camera', 'linecons-clip', 'linecons-clock', 'linecons-cloud', 'linecons-cup', 'linecons-data', 'linecons-diamond', 'linecons-display', 'linecons-eye', 'linecons-fire', 'linecons-food', 'linecons-heart', 'linecons-key', 'linecons-lab', 'linecons-like', 'linecons-location', 'linecons-lock', 'linecons-mail', 'linecons-megaphone', 'linecons-music', 'linecons-news', 'linecons-note', 'linecons-paperplane', 'linecons-params', 'linecons-pen', 'linecons-phone', 'linecons-photo', 'linecons-search', 'linecons-settings', 'linecons-shop', 'linecons-sound', 'linecons-stack', 'linecons-star', 'linecons-study', 'linecons-t-shirt', 'linecons-tag', 'linecons-trash', 'linecons-truck', 'linecons-tv', 'linecons-user', 'linecons-vallet', 'linecons-video', 'linecons-vynil', 'linecons-world' );

		return $icons;

	} add_filter( 'dslc_available_icons', 'linecons_alter_icons' );

	/**
	 * Enqueue Scripts
	 */
	function linecons_scripts() {

		wp_enqueue_style( 'sklc-linecons-font-css', LINECONS_URL . 'css/icons/stylesheet.css', array(), '1.0' );

	}
	add_action( 'wp_enqueue_scripts', 'linecons_scripts' );
	add_action( 'admin_enqueue_scripts', 'linecons_scripts' );
endif; // If is_extension_active.