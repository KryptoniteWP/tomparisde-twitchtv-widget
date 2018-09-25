<?php
/**
 * Stream Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TP_Twitch_Stream' ) ) {

	class TP_Twitch_Stream {
	    
		public $stream;
		public $options;
		public $args;

		public function __construct( $stream ) {

			// Variables
			$this->options = tp_twitch_get_options();
			$this->stream  = $stream;

			//tp_twitch_debug_log( $stream );
		}

		/**
		 * Get id
		 *
		 * @return int
		 */
		public function get_id() {
			return ( isset ( $this->stream['id'] ) ) ? $this->stream['id'] : 0;
		}

		/**
         * The stream container classes
         *
		 * @param string $classes
		 */
		public function the_classes( $classes = 'tp-twitch-stream' ) {

		    if ( $this->is_live() ) {
                $classes .= ' tp-twitch-stream--live';
            } else {
                $classes .= ' tp-twitch-stream--offline';
            }

		    echo $classes;
        }

        /**
         * Check whether the stream is live or not
         *
         * @return int
         */
        public function is_live() {
            return ( isset ( $this->stream['type'] ) && 'live' === $this->stream['type'] ) ? true : false;
        }

		/**
		 * Get url
		 *
		 * @return string
		 */
		public function get_url() {

			$url = 'https://www.twitch.tv/';

			$user_name = $this->get_user_name();

			if ( ! empty( $user_name ) ) {
				$url .= $user_name . '/';
			}

			return $url;
		}

		/**
		 * Get title
		 *
		 * @return mixed|string
		 */
		public function get_title() {

			$title = ( isset ( $this->stream['title'] ) ) ? $this->stream['title'] : '';

			return $title;
		}

		/**
         * Get thumbnail url
         *
		 * @param int $width
		 * @param int $height
		 *
		 * @return mixed|null
		 */
		public function get_thumbnail_url( $width = 0, $height = 0 ) {

			if ( ! isset ( $this->stream['thumbnail_url'] ) )
				return null;

			if ( empty( $thumbnail_url ) && ! empty( $this->stream['user']['offline_image_url'] ) )
                return $this->stream['user']['offline_image_url'];

			$width = ( ! empty( $width ) && is_numeric( $width ) ) ? intval( $width ) : 480;
			$height = ( ! empty( $height ) && is_numeric( $height ) ) ? intval( $height ) : 270;

			$thumbnail_url = $this->replace_image_sizes( $this->stream['thumbnail_url'], $width, $height );

			return $thumbnail_url;
		}

		/**
		 * Get thumbnail alt
		 *
		 * @return string
		 */
		public function get_thumbnail_alt() {
			return sprintf( esc_html__( 'Twitch stream of %s', 'tomparisde-twitchtv-widget' ), $this->get_user_display_name() );
		}

		/**
		 * Get game
		 *
		 * @return mixed|string
		 */
		public function get_game() {

		    if ( ! isset ( $this->stream['game_id'] ) )
		        return null;

		    $game = tp_twitch_get_game_by_id( $this->stream['game_id'] );

			return ( isset( $game['name'] ) ) ? $game['name'] : null;
		}

		/**
		 * Get url
		 *
		 * @return string
		 */
		public function get_game_url() {

			$url = 'https://www.twitch.tv/';

			if ( ! isset ( $this->stream['game_id'] ) )
				return $url;

			$game = tp_twitch_get_game_by_id( $this->stream['game_id'] );

			if ( isset( $game['name'] ) )
				$url .= 'directory/game/' . $game['name'];

			return $url;
		}

		/**
         * Get live viewer count
         *
		 * @param bool $format_number
		 *
		 * @return int
		 */
		public function get_viewer( $format_number = false ) {

		    if ( ! isset ( $this->stream['viewer_count'] ) )
		        return 0;

		    $viewer = $this->stream['viewer_count'];

			if ( $format_number )
				$viewer = $this->format_number( $viewer );

			return $viewer;
		}

		/**
         * Get total amount of views
         *
		 * @param bool $format_number
		 *
		 * @return int
		 */
		public function get_views( $format_number = false ) {

			if ( ! isset ( $this->stream['user']['view_count'] ) )
				return 0;

			$views = $this->stream['user']['view_count'];

            if ( $format_number )
                $views = $this->format_number( $views );

			return $views;
		}

        /**
         * Check whether the streamer is live or not
         *
         * @return int
         */
        public function is_user_partner() {
            return ( isset ( $this->stream['user']['broadcaster_type'] ) && 'partner' === $this->stream['user']['broadcaster_type'] ) ? true : false;
        }

		/**
		 * Get url
		 *
		 * @return string
		 */
		public function get_user_url() {

			$url = 'https://www.twitch.tv/';

			$user_name = $this->get_user_name();

			if ( ! empty( $user_name ) )
				$url .= $user_name . '/videos/all';

			return $url;
		}

		/**
		 * Get user avatar url
		 *
		 * @param int $width
		 * @param int $height
		 *
		 * @return mixed|null
		 */
		public function get_user_avatar_url( $width = 0, $height = 0 ) {

			if ( ! isset ( $this->stream['user']['profile_image_url'] ) )
				return null;

			// Allowed width/height values: 50, 150, 300
			$width = ( ! empty( $width ) && is_numeric( $width ) ) ? intval( $width ) : 300;
			$height = ( ! empty( $height ) && is_numeric( $height ) ) ? intval( $height ) : 300;

			$avatar_url = $this->replace_image_sizes( $this->stream['user']['profile_image_url'], $width, $height );

			return $avatar_url;
		}

		/**
		 * Get user name
		 *
		 * @return mixed|string
		 */
		public function get_user_name() {

			$user_name = ( isset ( $this->stream['user']['login'] ) ) ? $this->stream['user']['login'] : '';

			return $user_name;
		}

		/**
		 * Get user display name
		 *
		 * @return mixed|string
		 */
		public function get_user_display_name() {

			$user_name = ( isset ( $this->stream['user']['display_name'] ) ) ? $this->stream['user']['display_name'] : '';

			return $user_name;
		}

        /**
         * Maybe output user verified icon
         */
		public function the_user_verified_icon() {

		    if ( ! $this->is_user_partner() )
		        return;

		    echo '<span class="tp-twitch-icon-verified"></span>';
        }

		/**
         * Format number
         *
		 * @param $number
		 *
		 * @return string
		 */
		private function format_number( $number ) {

			$site_lang = tp_twitch_get_site_lang();

			if ( 'de' === $site_lang ) {
				$number = number_format( $number, 0, ',', '.');
			} elseif ( 'fr' === $site_lang ) {
				$number = number_format( $number, 0, ',', ' ');
			} else {
				$number = number_format( $number, 0, '.', ',' );
			}

			return $number;
        }

		/**
         * Replace image sizes
         *
		 * @param $image_url
		 * @param $width
		 * @param $height
		 *
		 * @return mixed
		 */
		private function replace_image_sizes( $image_url, $width, $height ) {

			// Replace fix values
			$image_url = str_replace( array( '1920x1080', '300x300' ), $width . 'x' . $height, $image_url );

		    // Replace placeholders
			$image_url = str_replace( array( '{width}', '{height}' ), array( $width, $height ), $image_url );

		    return $image_url;
        }
	}
}

