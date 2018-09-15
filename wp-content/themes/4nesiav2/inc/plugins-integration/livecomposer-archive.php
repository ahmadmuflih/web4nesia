<?php
/**
 * Live Composer: Archive Page Template Support
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * User can create archive page designs for
 * the next archive listing pages:
 *    – search results list,
 *    – author posts list,
 *    – posts by category listing,
 *    – posts by tab listing,
 *    – posts by date listing
 *    – home page with latest posts
 *    – nothing found page,
 *    – 404 error page,
 *
 * To change design of these listing pages in other themes you need to edit
 * PHP files. In our theme user has total control over archive pages via
 * Live Composer powered pages of specially created content type (lbmn_archive).
 *
 * These lbmn_archive pages are actually Live Composer - powered pages
 * with archive listing module inside. With this approach we provide
 * a theme user with a possibility to edit/create new archive pages
 * the same way they work with normal pages.
 *
 * In the WP admin there is a special section for this:
 * WP admin > Appearance > System Templates
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
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

// Check if Live Composer is active.
// Functions below needed only for the first generation of the theme.

/**
 * ----------------------------------------------------------------------
 * Hide system templates from direct access
 */

// add_action( 'template_redirect', 'lbmn_redirect_to_hide_teamplates' );

// function lbmn_redirect_to_hide_teamplates() {
// 	// Check if the user has rights to see template pages
// 	// and other admin-only resrouces
// 	if ( ! is_user_logged_in() && ! current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {

// 		$queried_post_type = get_post_type( get_the_ID() );
// 		// echo '$queried_post_type: ' . $queried_post_type;

// 		$posts_to_hide = array(
// 			'dslc_templates',
// 			'lbmn_archive',
// 			'lbmn_footer',
// 		);

// 		if ( in_array( $queried_post_type, $posts_to_hide ) ) {
// 			wp_redirect( home_url(), 301 );
// 			exit;
// 		}
// 	}
// }

// /**
//  * ----------------------------------------------------------------------
//  * Special content type for archive templates
//  * User can create archive page designs (search, author, category, tag, date)
//  * the same way he creates pages usign Live Composer
//  * In the back-end archive page templates is just a custom content type
//  */

// // Register Custom Post Type
// add_action( 'init', 'lbmn_archive_cpt', 0 );
// function lbmn_archive_cpt() {

// 	$labels = array(
// 		'name'               => _x( 'System Page Templates', 'Post Type General Name', 'text_domain' ),
// 		'singular_name'      => _x( 'System Page Template', 'Post Type Singular Name', 'text_domain' ),
// 		// 'menu_name'           => __( 'Post Type', 'text_domain' ),
// 		// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
// 		'all_items'          => __( 'All Templates', 'text_domain' ),
// 		'view_item'          => __( 'View Template', 'text_domain' ),
// 		'add_new_item'       => __( 'Add New Template', 'text_domain' ),
// 		'add_new'            => __( 'Add Template', 'text_domain' ),
// 		'edit_item'          => __( 'Edit Template', 'text_domain' ),
// 		'update_item'        => __( 'Update Template', 'text_domain' ),
// 		// 'search_items'        => __( 'Search Item', 'text_domain' ),
// 		'not_found'          => __( 'Not found', 'text_domain' ),
// 		'not_found_in_trash' => __( 'Not found in Trash', 'text_domain' ),
// 	);
// 	$args   = array(
// 		'label'               => __( 'lbmn_archive', 'text_domain' ),
// 		// 'description'         => __( 'Archive page templates used for pages like ', 'text_domain' ),
// 		'labels'              => $labels,
// 		'supports'            => array( 'title', 'custom-fields', ),
// 		// 'taxonomies'          => array( 'category', 'post_tag' ),
// 		'hierarchical'        => false,
// 		'public'              => true,
// 		'show_ui'             => true,
// 		'show_in_menu'        => false,
// 		'show_in_nav_menus'   => false,
// 		'show_in_admin_bar'   => false,
// 		'menu_position'       => 6,
// 		'menu_icon'           => '',
// 		'can_export'          => true,
// 		'has_archive'         => true,
// 		'exclude_from_search' => false, // custom templates 404, search, etc don't work if set to 'true'
// 		'publicly_queryable'  => true, //do not change to false
// 		'capability_type'     => 'page',
// 	);
// 	register_post_type( 'lbmn_archive', $args );
// }


// // Add custom Apperance > System Templates menu item
// add_action( 'admin_menu', 'lbmn_archives_add_appearance_menu' );
// function lbmn_archives_add_appearance_menu() {
// 	add_theme_page( 'System Page Templates', 'System Templates', 'edit_theme_options', 'edit.php?post_type=lbmn_archive', '' );
// }
