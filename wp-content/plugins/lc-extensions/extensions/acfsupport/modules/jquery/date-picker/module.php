<?php
/**
 * Module Date Picker
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
class ACF_Date_Picker extends DSLC_Module {

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

		$this->module_id = 'ACF_Date_Picker';
		$this->module_title = __( 'Date Picker', 'lc-acf-integration' );
		$this->module_icon = 'calendar';
		$this->module_category = 'ACF - jQuery';
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

			$fields = lcacf_get_all_fields( $id, 'date_picker' );

			if ( ! empty( $fields ) ) {

				foreach ( $fields as $field ) {

					$choices[] = array(
						'label' => $field['label'],
						'value' => $field['value'],
					);
				}
			}
		} else {

			$fields = lcacf_get_all_fields_by_group( 'date_picker' );

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
			 * Styling Options
			 */

			array(
				'label' => __( 'BG Color', 'lc-acf-integration' ),
				'id' => 'css_bg_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
					'affect_on_change_el' => '.lc-acf-module-date-picker',
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
			array(
				'label' => __( 'Box Shadow', 'lc-acf-integration' ),
				'id' => 'css_main_box_shadow',
				'std' => '',
				'type' => 'box_shadow',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'box-shadow',
				'section' => 'styling',
			),

			/**
			 * Typography
			 */

			array(
				'label' => __( 'Text Align', 'lc-acf-integration' ),
				'id' => 'css_typography_text_align',
				'std' => 'left',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'text-align',
				'section' => 'styling',
				'tab' => __( 'Typography', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Font', 'lc-acf-integration' ),
				'id' => 'css_typography_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => __( 'Typography', 'lc-acf-integration' ),
			),
				array(
					'label' => __( 'Color', 'lc-acf-integration' ),
					'id' => 'css_typography_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
				),
				array(
					'label' => __( 'Font Size', 'lc-acf-integration' ),
					'id' => 'css_typography_font_size',
					'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-acf-integration' ),
					'id' => 'css_typography_line_height',
					'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-acf-integration' ),
					'id' => 'css_typography_font_weight',
					'std' => '400',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => '100 - Thin',
							'value' => '100',
						),
						array(
							'label' => '200 - Extra Light',
							'value' => '200',
						),
						array(
							'label' => '300 - Light',
							'value' => '300',
						),
						array(
							'label' => '400 - Normal',
							'value' => '400',
						),
						array(
							'label' => '500 - Medium',
							'value' => '500',
						),
						array(
							'label' => '600 - Semi Bold',
							'value' => '600',
						),
						array(
							'label' => '700 - Bold',
							'value' => '700',
						),
						array(
							'label' => '800 - Extra Bold',
							'value' => '800',
						),
						array(
							'label' => '900 - Black',
							'value' => '900',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-acf-integration' ),
					'id' => 'css_typography_font_family',
					'std' => 'Open Sans',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
				),
				array(
					'label' => __( 'Font Style', 'lc-acf-integration' ),
					'id' => 'css_typography_font_style',
					'std' => 'normal',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'font-style',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
					'choices' => array(
						array(
							'label' => __( 'Normal', 'lc-acf-integration' ),
							'value' => 'normal',
						),
						array(
							'label' => __( 'Italic', 'lc-acf-integration' ),
							'value' => 'italic',
						),
					),
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-acf-integration' ),
					'id' => 'css_typography_letter_spacing',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => __( 'Typography', 'lc-acf-integration' ),
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'id' => 'css_typography_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Text Shadow', 'lc-acf-integration' ),
				'id' => 'css_typography_text_shadow',
				'std' => '',
				'type' => 'text_shadow',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'text-shadow',
				'section' => 'styling',
				'tab' => __( 'Typography', 'lc-acf-integration' ),
			),

			/**
			 * Responsive Tablet
			 */

			array(
				'label' => __( 'Responsive Styling', 'lc-acf-integration' ),
				'id' => 'css_res_t',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'lc-acf-integration' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'lc-acf-integration' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Text Align', 'lc-acf-integration' ),
				'id' => 'css_res_t_text_align',
				'std' => 'left',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'text-align',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Font Size', 'lc-acf-integration' ),
				'id' => 'css_res_t_font_size',
				'std' => '13',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'font-size',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Line Height', 'lc-acf-integration' ),
				'id' => 'css_res_t_line_height',
				'std' => '22',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'line-height',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Margin', 'lc-acf-integration' ),
				'id' => 'css_res_t_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_res_t_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_res_t_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_res_t_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_res_t_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
			array(
				'id' => 'css_res_t_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Padding', 'lc-acf-integration' ),
				'id' => 'css_res_t_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_res_t_padding_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_res_t_padding_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_res_t_padding_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_res_t_padding_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-acf-integration' ),
					'ext' => 'px',
				),
			array(
				'id' => 'css_res_t_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-acf-integration' ),
			),

			/**
			 * Responsive Phone
			 */

			array(
				'label' => __( 'Responsive Styling', 'lc-acf-integration' ),
				'id' => 'css_res_p',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'lc-acf-integration' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'lc-acf-integration' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Text Align', 'lc-acf-integration' ),
				'id' => 'css_res_p_text_align',
				'std' => 'left',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'text-align',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Font Size', 'lc-acf-integration' ),
				'id' => 'css_res_p_font_size',
				'std' => '13',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'font-size',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Line Height', 'lc-acf-integration' ),
				'id' => 'css_res_p_line_height',
				'std' => '22',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-module-date-picker',
				'affect_on_change_rule' => 'line-height',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Margin', 'lc-acf-integration' ),
				'id' => 'css_res_p_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_res_p_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_res_p_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_res_p_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_res_p_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
			array(
				'id' => 'css_res_p_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
			),
			array(
				'label' => __( 'Padding', 'lc-acf-integration' ),
				'id' => 'css_res_p_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
			),
				array(
					'label' => __( 'Top', 'lc-acf-integration' ),
					'id' => 'css_res_p_padding_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-acf-integration' ),
					'id' => 'css_res_p_padding_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-acf-integration' ),
					'id' => 'css_res_p_padding_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-acf-integration' ),
					'id' => 'css_res_p_padding_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-acf-module-date-picker',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-acf-integration' ),
					'ext' => 'px',
				),
			array(
				'id' => 'css_res_p_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-acf-integration' ),
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

		<div class="lc-acf-module-date-picker">
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

add_action( 'dslc_hook_register_modules' , create_function( '', 'return dslc_register_module( "ACF_Date_Picker" );' ) );
