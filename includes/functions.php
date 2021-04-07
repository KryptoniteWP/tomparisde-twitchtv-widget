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
 * Update options
 *
 * @param $options
 */
function tp_twitch_update_options( $options ) {
    update_option( 'tp_twitch', $options );
}

/**
 * Get single option (incl. default value)
 *
 * @param $key
 * @param string $default
 *
 * @return null|string
 */
function tp_twitch_get_option( $key, $default = '' ) {

	$options = tp_twitch_get_options();

	if ( isset( $options[$key] ) )
		return $options[$key];

	return ( $default ) ? $default : tp_twitch_get_option_default_value( $key );
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
			$value = 60;
			break;
        case 'language':
            $value = 'en';
            break;
        case 'widget_style':
            $value = 'white';
            break;
		case 'widget_size':
			$value = 'large';
			break;
		case 'widget_preview':
			$value = 'image';
			break;
		default:
			$value = null;
			break;
	}

	$value = apply_filters( 'tp_twitch_option_default_value', $value, $key );

	return $value;
}

/**
 * Delete cache
 */
function tp_twitch_delete_cache() {

	global $wpdb;

	$sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%_transient_tp_twitch_%"';

	$wpdb->query( $sql );

    if ( tp_twitch_is_pro_version() ) {
        set_transient( 'tp_twitch_delete_cache', '1', 10 ); // 10 sec
    }
}

/**
 * Delete streams cache
 */
function tp_twitch_delete_streams_cache() {

	global $wpdb;

	$sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%_transient_tp_twitch_streams_%"';

	$wpdb->query( $sql );
}

/**
 * Get games data (either from cache or API)
 *
 * @return array
 */
function tp_twitch_get_games() {

	// Looking for cached data
	$games = get_transient( 'tp_twitch_games' );

	// Looking for data via API
	if ( empty( $games ) ) {

        // Query API
        $args = array(
            'first' => 100
        );

        $games = tp_twitch_get_top_games_from_api( $args );

        if ( empty( $games ) )
            return null;

        $indexed_games = array(); // We need the game id to make them accessible

        foreach ( $games as $game ) {

            if ( ! isset( $game['id'] ) || ! isset( $game['name'] ) )
                continue;

            $indexed_games[$game['id']] = $game;
        }

        $games = $indexed_games;

        // Cache data
        set_transient( 'tp_twitch_games', $games, 7 * DAY_IN_SECONDS );
    }

	// Hook
	$games = apply_filters( 'tp_twitch_games', $games );

	// Return
	return $games;
}

/**
 * Get game by id
 *
 * @param   $id
 * @return  array
 */
function tp_twitch_get_game_by_id( $id ) {

    //tp_twitch_debug_log( __FUNCTION__ );

	if ( empty ( $id ) )
		return array();

	$games = tp_twitch_get_games();

    foreach ( $games as $game ) {

        if ( $game['id'] == $id )
            return $game;
    }

    return array();
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

		$options[0] = __( 'Please select...', 'tomparisde-twitchtv-widget' );

		$games = tp_twitch_array_sort( $games, 'name' );

		foreach ( $games as $game ) {

			if ( ! isset( $game['id'] ) || ! isset( $game['name'] ) )
				continue;

			$options[$game['id']] = $game['name'];
		}
	} else {
		$options[0] = __( 'Please connect to API first...', 'tomparisde-twitchtv-widget' );
	}

	return $options;
}

/**
 * Get languages
 *
 * Source #1: Twitch Language Selector
 * Source #2: https://gist.githubusercontent.com/DimazzzZ/4e2a5a6c8c6f67900091/raw/3dc51cb81ba4bb93c9e7ce7e9c4bb8abbd9ca782/iso-639-1-codes.php
 *
 * @return array
 */
