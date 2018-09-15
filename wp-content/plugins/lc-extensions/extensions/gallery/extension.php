<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_gallery_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'gallery',
			'rank' => 30,
			'title' => 'Image Gallery Grid',
			'details' => 'https://livecomposerplugin.com/downloads/gallery-images-grid/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=gallery-module',
			'description' => 'Display the images from your galleries and projects on any page (as images grid or carousel). The extension adds a new module.',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'gallery' ) ) :

		define( 'SKLC_ADDON_GIGRID_BASENAME', plugin_basename( __FILE__ ) );
		define( 'SKLC_ADDON_GIGRID_URL', plugin_dir_url( __FILE__ ) );
		define( 'SKLC_ADDON_GIGRID_NAME', dirname( plugin_basename( __FILE__ ) ) );
		define( 'SKLC_ADDON_GIGRID_ABS', dirname(__FILE__) );

		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) return;

		class LC_Gallery_Images_Grid_Module extends DSLC_Module {

			var $module_id;
			var $module_title;
			var $module_icon;
			var $module_category;

			function __construct() {

				$this->module_id = 'LC_Gallery_Images_Grid_Module';
				$this->module_title = __( 'Gallery Grid', 'lcproext' );
				$this->module_icon = 'picture';
				$this->module_category = 'Extensions';

			}

			public function galleries_posts_list() {
				$posts_list = array(
					array(
						'label' => __( 'Current Post', 'lcproext' ),
						'value' => '0',
					),
				);

				$posts_galleries = get_posts( array( 'post_type' => 'dslc_galleries', 'numberposts' => -1 ) );
				$posts_projects = get_posts( array( 'post_type' => 'dslc_projects', 'numberposts' => -1 ) );
				$posts_merged = array_merge( $posts_galleries, $posts_projects );
				
				foreach ( $posts_galleries as $post ) {
					
					$meta_galleries = get_post_meta( $post->ID, 'dslc_gallery_images', true );
					
					if ( ! empty( $meta_galleries ) ) {
						$posts_list[] = array(
							'label' => $post->post_title,
							'value' => $post->ID,
						);
					}
				}
				
				foreach ( $posts_projects as $post ) {
					
					$meta_projects = get_post_meta( $post->ID, 'dslc_project_images', true );
					
					if ( ! empty( $meta_projects ) ) {
						$posts_list[] = array(
							'label' => $post->post_title,
							'value' => $post->ID,
						);
					}
				}

				return $posts_list;
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
					array(
						'label' => __( 'Columns', 'lcproext' ),
						'id' => 'columns',
						'std' => '4',
						'type' => 'select',
						'choices' => $this->shared_options('posts_per_row_choices'),
					),
					array(
						'label' => __( 'Display Pictures From', 'lcproext' ),
						'id' => 'gallery_post_id',
						'std' => '0',
						'type' => 'select',
						'choices' => $this->galleries_posts_list(),
					),
					array(
						'label' => __( 'Type', 'lcproext' ),
						'id' => 'type',
						'std' => 'grid',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Grid', 'lcproext' ),
								'value' => 'grid',
							),
							array(
								'label' => __( 'Masonry', 'lcproext' ),
								'value' => 'masonry',
							),
						)
					),
					array(
						'label' => __( 'Lightbox', 'lcproext' ),
						'help' => __( 'Enabling will make clicking an image open up the image in a lightbox.', 'lcproext' ),
						'id' => 'lightbox_state',
						'std' => 'enabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Enabled', 'lcproext' ),
								'value' => 'enabled'
							),
							array(
								'label' => __( 'Disabled', 'lcproext' ),
								'value' => 'disabled'
							),
						)
					),

					/**
					 * Image
					 */

					array(
						'label' => __( 'Center If One', 'lcproext' ),
						'id' => 'center_if_one',
						'std' => 'disabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Enabled', 'lcproext' ),
								'value' => 'enabled'
							),
							array(
								'label' => __( 'Disabled', 'lcproext' ),
								'value' => 'disabled'
							),
						),
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'BG Color', 'lcproext' ),
						'id' => 'css_thumb_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Color', 'lcproext' ),
						'id' => 'css_thumb_border_color',
						'std' => '#e6e6e6',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Width', 'lcproext' ),
						'id' => 'css_thumb_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Borders', 'lcproext' ),
						'id' => 'css_thumb_border_trbl',
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
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Border Radius - Top', 'lcproext' ),
						'id' => 'css_thumb_border_radius_top',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image,.sklc-gallery-image img',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
						'ext' => 'px'
					),
					array(
						'label' => __( 'Border Radius - Bottom', 'lcproext' ),
						'id' => 'css_thumb_border_radius_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image,.sklc-gallery-image img',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Bottom', 'lcproext' ),
						'id' => 'css_thumb_margin',
						'std' => '30',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
						'ext' => 'px'
					),
					array(
						'label' => __( 'Padding Vertical', 'lcproext' ),
						'id' => 'css_thumb_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Padding Horizontal', 'lcproext' ),
						'id' => 'css_thumb_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.sklc-gallery-image',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Resize - Height', 'lcproext' ),
						'id' => 'thumb_resized_height',
						'std' => '',
						'type' => 'text',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
					),
					array(
						'label' => __( 'Resize - Width', 'lcproext' ),
						'id' => 'thumb_resized_width',
						'std' => '',
						'type' => 'text',
						'section' => 'styling',
						'tab' => __( 'image', 'lcproext' ),
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
				$show_real = false;

				if ( is_singular() && get_post_type() !== 'dslc_templates' ) {

					if ( is_singular( 'dslc_galleries' ) ) {
						$gallery_images = get_post_meta( get_the_ID(), 'dslc_gallery_images', true );
					} elseif ( is_singular( 'dslc_projects' ) ) {
						$gallery_images = get_post_meta( get_the_ID(), 'dslc_project_images', true );
					} elseif ( intval($options['gallery_post_id']) > 0 ) {

						$gallery_images = get_post_meta( intval($options['gallery_post_id']), 'dslc_gallery_images', true );

						// No results? Maybe it's project type?
						if ( !$gallery_images ) {
							$gallery_images = get_post_meta( intval($options['gallery_post_id']), 'dslc_project_images', true );
						}

					} else {
						$gallery_images = array();
					}


					if ( $gallery_images ) {
						$show_real = true;
						$gallery_images = explode( ' ', trim( $gallery_images ) );
					}

					$show_placeholder = false;

				}

				if ( $show_placeholder ) {

					$gallery_images = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );

				}

				$this->module_start( $options );

				/* Module Output Starts Here */

				/**
				 * Columns
				 */

				$columns_class = 'dslc-col dslc-' . $options['columns'] . '-col';
				$count = 0;
				$real_count = 0;
				$increment = $options['columns'];
				$max_count = 12;

				/**
				 * Type
				 */

				$container_class = 'sklc-gallery-images sklc-gallery-images-count-' . count( $gallery_images ) . ' sklc-gallery-images-center-' . $options['center_if_one'] . ' dslc-clearfix';

				if ( $options['type'] == 'masonry' && count( $gallery_images ) > 1 ) {
					$container_class .= ' dslc-init-masonry';
					$columns_class .= ' dslc-masonry-item ';
				} elseif ( $options['type'] == 'grid' ) {
					$container_class .= ' dslc-init-grid';
				}

				/**
				 * Lightbox
				 */

				$img_class = '';
				if ( $options['lightbox_state'] == 'enabled' ) {
					$img_class = 'dslc-trigger-lightbox-gallery';
				}

				?>

					<div class="<?php echo $container_class; ?>">

						<?php

							if ( is_array( $gallery_images ) ) {

								/**
								 * Go through each image
								 */

								foreach ( $gallery_images as $gallery_image ) {

									/**
									 * Increment Count and Class
									 */

									$real_count++;
									$count += $increment;

									if ( $count == $max_count ) {
										$count = 0;
										$columns_class_extra = ' dslc-last-col';
									} elseif ( $count == $increment ) {
										$columns_class_extra = ' dslc-first-col';
									} else {
										$columns_class_extra = '';
									}

									$style_attr = '';

									/**
									 * Resize image
									 */

									$manual_resize = false;
									if ( isset( $options['thumb_resized_height'] ) && ! empty( $options['thumb_resized_height'] ) || isset( $options['thumb_resized_width'] ) && ! empty( $options['thumb_resized_width'] ) ) {

										// Enable manual resize
										$manual_resize = true;

										// Define the width/height vars
										$manual_resize_width = false;
										$manual_resize_height = false;

										// If width supplied
										if ( isset( $options['thumb_resized_width'] ) && ! empty( $options['thumb_resized_width'] ) ) {
											$manual_resize_width = $options['thumb_resized_width'];
										}

										// If height supplied
										if ( isset( $options['thumb_resized_height'] ) && ! empty( $options['thumb_resized_height'] ) ) {
											$manual_resize_height = $options['thumb_resized_height'];
										}

									}

									// Get the image SRC
									if ( $show_placeholder ) {

										$gallery_image_src = DS_LIVE_COMPOSER_URL . '/images/placeholders/big-placeholder.png';

										$thumb_alt = '';

										if ( $manual_resize ) {

											if ( $manual_resize_width ) {
												$style_attr .= 'width:' . $manual_resize_width . 'px;';
											}

											if ( $manual_resize_height ) {
												$style_attr .= 'height:' . $manual_resize_height . 'px;';
											}

										}

									} else {

										$gallery_image_src = wp_get_attachment_image_src( $gallery_image, 'full' );
										$gallery_image_src = $gallery_image_src[0];

										// Get the ALT
										$thumb_alt = get_post_meta( $gallery_image, '_wp_attachment_image_alt', true );
										if ( ! $thumb_alt ) $thumb_alt = '';

										// If manual resize
										if ( $manual_resize ) {
											// Get resized version
											$gallery_image_src = dslc_aq_resize( $gallery_image_src, $manual_resize_width, $manual_resize_height, true );
										}

									}

									?><div class="sklc-gallery-image <?php echo $columns_class . $columns_class_extra; ?>"><img style="<?php echo $style_attr; ?>" class="<?php echo $img_class; ?>" src="<?php echo $gallery_image_src; ?>" alt="<?php echo $thumb_alt; ?>" /></div><?php

								}

							}

						?>

						<?php if ( ! $show_placeholder && $options['lightbox_state'] == 'enabled' ) : ?>

							<div class="dslc-lightbox-gallery">

								<?php if ( ! empty( $gallery_images ) ) : ?>

									<?php foreach ( $gallery_images as $gallery_image ) : ?>

										<?php
											$gallery_image_src = wp_get_attachment_image_src( $gallery_image, 'full' );
											$gallery_image_src = $gallery_image_src[0];

											$gallery_image_title = get_post_meta( $gallery_image, '_wp_attachment_image_alt', true );
											if ( ! $gallery_image_title ) $gallery_image_title = '';
										?>

										<a href="<?php echo $gallery_image_src; ?>" title="<?php echo esc_attr( $gallery_image_title ); ?>"></a>

									<?php endforeach; ?>

								<?php endif; ?>

							</div><!-- .dslc-gallery-lightbox -->

						<?php endif; ?>

					</div><!-- .sklc-gallery-images -->

				<?php

				/* Module Output Ends Here */

				$this->module_end( $options );

			}

		}
	endif; // If is_extension_active.
}

lcproext_gallery_init();

/**
 * Then, check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'gallery' ) ) :

	/**
	 * Register Module
	 *
	 * @since 1.0
	 */
	function lc_gallery_grid_module_init() {

		// Live Composer not active, do not proceed
		if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) return;

		dslc_register_module( 'LC_Gallery_Images_Grid_Module' );

	} add_action( 'dslc_hook_register_modules', 'lc_gallery_grid_module_init' );

	/**
	 * Load Scripts
	 *
	 * @since 1.0
	 */
	function lc_gallery_grid_module_scripts() {

		wp_enqueue_style( 'lc-gallery-grid-module-main-css', SKLC_ADDON_GIGRID_URL . 'css/main.css', array(), '1.0' );

	} add_action( 'wp_enqueue_scripts', 'lc_gallery_grid_module_scripts' );

endif; // If is_extension_active.
