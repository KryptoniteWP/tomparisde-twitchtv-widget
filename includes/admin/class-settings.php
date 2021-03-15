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
		 * TP_Twitch_Settings constructor.
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
                'tp_twitch_quickstart',
                __('Quickstart Guide', 'tomparisde-twitchtv-widget'),
                array( &$this, 'section_quickstart_render' ),
                'tp_twitch'
            );

            do_action( 'tp_twitch_register_settings_start' );

			add_settings_section(
				'tp_twitch_api',
				__( 'API Settings', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'section_api_render' ),
				'tp_twitch'
			);

			add_settings_field(
				'tp_twitch_api_status',
				__( 'Status', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'api_status_render' ),
				'tp_twitch',
				'tp_twitch_api',
				array('label_for' => 'tp_twitch_api_status')
			);

			add_settings_field(
				'tp_twitch_api_client_id',
				__( 'Client ID', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'api_client_id_render' ),
				'tp_twitch',
				'tp_twitch_api',
				array('label_for' => 'tp_twitch_api_client_id')
			);

			add_settings_field(
				'tp_twitch_api_client_secret',
				__( 'Client Secret', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'api_client_secret_render' ),
				'tp_twitch',
				'tp_twitch_api',
				array('label_for' => 'tp_twitch_api_client_secret')
			);

			do_action( 'tp_twitch_register_api_settings' );

			add_settings_section(
				'tp_twitch_general',
				__( 'General Settings', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'section_general_render' ),
				'tp_twitch'
			);

			add_settings_field(
				'tp_twitch_cache_duration',
				__( 'Cache Duration', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'cache_duration_render' ),
				'tp_twitch',
                'tp_twitch_general',
				array('label_for' => 'tp_twitch_cache_duration')
			);

			add_settings_field(
				'tp_twitch_no_streams_found',
				__( 'No Streams Found', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'no_streams_found_render' ),
				'tp_twitch',
				'tp_twitch_general',
				array('label_for' => 'tp_twitch_no_streams_found')
			);

			add_settings_field(
				'tp_twitch_no_streams_found_text',
				__( 'No Streams Found Message', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'no_streams_found_text_render' ),
				'tp_twitch',
				'tp_twitch_general',
				array( 'label_for' => 'tp_twitch_no_streams_found_text' )
			);

			do_action( 'tp_twitch_register_general_settings' );

			add_settings_section(
				'tp_twitch_defaults',
				__( 'Default Settings', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'section_defaults_render' ),
				'tp_twitch'
			);

            add_settings_field(
                'tp_twitch_language',
                __( 'Language', 'tomparisde-twitchtv-widget' ),
                array( &$this, 'language_render' ),
                'tp_twitch',
                'tp_twitch_defaults',
                array('label_for' => 'tp_twitch_language')
            );

            add_settings_field(
                'tp_twitch_widget_style',
                __( 'Style (Widget)', 'tomparisde-twitchtv-widget' ),
                array( &$this, 'widget_style_render' ),
                'tp_twitch',
                'tp_twitch_defaults',
                array('label_for' => 'tp_twitch_widget_style')
            );

			add_settings_field(
				'tp_twitch_widget_size',
				__( 'Size (Widget)', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'widget_size_render' ),
				'tp_twitch',
				'tp_twitch_defaults',
				array('label_for' => 'tp_twitch_widget_size')
			);

			add_settings_field(
				'tp_twitch_widget_preview',
				__( 'Preview (Widget)', 'tomparisde-twitchtv-widget' ),
				array( &$this, 'widget_preview_render' ),
				'tp_twitch',
				'tp_twitch_defaults',
				array('label_for' => 'tp_twitch_widget_preview')
			);

			do_action( 'tp_twitch_register_defaults_settings' );

            do_action( 'tp_twitch_register_settings_end' );

            add_settings_section(
                'tp_twitch_data',
                __( 'API Related Data', 'tomparisde-twitchtv-widget' ),
                array( &$this, 'section_data_render' ),
                'tp_twitch'
            );

            add_settings_section(
                'tp_twitch_help',
                __( 'Help & Support', 'tomparisde-twitchtv-widget' ),
                array( &$this, 'section_help_render' ),
                'tp_twitch'
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

		    // Defaults
		    $delete_cache = false;
			$delete_streams_cache = false;

		    //tp_twitch_debug_log( $input );

            // API
			$api_status = ( isset ( $this->options['api_status'] ) ) ? $this->options['api_status'] : false;
			$api_error = ( isset ( $this->options['api_error'] ) ) ? $this->options['api_error'] : '';

			if ( ! empty ( $input['api_client_id'] ) && ! empty ( $input['api_client_secret'] ) ) {

				$api_client_id = ( isset ( $this->options['api_client_id'] ) ) ? $this->options['api_client_id'] : '';
				$api_client_id_new = esc_html( $input['api_client_id'] );

				$api_client_secret = ( isset ( $this->options['api_client_secret'] ) ) ? $this->options['api_client_secret'] : '';
				$api_client_secret_new = esc_html( $input['api_client_secret'] );

				if ( $api_client_id_new != $api_client_id || $api_client_secret_new != $api_client_secret ) {

					$delete_cache = true;

					$result = tp_twitch()->api->verify_client_credentials( $api_client_id_new, $api_client_secret_new, true );

					$api_status = ( is_array( $result ) && isset( $result['data'] ) ) ? true : false;
					$api_error = ( ! empty ( $result['error'] ) ) ? $result['error'] : '';

					if ( ! empty ( $api_error ) )
					    tp_twitch_addlog( 'Twitch API >> Verify client id or client secret >> Error: "' . $api_error . '"' );

					// Sanitized Client ID and Client Secret input values
					$input['api_client_id'] = $api_client_id_new;
					$input['api_client_secret'] = $api_client_secret_new;
				}

			} else {
				// Client ID empty or Client Secret empty leads always to a false status
				$api_status = false;
            }

			$input['api_status'] = $api_status;
			$input['api_error'] = $api_error;

			// Cache duration changed
            if ( isset( $input['cache_duration'] ) && isset( $this->options['cache_duration'] ) && $input['cache_duration'] != $this->options['cache_duration'] ) {
	            $delete_streams_cache = true;
            }

            // Handle Delete Cache Action
			if ( isset ( $input['delete_cache'] ) && '1' === $input['delete_cache'] ) {
				$delete_cache = true;
				$input['delete_cache'] = '0';
			}

			// Maybe delete cache(s)
			if ( $delete_cache ) {
				tp_twitch_delete_cache();
            } elseif ( $delete_streams_cache ) {
			    tp_twitch_delete_streams_cache();
            }

            // Handle Delete Log Action
            if ( isset ( $input['delete_log'] ) && '1' === $input['delete_log'] ) {
                delete_option( 'tp_twitch_log' );
                $input['delete_log'] = '0';
            }

            // Hook
            $input = apply_filters( 'tp_twitch_settings_validate_input', $input );

			return $input;
		}

        /**
         * Section quickstart guide
         */
        function section_quickstart_render() {
            ?>
            <p>
                <strong><?php _e( 'Step 1: Create API Credentials', 'tomparisde-twitchtv-widget' ); ?></strong><br />
                <?php printf( wp_kses( __( 'Follow our guide which shows you <a href="%s" target="_blank" rel="nofollow">how to create Twitch API credentials</a>.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( add_query_arg( array(
	                'utm_source'   => 'settings-page',
	                'utm_medium'   => 'quickstart',
	                'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) ); ?>
            </p>

            <p>
                <strong><?php _e( 'Step 2: Enter your Client ID', 'tomparisde-twitchtv-widget' ); ?></strong><br />
                <?php _e('Once you created your API credentials, enter your personal <em>Client ID</em> into the field below.', 'tomparisde-twitchtv-widget'); ?>
            </p>

            <p>
                <strong><?php _e( 'Step 3: Enter your Client Secret', 'tomparisde-twitchtv-widget' ); ?></strong><br />
                <?php _e('Once you created your API credentials, enter your personal <em>Client Secret</em> into the field below.', 'tomparisde-twitchtv-widget'); ?>
            </p>

            <p>
                <strong><?php _e( 'Step 4: Place Twitch Streams on your Site', 'tomparisde-twitchtv-widget' ); ?></strong><br />
                <?php printf( wp_kses( __( 'Go to the <a href="%s" target="_blank" rel="nofollow">Widgets page</a>, place our Twitch widget wherever you want and adjust it according to your needs.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( admin_url( 'widgets.php' ) ) ); ?>
            </p>

            <?php do_action( 'tp_twitch_settings_section_quickstart_render' ); ?>

            <p><?php printf( wp_kses( __( 'Please take a look into the <a href="%s">documentation</a> for more options.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( add_query_arg( array(
                    'utm_source'   => 'settings-page',
                    'utm_medium'   => 'quickstart',
                    'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) ); ?></p>
            <?php
        }

		/**
		 * Section API description
		 */
		function section_api_render() {

			?>
            <p><?php printf( wp_kses( __( 'In order to show Twitch streams, this plugin requires access to the official <a href="%s" target="_blank" rel="nofollow">Twitch API</a>.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( 'https://dev.twitch.tv/' ) ); ?>&nbsp;</p>
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
                <span style="font-weight: bold; color: green;"><?php _e( 'Connected', 'tomparisde-twitchtv-widget' ); ?></span>
            <?php } else { ?>
                <span style="font-weight: bold; color: red;"><?php _e( 'Disconnected', 'tomparisde-twitchtv-widget' ); ?></span>
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
                <?php printf( wp_kses( __( 'We created a detailed guide which shows you <a href="%s" target="_blank" rel="nofollow">how to get your client id</a>.', 'tomparisde-twitchtv-widget' ), array( 'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( add_query_arg( array(
	                'utm_source'   => 'settings-page',
	                'utm_medium'   => 'api-client-id',
	                'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) ); ?>
            </p>
			<?php
		}

		/**
		 * API Client Secret
		 */
		function api_client_secret_render() {

			$api_client_secret = ( isset ( $this->options['api_client_secret'] ) ) ? $this->options['api_client_secret'] : '';
			?>
            <input id="tp_twitch_api_client_secret" class="regular-text" name="tp_twitch[api_client_secret]" type="password" value="<?php echo esc_html( $api_client_secret ); ?>" />
            <p class="description">

                <?php printf( wp_kses( __( 'We created a detailed guide which shows you <a href="%s" target="_blank" rel="nofollow">how to get your client secret</a>.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array(), 'target' => '_blank', 'rel' => 'nofollow' ) ) ), esc_url( add_query_arg( array(
	                'utm_source'   => 'settings-page',
	                'utm_medium'   => 'api-client-secret',
	                'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) );  ?>
            </p>
			<?php
		}

		/**
		 * Section general description
		 */
		function section_general_render() {

			?>
            <p><?php _e('Here you set up general settings which will be used plugin wide.', 'tomparisde-twitchtv-widget' ); ?></p>
			<?php
		}

		/**
		 * Cache Duration
		 */
		function cache_duration_render() {

			$cache_duration_options = array(
				15 => sprintf( esc_html( _n( '%d minute', '%d minutes', 15, 'tomparisde-twitchtv-widget'  ) ), 15 ),
				30 => sprintf( esc_html( _n( '%d minute', '%d minutes', 30, 'tomparisde-twitchtv-widget'  ) ), 30 ),
                60 => sprintf( esc_html( _n( '%d hour', '%d hours', 1, 'tomparisde-twitchtv-widget'  ) ), 1 ),
                120 => sprintf( esc_html( _n( '%d hour', '%d hours', 2, 'tomparisde-twitchtv-widget'  ) ), 2 ),
                180 => sprintf( esc_html( _n( '%d hour', '%d hours', 3, 'tomparisde-twitchtv-widget'  ) ), 3 ),
                240 => sprintf( esc_html( _n( '%d hour', '%d hours', 4, 'tomparisde-twitchtv-widget'  ) ), 4 ),
				360 => sprintf( esc_html( _n( '%d hour', '%d hours', 6, 'tomparisde-twitchtv-widget'  ) ), 6 ),
				720 => sprintf( esc_html( _n( '%d hour', '%d hours', 12, 'tomparisde-twitchtv-widget'  ) ), 12 ),
				1440 => sprintf( esc_html( _n( '%d hour', '%d hours', 24, 'tomparisde-twitchtv-widget'  ) ), 24 )
            );

			$cache_duration = ( isset ( $this->options['cache_duration'] ) && is_numeric( $this->options['cache_duration'] ) ) ? intval( $this->options['cache_duration'] ) : tp_twitch_get_option_default_value( 'cache_duration' );

			?>
            <select id="tp_twitch_cache_duration" name="tp_twitch[cache_duration]">
				<?php foreach ( $cache_duration_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $cache_duration, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
            <p class="description">
                <?php _e('In case you\'re using a caching plugin, it makes no sense to select a lower value, than the caching interval of your caching plugin.', 'tomparisde-twitchtv-widget' ); ?>
            </p>
            <input type="hidden" id="tp_twitch_delete_cache" name="tp_twitch[delete_cache]" value="0" />
			<?php
		}

		/**
		 * No streams found
		 */
		function no_streams_found_render() {

			$no_streams_found_options = array(
				'' => __( 'Hide Message', 'tomparisde-twitchtv-widget' ),
				'show' => __( 'Show Message', 'tomparisde-twitchtv-widget' ),
				'admin' => __( 'Show Message for Admins only', 'tomparisde-twitchtv-widget' )
			);

			$no_streams_found = ( isset ( $this->options['no_streams_found'] ) ) ? $this->options['no_streams_found'] : '';

			?>
            <select id="tp_twitch_no_streams_found" name="tp_twitch[no_streams_found]">
				<?php foreach ( $no_streams_found_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $no_streams_found, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
            <p class="description">
				<?php _e('Specify what happens when no streams were found.', 'tomparisde-twitchtv-widget' ); ?>
            </p>
			<?php
		}

		/**
		 * No streams found text
		 */
		function no_streams_found_text_render() {

            $no_streams_found_msg = ( isset ( $this->options['no_streams_found_text'] ) ) ? $this->options['no_streams_found_text'] : __( 'No streams found', 'tomparisde-twitchtv-widget' );

			?>
            <input id="tp_twitch_no_streams_found_text" class="regular-text" name="tp_twitch[no_streams_found_text]" type="text" value="<?php echo esc_html( $no_streams_found_msg ); ?>" />
            <p class="description">
                <?php _e( 'Customize the message displayed when they are no available streams.', 'tomparisde-twitchtv-widget' ); ?>
            </p>
			<?php
		}

		/**
		 * Section defaults description
		 */
		function section_defaults_render() {

		    ?>
            <p><?php _e('Here you set up the default settings which will be used for displaying streams and may be overwritten individually.', 'tomparisde-twitchtv-widget' ); ?></p>
            <?php
        }

        /**
         * Default language
         */
        function language_render() {

            $language_options = tp_twitch_get_language_options();

            $language = ( ! empty ( $this->options['language'] ) ) ? $this->options['language'] : tp_twitch_get_option_default_value( 'language' );
            ?>
            <select id="tp_twitch_language" name="tp_twitch[language]">
                <?php foreach ( $language_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $language, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>
            <?php
        }

        /**
         * Default widget style
         */
        function widget_style_render() {

            $widget_style_options = tp_twitch_get_widget_style_options();

            $widget_style = ( ! empty ( $this->options['widget_style'] ) ) ? $this->options['widget_style'] : tp_twitch_get_option_default_value( 'widget_style' );
            ?>
            <select id="tp_twitch_widget_style" name="tp_twitch[widget_style]">
                <?php foreach ( $widget_style_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $widget_style, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>
            <?php
        }


        /**
		 * Default widget size
		 */
		function widget_size_render() {

			$widget_size_options = tp_twitch_get_widget_size_options();

			$widget_size = ( ! empty ( $this->options['widget_size'] ) ) ? $this->options['widget_size'] : tp_twitch_get_option_default_value( 'widget_size' );
			?>
            <select id="tp_twitch_widget_size" name="tp_twitch[widget_size]">
				<?php foreach ( $widget_size_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $widget_size, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
			<?php
		}

		/**
		 * Default widget preview
		 */
		function widget_preview_render() {

			$widget_preview_options = tp_twitch_get_widget_preview_options();

			$widget_preview = ( ! empty ( $this->options['widget_preview'] ) ) ? $this->options['widget_preview'] : tp_twitch_get_option_default_value( 'widget_preview' );
			?>
            <select id="tp_twitch_widget_preview" name="tp_twitch[widget_preview]">
				<?php foreach ( $widget_preview_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $widget_preview, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
			<?php
		}

        /**
         * Section data render
         */
        function section_data_render() {

            if ( ! isset( $this->options['api_status'] ) || ! $this->options['api_status'] )
                return;

            $games = tp_twitch_get_games();
            $games_count = ( is_array( $games ) ) ? sizeof( $games ) : 0;
            $languages = tp_twitch_get_languages();
            $languages_count = ( is_array( $languages ) ) ? sizeof( $languages ) : 0;

            //tp_twitch_debug( $games );
            ?>
            <p><?php _e('Here you can find an overview of all available API related data.', 'tomparisde-twitchtv-widget' ); ?></p>

            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Data', 'tomparisde-twitchtv-widget' ); ?></th>
                        <th><?php _e('Description', 'tomparisde-twitchtv-widget' ); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Games', 'tomparisde-twitchtv-widget' ); ?></td>
                        <td><?php printf( __( 'Currently there are <strong id="tp-twitch-games-count">%d games</strong> available in the database.', 'tomparisde-twitchtv-widget' ), $games_count ); ?></td>
                        <td><span id="tp-twitch-data-games-toggle" class="button button-secondary" style="width: 160px; text-align: center;"><?php _e('Toggle Games List', 'tomparisde-twitchtv-widget' ); ?></span></td>
                    </tr>
                    <tr>
                        <td><?php _e('Languages', 'tomparisde-twitchtv-widget' ); ?></td>
                        <td><?php printf( __( 'Currently there are <strong>%d languages</strong> available in the database.', 'tomparisde-twitchtv-widget' ), $languages_count ); ?></td>
                        <td><span id="tp-twitch-data-languages-toggle" class="button button-secondary" style="width: 160px; text-align: center;"><?php _e('Toggle Languages List', 'tomparisde-twitchtv-widget' ); ?></span></td>
                    </tr>
                </tbody>
            </table>

            <div id="tp-twitch-data-games-container" style="display: none;">
                <?php
                // Sort games.
                if ( $games && is_array( $games ) ) {
                    $games = tp_twitch_array_sort( $games, 'name' );
                }

                if ( $games && is_array( $games ) && sizeof( $games ) > 0 ) { ?>
                    <h4><?php _e('Games','tomparisde-twitchtv-widget' ); ?></h4>

                    <?php do_action( 'tp_twitch_add_data_settings' ); ?>

                    <table id="tp-twitch-data-games-list" class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'tomparisde-twitchtv-widget' ); ?></th>
                                <th><?php _e('Game', 'tomparisde-twitchtv-widget' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $games as $game ) { ?>
                                <tr>
                                    <td>
                                        <?php echo esc_html( $game['id'] ); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html( $game['name'] ); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>

            <div id="tp-twitch-data-languages-container" style="display: none;">
                <?php
                // Sort languages.
                asort($languages );
                ?>
                <h4><?php _e('Languages','tomparisde-twitchtv-widget' ); ?></h4>
                <table class="widefat">
                    <thead>
                    <tr>
                        <th><?php _e('Code', 'tomparisde-twitchtv-widget' ); ?></th>
                        <th><?php _e('Language', 'tomparisde-twitchtv-widget' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $languages as $lang_code => $lang_name ) { ?>
                        <tr>
                            <td>
                                <?php echo esc_html( $lang_code ); ?>
                            </td>
                            <td>
                                <?php echo esc_html( $lang_name ); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        /**
         * Section help render
         */
        function section_help_render() {

            global $wp_version;

            $curl = $this->check_curl();

            $enabled = '<span style="color: green;"><strong><span class="dashicons dashicons-yes"></span> ' . __('Enabled', 'tomparisde-twitchtv-widget') . '</strong></span>';
            $disabled = '<span style="color: red;"><strong><span class="dashicons dashicons-no"></span> ' . __('Disabled', 'tomparisde-twitchtv-widget') . '</strong></span>';

            ?>
            <p>
                <?php printf( wp_kses( __( 'In case you experience some issue with our plugin, please <a href="%s" target="_blank">get in touch with us</a> and provide a screenshot of the table below.', 'tomparisde-twitchtv-widget' ), array( 'a' => array( 'href' => array(), 'target' => array( '_blank' ) ) ) ), esc_url( 'https://kryptonitewp.com/support/' ) ); ?>
            </p>

            <table class="widefat tp-twitch-settings-table">
                <thead>
                <tr>
                    <th width="300"><?php _e('Setting', 'tomparisde-twitchtv-widget'); ?></th>
                    <th><?php _e('Values', 'tomparisde-twitchtv-widget'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>Twitch API</th>
                    <td>
                        <?php if ( isset( $this->options['api_status'] ) && true === $this->options['api_status'] ) { ?>
                            <span style="font-weight: bold; color: green;"><?php _e( 'Connected', 'tomparisde-twitchtv-widget' ); ?></span>
                        <?php } else { ?>
                            <span style="font-weight: bold; color: red;"><?php _e( 'Disconnected', 'tomparisde-twitchtv-widget' ); ?></span>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>WordPress</th>
                    <td>Version <?php echo $wp_version; ?></td>
                </tr>
                <tr>
                    <th>PHP</th>
                    <td>Version <strong><?php echo phpversion(); ?></strong></td>
                </tr>
                <tr>
                    <th><?php printf( esc_html__( 'PHP "%1$s" extension', 'tomparisde-twitchtv-widget' ), 'cURL' ); ?></th>
                    <td>
                        <?php echo (isset ($curl['enabled']) && $curl['enabled']) ? $enabled : $disabled; ?>
                        <?php if (isset ($curl['version'])) echo ' (Version ' . $curl['version'] . ')'; ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <p>
                <?php _e('In case one of the values above is <span style="color: red;"><strong>red</strong></span>, please get in contact with your webhoster in order to enable the missing PHP extensions.', 'tomparisde-twitchtv-widget'); ?>
            </p>

            <p>
                <strong><?php _e('Log file', 'tomparisde-twitchtv-widget'); ?></strong><br />
                <textarea rows="5" style="width: 100%;"><?php echo get_option( 'tp_twitch_log', __( 'No entries yet. ', 'tomparisde-twitchtv-widget' ) ); ?></textarea>
            </p>
            <p>
                <input type="hidden" id="tp-twitch-delete-log" name="tp_twitch[delete_log]" value="0" />
                <?php submit_button( 'Delete log', 'delete button-secondary', 'tp-twitch-delete-log-submit', false ); ?>
            </p>
            <?php
        }

		/**
		 * Output options page
		 */
		function options_page() {
			?>

            <div class="tp-twitch-settings">
                <div class="wrap">
                    <h2><?php echo apply_filters( 'tp_twitch_settings_page_title', __( 'Twitch for WordPress', 'tomparisde-twitchtv-widget' ) ); ?></h2>

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form id="tp_twitch_settings_form" action="options.php" method="post">

                                        <?php
                                        settings_fields('tp_twitch');
                                        tp_twitch_do_settings_sections('tp_twitch');
                                        ?>

                                        <p>
                                            <?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?>
                                            <?php submit_button( __( 'Delete Cache', 'tomparisde-twitchtv-widget' ), 'button-secondary', 'tp_twitch_delete_cache_submit', false ); ?>
                                        </p>

                                    </form>
                                </div>

                            </div>
                            <!-- /#post-body-content -->
                            <div id="postbox-container-1" class="postbox-container">

                                <div class="postbox">
                                    <h3><span><span class="dashicons dashicons-star-filled"></span>&nbsp;<?php esc_html_e( 'Do You Enjoy our Plugin?', 'tomparisde-twitchtv-widget' ); ?></span></h3>
                                    <div class="inside">
                                        <p><?php _e( 'It would be great if you <strong>do us a big favor and give us a review</strong> for our plugin.', 'tomparisde-twitchtv-widget' ); ?></p>
                                        <p><?php esc_html_e( 'This will help us to make others aware of our plugin and we can continue to provide it with great features in long term.', 'tomparisde-twitchtv-widget' ); ?></p>
                                        <p>
                                            <a class="tp-twitch-settings-button tp-twitch-settings-button--block" target="_blank" href="<?php echo esc_url( 'https://wordpress.org/support/plugin/tomparisde-twitchtv-widget/reviews/?filter=5#new-post' ); ?>" rel="nofollow"><?php _e( 'Submit a review', 'tomparisde-twitchtv-widget' ); ?></a>
                                        </p>
                                    </div>
                                </div>

                                <div class="meta-box-sortables">
                                    <div class="postbox">
                                        <h3><span><?php _e('Resources &amp; Support', 'tomparisde-twitchtv-widget' ); ?></span></h3>
                                        <div class="inside">
                                            <p><?php _e('In order to make it as simple as possible for you, we created a detailed online documentation.', 'tomparisde-twitchtv-widget' ); ?></p>
                                            <ul>
                                                <li>
                                                    <?php
                                                    $docs_link = esc_url( add_query_arg( array(
                                                            'utm_source'   => 'settings-page',
                                                            'utm_medium'   => 'infobox-resources',
                                                            'utm_campaign' => 'TP Twitch',
                                                        ), TP_TWITCH_DOCS_URL )
                                                    );
                                                    ?>
                                                    <a href="<?php echo $docs_link; ?>" target="_blank"><?php _e('Documentation', 'tomparisde-twitchtv-widget' ); ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo TP_TWITCH_WP_ORG_URL; ?>" target="_blank"><?php _e('Plugin Page', 'tomparisde-twitchtv-widget' ); ?></a>
                                                </li>
                                                <li>
                                                    <a href="https://wordpress.org/plugins/tomparisde-twitchtv-widget/#developers" target="_blank"><?php _e('Changelog', 'tomparisde-twitchtv-widget' ); ?></a>
                                                </li>
                                                <li>
                                                    <a href="https://twitter.com/kryptonitewp" target="_blank"><?php _e('Follow us on Twitter', 'tomparisde-twitchtv-widget' ); ?></a>
                                                </li>
                                            </ul>
                                            <?php
                                            $website_link = esc_url( add_query_arg( array(
                                                    'utm_source'   => 'settings-page',
                                                    'utm_medium'   => 'infobox-resources',
                                                    'utm_campaign' => 'Twitch WP',
                                                ), 'https://kryptonitewp.com/' )
                                            );
                                            ?>
                                            <p>&copy; Copyright <?php echo date('Y' ); ?> <a href="<?php echo $website_link; ?>" target="_blank">KryptoniteWP</a></p>
                                        </div>
                                    </div>
                                </div>

                                <?php if ( ! tp_twitch_is_pro_version() ) { ?>
                                    <div class="postbox">
                                        <h3><span><?php _e('Upgrade to PRO Version', 'tomparisde-twitchtv-widget'); ?></span></h3>
                                        <div class="inside">

                                            <p><?php _e('The PRO version extends the plugin exclusively with a variety of different styles and some exclusively features.', 'tomparisde-twitchtv-widget'); ?></p>

                                            <ul>
                                                <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Display more than 3 streams', 'tomparisde-twitchtv-widget'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Place streams via shortcode', 'tomparisde-twitchtv-widget'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Choose from different styles', 'tomparisde-twitchtv-widget'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Sort streams by different criteria', 'tomparisde-twitchtv-widget'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('And more!', 'tomparisde-twitchtv-widget'); ?></strong></li>
                                            </ul>

                                            <p>
                                                <?php _e('We would be happy if you give it a chance!', 'tomparisde-twitchtv-widget'); ?>
                                            </p>

                                            <p>
                                                <?php
                                                $upgrade_link = tp_twitch_get_pro_version_url( 'settings-page', 'infobox-upgrade' );
                                                ?>
                                                <a class="tp-twitch-settings-button tp-twitch-settings-button--block" target="_blank" href="<?php echo $upgrade_link; ?>" rel="nofollow"><?php _e('More details', 'tomparisde-twitchtv-widget'); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- /.meta-box-sortables -->
                            </div>
                            <!-- /.postbox-container -->
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}

        /**
         * Check cURL
         *
         * @return array|bool
         */
        private function check_curl() {

            if ( ( function_exists('curl_version') ) ) {

                $curl_data = curl_version();
                $version = ( isset ( $curl_data['version'] ) ) ? $curl_data['version'] : null;

                return array(
                    'enabled' => true,
                    'version' => $version
                );
            } else {
                return false;
            }
        }
	}
}

/**
 * Custom settings section output
 *
 * Replacing: do_settings_sections( 'tp_twitch' );
 *
 * @param $page
 */
function tp_twitch_do_settings_sections($page) {

    global $wp_settings_sections, $wp_settings_fields;

    if (!isset($wp_settings_sections[$page]))
        return;

    foreach ((array)$wp_settings_sections[$page] as $section) {

        $title = '';

        if ($section['title'])
            $title = "<h3 class='hndle'>{$section['title']}</h3>\n";

        if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || ( !isset($wp_settings_fields[$page][$section['id']] ) && ! in_array( $section['id'], array( 'tp_twitch_quickstart', 'tp_twitch_data', 'tp_twitch_help' ) ) ) )
            continue;

        echo '<div class="postbox">';
        echo $title;
        echo '<div class="inside">';

        if ($section['callback'])
            call_user_func($section['callback'], $section);

        echo '<table class="form-table">';
        do_settings_fields($page, $section['id']);
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }
}
