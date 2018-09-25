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

    // Max
    if ( isset( $output_args['max'] ) && is_numeric( $output_args['max'] ) && sizeof( $streams ) > intval( $output_args['max'] ) ) {
        $streams = array_slice( $streams, 0, intval( $output_args['max'] ) );
    }

    return $streams;
}
add_filter( 'tp_twitch_display_streams', 'tp_twitch_manipulate_display_streams', 10, 3 );