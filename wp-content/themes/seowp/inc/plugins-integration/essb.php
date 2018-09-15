<?php
/**
 * ----------------------------------------------------------------------
 * WordPress Easy Social Share Buttons plugin integration
 */

if ( is_plugin_active( 'easy-social-share-buttons3/easy-social-share-buttons3.php' ) ) {

	add_action( 'admin_init', 'lbmn_essb_disable_post_editing_metabox' );

	if ( ! function_exists( 'lbmn_essb_disable_post_editing_metabox' ) ) {
		/**
		 * Disable Easy Social Share button meta box on the post editing screen.
		 * It takes too much space and will confuse new theme users.
		 *
		 * @todo: add a possibility to activate this box manually somehow.
		 *
		 * @todo: ask ESSB plugin developer to provide a filter for that and have
		 * a possibility to activate/deactivate this meta box
		 * via Post Editing > Screen Options panel.
		 */
		function lbmn_essb_disable_post_editing_metabox() {

			$essb_options = get_option( 'easy-social-share-buttons3' );

			// if ( ! array_key_exists( 'turnoff_essb_optimize_box', $essb_options ) ) {
				$essb_options['turnoff_essb_optimize_box'] = true;
			// }

			update_option( 'easy-social-share-buttons3', $essb_options );
		}
	}
}