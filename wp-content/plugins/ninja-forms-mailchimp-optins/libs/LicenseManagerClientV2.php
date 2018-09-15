<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LicenseManagerClientV2' ) ) {

	/**
	 * LicenseManagerClientV2 adds license checked updates for this product.
	 */
	class LicenseManagerClientV2 {

		private $api_endpoint;
		private $product_id;
		private $product_name;
		private $type;
		private $text_domain;
		private $plugin_file;

		/**
		 * Initializes the license manager client.
		 *
		 * @param $product_id   string  The text id (slug) of the product
		 * @param $product_name string  The name of the product
		 * @param $text_domain  string  Theme / plugin text domain, used for localizing the settings screens.
		 * @param $api_url      string  The URL to the license manager API
		 * @param $type         string  The type of project this class is being used in ('theme' or 'plugin')
		 * @param $plugin_file  string  The full path to the plugin's main file (only for plugins)
		 */
		public function __construct( $product_id, $product_name, $text_domain, $api_url, $type = 'plugin', $plugin_file = '' ) {
			// Store setup data
			$this->product_id   = $product_id;
			$this->product_name = $product_name;
			$this->text_domain  = $text_domain;
			$this->api_endpoint = $api_url;
			$this->type         = $type;
			$this->plugin_file  = $plugin_file;

			// Add actions required for the class's functionality.
			if ( is_admin() ) {
				if ( $type == 'theme' ) {
					// Check for updates (for themes)
					add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
				} elseif ( $type == 'plugin' ) {
					// Check for updates (for plugins)
					add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );

					// Showing plugin information
					add_filter( 'plugins_api', array( $this, 'plugins_api_handler' ), 10, 3 );
				}
			}
		}

		/**
		 * The filter that checks if there are updates to the theme or plugin
		 * using the WP License Manager API.
		 *
		 * @param $transient  mixed   The transient used for WordPress theme / plugin updates.
		 * @return mixed The transient with our (possible) additions.
		 */
		public function check_for_update( $transient )
		{
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$info = $this->is_update_available();
			if ( $info !== false ) {
				if ( $this->is_theme() ) {
					// Theme update
					$theme_data = wp_get_theme();
					$theme_slug = $theme_data->get_template();
					$transient->response[$theme_slug] = array(
						'new_version' => $info->version,
						'package'     => $info->package_url,
						'url'         => $info->description_url
					);
				} else {
					// Plugin update
					$plugin_slug = plugin_basename( $this->plugin_file );
					$transient->response[$plugin_slug] = (object) array(
						'new_version' => $info->version,
						'package'     => $info->package_url,
						'slug'        => $plugin_slug
					);
				}
			}

			return $transient;
		}

		/**
		 * Checks the license manager to see if there is an update available for this theme.
		 *
		 * @return object|bool If there is an update, returns the license information. Otherwise returns false.
		 */
		public function is_update_available()
		{
			$license_info = $this->get_license_info();
			if ( $this->is_api_error( $license_info ) ) {
				return false;
			}

			if ( version_compare( $license_info->version, $this->get_local_version(), '>' ) ) {
				return $license_info;
			}

			return false;
		}

		/**
		 * Calls the License Manager API to get the license information for the
		 * current product.
		 *
		 * @return object|bool   The product data, or false if API call fails.
		 */
		public function get_license_info()
		{
			$license_key = apply_filters( 'license_manager_client_v2_' . $this->product_id, '' );
			if ( empty( $license_key ) ) {
				return false;
			}

			$info = $this->call_api(
				'info',
				array(
					'p' => $this->product_id,
					'e' => base64_encode( get_option( 'admin_email' ) ),
					'l' => $license_key
				)
			);

			return $info;
		}

		/**
		 * A function for the WordPress "plugins_api" filter. Checks if
		 * the user is requesting information about the current plugin and returns
		 * its details if needed.
		 *
		 * This function is called before the Plugins API checks
		 * for plugin information on WordPress.org.
		 *
		 * @param $res bool|object The result object, or false (= default value).
		 * @param $action string The Plugins API action. We're interested in 'plugin_information'.
		 * @param $args array The Plugins API parameters.
		 * @return object The API response.
		 */
		public function plugins_api_handler( $res, $action, $args )
		{
			if ( $action == 'plugin_information' ) {

				// If the request is for this plugin, respond to it
				if ( isset( $args->slug ) && $args->slug == plugin_basename( $this->plugin_file ) ) {
					$info = $this->get_license_info();

					$res = (object) array(
						'name'          => isset( $info->name ) ? $info->name : '',
						'version'       => $info->version,
						'slug'          => $args->slug,
						'download_link' => $info->package_url,
						'tested'        => isset( $info->tested ) ? $info->tested : '',
						'requires'      => isset( $info->requires ) ? $info->requires : '',
						'last_updated'  => isset( $info->last_updated ) ? $info->last_updated : '',
						'homepage'      => isset( $info->description_url ) ? $info->description_url : '',
						'sections'      => array(
							'description' => $info->description,
						),
						'banners'       => array(
							'low'  => isset( $info->banner_low ) ? $info->banner_low : '',
							'high' => isset( $info->banner_high ) ? $info->banner_high : ''
						),
						'external'      => true
					);

					// Add change log tab if the server sent it
					if ( isset( $info->changelog ) ) {
						$res['sections']['changelog'] = $info->changelog;
					}

					return $res;
				}
			}

			return false;
		}

		/**
		 * A shorthand function for checking if we are in a theme or a plugin.
		 *
		 * @return bool True if this is a theme. False if a plugin.
		 */
		private function is_theme()
		{
			return $this->type == 'theme';
		}

		/**
		 * @return string   The theme / plugin version of the local installation.
		 */
		private function get_local_version()
		{
			if ( $this->is_theme() ) {
				$theme_data = wp_get_theme();
				return $theme_data->Version;
			} else {
				$plugin_data = get_plugin_data( $this->plugin_file, false );
				return $plugin_data['Version'];
			}
		}

		/**
		 * Makes a call to the license manager API.
		 *
		 * @param $action String The API method to invoke on the license manager site
		 * @param $params array The parameters for the API call
		 * @return array The API response
		 */
		private function call_api( $action, $params ) {
			$url = $this->api_endpoint . '/' . $action;

			// Append parameters for GET request and send the request
			$url .= '?' . http_build_query( $params );
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$response_body = wp_remote_retrieve_body( $response );
			$result = json_decode( $response_body );

			return $result;
		}

		/**
		 * Checks the API response to see if there was an error.
		 *
		 * @param $response mixed|object The API response to verify
		 * @return bool True if there was an error. Otherwise false.
		 */
		private function is_api_error( $response ) {
			if ( $response === false ) {
				return true;
			}

			if ( ! is_object( $response ) ) {
				return true;
			}

			if ( isset( $response->error ) ) {
				return true;
			}

			return false;
		}

	}

}
