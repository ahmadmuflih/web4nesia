<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register this extension meta-data in the main class.
 *
 * @return void
 */
function lcproext_video_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'video',
			'rank' => 28,
			'title' => 'Video Embed Module',
			'details' => 'https://livecomposerplugin.com/downloads/video-embed/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=video-module',
			'description' => 'Easily embed videos from various sources ( YouTube, Vimeo, Hulu, Vine... ) using drag and drop. The extension adds a new module. No need to mess with shortcodes or iframes to place video on your page.',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'video' ) ) :

		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) {
			return;
		}

		/**
		 * The Module Class
		 */
		class LC_Video_Embed_Module extends DSLC_Module {

			var $module_id;
			var $module_title;
			var $module_icon;
			var $module_category;

			function __construct() {

				$this->module_id = 'LC_Video_Embed_Module';
				$this->module_title = __( 'Video', 'lcproext' );
				$this->module_icon = 'play';
				$this->module_category = 'Extensions';

			}

			/**
			 * Module Options
			 *
			 * @since 1.0
			 */
			function options() {

				$dslc_options = array(

					array(
						'label' => __( 'Show On', 'lcproext' ),
						'id' => 'css_show_on',
						'std' => 'desktop tablet phone',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Desktop', 'lcproext' ),
								'value' => 'desktop',
							),
							array(
								'label' => __( 'Tablet', 'lcproext' ),
								'value' => 'tablet',
							),
							array(
								'label' => __( 'Phone', 'lcproext' ),
								'value' => 'phone',
							),
						),
					),
					array(
						'label' => __( 'Show Video', 'live-composer-page-builder' ),
						'id' => 'show_video',
						'std' => 'url',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'URL', 'live-composer-page-builder' ),
								'value' => 'url',
							),
							array(
								'label' => __( 'Embed', 'live-composer-page-builder' ),
								'value' => 'embed',
							),
						),
						'dependent_controls' => array(
							'url' => 'video_url, video_height, video_width',
							'embed' => 'video_embed',
						),
					),
					array(
						'label' => __( 'Video URL', 'lcproext' ),
						'id' => 'video_url',
						'std' => '',
						'type' => 'text',
					),
					array(
						'label' => __( 'Height', 'lcproext' ),
						'id' => 'video_height',
						'std' => '',
						'type' => 'text',
					),
					array(
						'label' => __( 'Width', 'lcproext' ),
						'id' => 'video_width',
						'std' => '',
						'type' => 'text',
					),
					array(
						'label' => __( 'Video Embed', 'live-composer-page-builder' ),
						'id' => 'video_embed',
						'std' => '',
						'type' => 'textarea',
						'section' => 'functionality',
					),

					/**
					 * General
					 */

					array(
						'label' => __( 'Align', 'lcproext' ),
						'id' => 'css_align',
						'std' => 'left',
						'type' => 'text_align',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'text-align',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Color', 'lcproext' ),
						'id' => 'css_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Color - Hover', 'lcproext' ),
						'id' => 'css_bg_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed:hover',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Color - Hover', 'lcproext' ),
						'id' => 'css_border_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed:hover',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Width', 'lcproext' ),
						'id' => 'css_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Borders', 'lcproext' ),
						'id' => 'css_border_trbl',
						'std' => 'top right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'lcproext' ),
								'value' => 'top',
							),
							array(
								'label' => __( 'Right', 'lcproext' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Bottom', 'lcproext' ),
								'value' => 'bottom',
							),
							array(
								'label' => __( 'Left', 'lcproext' ),
								'value' => 'left',
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Radius', 'lcproext' ),
						'id' => 'css_border_radius',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'border-radius',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Margin Bottom', 'lcproext' ),
						'id' => 'css_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Minimum Height', 'lcproext' ),
						'id' => 'css_min_height',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'min-height',
						'section' => 'styling',
						'ext' => 'px',
						'min' => 0,
						'max' => 1000,
						'increment' => 5,
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Box Shadow', 'live-composer-page-builder' ),
						'id' => 'css_box_shadow',
						'std' => '',
						'type' => 'box_shadow',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'box-shadow',
						'section' => 'styling',
					),

					/**
					 * Responsive tablet
					 */

					array(
						'label' => __( 'Responsive Styling', 'lcproext' ),
						'id' => 'css_res_t',
						'std' => 'disabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Disabled', 'lcproext' ),
								'value' => 'disabled',
							),
							array(
								'label' => __( 'Enabled', 'lcproext' ),
								'value' => 'enabled',
							),
						),
						'section' => 'responsive',
						'tab' => __( 'tablet', 'lcproext' ),
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_res_t_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'tab' => __( 'tablet', 'lcproext' ),
						'max' => 500,
						'increment' => 1,
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_res_t_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'tab' => __( 'tablet', 'lcproext' ),
						'ext' => 'px',
					),

					/**
					 * Responsive phone
					 */

					array(
						'label' => __( 'Responsive Styling', 'lcproext' ),
						'id' => 'css_res_p',
						'std' => 'disabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Disabled', 'lcproext' ),
								'value' => 'disabled',
							),
							array(
								'label' => __( 'Enabled', 'lcproext' ),
								'value' => 'enabled',
							),
						),
						'section' => 'responsive',
						'tab' => __( 'phone', 'lcproext' ),
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_res_p_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'tab' => __( 'phone', 'lcproext' ),
						'max' => 500,
						'increment' => 1,
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_res_p_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.lc-video-embed',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'tab' => __( 'phone', 'lcproext' ),
						'ext' => 'px',
					),

				);

				$dslc_options = array_merge( $dslc_options, $this->presets_options() );

				return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

			}

			/**
			 * Module Output
			 *
			 * @since 1.0
			 * @param array $options Saved Module Options.
			 */
			function output( $options ) {

				$this->module_start( $options );

				/* Module output stars here */
				echo '<div class="lc-video-embed" style="line-height:0">';

				// If a video URL or embed is set.
				if ( ! empty( $options['video_url'] ) || ! empty( $options['video_embed'] ) ) {

					if ( 'embed' == $options['show_video'] ) {

						$embed_code = stripcslashes( $options['video_embed'] );

						// If Video Embed is empty
						if ( ! $embed_code ) {

							// Show meessage if editor is active.
							if ( dslc_is_editor_active() ) {
								echo '<div class="dslc-notification dslc-red">';
									esc_html_e( 'A video Embed needs to be set in the module options.', 'lcproext' );
								echo '</div>';
							}
						} else {
							// If embed code ok, display it.
							echo $embed_code;
						}
					} else {

						// Embed Arguments.
						$embed_args = array();

						// Embed Argument Height.
						if ( isset( $options['video_height'] ) && $options['video_height'] && '' !== $options['video_height'] ) {
							$embed_args['height'] = $options['video_height'];
						}

						// Embed Argument Width.
						if ( isset( $options['video_width'] ) && $options['video_width'] && '' !== $options['video_width'] ) {
							$embed_args['width'] = $options['video_width'];
						}

						// Get embed code ( false if wrong ).
						// $embed_code = wp_oembed_get( $options['video_url'], $embed_args );
						$embed_code = wp_oembed_get( do_shortcode( stripcslashes( $options['video_url'] ) ), $embed_args );

						// If Video URL is empty
						if ( ! $embed_code ) {

							// Show meessage if editor is active.
							if ( dslc_is_editor_active() ) {
								echo '<div class="dslc-notification dslc-red">';
									esc_html_e( 'A video URL needs to be set in the module options.', 'lcproext' );
								echo '</div>';
							}
						} else {
							// If embed code ok, display it.
							echo $embed_code;
						}
					}
				} else {
					// Show message if editor active.
					if ( dslc_is_editor_active() ) {
						echo '<div class="dslc-notification dslc-red">';
							esc_html_e( 'A video needs to be set in the module options.', 'lcproext' );
						echo '</div>';
					}
				}

				echo '</div>'; // .lc-video-embed.

				/* Module output ends here */
				$this->module_end( $options );
			}
		}

	endif; // If is_extension_active.

}

lcproext_video_init();


/**
 * Check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'video' ) ) :

	/**
	 * Register Module
	 * @todo: move this function into the class?
	 * @since 1.0
	 */
	function lc_video_embed_module_init() {

		// Live Composer not active, do not proceed.
		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) {
			return;
		}

		dslc_register_module( 'LC_Video_Embed_Module' );

	} add_action( 'dslc_hook_register_modules', 'lc_video_embed_module_init' );

endif; // If is_extension_active.
