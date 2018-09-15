<?php
/**
 *  Plugin Name: Live Composer - Premium Extensions
 *  Plugin URI: https://livecomposerplugin.com/downloads/extensions/?utm_source=lcproext&utm_medium=wp-admin/plugins-list&utm_campaign=plugin_uri
 *  Description: Extend Live Composer with premium extensions
 *  Author: Live Composer Team
 *  Version: 1.1
 *  Author URI: https://livecomposerplugin.com/?utm_source=lcproext&utm_medium=wp-admin/plugins-list&utm_campaign=author_uri
 *  License: GPL3
 *  License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *  Domain Path: /lang
 *
 *  Live Composer - Premium Extensions Plugin is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Live Composer - Premium Extensions. If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package Live Composer - Premium Extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Run plugin only if the Live Composer plugin is loaded and active.
 */
function lcproext_plugin_init() {

	if ( class_exists( 'DSLC_Module' ) && version_compare( DS_LIVE_COMPOSER_VER, '1.3.9', '>=' )  ) {

		// Start Plugin.
		$lcproext_core = new LC_Extensions_Core;

	} else {
		/**
		 * Admin Notice
		 */
		function lcproext_inactive_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '%sCan\'t activate the Premium Extensions Plugin.%s The latest version of %sLive Composer%s plugin required.', 'lcmenupro' ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/live-composer-page-builder/">', '</a>' ); ?></p>
		</div>
		<?php }
		add_action( 'admin_notices', 'lcproext_inactive_notice' );

	} // End if class_exists check.

} add_action( 'init', 'lcproext_plugin_init', 1 );

