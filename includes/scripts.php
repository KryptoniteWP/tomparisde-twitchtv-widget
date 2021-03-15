<?php
/**
 * Scripts
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load admin scripts
 *
 * @since       2.0.0
 * @return      void
 */
function tp_twitch_admin_scripts( $hook ) {

	/**
	 *Settings page only
	 */
	$screen = get_current_screen();

	//if ( tp_twitch_is_plugin_admin_area() || ( isset( $screen->base ) && 'widgets' === $screen->base ) ) {

		wp_enqueue_script( 'tp-twitch-admin', TP_TWITCH_PLUGIN_URL . 'assets/dist/js/admin.js', array( 'jquery' ), TP_TWITCH_VERSION );
		wp_enqueue_style( 'tp-twitch-admin', TP_TWITCH_PLUGIN_URL . 'assets/dist/css/admin.css', false, TP_TWITCH_VERSION );

		do_action( 'tp_twitch_enqueue_admin_scripts' );
	//}
}

add_action( 'admin_enqueue_scripts', 'tp_twitch_admin_scripts', 100 );

/**
 * Load frontend scripts
 *
 * @since       2.0.0
 * @return      void
 */
function tp_twitch_scripts( $hook ) {

	wp_enqueue_script( 'tp-twitch', TP_TWITCH_PLUGIN_URL . 'assets/dist/js/main.js', array( 'jquery' ), TP_TWITCH_VERSION, true );
	wp_enqueue_style( 'tp-twitch', TP_TWITCH_PLUGIN_URL . 'assets/dist/css/main.css', false, TP_TWITCH_VERSION );

	do_action( 'tp_twitch_enqueue_scripts' );
}

add_action( 'wp_enqueue_scripts', 'tp_twitch_scripts' );