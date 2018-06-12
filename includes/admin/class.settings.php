<?php
/**
 * Settings
 *
 * Source: https://codex.wordpress.org/Settings_API
 *
 * @package     PluginName\Settings
 * @since       1.0.0
 */

// https://github.com/flowdee/wordpress-plugin-boilerplate

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('TP_TTVW_Settings')) {

    class TP_TTVW_Settings
    {
        public $options;

        public function __construct()
        {
            // Options
            $this->options = get_option('tp_ttvw');

            // Initialize
            add_action('admin_menu', array( &$this, 'add_admin_menu') );
            add_action('admin_init', array( &$this, 'init_settings') );
        }

        function add_admin_menu()
        {
            /*
             * Source: https://codex.wordpress.org/Function_Reference/add_options_page
             */
            add_options_page(
                'Twitch Widget - Lite', // Page title
                'Twitch Widget - Lite', // Menu title
                'manage_options', // Capabilities
                'tp_ttvw', // Menu slug
                array( &$this, 'options_page' ) // Callback
            );

        }

        function init_settings()
        {
            register_setting(
                'tp_ttvw',
                'tp_ttvw',
                array( &$this, 'validate_input_callback' )
            );

            // SECTION ONE
            add_settings_section(
                'tp_ttvw_section_one',
                false,
                false,
                'tp_ttvw'
            );

            add_settings_field(
                'tp_ttvw_api_key',
                __('Client ID', 'tp-ttvw'),
                array(&$this, 'api_key_render'),
                'tp_ttvw',
                'tp_ttvw_section_one',
                array('label_for' => 'tp_ttvw_api_key')
            );

            add_settings_field(
                'tp_ttvw_caching',
                __('Caching', 'tp-ttvw'),
                array(&$this, 'caching_render'),
                'tp_ttvw',
                'tp_ttvw_section_one',
                array('label_for' => 'tp_ttvw_caching')
            );

         }

        function validate_input_callback( $input ) {

            /*
             * Here you can validate (and manipulate) the user input before saving to the database
             */

            // Handle cache deletion
            if ( isset ( $input['delete_cache'] ) && $input['delete_cache'] === '1' ) {
                tp_ttvw_delete_cache();
                $input['delete_cache'] = '0';
            }

            return $input;
        }


        function api_key_render() {


            $api_key = ( isset ( $this->options['api_key'] ) ) ? $this->options['api_key'] : '';

            ?>

            <input type="text" id="tp_ttvw_api_key" name="tp_ttvw[api_key]" value="<?php echo $api_key; ?>" />


            <?php
        }


        function caching_render() {

            $select_options = array(
                '60' => __('1 minute', 'tp-ttvw'),
                '300' => __('5 minutes', 'tp-ttvw'),
                '600' => __('10 minutes', 'tp-ttvw'),
                '900' => __('15 minutes', 'tp-ttvw'),
                '1800' => __('30 minutes', 'tp-ttvw'),
                '2700' => __('45 minutes', 'tp-ttvw'),
                '3600' => __('60 minutes', 'tp-ttvw')
            );

            $selected = ( isset ( $this->options['cache_duration'] ) ) ? $this->options['cache_duration'] : '600';

            ?>
            <select id="tp_ttvw_caching" name="tp_ttvw[cache_duration]">
                <?php foreach ( $select_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $selected, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>

            <input type="hidden" id="tp_ttvw_delete_cache" name="tp_ttvw[delete_cache]" value="0" />

            <?php
        }

        function options_page() {
            ?>


            <script>
                    jQuery( document ).on( 'click', '#tp-ttvw-delete-cache-submit', function(event) {
                        jQuery('#tp_ttvw_delete_cache').val('1');
                    });
            </script>


            <div class="wrap">

                <h2><?php _e('Twitch Widget Settings', 'tp-tw'); ?></h2>

                <form action="options.php" method="post">

                    <?php
                    settings_fields( 'tp_ttvw' );
                    do_settings_sections( 'tp_ttvw' );
                    ?>

                    <p>
                        <?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?>
                        &nbsp;
                        <?php submit_button( 'Delete cache', 'delete button-secondary', 'tp-ttvw-delete-cache-submit', false ); ?>
                    </p>

                </form>

                <hr />

                <h3>Ressources</h3>
                <ul>
                    <li><a href="https://wordpress.org/plugins/tomparisde-twitchtv-widget/" target="_blank" rel="nofollow">Plugin Page</a></li>
                    <li><a href="https://kryptonitewp.com/demo/tp-twitch-widget/" target="_blank" rel="nofollow">Live Demonstration</a></li>
                    <li><a href="https://kryptonitewp.com/support/knb/tp-twitch-widget-documentation/" target="_blank" rel="nofollow">Online Documentation</a></li>
                </ul>

            </div>
            <?php
        }
    }
}

new TP_TTVW_Settings();