if ( ! class_exists( 'LC_Extensions_Core' ) ):

	/**
	 * Core Class
	 */
	class LC_Extensions_Core {

		/**
		 * Was this class ever instantiated?
		 *
		 * @var bool
		 */
		public static $initiated = false;

		/**
		 * Current version of the plugin.
		 *
		 * @var string
		 */
		private static $version;

		/**
		 * Define plugin slug based on the folder name.
		 *
		 * @var string
		 */
		private static $slug;

		/**
		 * Define plugin absolute path based.
		 *
		 * @var string
		 */
		private static $abspath;

		/**
		 * Define url to the plugin folder.
		 *
		 * @var string
		 */
		private static $dirurl;

		/**
		 * Array with information about each premium module.
		 *
		 * @var array
		 */
		private static $extensions;

		/**
		 * Define default extension status: active=true/inactive=false.
		 *
		 * @var bool
		 */
		private static $default_extension_status;


		/**
		 * Do all the required job on core object creation.
		 */
		function __construct() {
			// Do nothing if LiveComposer is not activated.
			if ( ! class_exists( 'DSLC_Module', false ) ) {
				error_log ( 'Live Composer not loaded' );
				return;
			}

			// Actions that needs to be lunched only once.
			if ( ! self::$initiated ) {

				$this->set_abspath();
				$this->set_dirurl();
				$this->set_slug();
				$this->set_version();
				$this->load_plugin_data();

				self::$initiated = true;

				$this->require_files();
				$this->require_extensions();

				add_filter( 'dslc_extensions_meta', array( $this,'filter_lc_extensions' ) );
				add_action( 'dslc_toggle_extension', array( $this,'toggle_extension' ) );
				add_action( 'admin_init', array( $this, 'license_manager_init'), 9);
			}
		}

		/**
		 * Register plugins that require premium 
		 */
		static public function license_manager_init() {

			// Require License Manager for Premium Extensions Plugin.
			return new LC_License_Manager(
				array(
					'slug' => 'lc-extensions',
					'product_id' => 8138,
					'file' => __FILE__,
					'version' => self::$version,
					'author' => 'Live Composer Team',
				)
			);
		}

		/**
		 * Update $slug member variable with right value
		 * based on the current name of the plugin folder.
		 */
		public function set_slug() {
			$plugin_path_array = explode( '/', plugin_basename( __FILE__) );
			self::$slug = $plugin_path_array[0];
		}

		static public function get_slug() {
			return self::$slug;
		}

		public function set_dirurl() {
			self::$dirurl = plugin_dir_url( __FILE__ );
		}

		static function get_dirurl() {
			return self::$dirurl;
		}

		/**
		 * Update $abspath member variable with right value.
		 */
		public function set_abspath() {
			self::$abspath = __DIR__;
		}

		static function get_abspath() {
			return self::$abspath;
		}

		/**
		 * Update $version member variable with a right value.
		 */
		public function set_version() {
			// $version = get_file_data( self::$abspath . '/' . __FILE__ . '.php', array( 'Version' ), 'plugin' );
			$version = get_file_data( __FILE__, array( 'Version' ), 'plugin' );
			self::$version = $version[0];
		}

		/**
		 * Load saved data from the database.
		 * For now we are storing only extension status in the database.
		 *
		 * @return void
		 */
		public function load_plugin_data() {
			$plugin_data = get_option( 'lcextpro', false );

			if ( $plugin_data && isset( $plugin_data['extensions'] ) ) {
				$extensions_db = $plugin_data['extensions'];

				// Iterate through class $extensions property to update
				// extension status based on data saved in the database.
				foreach ( $extensions_db as $extension_id => $extension) {
					self::$extensions[ $extension_id ]['active'] = $extension['active'];
				}
			}
		}

		/**
		 * Save plugin data in the database.
		 *
		 * @return void
		 */
		public function save_plugin_data() {
			$plugin_data = get_option( 'lcextpro', false );

			// We are saving only extension id and status.
			foreach ( self::$extensions as $extension_id => $extension) {
				$plugin_data['extensions'][ $extension_id ][ 'active' ] = $extension['active'];
			}

			// $plugin_data['extensions'] = self::$extensions;

			update_option( 'lcextpro', $plugin_data );
		}

		/**
		 * Required actions on plugin bootstrap.
		 *
		 * @return void
		 */
		public function require_files() {

			// require_once $this->get_abspath() . '/example/example.php';
		}

		/**
		 * Load extensions.
		 *
		 * @return void
		 */
		public function require_extensions() {
			$directories = glob( self::$abspath . '/extensions/*', GLOB_ONLYDIR );

			foreach ( $directories as $dir ) {
				$extensionpath = $dir . '/extension.php';
				if ( file_exists( $extensionpath ) ) {
					require_once $extensionpath;
				}
			}
		}

		/**
		 * Add meta about new extension
		 *
		 * @return void
		 */
		static public function register_extension( $meta ) {
			$id = '';
			$title = '';
			$thumbnail = '';
			$details = '';
			$description = '';
			$rank = 100; // 100 is the last / 0 is the first.
			$active = self::$default_extension_status; // Default extensions status.

			// Id or extensions slug.
			if ( isset( $meta['id'] ) ) {
				$id = $meta['id'];
			}

			// Extension title.
			if ( isset( $meta['title'] ) ) {
				$title = $meta['title'];
			}

			// Picture to use on extensions listing page.
			if ( isset( $meta['thumbnail'] ) ) {
				$thumbnail = $meta['thumbnail'];
			} else {
				$file = 'extensions/' . $id . '/thumbnail.png';
				$file_path = self::get_abspath() . '/' . $file;

				if ( file_exists ( $file_path ) ) {
					$thumbnail = self::get_dirurl() . $file; 
				} else {
					$thumbnail = false;
				}
			}

			// URL to the more infromation about the extension.
			if ( isset( $meta['details'] ) ) {
				$details = $meta['details'];
			}

			// A few lines with description about this extension.
			if ( isset( $meta['description'] ) ) {
				$description = $meta['description'];
			}

			// Check if self::$extensions is already have infromation regarding
			// status of this extension.
			if ( isset( self::$extensions[ $id ] ) && 
					isset( self::$extensions[ $id ]['active'] )  ) {
				$active = self::$extensions[ $id ]['active'];
			}

			// Rank used to sort the extensions on the listing page.
			if ( isset( $meta['rank'] ) ) {
				$rank = $meta['rank'];
			}

			// Check if active SEOWP theme.
			if ( function_exists( 'lbmn_setup' ) ) {
				$demo = true;

				$lc_license_manager = self::license_manager_init();
				$license_status     = $lc_license_manager->get_license_status('lc-extensions');

				if ( 'valid' === $license_status ) {
					$demo = false;
				} elseif ( 'cptsupport' === $meta['id'] || 'googlemaps' === $meta['id'] || 'menu' === $meta['id'] ) {
					$demo = false;
				}
			} else {
				$demo = false;
			}

			// Register extension in the class attribute.
			self::$extensions[ $meta['id'] ] = array(
				'title' => $title, 
				'thumbnail' => $thumbnail,
				'details' => $details,
				'description' => $description,
				'active' => $active,
				'rank' => $rank,
				'demo' => $demo
			);
		}

		/**
		 * Get meta about all extensions
		 *
		 * @return array
		 */
		public function get_extensions() {
			return self::$extensions;
		}

		public function filter_lc_extensions( $extensions ) {

			$extensions = $this->get_extensions();
			return $extensions;

		}

		public function toggle_extension( $extension_id ) {

			foreach ( self::$extensions as $id => $value) {
				if ( $extension_id === $id ) {
					self::$extensions[$id]['active'] = ! self::$extensions[$id]['active'];
				}
			}

			// Make sure changed status is stored in the database.
			$this->save_plugin_data();
		}

		static public function is_extension_active( $extension_id ) {
			if ( isset( self::$extensions[ $extension_id ] ) && 
					isset( self::$extensions[ $extension_id ]['active'] ) ) {
				return self::$extensions[ $extension_id ]['active'];
			} else {
				return self::$default_extension_status;
			}
		}
	}

