<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Big Tree Island automatic updates class.
 *
 * Version: 2
 */
if ( !class_exists('Bti_License_Tree') ) {

	class Bti_License_Tree {

		function __construct( $plugin_slug, $plugin_id, $plugin_version ) {

			$this->plugin_slug    = $plugin_slug;
			$this->plugin_id      = $plugin_id;
			$this->plugin_version = $plugin_version;

			add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins' ) );
		}

		/**
		* Method to retrieve plugin details from remote update server.
		*/
		function plugins_api( $false, $action, $args ) {

			// Check if this plugins API is about this plugin
			if( $args->slug != $this->plugin_slug ) {
				return $false;
			}

			// Plugin details
			$args = array(
				'action'      => 'plugin_information',
				'plugin_name' => $this->plugin_id,
				'version'     => $this->plugin_version
			);

			// Send request for detailed information
			$response = $this->api_request( $args );

			return $response;
		}

		/**
		* Method to check if there is a new plugin version available.
		*/
		function update_plugins( $transient ) {

			/**
			* Check if the transient contains the 'checked' information
			* If no, just return its value without hacking it
			*/
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			// Details required for update server
			$current_theme = wp_get_theme();
			$theme_name    = $current_theme->get('Name');
			$theme_version = $current_theme->get('Version');

			$args = array(
				'action'         => 'update_check',
				'plugin_name'    => $this->plugin_id,
				'plugin_slug'    => $this->plugin_slug,
				'plugin_version' => $this->plugin_version,
				'theme_name'     => $theme_name,
				'theme_version'  => $theme_version
			);

			// Send request checking for an update
			$response = $this->api_request( $args );

			// If there is a new version, modify the transient
			if( is_object( $response ) && version_compare( $response->new_version, $transient->checked[$this->plugin_slug], '>' ) ) {
				$transient->response[$this->plugin_slug] = $response;
			}

			return $transient;
		}

		/**
		* Helper function to perform HTTP requests
		*/
		function api_request( $args ) {

			// Send request
			$request = wp_remote_post(
			'http://updates.bigtreeisland.com/api/?' . http_build_query( $args )
			);

			if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) ) {
				return false;
			}

			$response = unserialize( wp_remote_retrieve_body( $request ) );

			if ( is_object( $response ) ) {
				return $response;
			} else {
				return false;
			}
		}

	}

}
