<?php
/**
 * Hooks
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Maybe manipulate the streams output
 *
 * @param $streams
 * @param $streams_args
 * @param $output_args
 * @return mixed
 */
function tp_twitch_manipulate_display_streams( $streams, $streams_args, $output_args ) {

    if ( ! is_array( $streams ) || sizeof( $streams ) === 0 )
        return $streams;

    //tp_twitch_debug_log( $streams );

    // Hide offline users
    if ( isset( $output_args['hide_offline'] ) && true === $output_args['hide_offline'] ) {

        foreach ( $streams as $stream_key => $stream_obj ) {

            if ( ! $stream_obj->is_live() )
                unset( $streams[$stream_key] );
        }
    }

    $streams = apply_filters( 'tp_twitch_manipulate_display_streams', $streams, $streams_args, $output_args );

    // Max
    if ( isset( $output_args['max'] ) && is_numeric( $output_args['max'] ) && sizeof( $streams ) > intval( $output_args['max'] ) ) {
        $streams = array_slice( $streams, 0, intval( $output_args['max'] ) );
    }

    return $streams;
}
add_filter( 'tp_twitch_display_streams', 'tp_twitch_manipulate_display_streams', 10, 3 );

/**
 * Extend available games
 *
 * Info: By default we only have access to the top games, so we may have to extend this list
 *
 * @param $games
 * @return mixed
 */
function tp_twitch_extend_available_games( $games ) {

    $games[490379] = array(
        'id'          => 490379,
        'name'        => "Tom Clancy's Ghost Recon: Wildlands",
        'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/Tom%20Clancy%27s%20Ghost%20Recon:%20Wildlands-{width}x{height}.jpg'
    );

    $games[510146] = array(
        'id'          => 510146,
        'name'        => "MLB The Show 19",
        'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/MLB%20The%20Show%2019-{width}x{height}.jpg'
    );

    $games[511496] = array(
        'id'          => 511496,
        'name'        => "Out Of The Park Baseball 20",
        'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/Out%20of%20the%20Park%20Baseball%2019-{width}x{height}.jpg'
    );

    $games[500626] = array(
        'id'          => 500626,
        'name'        => "Soulcalibur VI",
        'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/Soulcalibur%VI-{width}x{height}.jpg'
    );

    $games[513170] = array(
        'id'          => 513170,
        'name'        => "NHL 20",
        'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/NHL%2020-{width}x{height}.jpg'
    );

    return $games;
}
add_filter( 'tp_twitch_games', 'tp_twitch_extend_available_games', 10 );
