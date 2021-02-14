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

	asort( $sorter, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL );

	foreach ( $sorter as $ii => $va ) {
		$ret[ $ii ] = $array[ $ii ];
	}

	return $ret;
}

/**
 * Add missing games
 *
 * @param   array
 * @param   array
 *
 * @return  array
 */
function tp_twitch_maybe_add_missing_games( $games, $missing_games ) {

    foreach ( $missing_games as $missing_game ) {

        if ( ! isset ( $games[ $missing_game['id'] ] ) ) {
            $games[ $missing_game['id'] ] = $missing_game;
        }
    }

	return $games;
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
 * Check whether user is admin or not
 *
 * @return bool
 */
function tp_twitch_is_user_admin() {

	if ( ! function_exists( 'wp_get_current_user' ) )
		include_once( ABSPATH . 'wp-includes/pluggable.php' );

	return ( current_user_can('manage_options' ) ) ? true : false;
}

/**
 * Sanitize comma separated input
 *
 * @param $input
 * @return mixed
 */
function tp_twitch_sanitize_comma_separated_input( $input ) {
    return str_replace( array( ';', ' ' ), array( ',', ',' ), trim( sanitize_text_field( $input ) ) );
}

/**
 * Output data to a log for debugging reasons
 **/
function tp_twitch_addlog( $string ) {

    $log = get_option( 'tp_twitch_log', '' );
    $string = date( 'd.m.Y H:i:s' ) . " >>> " . $string . "\n";
    $log .= $string;
    update_option( 'tp_twitch_log', $log );
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