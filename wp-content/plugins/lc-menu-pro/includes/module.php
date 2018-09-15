<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Register Module
 */
add_action( 'dslc_hook_register_modules', 'lcmenupro_init_module' );

function lcmenupro_init_module() {
	return dslc_register_module( 'DSLC_Menu_Pro' );
}

// if ( dslc_is_module_active( 'DSLC_Menu_Pro' ) ) {
// 	include DS_LIVE_COMPOSER_ABS . '/modules/navigation/functions.php';
// }

class DSLC_Menu_Pro extends DSLC_Module {

	var $module_id;
	var $module_title;
	var $module_icon;
	var $module_category;

	function __construct() {
		$this->module_id       = 'DSLC_Menu_Pro';
		$this->module_title    = __( 'Menu PRO', 'lc-menu-pro' );
		$this->module_icon     = 'map-signs';
		$this->module_category = 'Extensions';
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

		$locs = get_registered_nav_menus();

		$loc_choices = array();
		$loc_choices[] = array(
			'label' => __( 'Choose Navigation', 'lc-menu-pro' ),
			'value' => 'not_set',
		);

		if ( ! empty( $locs ) ) {
			foreach ( $locs as $loc_id => $loc_label ) {
				$loc_choices[] = array(
					'label' => $loc_label,
					'value' => $loc_id,
				);
			}
		}

		$str_tab_menu_block = __( 'Menu Block', 'lc-menu-pro' );
		$str_tab_menu_item  = __( 'Menu Item', 'lc-menu-pro' );
		$str_tab_menu_icon  = __( 'Menu Item Icon', 'lc-menu-pro' );

		$str_tab_submenu_block = __( 'Submenu Block', 'lc-menu-pro' );
		$str_tab_submenu_item  = __( 'Submenu Item', 'lc-menu-pro' );
		$str_tab_submenu_icon = __( 'Submenu Item Icon', 'lc-menu-pro' );

		$str_tab_submenu_column = __( 'Submenu Columns', 'lc-menu-pro' );
		$str_tab_submenu_column_item = __( 'Item In Columns', 'lc-menu-pro' );
		$str_tab_submenu_item_title = __( 'Title', 'lc-menu-pro' );
		$str_tab_submenu_item_description = __( 'Description', 'lc-menu-pro' );
		$str_tab_submenu_item_special = __( 'Special Text', 'lc-menu-pro' );

		$str_tab_mobile_menu  = __( 'Mobile Menu', 'lc-menu-pro' );
		$str_tab_mobile_menu_toggle  = __( 'Hamburger Icon', 'lc-menu-pro' );

		$dslc_options = array(

			/**
			 * Functionality
			 */

			array(
				'label' => __( 'Show On', 'lc-menu-pro' ),
				'id' => 'css_show_on',
				'std' => 'desktop tablet phone',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Desktop', 'lc-menu-pro' ),
						'value' => 'desktop',
					),
					array(
						'label' => __( 'Tablet', 'lc-menu-pro' ),
						'value' => 'tablet',
					),
					array(
						'label' => __( 'Phone', 'lc-menu-pro' ),
						'value' => 'phone',
					),
				),
			),
			array(
				'label' => __( 'Navigation', 'lc-menu-pro' ),
				'id' => 'location',
				'std' => 'not_set',
				'type' => 'select',
				'choices' => $loc_choices,
				'help' => __( 'The locations from the theme will be shown here but you can register your own in <br>WP Admin > Live Composer > Navigation.', 'lc-menu-pro' ),
			),

			array(
				'label' => __( 'Mobile Navigation', 'lc-menu-pro' ),
				'id' => 'location_mobile',
				'std' => 'not_set',
				'type' => 'select',
				'choices' => $loc_choices,
				'help' => __( 'The locations from the theme will be shown here but you can register your own in <br>WP Admin > Live Composer > Navigation.', 'lc-menu-pro' ),
			),

			array(
				'label' => __( 'Mobile Menu Logo', 'lc-menu-pro' ),
				'id' => 'mobile_logo',
				'std' => '',
				'type' => 'image',
			),

			/**
			 * Styling
			 */

