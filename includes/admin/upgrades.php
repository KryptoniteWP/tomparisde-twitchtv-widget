<?php
/**
 * Plugin Upgrades
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handling plugin upgrades
 */
function tp_twitch_plugin_upgrades() {

    $version_installed = get_option( 'tp_twitch_version', '' );

    //tp_twitch_debug_log( 'tp_twitch_plugin_upgrades() >> $version_installed: ' . $version_installed );
    //tp_twitch_debug_log( 'tp_twitch_plugin_upgrades() >> TP_TWITCH_VERSION: ' . TP_TWITCH_VERSION );

    // Plugin already up2date
    if ( $version_installed === TP_TWITCH_VERSION )
        return;

    /*
     * Loop updates
     ---------------------------------------------------------- */

    // v2.0 (Rebuild)
    if ( ! empty ( get_option( 'tp_ttvw' ) ) )
        tp_twitch_plugin_upgrade_v2_rebuild();

    if ( ! empty( $version_installed ) ) {

        // API Auth Changes
        if ( version_compare( $version_installed, '3.0.0', '<' ) )
            tp_twitch_plugin_upgrade_v3();

    }
    /* ---------------------------------------------------------- */

    // Update installed version
    update_option( 'tp_twitch_version', TP_TWITCH_VERSION );
}
add_action( 'admin_init', 'tp_twitch_plugin_upgrades', 10 );

/**
 * Version 2 Rebuild
 */
function tp_twitch_plugin_upgrade_v2_rebuild() {

    // Delete options
    delete_option( 'tp_ttvw' );

    // Add admin notice
    add_action( 'admin_notices', function() {
        ?>
        <div class="notice-warning notice tp-twitch-notice is-dismissible">
            <p><?php esc_html_e('Welcome to our brand new Stream Embed for Twitch plugin!', 'tomparisde-twitchtv-widget' ); ?></p>
            <p><?php esc_html_e('We made a complete rebuild of our plugin which allows us to implement more amazing features for you. Please complete the following steps in order to continue using the plugin:', 'tomparisde-twitchtv-widget' ); ?></p>
            <ol>
                <?php
                // translators: %s: URL to the settings page
                $settings_link = sprintf( wp_kses( __( 'Please go to the <a href="%s">settings page</a> and enter your credentials for the new Twitch API.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'options-general.php?page=tp_twitch' ) ) );
                ?>
                <li><?php echo wp_kses_post($settings_link); ?></li>
                <?php
                // translators: %s: URL to the widgets page
                $widgets_link = sprintf( wp_kses( __( 'Afterward, visit the <a href="%s">widgets page</a> and place our new widgets in your sidebar.', 'tomparisde-twitchtv-widget' ),
                        array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'widgets.php' ) )
                );
                ?>
                <li><?php echo wp_kses_post($widgets_link); ?></li>
            </ol>
            <p><?php esc_html_e('Thank you for using our plugin! You are awesome.', 'tomparisde-twitchtv-widget' ); ?></p>
        </div>
        <?php
    });
}

/**
 * Version 3
 *
 * - API Client Secret required
 */
function tp_twitch_plugin_upgrade_v3() {

    if ( ! empty ( tp_twitch_get_option( 'api_client_id' ) ) && empty ( tp_twitch_get_option( 'api_client_secret' ) ) ) {

        $options = tp_twitch_get_options();

        // Reset API status.
        $options['api_status'] = false;
        tp_twitch_update_options( $options );
    }
}