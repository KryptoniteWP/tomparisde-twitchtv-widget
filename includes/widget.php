<?php
/**
 * Widgets
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TP_Twitch_Widget' ) ) :

	class TP_Twitch_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'tp_twitch_widget', // Base ID
				esc_html__( 'Twitch', 'tomparisde-twitchtv-widget' ), // Name
				array( 'description' => esc_html__( 'Display Twitch streams in your sidebars.', 'tomparisde-twitchtv-widget' ), ) // Args
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
			echo $args['before_widget'];

			/*
			 * Widget Header
			 */
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			/*
			 * Widget Body
			 */
			$streams_args = array();
			$template_args = array();
			$output_args = array();

            //tp_twitch_debug( $instance );

			// Streamer
			if ( ! empty ( $instance['streamer'] ) ) {
				$streams_args['streamer'] = $instance['streamer'];

            // Search
			} else {

				// Game.
				if ( ! empty ( $instance['game'] ) ) {
					$streams_args['game_id'] = intval( $instance['game'] );
				}

				// Language
                $streams_args['language'] = ( ! empty ( $instance['language'] ) ) ? $instance['language'] : tp_twitch_get_option( 'language' );
            }

            $streams_args = apply_filters( 'tp_twitch_widget_streams_args', $streams_args, $instance );

            // Max
            if ( ! empty ( $instance['max'] ) && is_numeric( $instance['max'] ) ) {
                $output_args['max'] = intval( $instance['max'] );
            }

            // Hide offline users
            if ( isset( $instance['hide_offline'] ) && true == $instance['hide_offline'] )
                $output_args['hide_offline'] = true;

            $output_args = apply_filters( 'tp_twitch_widget_output_args', $output_args, $instance );

			// Template, which is hardcoded for widgets
			$template_args['template'] = 'widget';

            // Style
            $template_args['style'] = ( ! empty ( $instance['style'] ) ) ? $instance['style'] : tp_twitch_get_option( 'widget_style' );

			// Size
			$template_args['size'] = ( ! empty ( $instance['size'] ) ) ? $instance['size'] : tp_twitch_get_option( 'widget_size' );

			// Preview
			$template_args['preview'] = ( ! empty ( $instance['preview'] ) ) ? $instance['preview'] : tp_twitch_get_option( 'widget_preview' );

            $template_args = apply_filters( 'tp_twitch_widget_template_args', $template_args, $instance );

			//tp_twitch_debug( $streams_args, '$streams_args' );
			//tp_twitch_debug( $template_args, '$template_args' );
            //tp_twitch_debug( $output_args, '$output_args' );

			// Final output.
			tp_twitch_display_streams( $streams_args, $template_args, $output_args, true );

			/*
			 * Widget Footer
			 */
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

            tp_twitch_pre_pro_the_widget_upgrade_info();

			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
			?>
            <!-- Title -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'tomparisde-twitchtv-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <?php
			$streamer = ( ! empty( $instance['streamer'] ) ) ? $instance['streamer'] : '';
            ?>

            <h4><?php _e('Streams Settings', 'tomparisde-twitchtv-widget' ); ?></h4>
            <!-- Streamer -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'streamer' ) ); ?>"><?php esc_attr_e( 'Streamer', 'tomparisde-twitchtv-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'streamer' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'streamer' ) ); ?>" type="text" value="<?php echo esc_attr( $streamer ); ?>" data-tp-twitch-widget-config-streamer-input="true">
                <?php tp_twitch_pre_pro_the_widget_streams_max_note(); ?>
            </p>
            <p class="description">
                <?php printf( esc_html__( 'The username of a streamer. For instance: %s', 'tomparisde-twitchtv-widget' ), '<strong>dreamhackcs</strong>' ); ?>
            </p>
            <p class="description">
                <?php printf( esc_html__( 'Comma separate multiple streamers as follows: %s', 'tomparisde-twitchtv-widget' ), '<strong>dreamhackcs,RiotGames2</strong>' ); ?>
            </p>

            <div class="tp-twitch-widget-config-search-block"<?php if ( ! empty( $streamer ) ) echo ' style="display: none;"'; ?>><!-- Don't show this block when streamers were entered -->
                <!-- Game -->
                <?php
                $game_options = tp_twitch_get_game_options();
                $game = ( ! empty( $instance['game'] ) && is_numeric( $instance['game'] ) ) ? intval( $instance['game'] ) : 0;
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>"><?php esc_attr_e( 'Game', 'tomparisde-twitchtv-widget' ); ?></label>
                    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'game' ) ); ?>">
                        <?php foreach ( $game_options as $key => $label ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $game, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                        <?php } ?>
                    </select>
                    <?php tp_twitch_pre_pro_the_available_games_note(); ?>
                </p>
                <!-- Language -->
                <?php
                $language_options = tp_twitch_get_language_options();
                $language = ( ! empty( $instance['language'] ) ) ? $instance['language'] : '';
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>"><?php esc_attr_e( 'Language', 'tomparisde-twitchtv-widget' ); ?></label>
                    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'language' ) ); ?>">
                        <?php foreach ( $language_options as $key => $label ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $language, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                        <?php } ?>
                    </select>
                </p>
            </div>

            <h4><?php _e('Output Settings', 'tomparisde-twitchtv-widget' ); ?></h4>
            <!-- Maximum Amount of Streams -->
            <?php $max = ( ! empty( $instance['max'] ) && is_numeric( $instance['max'] ) ) ? intval( $instance['max'] ) : tp_twitch_get_default_streams_max(); ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"><?php esc_attr_e( 'Maximum Amount of Streams', 'tomparisde-twitchtv-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max' ) ); ?>" type="number" value="<?php echo esc_attr( $max ); ?>">
                <?php tp_twitch_pre_pro_the_widget_streams_max_note(); ?>
            </p>

            <!-- Hide offline -->
            <?php $hide_offline = ( isset( $instance['hide_offline'] ) && '1' == $instance['hide_offline'] ) ? 1 : 0; ?>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hide_offline' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_offline' ) ); ?>" type="checkbox" value="1" <?php checked( $hide_offline, true ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'hide_offline' ) ); ?>"><?php esc_attr_e( 'Hide offline streams', 'tomparisde-twitchtv-widget' ); ?></label>
            </p>

            <?php do_action( 'tp_twitch_widget_form_output_settings', $this, $instance ); ?>

            <h4><?php _e('Template Settings', 'tomparisde-twitchtv-widget' ); ?></h4>
            <!-- Style -->
            <?php
            $style_options = tp_twitch_get_widget_style_options( false );
            $style = ( ! empty( $instance['style'] ) ) ? $instance['style'] : '';
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_attr_e( 'Style', 'tomparisde-twitchtv-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                    <?php foreach ( $style_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $style, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                    <?php } ?>
                </select>
                <?php tp_twitch_pre_pro_the_styles_note(); ?>
            </p>
            <!-- Size -->
			<?php
			$size_options = tp_twitch_get_widget_size_options( false );
			$size = ( ! empty( $instance['size'] ) ) ? $instance['size'] : '';
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_attr_e( 'Size', 'tomparisde-twitchtv-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>">
					<?php foreach ( $size_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $size, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- Preview -->
			<?php
			$preview_options = tp_twitch_get_widget_preview_options( false );
			$preview = ( ! empty( $instance['preview'] ) ) ? $instance['preview'] : '';
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'preview' ) ); ?>"><?php esc_attr_e( 'Preview', 'tomparisde-twitchtv-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'preview' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'preview' ) ); ?>">
					<?php foreach ( $preview_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $preview, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>

            <?php do_action( 'tp_twitch_widget_form_template_settings', $instance ); ?>

            <!-- Documentation -->
            <h4><?php _e('Need help?', 'tomparisde-twitchtv-widget'); ?></h4>
            <p>
                <?php printf( wp_kses( __( 'Please take a look into the <a href="%s">documentation</a> for help and find out more options.', 'tomparisde-twitchtv-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( add_query_arg( array(
                    'utm_source'   => 'widgets-page',
                    'utm_medium'   => 'need-help-note',
                    'utm_campaign' => 'Twitch WP',
                ), TP_TWITCH_DOCS_URL ) ) ); ?>
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

			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['streamer'] = ( ! empty( $new_instance['streamer'] ) ) ? tp_twitch_sanitize_comma_separated_input( $new_instance['streamer'] ) : '';
			$instance['game'] = ( ! empty( $new_instance['game'] ) ) ? sanitize_text_field( $new_instance['game'] ) : '';
			$instance['language'] = ( ! empty( $new_instance['language'] ) ) ? sanitize_text_field( $new_instance['language'] ) : '';
			$instance['max'] = ( ! empty( $new_instance['max'] ) ) ? sanitize_text_field( $new_instance['max'] ) : '';
            $instance['hide_offline'] = ( ! empty( $new_instance['hide_offline'] ) ) ? sanitize_text_field( $new_instance['hide_offline'] ) : '';
            $instance['style'] = ( ! empty( $new_instance['style'] ) ) ? sanitize_text_field( $new_instance['style'] ) : '';
			$instance['size'] = ( ! empty( $new_instance['size'] ) ) ? sanitize_text_field( $new_instance['size'] ) : '';
			$instance['preview'] = ( ! empty( $new_instance['preview'] ) ) ? sanitize_text_field( $new_instance['preview'] ) : '';

			$instance = apply_filters( 'tp_twitch_widget_update', $instance, $new_instance, $old_instance );

			return $instance;
		}

	}
endif; // End if class_exists check

/**
 * Register Widgets
 */
function tp_twitch_register_widgets() {
	register_widget( 'TP_Twitch_Widget' );
}
add_action( 'widgets_init', 'tp_twitch_register_widgets' );