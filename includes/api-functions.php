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
 * @param   array $args
 * @return  mixed
 */
function tp_twitch_get_top_games_from_api( $args = array() ) {

    $response = tp_twitch()->api->get_top_games( $args );

    if ( empty( $response['data'] ) || ! is_array( $response['data'] ) )
        return null;

    return apply_filters( 'tp_twitch_api_top_games', $response['data'], $response, $args );
}

/**
 * Get streams from API
 *
 * @param   array $args
 * @return  array
 */
function tp_twitch_get_streams_from_api( $args = array() ) {

	// Convert non-standard arguments
	if ( isset( $args['streamer'] ) ) {

        $args['user_login'] = $args['streamer'];
        unset( $args['streamer'] );
    }

    // Call API
    $response = tp_twitch()->api->get_streams( $args );

    //tp_twitch_debug( $response, 'tp_twitch_get_streams_from_api > $response' );

    if ( empty( $response['data'] ) || ! is_array( $response['data'] ) )
        return array();

    return apply_filters( 'tp_twitch_api_streams', $response['data'], $response, $args );
}

/**
 * Get users from API
 *
 * @param   array $args
 * @return  array
 */
function tp_twitch_get_users_from_api( $args = array() ) {

    if ( empty( $args['user_id'] ) && empty( $args['user_login'] ) ) {
        return array();
    }

    // Convert non-standard arguments
	if ( isset( $args['user_id'] ) )
		$args['id'] = $args['user_id'];

	if ( isset( $args['user_login'] ) )
		$args['login'] = $args['user_login'];

    // Call API
	$results = tp_twitch()->api->get_users( $args );

    return ( isset( $results['data'] ) && is_array( $results['data'] ) && sizeof( $results['data'] ) > 0 ) ? $results['data'] : array();
}