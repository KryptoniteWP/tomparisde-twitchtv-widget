<?php
/**
 * API Functions
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get top games from API
 *
 * @param array $args
 *
 * @return null
 */
function tp_twitch_get_top_games_from_api( $args = array() ) {

	$results = tp_twitch()->api->get_top_games( $args );

    return ( isset( $results['data'] ) && is_array( $results['data'] ) && sizeof( $results['data'] ) > 0 ) ? $results['data'] : null;
}

/**
 * Get streams from API
 *
 * @param array $args
 *
 * @return null
 */
function tp_twitch_get_streams_from_api( $args = array() ) {

	// Convert non-standard arguments
	if ( isset( $args['streamer'] ) )
		$args['user_login'] = $args['streamer'];

	if ( isset( $args['max'] ) )
		$args['first'] = $args['max'];

	// Call API
	$results = tp_twitch()->api->get_streams( $args );

    //tp_twitch_debug( $results, 'tp_twitch_get_streams_from_api > $results' );

	return ( isset( $results['data'] ) && is_array( $results['data'] ) && sizeof( $results['data'] ) > 0 ) ? $results['data'] : null;
}

/**
 * Get users from API
 *
 * @param array $args
 *
 * @return null
 */
function tp_twitch_get_users_from_api( $args = array() ) {

	// Convert non-standard arguments
	if ( isset( $args['user_id'] ) )
		$args['id'] = $args['user_id'];

	if ( isset( $args['user_login'] ) )
		$args['login'] = $args['user_login'];

	// Call API
	$results = tp_twitch()->api->get_users( $args );

    return ( isset( $results['data'] ) && is_array( $results['data'] ) && sizeof( $results['data'] ) > 0 ) ? $results['data'] : null;
}