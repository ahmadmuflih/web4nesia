<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_beforeafter_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'beforeafter',
			'rank' => 40,
			'title' => 'Before/After Image',
			'details' => 'https://livecomposerplugin.com/downloads/beforeafter-image-slider-add-on/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=before-after-slider',
			'description' => 'The best way to highlight visual differences between two images/photos. Useful for redesign projects and architects.',
							
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'beforeafter' ) ) :

		define( 'SPC_BAI_URL', plugin_dir_url( __FILE__ ) );
		define( 'SPC_BAI_ABS', dirname(__FILE__) );

		include SPC_BAI_ABS . '/inc/functions.php';

		add_action('dslc_hook_register_modules',
			create_function('', 'return dslc_register_module( "LC_Before_After_Image" );')
		);

		class LC_Before_After_Image extends DSLC_Module {
				
			// // Module Attributes
			// var $module_id = 'LC_Before_After_Image';
			// var $module_title = __( 'Before/After Image', 'lcproext' );
			// var $module_icon = 'picture';
			// var $module_category = 'Extensions';
			
			function __construct() {

					$this->module_id = 'LC_Before_After_Image';
					$this->module_title = __( 'Before/After Image', 'lcproext' );
					$this->module_icon = 'picture';
					$this->module_category = 'Extensions';

			}
		
			// Module Options
			function options() { 

				// The options array
				$options = array(
					
					array(
						'label' => 'Before Image',
						'id' => 'before_image',
						'std' => '',
						'type' => 'image',
					),
					array(
						'label' => 'After Image',
						'id' => 'after_image',
						'std' => '',
						'type' => 'image',
					),
					array(
						'label' => 'Orientation',
						'id' => 'bai_orientation',
						'std' => 'horizontal',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => 'Horizontal',
								'value' => 'horizontal'
							),
							array(
								'label' => 'Vertical',
								'value' => 'vertical'
							),
						)
					),

					/**
					 * Styling
					 */

					array(
						'label' => __( 'BG Color', 'lcproext' ),
						'id' => 'css_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Border Width', 'lcproext' ),
						'id' => 'css_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Borders', 'lcproext' ),
						'id' => 'css_border_trbl',
						'std' => 'top right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'lcproext' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'lcproext' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'lcproext' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'lcproext' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Border Radius', 'lcproext' ),
						'id' => 'css_border_radius',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai, .spc-bai img',
						'affect_on_change_rule' => 'border-radius',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Margin Bottom', 'lcproext' ),
						'id' => 'css_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'General'
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.spc-bai',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'General'
					),

					/**
					 * Handle
					 */

					array(
						'label' => __( 'Background Color', 'lcproext' ),
						'id' => 'css_handle_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-handle',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_handle_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-handle',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Border Radius', 'lcproext' ),
						'id' => 'css_handle_border_radius',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-handle',
						'affect_on_change_rule' => 'border-radius',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Left Arrow - Color', 'lcproext' ),
						'id' => 'css_l_arrow_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-left-arrow',
						'affect_on_change_rule' => 'border-right-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Right Arrow - Color', 'lcproext' ),
						'id' => 'css_r_arrow_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-right-arrow',
						'affect_on_change_rule' => 'border-left-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Up Arrow - Color', 'lcproext' ),
						'id' => 'css_u_arrow_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-up-arrow',
						'affect_on_change_rule' => 'border-bottom-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Down Arrow - Color', 'lcproext' ),
						'id' => 'css_d_arrow_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-down-arrow',
						'affect_on_change_rule' => 'border-top-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),
					array(
						'label' => __( 'Separator - Color', 'lcproext' ),
						'id' => 'css_sep_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.twentytwenty-handle:before, .twentytwenty-handle:after',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Handle'
					),


				);

				// Return the array
				return apply_filters( 'dslc_module_options', $options, $this->module_id );

			}
		
			// Module Output
			function output( $options ) {

				global $dslc_active;

				if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) )
					$dslc_is_admin = true;
				else
					$dslc_is_admin = false;

				// REQUIRED
				$this->module_start( $options );

				?><div class="spc-bai" data-orientation="<?php echo $options['bai_orientation']; ?>"><?php

					if ( $options['before_image'] && $options['after_image'] ) :

						?><div class="spc-bai-wrapper"><?php
							?><img src="<?php echo $options['before_image']; ?>"><?php
							?><img src="<?php echo $options['after_image']; ?>"><?php
						?></div><?php

					endif;

				?></div><?php

				if ( $dslc_is_admin && ( ! $options['before_image'] || ! $options['after_image'] ) ) :
					?><div class="dslc-notification dslc-red">Edit the module and set the before and after image.</div><?php
				endif;

				// REQUIRED
				$this->module_end( $options );
			}
		}
	
	endif; // If is_extension_active.
}

lcproext_beforeafter_init();
