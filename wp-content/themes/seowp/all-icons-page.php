<?php
/**
 * Render admin page that outputs a list of all custom icons available
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This file creates a virtual admin page with the next address
 * http://yoursite.com/wp-admin/?lbmn_listicons=all_icons
 *
 * On this page we list all the custom icons available from
 * /iconfont/ folder. This page used to extend Live Composer with
 * out extended icon pack.
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

// if url is http://yoursite.com/wp-admin/?lbmn_listicons=all_icons
// TODO: wrap with a function that attached to some WP hook?
if ( isset( $_GET['lbmn_listicons'] ) && ! empty( $_GET['lbmn_listicons'] ) ) {

	if ( $_GET['lbmn_listicons'] == 'all_icons' ) {
		// open json file generated by iconmoon in /theme/iconfont/selection.json
		$string          = file_get_contents( get_template_directory() . '/iconfont/selection.json' );
		$json_a          = json_decode( $string, true );
		$json_a['icons'] = array_reverse( $json_a['icons'] );

		// output list of icons
		echo '<ul class="lbmn-icons-grid">';
		foreach ( $json_a['icons'] as $k => $v ) {
			$icon_name = $v['properties']['name'];
			echo '<li class="icon-item">';
			echo '<span class="icon-item__icon dslc-icon-ext-' . $icon_name . '"></span>';
			echo '<span class="icon-item__name">ext-' . $icon_name . '</span>';
			echo '</li>';
		}
		echo '</ul>';
	}
	die();
}