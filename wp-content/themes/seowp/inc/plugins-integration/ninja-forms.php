<?php
/**
 * Ninja Forms plugin integration
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * In this file we integrate Ninja Forms with our theme:
 *    – Add the NINJA FORMS module on Live Composer toolbar
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2015 Lumberman Designs
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

// Delete the redirect transient to not allow Ninja Forms to redirect
// theme users to their welcome page ont he first plugin install
delete_transient( '_nf_activation_redirect' );

// Disable annoying Ninja Form notices.
// https://www.evernote.com/shard/s554/sh/06c1f5b8-86ad-4de9-ba4a-049560d97dfd/62767bfc0b68cbf7
remove_filter( 'nf_admin_notices', 'nf_admin_notices' );

if ( class_exists( 'Ninja_Forms' ) && ( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0.0', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) ) {

	add_action( 'admin_notices', 'lbmn_notice_nf' );

	if ( ! function_exists( 'lbmn_notice_nf' ) ) {
		function lbmn_notice_nf() {

			$theme_updated = get_option( 'lbmn_theme_updated', false );
			$theme_update_completed = get_option( 'lbmn_update_completed', false );

			if ( current_user_can( 'install_plugins' ) && $theme_updated && ! $theme_update_completed && ! lbmn_is_theme_update_page() ) {
				echo '<div class="notice notice-error is-dismissible lbmn-notice-nf">';
				echo '<p>' . __( 'Please update Ninja Forms.', 'lbmn' ) . '</p>';
				echo '</div>';
			}
		}
	}

} else {

	// Check if Live Composer plugins are active.
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) && class_exists( 'Ninja_Forms' ) ) {

		if ( ! function_exists( 'lbmn_get_ninjaform_id_by_title' ) ) {

			/**
			 * Helper function used to get the Ninja Form ID by form Title
			 */
			function lbmn_get_ninjaform_id_by_title( $form_title = '' ) {
				$all_forms = array();

				$all_forms = Ninja_Forms()->form()->get_forms();

				if ( is_array( $all_forms ) && ! empty( $all_forms ) ) {
					foreach ( $all_forms as $form ) {
						$form_id = $form->get_id();
						$form_setting_title = $form->get_setting( 'title' );

						if ( isset( $form_setting_title ) ) {
							if ( stripslashes( $form_setting_title ) == stripslashes( $form_title ) ) {
								return $form_id;
							}
						}
					}
				}

				return false;
			}
		}

		/**
		 * Module LBMN_Ninja_Forms
		 */
		class LBMN_Ninja_Forms extends DSLC_Module {

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
			 * Construct
			 */
			function __construct() {
				$this->module_id       = 'LBMN_Ninja_Forms';
				$this->module_title    = __( 'Ninja Forms', 'live-composer-page-builder' );
				$this->module_icon     = 'envelope';
				$this->module_category = 'Extensions';
			}

			/**
			 * Options
			 */
			function options() {

				$ninja_form_choices = array();

				$ninja_form_choices[] = array(
					'label' => __( '-- Select --', 'live-composer-page-builder' ),
					'value' => 'not_set',
				);

				/**
				 * ----------------------------------------------------------------------
				 * Get all the forms available
				 */

				$all_forms = array();

				$all_forms = Ninja_Forms()->form()->get_forms();

				if ( is_array( $all_forms ) && ! empty( $all_forms ) ) {

					foreach ( $all_forms as $form ) {
					    $ninja_form_choices[] = array(
					        'label' => $form->get_setting( 'title' ),
					        'value' => $form->get_setting( 'title' ),
					    );
					}
				}

				$dslc_options = array(

					array(
						'label'   => __( 'Show On', 'live-composer-page-builder' ),
						'id'      => 'css_show_on',
						'std'     => 'desktop tablet phone',
						'type'    => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Desktop', 'live-composer-page-builder' ),
								'value' => 'desktop',
							),
							array(
								'label' => __( 'Tablet', 'live-composer-page-builder' ),
								'value' => 'tablet',
							),
							array(
								'label' => __( 'Phone', 'live-composer-page-builder' ),
								'value' => 'phone',
							),
						),
					),
					array(
						'label'   => __( 'Form Name', 'live-composer-page-builder' ),
						'id'      => 'ninjaform_title',
						'std'     => 'not_set',
						'type'    => 'select',
						'choices' => $ninja_form_choices,
					),
					array(
						'label' => __( 'Error-Proof Mode', 'live-composer-page-builder' ),
						'id' => 'error_proof_mode',
						'std' => 'active',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Enabled', 'live-composer-page-builder' ),
								'value' => 'active',
							),
						),
						'help' => __( 'Some JavaScript code and shortcodes can break the page editing.<br> Use <b>Error-Proof Mode</b> to make it work.', 'live-composer-page-builder' ),
						'visibility' => 'hidden',
					),

					/**
					 * Styling Options
					 */

					array(
						'label'                 => __( ' BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_main_bg_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_main_border_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_main_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_main_border_trbl',
						'std'                   => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
					),
					array(
						'label'                 => __( 'Border Radius - Top', 'live-composer-page-builder' ),
						'id'                    => 'css_main_border_radius_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_main_border_radius_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_margin_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Form Block: Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_main_padding_vertical',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Form Block: Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_main_padding_horizontal',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-cont',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
					),

					/**
					 * Textbox / Textarea
					 */

					array(
						'label'                 => __( 'BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_bg_color',
						'std'                   => 'rgba(0,0,0,0)',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number],input[type=tel]',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_border_color',
						'std'                   => '', // 'std' => '#ddd',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number],input[type=tel]',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_color',
						'std'                   => '', // 'std' => '#4d4d4d',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number],input[type=tel]',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_focus_bg_color',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus,input[type=tel]:focus',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_focus_border_color',
						'std'                   => '', // 'std' => '#eee',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus,input[type=tel]:focus',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_focus_txt_color',
						'std'                   => '', // 'std' => '#eee',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus,input[type=tel]:focus',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_border_width',
						'std'                   => '', // 'std' => '1',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_border_radius',
						'std'                   => '', // 'std' => '4',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'border-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_font_weight',
						'std'                   => '', // 'std' => '500',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height Input', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_line_height',
						'std'                   => '', // 'std' => '23',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Line Height Textarea', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_line_height',
						'std'                   => '', // 'std' => '23',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'textarea',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Min-Height Textarea', 'live-composer-page-builder' ),
						'id'                    => 'css_textarea_min_height',
						'std'                   => '', // 'std' => '100',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'textarea',
						'affect_on_change_rule' => 'height',
						'ext'                   => 'px',
						'min'                   => 0,
						'max'                   => 500,
						'section'               => 'styling',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_margin_bottom',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_padding_vertical',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_inputs_padding_horizontal',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number], input[type=tel]',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Textbox / Textarea', 'live-composer-page-builder' ),
					),

					/**
					 * Selectors
					 */

					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label,  .optin_mailchimp-wrap input+span',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_font_weight',
						'std'                   => '',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_line_height',
						'std'                   => '', // 'std' => '23',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Vertical Shift', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_input_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => ".checkbox-wrap label, .optin_mailchimp-wrap input+span, .field-wrap input[type='checkbox'], .field-wrap input[type='radio']",
						'affect_on_change_rule' => 'top',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => 'px',
						'min'                   => -20,
						'max'                   => 20,
						'increment'             => 1,
					),
					array(
						'label'                 => __( 'Input Margin Right', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_input_margin_right',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => ".checkbox-wrap label, .optin_mailchimp-wrap input+span, .field-wrap input[type='checkbox'], .field-wrap input[type='radio']",
						'affect_on_change_rule' => 'margin-right',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
	/*				array(
						'label'                 => __( 'Label Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_margin_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap label',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Label Margin Right', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_margin_right',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap label, .optin_mailchimp-wrap input+span, .list-radio-wrap li label, .list-checkbox-wrap li label',
						'affect_on_change_rule' => 'margin-right',
						'section'               => 'styling',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),*/
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_padding_vertical',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap li label, .list-checkbox-wrap li label',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_checkbox_labels_padding_horizontal',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap li label, .list-checkbox-wrap li label',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Radio / Checkboxes', 'live-composer-page-builder' ),
					),

					/**
					 * Select
					 */

					array(
						'label'                 => __( 'BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_bg_color',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_border_color',
						'std'                   => '', // 'std' => '#ddd',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_focus_bg_color',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select:focus',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_focus_border_color',
						'std'                   => '', // 'std' => '#eee',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select:focus',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Focused: Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_focus_txt_color',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select:focus',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_select_border_width',
						'std'                   => '', // 'std' => '1',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_select_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
						'id'                    => 'css_select_border_radius',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'border-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_select_color',
						'std'                   => '', // 'std' => '#4d4d4d',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_select_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_select_font_weight',
						'std'                   => '', // 'std' => '500',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_select_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height Input', 'live-composer-page-builder' ),
						'id'                    => 'css_select_line_height',
						'std'                   => '', // 'std' => '23',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_select_margin_bottom',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_select_padding_vertical',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_select_padding_horizontal',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'select',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'List > Dropdown', 'live-composer-page-builder' ),
					),

					/**
					 * HTML Element
					 */

					array(
						'label'                 => __( ' BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_text_bg_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_text_border_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_text_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_text_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Top', 'live-composer-page-builder' ),
						'id'                    => 'css_text_border_radius_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_text_border_radius_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_text_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Link Color', 'live-composer-page-builder' ),
						'id'                    => 'css_link_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container a',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Link Color: Hover', 'live-composer-page-builder' ),
						'id'                    => 'css_link_color_hover',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container a:hover',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_text_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_text_font_weight',
						'std'                   => '', // 'std' => '400',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_text_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_text_line_height',
						'std'                   => '', // 'std' => '22',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_text_margin_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_text_padding_vertical',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_text_padding_horizontal',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
						'id'                    => 'css_text_text_align',
						'std'                   => '', // 'std' => 'left',
						'type'                  => 'select',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.html-container',
						'affect_on_change_rule' => 'text-align',
						'section'               => 'styling',
						'tab'                   => __( 'HTML Element', 'live-composer-page-builder' ),
						'choices'               => array(
							array(
								'label' => __( 'Left', 'live-composer-page-builder' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'live-composer-page-builder' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'live-composer-page-builder' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'live-composer-page-builder' ),
								'value' => 'justify',
							),
						),
					),

					/**
					 * Hr
					 */
					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_hr_bg_color',
						'std'                   => '', // 'std' => '#ededed',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.hr-wrap hr',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'hr', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_hr_height',
						'std'                   => '', // 'std' => '1',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.hr-wrap hr',
						'affect_on_change_rule' => 'margin-bottom,padding-bottom',
						'ext'                   => 'px',
						'min'                   => 1,
						'max'                   => 20,
						'section'               => 'styling',
						'tab'                   => __( 'hr', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Margin Top', 'live-composer-page-builder' ),
						'id'                    => 'css_hr_margin_top',
						'std'                   => '', // 'std' => '20',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.hr-wrap',
						'affect_on_change_rule' => 'margin-top',
						'section'               => 'styling',
						'tab'                   => __( 'hr', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Borrom', 'live-composer-page-builder' ),
						'id'                    => 'css_hr_margin_bottom',
						'std'                   => '', // 'std' => '20',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.hr-wrap',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'tab'                   => __( 'hr', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),

					/**
					 * Submit Button
					 */

					array(
						'label'                 => __( 'BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_bg_color',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_border_color',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_color',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Hover: BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_bg_color_hover',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit]:hover, button:hover',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Hover: Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_border_color_hover',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit]:hover, button:hover',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Hover: Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_button_color_hover',
						'std'                   => '', // 'std' => '#fff',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit]:hover, button:hover',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_button_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_button_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
						'id'                    => 'css_button_border_radius',
						'std'                   => '', // 'std' => '3',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'border-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_button_font_size',
						'std'                   => '', // 'std' => '16',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_button_font_weight',
						'std'                   => '', // 'std' => '300',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_button_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_button_line_height',
						'std'                   => '', // 'std' => '21',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_button_padding_vertical',
						'std'                   => '', // 'std' => '14',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_button_padding_horizontal',
						'std'                   => '', // 'std' => '18',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => 'input[type=submit], button, input[type=button]',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Margin Top', 'live-composer-page-builder' ),
						'id'                    => 'css_button_margin_top',
						'std'                   => '', // 'std' => '20',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.submit-wrap',
						'affect_on_change_rule' => 'margin-top',
						'section'               => 'styling',
						'tab'                   => __( 'Submit', 'live-composer-page-builder' ),
						'ext'                   => 'px',
						'min'                   => -50,
						'max'                   => 50,
						'increment'             => 1,
					),

					/**
					 * Field > Padding
					 */

					array(
						'label'                 => __( 'Padding Top', 'live-composer-page-builder' ),
						'id'                    => 'css_form_field_padding_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap',
						'affect_on_change_rule' => 'padding-top',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Padding', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_form_field_padding_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap',
						'affect_on_change_rule' => 'padding-bottom',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Padding', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Left', 'live-composer-page-builder' ),
						'id'                    => 'css_form_field_padding_left',
						'std'                   => '',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap',
						'affect_on_change_rule' => 'padding-left',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Padding', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Right', 'live-composer-page-builder' ),
						'id'                    => 'css_form_field_padding_right',
						'std'                   => '', // 'std' => '30',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap',
						'affect_on_change_rule' => 'padding-right',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Padding', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),

					/**
					 * Field > Label
					 */

					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_font_size',
						'std'                   => '', // 'std' => '16',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_font_weight',
						'std'                   => '', // 'std' => '300',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_line_height',
						'std'                   => '', // 'std' => '24',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_margin_bottom',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_padding_vertical',
						'std'                   => '',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_labels_padding_horizontal',
						'std'                   => '',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap label',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Label', 'live-composer-page-builder' ),
					),

					/**
					 * Description
					 */

					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_description_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_description_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_description_font_weight',
						'std'                   => '', // 'std' => '400',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_description_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_description_line_height',
						'std'                   => '', // 'std' => '22',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Top', 'live-composer-page-builder' ),
						'id'                    => 'css_description_margin_top',
						'std'                   => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'margin-top',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_description_margin_bottom',
						'std'                   => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
						'id'                    => 'css_description_text_align',
						'std'                   => '', // 'std' => 'left',
						'type'                  => 'select',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-field-description',
						'affect_on_change_rule' => 'text-align',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Description', 'live-composer-page-builder' ),
						'choices'               => array(
							array(
								'label' => __( 'Left', 'live-composer-page-builder' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'live-composer-page-builder' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'live-composer-page-builder' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'live-composer-page-builder' ),
								'value' => 'justify',
							),
						),
					),

					/**
					 * Form Error
					 */

					array(
						'label'                 => __( 'BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_bg_color',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_border_color',
						'std'                   => '', // 'std' => '#5890e5',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius: Top', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_border_radius_top',
						'std'                   => '', // 'std' => '3',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius: Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_border_radius_bottom',
						'std'                   => '', // 'std' => '3',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_font_weight',
						'std'                   => '', // 'std' => '400',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_line_height',
						'std'                   => '', // 'std' => '22',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_padding_vertical',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_padding_horizontal',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-required-error',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Field > Error', 'live-composer-page-builder' ),
					),

					/**
					 * Form Message > Required
					 */

					array(
						'label'                 => __( 'Required fields message', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_display',
						'std'                   => '', // 'std' => 'block',
						'type'                  => 'select',
						'choices'               => array(
							array(
								'label' => __( 'Show', 'live-composer-page-builder' ),
								'value' => 'block',
							),
							array(
								'label' => __( 'Hide', 'live-composer-page-builder' ),
								'value' => 'none',
							),
						),
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'display',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_font_size',
						'std'                   => '', // 'std' => '14',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_font_weight',
						'std'                   => '', // 'std' => '300',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_line_height',
						'std'                   => '', // 'std' => '21',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_margin_bottom',
						'std'                   => '', // 'std' => '20',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_padding_vertical',
						'std'                   => '',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_padding_horizontal',
						'std'                   => '',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
						'id'                    => 'css_req_items_text_align',
						'std'                   => '', // 'std' => 'left',
						'type'                  => 'select',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-form-fields-required',
						'affect_on_change_rule' => 'text-align',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
						'choices'               => array(
							array(
								'label' => __( 'Left', 'live-composer-page-builder' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'live-composer-page-builder' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'live-composer-page-builder' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'live-composer-page-builder' ),
								'value' => 'justify',
							),
						),
					),

					/**
					 * Required Symbol
					 */

					array(
						'label'                 => __( 'Required Symbol (*)', 'live-composer-page-builder' ),
						'id'                    => 'css_symbol_display',
						'std'                   => '', // 'std' => 'inline',
						'type'                  => 'select',
						'choices'               => array(
							array(
								'label' => __( 'Show', 'live-composer-page-builder' ),
								'value' => 'inline',
							),
							array(
								'label' => __( 'Hide', 'live-composer-page-builder' ),
								'value' => 'none',
							),
						),
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.field-wrap .ninja-forms-req-symbol',
						'affect_on_change_rule' => 'display',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Color', 'live-composer-page-builder' ),
						'id'                    => 'css_symbol_color',
						'std'                   => '', // 'std' => 'rgb(244, 95, 95)',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.ninja-forms-req-symbol, .ninja-forms-req-symbol *',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Required', 'live-composer-page-builder' ),
					),

					/**
					 * Form Error Message
					 */

					array(
						'label'                 => __( ' BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_bg_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors, .nf-error-field-errors > *',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_border_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Top', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_border_radius_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_border_radius_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_color',
						'std'                   => 'rgb(65, 72, 77)',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors, .nf-error-field-errors *',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors, .nf-error-field-errors *',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_font_weight',
						'std'                   => '', // 'std' => '400',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_line_height',
						'std'                   => '', // 'std' => '22',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_margin_bottom',
						'std'                   => '', // 'std' => '25',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'margin-bottom',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_padding_vertical',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_padding_horizontal',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
						'id'                    => 'css_form_error_msg_text_align',
						'std'                   => '', // 'std' => 'left',
						'type'                  => 'select',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-error-field-errors',
						'affect_on_change_rule' => 'text-align',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Error', 'live-composer-page-builder' ),
						'choices'               => array(
							array(
								'label' => __( 'Left', 'live-composer-page-builder' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'live-composer-page-builder' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'live-composer-page-builder' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'live-composer-page-builder' ),
								'value' => 'justify',
							),
						),
					),

					/**
					 * Form Success Message
					 */

					array(
						'label'                 => __( ' BG Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_bg_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg, .nf-response-msg > *',
						'affect_on_change_rule' => 'background-color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_border_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'border-color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_border_width',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'border-width',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Borders', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_border_trbl',
						'std'                   => '', // 'std' => 'top right bottom left',
						'type'                  => 'checkbox',
						'choices'               => array(
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'border-style',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Top', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_border_radius_top',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Border Radius - Bottom', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_border_radius_bottom',
						'std'                   => '', // 'std' => '0',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Color', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_color',
						'std'                   => '',
						'type'                  => 'color',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg, .nf-response-msg *',
						'affect_on_change_rule' => 'color',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_font_size',
						'std'                   => '', // 'std' => '13',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg, .nf-response-msg *',
						'affect_on_change_rule' => 'font-size',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_font_weight',
						'std'                   => '', // 'std' => '400',
						'type' 					=> 'select',
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
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'font-weight',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
						'ext'                   => '',
					),
					array(
						'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_font_family',
						'std'                   => '',
						'type'                  => 'font',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'font-family',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_line_height',
						'std'                   => '', // 'std' => '22',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'line-height',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
						'ext'                   => 'px',
					),
					array(
						'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_padding_vertical',
						'std'                   => '', // 'std' => '10',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_padding_horizontal',
						'std'                   => '', // 'std' => '15',
						'type'                  => 'slider',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section'               => 'styling',
						'ext'                   => 'px',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
					),
					array(
						'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
						'id'                    => 'css_form_success_msg_text_align',
						'std'                   => '', // 'std' => 'left',
						'type'                  => 'select',
						'refresh_on_change'     => false,
						'affect_on_change_el'   => '.nf-response-msg',
						'affect_on_change_rule' => 'text-align',
						'section'               => 'styling',
						'tab'                   => __( 'Form Message > Success', 'live-composer-page-builder' ),
						'choices'               => array(
							array(
								'label' => __( 'Left', 'live-composer-page-builder' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'live-composer-page-builder' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'live-composer-page-builder' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'live-composer-page-builder' ),
								'value' => 'justify',
							),
						),
					),
				);

				return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );
			}

			/**
			 * Module HTML output.
			 *
			 * @param  array $options Module options to fill the module template.
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

				if ( ! isset( $options['ninjaform_title'] ) || 'not_set' == $options['ninjaform_title'] ) {
					echo '<div class="dslc-notification dslc-red dslca-module-edit-hook">' . __( 'Click here to choose the form.', 'live-composer-page-builder' ) . '<span class="dslca-module-edit-hook dslc-icon dslc-icon-cog"></span></div>';
				} elseif ( $render_code ) {
					$form_id = lbmn_get_ninjaform_id_by_title( $options['ninjaform_title'] );

					if ( empty ( $form_id ) ) {
						echo '<div class="dslc-notification dslc-red">' . __( 'There is no form with title: ', 'live-composer-page-builder' ) . '<strong>"' . $options['ninjaform_title']  . '"</strong>. ' . __( 'Select a new form title if you rename it.', 'live-composer-page-builder' ) . '</div>';
					} else {
						echo '[ninja_form id=' . $form_id . ']';
						// Ninja_Forms()->display( $form_id );
						// ↑↑↑ Can't use this function as it breaks LC cache.
						// Ninja Forms load additional JS on each call and it can't be cached.
					}
				} else {
					echo '<div class="dslc-notification dslc-green">' . __( 'Save and refresh the page to display the form properly.', 'live-composer-page-builder' ) . '</div>';
				}
			}
		}

		/**
		 * Add the NINJA FORMS module on Live Composer toolbar
		 */
		// add_action( 'dslc_hook_register_modules', create_function( '', 'return dslc_register_module( "LBMN_Ninja_Forms" );' ) );

		add_action( 'dslc_hook_register_modules', 'lbmn_register_module_ninja_forms' );
		function lbmn_register_module_ninja_forms() {
			return dslc_register_module( "LBMN_Ninja_Forms" );
		}

		/**
		 * Set default values for Ninja Forms Module in the Live Composer
		 *
		 * @param  array  $options Module options to fill the module template.
		 * @param  string $id      Module ID.
		 */
		function lbmn_alter_nf_defaults_in_lc( $options, $id ) {
			// The array that will hold new defaults.
			$new_defaults = array();

			if ( 'LBMN_Ninja_Forms' == $id ) {
				$new_defaults = array(
					'css_margin_bottom'             => '40',
					'css_main_border_width'         => '0',
					'css_form_field_padding_right'  => '30',
					'css_form_field_padding_bottom' => '20',

					'css_labels_font_size'   => '16',
					'css_labels_font_weight' => '300',

					'css_inputs_border_color'       => 'rgb(220, 221, 221)',
					'css_inputs_color'              => 'rgb(172, 174, 174)',
					'css_inputs_focus_border_color' => 'rgb(90, 173, 225)',
					'css_inputs_focus_txt_color'    => 'rgb(60, 60, 60)',
					'css_inputs_border_width'       => '1',
					'css_inputs_border_trbl'        => 'top right bottom left ',
					'css_inputs_border_radius'      => '4',
					'css_inputs_font_size'          => '16',
					'css_inputs_line_height'        => '24',
					'css_inputs_line_height'        => '24',
					'css_inputs_padding_vertical'   => '10',
					'css_inputs_padding_horizontal' => '12',
					'css_inputs_margin_bottom'      => '0',

					'css_button_bg_color'           => 'rgb(90, 173, 225)',
					'css_button_color'              => 'rgb(255, 255, 255)',
					'css_button_bg_color_hover'     => 'rgb(77, 125, 192)',
					'css_button_border_width'       => '0',
					'css_button_border_trbl'        => 'top right bottom left ',
					'css_button_border_radius'      => '4',
					'css_button_font_size'          => '18',
					'css_button_font_weight'        => '300',
					'css_button_line_height'        => '21',
					'css_button_padding_vertical'   => '14',
					'css_button_padding_horizontal' => '20',
					'css_button_margin_top'         => '15',

					'css_req_items_color'            => 'rgb(165, 165, 165)',
					'css_req_items_font_size'        => '14',
					'css_req_items_line_height'      => '21',
					'css_req_items_margin_bottom'    => '30',
					'css_req_items_padding_vertical' => '15',
					'css_symbol_color'               => 'rgb(244, 133, 27)',

					'css_text_color'       => 'rgb(165, 165, 165)',
					'css_text_font_size'   => '14',
					'css_text_line_height' => '21',

					'css_description_color'       => 'rgb(165, 165, 165)',
					'css_description_font_size'   => '12',
					'css_description_line_height' => '18',
					'css_description_margin_top'  => '10',

					'css_form_error_bg_color'             => 'rgb(252, 9, 27)',
					'css_form_error_border_radius_top'    => '0',
					'css_form_error_border_radius_bottom' => '4',
					'css_form_error_color'                => 'rgb(255, 255, 255)',
					'css_form_error_font_size'            => '12',
					'css_form_error_line_height'          => '14',
					'css_form_error_bottom'               => '0',
					'css_form_error_padding_vertical'     => '4',
					'css_form_error_padding_horizontal'   => '12',

					'css_form_success_msg_bg_color'             => 'rgb(245, 248, 235)',
					'css_form_success_msg_border_color'         => 'rgb(217, 223, 195)',
					'css_form_success_msg_border_width'         => '1',
					'css_form_success_msg_border_trbl'          => 'bottom ',
					'css_form_success_msg_border_radius_top'    => '4',
					'css_form_success_msg_border_radius_bottom' => '4',
					'css_form_success_msg_color'                => 'rgb(145, 177, 40)',
					'css_form_success_msg_font_size'            => '21',
					'css_form_success_msg_line_height'          => '30',
					'css_form_success_msg_padding_vertical'     => '30',
					'css_form_success_msg_text_align'           => 'center',

					'css_checkbox_labels_font_size'   => '16',
					'css_checkbox_labels_font_weight' => '300',
					'css_checkbox_input_margin_right' => '6',

					'css_select_border_color'       => 'rgb(220, 221, 221)',
					'css_select_border_width'       => '1',
					'css_select_border_trbl'        => 'top right bottom left ',
					'css_select_padding_vertical'   => '9',
					'css_select_padding_horizontal' => '14',

					'css_hr_bg_color'      => 'rgba(220, 221, 221, 0.48)',
					'css_hr_height'        => '1',
					'css_hr_margin_top'    => '20',
					'css_hr_margin_bottom' => '15',

				);
			}

			// Call the function that alters the defaults and return.
			return dslc_set_defaults( $new_defaults, $options );
		}
		add_filter( 'dslc_module_options', 'lbmn_alter_nf_defaults_in_lc', 10, 2 );


		function lbmn_ninja_forms_custom_display_before_field( $field_id, $data ) {

			// Wrap HR with extra div for esier styling.
			if ( 'hr' == $data['label'] ) {
				echo '<div class="field-wrap hr-wrap">';
			}
		}
		add_action( 'ninja_forms_display_before_field', 'lbmn_ninja_forms_custom_display_before_field', 10, 2 );


		function lbmn_ninja_forms_custom_display_after_field( $field_id, $data ) {

			// Wrap HR with extra div for esier styling.
			if ( 'hr' == $data['label'] ) {
				echo '</div>';
			}
		}
		add_action( 'ninja_forms_display_after_field', 'lbmn_ninja_forms_custom_display_after_field', 10, 2 );
	}
}