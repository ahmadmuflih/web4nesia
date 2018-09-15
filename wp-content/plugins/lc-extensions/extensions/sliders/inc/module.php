<?php
/**
 * File with Live Composer module registration
 *
 * @package Live Composer - Master Slider
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

// Disabling the auto-update feature.
add_filter( 'masterslider_disable_auto_update', '__return_true' );

/**
 * Module Class
 */
class LCPROEXT_MasterSlider extends DSLC_Module {

	/**
	 * Unique module id
	 *
	 * @var string
	 */
	var $module_id;

	/**
	 * Module label to show in the page builder
	 *
	 * @var string
	 */
	var $module_title;

	/**
	 * Module icon name (FontAwesome)
	 *
	 * @var string
	 */
	var $module_icon;

	/**
	 * Section in the modules panel that includes this module
	 * Live Composer Extensions should use 'Extensions'
	 *
	 * @var string
	 */
	var $module_category;

	/**
	 * Construct
	 */
	function __construct() {
		$this->module_id = 'LCPROEXT_MasterSlider';
		$this->module_title = __( 'Master Slider', 'lc_masterslider' );
		$this->module_icon = 'picture';
		$this->module_category = 'Extensions';

		if ( ! wp_script_is( 'masterslider-core', 'registered' ) ) {
			if ( class_exists( 'MSP_Frontend_Assets' ) ) {
				$msp_fa = new MSP_Frontend_Assets();
				wp_register_script( 'masterslider-core', $msp_fa->assets_dir . '/js/masterslider.min.js', array( 'jquery', 'jquery-easing' ), $msp_fa->version, true );
				wp_enqueue_script( 'masterslider-core' );
			} else {
				return false;
			}
		}
	}

	/**
	 * Options
	 */
	function options() {

		// Get sliders.
		global $wpdb;
		$table_name = $wpdb->prefix . 'masterslider_sliders';
		$sliders = $wpdb->get_results( "SELECT ID, title FROM $table_name" );
		$slider_choices = array();

		$slider_choices[] = array(
			'label' => __( '-- Select --', 'lc_masterslider' ),
			'value' => 'not_set',
		);

		if ( ! empty( $sliders ) ) {
			foreach ( $sliders as $slider ) {
				$slider_choices[] = array(
					'label' => $slider->title,
					'value' => $slider->ID,
				);
			}
		}

		$dslc_options = array(
			array(
				'label' => __( 'Master Slider', 'lc_masterslider' ),
				'id' => 'masterslider_id',
				'std' => 'not_set',
				'type' => 'select',
				'choices' => $slider_choices,
			),
			array(
				'label' => __( 'Error-Proof Mode', 'lc_masterslider' ),
				'id' => 'error_proof_mode',
				'std' => 'active',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Enabled', 'lc_masterslider' ),
						'value' => 'active',
					),
				),
				'help' => __( 'Some JavaScript code and shortcodes can break the page editing.<br> Use <b>Error-Proof Mode</b> to make it work.', 'lc_masterslider' ),
				'visibility' => 'hidden',
			),
		);

		return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );
	}

	/**
	 * Output the module render
	 *
	 * @param  array $options All the plugin options.
	 * @return void
	 */
	function output( $options ) {

		global $dslc_active;

		if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
			$dslc_is_admin = true;
		} else {
			$dslc_is_admin = false;
		}

		// Check if Error-Proof mode activated in module options.
		$error_proof_mode = false;
		if ( isset( $options['error_proof_mode'] ) && '' !== $options['error_proof_mode'] ) {
			$error_proof_mode = true;
		}

		// Check if module rendered via ajax call.
		$ajax_module_render = true;
		if ( isset( $options['module_render_nonajax'] ) ) {
			$ajax_module_render = false;
		}

		// Decide if we should render the module or wait for the page refresh.
		$render_code = true;
		if ( $dslc_is_admin && $error_proof_mode && $ajax_module_render ) {
			$render_code = false;
		}

		$this->module_start( $options );

		if ( $dslc_is_admin && ( 'not_set' === $options['masterslider_id'] ) ) {
			$output_slider = '<div class="dslc-notification dslc-red">' . __( 'Click the cog icon on the right of this box to choose which slider to show.', 'lc_masterslider' ) . '<span class="dslca-module-edit-hook dslc-icon dslc-icon-cog"></span></span></div>';
		} elseif ( $render_code ) {
			$output_slider = do_shortcode( '[masterslider id="'. $options['masterslider_id'] .'"]' );
		} else {
			$output_slider = '<div class="dslc-notification dslc-green">' . __( 'Save and refresh the page to display the module safely.', 'lc_masterslider' ) . '</div>';
		}

		echo $output_slider;

		$this->module_end( $options );
	}
}

add_action('dslc_hook_register_modules',
	create_function( '', 'return dslc_register_module( "LCPROEXT_MasterSlider" );' )
);
