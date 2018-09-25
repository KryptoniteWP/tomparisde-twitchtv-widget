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

    // Plugin already up2date
    //if ( $version_installed === TP_TWITCH_VERSION )
      //  return;

    //if ( $version_installed === TP_TWITCH_VERSION || ( ! empty( $version_installed ) && version_compare( TP_TWITCH_VERSION, $version_installed, '<' ) ) )
    //    return;

    /*
     * Loop updates
     ---------------------------------------------------------- */

    // v2.0 (Rebuild)
    if ( ! empty ( get_option( 'tp_ttvw' ) ) ) {
        tp_twitch_plugin_upgrade_v2_rebuild();
    }

    tp_twitch_plugin_upgrade_v2_rebuild();


    if ( ! empty( $version_installed ) ) {

        // 2.0 rebuild
        //if ( version_compare( $version_installed, '3.0.0', '<' ) )
           //aawp_admin_plugin_upgrade_rebuild_cleanup();

    }
    /* ---------------------------------------------------------- */

    // Update installed version
    //update_option( 'tp_twitch_version', TP_TWITCH_VERSION );
}
add_action( 'admin_init', 'tp_twitch_plugin_upgrades' );

/**
 * Version 2 Rebuild
 */
function tp_twitch_plugin_upgrade_v2_rebuild() {

    echo 'blub';

    // Delete options
    //delete_option( 'tp_ttvw' );

    // Add admin notice
    add_action( 'admin_notices', function() {
        ?>
        <div class="updated notice tp-twitch-notice is-dismissible">
            <p>Thank you for using this plugin! <strong>You are awesome</strong>.</p>
        </div>
        <?php
    });
}