endif; // if ( ! class_exists( 'LC_Extensions_Core' ) ).

/**
 * In some cases we need to disable our legacy plugins that
 * are bundled now into this single set.
 * Also we are redirecting users to the pugin settings on activation. 
 *
 * @return void
 */
function lcproext_detect_plugin_activation( $plugin ) {
	// ex.: $plugin = akismet/akismet.php

	$old_plugin_slugs = array(
		// Google Maps
		'lc-googlemaps/lc-googlemaps.php',
		'sklc-addon-googlemaps/sklc-addon-googlemaps.php',
		// Gallery
		'lc-gallery/sklc-addon-gallery-images.php',
		'sklc-addon-gallery-images/sklc-addon-gallery-images.php',
		// Video
		'lc-video-embed/lc-video-embed.php',
		'lc-extension-video-embed/lc-video-embed.php',
		'lc-module-video-embed/lc-module-video-embed.php',
		// Animations
		'lc-addon-animations/lc-addon-animations.php',
		// Posts Links
		'sklc-addon-prev-next-post/sklc-addon-prev-next-post.php',
		// Per Content Width
		'sklc-per-page-content-width/sklc-per-page-content-width.php',
		// Before/After Image slider
		'sklc-before-after-image/sklc-before-after-image.php',
		// LineIcons
		'sklc-linecons-icons/sklc-linecons-icons.php',
		// Templates for CPT
		'lc-templatesforcpt/lc-templatesforcpt.php',
		// ACF integration
		'lc-acfintegration/lc-acfintegration.php',
		// Sliders integration
		'lc-sliders-integration/lc-masterslider.php',
		// Menu Pro
		'lc-menu-pro/lc-menu-pro.php',
	);

	// Deactivate the old version of plugin.
	foreach ( $old_plugin_slugs as $old_plugin ) {
		if ( is_plugin_active( $old_plugin ) ) {
			deactivate_plugins( $old_plugin );
		}
	}

	/*
	 * If a custom using the theme SEOWP
	 * then a need to activate the module
	 * CPT Support, Mega Menu, Google Maps
	 */
	if ( function_exists( 'lbmn_setup' ) ) {
		if ( empty( get_option( 'lcextpro' ) ) ) {
			$plugin_data   = get_option( 'lcextpro', false );
			$plugin_status = array(
				'extensions' => array(
					'cptsupport' => array(
						'active' => true,
					),
					'googlemaps' => array(
						'active' => true,
					),
					'menu' => array(
						'active' => true,
					),
				),
			);

			update_option( 'lcextpro', $plugin_status );
		}
	}

	// Redirection on plugin activation to the settings.
	if ( plugin_basename( __FILE__ ) === $plugin ) {
		// Make Welcome screen optional.
		$show_welcome_screen = true;
		if ( ! apply_filters( 'lcproext_show_welcome_screen', $show_welcome_screen ) ) {
			return;
		}

		// Bail if activating from network, or bulk.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || isset( $_GET['tgmpa-activate'] ) || isset( $_GET['tgmpa-install'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=dslc_plugin_options&tab=extensions' ) );
		exit; // ! important to keep this exit line
		// Function wp_redirect() does not exit automatically and should almost always be followed by exit.
	}

} add_action( 'activated_plugin', 'lcproext_detect_plugin_activation' );