function tp_twitch_get_languages() {

	return array(
		'da' => __('Danish', 'tp-twitch-game' ),
		'de' => __( 'German', 'tp-twitch-game' ),
		'en' => __( 'English', 'tp-twitch-game' ),
		'en-gb' => __('English (UK)', 'tp-twitch-game' ),
		'es' => __( 'Spanish', 'tp-twitch-game' ),
		'es-mx' => __( 'Spanish (Latin American)', 'tp-twitch-game' ),
		'fr' => __( 'French', 'tp-twitch-game' ),
		'it' => __( 'Italian', 'tp-twitch-game' ),
		'hu' => __( 'Hungarian', 'tp-twitch-game' ),
		'nl' => __( 'Dutch', 'tp-twitch-game' ),
		'no' => __( 'Norwegian', 'tp-twitch-game' ),
		'pl' => __( 'Polish', 'tp-twitch-game' ),
		'pt' => __( 'Portuguese', 'tp-twitch-game' ),
		'pt-br' => __( 'Portuguese (Brazil)', 'tp-twitch-game' ),
		'sk' => __( 'Slovenian', 'tp-twitch-game' ),
		'fi' => __( 'Finnish', 'tp-twitch-game' ),
		'sv' => __( 'Swedish', 'tp-twitch-game' ),
		'vi' => __( 'Vietnamese', 'tp-twitch-game' ),
		'tr' => __( 'Turkish', 'tp-twitch-game' ),
		'cs' => __( 'Czech', 'tp-twitch-game' ),
		'el' => __( 'Greek', 'tp-twitch-game' ),
		'bg' => __( 'Bulgarian', 'tp-twitch-game' ),
		'ru' => __( 'Russian', 'tp-twitch-game' ),
		'ar' => __( 'Arabic', 'tp-twitch-game' ),
		'th' => __( 'Thai', 'tp-twitch-game' ),
		'zh-cn' => __( 'Chinese', 'tp-twitch-game' ),
		'zh-tw' => __( 'Chinese (Traditional)', 'tp-twitch-game' ),
		'ja' => __( 'Japanese', 'tp-twitch-game' ),
		'ko' => __( 'Korean', 'tp-twitch-game' ),
		'hi' => __( 'Hindi', 'tp-twitch-game' ),
		'ro' => __( 'Romanian', 'tp-twitch-game' ),
	);
}

/**
 * Get language options
 *
 * @return array
 */
function tp_twitch_get_language_options() {

	$languages = tp_twitch_get_languages();

	$options = array();

	if ( is_array( $languages ) && sizeof ( $languages ) > 0 ) {

		asort($languages );

		$options = array(
			'' => __( 'Please select...', 'tomparisde-twitchtv-widget' )
		);

		$options = array_merge( $options, $languages );
	}

	return $options;
}

/**
 * Get widget style options
 *
 * @param bool $is_settings_page
 * @return array
 */
function tp_twitch_get_widget_style_options( $is_settings_page = true ) {

    $options = ( ! $is_settings_page ) ? array( '' => __( 'Standard (Settings Page)' ) ) : array();

    $options['white'] = __( 'Default', 'tomparisde-twitchtv-widget' );

    $options = apply_filters( 'tp_twitch_style_options', $options );

    return $options;
}

/**
 * Get widget size options
 *
 * @param bool $is_settings_page
 * @return array
 */
function tp_twitch_get_widget_size_options( $is_settings_page = true ) {

    $options = ( ! $is_settings_page ) ? array( '' => __( 'Standard (Settings Page)' ) ) : array();

    $options['large'] = __( 'Large', 'tomparisde-twitchtv-widget' );
    $options['small'] = __( 'Small', 'tomparisde-twitchtv-widget' );
    $options['large-first'] = __( 'First Large, Others Small', 'tomparisde-twitchtv-widget' );

    return $options;
}

/**
 * Get widget preview options
 *
 * @param bool $is_settings_page
 * @return array
 */
function tp_twitch_get_widget_preview_options( $is_settings_page = true ) {

    $options = ( ! $is_settings_page ) ? array( '' => __( 'Standard (Settings Page)' ) ) : array();

    $options['image'] = __( 'Image', 'tomparisde-twitchtv-widget' );
    $options['video'] = __( 'Video', 'tomparisde-twitchtv-widget' );
    $options['video-first'] = __( 'First Video, Others Images', 'tomparisde-twitchtv-widget' );

    return $options;
}

