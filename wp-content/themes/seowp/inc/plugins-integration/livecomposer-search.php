<?php
/**
 * Search enabler for the Live Composer powered pages
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Problem: by default Live Composer save all the content
 * in the 'dslc_code' custom field. The data is serialized and compressed.
 *
 * Solution: on each post update 'save_post' extract content parts
 * of LC modules and save them as a plain text in 'dslc_search_content'
 * meta box. Then modify WP search so it goes through this custom field too
 * each time it search through the database.
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

// Check if Live Composer is active
if ( defined( 'DS_LIVE_COMPOSER_URL' ) ) {


	// On each post update extract LC modules content ans save it as plain text
	// into 'dslc_search_content' custom field
	add_action( 'save_post', 'lbmn_save_lc_content_as_meta' );
	function lbmn_save_lc_content_as_meta( $post_id ) {

		$dslc_code_content = get_post_meta( $post_id, 'dslc_code', true );
		if ( $dslc_code_content ) {
			$raw_lc_code              = get_post_meta( $post_id, 'dslc_code', true );
			$lc_code_decoded          = array();
			$lc_code_serialized_parts = array();

			// var_dump($raw_lc_code);

			preg_match_all( "/\[dslc_module[a-z\=\"\040]*\]([A-Za-z0-9+\/\=]*)?\[\/dslc_module\]+/", $raw_lc_code, $lc_code_serialized_parts, PREG_SET_ORDER );

			foreach ( $lc_code_serialized_parts as $module_code_serialized ) {

				// var_dump($module_code_serialized);

				$module_code_serialized = $module_code_serialized[1];
				// $module_code_serialized = str_replace('[dslc_module]', '', $module_code_serialized);
				// $module_code_serialized = str_replace('[/dslc_module]', '', $module_code_serialized);
				$decoded_temp = maybe_unserialize( base64_decode( $module_code_serialized ) );

				// lbmn_debug_console( $decoded_temp );

				// Recreate modules titles output
				if ( isset( $decoded_temp['title'] ) ) {
					if ( $decoded_temp['module_id'] == 'DSLC_Info_Box' ) {
						$lc_code_decoded[] = '<h4>' . $decoded_temp['title'] . '</h4>';
					}
				}

				// Recreate modules content output
				if ( isset( $decoded_temp['content'] ) ) {
					$lc_code_decoded[] = $decoded_temp['content'];
				}

				// Recreate image output
				if ( $decoded_temp['module_id'] == 'DSLC_Image' ) {

					$image_output = $img_link_type = $img_image_alt = $img_image_title = $img_link_url = '';

					if ( isset( $decoded_temp['link_type'] ) ) {
						$img_link_type = $decoded_temp['link_type'];
					}

					if ( isset( $decoded_temp['link_url'] ) ) {
						$img_link_url = $decoded_temp['link_url'];
					}

					if ( isset( $decoded_temp['image_alt'] ) ) {
						$img_image_alt = $decoded_temp['image_alt'];
					}

					if ( isset( $decoded_temp['image_title'] ) ) {
						$img_image_title = $decoded_temp['image_title'];
					}


					if ( $img_link_type !== 'none' || $img_link_type !== '' ) {
						$image_output .= '<a href="' . $img_link_url . '">';
					}
					$image_output .= '<img src="#" alt="' . $img_image_alt . '" title="' . $img_image_title . '" />';

					if ( $img_link_type !== 'none' || $img_link_type !== '' ) {
						$image_output .= '</a>';
					}

					$lc_code_decoded[] = $image_output;
				}

				// Recreate button output
				if ( $decoded_temp['module_id'] == 'DSLC_Button' ) {
					$button_output  = $btn_button_text = '';
					$btn_button_url = '#';

					if ( isset( $decoded_temp['button_text'] ) ) {
						$btn_button_text = $decoded_temp['button_text'];
					}

					if ( isset( $decoded_temp['button_url'] ) ) {
						$btn_button_url = $decoded_temp['button_url'];
					}

					$button_output .= '<a href="' . $btn_button_url . '">' . $btn_button_text . '</a>';

					$lc_code_decoded[] = $button_output;
				}

			}

			// var_dump($lc_code_decoded);


			if ( ! empty( $lc_code_decoded ) ) {
				$lc_code_decoded = implode( " ", $lc_code_decoded );
				// echo "$lc_code_decoded";
				update_post_meta( $post_id, 'dslc_html_content', $lc_code_decoded );
			}
		}
	}

} // if ( defined( 'DS_LIVE_COMPOSER_URL' ) )