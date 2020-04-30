<?php
/**
 * Admin Notices
 *
 * @since       3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handling admin notices
 *
 * @since       3.0.0
 */
function tp_twitch_admin_notices() {

    $notices = array();

    if ( ! empty ( tp_twitch_get_option( 'api_client_id' ) ) && empty ( tp_twitch_get_option( 'api_client_secret' ) ) ) {

        $notices[] = array(
            'type' => 'warning',
            'content' =>
                '<p><strong>' . __( 'Action required:', 'tomparisde-twitchtv-widget' ) . '</strong>&nbsp;' .
                sprintf( wp_kses( __( 'Starting on April 30, 2020, the Twitch API requires additional API credentials. Please go to the <a href="%s">settings page</a> and complete your API credentials.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'options-general.php?page=tp_twitch' ) ) ) . '</p>'
        );
    }

    if ( empty ( $notices ) )
        return;

    // Add notices to WordPress.
    add_action( 'admin_notices', function() use ( $notices ) {

        foreach ( $notices as $notice ) {

            if ( empty ( $notice['type'] ) || empty ( $notice['content'] ) )
                continue;
            ?>
            <div class="notice-<?php echo esc_attr( $notice['type'] ); ?> notice tp-twitch-notice is-dismissible">
                <?php echo $notice['content']; ?>
            </div>
            <?php
        }

    });
}
add_action( 'admin_init', 'tp_twitch_admin_notices', 20 ); // Make sure this hook loads after upgrade routine.