			array(
				'label' => __( 'Items – Align', 'lc-menu-pro' ),
				'id' => 'css_main_align',
				'std' => 'flex-end',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Left', 'lc-menu-pro' ),
						'value' => 'flex-start',
					),
					array(
						'label' => __( 'Right', 'lc-menu-pro' ),
						'value' => 'flex-end',
					),
					array(
						'label' => __( 'Center', 'lc-menu-pro' ),
						'value' => 'center',
					),
					array(
						'label' => __( 'Space Between', 'lc-menu-pro' ),
						'value' => 'space-between',
					),
					/*array(
						'label' => __( 'Space Around', 'lc-menu-pro' ),
						'value' => 'space-around',
					),*/
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-navigation',
				'affect_on_change_rule' => 'justify-content',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
			array(
				'label' => __( 'Background', 'lc-menu-pro' ),
				'id' => 'css_main_bg_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
				array(
					'label' => __( 'BG Color', 'lc-menu-pro' ),
					'id' => 'css_main_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'BG Image', 'lc-menu-pro' ),
					'id' => 'css_main_bg_img',
					'std' => '',
					'type' => 'image',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-image',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'BG Image Repeat', 'lc-menu-pro' ),
					'id' => 'css_main_bg_img_repeat',
					'std' => 'repeat',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Repeat', 'lc-menu-pro' ),
							'value' => 'repeat',
						),
						array(
							'label' => __( 'Repeat Horizontal', 'lc-menu-pro' ),
							'value' => 'repeat-x',
						),
						array(
							'label' => __( 'Repeat Vertical', 'lc-menu-pro' ),
							'value' => 'repeat-y',
						),
						array(
							'label' => __( 'Do NOT Repeat', 'lc-menu-pro' ),
							'value' => 'no-repeat',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-repeat',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'BG Image Attachment', 'lc-menu-pro' ),
					'id' => 'css_main_bg_img_attch',
					'std' => 'scroll',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Scroll', 'lc-menu-pro' ),
							'value' => 'scroll',
						),
						array(
							'label' => __( 'Fixed', 'lc-menu-pro' ),
							'value' => 'fixed',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-attachment',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'BG Image Position', 'lc-menu-pro' ),
					'id' => 'css_main_bg_img_pos',
					'std' => 'top left',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Top Left', 'lc-menu-pro' ),
							'value' => 'left top',
						),
						array(
							'label' => __( 'Top Right', 'lc-menu-pro' ),
							'value' => 'right top',
						),
						array(
							'label' => __( 'Top Center', 'lc-menu-pro' ),
							'value' => 'Center Top',
						),
						array(
							'label' => __( 'Center Left', 'lc-menu-pro' ),
							'value' => 'left center',
						),
						array(
							'label' => __( 'Center Right', 'lc-menu-pro' ),
							'value' => 'right center',
						),
						array(
							'label' => __( 'Center', 'lc-menu-pro' ),
							'value' => 'center center',
						),
						array(
							'label' => __( 'Bottom Left', 'lc-menu-pro' ),
							'value' => 'left bottom',
						),
						array(
							'label' => __( 'Bottom Right', 'lc-menu-pro' ),
							'value' => 'right bottom',
						),
						array(
							'label' => __( 'Bottom Center', 'lc-menu-pro' ),
							'value' => 'center bottom',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-position',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'BG Image Size', 'lc-menu-pro' ),
					'id' => 'bg_image_size',
					'std' => 'auto',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'background-size',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Original', 'lc-menu-pro' ),
							'value' => 'auto',
						),
						array(
							'label' => __( 'Cover', 'lc-menu-pro' ),
							'value' => 'cover',
						),
						array(
							'label' => __( 'Contain', 'lc-menu-pro' ),
							'value' => 'contain',
						),
					),
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
			array(
				'label' => __( 'Background', 'lc-menu-pro' ),
				'id' => 'css_main_bg_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_main_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
				array(
					'label' => __( 'Border Color', 'lc-menu-pro' ),
					'id' => 'css_main_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'Border Width', 'lc-menu-pro' ),
					'id' => 'css_main_border_width',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'lc-menu-pro' ),
					'id' => 'css_main_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-menu-pro' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-menu-pro' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-menu-pro' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-menu-pro' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
				),
				array(
					'label' => __( 'Border Radius - Top', 'lc-menu-pro' ),
					'id' => 'css_main_border_radius_top',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'lc-menu-pro' ),
					'id' => 'css_main_border_radius_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_main_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_main_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
				array(
					'label' => __( 'Top', 'lc-menu-pro' ),
					'id' => 'css_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-menu-pro' ),
					'id' => 'css_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-menu-pro' ),
					'id' => 'css_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-menu-pro' ),
					'id' => 'css_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_main_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_main_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_main_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_main_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_main_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_main_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_menu_block,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_main_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
			),
			array(
				'label' => __( 'Minimum Height', 'lc-menu-pro' ),
				'id' => 'css_min_height',
				'onlypositive' => true, // Value can't be negative.
				'std' => '0',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-inner',
				'affect_on_change_rule' => 'min-height',
				'section' => 'styling',
				'tab' => $str_tab_menu_block,
				'ext' => 'px',
				'increment' => 5,
			),

			/**
			 * Styling - Item
			 */

			array(
				'label' => __( 'Spacing (sides)', 'lc-menu-pro' ),
				'id' => 'css_item_spacing',
				'std' => '1',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.menu > li',
				'affect_on_change_rule' => 'margin-left,margin-right',
				'section' => 'styling',
				'ext' => 'px',
				'tab' => $str_tab_menu_item,
			),
			array(
				'label' => __( 'Link Color', 'lc-menu-pro' ),
				'id' => 'css_item_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_item_color',
					'std' => 'rgba(0,0,0,0.9)',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner li a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_item_color_hover',
					'std' => '#1f9be8',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner li a:hover, .lcmenupro-inner li:hover a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Color - Active', 'lc-menu-pro' ),
					'id' => 'css_item_color_active',
					'std' => '#1f9be8',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'li.current-menu-item a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
			array(
				'label' => __( 'Link Color', 'lc-menu-pro' ),
				'id' => 'css_item_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),

			array(
				'label' => __( 'Background', 'lc-menu-pro' ),
				'id' => 'css_item_bg_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'BG Color', 'lc-menu-pro' ),
					'id' => 'css_item_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'BG Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_item_bg_color_hover',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:hover',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'BG Color - Active', 'lc-menu-pro' ),
					'id' => 'css_item_bg_color_active',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.current-menu-item',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_item_bg_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),

			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_item_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_item_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '16',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_item_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '24',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > a',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_item_font_weight',
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
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_item_font_family',
					'std' => 'Roboto',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > a, .lcmenupro-mobile-menu a',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_item_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_item_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_item_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_item_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_item_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					// 'affect_on_change_el' => '.menu > li > a',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_item_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					// 'affect_on_change_el' => '.menu > li > a',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_item_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					// 'affect_on_change_el' => '.menu > li > a',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_item_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					// 'affect_on_change_el' => '.menu > li > a',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_item_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),

			array(
				'label' => __( 'Chevron (Dropdown Arrow Icon)', 'lc-menu-pro' ),
				'id' => 'css_item_chevron_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'Enable/Disable', 'lc-menu-pro' ),
					'id' => 'css_item_chevron_display',
					'std' => 'none',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.dslc-navigation-arrow',
					'affect_on_change_rule' => 'display',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'lc-menu-pro' ),
							'value' => 'inline-block',
						),
						array(
							'label' => __( 'Disabled', 'lc-menu-pro' ),
							'value' => 'none',
						),
					),
				),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_item_chevron_color',
					'std' => '#555555',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.dslc-navigation-arrow',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Size', 'lc-menu-pro' ),
					'id' => 'css_item_chevron_size',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.dslc-navigation-arrow',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_item_chevron_spacing',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.dslc-navigation-arrow',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_item_chevron_spacing_right',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.dslc-navigation-arrow',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Chevron (Dropdown Arrow Icon)', 'lc-menu-pro' ),
				'id' => 'css_item_chevron_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),

			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_item_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),
				array(
					'label' => __( 'Border Color', 'lc-menu-pro' ),
					'id' => 'css_item_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Border Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_item_border_color_hover',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:hover',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Border Color - Active', 'lc-menu-pro' ),
					'id' => 'css_item_border_color_active',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.current-menu-item',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Border Width', 'lc-menu-pro' ),
					'id' => 'css_item_border_width',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Borders', 'lc-menu-pro' ),
					'id' => 'css_item_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-menu-pro' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-menu-pro' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-menu-pro' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-menu-pro' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Border Radius - Top', 'lc-menu-pro' ),
					'id' => 'css_item_border_radius_top',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_menu_item,
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'lc-menu-pro' ),
					'id' => 'css_item_border_radius_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_menu_item,
				),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_item_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_item,
			),

			/**
			 * Icon
			 */

			array(
				'label' => __( 'Icon Size', 'lc-menu-pro' ),
				'id' => 'css_icon_size',
				'onlypositive' => true, // Value can't be negative.
				'std' => '17',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
				'affect_on_change_rule' => 'font-size, width, height',
				'section' => 'styling',
				'tab' => $str_tab_menu_icon,
				'min' => 1,
				'max' => 50,
				'ext' => 'px',
			),

			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_icon_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_icon,
			),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_icon_color',
					'std' => '#909497',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
				),
				array(
					'label' => __( 'Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_icon_color_hover',
					'std' => '#56aee3',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:hover::before, .menu-item[class*=' dslc-icon-']:hover::before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
				),
			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_icon_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_icon,
			),

			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_icon_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_menu_icon,
			),
				array(
					'label' => __( 'Margin Top', 'lc-menu-pro' ),
					'id' => 'css_icon_margin_top',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
					'max' => 20,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_icon_margin_right',
					// 'onlypositive' => true, // Value can't be negative.
					'max' => 20,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_icon_margin_bottom',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
					'max' => 20,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_icon_margin_left',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_menu_icon,
					'max' => 20,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_icon_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_menu_icon,
			),

			/**
			 * Subnav
			 */

			array(
				'label' => __( 'Dropdown Preview', 'lc-menu-pro' ),
				'label_alt' => __( 'Show Dropdown', 'lc-menu-pro' ),
				'help' => __( 'Click multiple times on the button to change between dropdown.' ),
				'id' => 'css_toggle_dropdown',
				'std' => '',
				'type' => 'button',
				'refresh_on_change' => false,
				'advanced_action' => 'dslc_show_dropdown()',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
			array(
				'label' => __( 'Dropdown Direction', 'lc-menu-pro' ),
				'id' => 'css_subnav_position',
				'std' => 'left',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Left', 'lc-menu-pro' ),
						'value' => 'left',
					),
					array(
						'label' => __( 'Center', 'lc-menu-pro' ),
						'value' => 'center',
					),
					array(
						'label' => __( 'Right', 'lc-menu-pro' ),
						'value' => 'right',
					),
				),
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
/* @todo: needs more work.
			array(
				'label' => __( 'Align', 'lc-menu-pro' ),
				'id' => 'css_subnav_align',
				'std' => 'left',
				'type' => 'text_align',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.menu ul.sub-menu',
				'affect_on_change_rule' => 'text-align',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
*/

