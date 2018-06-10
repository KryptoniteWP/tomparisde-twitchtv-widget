<?php
/*
Plugin Name: TomParisDE Twitch Widget - Lite
Description: Your TomParisDE Twitch Widget for Blogs, Clan-, Fan- and Community Sites
Version: 1.2.15
Plugin URI: https://tp-twitch-widget.com/
Author: Florian 'TomParisDE' Kirchner
Author URI: https://tp-twitch-widget.com/
Text Domain: tp-ttvw
Domain Path: /languages
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
        exit;
}

if( !class_exists( 'TP_TTVW' ) ) {

        /**
         * Main TP_TTVW class
         *
         * @since       1.0.0
         */
        class TP_TTVW {

                /**
                 * @var         TP_TTWV $instance The one true TP_TTVW
                 * @since       1.0.0
                 */
                private static $instance;


                /**
                 * Get active instance
                 *
                 * @access      public
                 * @since       1.0.0
                 * @return      object self::$instance The one true TP_TTVW
                 */
                public static function instance() {
                        if( !self::$instance ) {
                                self::$instance = new TP_TTVW();
                                self::$instance->setup_constants();
                                self::$instance->includes();
                                self::$instance->load_textdomain();
                        }

                        return self::$instance;
                }


                /**
                 * Setup plugin constants
                 *
                 * @access      private
                 * @since       1.0.0
                 * @return      void
                 */
                private function setup_constants() {

                        // Plugin version
                        define( 'TP_TTVW_VER', '1.2.15' );

                        // Plugin path
                        define( 'TP_TTVW_DIR', plugin_dir_path( __FILE__ ) );

                        // Plugin URL
                        define( 'TP_TTVW_URL', plugin_dir_url( __FILE__ ) );

                        // Chache
                        define('TP_TTVW_CACHE', 'tp_ttvw_cache_');
                }


                /**
                 * Include necessary files
                 *
                 * @access      private
                 * @since       1.0.0
                 * @return      void
                 */
                private function includes() {

                        // Include files and scripts
                        if (is_admin()) {
                                require_once TP_TTVW_DIR . 'includes/admin/class.settings.php';
                        }
                        require_once TP_TTVW_DIR . 'includes/functions.php';
                        require_once TP_TTVW_DIR . 'includes/widget.php';
                        require_once TP_TTVW_DIR . 'includes/twitch.php';
                }

                /**
                 * Internationalization
                 *
                 * @access      public
                 * @since       1.0.0
                 * @return      void
                 */
                public function load_textdomain() {

                        // Load the default language files
                        load_plugin_textdomain( 'tp-ttvw', false, 'tp-twitch-widget/languages' );
                }

                /*
                 * Activation function fires when the plugin is activated.
                 *
                 * @since  1.0.0
                 * @access public
                 * @return void
                 */
                public static function activation() {
                        // nothing
                }

                /*
                 * Uninstall function fires when the plugin is being uninstalled.
                 *
                 * @since  1.0.0
                 * @access public
                 * @return void
                 */
                public static function uninstall() {
                        // nothing
                }
        }

        /**
         * The main function responsible for returning the one true TP_TTVW
         * instance to functions everywhere
         *
         * @since       1.0.0
         * @return      \TP_TTVW The one true TP_TTVW
         */
        function TP_TTVW_load() {
                return TP_TTVW::instance();
        }

        /**
         * The activation & uninstall hooks are called outside of the singleton because WordPress doesn't
         * register the call from within the class hence, needs to be called outside and the
         * function also needs to be static.
         */
        register_activation_hook( __FILE__, array( 'TP_TTVW', 'activation' ) );
        register_uninstall_hook( __FILE__, array( 'TP_TTVW', 'uninstall') );

        add_action( 'plugins_loaded', 'TP_TTVW_load' );

} // End if class_exists check


function tp_ttvw_enqueue_scripts() {
        wp_enqueue_style( 'tp_ttvw_style', TP_TTVW_URL . '/public/css/style.css' );
}

add_action( 'wp_enqueue_scripts', 'tp_ttvw_enqueue_scripts' );