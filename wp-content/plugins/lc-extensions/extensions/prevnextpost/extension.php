<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_prevnextpost_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'prevnextpost',
			'rank' => 45,
			'title' => 'Previous & Next Posts Links',
			'description' => 'This add-on for Live Composer is a new module that shows links to previous and next post (adjacent to the currently shown one). It works for the custom post types as well, not just blog posts.',
			'details' => 'https://livecomposerplugin.com/downloads/previousnext-post-links-add-on/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=prev-next-links',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'prevnextpost' ) ) :

		define( 'SKLC_ADDON_PRNEP_BASENAME', plugin_basename( __FILE__ ) );
		define( 'SKLC_ADDON_PRNEP_URL', plugin_dir_url( __FILE__ ) );
		define( 'SKLC_ADDON_PRNEP_NAME', dirname( plugin_basename( __FILE__ ) ) );
		define( 'SKLC_ADDON_PRNEP_ABS', dirname(__FILE__) );

		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) return;
	
		class LC_Prev_Next_Post_Links extends DSLC_Module {

			var $module_id;
			var $module_title;
			var $module_icon;
			var $module_category;

			function __construct() {

				$this->module_id = 'LC_Prev_Next_Post_Links';
				$this->module_title = __( '&lt; Post Links &gt;', 'sklc-addon-prev-next-post' );
				$this->module_icon = 'link';
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
								'value' => 'desktop'
							),
							array(
								'label' => __( 'Tablet', 'lcproext' ),
								'value' => 'tablet'
							),
							array(
								'label' => __( 'Phone', 'lcproext' ),
								'value' => 'phone'
							),
						),
					),

					/* General Styling */

					array(
						'label' => __( 'BG Color', 'lcproext' ),
						'id' => 'css_main_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Image', 'lcproext' ),
						'id' => 'css_main_bg_img',
						'std' => '',
						'type' => 'image',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'background-image',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Image Repeat', 'lcproext' ),
						'id' => 'css_main_bg_img_repeat',
						'std' => 'repeat',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Repeat', 'lcproext' ),
								'value' => 'repeat',
							),
							array(
								'label' => __( 'Repeat Horizontal', 'lcproext' ),
								'value' => 'repeat-x',
							),
							array(
								'label' => __( 'Repeat Vertical', 'lcproext' ),
								'value' => 'repeat-y',
							),
							array(
								'label' => __( 'Do NOT Repeat', 'lcproext' ),
								'value' => 'no-repeat',
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'background-repeat',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Image Attachment', 'lcproext' ),
						'id' => 'css_main_bg_img_attch',
						'std' => 'scroll',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Scroll', 'lcproext' ),
								'value' => 'scroll',
							),
							array(
								'label' => __( 'Fixed', 'lcproext' ),
								'value' => 'fixed',
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'background-attachment',
						'section' => 'styling',
					),
					array(
						'label' => __( 'BG Image Position', 'lcproext' ),
						'id' => 'css_main_bg_img_pos',
						'std' => 'top left',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Top Left', 'lcproext' ),
								'value' => 'left top',
							),
							array(
								'label' => __( 'Top Right', 'lcproext' ),
								'value' => 'right top',
							),
							array(
								'label' => __( 'Top Center', 'lcproext' ),
								'value' => 'Center Top',
							),
							array(
								'label' => __( 'Center Left', 'lcproext' ),
								'value' => 'left center',
							),
							array(
								'label' => __( 'Center Right', 'lcproext' ),
								'value' => 'right center',
							),
							array(
								'label' => __( 'Center', 'lcproext' ),
								'value' => 'center center',
							),
							array(
								'label' => __( 'Bottom Left', 'lcproext' ),
								'value' => 'left bottom',
							),
							array(
								'label' => __( 'Bottom Right', 'lcproext' ),
								'value' => 'right bottom',
							),
							array(
								'label' => __( 'Bottom Center', 'lcproext' ),
								'value' => 'center bottom',
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'background-position',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_main_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Width', 'lcproext' ),
						'id' => 'css_main_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Borders', 'lcproext' ),
						'id' => 'css_main_border_trbl',
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
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Radius - Top', 'lcproext' ),
						'id' => 'css_main_border_radius_top',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Border Radius - Bottom', 'lcproext' ),
						'id' => 'css_main_border_radius_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Margin Bottom', 'lcproext' ),
						'id' => 'css_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_main_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_main_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Box Shadow', 'lcproext' ),
						'id' => 'css_main_box_shadow',
						'std' => '',
						'type' => 'box_shadow',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts',
						'affect_on_change_rule' => 'box-shadow',
						'section' => 'styling',
					),

					/* Links Styling */

					array(
						'label' => __( 'BG Color', 'lcproext' ),
						'id' => 'css_links_bg_color',
						'std' => '#5890e5',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'BG Color - Hover', 'lcproext' ),
						'id' => 'css_links_bg_color_hover',
						'std' => '#4b7bc2',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a:hover',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_links_border_color',
						'std' => '#000',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Color - Hover', 'lcproext' ),
						'id' => 'css_links_border_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a:hover',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Width', 'lcproext' ),
						'id' => 'css_links_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Borders', 'lcproext' ),
						'id' => 'css_links_border_trbl',
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
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Radius', 'lcproext' ),
						'id' => 'css_links_border_radius',
						'std' => '3',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'border-radius',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Color', 'lcproext' ),
						'id' => 'css_links_color',
						'std' => '#ffffff',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Color - Hover', 'lcproext' ),
						'id' => 'css_links_color_hover',
						'std' => '#ffffff',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a:hover',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Font Size', 'lcproext' ),
						'id' => 'css_links_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
						'ext' => 'px'
					),
					array(
						'label' => __( 'Font Weight', 'lcproext' ),
						'id' => 'css_links_font_weight',
						'std' => '800',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'font-weight',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
						'ext' => '',
						'min' => 100,
						'max' => 900,
						'increment' => 100
					),
					array(
						'label' => __( 'Font Family', 'lcproext' ),
						'id' => 'css_links_font_family',
						'std' => 'Lato',
						'type' => 'font',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'font-family',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Letter Spacing', 'lcproext' ),
						'id' => 'css_links_letter_spacing',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'letter-spacing',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
						'ext' => 'px',
						'min' => -50,
						'max' => 50
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_links_padding_vertical',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_links_padding_horizontal',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Box Shadow', 'lcproext' ),
						'id' => 'css_links_box_shadow',
						'std' => '',
						'type' => 'box_shadow',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a',
						'affect_on_change_rule' => 'box-shadow',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),
					array(
						'label' => __( 'Box Shadow - Hover', 'lcproext' ),
						'id' => 'css_links_box_shadow_hover',
						'std' => '',
						'type' => 'box_shadow',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a:hover',
						'affect_on_change_rule' => 'box-shadow',
						'section' => 'styling',
						'tab' => __( 'Links', 'lcproext' ),
					),

					/**
					 * Icon
					 */

					array(
						'label' => __( 'Color', 'lcproext' ),
						'id' => 'css_icon_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a .dslc-icon',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => __( 'icon', 'lcproext' ),
					),
					array(
						'label' => __( 'Color - Hover', 'lcproext' ),
						'id' => 'css_icon_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a:hover .dslc-icon',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => __( 'icon', 'lcproext' ),
					),
					array(
						'label' => __( 'Spacing', 'lcproext' ),
						'id' => 'css_icon_margin',
						'std' => '5',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-adjacent-posts a .dslc-icon',
						'affect_on_change_rule' => 'margin-right,margin-left',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'icon', 'lcproext' ),
					),


				);

				$dslc_options = array_merge( $dslc_options, $this->shared_options( 'animation_options', array( 'hover_opts' => false ) ) );
				$dslc_options = array_merge( $dslc_options, $this->presets_options() );

				return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

			}

			/**
			 * Module Output
			 *
			 * @since 1.0
			 */
			function output( $options ) {

				global $dslc_active;
				
				$show_placeholder = true;

				if ( is_singular() && get_post_type() !== 'dslc_templates' ) {

					// No placeholder
					$show_placeholder = false;

					// Get adjancent posts
					$prev_post = get_adjacent_post( false, '', true );
					$next_post = get_adjacent_post( false, '', false );

					// Previous post data
					$prev_post_URL = false;
					$prev_post_title = false;
					if ( is_a( $prev_post, 'WP_Post' ) ) {
						$prev_post_URL = get_permalink( $prev_post->ID );
						$prev_post_title = get_the_title( $prev_post->ID );
					}

					// Next post data
					$next_post_URL = false;
					$next_post_title = false;
					if ( is_a( $next_post, 'WP_Post' ) ) {
						$next_post_URL = get_permalink( $next_post->ID );
						$next_post_title = get_the_title( $next_post->ID );
					}

				}

				if ( $show_placeholder ) {

					// URLs
					$prev_post_URL = '#';
					$next_post_URL = '#';

					// Titles
					$prev_post_title = 'Previous Post Title';
					$next_post_title = 'Next Post Title';

				}

				$this->module_start( $options );

				/* Module Output Starts Here */

				?>

					<div class="dslc-adjacent-posts dslc-clearfix">

						<?php if ( $prev_post_URL ) : ?>
							<div class="dslc-adjacent-posts-prev dslc-fl">
								<a href="<?php echo $prev_post_URL; ?>"><span class="dslc-icon dslc-icon-chevron-left"></span><?php echo $prev_post_title; ?></a>
							</div><!-- .dslc-adjacent-posts-prev -->
						<?php endif; ?>

						<?php if ( $next_post_URL ) : ?>
							<div class="dslc-adjacent-posts-next dslc-fr">
								<a href="<?php echo $next_post_URL; ?>"><?php echo $next_post_title; ?><span class="dslc-icon dslc-icon-chevron-right"></span></a>
							</div><!-- .dslc-adjacent-posts-next -->
						<?php endif; ?>

					</div><!-- .dslc-adjacent-posts -->

				<?php

				/* Module Output Ends Here */

				$this->module_end( $options );

			}

		}

	endif; // If is_extension_active.


}

lcproext_prevnextpost_init();


/**
 * Then, check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'prevnextpost' ) ) :

	/**
	 * Register Module
	 *
	 * @since 1.0
	 */
	function sklc_addon_prnep_register_module() {

		// Live Composer not active, do not proceed
		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) return;

		dslc_register_module( 'LC_Prev_Next_Post_Links' );

	} add_action( 'dslc_hook_register_modules', 'sklc_addon_prnep_register_module' );

	/**
	 * Load Scripts
	 *
	 * @since 1.0
	 */
	function sklc_addon_prnep_scripts() {

		wp_enqueue_style( 'sklc-addon-prnep-main-css', SKLC_ADDON_PRNEP_URL . 'css/main.css', array(), '1.0' );

	} add_action( 'wp_enqueue_scripts', 'sklc_addon_prnep_scripts' );

endif; // If is_extension_active.