/*
Array
(
	[29375821632] => Array
	(
		[id] => 29375821632
            [game_id] => 138585
            [community_ids] => Array
(
)

[type] => live
[title] => TSM Kripp Arena Night / UK Amazon Deal https://amzn.to/2tTM2FG #ad (◕‿◕✿)(◕‿◕✿)
            [viewer_count] => 19568
            [started_at] => 2018-07-07T03:00:30Z
[language] => en
[thumbnail_url] => https://static-cdn.jtvnw.net/previews-ttv/live_user_nl_kripp-{width}x{height}.jpg
            [user] => Array
(
	[id] => 29795919
                    [login] => nl_kripp
[display_name] => nl_Kripp
[type] =>
                    [broadcaster_type] => partner
[description] => Gamer YouTuber Streamer Joker
[profile_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/nl_kripp-profile_image-294722d79072f28f-300x300.png
                    [offline_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/0d79622aea9f80dc-channel_offline_image-1920x1080.png
                    [view_count] => 196448018
                )

        )

    [29376805264] => Array
(
	[id] => 29376805264
            [game_id] => 138585
            [community_ids] => Array
(
	[0] => 136d5c6a-a7ed-4b6d-aedb-1deb08793157
[1] => e460229e-2100-4c42-894c-d758651f06c6
[2] => efc81f8f-234e-4bc0-b6b6-11bf1446a384
                )

            [type] => live
[title] => zalae
[viewer_count] => 2593
            [started_at] => 2018-07-07T04:45:48Z
[language] => en
[thumbnail_url] => https://static-cdn.jtvnw.net/previews-ttv/live_user_zalaehs-{width}x{height}.jpg
            [user] => Array
(
	[id] => 78583120
                    [login] => zalaehs
[display_name] => ZalaeHS
[type] =>
                    [broadcaster_type] => partner
[description] => follow my twitter @zalaehs for stream announcements
[profile_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/220e0fb13d94ba4e-profile_image-300x300.jpeg
                    [offline_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/b396236678a73ef4-channel_offline_image-1920x1080.jpeg
                    [view_count] => 4935344
                )

        )

    [29377725744] => Array
(
	[id] => 29377725744
            [game_id] => 138585
            [community_ids] => Array
(
)

[type] => live
[title] => GivePLZ
[viewer_count] => 505
            [started_at] => 2018-07-07T07:00:15Z
[language] => en
[thumbnail_url] => https://static-cdn.jtvnw.net/previews-ttv/live_user_fryingkirby-{width}x{height}.jpg
            [user] => Array
(
	[id] => 66590885
                    [login] => fryingkirby
[display_name] => FryingKirby
[type] =>
                    [broadcaster_type] => affiliate
[description] => omgKirby
[profile_image_url] => https://static-cdn.jtvnw.net/jtv_user_pictures/2673e9fe-ba2c-408c-806f-df603926e2af-profile_image-300x300.png
                    [offline_image_url] =>
                    [view_count] => 13931
                )

        )

)
*/
?>