<?php
/**
 * Template Functions
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get template file
 *
 * @param $template
 *
 * @return string
 */
function tp_twitch_get_template_file( $template ) {

	$template_file = TP_TWITCH_PLUGIN_DIR . 'templates/' . $template . '.php';

	$template_file = apply_filters( 'tp_twitch_template_file', $template_file, $template );

	if ( file_exists( $template_file ) ) {
		return $template_file;
	}

	return TP_TWITCH_PLUGIN_DIR . 'templates/widget.php';
}

/**
 * Get default template
 *
 * @param bool $is_widget
 *
 * @return string
 */
function tp_twitch_get_default_template( $is_widget = true ) {

	$template = apply_filters( 'tp_twitch_get_template', 'widget', $is_widget );

	return $template;
}

/**
 * Display streams
 *
 * @param array $streams_args
 * @param array $template_args
 * @param bool $is_widget
 */
function tp_twitch_display_streams( $streams_args = array(), $template_args = array(), $is_widget = true ) {

	$streams = tp_twitch_get_streams( $streams_args );

	//tp_twitch_debug( $streams, '$streams' );

	// Streams found.
	if ( $streams ) {

		$template = ( isset ( $template_args['template'] ) ) ? $template_args['template'] : tp_twitch_get_default_template( $is_widget );
		$template_file = tp_twitch_get_template_file( $template );

		//tp_twitch_debug( $template, '$template' );
		//tp_twitch_debug( $template_file, '$template_file' );

		// Load template.
		if ( $template_file ) {

			 include $template_file;

		 // Template not found.
		} else {
			_e( 'Template not found.', 'tp-twitch-widget' );
		}

	// No streams found.
	} else {

		$no_streams_found = tp_twitch_get_option( 'no_streams_found' );
		$no_streams_found_text = apply_filters( 'tp_twitch_no_streams_found_text', __( 'No streams found.', 'tp-twitch-widget' ) );

		if ( 'show' === $no_streams_found ) {
			echo $no_streams_found_text;
		} elseif ( 'admin' === $no_streams_found && tp_twitch_is_user_admin() ) {
			echo $no_streams_found_text;
		}
	}
}