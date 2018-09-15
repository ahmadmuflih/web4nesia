<?php
/**
 * File with Live Composer module registration
 *
 * @package Live Composer - Google Maps
 */

/**
 * Register translation properties
 *
 * @return void
 */
function lcgooglemaps_gmaps_textdomain() {
	load_plugin_textdomain( 'lc_googlemaps', false, LC_GOOGLEMAPS_DIRNAME . '/lang/' );
} add_action( 'init', 'lcgooglemaps_gmaps_textdomain' );

/**
 * Get coordinated from address
 * Thanks to Pippin Williamson for this one https://pippinsplugins.com/
 *
 * @param  string  $address       Address to transform into Lat/Long.
 * @param  boolean $force_refresh Generate again or return the cached one.
 * @return string                 Coordinates or error code.
 */
function lcgooglemaps_address_to_coordinates( $address, $force_refresh = false ) {

	$address_hash = md5( $address );

	$coordinates = get_transient( $address_hash );

	if ( $force_refresh || false === $coordinates ) {

		$args       = array( 'address' => urlencode( $address ), 'sensor' => 'false' );

		// What protocol? wp_remote_get isn't working with url without protocol declared.
		$protocol = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS']  || 443 == $_SERVER['SERVER_PORT']) ? 'https://' : 'http://';
		$url        = esc_url_raw( add_query_arg( $args, $protocol.'maps.googleapis.com/maps/api/geocode/json' ) );
		$response 	= wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return;
		}

		$data = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $data ) ) {
			return;
		}

		if ( 200 == $response['response']['code'] ) {

			$data = json_decode( $data );

			if ( 'OK' === $data->status ) {

				$coordinates = $data->results[0]->geometry->location;

				$cache_value['lat'] 	= $coordinates->lat;
				$cache_value['lng'] 	= $coordinates->lng;
				$cache_value['address'] = (string) $data->results[0]->formatted_address;

				set_transient( $address_hash, $cache_value, 3600 * 24 * 30 );
				$data = $cache_value;

			} elseif ( 'ZERO_RESULTS' === $data->status ) {
				return 'zero_results';
			} elseif ( 'INVALID_REQUEST' === $data->status ) {
				return 'invalid_request';
			} else {
				return 'error';
			}
		} else {
			return 'no_load';
		}
	} else {

		// Return cached results.
		$data = $coordinates;
	} // End if().

	return $data;
}



/**
 * Shortcode needed in the Google Maps module
 * to make sure JavaScript get rendered even when LC cache enabled.
 */
function lcgooglemaps_add_js ( $atts, $content = null ) {

	$google_api_key = '';

	if ( isset( $atts['google_api'] ) ) {
		$google_api_key = $atts['google_api'];
	}

	/**
	 *	Load necessary scripts here instead of 'wp_enqueue_scripts' for better performance.
	 *	See http://scribu.net/wordpress/conditional-script-loading-revisited.html for more details.
	 */
	ob_start();
		wp_enqueue_script( 'sklc-gmaps-api', '//maps.googleapis.com/maps/api/js?key=' . $google_api_key, array(), LC_GOOGLEMAPS_VER, true );
		wp_enqueue_script( 'sklc-gmaps-js', LC_GOOGLEMAPS_URL . 'js/main.js', array(), LC_GOOGLEMAPS_VER, true );
		wp_enqueue_style( 'sklc-gmaps-css', LC_GOOGLEMAPS_URL . 'css/main.css', array(), LC_GOOGLEMAPS_VER );

	$scripts_rendered = ob_get_contents();
	ob_end_clean();

	return $scripts_rendered;

} add_shortcode( 'lcgooglemaps_add_js', 'lcgooglemaps_add_js' );
