<?php
/**
 * Settings Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TP_Twitch_Settings' ) ) {

	class TP_Twitch_Settings {

		public $options;

		/**
		 * MailerLite_RSS_Feed_Image_Settings constructor.
		 */
		public function __construct() {

			// Options
			$this->options = tp_twitch_get_options();

			// Initialize
			add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( &$this, 'init_settings' ) );
		}

		/**
		 * Register admin menu
		 */
		function add_admin_menu() {

			add_options_page(
				'Twitch for WordPress', // Page title
				'Twitch', // Menu title
				'manage_options', // Capabilities
				'tp_twitch', // Menu slug
				array( &$this, 'options_page' ) // Callback
			);

		}

		/**
		 * Register settings
		 */
		function init_settings() {

			register_setting(
				'tp_twitch',
				'tp_twitch',
				array( &$this, 'validate_input_callback' )
			);

			add_settings_section(
				'tp_twitch_api',
				__( 'API Settings', 'tp-twitch-widget' ),
				array( &$this, 'section_api_render' ),
				'tp_twitch'
			);

			add_settings_field(
				'tp_twitch_api_status',
				__( 'Status', 'tp-twitch-widget' ),
				array( &$this, 'api_status_render' ),
				'tp_twitch',
				'tp_twitch_api',
				array('label_for' => 'tp_twitch_api_status')
			);

			add_settings_field(
				'tp_twitch_api_client_id',
				__( 'Client ID', 'tp-twitch-widget' ),
				array( &$this, 'api_client_id_render' ),
				'tp_twitch',
				'tp_twitch_api',
				array('label_for' => 'tp_twitch_api_client_id')
			);

			add_settings_section(
				'tp_twitch_general',
				__( 'General Settings', 'tp-twitch-widget' ),
				array( &$this, 'section_general_render' ),
				'tp_twitch'
			);

			add_settings_field(
				'tp_twitch_cache_duration',
				__( 'Cache Duration', 'tp-twitch-widget' ),
				array( &$this, 'cache_duration_render' ),
				'tp_twitch',
                'tp_twitch_general',
				array('label_for' => 'tp_twitch_cache_duration')
			);

			add_settings_section(
				'tp_twitch_defaults',
				__( 'Default Settings', 'tp-twitch-widget' ),
				array( &$this, 'section_defaults_render' ),
				'tp_twitch'
			);

			add_settings_field(
				'tp_twitch_game',
				__( 'Game', 'tp-twitch-widget' ),
				array( &$this, 'game_render' ),
				'tp_twitch',
				'tp_twitch_defaults',
				array('label_for' => 'tp_twitch_game')
			);

			add_settings_field(
				'tp_twitch_language',
				__( 'Language', 'tp-twitch-widget' ),
				array( &$this, 'language_render' ),
				'tp_twitch',
				'tp_twitch_defaults',
				array('label_for' => 'tp_twitch_language')
			);

		}

		/**
         * Validate input callback
         *
		 * @param $input
		 *
		 * @return mixed
		 */
		function validate_input_callback( $input ) {

		    //tp_twitch_debug_log( $input );

			$api_status = ( isset ( $this->options['api_status'] ) ) ? $this->options['api_status'] : false;
			$api_error = ( isset ( $this->options['api_error'] ) ) ? $this->options['api_error'] : '';

			if ( ! empty ( $input['api_client_id'] ) ) {

				$api_client_id = ( isset ( $this->options['api_client_id'] ) ) ? $this->options['api_client_id'] : '';
				$api_client_id_new = $input['api_client_id'];

				if ( $api_client_id_new != $api_client_id ) {

					$result = tp_twitch()->api->verify_client_id( $api_client_id_new );

					$api_status = ( is_array( $result ) && isset( $result['data'] ) ) ? true : false;
					$api_error = ( ! empty ( $result['error'] ) ) ? $result['error'] : '';
				}

			} else {
			    // Client ID empty leads always to a false status
				$api_status = false;
            }

			$input['api_status'] = $api_status;
			$input['api_error'] = $api_error;

            // Handle Delete Cache Action
			if ( isset ( $input['delete_cache'] ) && '1' === $input['delete_cache'] ) {
				tp_twitch_delete_cache();
				$input['delete_cache'] = '0';
			}

			return $input;
		}

		/**
		 * Section API description
		 */
		function section_api_render() {

			?>
            <p><?php printf( wp_kses( __( 'In order to show Twitch streams, this plugin requires access to the official <a href="%s" target="_blank" rel="nofollow">Twitch API</a>.', 'tp-twitch-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( 'https://dev.twitch.tv/' ) ); ?>&nbsp;</p>
			<?php
		}

		/**
		 * API status
		 */
		function api_status_render() {

			$api_status = ( isset ( $this->options['api_status'] ) && true === $this->options['api_status'] ) ? true : false;
			$api_error = ( isset( $this->options['api_error'] ) ) ? $this->options['api_error'] : '';
			?>
            <?php if ( $api_status ) { ?>
                <span style="font-weight: bold; color: green;"><?php _e( 'Connected', 'tp-twitch-widget' ); ?></span>
            <?php } else { ?>
                <span style="font-weight: bold; color: red;"><?php _e( 'Disconnected', 'tp-twitch-widget' ); ?></span>
            <?php } ?>

            <?php if ( ! empty( $api_error ) ) { ?>
                <code><?php echo esc_html( $api_error ); ?></code>
            <?php } ?>
			<?php
		}

		/**
		 * API Client ID
		 */
		function api_client_id_render() {

			$api_client_id = ( isset ( $this->options['api_client_id'] ) ) ? $this->options['api_client_id'] : '';

			?>
            <input id="tp_twitch_api_client_id" class="regular-text" name="tp_twitch[api_client_id]" type="text" value="<?php echo esc_html( $api_client_id ); ?>" />
            <p class="description">
                <?php printf( wp_kses( __( 'We created a detailed guide which shows you <a href="%s" target="_blank" rel="nofollow">how to get your client id</a>.', 'tp-twitch-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( add_query_arg( array(
	                'utm_source'   => 'settings-page',
	                'utm_medium'   => 'api-client-id',
	                'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) ); ?>
            </p>
			<?php
		}

		/**
		 * Section general description
		 */
		function section_general_render() {

			?>
            <p><?php _e('Here you set up general settings which will be used plugin wide.', 'tp-twitch-widget' ); ?></p>
			<?php
		}

		/**
		 * Cache Duration
		 */
		function cache_duration_render() {

			$cache_duration_options = array(
                1 => sprintf( esc_html( _n( '%d hour', '%d hours', 1, 'tp-twitch-widget'  ) ), 1 ),
                2 => sprintf( esc_html( _n( '%d hour', '%d hours', 2, 'tp-twitch-widget'  ) ), 2 ),
                3 => sprintf( esc_html( _n( '%d hour', '%d hours', 3, 'tp-twitch-widget'  ) ), 3 ),
                4 => sprintf( esc_html( _n( '%d hour', '%d hours', 4, 'tp-twitch-widget'  ) ), 4 ),
				6 => sprintf( esc_html( _n( '%d hour', '%d hours', 6, 'tp-twitch-widget'  ) ), 6 ),
				12 => sprintf( esc_html( _n( '%d hour', '%d hours', 12, 'tp-twitch-widget'  ) ), 12 ),
				24 => sprintf( esc_html( _n( '%d hour', '%d hours', 24, 'tp-twitch-widget'  ) ), 24 )
            );

			$cache_duration = ( isset ( $this->options['cache_duration'] ) && is_numeric( $this->options['cache_duration'] ) ) ? intval( $this->options['cache_duration'] ) : tp_twitch_get_option_default_value( 'cache_duration' );

			?>
            <select id="tp_twitch_cache_duration" name="tp_twitch[cache_duration]">
				<?php foreach ( $cache_duration_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $cache_duration, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
            <p class="description">
                <?php _e('In case you\'re using a caching plugin, it makes no sense to select a lower value, than the caching interval of your caching plugin.', 'tp-twitch-widget' ); ?>
            </p>
            <input type="hidden" id="tp_twitch_delete_cache" name="tp_twitch[delete_cache]" value="0" />
			<?php
		}

		/**
		 * Section defaults description
		 */
		function section_defaults_render() {

		    ?>
            <p><?php _e('Here you set up the default settings which will be for your widgets. This settings can be overwritten on the widget edit screen.', 'tp-twitch-widget' ); ?></p>
            <?php
        }

		/**
		 * Default game
		 */
		function game_render() {

			$game_options = tp_twitch_get_game_options();

			$game = ( ! empty ( $this->options['game'] ) ) ? $this->options['game'] : '';
			?>
            <select id="tp_twitch_game" name="tp_twitch[game]">
				<?php foreach ( $game_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $game, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
			<?php
		}

		/**
		 * Default language
		 */
		function language_render() {

			$language_options = tp_twitch_get_language_options();

			$language = ( ! empty ( $this->options['language'] ) ) ? $this->options['language'] : 'en';
			?>
            <select id="tp_twitch_language" name="tp_twitch[language]">
				<?php foreach ( $language_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $language, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
			<?php
		}

		/**
		 * Output options page
		 */
		function options_page() {
			?>

            <div class="wrap">
                <h1><?php _e( 'Twitch for WordPress', 'tp-twitch-widget' ); ?></h1>

                <form action="options.php" method="post">
					<?php
					settings_fields( 'tp_twitch' );
					do_settings_sections( 'tp_twitch' );
					?>

                    <p>
                        <?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?>
                        <?php submit_button( __( 'Delete Cache', 'tp-twitch-widget' ), 'button-secondary', 'tp_twitch_delete_cache_submit', false ); ?>
                    </p>
                </form>

                <?php tp_twitch_debug( $this->options ); ?>
            </div>
			<?php
		}
	}
}

new TP_Twitch_Settings();