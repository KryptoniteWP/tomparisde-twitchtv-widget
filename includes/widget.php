<?php

/**
 * Adds Foo_Widget widget.
 */
class TP_TTVW_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'tp_ttvw_widget', // Base ID
            __( 'TomParisDE Twitch Widget - Lite', 'tp-ttvw' ), // Name
            array( 'description' => __( 'Your TomParisDE Twitch Widget for Blogs, Clan-, Fan- and Community Sites', 'tp-ttvw' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        if ( ! empty ( $instance['channel'] ) || ! empty ( $instance['twitch_game'] ) ) {

            // @$api_key = $instance['api_key'];
            @$hide_offline_channels = $instance['hide_offline_channels'];
            @$hide_title = $instance['hide_title'];
            @$hide_stats = $instance['hide_stats'];
            @$banner_postion = $instance['banner_postion'];
            @$template = $instance['template'];
            @$style = " " . $instance['style'];
            @$font_color = $instance['font_color'];
            @$twitch_game = $instance['twitch_game'];
            @$max_games = intval( $instance['max_games'] );
            @$channel_or_game = $instance['channel_or_game'];
            @$twitch_streamer_language = $instance['twitch_streamer_language'];
            @$live_or_picture = $instance['live_or_picture'];
            @$live_or_picture_height = $instance['live_or_picture_height'];
            @$live_or_picture_width = $instance['live_or_picture_width'];
            @$remove_channels = $instance['remove_channels'];
            @$option_channellist_big = intval( $instance['option_channellist_big'] );
            @$show_aktiv_game = intval( $instance['aktiv_game'] );
            @$twitch_partner = intval( $instance['twitch_partner'] );
            @$twitch_streamer_language_two = $instance['twitch_streamer_language_two'];


            // Streamer Language
            if ( empty ($twitch_streamer_language) )
            {
                $twitch_streamer_language = $twitch_streamer_language_two;
            }


            // Format Variables
            $remove_channels = explode(",", $remove_channels );
            $remove_channels = array_map('strtolower', $remove_channels);
            $max_games_api = $max_games + sizeof ( $remove_channels );


            // Data for output
            $channels_data = array();


            // Get channels from user
            $channels = str_replace(' ', '', $instance['channel']);
            $channels = explode(",", $channels );


            // channel or game question
            if ( $channel_or_game == 'channels' )
            {
                foreach ( $channels as $channel)
                {
                    $channel_data = tp_get_channel_data ( $channel );
                    if ($channel_data) {
                        $channels_data[] = $channel_data;
                    }
                } }
            else {
                $channel_data = tp_get_channel_or_game_data( $twitch_game, $max_games_api, $twitch_streamer_language );
                if ($channel_data) {
                    foreach ($channel_data as $key => $channel) {
                        if ( in_array ($channel['username'], $remove_channels ) ) {
                            unset($channel_data[$key]);
                        }
                    }


                    $channels_data = array_slice( $channel_data, 0,$max_games);
                }
            }

            // if offline dont show
            if ( $hide_offline_channels == '1') {

                if (is_array($channels_data) && sizeof ($channels_data) > 0) {
                    foreach ( $channels_data as $key => $channel) {
                        if ( $channel['live'] != 1 ) {
                            unset($channels_data[$key]);
                        }
                    }
                }
            }

            // Sort by Viewers
            if ( is_array($channels_data) && sizeof ( $channels_data ) > 0 ) {
                foreach ($channels_data as $key => $row) {
                    $viewers[$key] = (isset ($row['viewers']) ) ? $row['viewers'] : '';
                }

                array_multisort($viewers, SORT_DESC, $channels_data);

            }

            // don't show view if 0 channels + mix big box online with small offline box
            if ( is_array($channels_data) && sizeof ( $channels_data ) > 0 ) {

                echo $args['before_widget'];
                if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
                }

                if ( $channel_or_game == 'game' && !empty( $option_channellist_big)) {
                    $channels_data_big = array();
                    $channels_data_small = array();

                    for ($i = 0; $i < sizeof($channels_data); $i++) {
                        if ($i < $option_channellist_big) {
                            $channels_data_big[] = $channels_data[$i];
                        } else {
                            $channels_data_small[] = $channels_data[$i];
                        }
                    }

                    // big ausaugeben
                    if ( sizeof( $channels_data_big ) > 0 ) {
                        $channels_data = $channels_data_big;
                        include TP_TTVW_DIR . 'views/widget_big.php';
                    }

                    // small ausgeben
                    if ( sizeof( $channels_data_small ) > 0 ) {
                        $channels_data = $channels_data_small;
                        include TP_TTVW_DIR . 'views/widget_small.php';
                    }

                }

                elseif ( $template == 'widget_big' ) {
                    $channels_data_big = array();
                    $channels_data_small = array();


                    // Array trennen
                    foreach ( $channels_data as $key => $channel) {
                        if ( $channel['live'] == 1 ) {
                            $channels_data_big[] = $channel;
                        }
                        else {
                            $channels_data_small[] = $channel;
                        }
                    }

                    // big ausaugeben
                    if ( sizeof( $channels_data_big ) > 0 ) {
                        $channels_data = $channels_data_big;
                        include TP_TTVW_DIR . 'views/widget_big.php';
                    }

                    // small ausgeben
                    if ( sizeof( $channels_data_small ) > 0 ) {
                        $channels_data = $channels_data_small;
                        include TP_TTVW_DIR . 'views/widget_small.php';
                    }
                }
                else {
                    include TP_TTVW_DIR . 'views/' . $template . '.php';
                }

                echo $args['after_widget'];

            } else {
                //echo 'keine Channels online';
            }



        } else {
            // Hinweis keine Channels übergeben
        }

    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        // $api_key = ! empty( $instance['api_key'] ) ? $instance['api_key'] : '';
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $channel = ! empty( $instance['channel'] ) ? $instance['channel'] : '';
        $hide_offline_channels = ! empty( $instance['hide_offline_channels'] ) ? $instance['hide_offline_channels'] : '';
        $hide_title = ! empty( $instance['hide_title'] ) ? $instance['hide_title'] : '';
        $hide_stats = ! empty( $instance['hide_stats'] ) ? $instance['hide_stats'] : '';
        $banner_postion = ! empty( $instance['banner_postion'] ) ? $instance['banner_postion'] : '';
        $template = ! empty( $instance['template'] ) ? $instance['template'] : '';
        $style = ! empty( $instance['style'] ) ? $instance['style'] : '';
        $font_color = ! empty( $instance['font_color'] ) ? $instance['font_color'] : '';
        $twitch_game = ! empty( $instance['twitch_game'] ) ? $instance['twitch_game'] : '';
        $max_games = ! empty( $instance['max_games'] ) ? $instance['max_games'] : '';
        $max_games_lite = ! empty( $instance['max_games_lite'] ) ? $instance['max_games_lite'] : '';
        $channel_or_game = ! empty( $instance['channel_or_game'] ) ? $instance['channel_or_game'] : '';
        $twitch_streamer_language = ! empty( $instance['twitch_streamer_language'] ) ? $instance['twitch_streamer_language'] : '';
        $live_or_picture = ! empty( $instance['live_or_picture'] ) ? $instance['live_or_picture'] : '';
        $live_or_picture_height = ! empty( $instance['live_or_picture_height'] ) ? $instance['live_or_picture_height'] : '';
        $live_or_picture_width = ! empty( $instance['live_or_picture_width'] ) ? $instance['live_or_picture_width'] : '';
        $remove_channels = ! empty( $instance['remove_channels'] ) ? $instance['remove_channels'] : '';
        $option_channellist_big = ! empty( $instance['option_channellist_big'] ) ? $instance['option_channellist_big'] : '0';
        $show_aktiv_game = ! empty( $instance['aktiv_game'] ) ? $instance['aktiv_game'] : '';
        $twitch_partner = ! empty( $instance['twitch_partner'] ) ? $instance['twitch_partner'] : '';
        $twitch_streamer_language_two = ! empty( $instance['twitch_streamer_language_two'] ) ? $instance['twitch_streamer_language_two'] : '';


        ?>

        <!-- GLOBAL SETTINGS -->
        <p><a href="https://tp-twitch-widget.com/" target="_blank"> <img style="margin-left: 30%" src="<?php tp_ttvw_the_assets(); ?>img/tp_logo.png" width="150" height="auto" alt="TomParisDE Logo"/></a></p>

        <p style="font-size: 20px; margin-top: -10px; margin-bottom: 30px; text-align: center;"><a href="https://tp-twitch-widget.com/" target="_blank">Get the TP Twitch Widget - Full Version</a></p>

        <p style="text-decoration: underline; font-size: 24px; font-weight: 600;"><?php _e( 'Global Settings', 'tp-ttvw' ); ?></p>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'channel_or_game' ); ?>"><?php _e( 'Channel(s) or Game', 'tp-ttvw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'channel_or_game' ); ?>" name="<?php echo $this->get_field_name( 'channel_or_game' ); ?>">
                <option value="channels"<?php selected( $channel_or_game, "channels" ); ?>><?php _e( 'Channel(s)', 'tp-ttvw' ); ?></option>
                <option value="game"<?php selected( $channel_or_game, "game" ); ?>><?php _e( 'Game', 'tp-ttvw' ); ?></option>
            </select>
        </p>

        <!-- TWITCH CHANNEL SETTINGS -->
        <p style="text-decoration: underline; font-size: 24px; font-weight: 600;"><?php _e( 'Twitch Channel Settings', 'tp-ttvw' ); ?></p>

        <p style="background: #ff2742;">
            <label style="color: #fff; padding-left: 7px; padding-bottom: 8px; font-size: 14px;" for="<?php echo $this->get_field_id( 'channel' ); ?>"><?php _e( 'TP Twitch Widget - Lite [Twitch Channel(s)] is limited (1)', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'channel' ); ?>" name="<?php echo $this->get_field_name( 'channel' ); ?>" type="text" value="<?php echo esc_attr( $channel ); ?>">
        </p>

        <p style="margin-top: -16px;">If you want to put more streamer in here, you can separate your streamer with ” , ”</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'hide_offline_channels' ); ?>"><?php _e( 'Hide Offline User', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'hide_offline_channels' ); ?>" name="<?php echo $this->get_field_name( 'hide_offline_channels' ); ?>" type="checkbox" value="1" <?php echo(@$instance['hide_offline_channels'] == 1 ? 'checked' : ''); ?>  >
        </p>


        <!-- TWITCH GAME SETTINGS -->
        <p style="text-decoration: underline; font-size: 24px; font-weight: 600;"><?php _e( 'Twitch Game Settings', 'tp-ttvw' ); ?></p>

        <p>
            <label for="<?php echo $this->get_field_id( 'twitch_game' ); ?>"><?php _e( 'Twitch Game', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitch_game' ); ?>" name="<?php echo $this->get_field_name( 'twitch_game' ); ?>" type="text" value="<?php echo esc_attr( $twitch_game ); ?>">
        </p>

        <p style="margin-top: -16px;">A drop down list is coming as soon as possible</p>
        <p style="margin-top: -16px;">Meanwhile check the <a href="https://tp-twitch-widget.com/documentation/" target="_blank">documentation</a> </p>

        <p style="background: #ff2742;">
            <label style="color: #fff; padding-left: 7px; padding-bottom: 8px; font-size: 14px;" for="<?php echo $this->get_field_id( 'max_games' ); ?>"><?php _e( 'TP Twitch Widget - Lite [Max Streams (1-100)] is limited (1)', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max_games' ); ?>" name="<?php echo $this->get_field_name( 'max_games' ); ?>" type="text" value="<?php echo esc_attr( $max_games ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'option_channellist_big' ); ?>"><?php _e( 'View first (1-100) as Big Channels', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'option_channellist_big' ); ?>" name="<?php echo $this->get_field_name( 'option_channellist_big' ); ?>" type="text" value="<?php echo esc_attr( $option_channellist_big ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'twitch_streamer_language_two' ); ?>"><?php _e( 'Twitch Streamer Language', 'tp-tw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'twitch_streamer_language_two' ); ?>" name="<?php echo $this->get_field_name( 'twitch_streamer_language_two' ); ?>">
                <option value="en"<?php selected( $twitch_streamer_language_two, "en" ); ?>><?php _e( 'English', 'tp-tw' ); ?></option>
                <option value="da"<?php selected( $twitch_streamer_language_two, "da" ); ?>><?php _e( 'Dansk', 'tp-tw' ); ?></option>
                <option value="de"<?php selected( $twitch_streamer_language_two, "de" ); ?>><?php _e( 'Deutsch', 'tp-tw' ); ?></option>
                <option value="en-gb"<?php selected( $twitch_streamer_language_two, "en-gb" ); ?>><?php _e( 'English - UK', 'tp-tw' ); ?></option>
                <option value="es"<?php selected( $twitch_streamer_language_two, "es" ); ?>><?php _e( 'Español - España', 'tp-tw' ); ?></option>
                <option value="es-mx"<?php selected( $twitch_streamer_language_two, "es-mx" ); ?>><?php _e( 'Español - Latinoamérica', 'tp-tw' ); ?></option>
                <option value="fr"<?php selected( $twitch_streamer_language_two, "fr" ); ?>><?php _e( 'Français', 'tp-tw' ); ?></option>
                <option value="it"<?php selected( $twitch_streamer_language_two, "it" ); ?>><?php _e( 'Italiano', 'tp-tw' ); ?></option>
                <option value="hu"<?php selected( $twitch_streamer_language_two, "hu" ); ?>><?php _e( 'Magyar', 'tp-tw' ); ?></option>
                <option value="nl"<?php selected( $twitch_streamer_language_two, "nl" ); ?>><?php _e( 'Nederlands', 'tp-tw' ); ?></option>
                <option value="no"<?php selected( $twitch_streamer_language_two, "no" ); ?>><?php _e( 'Norsk', 'tp-tw' ); ?></option>
                <option value="pl"<?php selected( $twitch_streamer_language_two, "pl" ); ?>><?php _e( 'Polski', 'tp-tw' ); ?></option>
                <option value="pt"<?php selected( $twitch_streamer_language_two, "pt" ); ?>><?php _e( 'Português', 'tp-tw' ); ?></option>
                <option value="pt-br"<?php selected( $twitch_streamer_language_two, "pt-br" ); ?>><?php _e( 'Português - Brasil', 'tp-tw' ); ?></option>
                <option value="sk"<?php selected( $twitch_streamer_language_two, "sk" ); ?>><?php _e( 'Slovenčina', 'tp-tw' ); ?></option>
                <option value="fi"<?php selected( $twitch_streamer_language_two, "fi" ); ?>><?php _e( 'Suomi', 'tp-tw' ); ?></option>
                <option value="sv"<?php selected( $twitch_streamer_language_two, "sv" ); ?>><?php _e( 'Svenska', 'tp-tw' ); ?></option>
                <option value="vi"<?php selected( $twitch_streamer_language_two, "vi" ); ?>><?php _e( 'Tiếng Việt', 'tp-tw' ); ?></option>
                <option value="tr"<?php selected( $twitch_streamer_language_two, "tr" ); ?>><?php _e( 'Türkçe', 'tp-tw' ); ?></option>
                <option value="cs"<?php selected( $twitch_streamer_language_two, "cs" ); ?>><?php _e( 'Čeština', 'tp-tw' ); ?></option>
                <option value="el"<?php selected( $twitch_streamer_language_two, "el" ); ?>><?php _e( 'Ελληνικά', 'tp-tw' ); ?></option>
                <option value="bg"<?php selected( $twitch_streamer_language_two, "bg" ); ?>><?php _e( 'Български', 'tp-tw' ); ?></option>
                <option value="ru"<?php selected( $twitch_streamer_language_two, "ru" ); ?>><?php _e( 'Русский', 'tp-tw' ); ?></option>
                <option value="ar"<?php selected( $twitch_streamer_language_two, "ar" ); ?>><?php _e( 'العربية', 'tp-tw' ); ?></option>
                <option value="th"<?php selected( $twitch_streamer_language_two, "th" ); ?>><?php _e( 'ภาษาไทย', 'tp-tw' ); ?></option>
                <option value="zh-cn"<?php selected( $twitch_streamer_language_two, "zh-cn" ); ?>><?php _e( '中文 简体', 'tp-tw' ); ?></option>
                <option value="zh-tw"<?php selected( $twitch_streamer_language_two, "zh-tw" ); ?>><?php _e( '中文 繁體', 'tp-tw' ); ?></option>
                <option value="ja"<?php selected( $twitch_streamer_language_two, "ja" ); ?>><?php _e( '日本語', 'tp-tw' ); ?></option>
                <option value="ko"<?php selected( $twitch_streamer_language_two, "ko" ); ?>><?php _e( '한국어', 'tp-tw' ); ?></option>
                <option value="hi"<?php selected( $twitch_streamer_language_two, "hi" ); ?>><?php _e( 'हिंदी', 'tp-tw' ); ?></option>
                <option value="ro"<?php selected( $twitch_streamer_language_two, "ro" ); ?>><?php _e( 'Română', 'tp-tw' ); ?></option>
            </select>
        </p>

        <p style="background: #ff2742;">
            <label style="color: #fff; padding-left: 7px; padding-bottom: 8px; font-size: 14px;" for="<?php echo $this->get_field_id( 'remove_channels' ); ?>"><?php _e( 'TP Twitch Widget - Lite [Exclude Twitch Channel(s)]', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'remove_channels' ); ?>" name="<?php echo $this->get_field_name( 'remove_channels' ); ?>" type="text" value="TP Twitch Widget - Full Version Feature">
        </p>


        <!-- FONT END SETTINGS -->
        <p style="text-decoration: underline; font-size: 24px; font-weight: 600;"><?php _e( 'Front-end Output', 'tp-ttvw' ); ?></p>
        <p style="margin-top: 0px; font-size: 16px; font-weight: 600;"><?php _e( 'Global Settings', 'tp-tw' ); ?></p>

        <p>
            <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template', 'tp-ttvw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>">
                <option value="widget_small"<?php selected( $template, "widget_small" ); ?>><?php _e( 'Small', 'tp-ttvw' ); ?></option>
                <option value="widget_big"<?php selected( $template, "widget_big" ); ?>><?php _e( 'Big', 'tp-ttvw' ); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style', 'tp-ttvw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
                <option value="light"<?php selected( $style, "light" ); ?>><?php _e( 'Light', 'tp-ttvw' ); ?></option>
                <option value="dark"<?php selected( $style, "dark" ); ?>><?php _e( 'Dark', 'tp-ttvw' ); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'font_color' ); ?>"><?php _e( 'Font Color', 'tp-ytw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'font_color' ); ?>" name="<?php echo $this->get_field_name( 'font_color' ); ?>" placeholder="#000" type="text" value="<?php echo esc_attr( $font_color ); ?>">
        </p>

        <p style="background: #ff2742; color: #fff; padding-left: 7px; padding-bottom: 1px; font-size: 14px; margin-bottom: -12px;">TP Twitch Widget - Lite [Only Picture View available]</p>
          <p style="background: #ff2742">
            <label style="color: #fff; padding-left: 7px; padding-bottom: 8px; font-size: 14px;" for="<?php echo $this->get_field_id( 'live_or_picture' ); ?>"><?php _e( 'Live View or Live Preview Picture: (Big Template)', 'tp-ytw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'live_or_picture' ); ?>" name="<?php echo $this->get_field_name( 'live_or_picture' ); ?>">
                <option value="twitch_picture"<?php selected( $live_or_picture, "twitch_picture" ); ?>><?php _e( 'Live View', 'tp-ytw' ); ?></option>
                <option value="twitch_picture"<?php selected( $live_or_picture, "twitch_picture" ); ?>><?php _e( 'Live Preview Picture', 'tp-ytw' ); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'live_or_picture_width' ); ?>"><?php _e( 'Live View (Width): (Big Template)', 'tp-ytw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'live_or_picture_width' ); ?>" name="<?php echo $this->get_field_name( 'live_or_picture_width' ); ?>" placeholder="auto" type="text" value="<?php echo esc_attr( $live_or_picture_width ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'live_or_picture_height' ); ?>"><?php _e( 'Live View (Height): (Big Template)', 'tp-ytw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'live_or_picture_height' ); ?>" name="<?php echo $this->get_field_name( 'live_or_picture_height' ); ?>" placeholder="auto" type="text" value="<?php echo esc_attr( $live_or_picture_height ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'banner_postion' ); ?>"><?php _e( 'Live View or Live Preview Picture Position: (Big Template)', 'tp-ytw' ); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id( 'banner_postion' ); ?>" name="<?php echo $this->get_field_name( 'banner_postion' ); ?>">
                <option value="top"<?php selected( $banner_postion, "top" ); ?>><?php _e( 'Top', 'tp-ytw' ); ?></option>
                <option value="middle"<?php selected( $banner_postion, "middle" ); ?>><?php _e( 'Middle', 'tp-ytw' ); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'hide_title' ); ?>"><?php _e( 'Hide Title (Big Template)', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'hide_title' ); ?>" name="<?php echo $this->get_field_name( 'hide_title' ); ?>" type="checkbox" value="1" <?php echo(@$instance['hide_title'] == 1 ? 'checked' : ''); ?>  >
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'aktiv_game' ); ?>"><?php _e( 'Hide currently playing game (Big Template)', 'tp-tw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'aktiv_game' ); ?>" name="<?php echo $this->get_field_name( 'aktiv_game' ); ?>" type="checkbox" value="1" <?php echo(@$instance['aktiv_game'] == 1 ? 'checked' : ''); ?>  >
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'hide_stats' ); ?>"><?php _e( 'Hide Statistics (Big Template)', 'tp-ttvw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'hide_stats' ); ?>" name="<?php echo $this->get_field_name( 'hide_stats' ); ?>" type="checkbox" value="1" <?php echo(@$instance['hide_stats'] == 1 ? 'checked' : ''); ?>  >
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'twitch_partner' ); ?>"><?php _e( 'Hide Partner Logo', 'tp-tw' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitch_partner' ); ?>" name="<?php echo $this->get_field_name( 'twitch_partner' ); ?>" type="checkbox" value="1" <?php echo(@$instance['twitch_partner'] == 1 ? 'checked' : ''); ?>  >
        </p>

        <?php
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        // $instance['api_key'] = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['channel'] = ( ! empty( $new_instance['channel'] ) ) ? strip_tags( $new_instance['channel'] ) : '';
        $instance['hide_offline_channels'] = ( ! empty( $new_instance['hide_offline_channels'] ) ) ? strip_tags( $new_instance['hide_offline_channels'] ) : '';
        $instance['hide_title'] = ( ! empty( $new_instance['hide_title'] ) ) ? strip_tags( $new_instance['hide_title'] ) : '';
        $instance['hide_stats'] = ( ! empty( $new_instance['hide_stats'] ) ) ? strip_tags( $new_instance['hide_stats'] ) : '';
        $instance['template'] = ( ! empty( $new_instance['template'] ) ) ? strip_tags( $new_instance['template'] ) : 'widget_small';
        $instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : 'light';
        $instance['banner_postion'] = ( ! empty( $new_instance['banner_postion'] ) ) ? strip_tags( $new_instance['banner_postion'] ) : 'middle';
        $instance['font_color'] = ( ! empty( $new_instance['font_color'] ) ) ? strip_tags( $new_instance['font_color'] ) : '';
        $instance['twitch_game'] = ( ! empty( $new_instance['twitch_game'] ) ) ? strip_tags( $new_instance['twitch_game'] ) : '';
        $instance['max_games'] = 1;
        $instance['channel_or_game'] = ( ! empty( $new_instance['channel_or_game'] ) ) ? strip_tags( $new_instance['channel_or_game'] ) : '';
        $instance['twitch_streamer_language'] = ( ! empty( $new_instance['twitch_streamer_language'] ) ) ? strip_tags( $new_instance['twitch_streamer_language'] ) : '';
        $instance['live_or_picture'] = ( ! empty( $new_instance['live_or_picture'] ) ) ? strip_tags( $new_instance['live_or_picture'] ) : '';
        $instance['live_or_picture_height'] = ( ! empty( $new_instance['live_or_picture_height'] ) ) ? strip_tags( $new_instance['live_or_picture_height'] ) : '';
        $instance['live_or_picture_width'] = ( ! empty( $new_instance['live_or_picture_width'] ) ) ? strip_tags( $new_instance['live_or_picture_width'] ) : '';
        // $instance['remove_channels'] = ( ! empty( $new_instance['remove_channels'] ) ) ? strip_tags( $new_instance['remove_channels'] ) : '';
        $instance['option_channellist_big'] = ( ! empty( $new_instance['option_channellist_big'] ) ) ? strip_tags( $new_instance['option_channellist_big'] ) : '';
        $instance['aktiv_game'] = ( ! empty( $new_instance['aktiv_game'] ) ) ? strip_tags( $new_instance['aktiv_game'] ) : '';
        $instance['twitch_partner'] = ( ! empty( $new_instance['twitch_partner'] ) ) ? strip_tags( $new_instance['twitch_partner'] ) : '';
        $instance['twitch_streamer_language_two'] = ( ! empty( $new_instance['twitch_streamer_language_two'] ) ) ? strip_tags( $new_instance['twitch_streamer_language_two'] ) : 'en';

        return $instance;
    }

} // class Foo_Widget


// register TP Twitch widget
function tp_ttvw_register_widget() {
    register_widget( 'TP_TTVW_Widget' );
}
add_action( 'widgets_init', 'tp_ttvw_register_widget' );