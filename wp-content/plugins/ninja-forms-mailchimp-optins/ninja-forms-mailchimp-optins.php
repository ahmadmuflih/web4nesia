<?php
/*
 * Plugin Name: Ninja Forms - MailChimp Opt-ins
 * Plugin URI: http://codecanyon.net/item/ninja-forms-mailchimp-optins/10789725
 * Description: Adds MailChimp newsletter opt-in to Ninja Forms.
 * Version: 3.0.0
 * Author: Big Tree Island
 * Author URI: https://codecanyon.net/user/bigtreeisland/portfolio
 * Text Domain: ninja-forms-mailchimp-optins
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0.0', '>' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

	include 'deprecated/ninja-forms-mailchimp-optins.php';

} else {

	/**
	 * Class NF_MailChimpOptins
	 *
	 * @since 3.0.0
	 */
	final class NF_MailChimpOptins
	{
		const VERSION = '3.0.0';
		const SLUG    = 'ninja-forms-mailchimp-optins';
		const NAME    = 'MailChimp Optins';
		const AUTHOR  = 'Big Tree Island';
		const PREFIX  = 'NF_MailChimpOptins';

		private static $instance;
		public static $dir = '';
		public static $url = '';

		/**
		 * Main Plugin Instance
		 *
		 * Insures that only one instance of a plugin class exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 3.0.0
		 * @static
		 * @static var array $instance
		 * @return NF_MailChimpOptins Highlander Instance
		 */
		public static function instance()
		{
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NF_MailChimpOptins ) ) {
				self::$instance = new NF_MailChimpOptins();
				self::$dir      = plugin_dir_path( __FILE__ );
				self::$url      = plugin_dir_url( __FILE__ );

				// Import library
				if ( ! class_exists( 'MailChimp' ) ) {
					include_once self::$dir . "libs/mailchimp-api/src/MailChimp.php";
				}

				// Register our autoloader
				spl_autoload_register( array( self::$instance, 'autoloader' ) );
			}
		}

		public function __construct()
		{
			// Register custom fields
			add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ) );

			// Register actions
			add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );

			// Register settings bits
			add_filter( 'ninja_forms_plugin_settings_groups', array( $this, 'add_settings_group' ), 10, 1 );
			add_filter( 'ninja_forms_plugin_settings', array( $this, 'add_settings' ), 10, 1 );
			add_action( 'wp_ajax_mailchimp_optins_validate_key', array( $this, 'ajax_validate_mailchimp_api_key' ) );

			// Enqueue assets
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		/**
		 * Add settings group.
		 *
		 * @since 3.0.0
		 */
		public function add_settings_group( $current )
		{
			$settings_group = NF_MailChimpOptins::config( 'PluginSettingsGroup' );

			return array_merge( $current, $settings_group );
		}

		/**
		 * Add settings fields to metabox.
		 *
		 * @since 3.0.0
		 */
		public function add_settings( $settings )
		{
			$settings['mailchimp_optins'] = NF_MailChimpOptins::config( 'PluginSettingsMailChimpOptins' );

			return $settings;
		}

		/**
		 * Validate MailChimp API Key.
		 *
		 * @since 3.0.0
		 */
		public function ajax_validate_mailchimp_api_key()
		{
			$api_key = isset( $_POST['mailchimp_api_key'] ) ? $_POST['mailchimp_api_key'] : false;
			if ( !empty( $api_key ) && $api_key != false ) {
				$mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );
				$lists = $mailchimp->get('lists');
				if ( $mailchimp->success() ) {
					echo json_encode( array( 'success' => true ) );
					die();
				}
			}

			echo json_encode( array( 'success' => false ) );
			die();
		}

		/**
		 * Register custom fields.
		 *
		 * @since 3.0.0
		 */
		public function register_fields($actions)
		{
			return $actions;
		}

		/**
		 * Register custom actions.
		 *
		 * @since 3.0.0
		 */
		public function register_actions($actions)
		{
			$actions[ 'mailchimp-optins' ] = new NF_MailChimpOptins_Actions_Subscribe();

			return $actions;
		}

		/*
		 * Optional methods for convenience.
		 *
		 * @since 3.0.0
		 */
		public function autoloader($class_name)
		{
			if ( class_exists( $class_name ) ) {
				return;
			}

			if ( false === strpos( $class_name, self::PREFIX ) ) {
				return;
			}

			$class_name  = str_replace( self::PREFIX, '', $class_name );
			$classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
			$class_file  = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

			if ( file_exists( $classes_dir . $class_file ) ) {
				require_once $classes_dir . $class_file;
			}
		}

		/**
		 * Template
		 *
		 * @since 3.0.0
		 * @param string $file_name
		 * @param array $data
		 */
		public static function template( $file_name = '', array $data = array() )
		{
			if( ! $file_name ) {
				return;
			}

			extract( $data );

			include self::$dir . 'includes/Templates/' . $file_name;
		}

		/**
		 * Config
		 *
		 * @since 3.0.0
		 * @param $file_name
		 * @return mixed
		 */
		public static function config( $file_name ) {
			return include self::$dir . 'includes/Config/' . $file_name . '.php';
		}

		/**
		 * Enqueue admin assets.
		 *
		 * @since 3.0.0
		 * @param $hook
		 */
		function enqueue_admin_assets( $hook )
		{
			if ( 'forms_page_nf-settings' == $hook ) {
				wp_enqueue_script( 'mailchimp-optins-admin', self::$url . '/js/admin.js', array( 'jquery' ) );
			}
		}

	}

	/**
	 * The main function responsible for returning The Highlander Plugin
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * @since 3.0.0
	 * @return {class} Highlander Instance
	 */
	function NF_MailChimpOptins() {
		return NF_MailChimpOptins::instance();
	}

	NF_MailChimpOptins();

	/**
	 * Plugin supports automatic licensed updates. Comment code below if you
	 * made any changes to the code and would like to avoid automatic updates.
	 *
	 * @since 3.0.0
	 */
	if ( is_admin() ) {
		require_once plugin_dir_path( __FILE__ ) . 'libs/LicenseManagerClientV2.php';
		$license_manager = new LicenseManagerClientV2(
			'ninja-forms-mailchimp-optins',
			'Ninja Forms MailChimp Opt-ins',
			'ninja-forms-mailchimp-optins',
			'http://v2.bigtreeisland.com/api/license-manager/v1',
			'plugin',
			__FILE__
		);

		function get_item_purchase_code( $item_purchase_code )
		{
			return Ninja_Forms()->get_setting( 'envato_license' );
		}

		add_filter( 'license_manager_client_v2_ninja-forms-mailchimp-optins', 'get_item_purchase_code' );
	}

}