/**
 * Get streams key based on arguments
 *
 * @param array $args
 *
 * @return string
 */
function tp_twitch_get_streams_key( $args = array() ) {
	return 'tp_twitch_streams_' . md5( json_encode( $args ) );
}

/**
 * Get streams cache
 *
 * @param $args
 *
 * @return mixed
 */
function tp_twitch_get_streams_cache( $args ) {

	$streams_key = tp_twitch_get_streams_key( $args );

	$streams = get_transient( $streams_key );

	return ( ! empty( $streams ) ) ? $streams : array();
}

/**
 * Set streams cache
 *
 * @param $streams
 * @param $args
 */
function tp_twitch_set_streams_cache( $streams, $args ) {

	$options = tp_twitch_get_options();

	$cache_duration = ( ! empty( $options['cache_duration'] ) && is_numeric( $options['cache_duration'] ) ) ? $options['cache_duration'] : tp_twitch_get_option_default_value( 'cache_duration' );

	//tp_twitch_debug_log( 'tp_twitch_set_streams_cache >> $cache_duration: ' . $cache_duration );

	// Generate streams key
	$streams_key = tp_twitch_get_streams_key( $args );

	// Cache data
	set_transient( $streams_key, $streams, $cache_duration * MINUTE_IN_SECONDS );
}

/**
 * Get streams
 *
 * @param array $args
 * @param array $output_args
 * @return array
 */
function tp_twitch_get_streams( $args = array(), $output_args = array() ) {

    //tp_twitch_debug_log( __FUNCTION__ );

    //tp_twitch_debug_log( '$args: ' );
    //tp_twitch_debug_log( $args );

    $streams = array();

    $args = tp_twitch_prepare_streams_args( $args, $output_args );

    //tp_twitch_debug( $args, __FUNCTION__ . ' > $args' );

    if ( ! isset( $args['no_cache'] ) || true != $args['no_cache'] ) {

        $streams = tp_twitch_get_streams_cache( $args );

        if ( ! empty( $streams ) ) {
            return tp_twitch_setup_streams( $streams );
        }
    }

	//tp_twitch_debug( 'tp_twitch_get_streams >> no cache!' );

    if ( ! isset( $args['max'] ) || ! is_numeric( $args['max'] ) ) {

        $streams_data =tp_twitch_get_streams_from_api( $args );

        $streams = tp_twitch_setup_streams_data( $streams_data, $args );

    } else {

        $allowed_limit = 100;

        if ( ! isset( $args['pagination'] ) ) {

            $args['first'] = ( $args['max'] > $allowed_limit ) ? $allowed_limit : $args['max'];

            unset( $args['max'] );

            $streams_data = tp_twitch_get_streams_from_api( $args );

            $streams = tp_twitch_setup_streams_data( $streams_data, $args );

        } else {
            $streams = apply_filters( 'tp_twitch_stream_pagination', $args );
        }
    }

	if ( ! empty( $streams ) )
		tp_twitch_set_streams_cache( $streams, $args );

	return tp_twitch_setup_streams( $streams );
}

/**
 * Prepare streams args
 *
 * @param $args
 * @param $output_args
 * @return mixed
 */
