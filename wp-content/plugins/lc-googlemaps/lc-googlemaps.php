<?php
/**
 *	Plugin Name: Live Composer - Google Maps Module
 *	Plugin URI: https://livecomposerplugin.com/add-ons/?utm_source=lc-lightbox&utm_medium=wp-admin/plugins-list&utm_campaign=plugin_uri
 *	Description: Adds a google maps module to Live Composer page builder plugin.
 *	Author: Live Composer Team
 * Author URI: https://livecomposerplugin.com/?utm_source=lc-lightbox&utm_medium=wp-admin/plugins-list&utm_campaign=author_uri
 *	Version: 1.1.7
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /lang
 *
 * Google Maps Module Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Google Maps Module Plugin. If not, see <http://www.gnu.org/licenses/>.
 *
 * Idea, initial development and inspiration by
 * Slobodan Kustrimovic https://github.com/BobaWebDev
 *
 * @package Live Composer - Google Maps
 */

/**
 * Main Plugin Initialization
 *
 * @return void
 */
function lcgooglemaps_plugin_init() {

	// Load our plugin only if Live Composer already initiated.
	if ( defined( 'DS_LIVE_COMPOSER_URL' ) ) {

		define( 'LC_GOOGLEMAPS_URL', plugin_dir_url( __FILE__ ) );
		define( 'LC_GOOGLEMAPS_ABS', dirname( __FILE__ ) );
		define( 'LC_GOOGLEMAPS_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
		define( 'LC_GOOGLEMAPS_VER', '1.1.7' );
		define( 'LC_GOOGLEMAPS_DEFAULT_ADDR', '14 High St, Newmarket CB8 8LB, United Kingdom' );

		include LC_GOOGLEMAPS_ABS . '/inc/functions.php';
		include LC_GOOGLEMAPS_ABS . '/inc/module.php';

		/**
		 * Add CSS
		 */
		function lcgooglemaps_style() {
			wp_enqueue_style( 'sklc-gmaps-css', LC_GOOGLEMAPS_URL . 'css/main.css' );
		}
		add_action( 'wp_enqueue_scripts', 'lcgooglemaps_style' );
	}
}
add_action( 'plugins_loaded', 'lcgooglemaps_plugin_init' );

/**
 * On plugin activation check if there is
 * previous generation of the plugin installed.
 * If found, disable these "unwanted" versions of LC Google Maps.
 *
 * @return void
 */
function lcgooglemaps_disable_old_plugin() {

	if ( stristr( __FILE__ , 'lc-googlemaps.php') ) {

		/**
		 * Deactivate the old version of plugin.
		 * New version is lc-googlemaps/lc-googlemaps.php
		 */
		$old_plugin = 'sklc-addon-googlemaps/sklc-addon-googlemaps.php';
		if ( is_plugin_active( $old_plugin ) ) {
			deactivate_plugins( $old_plugin );
		}
	}
}
register_activation_hook( __FILE__, 'lcgooglemaps_disable_old_plugin' );