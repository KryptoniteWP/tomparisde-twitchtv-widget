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
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			echo esc_html__( 'Hello, World!', 'tp-twitch-widget' );
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
			$type = ( ! empty( $instance['type'] ) ) ? $instance['type'] : '';
			$max = ( ! empty( $instance['max'] ) && is_numeric( $instance['max'] ) ) ? intval( $instance['max'] ) : 5;
			
			?>
            <!-- Title -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'tp-twitch-widget' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
            <!-- Type -->
			<?php
			$type_options = array(
				''         => __( 'Please select...', 'tp-twitch-widget' ),
				'game'     => __( 'Game', 'tp-twitch-widget' ),
				'streamer' => __( 'Streamer', 'tp-twitch-widget' )
			);
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_attr_e( 'Show Streams by...', 'tp-twitch-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
					<?php foreach ( $type_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- Type "GAME": Game -->
			<?php
			$game_options = tp_twitch_get_game_options();
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>"><?php esc_attr_e( 'Game', 'tp-twitch-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'game' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'game' ) ); ?>">
					<?php foreach ( $game_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- Type "GAME": Language -->
			<?php
			$language_options = tp_twitch_get_language_options();
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>"><?php esc_attr_e( 'Language', 'tp-twitch-widget' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'language' ) ); ?>">
					<?php foreach ( $language_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- General: Maximum Amount of Streams -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"><?php esc_attr_e( 'Maximum Amount of Streams:', 'tp-twitch-widget' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max' ) ); ?>" type="number" value="<?php echo esc_attr( $max ); ?>">
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