function tp_twitch_prepare_streams_args( $args, $output_args ) {

    if ( empty( $args['streamer'] ) && empty( $args['game_id'] ) ) {

        if ( empty( $output_args['max'] ) || ! is_numeric( $output_args['max'] ) ) {
            return $args;
        }

        $max = intval( $output_args['max'] );

        if ( $max > apply_filters( 'tp_twitch_streams_max', tp_twitch_get_default_streams_max() ) ) {

            $args['max'] = apply_filters( 'tp_twitch_streams_max', tp_twitch_get_default_streams_max() );
        }

        return $args;
    }

    // Maximum number of objects to return allowed by twitch API for a single request
    $allowed_limit = 100;

    if ( ! empty( $args['streamer'] ) ) {

        //tp_twitch_debug_log( __FUNCTION__ . ' > $args BEFORE FILTER:' );
        //tp_twitch_debug_log( $args );

        $args['streamer'] = strtolower( $args['streamer'] );

        // Trim possible spaces
        $args['streamer'] = str_replace( ' ', '', $args['streamer'] );

        $stream_array = explode( ',', $args['streamer'] );

        $stream_count = count( $stream_array );

        // Count of streamers <= 100

        if ( $stream_count <= $allowed_limit ) {

            if ( empty( $output_args['max'] ) || ! is_numeric( $output_args['max'] ) ) {
                return $args;
            }

            if ( intval( $output_args['max'] ) > apply_filters( 'tp_twitch_streams_max', tp_twitch_get_default_streams_max() ) ) {

                $args['max'] = apply_filters( 'tp_twitch_streams_max', tp_twitch_get_default_streams_max() );
            } else {
                $args['max'] = intval( $output_args['max'] );
            }

            return $args;
        }

        // Count of streamers > 100

        $args['pagination'] = $stream_count;

        if ( empty( $output_args['max'] ) || ! is_numeric( $output_args['max'] ) ) {

            $args['max'] = $allowed_limit;
        } else {
            $args['max'] = intval( $output_args['max'] );
        }

        if ( $args['max'] <= $allowed_limit ) {
            $stream_array = array_slice( $stream_array, 0,  $args['max'] - 1 );
        }

        $args['streamer'] = implode( ',', $stream_array );
    }

    return $args;
}

/**
 * Setup streams
 *
 * @param $streams
 *
 * @return array
 */
function tp_twitch_setup_streams( $streams ) {

	if ( ! is_array( $streams ) )
		return $streams;

	// Build objects
	$streams_objects = array();

	if ( sizeof( $streams ) > 0 ) {

		foreach ( $streams as $stream ) {
			$streams_objects[] = ( is_array( $stream ) ) ? new TP_Twitch_Stream( $stream ) : $stream;
		}
	}

	return $streams_objects;
}

/**
 * Setup streams and maybe fetch additional data from API
 *
 * @param $streams
 * @param $streams_args
 * @return array
 */
