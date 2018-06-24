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
		 * Client ID
		 */
		private $client_id;

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
		}

		/**
		 * Enable debug mode
		 */
		public function enable_debug_mode() {
			$this->debug = true;
		}

		/**
		 * Verify client id
		 *
		 * @param $client_id
		 *
		 * @return bool
		 */
		public function verify_client_id( $client_id ) {

			$this->client_id = $client_id;

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

			$result = $this->request( 'streams', $args );

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
		private function request( $url = '', $args = array() ) {

			if ( empty( $this->client_id ) )
				return null;

			if ( is_array( $args ) && sizeof( $args ) > 0 ) {

				$this->debug( $args, 'TP_Twitch_API->request >> $args' );

				$query_args = array();

				foreach ( $args as $arg_key => $arg_value ) {

					if ( ! empty ( $arg_value ) ) {
						$query_args[$arg_key] = $arg_value;
					}
				}

				if ( sizeof( $query_args ) > 0 ) {
					$url .= '?' . http_build_query( $query_args );
				}
			}

			$this->debug( 'TP_Twitch_API->request >> ' . $url );

			$headers = array(
				'Client-ID' => $this->client_id
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