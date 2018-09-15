<?php
/**
 * ----------------------------------------------------------------------
 * Beacon is in-app helper and documentation search tool from HelpScout
 * This tool will help our clients to quickly find answers to their
 * questions in the theme documentation and contact us for support if needed.
 * http://www.helpscout.net/features/beacon/
 */

/**
 * ----------------------------------------------------------------------
 * Beacon integration
 */

// Disabled for now.
// add_action( 'admin_enqueue_scripts', 'lbmn_beacon_js' );
if ( ! function_exists( 'lbmn_beacon_js' ) ) {
	function lbmn_beacon_js( $hook ) {

		if ( 'toplevel_page_livecomposer_editor' === $hook ) {
			$current_screen = 'dslc-editing-screen';
		} else {
			$current_screen = '';
		}

		if ( 'dslc-editing-screen' !== $current_screen ) {

			$theme_dir_beacon = get_template_directory_uri();

			// $beaconData providing our support team with additional info about
			// the user enviroment like WP version, Purchase Code, etc.
			$beaconData = array(
				// 'wp_version'          => get_bloginfo('version'),
				'purchase_code' => get_option( 'lbmn_purchase_code' ),
				'update_theme'  => lbmn_get_theme_updates_log(),
				// 'get_all_plugins'     => lbmn_get_all_plugins(),
				// 'get_permalink'       => lbmn_get_permalink_option(),
				// 'get_php_information' => lbmn_get_php_information(),
			);

			if ( ! get_option( LBMN_THEME_NAME . '_hide_beacon' ) ) {
				wp_enqueue_script( 'lbmn_js_beacon', // handle
					$theme_dir_beacon . '/inc/beacon-helper/beacon.js', array(), // deps
					SEOWP_THEME_VER,  // ver
					true        // in_footer
				);

				wp_localize_script( 'lbmn_js_beacon', 'beaconGetParametrs', $beaconData );
			}
		}

	}
}

/**
 * Function returns history of the version updates to be included with the support request
 */
if ( ! function_exists( 'lbmn_get_theme_updates_log' ) ) {
	function lbmn_get_theme_updates_log() {
		$theme_updates_log = get_option( 'lbmn_theme_updates_log' );

		if ( $theme_updates_log ) {
			foreach ( $theme_updates_log as $value ) {
				$arr[] = $value . "<br />";
			}

			$updates_log = implode( "", $arr );
		} else {
			$updates_log = 'New installation';
		}

		return $updates_log;
	}
}

/**
 * Function returns installed plugins to be included with the support request
 */
if ( ! function_exists( 'lbmn_get_all_plugins' ) ) {
	function lbmn_get_all_plugins() {
		$all_plugins      = get_plugins();
		$all_plugins_keys = array_keys( $all_plugins );
		$plugins_list     = array();

		$loopCtr = 0;
		foreach ( $all_plugins as $plugin_item ) {

			$plugin_root_file = $all_plugins_keys[ $loopCtr ];
			$plugin_status    = is_plugin_active( $plugin_root_file );

			if ( $plugin_status ) {
				$plugins_list[] = '<p style="margin-bottom: 7px;"><b>Plugin:</b> ' . $plugin_item['Title'] . '<br /><b>Version:</b> ' . $plugin_item['Version'] . '</p>';
			}

			$loopCtr++;
		}

		if ( empty( $plugins_list ) ) {
			$plugin = implode( "", $plugins_list );

			return $plugin;
		} else {
			return false;
		}

	}
}

/**
 * Function return installed plugins to be included with the support request
 */
if ( ! function_exists( 'lbmn_get_permalink_option' ) ) {
	function lbmn_get_permalink_option() {
		$permalink_structure = get_option( 'permalink_structure' );

		switch ( $permalink_structure ) {
			case '':
				$permalink = 'Default';
				break;

			case '/%year%/%monthnum%/%day%/%postname%/':
				$permalink = 'Day and name';
				break;

			case '/%year%/%monthnum%/%postname%/':
				$permalink = 'Month and name';
				break;

			case '/archives/%post_id%':
				$permalink = 'Numeric';
				break;

			case '/%postname%/':
				$permalink = 'Post name';
				break;

			default:
				$permalink = 'Custom Structure';
				break;
		}

		return $permalink;
	}
}

/**
 * Function return critical for support php and server limits
 */
if ( ! function_exists( 'lbmn_get_php_information' ) ) {
	function lbmn_get_php_information() {
		// $php_information  = "<b>Memory Limit: </b>" . ini_get("memory_limit") . "<br />";
		// $php_information .= "<b>Time Limit: </b>" . ini_get("max_execution_time") . "<br />";
		// $php_information .= "<b>Max Upload Size: </b>" . ini_get("upload_max_filesize") . "<br />";
		// $php_information .= "<b>PHP Version: </b>" . phpversion() . "<br />";

		return $php_information;
	}
}

/* Action to hide Beacon helper */
add_action( 'current_screen', 'lbmn_hide_beacon_helper' );
if ( ! function_exists( 'lbmn_hide_beacon_helper' ) ) {
	function lbmn_hide_beacon_helper() {
		if ( ! is_admin() ) {
			return;
		}

		// Change option value based on the URL variable
		if ( isset( $_GET['hide_beacon'] ) ) {
			update_option( LBMN_THEME_NAME . '_hide_beacon', true );
		}

		if ( isset( $_GET['show_beacon'] ) ) {
			update_option( LBMN_THEME_NAME . '_hide_beacon', false );
		}

		add_filter( 'lbmn_theme_help_tab_content', 'lbmn_add_beacon_toggle' );
	}
}

function lbmn_add_beacon_toggle( $help_panel_content ) {
	// Prepare hide/show links for the Help tab on themes page

	$beacon_button = '<p><strong>Interactive helper (bottom-right):</strong> <ul><li>';
	if ( ! get_option( LBMN_THEME_NAME . '_hide_beacon' ) ) {
		$beacon_button .= '<a href="' . esc_url( add_query_arg( 'hide_beacon', 'true', admin_url( 'themes.php' ) ) ) . '">Hide</a> the helper button';
	} else {
		$beacon_button .= '<a href="' . esc_url( add_query_arg( 'show_beacon', 'true', admin_url( 'themes.php' ) ) ) . '">Show</a> the helper button';
	}

	$beacon_button .= '</li></ul>';

	$help_panel_content .= $beacon_button;

	return $help_panel_content;
}
