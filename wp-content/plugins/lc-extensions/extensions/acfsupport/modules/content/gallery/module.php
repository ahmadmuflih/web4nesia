<?php
/**
 * Module Gallery
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
class ACF_Gallery extends DSLC_Module {

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

		$this->module_id = 'ACF_Gallery';
		$this->module_title = __( 'Gallery', 'lc-acf-integration' );
		$this->module_icon = 'picture';
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

			$fields = lcacf_get_all_fields( $id, 'gallery' );

			if ( ! empty( $fields ) ) {

				foreach ( $fields as $field ) {

					$choices[] = array(
						'label' => $field['label'],
						'value' => $field['value'],
					);
				}
			}
		} else {

			$fields = lcacf_get_all_fields_by_group( 'gallery' );

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
						array(
				'label' => __( 'Animation', 'live-composer-page-builder' ),
				'id' => 'animation',
				'std' => 'fade',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Fade', 'live-composer-page-builder' ),
						'value' => 'fade',
					),
					array(
						'label' => __( 'Fade Up Slide', 'live-composer-page-builder' ),
						'value' => 'fadeUp',
					),
					array(
						'label' => __( 'Slide', 'live-composer-page-builder' ),
						'value' => 'false',
					),
					array(
						'label' => __( 'Back Slide', 'live-composer-page-builder' ),
						'value' => 'backSlide',
					),
					array(
						'label' => __( 'Go Down', 'live-composer-page-builder' ),
						'value' => 'goDown',
					),
				),
			),
			array(
				'label' => __( 'Animation Speed (ms)', 'live-composer-page-builder' ),
				'id' => 'animation_speed',
				'std' => '800',
				'type' => 'text',
			),
			array(
				'label' => __( 'Autoplay Speed (ms)', 'live-composer-page-builder' ),
				'id' => 'autoplay',
				'std' => '0',
				'type' => 'text',
			),
			array(
				'label' => __( 'Flexible Height', 'live-composer-page-builder' ),
				'id' => 'flexible_height',
				'std' => 'true',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Enabled', 'live-composer-page-builder' ),
						'value' => 'true',
					),
					array(
						'label' => __( 'Disabled', 'live-composer-page-builder' ),
						'value' => 'false',
					),
				),
			),

			/**
			 * General Styling
			 */

			array(
				'label' => __( 'BG Color', 'live-composer-page-builder' ),
				'id' => 'css_bg_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Border Color', 'live-composer-page-builder' ),
				'id' => 'css_border_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'border-color',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Border Width', 'live-composer-page-builder' ),
				'id' => 'css_border_width',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'border-width',
				'section' => 'styling',
				'ext' => 'px',
			),
			array(
				'label' => __( 'Borders', 'live-composer-page-builder' ),
				'id' => 'css_border_trbl',
				'std' => 'top right bottom left',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top',
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right',
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom',
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left',
					),
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'border-style',
				'section' => 'styling',
			),
			array(
				'label' => __( 'Border Radius - Top', 'live-composer-page-builder' ),
				'id' => 'css_border_radius_top',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
				'section' => 'styling',
				'ext' => 'px',
			),
			array(
				'label' => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
				'id' => 'css_border_radius_bottom',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
				'section' => 'styling',
				'ext' => 'px',
			),
			array(
				'label' => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id' => 'css_margin_bottom',
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'margin-bottom',
				'section' => 'styling',
				'ext' => 'px',
			),
			array(
				'label' => __( 'Minimum Height', 'live-composer-page-builder' ),
				'id' => 'css_min_height',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'min-height',
				'section' => 'styling',
				'ext' => 'px',
				'increment' => 5,
			),
			array(
				'label' => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'styling',
				'ext' => 'px',
			),
			array(
				'label' => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'styling',
				'ext' => 'px',
			),

			/**
			 * Slider
			 */

			array(
				'label' => __( 'BG Color', 'live-composer-page-builder' ),
				'id' => 'css_slider_bg_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Color', 'live-composer-page-builder' ),
				'id' => 'css_slider_border_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'border-color',
				'section' => 'styling',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Width', 'live-composer-page-builder' ),
				'id' => 'css_slider_border_width',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'border-width',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Borders', 'live-composer-page-builder' ),
				'id' => 'css_slider_border_trbl',
				'std' => 'top right bottom left',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top',
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right',
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom',
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left',
					),
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'border-style',
				'section' => 'styling',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Radius - Top', 'live-composer-page-builder' ),
				'id' => 'css_slider_border_radius_top',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
				'id' => 'css_slider_border_radius_bottom',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_slider_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_slider_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Align', 'live-composer-page-builder' ),
				'id' => 'css_slider_align',
				'std' => 'center',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider .dslc-slider-item',
				'affect_on_change_rule' => 'text-align',
				'section' => 'styling',
				'tab' => __( 'slider', 'live-composer-page-builder' ),
			),

			/**
			 * Slides
			 */

			array(
				'label' => __( 'BG Color', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_bg_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Color', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_border_color',
				'std' => '',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'border-color',
				'section' => 'styling',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Width', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_border_width',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'border-width',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Borders', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_border_trbl',
				'std' => 'top right bottom left',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top',
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right',
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom',
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left',
					),
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'border-style',
				'section' => 'styling',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Radius - Top', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_border_radius_top',
				'onlypositive' => true, // Value can't be negative.
				'std' => '5',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item,.lc-acf-gallery-slider-main .dslc-slider-item img',
				'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_border_radius_bottom',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item,.lc-acf-gallery-slider-main .dslc-slider-item img',
				'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_slider_item_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Slider - item', 'live-composer-page-builder' ),
			),

			/**
			 * Navigation
			 */

			array(
				'label' => __( 'Border Radius', 'live-composer-page-builder' ),
				'id' => 'css_circles_bradius',
				'onlypositive' => true, // Value can't be negative.
				'std' => '50',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page span',
				'affect_on_change_rule' => 'border-radius',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Color', 'live-composer-page-builder' ),
				'id' => 'css_circles_color',
				'std' => '#b9b9b9',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page span',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Color - Active', 'live-composer-page-builder' ),
				'id' => 'css_circles_color_active',
				'std' => '#5890e5',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page.active span',
				'affect_on_change_rule' => 'background-color',
				'section' => 'styling',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Margin Vertical', 'live-composer-page-builder' ),
				'id' => 'css_circles_margin_top',
				'std' => '20',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-controls',
				'affect_on_change_rule' => 'margin',
				'section' => 'styling',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Size', 'live-composer-page-builder' ),
				'id' => 'css_circles_size',
				'std' => '7',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page span',
				'affect_on_change_rule' => 'width,height',
				'section' => 'styling',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Spacing', 'live-composer-page-builder' ),
				'id' => 'css_circles_spacing',
				'std' => '3',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page',
				'affect_on_change_rule' => 'margin-left,margin-right',
				'section' => 'styling',
				'tab' => __( 'Navigation', 'live-composer-page-builder' ),
				'ext' => 'px',
			),

			/**
			 * Responsive Tablet
			 */

			array(
				'label' => __( 'Responsive Styling', 'live-composer-page-builder' ),
				'id' => 'css_res_t',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'live-composer-page-builder' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'live-composer-page-builder' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id' => 'css_res_t_margin_bottom',
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'margin-bottom',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_t_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_t_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Slider - Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_t_slider_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider - Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_t_slider_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider Item - Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_t_slider_item_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider Item - Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_t_slider_item_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Nav - Margin Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_t_circles_margin_top',
				'std' => '20',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-controls',
				'affect_on_change_rule' => 'margin',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Nav - Size', 'live-composer-page-builder' ),
				'id' => 'css_res_t_circles_size',
				'std' => '7',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page span',
				'affect_on_change_rule' => 'width,height',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Nav - Spacing', 'live-composer-page-builder' ),
				'id' => 'css_res_t_circles_spacing',
				'std' => '3',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page',
				'affect_on_change_rule' => 'margin-left,margin-right',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'live-composer-page-builder' ),
				'ext' => 'px',
			),

			/**
			 * Responsive phone
			 */

			array(
				'label' => __( 'Responsive Styling', 'live-composer-page-builder' ),
				'id' => 'css_res_p',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'live-composer-page-builder' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'live-composer-page-builder' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id' => 'css_res_p_margin_bottom',
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'margin-bottom',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_p_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_p_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Slider - Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_p_slider_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider - Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_p_slider_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider Item - Padding Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_p_slider_item_padding_vertical',
				'onlypositive' => true, // Value can't be negative.
				'max' => 600,
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Slider Item - Padding Horizontal', 'live-composer-page-builder' ),
				'id' => 'css_res_p_slider_item_padding_horizontal',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lc-acf-gallery-slider-main .dslc-slider-item',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section' => 'responsive',
				'ext' => 'px',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
			),
			array(
				'label' => __( 'Nav - Margin Vertical', 'live-composer-page-builder' ),
				'id' => 'css_res_p_circles_margin_top',
				'std' => '20',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-controls',
				'affect_on_change_rule' => 'margin',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Nav - Size', 'live-composer-page-builder' ),
				'id' => 'css_res_p_circles_size',
				'std' => '7',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page span',
				'affect_on_change_rule' => 'width,height',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),
			array(
				'label' => __( 'Nav - Spacing', 'live-composer-page-builder' ),
				'id' => 'css_res_p_circles_spacing',
				'std' => '3',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.owl-pagination .owl-page',
				'affect_on_change_rule' => 'margin-left,margin-right',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'live-composer-page-builder' ),
				'ext' => 'px',
			),

		);

		$dslc_options = array_merge( $dslc_options, $this->shared_options( 'animation_options', array(
			'hover_opts' => false,
		) ) );
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

		<div class="lc-acf-gallery-slider">
			<div class="lc-acf-gallery-slider-main">
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
			$animation = $options['animation'];
			$animation_speed = $options['animation_speed'];
			$autoplay = $options['autoplay'];
			$flexible_height = $options['flexible_height'];

			$lcacf_array_real_data = array(
				'module_id' => $module_id,
				'post_id' => $post_id,
				'preview_id' => $preview_id,
				'field' => $field,
				'dslc_is_admin' => $dslc_is_admin,
				'animation' => $animation,
				'animation_speed' => $animation_speed,
				'autoplay' => $autoplay,
				'flexible_height' => $flexible_height,
			);

			$lcacf_array_default_data = array(
				'module_id' => $module_id,
				'field' => $field,
				'dslc_is_admin' => $dslc_is_admin,
				'animation' => $animation,
				'animation_speed' => $animation_speed,
				'autoplay' => $autoplay,
				'flexible_height' => $flexible_height,
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
			</div><!-- .lc-acf-gallery-slider-main -->
		</div><!-- .lc-acf-gallery-slider -->

		<?php
		/* Module output ends here */
		$this->module_end( $options );
	}
}

add_action( 'dslc_hook_register_modules' , create_function( '', 'return dslc_register_module( "ACF_Gallery" );' ) );
