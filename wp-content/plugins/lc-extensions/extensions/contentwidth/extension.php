<?php
// Exit if accessed directly!
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register this extension meta-data in the main class.
 *
 * @return void
 */
function lcproext_contentwidth_init() {

	// Register a new feature.
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'contentwidth',
			'rank' => 48,
			'title' => 'Custom Page Content Width',
			'description' => 'Allows different widths of the Live Composer content area to be set on different pages/templates.',
			'details' => 'https://livecomposerplugin.com/downloads/per-page-content-width-add-on/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=content-width',
		)
	);

} lcproext_contentwidth_init();

/**
 * Then, check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'contentwidth' ) ) :

	// Paths
	define( 'SKLC_PPCW_URL', plugin_dir_url( __FILE__ ) );
	define( 'SKLC_PPCW_ABS', dirname(__FILE__) );

	/**
	 * Add options for per page content width
	 *
	 * @since 1.0
	 */
	function contentwidth_options() {

		global $dslc_var_post_options;

		// Add option for pages
		$dslc_var_post_options['dslc-page-options'] = array(
			'title' => 'Page Options',
			'show_on' => 'page',
			'options' => array(
				array(
					'label' => 'Content Width',
					'descr' => 'The width of the modules section when row is set to wrapped. If not set the value from general options will be used.',
					'std' => '',
					'id' => 'contentwidth_content_width',
					'type' => 'text',
				),
			)
		);

		// Add option for templates
		$dslc_var_post_options['dslc-templates-opts']['options'][] = array(
			'label' => 'Content Width',
			'descr' => 'The width of the modules section when row is set to wrapped. If not set the value from general options will be used.',
			'std' => '',
			'id' => 'contentwidth_content_width',
			'type' => 'text',
		);

	} add_action( 'init', 'contentwidth_options', 91 );

	/**
	 * Apply per page content width
	 *
	 * @since 1.0
	 */
	function contentwidth_apply( $lc_width ) {

		// Post types that support templates
		global $dslc_post_types;
		$new_width = $lc_width;
		$post_ID = false;

		// If single, load template
		if ( is_singular( $dslc_post_types ) ) {
			$post_ID = dslc_st_get_template_ID( get_the_ID() );
		}

		// If archive, load template
		if ( is_archive() && ! is_author() && ! is_search() ) {
			$post_ID = dslc_get_option( get_post_type(), 'dslc_plugin_options_archives' );
		}

		// If author archives
		if ( is_author() ) {
			$post_ID = dslc_get_option( 'author', 'dslc_plugin_options_archives' );
		}

		// If search results page
		if ( is_search() ) {
			$post_ID = dslc_get_option( 'search_results', 'dslc_plugin_options_archives' );
		}

		// If 404 page
		if ( is_404() ) {
			$post_ID = dslc_get_option( '404_page', 'dslc_plugin_options_archives' );
		}

		// If a page or post template
		if ( is_singular( array( 'page', 'dslc_templates' ) ) ) {
			$post_ID = get_the_ID();
		}

		// If we have a post ID
		if ( $post_ID  ) {

			// Get custom width
			$custom_width = get_post_meta( $post_ID, 'contentwidth_content_width', true );

			// If custom width set
			if ( $custom_width ) {

				// Set new width
				$new_width = $custom_width;

				// If px or % not included add px
				if ( strpos( $new_width, 'px' ) === false && strpos( $new_width, '%' ) === false ) {
					$new_width = $new_width . 'px';
				}

			}

		}

		return $new_width;

	} add_filter( 'dslc_content_width', 'contentwidth_apply' );

endif; // If is_extension_active.
