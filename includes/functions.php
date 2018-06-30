<?php
/**
 * Functions
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get options
 *
 * return array options or empty when not available
 */
function tp_twitch_get_options() {
    return get_option( 'tp_twitch', array() );
}

/**
 * Get option default value
 *
 * @param $key
 *
 * @return null|string
 */
function tp_twitch_get_option_default_value( $key ) {

	switch ( $key ) {
		case 'cache_duration':
			$value = '720';
			break;
		default:
			$value = null;
			break;
	}

	return $value;
}

/**
 * Delete cache
 */
function tp_twitch_delete_cache() {

	// Cached data
	delete_option( 'tp_twitch_games' );
}

/**
 * Get games data (either from cache or API)
 *
 * @return array
 */
function tp_twitch_get_games() {

	// Looking for cached data
	$games = get_option( 'tp_twitch_games' );

	if ( $games )
		return $games;

	// Query API
	$args = array(
		'first' => 100
	);

	$games = tp_twitch_get_top_games_from_api( $args );

	// Cache data
	update_option( 'tp_twitch_games', $games );

	// Return
	return $games;
}

/**
 * Get game options
 *
 * @return array
 */
function tp_twitch_get_game_options() {

	$games = tp_twitch_get_games();

	$options = array();

	if ( is_array( $games ) && sizeof ( $games ) > 0 ) {

		$options[0] = __( 'Please select...', 'tp-twitch-widget' );

		$games = tp_twitch_array_sort( $games, 'name' );

		foreach ( $games as $game ) {

			if ( ! isset( $game['id'] ) || ! isset( $game['name'] ) )
				continue;

			$options[$game['id']] = $game['name'];
		}
	}

	return $options;
}

/**
 * Get streams
 *
 * @param array $args
 *
 * @return null
 */
function tp_twitch_get_streams( $args = array() ) {

	$streams = tp_twitch_get_streams_from_api( $args );

	$streams = tp_twitch_setup_streams_data( $streams );

	return $streams;
}

/**
 * Setup streams and maybe fetch additional data from API
 *
 * @param $streams
 *
 * @return array
 */
function tp_twitch_setup_streams_data( $streams ) {

	if ( ! is_array( $streams ) )
		return null;

	// Collect users
	$users = array();
	$user_ids = array();

	foreach ( $streams as $stream ) {

		if ( ! empty( $stream['id'] ) ) {
			$user_ids[] = $stream['id'];
		}
	}

	if ( sizeof( $user_ids ) > 0 ) {
		$users = tp_twitch_get_users_from_api();
	}
	// TODO


	$streams_data = array();

	foreach ( $streams as $stream ) {

		$data = array(
			'id' => ( isset( $stream['id'] ) ) ? $stream['id'] : 0,
			'user_id' => ( isset( $stream['user_id'] ) ) ? $stream['user_id'] : 0,
			'game_id' => ( isset( $stream['game_id'] ) ) ? $stream['game_id'] : 0,
			'community_ids' => ( isset( $stream['community_ids'] ) ) ? $stream['community_ids'] : '',
			'type' => ( isset( $stream['type'] ) ) ? $stream['type'] : '',
			'title' => ( isset( $stream['title'] ) ) ? $stream['title'] : '',
			'viewer_count' => ( isset( $stream['viewer_count'] ) ) ? $stream['viewer_count'] : 0,
			'started_at' => ( isset( $stream['started_at'] ) ) ? $stream['started_at'] : '',
			'language' => ( isset( $stream['language'] ) ) ? $stream['language'] : '',
			'thumbnail_url' => ( isset( $stream['thumbnail_url'] ) ) ? $stream['thumbnail_url'] : ''
		);

		$streams_data[] = $data;
	}

	return $streams_data;
}