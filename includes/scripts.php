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

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? '' : '.min';

	/**
	 *Settings page only
	 */
	$screen = get_current_screen();

	//if ( tp_twitch_is_plugin_admin_area() || ( isset( $screen->base ) && 'widgets' === $screen->base ) ) {

		wp_enqueue_script( 'tp-twitch-admin-script', TP_TWITCH_PLUGIN_URL . 'public/js/admin' . $suffix . '.js', array( 'jquery' ), TP_TWITCH_VERSION );
		wp_enqueue_style( 'tp-twitch-admin-style', TP_TWITCH_PLUGIN_URL . 'public/css/admin' . $suffix . '.css', false, TP_TWITCH_VERSION );

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

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'tp-twitch-script', TP_TWITCH_PLUGIN_URL . 'public/js/scripts' . $suffix . '.js', array( 'jquery' ), TP_TWITCH_VERSION, true );
	wp_enqueue_style( 'tp-twitch-style', TP_TWITCH_PLUGIN_URL . 'public/css/styles' . $suffix . '.css', false, TP_TWITCH_VERSION );

	do_action( 'tp_twitch_enqueue_scripts' );
}

add_action( 'wp_enqueue_scripts', 'tp_twitch_scripts' );
