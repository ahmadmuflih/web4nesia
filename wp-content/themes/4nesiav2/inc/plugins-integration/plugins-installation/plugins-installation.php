<?php
/**
 * Add Custom CSS Style and JS Files on the required plugin installation screen.
 *
 * @return void 	No data returned.
 */

add_action( 'admin_enqueue_scripts', 'lbmn_pluginsinstall_scripts' );

if ( ! function_exists( 'lbmn_pluginsinstall_scripts' ) ) {
	function lbmn_pluginsinstall_scripts( $admin_page_suffix ) {

		if ( 'appearance_page_install-required-plugins' === $admin_page_suffix ) {

			wp_enqueue_style(
				'lbmn_pluginsinstall_css',
				get_template_directory_uri() . '/inc/plugins-integration/plugins-installation/plugins-installation.css',
				false,
				SEOWP_THEME_VER
			);

			wp_enqueue_script(
				'lbmn_pluginsinstall_js',
				get_template_directory_uri() . '/inc/plugins-integration/plugins-installation/plugins-installation.js',
				array( 'jquery' ),
				SEOWP_THEME_VER
			);

			/*
			wp_localize_script(
				'lbmn_custom_wp_admin_script_validate',
				'lbmnajax',
				array(
					'nonce' => wp_create_nonce( 'lbmn_themeinstall_scripts' ),
				)
			);
			*/

			// Live Composer has links to images hard-coded, so before importing
			// media we need to check that the Settings > Media >
			// 'Organize my uploads into month- and year-based folders' unchecked
			// as on demo server. After import is done we set back original state
			// of this setting.
			$setting_original_useyearmonthfolders = get_option( 'uploads_use_yearmonth_folders' );
			update_option( 'uploads_use_yearmonth_folders_backup', $setting_original_useyearmonthfolders );
			update_option( 'uploads_use_yearmonth_folders', 0 );
		}
	}
}
