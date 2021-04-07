<?php
/**
 * Plugin Name:     Twitch for WordPress
 * Plugin URI:      https://de.wordpress.org/plugins/tomparisde-twitchtv-widget/
 * Description:     Display Twitch streams on your sidebars.
 * Version:         3.2.3
 * Author:          KryptoniteWP
 * Author URI:      https://kryptonitewp.com
 * Text Domain:     tomparisde-twitchtv-widget
 *
 * @author          KryptoniteWP
 * @copyright       Copyright (c) KryptoniteWP
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'TP_Twitch' ) ) :

	/**
	 * Main TP_Twitch class
	 *
	 * @since       2.0.0
	 */
	final class TP_Twitch {
		/** Singleton *************************************************************/

		/**
		 * TP_Twitch instance.
		 *
		 * @access private
		 * @since  2.0.0
		 * @var    TP_Twitch The one true TP_Twitch
		 */
		private static $instance;

		/**
		 * The version number of TP_Twitch.
		 *
		 * @access private
		 * @since  2.0.0
		 * @var    string
		 */
		private $version = '3.2.3';

		/**
		 * The settings instance variable.
		 *
		 * @access public
		 * @since  2.0.0
		 * @var    TP_Twitch_Settings
		 */
		public $settings;

		/**
		 * The api instance variable.
		 *
		 * @access public
		 * @since  2.0.0
		 * @var    TP_Twitch_API
		 */
		public $api;

		/**
		 * Main TP_Twitch Instance
		 *
		 * Insures that only one instance of TP_Twitch exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @uses TP_Twitch::setup_globals() Setup the globals needed
		 * @uses TP_Twitch::includes() Include the required files
		 * @uses TP_Twitch::setup_actions() Setup the hooks and actions
		 * @return TP_Twitch
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof TP_Twitch ) ) {
				self::$instance = new TP_Twitch;

				if( version_compare( PHP_VERSION, '5.3', '<' ) ) {

					add_action( 'admin_notices', array( 'TP_Twitch', 'below_php_version_notice' ) );

					return self::$instance;

				}

				self::$instance->setup_constants();
				self::$instance->includes();

				add_action( 'plugins_loaded', array( self::$instance, 'setup_objects' ), -1 );
				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			}
			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 4.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tomparisde-twitchtv-widget' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 4.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tomparisde-twitchtv-widget' ), '1.0' );
		}

		/**
		 * Show a warning to sites running PHP < 5.3
		 *
		 * @static
		 * @access private
		 * @since 4.0.0
		 * @return void
		 */
		public static function below_php_version_notice() {
			?>
			<div class="error">
				<p>
					<?php sprintf( esc_html__( 'Your version of PHP is below the minimum version of PHP required by our Twitch plugin. Please contact your hosting company and request that your version will be upgraded to %1$s or later.', 'tomparisde-twitchtv-widget' ), '5.3' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'TP_TWITCH_VERSION' ) ) {
				define( 'TP_TWITCH_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'TP_TWITCH_PLUGIN_DIR' ) ) {
				define( 'TP_TWITCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'TP_TWITCH_PLUGIN_URL' ) ) {
				define( 'TP_TWITCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'TP_TWITCH_PLUGIN_FILE' ) ) {
				define( 'TP_TWITCH_PLUGIN_FILE', __FILE__ );
			}

			// WordPress.org URL
            if ( ! defined( 'TP_TWITCH_WP_ORG_URL' ) ) {
                define( 'TP_TWITCH_WP_ORG_URL', 'https://wordpress.org/plugins/tomparisde-twitchtv-widget/');
            }

			// Docs URL
			if ( ! defined( 'TP_TWITCH_DOCS_URL' ) ) {
				define( 'TP_TWITCH_DOCS_URL', 'https://kryptonitewp.com/docs/article/twitch-wordpress-documentation/' );
			}
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			// Helper & essential functions
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/helper.php';
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/functions.php';
            require_once TP_TWITCH_PLUGIN_DIR . 'includes/pro-functions.php';
            require_once TP_TWITCH_PLUGIN_DIR . 'includes/hooks.php';

			// Core
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/class-twitch-api.php';
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/class-stream.php';
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/api-functions.php';
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/template-functions.php';
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/widget.php';

			// Other
			require_once TP_TWITCH_PLUGIN_DIR . 'includes/scripts.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once TP_TWITCH_PLUGIN_DIR . 'includes/admin/plugins.php';
				require_once TP_TWITCH_PLUGIN_DIR . 'includes/admin/class-settings.php';
				require_once TP_TWITCH_PLUGIN_DIR . 'includes/admin/upgrades.php';
                require_once TP_TWITCH_PLUGIN_DIR . 'includes/admin/notices.php';
			}
		}

		/**
		 * Setup all objects
		 *
		 * @access public
		 * @since 1.6.2
		 * @return void
		 */
		public function setup_objects() {

			self::$instance->api = new TP_Twitch_API();

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				self::$instance->settings = new TP_Twitch_Settings();
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( TP_TWITCH_PLUGIN_FILE ) ) . '/languages/';

			/**
			 * Filters the languages directory path to use for TP_Twitch.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'tp_twitch_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter

			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Defines the plugin language locale used in TP_Twitch.
			 *
			 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
			 *                  otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'tomparisde-twitchtv-widget' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'tomparisde-twitchtv-widget', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/tomparisde-twitchtv-widget/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/tomparisde-twitchtv-widget/ folder
				load_textdomain( 'tomparisde-twitchtv-widget', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/tomparisde-twitchtv-widget/languages/ folder
				load_textdomain( 'tomparisde-twitchtv-widget', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'tomparisde-twitchtv-widget', false, $lang_dir );
			}
		}
	}
endif; // End if class_exists check

/**
 * The main function responsible for returning the one true TP_Twitch
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $tp_twitch = tp_twitch(); ?>
 *
 * @since 1.0
 * @return TP_Twitch The one true TP_Twitch Instance
 */
function tp_twitch() {
	return TP_Twitch::instance();
}
tp_twitch();

