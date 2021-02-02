<?php
/**
 * Twitch API Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TP_Twitch_API' ) ) {

	class TP_Twitch_API {

		/**
		 * API URL
		 *
		 * @var string
		 */
		private $api_url = 'https://api.twitch.tv/helix/';

		/**
		 * OAuth token URL
		 *
		 * OAuth Client Credentials Flow:
		 * https://dev.twitch.tv/docs/authentication/
		 *
		 * @var string
		 */
		private $token_url = 'https://id.twitch.tv/oauth2/token';

		/**
		 * Client ID
		 */
		private $client_id;

		/**
		 * Client secret
		 */
		private $client_secret;

		/**
		 * Debug mode
		 */
		private $debug = false;

		/**
		 * TP_Twitch_API constructor.
		 */
		public function __construct() {

			$options = tp_twitch_get_options();

			$this->client_id = ( ! empty ( $options['api_client_id'] ) ) ? esc_html( $options['api_client_id'] ) : '';

			$this->client_secret = ( ! empty ( $options['api_client_secret'] ) ) ? esc_html( $options['api_client_secret'] ) : '';
		}

		/**
		 * Enable debug mode
		 */
		public function enable_debug_mode() {
			$this->debug = true;
		}

		/**
		 * Verify client credentials
		 *
		 * @param $client_id
		 * @param $client_secret
		 * @param bool $delete_cache
		 *
		 * @return mixed
		 */
		public function verify_client_credentials( $client_id, $client_secret, $delete_cache = false ) {

			$this->client_id = $client_id;
			$this->client_secret = $client_secret;

			if ( $delete_cache )
				delete_transient( 'tp_twitch_token' );

			$result = $this->get_top_games();

			return $result;
		}

		/**
		 * Get top games
		 *
		 * https://dev.twitch.tv/docs/api/reference/#get-top-games
		 *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public function get_top_games( $args = array() ) {

			$result = $this->request( 'games/top', $args );

			return $result;
		}

		/**
		 * Get streams
		 *
		 * https://dev.twitch.tv/docs/api/reference/#get-streams
		 *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public function get_streams( $args = array() ) {

			$defaults = array(
				'first' => 10, // Amount of streams
				'game_id' => '',
				'language' => '',
				'user_id' => '',
				'user_login' => ''
			);

			/**
			 * Parse incoming $args into an array and merge it with $defaults
			 */
			$args = wp_parse_args( $args, $defaults );

            //tp_twitch_debug( $args, 'API > get_streams > $args' );

			$result = $this->request( 'streams', $args );

			//tp_twitch_debug( $result, 'API > get_streams > $result' );

			return $result;
		}

		/**
		 * Get users
		 *
		 * https://dev.twitch.tv/docs/api/reference/#get-users
		 *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public function get_users( $args = array() ) {

			$defaults = array(
				'id' => '',
				'login' => '',
			);

			/**
			 * Parse incoming $args into an array and merge it with $defaults
			 */
			$args = wp_parse_args( $args, $defaults );

			$result = $this->request( 'users', $args );

			return $result;
		}

		/**
		 * Fetch data from Twitch API
		 *
		 * @param string $url
		 * @param array $args
		 *
		 * @return array|mixed|null|object|string
		 */
		protected function request( $url = '', $args = array() ) {

			if ( empty( $this->client_id ) || empty( $this->client_secret ) )
				return null;

			$token = $this->get_token();

			if ( false === $token )
				return null;

			if ( is_array( $args ) && sizeof( $args ) > 0 ) {

				$this->debug( $args, 'TP_Twitch_API->request >> $args' );

				$query_args = array();

				foreach ( $args as $arg_key => $arg_value ) {

					if ( ! empty ( $arg_value ) ) {

						// Comma separated values must be converted to arrays
						if ( is_string( $arg_value ) && strpos( $arg_value, ',') !== false )
							$arg_value = explode(',', $arg_value);

						// Add query args
						$query_args[$arg_key] = $arg_value;
					}
				}

				if ( sizeof( $query_args ) > 0 ) {
					// Extended "http_build_query" in order to add multiple args with the same key
					$query = http_build_query( $query_args,null, '&' );;
					$query_string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
					$url .= '?' . $query_string;
				}
			}

			$this->debug( 'TP_Twitch_API->request >> ' . $url );

			$headers = array(
				'Client-ID' => $this->client_id,
				'Authorization' => 'Bearer ' . base64_decode( $token )
             );

			$response = wp_remote_get( $this->api_url . $url, array(
				'timeout' => 15,
				'headers' => $headers
			));

			// Check for error
			if ( is_wp_error( $response ) )
				return null;

			$result = wp_remote_retrieve_body( $response );

			// Check for error
			if ( is_wp_error( $result ) ) {
				return null;
			}

			$result = json_decode( $result, true );

			$this->debug( $result, 'TP_Twitch_API->request >> $result' );

			return $result;
		}

		/**
		 * Get OAuth token
		 *
		 * @return bool|string
		 */
		private function get_token() {

			$token = get_transient( 'tp_twitch_token' );

			if ( false !== $token ) {
				return $token;
			}

			$args = [
				'client_id' => $this->client_id,
				'client_secret' => $this->client_secret,
				'grant_type' => 'client_credentials'
			];

			$headers = [
				'Content-Type' => 'application/json'
			];

			$response = wp_remote_post( $this->token_url, [
				'headers' => $headers,
				'body'    => wp_json_encode( $args ),
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$result = wp_remote_retrieve_body( $response );

			$result = json_decode( $result, true );

			/**
			 * Additionally we can validate token for debugging:
			 * curl -H "Authorization: OAuth <access_token>" https://id.twitch.tv/oauth2/validate
			 *
			 * response example:
			 * {"client_id":"1cphbefbx1lbtyfvzx3ja268226w7y","scopes":[],"expires_in":5445171}
			 */
			$this->debug( $result, 'TP_Twitch_API->get_token >> $token' );

			if ( $result === false || ! isset( $result['access_token'] ) )
				return false;

			$token = base64_encode( $result['access_token'] );

			set_transient( 'tp_twitch_token', $token, $result['expires_in'] - 30 );

			return $token;
		}

		/**
		 * Debug
		 *
		 * @param $args
		 * @param string $title
		 */
		private function debug( $args, $title = '' ) {

			if ( ! $this->debug )
				return;

			tp_twitch_debug( $args, $title );
		}

		/**
		 * Debug log
		 *
		 * @param $message
		 * @param string $title
		 */
		private function debug_log( $message, $title = '' ) {

			if ( ! $this->debug )
				return;

			if ( ! empty( $title ) )
				tp_twitch_debug_log( $title );

			tp_twitch_debug_log( $message );
		}
	}
}