function tp_twitch_setup_streams_data( $streams, $streams_args ) {

	if ( ! is_array( $streams ) )
        $streams = array();

	// Collect users
	$user_ids = array();

	if ( sizeof( $streams ) > 0 ) {

        foreach ( $streams as $stream ) {

            if ( ! empty( $stream['user_id'] ) ) {
                $user_ids[] = $stream['user_id'];
            }
        }
    }

	$users = tp_twitch_get_users_from_api( array( 'user_id' => $user_ids ) );

	// Handling offline users/streams
    if ( ! empty ( $streams_args['streamer'] ) ) {

        $user_logins_fetched = array();

        // Collecting already fetched user logins
        if ( sizeof( $users ) > 0 ) {

            foreach ( $users as $user ) {

                if ( ! isset ( $user['login'] ) || in_array( $user['login'], $user_logins_fetched ) )
                    continue;

                $user_logins_fetched[] = $user['login'];
            }
        }

        //tp_twitch_debug( $user_logins_fetched, '$user_logins_fetched' );

        // Collect not yet fetched user logins
        $user_logins_missing = array();

        $user_logins = explode(',', $streams_args['streamer'] );

        foreach ( $user_logins as $user_login ) {

            if ( ! in_array( $user_login, $user_logins_fetched ) )
                $user_logins_missing[] = $user_login;
        }

        //tp_twitch_debug( $user_logins_missing, '$user_logins_missing' );

        // Maybe fetch missing users
        if ( sizeof( $user_logins_missing ) > 0 ) {
            $users_offline = tp_twitch_get_users_from_api( array( 'user_login' => $user_logins_missing ) );

            //tp_twitch_debug( $users_offline, '$users_offline' );

            if ( is_array( $users_offline ) && sizeof( $users_offline ) > 0 ) {

                $users = array_merge( $users, $users_offline );
            }
        }
    }

    //tp_twitch_debug( $users, '$users live + offline' );

	// Prepare users data
	$users_data = array();

	if ( sizeof( $users ) > 0 ) {

		foreach ( $users as $user ) {

			/* Exemplary data.
			[id] => 19571641
            [login] => ninja
			[display_name] => Ninja
			[type] =>
            [broadcaster_type] => partner
			[description] => Professional Battle Royale player. Follow my twitter @Ninja and for more content subscribe to my Youtube.com/Ninja
			[profile_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/6d942669-203f-464d-8623-db376ff971e0-profile_image-300x300.png
            [offline_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/ninja-channel_offline_image-bb607ec9e64184fa-1920x1080.png
            [view_count] => 235274410
			*/

			$user_data = array(
				'id' => ( isset( $user['id'] ) ) ? $user['id'] : 0,
				'login' => ( isset( $user['login'] ) ) ? $user['login'] : '',
				'display_name' => ( isset( $user['display_name'] ) ) ? $user['display_name'] : '',
				'type' => ( isset( $user['type'] ) ) ? $user['type'] : '',
				'broadcaster_type' => ( isset( $user['broadcaster_type'] ) ) ? $user['broadcaster_type'] : '',
				'description' => ( isset( $user['description'] ) ) ? $user['description'] : '',
				'profile_image_url' => ( isset( $user['profile_image_url'] ) ) ? $user['profile_image_url'] : '',
				'offline_image_url' => ( isset( $user['offline_image_url'] ) ) ? $user['offline_image_url'] : '',
				'view_count' => ( isset( $user['view_count'] ) ) ? $user['view_count'] : 0,
			);

			$users_data[$user_data['id']] = $user_data;
		}
	}

    //tp_twitch_debug( $users_data, '$users_data' );

	// Prepare streams
    $user_streams = array();

    if ( sizeof( $streams ) > 0 ) {

        foreach ( $streams as $stream ) {

            if ( ! isset( $stream['user_id'] ) )
                continue;

            $user_streams[$stream['user_id']] = $stream;
        }
    }

    //tp_twitch_debug( $user_streams, '$user_streams' );

	// Build streams data
	$streams_data = array();

    foreach ( $users_data as $user_data ) {

        if ( ! isset($user_data['id'] ) )
            continue;

        $stream = (isset($user_streams[$user_data['id']])) ? $user_streams[$user_data['id']] : array();

        $stream_data = array(
            'id' => (isset($stream['id'])) ? $stream['id'] : 0,
            'game_id' => (isset($stream['game_id'])) ? $stream['game_id'] : 0,
            'community_ids' => (isset($stream['community_ids'])) ? $stream['community_ids'] : '',
            'type' => (isset($stream['type'])) ? $stream['type'] : 'offline',
            'title' => (isset($stream['title'])) ? $stream['title'] : '',
            'viewer_count' => (isset($stream['viewer_count'])) ? $stream['viewer_count'] : 0,
            'started_at' => (isset($stream['started_at'])) ? $stream['started_at'] : '',
            'language' => (isset($stream['language'])) ? $stream['language'] : '',
            'thumbnail_url' => (isset($stream['thumbnail_url'])) ? $stream['thumbnail_url'] : '',
            'user' => $user_data,
        );

        $streams_data[] = $stream_data;
    }

    //tp_twitch_debug( $streams_data, 'tp_twitch_setup_streams_data() >> $streams_data' );

	return $streams_data;
}

/**
 * Get default streams max
 *
 * @return int
 */
function tp_twitch_get_default_streams_max() {
    return 3;
}

/**
 * Get current site host
 *
 * @return string
 */
function tp_twitch_get_site_host() {

	$parsed_url = parse_url( get_site_url() );

	return $parsed_url['host'];
}