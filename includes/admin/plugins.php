<?php
/**
 * Plugins
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function tp_twitch_action_links( $links, $file ) {

	if ( $file != 'tomparisde-twitchtv-widget/tp-twitch-widget.php' )
		return $links;

    $settings_link = '<a href="' . admin_url( 'options-general.php?page=tp_twitch' ) . '">' . esc_html__( 'Settings', 'tomparisde-twitchtv-widget' ) . '</a>';

	array_unshift( $links, $settings_link );

    return $links;
}
add_filter( 'plugin_action_links', 'tp_twitch_action_links', 10, 2 );

/**
 * Plugin row meta links
 *
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function tp_twitch_row_meta( $input, $file ) {

    if ( $file != 'tomparisde-twitchtv-widget/tp-twitch-widget.php' )
        return $input;

    $docs_link = esc_url( add_query_arg( array(
            'utm_source'   => 'plugins-page',
            'utm_medium'   => 'plugin-row',
            'utm_campaign' => 'Twitch WP',
        ), TP_TWITCH_DOCS_URL )
    );

    $links = array(
        '<a href="' . $docs_link . '">' . esc_html__( 'Documentation', 'tomparisde-twitchtv-widget' ) . '</a>',
    );

    $input = array_merge( $input, $links );

    return $input;
}
add_filter( 'plugin_row_meta', 'tp_twitch_row_meta', 10, 2 );