<?php
/**
 * File with Live Composer module registration
 *
 * @package Live Composer - Google Maps
 */

	/**
	 * Register Module
	 */
	add_action('dslc_hook_register_modules',
		create_function( '', 'return dslc_register_module( "SKLC_GMaps_Module" );' )
	);

	/**
	 * Module Class
	 */
	class SKLC_GMaps_Module extends DSLC_Module {

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

			$this->module_id = 'SKLC_GMaps_Module';
			$this->module_title = __( 'Map', 'lc_googlemaps' );
			$this->module_icon = 'map-marker';
			$this->module_category = 'Extensions';

		}

		/**
		 * Options
		 */
		function options() {

			$help_google_api = __( 'Google Maps <a href="https://googlegeodevelopers.blogspot.co.za/2016/06/building-for-scale-updates-to-google.html" target="_blank" class="dslca-link">now requires</a> the use of a Google Maps API key to display a map on your site.', 'lc_googlemaps' ) . '<br/>';
			$help_google_api .= __( 'Google Maps API key is free for regular usage.', 'lc_googlemaps' ) . '<br/>';
			$help_google_api .= __( 'Tutorial: <a href="https://livecomposerplugin.com/downloads/google-maps-add-on/#how-to-create-api-key" target="_blank" class="dslca-link">How to create Google Maps API key</a>.', 'lc_googlemaps' ) . '<br/>';

			$options = array(

				array(
					'label' => __( 'Error-Proof Mode', 'lcproext' ),
					'id' => 'error_proof_mode',
					'std' => 'active',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lcproext' ),
							'value' => 'active',
						),
					),
					'help' => __( 'Some JavaScript code and shortcodes can break the page editing.<br> Use <b>Error-Proof Mode</b> to make it work.', 'lcproext' ),
					'visibility' => 'hidden',
				),
				array(
					'label' => __( 'Google Maps API key (REQUIRED)', 'lc_googlemaps' ),
					'id' => 'google-api',
					'std' => '',
					'type' => 'text',
					'help' => $help_google_api,
				),
				array(
					'label' => __( 'Address', 'lc_googlemaps' ),
					'id' => 'address',
					'std' => LC_GOOGLEMAPS_DEFAULT_ADDR,
					'type' => 'text',
				),
				array(
					'label' => __( 'Custom Marker IMG', 'lc_googlemaps' ),
					'id' => 'custommarker',
					'std' => '',
					'type' => 'image',
				),
				array(
					'label' => __( 'Mousewheel Zooming', 'lc_googlemaps' ),
					'id' => 'zooming',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lc_googlemaps' ),
							'value' => 'enabled',
						),
						array(
							'label' => __( 'Disabled', 'lc_googlemaps' ),
							'value' => 'disabled',
						),
					),
				),
				array(
					'label' => __( 'Doubleclick Zooming', 'lc_googlemaps' ),
					'id' => 'dblzooming',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lc_googlemaps' ),
							'value' => 'enabled',
						),
						array(
							'label' => __( 'Disabled', 'lc_googlemaps' ),
							'value' => 'disabled',
						),
					),
				),
				array(
					'label' => __( 'Dragging', 'lc_googlemaps' ),
					'id' => 'dragging',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lc_googlemaps' ),
							'value' => 'enabled',
						),
						array(
							'label' => __( 'Disabled', 'lc_googlemaps' ),
							'value' => 'disabled',
						),
					),
				),
				array(
					'label' => __( 'Map UI', 'lc_googlemaps' ),
					'id' => 'mapui',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lc_googlemaps' ),
							'value' => 'enabled',
						),
						array(
							'label' => __( 'Disabled', 'lc_googlemaps' ),
							'value' => 'disabled',
						),
					),
				),
				array(
					'label' => __( 'Type', 'lc_googlemaps' ),
					'id' => 'maptype',
					'std' => 'ROADMAP',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Hybrid', 'lc_googlemaps' ),
							'value' => 'HYBRID',
						),
						array(
							'label' => __( 'Roadmap', 'lc_googlemaps' ),
							'value' => 'ROADMAP',
						),
						array(
							'label' => __( 'Satellite', 'lc_googlemaps' ),
							'value' => 'SATELLITE',
						),
						array(
							'label' => __( 'Terrain', 'lc_googlemaps' ),
							'value' => 'TERRAIN',
						),
					),
				),
				array(
					'label' => __( 'Height', 'lc_googlemaps' ),
					'id' => 'height',
					'std' => '400',
					'type' => 'text',
				),
				array(
					'label' => __( 'Zoom', 'lc_googlemaps' ),
					'help' => 'Set a value from 1 to 21. Bigger the number bigger the zoom.',
					'id' => 'zoom',
					'std' => '15',
					'type' => 'text',
				),

				/**
				 * Styling
				 */

				array(
					'label' => __( ' BG Color', 'lc_googlemaps' ),
					'id' => 'css_main_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image', 'lc_googlemaps' ),
					'id' => 'css_main_bg_img',
					'std' => '',
					'type' => 'image',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-image',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Repeat', 'lc_googlemaps' ),
					'id' => 'css_main_bg_img_repeat',
					'std' => 'repeat',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Repeat', 'lc_googlemaps' ),
							'value' => 'repeat',
						),
						array(
							'label' => __( 'Repeat Horizontal', 'lc_googlemaps' ),
							'value' => 'repeat-x',
						),
						array(
							'label' => __( 'Repeat Vertical', 'lc_googlemaps' ),
							'value' => 'repeat-y',
						),
						array(
							'label' => __( 'Do NOT Repeat', 'lc_googlemaps' ),
							'value' => 'no-repeat',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-repeat',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Attachment', 'lc_googlemaps' ),
					'id' => 'css_main_bg_img_attch',
					'std' => 'scroll',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Scroll', 'lc_googlemaps' ),
							'value' => 'scroll',
						),
						array(
							'label' => __( 'Fixed', 'lc_googlemaps' ),
							'value' => 'fixed',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-attachment',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Position', 'lc_googlemaps' ),
					'id' => 'css_main_bg_img_pos',
					'std' => 'top left',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Top Left', 'lc_googlemaps' ),
							'value' => 'left top',
						),
						array(
							'label' => __( 'Top Right', 'lc_googlemaps' ),
							'value' => 'right top',
						),
						array(
							'label' => __( 'Top Center', 'lc_googlemaps' ),
							'value' => 'Center Top',
						),
						array(
							'label' => __( 'Center Left', 'lc_googlemaps' ),
							'value' => 'left center',
						),
						array(
							'label' => __( 'Center Right', 'lc_googlemaps' ),
							'value' => 'right center',
						),
						array(
							'label' => __( 'Center', 'lc_googlemaps' ),
							'value' => 'center center',
						),
						array(
							'label' => __( 'Bottom Left', 'lc_googlemaps' ),
							'value' => 'left bottom',
						),
						array(
							'label' => __( 'Bottom Right', 'lc_googlemaps' ),
							'value' => 'right bottom',
						),
						array(
							'label' => __( 'Bottom Center', 'lc_googlemaps' ),
							'value' => 'center bottom',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-position',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Color', 'lc_googlemaps' ),
					'id' => 'css_main_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Width', 'lc_googlemaps' ),
					'id' => 'css_main_border_width',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'lc_googlemaps' ),
					'id' => 'css_main_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc_googlemaps' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc_googlemaps' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc_googlemaps' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc_googlemaps' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Wrapper: Border Radius - Top', 'lc_googlemaps' ),
					'id' => 'css_main_border_radius_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Wrapper: Border Radius - Bottom', 'lc_googlemaps' ),
					'id' => 'css_main_border_radius_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Map: Border Radius - Top', 'lc_googlemaps' ),
					'id' => 'css_main_map_border_radius_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Map: Border Radius - Bottom', 'lc_googlemaps' ),
					'id' => 'css_main_map_border_radius_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc_googlemaps' ),
					'id' => 'css_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Vertical', 'lc_googlemaps' ),
					'id' => 'css_main_padding_vertical',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Horizontal', 'lc_googlemaps' ),
					'id' => 'css_main_padding_horizontal',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
				),

			);

			$options = array_merge( $options, $this->shared_options( 'animation_options' ) );
			$options = array_merge( $options, $this->presets_options() );

			return apply_filters( 'dslc_module_options', $options, $this->module_id );

		}

		/**
		 * Output the module render
		 *
		 * @param  array $options All the plugin options.
		 * @return void
		 */
		function output( $options ) {

			/* Module Start */
			$this->module_start( $options );

			global $dslc_active;

			if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
				$dslc_is_admin = true;
			} else {
				$dslc_is_admin = false;
			}

			// Check if Error-Proof mode activated in module options
			$error_proof_mode = false;
			if ( isset( $options['error_proof_mode'] ) && $options['error_proof_mode'] != ''  ) {
				$error_proof_mode = true;
			}

			// Check if module rendered via ajax call
			$ajax_module_render = true;
			if ( isset( $options['module_render_nonajax'] ) ) {
				$ajax_module_render = false;
			}

			// Decide if we should render the module or wait for the page refresh
			$render_code = true;
			if ( $dslc_is_admin && $error_proof_mode && $ajax_module_render ) {
				$render_code = false;
			}

			$map_error = false;

			// Google Maps doesn't work without Google API anymore!
			if ( ! empty( $options['google-api'] ) && ! empty( $options['address'] ) ) {

				if ( $render_code ) {
					$coordinates = lcgooglemaps_address_to_coordinates( $options['address'] );

					if ( 'zero_results' === $coordinates || 'invalid_request' === $coordinates || 'error' === $coordinates ) {
						$map_error = __( 'Could not find this address. Check it on Google Maps website to make sure it is correct.', 'lc_googlemaps' );
					} elseif ( 'no_load' === $coordinates ) {
						$map_error = __( 'Google API is not responding at the moment. Please try again shortly', 'lc_googlemaps' );
					} else {

						?>
						[lcgooglemaps_add_js google_api="<?php echo $options['google-api']; ?>"]
						<div class="sklc-gmaps-wrapper">
							<div class="sklc-gmaps"
								data-lat="<?php esc_attr_e( $coordinates['lat'] ); ?>"
								data-lng="<?php esc_attr_e( $coordinates['lng'] ); ?>"
								data-zooming="<?php esc_attr_e( $options['zooming'] ); ?>"
								data-dblzooming="<?php esc_attr_e( $options['dblzooming'] ); ?>"
								data-dragging="<?php esc_attr_e( $options['dragging'] ); ?>"
								data-mapui="<?php esc_attr_e( $options['mapui'] ); ?>"
								data-maptype="<?php esc_attr_e( $options['maptype'] ); ?>"
								data-zoom="<?php esc_attr_e( $options['zoom'] ); ?>"
								data-custom-marker="<?php esc_attr_e( $options['custommarker'] ); ?>"
								style="width: 100%; height: <?php esc_attr_e( $options['height'] ); ?>px;"></div>
						</div><!-- .sklc-gmaps-wrapper -->
						<?php

					}
				} else {
					echo '<div class="dslc-notification dslc-green">' . __('Please, save and refresh this page to load the map.', 'lcproext') . '</div>';
				}

			} elseif ( $dslc_is_admin ) {
				$map_error = true;
			}

			if ( $map_error ) {

				$message = __( 'Click to set address and Google key.', 'lc_googlemaps' );	
				$message_color = 'green';

				// Show error message to the website admins only.
				if ( isset( $options['address'] ) && ( LC_GOOGLEMAPS_DEFAULT_ADDR !== $options['address'] ) && empty( $options['google-api'] ) ) {
					$message = __( 'Sorry, Google won\'t show a map without an API key.', 'lc_googlemaps' );	
				} elseif ( ! empty( $options['google-api'] ) && ( empty( $options['address'] ) || isset( $options['address'] ) && ( LC_GOOGLEMAPS_DEFAULT_ADDR === $options['address'] ) ) ) {
					$message = __( 'Have you set the right address?', 'lc_googlemaps' );	
				}

				if ( is_string( $map_error ) ) {
					$message = $map_error;
					$message_color = 'red';
				}


				?>
					<div class="lc-googlemap-empty-map">
						<div class="dslc-notification dslc-<?php echo $message_color; ?>"><?php echo $message; ?> <span class="dslca-module-edit-hook dslca-notification-action"><span class="dslc-icon dslc-icon-location-arrow"></span><?php esc_html_e( 'Edit', 'lc_googlemaps' ); ?></span></div>
					</div>
			
					<style type="text/css">
						#dslc-content .sklc-gmaps-wrapper img {
							max-width: none;
						}

						.lc-googlemap-empty-map {
							background: rgba(0, 0, 0, 0) url("../images/map-placeholder.jpg") no-repeat scroll 0 0 / cover ;
							padding: 120px;
						}

						.lc-googlemap-empty-map:before {
							background: rgba(255, 255, 255, 0.4) none repeat scroll 0 0;
							content: "";
							height: 100%;
							left: 0;
							position: absolute;
							top: 0;
							width: 100%;
						}

						.lc-googlemap-empty-map .dslc-notification {
							margin-bottom: 0;
							text-align: center;
						}

						.lc-googlemap-empty-map .dslca-module-edit-hook.dslca-notification-action {
						background: rgba(255, 255, 255, 0.21);
						border-radius: 4px;
						display: inline-block;
						margin: 8px 4px 4px;
						padding: 1px 9px;
						}

						.lc-googlemap-empty-map .dslca-notification-action {
							cursor: pointer;
						}

						.lc-googlemap-empty-map .dslc-notification  .dslc-icon {
							position: relative;
							left: auto;
							top: auto;
							right: auto;
							margin-right: 8px;
						}

						.dslc-notification.lc-notice-blue {
							background: #5890e5;
							border-color: #5890e5;
							color: #fff;
							text-shadow: 0 1px 1px #5890e5;
						}
					</style>
				<?php

				// wp_enqueue_style( 'sklc-gmaps-css', LC_GOOGLEMAPS_URL . 'css/main.css' );
			}


			/* Module End */
			$this->module_end( $options );

		}
	}
