<?php
/**
 * Plugin Name: Ninja Forms MailChimp Opt-ins
 * Description: Add newsletter opt-in options to any Ninja Form.
 * Author: Big Tree Island
 * Author URI: http://codecanyon.net/user/bigtreeisland/portfolio
 * Version: 1.2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Global constants
define( 'BTI_NFNO_NINJA_FORMS', 'ninja-forms/ninja-forms.php' );
define( 'BTI_NFNO_PLUGIN_URL',  plugins_url( '', __FILE__ ) );
define( 'BTI_NFNO_PLUGIN_BASE', plugin_dir_path( __FILE__ ) );
define( 'BTI_NFNO_TRANS', 'bti_nfno' );

include_once "includes/bti_nfno_mailchimp.php";
// include_once "includes/bti_nfno_updates.php";

class Big_Tree_Island_NF_MailChimp_Optins {

	/**
	 * @since Version 1.0.0
	 */
	function __construct() {

		// Deactivate plugin if Ninja Forms plugin is not present/activated
		add_action( 'admin_init', array( $this, 'activation_check' ) );

		// Initialize plugin classes
		if ( $this->ninja_forms_check() ) {
			$mailchimp = new Big_Tree_Island_NFNO_Mailchimp();

			// Plugin updates
			// $updates = new Bti_License_Tree(
			// 	plugin_basename( __FILE__ ),
			// 	'ninja-forms-newsletter-optins',
			// 	'1.2.1'
			// );

			// Equeue scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Method to register and equeues JS files for wp-admin pages.
	 *
	 * @since Version 1.0.0
	 */
	function enqueue_scripts( $hook ) {
		// Register nfno-admin.js
		wp_register_script(
			'bigtreeisland_nfno_admin_js',
			BTI_NFNO_PLUGIN_URL . '/js/nfno-admin.js',
			array(
				'jquery'
			)
		);

		// Load javascript only under right admin page
		if ( 'toplevel_page_ninja-forms' == $hook ) {
			wp_enqueue_script( 'bigtreeisland_nfno_admin_js' );
		}
	}

	/**
	 * Deactivate Ninja Forms - MailChimp if Ninja Forms is not active
	 *
	 * @since Version 1.0.0
	 */
	function activation_check() {
		if ( ! $this->ninja_forms_check() ) {
			if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	/**
	 * Method that checks if plugin could be initiated.
	 *
	 * @since Version 1.0.0
	 */
	function ninja_forms_check() {
		$active_plugins = get_option( 'active_plugins' );
		foreach ( $active_plugins as $plugin ) {
			if ( BTI_NFNO_NINJA_FORMS == $plugin ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Disabled notice. Will be shown when Ninja Forms is not present
	 * or not activated.
	 *
	 * @since Version 1.0.0
	 */
	function disabled_notice() {
		?>
			<div class="error">
				<p>
					<?php echo esc_html__( 'Ninja Forms MailChimp Opt-ins requires Ninja Forms to be present and activated!', BTI_NFNO_TRANS ); ?>
				</p>
			</div>
		<?php
	}

}

$big_tree_island_nf_mailchimp_optins = new Big_Tree_Island_NF_MailChimp_Optins();
