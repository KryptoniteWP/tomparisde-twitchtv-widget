<?php

function tp_debug($args)
{
    echo '<pre>';
    print_r($args);
    echo '</pre>';
}

function tp_get_channel_data( $channel )
{

    // Get cache
    $cache = get_transient( TP_TTVW_CACHE . $channel );

    if (!empty ($cache) && !WP_DEBUG)  {
        return $cache;
    }

    $options = get_option('tp_ttvw');
    if (!empty ($options['api_key'])) {
        $api_key = $options['api_key'];
    }
    else {
        return false;
    }

    $data = array('username' => $channel);

    // Get basic data
    $basic = tp_ttvw_get_data('https://api.twitch.tv/kraken/channels/' . $channel . '?client_id=' . $api_key);

    if (!isset ( $basic['display_name'] ) ) {
        return false;
    }

    $data['display_name'] = $basic['display_name'];
    $data['broadcaster_language'] = $basic['broadcaster_language'];
    $data['channel_url'] = $basic['url'];
    $data['views'] = $basic['views'];
    $data['followers'] = $basic['followers'];
    $data['logo'] = $basic['logo'];
    $data['twitch_partner'] = $basic['partner'];

    // Get live data
    $live = tp_ttvw_get_data('https://api.twitch.tv/kraken/streams/' . $channel . '?client_id=' . $api_key);

    if (!empty ($live['stream'])) {
        $data['live'] = true;
        $data['viewers'] = $live['stream']['viewers'];
        $data['preview'] = $live['stream']['preview']['small'];
        $data['preview_medium'] = $live['stream']['preview']['medium'];
        $data['preview_large'] = $live['stream']['preview']['large'];
        $data['aktiv_game'] = $live['stream']['channel']['game'];
        $data['channel_title'] = $live['stream']['channel']['status'];

    } else {
        $data['live'] = false;
    }

    // Logic
    if (!empty ($data['preview'])) {
        $data['image'] = $data['preview'];
    } elseif (!empty ($data['logo'])) {
        $data['image'] = $data['logo'];
    } else {
        $data['image'] = TP_TTVW_URL . 'public/img/twitch-logo-45x45.png';
    }

    // Save cache
    $cache_duration = (isset ($options['cache_duration'])) ? intval($options['cache_duration']) : 600;
    $transient_key = TP_TTVW_CACHE . $channel;
    set_transient( $transient_key, $data, $cache_duration );

    // Get the current list of transients.
    $transient_keys = get_option( 'tp_ttvw_cache_transient_keys', array() );

    // Appent new transient key
    $transient_keys[] = $transient_key;

    // Update the list of transients
    update_option( 'tp_ttvw_cache_transient_keys', $transient_keys );

    return $data;
}

function tp_get_channel_or_game_data( $twitch_game, $max_games, $twitch_streamer_language ){

    $transient_key = TP_TTVW_CACHE . serialize( $twitch_game . $max_games . $twitch_streamer_language );

    // Get cache
    $cache = get_transient( $transient_key );

    if (!empty ($cache) && !WP_DEBUG ) {
        return $cache;
    }

    $options = get_option('tp_ttvw');
    if (!empty ($options['api_key'])) {
        $api_key = $options['api_key'];
    }
    else {
        return false;
    }

    $data = array();

    // Get live data
    $ergebnis = tp_ttvw_get_data('https://api.twitch.tv/kraken/streams/?game=' . $twitch_game . '&limit=' . $max_games . '&language=' . $twitch_streamer_language .'&client_id=' . $api_key);

    if (!isset ( $ergebnis['streams'] ) ) {
        return false;
    }

    foreach ( $ergebnis['streams'] as $item ) {

        if (!isset ( $item['channel']['name'] ) ) {
            continue;
        }

        $data_stream = array();

        $data_stream['live'] = true;

        $data_stream['username'] = (isset ($item['channel']['name'])) ? $item['channel']['name'] : '';
        $data_stream['display_name'] = $item['channel']['display_name'];
        $data_stream['broadcaster_language'] = $item['channel']['broadcaster_language'];
        $data_stream['channel_url'] = $item['channel']['url'];
        $data_stream['views'] = $item['channel']['views'];
        $data_stream['followers'] = $item['channel']['followers'];
        $data_stream['logo'] = $item['channel']['logo'];
        $data_stream['twitch_partner'] = $item['channel']['partner'];

        $data_stream['viewers'] = $item['viewers'];
        $data_stream['preview'] = $item['preview']['small'];
        $data_stream['preview_medium'] = $item['preview']['medium'];
        $data_stream['preview_large'] = $item['preview']['large'];
        $data_stream['aktiv_game'] = $item['channel']['game'];
        $data_stream['channel_title'] = $item['channel']['status'];

        // Logic
        if (!empty ($data_stream['preview'])) {
            $data_stream['image'] = $data_stream['preview'];
        } elseif (!empty ($data_stream['logo'])) {
            $data_stream['image'] = $data_stream['logo'];
        } else {
            $data_stream['image'] = TP_TTVW_URL . 'public/img/twitch-logo-45x45.png';
        }

        $data[] = $data_stream;

    }

    // Save cache
    $cache_duration = (isset ($options['cache_duration'])) ? intval($options['cache_duration']) : 600;
    set_transient( $transient_key, $data, $cache_duration );

    // Get the current list of transients.
    $transient_keys = get_option( 'tp_ttvw_cache_transient_keys', array() );

    // Appent new transient key
    $transient_keys[] = $transient_key;

    // Update the list of transients
    update_option( 'tp_ttvw_cache_transient_keys', $transient_keys );

    return $data;

}