/*
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
*/
			array(
				'label' => __( 'Background', 'lc-menu-pro' ),
				'id' => 'css_subnav_bg_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
				array(
					'label' => __( 'BG Color', 'lc-menu-pro' ),
					'id' => 'css_subnav_bg_color',
					'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'BG Image', 'lc-menu-pro' ),
					'id' => 'css_subnav_bg_img',
					'std' => '',
					'type' => 'image',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu',
					'affect_on_change_rule' => 'background-image',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'BG Image Repeat', 'lc-menu-pro' ),
					'id' => 'css_subnav_bg_img_repeat',
					'std' => 'repeat',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Repeat', 'lc-menu-pro' ),
							'value' => 'repeat',
						),
						array(
							'label' => __( 'Repeat Horizontal', 'lc-menu-pro' ),
							'value' => 'repeat-x',
						),
						array(
							'label' => __( 'Repeat Vertical', 'lc-menu-pro' ),
							'value' => 'repeat-y',
						),
						array(
							'label' => __( 'Do NOT Repeat', 'lc-menu-pro' ),
							'value' => 'no-repeat',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu',
					'affect_on_change_rule' => 'background-repeat',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'BG Image Attachment', 'lc-menu-pro' ),
					'id' => 'css_subnav_bg_img_attch',
					'std' => 'scroll',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Scroll', 'lc-menu-pro' ),
							'value' => 'scroll',
						),
						array(
							'label' => __( 'Fixed', 'lc-menu-pro' ),
							'value' => 'fixed',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu',
					'affect_on_change_rule' => 'background-attachment',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'BG Image Position', 'lc-menu-pro' ),
					'id' => 'css_subnav_bg_img_pos',
					'std' => 'top left',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Top Left', 'lc-menu-pro' ),
							'value' => 'left top',
						),
						array(
							'label' => __( 'Top Right', 'lc-menu-pro' ),
							'value' => 'right top',
						),
						array(
							'label' => __( 'Top Center', 'lc-menu-pro' ),
							'value' => 'Center Top',
						),
						array(
							'label' => __( 'Center Left', 'lc-menu-pro' ),
							'value' => 'left center',
						),
						array(
							'label' => __( 'Center Right', 'lc-menu-pro' ),
							'value' => 'right center',
						),
						array(
							'label' => __( 'Center', 'lc-menu-pro' ),
							'value' => 'center center',
						),
						array(
							'label' => __( 'Bottom Left', 'lc-menu-pro' ),
							'value' => 'left bottom',
						),
						array(
							'label' => __( 'Bottom Right', 'lc-menu-pro' ),
							'value' => 'right bottom',
						),
						array(
							'label' => __( 'Bottom Center', 'lc-menu-pro' ),
							'value' => 'center bottom',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu',
					'affect_on_change_rule' => 'background-position',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
			array(
				'label' => __( 'Background', 'lc-menu-pro' ),
				'id' => 'css_subnav_bg_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),

			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),
				array(
					'label' => __( 'Border Color', 'lc-menu-pro' ),
					'id' => 'css_subnav_border_color',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'Border Width', 'lc-menu-pro' ),
					'id' => 'css_subnav_border_width',
					'onlypositive' => true, // Value can't be negative.
					'max' => 10,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'Borders', 'lc-menu-pro' ),
					'id' => 'css_subnav_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-menu-pro' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-menu-pro' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-menu-pro' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-menu-pro' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'Border Radius - Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_border_radius_top',
					'onlypositive' => true, // Value can't be negative.
					'std' => '4',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_block,
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_border_radius_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '4',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li > ul.sub-menu, .menu > li:not(.menu-type-columns) ul.sub-menu',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_block,
				),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_block,
			),

			/**
			 * Styling - Submenu > Item
			 */
			array(
				'label' => __( 'Text Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_color',
					'std' => '#909497',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) .sub-menu li > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_color_hover',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) .sub-menu li:hover > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_color_active',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) .sub-menu li.current-menu-item > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
			array(
				'label' => __( 'Text Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),


			array(
				'label' => __( 'Background Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_bg_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					//'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info)',
					// .menu > li:not(.menu-type-columns)
					// '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a'
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li:not(.lcmenu-additional-info)',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_bg_color_hover',
					'std' => '#56aee3',
					'type' => 'color',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info):hover',
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li:not(.lcmenu-additional-info):hover',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_bg_color_active',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info).current-menu-item',
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li:not(.lcmenu-additional-info).current-menu-item',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
			array(
				'label' => __( 'Background Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_bg_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),


			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '6',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '6',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),


			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '21',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_font_weight',
					'std' => '300',
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
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_font_family',
					'std' => 'Roboto',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li a',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),

			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),
				array(
					'label' => __( 'Border Color', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_color',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Border Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_color_hover',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li:hover',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Border Color - Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_color_active',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li.current-menu-item',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Border Width', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_width',
					'onlypositive' => true, // Value can't be negative.
					'max' => 10,
					'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Borders', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_trbl',
					'std' => 'bottom',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-menu-pro' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-menu-pro' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-menu-pro' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-menu-pro' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Border Radius - Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_radius_top',
					'onlypositive' => true, // Value can't be negative.
					'std' => '2',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_item,
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_border_radius_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '2',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li:not(.menu-type-columns) li',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_item,
				),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item,
			),

			/**
			 * SubMenu > Icon
			 */

			array(
				'label' => __( 'Icon Size', 'lc-menu-pro' ),
				'id' => 'css_subnav_icon_size',
				'onlypositive' => true, // Value can't be negative.
				'std' => '17',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
				'affect_on_change_rule' => 'font-size, width, height',
				'section' => 'styling',
				'tab' => $str_tab_submenu_icon,
				'min' => 1,
				'max' => 50,
				'ext' => 'px',
			),

			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_subnav_icon_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_icon,
			),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_color',
					'std' => '#909497',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
				),
				array(
					'label' => __( 'Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_color_hover',
					'std' => '#56aee3',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:hover::before, .sub-menu .menu-item[class*=' dslc-icon-']:hover::before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
				),
			array(
				'label' => '',
				'id' => 'css_subnav_icon_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_icon,
			),

			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_icon_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_icon,
			),
				array(
					'label' => __( 'Margin Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_margin_top',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
					'max' => 20,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_margin_right',
					// 'onlypositive' => true, // Value can't be negative.
					'max' => 20,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_margin_bottom',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
					'max' => 20,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_icon_margin_left',
					// 'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".sub-menu .menu-item[class^='dslc-icon-']:before, .sub-menu .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_icon,
					'max' => 20,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_icon_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_icon,
			),

			/**
			 * Styling - Submenu With Columns
			 */
			array(
				'label' => __( 'Dropdown Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_panel_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_panel_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 30,
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_panel_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 30,
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_panel_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column,
					'max' => 30,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_panel_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column,
					'max' => 30,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_panel_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column,
			),

			array(
				'label' => __( 'Columns Spacing', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_spacing',
				'onlypositive' => true, // Value can't be negative.
				'max' => 30,
				'std' => '16',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.menu > li.menu-type-columns > .sub-menu > .menu-item-has-children',
				'affect_on_change_rule' => 'margin-right',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column,
				'ext' => 'px',
			),

			/**
			 * Styling - Submenu Columns Item
			 */
			array(
				'label' => __( 'Text Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_color',
					'std' => '#909497',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.menu-item-has-children) > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_color_hover',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.menu-item-has-children):hover > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_color_active',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.menu-item-has-children).current-menu-item > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
			array(
				'label' => __( 'Text Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),

			array(
				'label' => __( 'Icon Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_icon_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_icon_color',
					'std' => '#909497',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu > li.menu-type-columns .sub-menu .sub-menu li[class^='dslc-icon-']:not(.menu-item-has-children):before, .menu > li.menu-type-columns .sub-menu .sub-menu li[class*=' dslc-icon-']:not(.menu-item-has-children):before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_icon_color_hover',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu > li.menu-type-columns .sub-menu .sub-menu li[class^='dslc-icon-']:not(.menu-item-has-children):hover:before, .menu > li.menu-type-columns .sub-menu .sub-menu li[class*=' dslc-icon-']:not(.menu-item-has-children):hover:before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_icon_color_active',
					'std' => '#ffffff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".menu > li.menu-type-columns .sub-menu .sub-menu li.current-menu-item[class^='dslc-icon-']:not(.menu-item-has-children):before, .menu > li.menu-type-columns .sub-menu .sub-menu li[class*=' dslc-icon-']:not(.menu-item-has-children):before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
			array(
				'label' => '',
				'id' => 'css_subnav_column_item_icon_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),


			array(
				'label' => __( 'Background Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_bg_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					//'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info)',
					// .menu > li:not(.menu-type-columns)
					// '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a'
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.lcmenu-additional-info)',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_bg_color_hover',
					'std' => '#56aee3',
					'type' => 'color',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info):hover',
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.lcmenu-additional-info):hover',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_bg_color_active',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.menu ul li:not(.menu-item-has-children):not(.lcmenu-additional-info).current-menu-item',
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .sub-menu li:not(.lcmenu-additional-info).current-menu-item',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
			array(
				'label' => __( 'Background Colors', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_bg_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),


			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '6',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li:not(.menu-item-has-children)',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li:not(.menu-item-has-children)',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '6',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li:not(.menu-item-has-children)',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li:not(.menu-item-has-children)',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),


			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '21',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_font_weight',
					'std' => '300',
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
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_font_family',
					'std' => 'Roboto',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li a',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),


			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_border_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),
				array(
					'label' => __( 'Border Color', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_color',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Border Color - Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_color_hover',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li:hover',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Border Color - Active', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_color_active',
					'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li.current-menu-item',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Border Width', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_width',
					'onlypositive' => true, // Value can't be negative.
					'max' => 10,
					'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Borders', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_trbl',
					'std' => 'bottom',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-menu-pro' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-menu-pro' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-menu-pro' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-menu-pro' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Border Radius - Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_radius_top',
					'onlypositive' => true, // Value can't be negative.
					'std' => '2',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_column_item,
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_column_item_border_radius_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '2',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns li',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => $str_tab_submenu_column_item,
				),
			array(
				'label' => __( 'Border', 'lc-menu-pro' ),
				'id' => 'css_subnav_column_item_border_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_column_item,
			),

			/**
			 * Subnav Item - Title
			 */

			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),
				array(
					'label' => __( 'Normal', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_color',
					'std' => '#000000',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
				),
				array(
					'label' => __( 'Hover', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_color_hover',
					'std' => '#000000',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a:hover',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
				),
			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),


			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_font_weight',
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
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_font_family',
					'std' => 'Roboto',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_text_transform',
					'std' => 'uppercase',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_letter_spacing',
					'max' => 30,
					'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),

			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),
				array(
					'label' => __( 'Margin Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_margin_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_margin_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_margin_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_title_margin_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu > li.menu-type-columns .sub-menu .menu-item-has-children > a',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_title,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_title_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_title,
			),


			/**
			 * Subnav Item - Description
			 */

			array(
				'label' => __( 'Color', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_description_color',
				'std' => '#b9b6b6',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
				'affect_on_change_rule' => 'color',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_description,
			),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_description_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_description,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '12',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '16',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_font_weight',
					'std' => '300',
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
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_font_family',
					'std' => 'Roboto',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'label' => __( 'Font', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_description_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_description,
			),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_description_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_description,
			),
				array(
					'label' => __( 'Margin Top', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_margin_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '3',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_margin_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_margin_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_subnav_item_description_margin_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu-item-has-children .menu-item-description',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_description,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Margin', 'lc-menu-pro' ),
				'id' => 'css_subnav_item_description_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_description,
			),

			/**
			 * Additional Information
			 */

			array(
				'label' => __( 'Colors', 'lc-menu-pro' ),
				'id' => 'css_additional_info_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
				array(
					'label' => __( 'Title - Color', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_color',
					'std' => '#555555',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
				array(
					'label' => __( 'Description - Color', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_color',
					'std' => '#555555',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
			array(
				'label' => '',
				'id' => 'css_additional_info_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),

			array(
				'label' => __( 'Title - Font', 'lc-menu-pro' ),
				'id' => 'css_additional_info_title_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
				array(
					'label' => __( 'Font Size', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '14',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Line Height', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Weight', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_font_weight',
					'std' => '700',
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
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => '',
				),
				array(
					'label' => __( 'Font Family', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_font_family',
					'std' => 'Montserrat',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
				array(
					'label' => __( 'Text Transform', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
				array(
					'label' => __( 'Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_additional_info_title_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'label' => __( 'Title - Font', 'lc-menu-pro' ),
				'id' => 'css_additional_info_title_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
			array(
				'label' => __( 'Description - Font', 'lc-menu-pro' ),
				'id' => 'css_additional_info_description_font_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
				array(
					'label' => __( 'Description - Font Size', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '14',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Description - Line Height', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_line_height',
					'onlypositive' => true, // Value can't be negative.
					'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Description - Font Weight', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_font_weight',
					'std' => '700',
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
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => '',
				),
				array(
					'label' => __( 'Description - Font Family', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_font_family',
					'std' => 'Montserrat',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
				array(
					'label' => __( 'Description - Text Transform', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_text_transform',
					'std' => 'none',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'None', 'lc-menu-pro' ),
							'value' => 'none',
						),
						array(
							'label' => __( 'Capitalize', 'lc-menu-pro' ),
							'value' => 'capitalize',
						),
						array(
							'label' => __( 'Uppercase', 'lc-menu-pro' ),
							'value' => 'uppercase',
						),
						array(
							'label' => __( 'Lowercase', 'lc-menu-pro' ),
							'value' => 'lowercase',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'text-transform',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
				),
				array(
					'label' => __( 'Description - Letter Spacing', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_letter_spacing',
					'max' => 30,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'letter-spacing',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
				),
			array(
				'label' => __( 'Description - Font', 'lc-menu-pro' ),
				'id' => 'css_additional_info_description_font_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
			array(
				'label' => __( 'Description - Margin', 'lc-menu-pro' ),
				'id' => 'css_additional_info_description_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
				array(
					'label' => __( 'Margin Top', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_margin_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Right', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_margin_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_margin_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Left', 'lc-menu-pro' ),
					'id' => 'css_additional_info_description_margin_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenu-additional-info .menu-item-description',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Description - Margin', 'lc-menu-pro' ),
				'id' => 'css_additional_info_description_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
			array(
				'label' => __( 'Padding', 'lc-menu-pro' ),
				'id' => 'css_additional_info_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),
				array(
					'label' => __( 'Padding Top', 'lc-menu-pro' ),
					'id' => 'css_additional_info_padding_top',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'lc-menu-pro' ),
					'id' => 'css_additional_info_padding_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 600,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Bottom', 'lc-menu-pro' ),
					'id' => 'css_additional_info_padding_bottom',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Left', 'lc-menu-pro' ),
					'id' => 'css_additional_info_padding_left',
					'onlypositive' => true, // Value can't be negative.
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.menu .sub-menu .lcmenu-additional-info > a',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_submenu_item_special,
					'ext' => 'px',
				),
			array(
				'label' => __( 'Description - Margin', 'lc-menu-pro' ),
				'id' => 'css_additional_info_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_submenu_item_special,
			),

			/**
			 * Mobile Menu
			 */

			array(
				'label' => __( 'Mobile Menu Preview', 'lc-menu-pro' ),
				'label_alt' => __( 'Show Mobile Menu', 'lc-menu-pro' ),
				'id' => 'css_toggle_menu_preview',
				'std' => '',
				'type' => 'button',
				'refresh_on_change' => false,
				'advanced_action' => 'dslc_show_menu()',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Colors', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_colors_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Content Overlay', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_overlay_bg_color',
					'std' => 'rgba(0,0,0,0.63)',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-site-overlay',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Menu Background', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_bg_color',
					'std' => '#000',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-inner',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
			array(
				'label' => '',
				'id' => 'css_mobile_menu_colors_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Menu Panel - Padding', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Vertical', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_padding_top',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu',
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-inner',
					'affect_on_change_rule' => 'padding-top, padding-bottom',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Horizontal', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_padding_left',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu',
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-inner',
					'affect_on_change_rule' => 'padding-left, padding-right',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
			array(
				'id' => 'css_mobile_menu_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Mobile Logo', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_logo_padding_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Size', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_logo_width',
					'onlypositive' => true, // Value can't be negative.
					'std' => '100',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-logo img',
					'affect_on_change_rule' => 'width',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'min' => 1,
					'max' => 1000,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Spacing – Top', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_logo_padding_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-logo',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Spacing – Bottom', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_logo_padding_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-logo',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
			array(
				'label' => '',
				'id' => 'css_mobile_menu_logo_padding_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Close Icon', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_icon_close_color_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Size', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_close_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-close-hook .lcmenupro-icon',
					'affect_on_change_rule' => 'height, width',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'min' => 1,
					'max' => 50,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_close_padding',
					'onlypositive' => true, // Value can't be negative.
					'std' => '4',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-close-hook .lcmenupro-icon',
					'affect_on_change_rule' => 'padding',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'min' => 1,
					'max' => 50,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Background Color', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_close_bg_color',
					'std' => 'rgba(94,94,94,0.22)',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-inner .lcmenu-mobile-close-hook',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Icon Color', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_close_color',
					'std' => '#605c5c',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenu-mobile-close-hook .lcmenupro-icon',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
			array(
				'id' => 'css_mobile_menu_icon_close_color_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Menu Item Icon', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_icon_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Size', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '17',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => ".menu-item[class^='dslc-icon-']:before, .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_el' => ".lcmenupro-mobile-navigation .menu-item[class^='dslc-icon-']:before, .lcmenupro-mobile-navigation .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'font-size, width, height',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'min' => 1,
					'max' => 50,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_color',
					'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".lcmenupro-mobile-navigation .menu-item[class^='dslc-icon-']:before, .lcmenupro-mobile-navigation .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Color: Hover', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_color_hover',
					'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".lcmenupro-mobile-navigation .menu-item[class^='dslc-icon-']:hover::before, .lcmenupro-mobile-navigation .menu-item[class*=' dslc-icon-']:hover::before",
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Spacing – Right', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_margin_right',
					'onlypositive' => true, // Value can't be negative.
					'max' => 40,
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".lcmenupro-mobile-navigation .menu-item[class^='dslc-icon-']:before, .lcmenupro-mobile-navigation .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Spacing – Left', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_icon_margin_left',
					'onlypositive' => true, // Value can't be negative.
					'max' => 40,
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".lcmenupro-mobile-navigation .menu-item[class^='dslc-icon-']:before, .lcmenupro-mobile-navigation .menu-item[class*=' dslc-icon-']:before",
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
			array(
				'id' => 'css_mobile_menu_icon_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			array(
				'label' => __( 'Menu Item', 'lc-menu-pro' ),
				'id' => 'css_mobile_menu_item_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),
				array(
					'label' => __( 'Color', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_color',
					'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Color: Active', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_color_active',
					'std' => '#e0e0e0',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu a:active',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_subnav_item_padding_left',
					'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu',
					'affect_on_change_el' => '.lcmenupro-mobile-menu > .menu-item',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Size – Main Items', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '18',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Font Size – Subnav Items', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_subnav_item_font_size',
					'onlypositive' => true, // Value can't be negative.
					'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu ul.sub-menu li a',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Subnav Items – Spacing Left', 'lc-menu-pro' ),
					'id' => 'css_mobile_menu_subnav_item_padding_left',
					'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.lcmenupro-mobile-navigation .lcmenupro-mobile-menu',
					'affect_on_change_el' => '.lcmenupro-mobile-menu .sub-menu',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
					'ext' => 'px',
				),
			array(
				'id' => 'css_mobile_menu_item_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'styling',
				'tab' => $str_tab_mobile_menu,
			),

			// ============================================================

				array(
					'label' => __( 'Visibility', 'lc-menu-pro' ),
					'id' => 'css_mobile_show_ongroup',
					'type' => 'group',
					'action' => 'open',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu_toggle,
				),
					array(
						'id' => 'css_fullmenu_show_on',
						'std' => 'desktop',
						'label' => __( 'Show Full Menu On', 'lc-menu-pro' ),
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => 'Desktop',
								'value' => 'desktop',
							),
							array(
								'label' => 'Tablet',
								'value' => 'tablet',
							),
							array(
								'label' => 'Phone',
								'value' => 'phone',
							),
						),
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
					),

					array(
						'id' => 'css_mobile_toggle_show_on',
						'std' => '',
						'label' => __( 'Show Mobile Toggle Icon On', 'lc-menu-pro' ),
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => 'Desktop',
								'value' => 'desktop',
							),
							array(
								'label' => 'Tablet',
								'value' => 'tablet',
							),
							array(
								'label' => 'Phone',
								'value' => 'phone',
							),
						),
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
					),
				array(
					'id' => 'css_mobile_show_ongroup',
					'type' => 'group',
					'action' => 'close',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu_toggle,
				),

				array(
					'label' => __( 'Toggle Icon', 'lc-menu-pro' ),
					'id' => 'css_menu_toggle_group',
					'type' => 'group',
					'action' => 'open',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu_toggle,
				),
					array(
						'label' => __( 'Icon Size', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_width',
						'onlypositive' => true, // Value can't be negative.
						'std' => '24',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook',
						'affect_on_change_rule' => 'width, height',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
						'min' => 1,
						'max' => 80,
						'ext' => 'px',
					),
					array(
						'label' => __( 'Color', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_color',
						'std' => 'rgba(10,10,10,0.49)',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
					),
					array(
						'label' => __( 'Color: Hover', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_color_hover',
						'std' => 'rgba(10,10,10,0.49)',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook:hover',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
					),

				array(
					'id' => 'css_menu_toggle_group',
					'type' => 'group',
					'action' => 'close',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu,
				),

				array(
					'label' => __( 'Margins', 'lc-menu-pro' ),
					'id' => 'css_menu_toggle_icon_margin_group',
					'type' => 'group',
					'action' => 'open',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu_toggle,
				),
					array(
						'label' => __( 'Top', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_margin_top',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook',
						'affect_on_change_rule' => 'margin-top',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
						'ext' => 'px',
					),
					array(
						'label' => __( 'Right', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_margin_right',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
						'ext' => 'px',
					),
					array(
						'label' => __( 'Left', 'lc-menu-pro' ),
						'id' => 'css_menu_toggle_icon_margin_left',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lcmenu-mobile-hook',
						'affect_on_change_rule' => 'margin-left',
						'section' => 'styling',
						'tab' => $str_tab_mobile_menu_toggle,
						'ext' => 'px',
					),
				array(
					'label' => __( 'Menu Toggle Icon - Margin', 'lc-menu-pro' ),
					'id' => 'css_menu_toggle_icon_margin_group',
					'type' => 'group',
					'action' => 'close',
					'section' => 'styling',
					'tab' => $str_tab_mobile_menu_toggle,
				),
			);

			/**
			 * Responsive Tablet
			 */
/*
			array(
				'label' => __( 'Responsive Styling', 'lc-menu-pro' ),
				'id' => 'css_res_t',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'lc-menu-pro' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'lc-menu-pro' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Preview', 'lc-menu-pro' ),
				'label_alt' => __( 'Show Menu', 'lc-menu-pro' ),
				'id' => 'css_toggle_menu',
				'std' => '',
				'type' => 'button',
				'refresh_on_change' => false,
				'advanced_action' => 'dslc_show_menu()',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Color', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_color',
				'std' => '#555',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-icon',
				'affect_on_change_rule' => 'color',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Align', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_align',
				'std' => 'center',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Left', 'lc-menu-pro' ),
						'value' => 'flex-start',
					),
					array(
						'label' => __( 'Right', 'lc-menu-pro' ),
						'value' => 'flex-end',
					),
					array(
						'label' => __( 'Center', 'lc-menu-pro' ),
						'value' => 'center',
					),
					array(
						'label' => __( 'Space Between', 'lc-menu-pro' ),
						'value' => 'space-between',
					),
					array(
						'label' => __( 'Space Around', 'lc-menu-pro' ),
						'value' => 'space-around',
					),
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-inner',
				'affect_on_change_rule' => 'justify-content',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
				'section' => 'responsive',
			),
			array(
				'label' => __( 'Menu Toggle Icon - Margin', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
			),
				array(
					'label' => __( 'Top', 'lc-menu-pro' ),
					'id' => 'css_res_t_menu_toggle_icon_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-menu-pro' ),
					'id' => 'css_res_t_menu_toggle_icon_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-menu-pro' ),
					'id' => 'css_res_t_menu_toggle_icon_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-menu-pro' ),
					'id' => 'css_res_t_menu_toggle_icon_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'responsive',
					'tab' => __( 'Tablet', 'lc-menu-pro' ),
					'ext' => 'px',
				),
			array(
				'label' => __( 'Menu Toggle Icon - Margin', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Width', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_width',
				'onlypositive' => true, // Value can't be negative.
				'std' => '40',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenu-mobile-hook',
				'affect_on_change_rule' => 'width',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
				'min' => 1,
				'max' => 500,
				'ext' => 'px',
			),
			array(
				'label' => __( 'Menu Toggle Icon - Height', 'lc-menu-pro' ),
				'id' => 'css_res_t_menu_toggle_icon_height',
				'onlypositive' => true, // Value can't be negative.
				'std' => '40',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenu-mobile-hook',
				'affect_on_change_rule' => 'height',
				'section' => 'responsive',
				'tab' => __( 'Tablet', 'lc-menu-pro' ),
				'min' => 1,
				'max' => 500,
				'ext' => 'px',
			),
*/
			/**
			 * Responsive Phone
			 */
/*
			array(
				'label' => __( 'Responsive Styling', 'lc-menu-pro' ),
				'id' => 'css_res_p',
				'std' => 'disabled',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Disabled', 'lc-menu-pro' ),
						'value' => 'disabled',
					),
					array(
						'label' => __( 'Enabled', 'lc-menu-pro' ),
						'value' => 'enabled',
					),
				),
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Preview', 'lc-menu-pro' ),
				'label_alt' => __( 'Show Menu', 'lc-menu-pro' ),
				'id' => 'css_toggle_menu',
				'std' => '',
				'type' => 'button',
				'refresh_on_change' => false,
				'advanced_action' => 'dslc_show_menu()',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Color', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_color',
				'std' => '#555',
				'type' => 'color',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-icon',
				'affect_on_change_rule' => 'color',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Align', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_align',
				'std' => 'center',
				'type' => 'select',
				'choices' => array(
					array(
						'label' => __( 'Left', 'lc-menu-pro' ),
						'value' => 'flex-start',
					),
					array(
						'label' => __( 'Right', 'lc-menu-pro' ),
						'value' => 'flex-end',
					),
					array(
						'label' => __( 'Center', 'lc-menu-pro' ),
						'value' => 'center',
					),
					array(
						'label' => __( 'Space Between', 'lc-menu-pro' ),
						'value' => 'space-between',
					),
					array(
						'label' => __( 'Space Around', 'lc-menu-pro' ),
						'value' => 'space-around',
					),
				),
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenupro-inner',
				'affect_on_change_rule' => 'justify-content',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
				'section' => 'responsive',
			),
			array(
				'label' => __( 'Menu Toggle Icon - Margin', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_margin_group',
				'type' => 'group',
				'action' => 'open',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
			),
				array(
					'label' => __( 'Top', 'lc-menu-pro' ),
					'id' => 'css_res_p_menu_toggle_icon_margin_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Right', 'lc-menu-pro' ),
					'id' => 'css_res_p_menu_toggle_icon_margin_right',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-right',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Bottom', 'lc-menu-pro' ),
					'id' => 'css_res_p_menu_toggle_icon_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-menu-pro' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Left', 'lc-menu-pro' ),
					'id' => 'css_res_p_menu_toggle_icon_margin_left',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lcmenupro-inner',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'responsive',
					'tab' => __( 'Phone', 'lc-menu-pro' ),
					'ext' => 'px',
				),
			array(
				'label' => __( 'Menu Toggle Icon - Margin', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_margin_group',
				'type' => 'group',
				'action' => 'close',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
			),
			array(
				'label' => __( 'Menu Toggle Icon - Width', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_width',
				'onlypositive' => true, // Value can't be negative.
				'std' => '40',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenu-mobile-hook',
				'affect_on_change_rule' => 'width',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
				'min' => 1,
				'max' => 500,
				'ext' => 'px',
			),
			array(
				'label' => __( 'Menu Toggle Icon - Height', 'lc-menu-pro' ),
				'id' => 'css_res_p_menu_toggle_icon_height',
				'onlypositive' => true, // Value can't be negative.
				'std' => '40',
				'type' => 'slider',
				'refresh_on_change' => false,
				'affect_on_change_el' => '.lcmenu-mobile-hook',
				'affect_on_change_rule' => 'height',
				'section' => 'responsive',
				'tab' => __( 'Phone', 'lc-menu-pro' ),
				'min' => 1,
				'max' => 500,
				'ext' => 'px',
			),
		);
*/

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

		$the_image = false;
		$image_alt = '';
		$image_title = '';

		if ( isset( $options['mobile_logo'] ) && ! empty( $options['mobile_logo'] ) ) {
			$the_image = $options['mobile_logo'];

			if ( ! empty( $options['resize_width'] ) || ! empty( $options['resize_height'] ) ) {

				$resize = true;
				$resize_width = false;
				$resize_height = false;

				if ( ! empty( $options['resize_width'] ) ) {
					$resize_width = $options['resize_width'];
				}

				if ( ! empty ( $options['resize_height'] ) ) {
					$resize_height = $options['resize_height'];
				}

				$the_image = dslc_aq_resize( $options['mobile_logo'], $resize_width, $resize_height, true );

			}
		}

		/* Module output starts here */

		global $dslc_active;

		if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
			$dslc_is_admin = true;
		} else {
			$dslc_is_admin = false;
		}

		if ( 'not_set' === $options['location'] ) {

			if ( $dslc_is_admin ) {

				?><div class="dslc-notification dslc-red"><?php esc_attr_e( 'Edit the module and choose which location to show.', 'lc-menu-pro' ); ?> <span class="dslca-refresh-module-hook dslc-icon dslc-icon-refresh"></span></span></div><?php
			}
		} elseif ( ! has_nav_menu( $options['location'] ) ) {

			if ( $dslc_is_admin ) {

				?><div class="dslc-notification dslc-red"><?php esc_attr_e( 'The chosen location does not have a menu assigned.', 'lc-menu-pro' ); ?> <span class="dslca-refresh-module-hook dslc-icon dslc-icon-refresh"></span></span></div><?php
			}
		} else {

			/* Full Menu Visibility Classes */
			$full_menu_classes = '';

			$css_fullmenu_show_on = '';

			if ( isset( $options['css_fullmenu_show_on'] ) ) {
				$css_fullmenu_show_on = $options['css_fullmenu_show_on'];
			}

			// if ( isset( $options['css_fullmenu_show_on'] )
				// && ! empty( $options['css_fullmenu_show_on'] ) ) {

				if ( false === stripos( $css_fullmenu_show_on, 'desktop' ) ) {
					$full_menu_classes .= 'dslc-hide-on-desktop ';
				}

				if ( false === stripos( $css_fullmenu_show_on, 'tablet' ) ) {
					$full_menu_classes .= 'dslc-hide-on-tablet ';
				}

				if ( false === stripos( $css_fullmenu_show_on, 'phone' ) ) {
					$full_menu_classes .= 'dslc-hide-on-phone ';
				}
			// }

			/*else {
				$full_menu_classes = 'dslc-hide-on-tablet dslc-hide-on-phone ';
			}*/

			/* Responsive Toggle Icon Visibility Classes */
			$toggle_responsive_classes = '';
			$css_mobile_toggle_show_on = '';

			if ( isset( $options['css_mobile_toggle_show_on'] ) ) {
				$css_mobile_toggle_show_on = $options['css_mobile_toggle_show_on'];
			}

			// if ( isset( $options['css_mobile_toggle_show_on'] ) ) {

				if ( false === stripos( $css_mobile_toggle_show_on, 'desktop' ) ) {
					$toggle_responsive_classes .= 'dslc-hide-on-desktop ';
				}

				if ( false === stripos( $css_mobile_toggle_show_on, 'tablet' ) ) {
					$toggle_responsive_classes .= 'dslc-hide-on-tablet ';
				}

				if ( false === stripos( $css_mobile_toggle_show_on, 'phone' ) ) {
					$toggle_responsive_classes .= 'dslc-hide-on-phone ';
				}

			// }

			/*else {
				$toggle_responsive_classes = 'dslc-hide-on-desktop ';
			}*/

			?>
			<!-- <div class="lcmenu-pro"> -->
				<div class="lcmenupro-navigation lcmenupro-sub-position-<?php echo esc_attr( $options['css_subnav_position'] ); ?>">
					<div class="lcmenupro-inner">
					<!-- $full_menu_classes -->
						[dslc_nav_render_menu theme_location="<?php echo $options['location']; ?>" menu_class="menu <?php echo $full_menu_classes; ?>" ]
						<?php
						// Moved into the shortcode to make LC caching working properly.
						// wp_nav_menu( array( 'theme_location' => $options['location'], 'menu_class' => 'menu dslc-hide-on-tablet dslc-hide-on-phone' ) );

						// echo '<svg class="lcmenupro-icon lcmenu-mobile-hook ' . $toggle_responsive_classes . '"><use xlink:href="#icon-menu"></use></svg>';
						?>
					</div>
					<?php
						echo '<svg class="lcmenupro-icon lcmenu-mobile-hook ' . $toggle_responsive_classes . '"><use xlink:href="#icon-menu"></use></svg>';
					?>
				</div>
			<!-- </div> -->

			<div class="lcmenupro-site-overlay"></div>

			<div class="lcmenupro-mobile-navigation">
				<div class="lcmenupro-mobile-inner">
					<div class="lcmenu-mobile-close-hook">
						<svg class="lcmenupro-icon"><use xlink:href="#icon-x"></use></svg>
					</div>
					<?php if ( $the_image ) : ?>
						<?php
							if ( ! empty( $options['image_alt'] ) ) {
								$image_alt = $options['image_alt'];
							}

							if ( ! empty( $options['image_title'] ) ) {
								$image_title = $options['image_title'];
							}
						?>
						<div class="lcmenu-mobile-logo">
							<img src="<?php echo esc_attr( $the_image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" title="<?php echo esc_attr( $image_title ); ?>" />
						</div>
					<?php endif; ?>
					[dslc_nav_render_menu theme_location="<?php echo $options['location_mobile']; ?>" menu_class="lcmenupro-mobile-menu" ]
					<?php
						// Moved into the shortcode to make LC caching working properly.
						// wp_nav_menu( array( 'theme_location' => $options['location_mobile'], 'menu_class' => 'lcmenupro-mobile-menu' ) );
					?>
				</div>
			</div>

			<script type="text/javascript">
				/* Add chevron icon */
				var menuItems = document.querySelectorAll( '.menu > li.menu-item-has-children:not(.menu-with-arrow) > a' );

				for (var i = 0, len = menuItems.length; i < len; i++) {
				  menuItems[i].insertAdjacentHTML('afterend', '<span class="dslc-navigation-arrow dslc-icon dslc-icon-chevron-down"></span>');
				  menuItems[i].parentElement.className += " menu-with-arrow";
				}
			</script>

			<?php if ( $dslc_active ) : ?>
				<script type="text/javascript">
					/* Calculate left offset for the full-width dropdowns on hover.*/
					jQuery('.menu-width-full').on('hover', function(event) {
						event.preventDefault();
						// @todo: cache it somehow?
						if ( jQuery(event.target).hasClass('menu-width-full') ) {
							setLeftMenuOffset(event.target);
						}
					});
				</script>
			<?php endif;
			/* Add SVG icon definitions */ ?>


			<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<defs>
					<symbol id="icon-menu" viewBox="0 0 24 24">
						<title>menu</title>
						<path d="M21 11h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
						<path d="M3 7h18c0.55 0 1-0.45 1-1s-0.45-1-1-1h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1z"></path>
						<path d="M21 17h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
					</symbol>

					<symbol id="icon-x" viewBox="0 0 24 24">
						<title>x</title>
						<path d="M13.413 12l5.294-5.294c0.387-0.387 0.387-1.025 0-1.413s-1.025-0.387-1.413 0l-5.294 5.294-5.294-5.294c-0.387-0.387-1.025-0.387-1.413 0s-0.387 1.025 0 1.413l5.294 5.294-5.294 5.294c-0.387 0.387-0.387 1.025 0 1.413 0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294l5.294-5.294 5.294 5.294c0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294c0.387-0.387 0.387-1.025 0-1.413l-5.294-5.294z"></path>
					</symbol>
<?php /*
				<symbol id="icon-align-justify" viewBox="0 0 24 24">
				<title>align-justify</title>
				<path d="M21 9h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				<path d="M3 7h18c0.55 0 1-0.45 1-1s-0.45-1-1-1h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1z"></path>
				<path d="M21 13h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				<path d="M21 17h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				</symbol>
				<symbol id="icon-align-left" viewBox="0 0 24 24">
				<title>align-left</title>
				<path d="M3 11h14c0.55 0 1-0.45 1-1s-0.45-1-1-1h-14c-0.55 0-1 0.45-1 1s0.45 1 1 1z"></path>
				<path d="M3 7h18c0.55 0 1-0.45 1-1s-0.45-1-1-1h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1z"></path>
				<path d="M21 13h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				<path d="M17 17h-14c-0.55 0-1 0.45-1 1s0.45 1 1 1h14c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				</symbol>
				<symbol id="icon-align-right" viewBox="0 0 24 24">
				<title>align-right</title>
				<path d="M21 9h-14c-0.55 0-1 0.45-1 1s0.45 1 1 1h14c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				<path d="M3 7h18c0.55 0 1-0.45 1-1s-0.45-1-1-1h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1z"></path>
				<path d="M21 13h-18c-0.55 0-1 0.45-1 1s0.45 1 1 1h18c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				<path d="M21 17h-14c-0.55 0-1 0.45-1 1s0.45 1 1 1h14c0.55 0 1-0.45 1-1s-0.45-1-1-1z"></path>
				</symbol>
				<symbol id="icon-chevron-down" viewBox="0 0 24 24">
				<title>chevron-down</title>
				<path d="M18.706 8.294c-0.387-0.387-1.025-0.387-1.413 0l-5.294 5.294-5.294-5.294c-0.387-0.387-1.025-0.387-1.413 0s-0.387 1.025 0 1.413l6 6c0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294l6-6c0.394-0.387 0.394-1.025 0-1.413z"></path>
				</symbol>
				<symbol id="icon-chevron-right" viewBox="0 0 24 24">
				<title>chevron-right</title>
				<path d="M15.706 11.294l-6-6c-0.387-0.387-1.025-0.387-1.413 0s-0.387 1.025 0 1.413l5.294 5.294-5.294 5.294c-0.387 0.387-0.387 1.025 0 1.413 0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294l6-6c0.394-0.387 0.394-1.025 0-1.413z"></path>
				</symbol>
				<symbol id="icon-grid" viewBox="0 0 24 24">
				<title>grid</title>
				<path d="M9 2h-5c-1.1 0-2 0.9-2 2v5c0 1.1 0.9 2 2 2h5c1.1 0 2-0.9 2-2v-5c0-1.1-0.9-2-2-2zM9 9h-5v-5c0 0 0 0 0 0h5v5z"></path>
				<path d="M20 2h-5c-1.1 0-2 0.9-2 2v5c0 1.1 0.9 2 2 2h5c1.1 0 2-0.9 2-2v-5c0-1.1-0.9-2-2-2zM20 9h-5v-5c0 0 0 0 0 0h5v5z"></path>
				<path d="M20 13h-5c-1.1 0-2 0.9-2 2v5c0 1.1 0.9 2 2 2h5c1.1 0 2-0.9 2-2v-5c0-1.1-0.9-2-2-2zM20 20h-5v-5c0 0 0 0 0 0h5v5z"></path>
				<path d="M9 13h-5c-1.1 0-2 0.9-2 2v5c0 1.1 0.9 2 2 2h5c1.1 0 2-0.9 2-2v-5c0-1.1-0.9-2-2-2zM9 20h-5v-5c0 0 0 0 0 0h5v5z"></path>
				</symbol>
				<symbol id="icon-more-horizontal" viewBox="0 0 24 24">
				<title>more-horizontal</title>
				<path d="M12 9c-1.656 0-3 1.344-3 3s1.344 3 3 3c1.656 0 3-1.344 3-3s-1.344-3-3-3zM12 13c-0.55 0-1-0.45-1-1s0.45-1 1-1 1 0.45 1 1-0.45 1-1 1z"></path>
				<path d="M20 9c-1.656 0-3 1.344-3 3s1.344 3 3 3 3-1.344 3-3c0-1.656-1.344-3-3-3zM20 13c-0.55 0-1-0.45-1-1s0.45-1 1-1 1 0.45 1 1-0.45 1-1 1z"></path>
				<path d="M4 9c-1.656 0-3 1.344-3 3s1.344 3 3 3 3-1.344 3-3c0-1.656-1.344-3-3-3zM4 13c-0.55 0-1-0.45-1-1s0.45-1 1-1 1 0.45 1 1-0.45 1-1 1z"></path>
				</symbol>
				<symbol id="icon-more-vertical" viewBox="0 0 24 24">
				<title>more-vertical</title>
				<path d="M12 9c-1.656 0-3 1.344-3 3s1.344 3 3 3c1.656 0 3-1.344 3-3s-1.344-3-3-3zM12 13c-0.55 0-1-0.45-1-1s0.45-1 1-1 1 0.45 1 1-0.45 1-1 1z"></path>
				<path d="M12 7c1.656 0 3-1.344 3-3s-1.344-3-3-3c-1.656 0-3 1.344-3 3s1.344 3 3 3zM12 3c0.55 0 1 0.45 1 1s-0.45 1-1 1-1-0.45-1-1 0.45-1 1-1z"></path>
				<path d="M12 17c-1.656 0-3 1.344-3 3s1.344 3 3 3c1.656 0 3-1.344 3-3s-1.344-3-3-3zM12 21c-0.55 0-1-0.45-1-1s0.45-1 1-1 1 0.45 1 1-0.45 1-1 1z"></path>
				</symbol>
				<symbol id="icon-search" viewBox="0 0 24 24">
				<title>search</title>
				<path d="M21.706 20.294l-4.531-4.531c1.144-1.45 1.825-3.281 1.825-5.262 0-4.688-3.813-8.5-8.5-8.5s-8.5 3.813-8.5 8.5c0 4.688 3.813 8.5 8.5 8.5 1.981 0 3.813-0.681 5.256-1.825l4.531 4.531c0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294c0.4-0.387 0.4-1.025 0.006-1.413zM4 10.5c0-3.581 2.919-6.5 6.5-6.5s6.5 2.919 6.5 6.5c0 1.775-0.712 3.381-1.869 4.556-0.012 0.012-0.025 0.025-0.037 0.038s-0.025 0.025-0.038 0.038c-1.175 1.156-2.781 1.869-4.556 1.869-3.581 0-6.5-2.919-6.5-6.5z"></path>
				</symbol>
				<symbol id="icon-x-circle" viewBox="0 0 24 24">
				<title>x-circle</title>
				<path d="M12 1c-6.063 0-11 4.938-11 11s4.938 11 11 11 11-4.938 11-11-4.938-11-11-11zM12 21c-4.962 0-9-4.038-9-9s4.038-9 9-9c4.962 0 9 4.038 9 9s-4.038 9-9 9z"></path>
				<path d="M15.706 8.294c-0.387-0.387-1.025-0.387-1.413 0l-2.294 2.294-2.294-2.294c-0.387-0.387-1.025-0.387-1.413 0s-0.387 1.025 0 1.413l2.294 2.294-2.294 2.294c-0.387 0.387-0.387 1.025 0 1.413 0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294l2.294-2.294 2.294 2.294c0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294c0.387-0.387 0.387-1.025 0-1.413l-2.294-2.294 2.294-2.294c0.394-0.387 0.394-1.025 0-1.413z"></path>
				</symbol>
				<symbol id="icon-x-square" viewBox="0 0 24 24">
				<title>x-square</title>
				<path d="M19 2h-14c-1.656 0-3 1.344-3 3v14c0 1.656 1.344 3 3 3h14c1.656 0 3-1.344 3-3v-14c0-1.656-1.344-3-3-3zM20 19c0 0.55-0.45 1-1 1h-14c-0.55 0-1-0.45-1-1v-14c0-0.55 0.45-1 1-1h14c0.55 0 1 0.45 1 1v14z"></path>
				<path d="M15.706 8.294c-0.387-0.387-1.025-0.387-1.413 0l-2.294 2.294-2.294-2.294c-0.387-0.387-1.025-0.387-1.413 0s-0.387 1.025 0 1.413l2.294 2.294-2.294 2.294c-0.387 0.387-0.387 1.025 0 1.413 0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294l2.294-2.294 2.294 2.294c0.194 0.194 0.45 0.294 0.706 0.294s0.513-0.1 0.706-0.294c0.387-0.387 0.387-1.025 0-1.413l-2.294-2.294 2.294-2.294c0.394-0.387 0.394-1.025 0-1.413z"></path>
				</symbol>
*/ ?>
				</defs>
			</svg>

			<?php

			if ( $dslc_is_admin ) { ?>

				<style type="text/css">

					@-webkit-keyframes fade-in {   0% { opacity: 0; }   100% { opacity: 1; }   }
					@-moz-keyframes    fade-in {   0% { opacity: 0; }   100% { opacity: 1; }   }
					@-o-keyframes      fade-in {   0% { opacity: 0; }   100% { opacity: 1; }   }
					@keyframes         fade-in {   0% { opacity: 0; }   100% { opacity: 1; }   }

					#dslc-content .dslc-navigation .menu li:hover > ul {
						display: block;
						opacity: 1;
						-webkit-animation: fade-in 0.3s linear; /* Safari 4+ */
						-moz-animation: fade-in 0.3s linear; /* Fx 5+ */
						-o-animation: fade-in 0.3s linear; /* Opera 12+ */
						animation: fade-in 0.3s linear; /* IE 10+, Fx 29+ */
					}

				</style><?php
			}
		} // End if().
	} // End function().
} // End class.
