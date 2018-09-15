<?php
/**
 * Module oEmbed
 *
 * @package Live Composer - ACF integration
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Module Class
 */
class ACF_oEmbed extends DSLC_Module {

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
	 *
	 * @var string
	 */
	var $module_category;

	/**
	 * Exclude from main listing
	 *
	 * @var string
	 */
	var $exclude_from_main_listing;

	/**
	 * Construct
	 */
	function __construct() {

		$this->module_id = 'ACF_oEmbed';
		$this->module_title = __( 'oEmbed', 'lc-acf-integration' );
		$this->module_icon = 'code';
		$this->module_category = 'ACF - Content';
		$this->exclude_from_main_listing = true;
	}

	/**
	 * Module options.
	 * Function build array with all the module functionality and styling options.
	 * Based on this array Live Composer builds module settings panel.
	 * – Every array inside $dslc_options means one option = one control.
	 * – Every option should have unique (for this module) id.
	 * – Options divides on "Functionality" and "Styling".
	 * – Styling options start with css_XXXXXXX
	 * – Responsive options start with css_res_t_ (Tablet) or css_res_p_ (Phone)
	 * – Options can be hidden.
	 * – Options can have a default value.
	 * – Options can request refresh from server on change or do live refresh via CSS.
	 *
	 * @return array All the module options in array.
	 */
	function options() {

		if ( isset( $_POST['post_id'] ) ) {

			$post_id = $_POST['post_id'];
			$type = get_post_type( $post_id );

			if ( 'dslc_templates' === $type ) {

				if ( isset( $_POST['dslc_url_vars']['preview_id'] ) ) {
					$id = $_POST['dslc_url_vars']['preview_id'];
				} else {
					$id = '';
				}
			} else {
				$id = $post_id;
			}
		} else {
			$id = '';
		}

		$choices = array();
		$choices[] = array(
			'label' => __( 'Choose field', 'lc-acf-integration' ),
			'value' => 'not_set',
		);

		if ( ! empty( $id ) ) {

			$fields = lcacf_get_all_fields( $id, 'oembed' );

			if ( ! empty( $fields ) ) {

				foreach ( $fields as $field ) {

					$choices[] = array(
						'label' => $field['label'],
						'value' => $field['value'],
					);
				}
			}
		} else {

			$fields = lcacf_get_all_fields_by_group( 'oembed' );

			if ( ! empty( $fields ) ) {

				foreach ( $fields as $field ) {

					$choices[] = array(
						'label' => $field['label'],
						'value' => $field['value'],
					);
				}
			}
		}

		$dslc_options = array(

			array(
				'label' => __( 'Show On', 'lc-acf-integration' ),
				'id' => 'css_show_on',
				'std' => 'desktop tablet phone',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Desktop', 'lc-acf-integration' ),
						'value' => 'desktop',
					),
					array(
						'label' => __( 'Tablet', 'lc-acf-integration' ),
						'value' => 'tablet',
					),
					array(
						'label' => __( 'Phone', 'lc-acf-integration' ),
						'value' => 'phone',
					),
				),
			),
			array(
				'label' => __( 'ACF Field to Display', 'lc-acf-integration' ),
				'id' => 'select_field',
				'std' => 'not_set',
				'type' => 'select',
				'choices' => $choices,
			),

			/**
			 * Styling
			 */

			array(
				'label' => __( 'Align', 'lc-acf-integration' ),
				'id' => 'css_align',
				'std' => 'center',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-oembed',
				'affect_on_change_rule' => 'text-align',
				'section' => 'styling',
			),
			array(
				'label' => __( 'BG Color', 'lc-acf-integration' ),
				'id' => 'css_bg_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-oembed',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Border', 'lc-acf-integration' ),
				'id' => 'css_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
			),
				array(
					'label' => __( 'Color', 'lc-acf-integration' ),
					'id' => 'css_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Width', 'lc-acf-integration' ),
					'id' => 'css_border_width',
					'onlypositive' => true,
					'max' => 10,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'lc-acf-integration' ),
					'id' => 'css_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-acf-integration' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-acf-integration' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-acf-integration' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-acf-integration' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Radius - Top', 'lc-acf-integration' ),
					'id' => 'css_border_radius_top',
					'onlypositive' => true,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Radius - Bottom', 'lc-acf-integration' ),
					'id' => 'css_border_radius_bottom',
					'onlypositive' => true,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
			array(
				'id' => 'css_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Margin', 'lc-acf-integration' ),
				'id' => 'css_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'ext' => 'px',
				),
			array(
				'id' => 'css_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Padding', 'lc-acf-integration' ),
				'id' => 'css_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_padding_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_padding_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_padding_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_padding_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-oembed',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'ext' => 'px',
				),
			array(
				'id' => 'css_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
			),
		);

		$dslc_options = array_merge( $dslc_options, $this->shared_options( 'animation_options', array( 'hover_opts' => false ) ) );
		$dslc_options = array_merge( $dslc_options, $this->presets_options() );

		return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );
	}

	/**
	 * Module HTML output.
	 *
	 * @param  array $options Module options to fill the module template.
	 * @return void
	 */
	function output( $options ) {

		global $dslc_active;

		if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
			$dslc_is_admin = true;
		} else {
			$dslc_is_admin = false;
		}

		$this->module_start( $options );
		/* Module output starts here */
		?>

		<div class="lc-acf-module-oembed">
			<?php

			if ( isset( $_POST['dslc_url_vars'] ) ) {

				if ( isset( $_POST['dslc_url_vars']['preview_id'] ) ) {
					$preview_id = $_POST['dslc_url_vars']['preview_id'];
				} else {
					$preview_id = '';
				}
			} elseif ( isset( $_GET['preview_id'] ) ) {

				$preview_id = $_GET['preview_id'];
			} else {

				$preview_id = '';
			}

			$module_id = $this->module_id;
			$post_id = $options['post_id'];
			$field = $options['select_field'];

			$lcacf_array_real_data = array(
				'module_id' => $module_id,
				'post_id' => $post_id,
				'preview_id' => $preview_id,
				'field' => $field,
				'dslc_is_admin' => $dslc_is_admin,
			);

			$lcacf_array_default_data = array(
				'module_id' => $module_id,
				'field' => $field,
				'dslc_is_admin' => $dslc_is_admin,
			);

			if ( ! $dslc_active ) {

				if ( 'dslc_templates' !== get_post_type( get_the_ID() ) ) {
					lcacf_display_real_data( $lcacf_array_real_data );
				} else {
					if ( is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
						lcacf_display_default_data( $lcacf_array_default_data );
					}
				}
			} elseif ( $dslc_active ) {

				if ( 'dslc_templates' !== get_post_type( $post_id ) ) {
					lcacf_display_real_data( $lcacf_array_real_data );
				} elseif ( ! empty( $preview_id ) ) {
					lcacf_display_real_data( $lcacf_array_real_data );
				} else {
					lcacf_display_default_data( $lcacf_array_default_data );
				}
			}

			?>
		</div>

		<?php
		/* Module output ends here */
		$this->module_end( $options );
	}
}

add_action( 'dslc_hook_register_modules' , create_function( '', 'return dslc_register_module( "ACF_oEmbed" );' ) );
