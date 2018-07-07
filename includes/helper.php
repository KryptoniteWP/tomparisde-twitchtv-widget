<?php
/**
 * Helper
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check whether we are on our admin pages or not
 *
 * @return bool
 */
function tp_twitch_is_plugin_admin_area() {

	$screen = get_current_screen();

	return ( strpos( $screen->id, 'tp_twitch' ) !== false ) ? true : false;
}

/**
 * Sort array by key values
 *
 * @param $array
 * @param $key
 *
 * @return array
 */
function tp_twitch_array_sort( $array, $key ) {

	$sorter = array();
	$ret    = array();

	reset( $array );

	foreach ( $array as $ii => $va ) {
		$sorter[ $ii ] = $va[ $key ];
	}

	asort( $sorter );

	foreach ( $sorter as $ii => $va ) {
		$ret[ $ii ] = $array[ $ii ];
	}

	return $ret;
}

/**
 * Get site language
 *
 * @return string
 */
function tp_twitch_get_site_lang() {

	$lang = get_bloginfo( 'language' );

	$lang = substr( $lang, 0, 2 );

	return $lang;
}

/**
 * Better Debugging
 *
 * @param $args
 * @param bool $title
 */
function tp_twitch_debug( $args, $title = false ) {

	if ( defined( 'WP_DEBUG') && true === WP_DEBUG ) {

		if ( $title ) {
			echo '<h3>' . $title . '</h3>';
		}

		if ( $args ) {
			echo '<pre>';
			print_r($args);
			echo '</pre>';
		}
	}
}

/**
 * Debug logging
 *
 * @param $message
 */
function tp_twitch_debug_log( $message ) {

	if ( defined( 'WP_DEBUG') && true === WP_DEBUG ) {
		if (is_array( $message ) || is_object( $message ) ) {
			error_log( print_r( $message, true ) );
		} else {
			error_log( $message );
		}
	}
}