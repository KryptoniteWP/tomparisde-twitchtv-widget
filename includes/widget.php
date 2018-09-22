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
				esc_html__( 'Twitch', 'tp-twitch-widget' ), // Name
				array( 'description' => esc_html__( 'Display Twitch streams in your sidebars.', 'tp-twitch-widget' ), ) // Args
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
			 * WidgetHeader
			 */
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			/*
			 * Widget Body
			 */
			$streams_args = array();
			$template_args = array();

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
                $template_args['language'] = ( ! empty ( $instance['language'] ) ) ? $instance['language'] : tp_twitch_get_option( 'language' );

				// Max
				if ( ! empty ( $instance['max'] ) ) {
					$streams_args['max'] = intval( $instance['max'] );
				}
            }

			// Template, which is hardcoded for widgets
			$template_args['template'] = 'widget';

			// Size
			$template_args['widget_size'] = ( ! empty ( $instance['size'] ) ) ? $instance['size'] : tp_twitch_get_option( 'widget_size' );

			// Preview
			$template_args['widget_preview'] = ( ! empty ( $instance['preview'] ) ) ? $instance['preview'] : tp_twitch_get_option( 'widget_preview' );

			//tp_twitch_debug( $streams_args, '$streams_args' );
			//tp_twitch_debug( $template_args, '$template_args' );

			// Final output.
			tp_twitch_display_streams( $streams_args, $template_args, true );

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
		    
			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'New title', 'tp-twitch-widget' );
			?>
            <!-- Title -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'tp-twitch-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <?php
			$streamer = ( ! empty( $instance['streamer'] ) ) ? $instance['streamer'] : '';
            ?>
            <!-- Streamer -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'streamer' ) ); ?>"><?php esc_attr_e( 'Streamer:', 'tp-twitch-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'streamer' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'streamer' ) ); ?>" type="text" value="<?php echo esc_attr( $streamer ); ?>" data-tp-twitch-widget-config-streamer-input="true">
            </p>
            <div class="tp-twitch-widget-config-search-block"<?php if ( ! empty( $streamer ) ) echo ' style="display: none;"'; ?>><!-- Don't show this block when streamers were entered -->
                <!-- Game -->
                <?php
                $game_options = tp_twitch_get_game_options();
                $game = ( ! empty( $instance['game'] ) && is_numeric( $instance['game'] ) ) ? intval( $instance['game'] ) : 0;
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>"><?php esc_attr_e( 'Game', 'tp-twitch-widget' ); ?></label>
                    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'game' ) ); ?>">
                        <?php foreach ( $game_options as $key => $label ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $game, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                        <?php } ?>
                    </select>
                </p>
                <!-- Language -->
                <?php
                $language_options = tp_twitch_get_language_options();
                $language = ( ! empty( $instance['language'] ) ) ? $instance['language'] : '';
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>"><?php esc_attr_e( 'Language', 'tp-twitch-widget' ); ?></label>
                    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'language' ) ); ?>">
                        <?php foreach ( $language_options as $key => $label ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $language, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                        <?php } ?>
                    </select>
                </p>
                <!-- Maximum Amount of Streams -->
                <?php
                $max = ( ! empty( $instance['max'] ) && is_numeric( $instance['max'] ) ) ? intval( $instance['max'] ) : 5;
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"><?php esc_attr_e( 'Maximum Amount of Streams:', 'tp-twitch-widget' ); ?></label>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max' ) ); ?>" type="number" value="<?php echo esc_attr( $max ); ?>">
                </p>
            </div>
            <!-- Size -->
			<?php
			$size_options = tp_twitch_get_widget_size_options();
			$size = ( ! empty( $instance['size'] ) ) ? $instance['size'] : '';
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_attr_e( 'Size', 'tp-twitch-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>">
					<?php foreach ( $size_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $size, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- Preview -->
			<?php
			$preview_options = tp_twitch_get_widget_preview_options();
			$preview = ( ! empty( $instance['preview'] ) ) ? $instance['preview'] : '';
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'preview' ) ); ?>"><?php esc_attr_e( 'Preview', 'tp-twitch-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'preview' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'preview' ) ); ?>">
					<?php foreach ( $preview_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $preview, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
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
			$instance['streamer'] = ( ! empty( $new_instance['streamer'] ) ) ? str_replace( array( ';', ' ' ), array( ',', ',' ), trim( sanitize_text_field( $new_instance['streamer'] ) ) ) : '';
			$instance['game'] = ( ! empty( $new_instance['game'] ) ) ? sanitize_text_field( $new_instance['game'] ) : '';
			$instance['language'] = ( ! empty( $new_instance['language'] ) ) ? sanitize_text_field( $new_instance['language'] ) : '';
			$instance['max'] = ( ! empty( $new_instance['max'] ) ) ? sanitize_text_field( $new_instance['max'] ) : '';
			$instance['size'] = ( ! empty( $new_instance['size'] ) ) ? sanitize_text_field( $new_instance['size'] ) : '';
			$instance['preview'] = ( ! empty( $new_instance['preview'] ) ) ? sanitize_text_field( $new_instance['preview'] ) : '';

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