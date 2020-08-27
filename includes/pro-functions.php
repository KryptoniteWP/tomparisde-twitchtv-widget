<?php
/**
 * Pro Version Functions
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check whether pro version is installed or not
 *
 * @return bool
 */
function tp_twitch_is_pro_version() {
    return ( function_exists( 'TP_Twitch_Pro') ) ? true : false;
}

/**
 * Get upgrade url
 *
 * @param string $source
 * @param string $medium
 * @return string
 */
function tp_twitch_get_pro_version_url( $source = '', $medium = '' ) {

    return esc_url( add_query_arg( array(
            'utm_source'   => $source,
            'utm_medium'   => $medium,
            'utm_campaign' => 'Twitch WP',
        ), 'https://kryptonitewp.com/downloads/twitch-wordpress-pro/' )
    );
}

/**
 * Output the widget streams max note
 */
function tp_twitch_pre_pro_the_widget_upgrade_info() {

    if ( tp_twitch_is_pro_version() )
        return;
    ?>
    <p>
        <span class="dashicons dashicons-star-filled"></span>&nbsp;<?php printf( wp_kses( __( 'You want more features? <a href="%s" target="_blank" rel="nofollow">Check out the Pro version</a>!', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( tp_twitch_get_pro_version_url( 'widgets-page', 'textlink' ) ) ); ?>
    </p>
    <?php
}

/**
 * Output the widget streams max note
 */
function tp_twitch_pre_pro_the_widget_streams_max_note() {

    if ( tp_twitch_is_pro_version() )
        return;

    echo '<small>';
    printf( esc_html__( 'You can display a maximum of %d streams.', 'tomparisde-twitchtv-widget' ), tp_twitch_get_default_streams_max() );
    echo '<br />';
    printf( wp_kses( __( '<a href="%s" target="_blank">Upgrade now</a> in order to show more streams.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => array( '_blank' ) ) ) ), esc_url( tp_twitch_get_pro_version_url( 'widgets-page', 'streams-max-note' ) ) );
    echo '</small>';
}

/**
 * Output the styles note
 */
function tp_twitch_pre_pro_the_styles_note() {

    if ( tp_twitch_is_pro_version() )
        return;

    echo '<small>';
    printf( wp_kses( __( 'You would like to have more designs to choose from? <a href="%s" target="_blank">Upgrade now</a>.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => array( '_blank' ) ) ) ), esc_url( tp_twitch_get_pro_version_url( 'widgets-page', 'styles-note' ) ) );
    echo '</small>';
}

/**
 * Output the available games note
 */
function tp_twitch_pre_pro_the_available_games_note() {

    if ( tp_twitch_is_pro_version() )
      return;

    echo '<small>';
    printf( wp_kses( __( 'There are <strong>+100 more games</strong> available in the PRO version! <a href="%s" target="_blank">Upgrade now</a>.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => array( '_blank' ) ), 'strong' => array() ) ), esc_url( tp_twitch_get_pro_version_url( 'widgets-page', 'styles-note' ) ) );
    echo '</small>';
}