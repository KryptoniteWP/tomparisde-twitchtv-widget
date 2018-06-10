<?php

function tp_ttvw_get_data($url){

    $response = wp_remote_get( esc_url_raw( $url ) );

    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */

    $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

    return $api_response;
}

function tp_ttvw_the_assets() {
    echo TP_TTVW_URL . 'public/';
}

function tp_ttvw_delete_cache() {
// Get our list of transient keys from the DB.
    $transient_keys = get_option( 'tp_ttvw_cache_transient_keys' );

    if ( ! empty ( $transient_keys ) && is_array( $transient_keys ) ) {
        // For each key, delete that transient.
        foreach( $transient_keys as $t ) {
            delete_transient( $t );
        }
    }

    // Reset our DB value.
    update_option( 'tp_ttvw_cache_transient_keys', array() );
}
