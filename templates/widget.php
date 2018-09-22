<?php
/**
 * Widget template
 *
 * @var TP_Twitch_Stream $stream
 */

if ( ! isset( $streams ) || ! isset( $template_args ) )
	return;

$widget_style = ( isset ( $template_args ) && ! empty( $template_args['widget_style'] ) ) ? $template_args['widget_style'] : '';
$widget_size = ( isset ( $template_args ) && ! empty( $template_args['widget_size'] ) ) ? $template_args['widget_size'] : 'large';
$widget_preview = ( isset ( $template_args ) && ! empty( $template_args['widget_preview'] ) ) ? $template_args['widget_preview'] : 'image';

$stream_count = 0;
?>
<div class="tp-twitch">
    <div class="tp-twitch-streams tp-twitch-streams--widget<?php if ( ! empty( $widget_style ) ) echo ' tp-twitch-streams--' . $widget_style; ?>" >

	    <?php foreach ( $streams as $stream ) { ?>
            <?php $stream_count++; ?>

            <?php if ( 'large' === $widget_size || ( 'large-first' === $widget_size && 1 === $stream_count ) ) { ?>

                <div class="<?php $stream->the_classes('tp-twitch-stream'); ?>">
                    <div class="tp-twitch-stream__header">

                        <?php if ( 'video' === $widget_preview || ( 'video-first' === $widget_preview && 1 === $stream_count ) ) { ?>

                            <div class="tp-twitch-stream__video">
                                <div class="tp-twitch-iframe-container">
                                    <iframe
                                            src="https://player.twitch.tv/?channel=<?php echo $stream->get_user_name(); ?>&muted=true"
                                            width="560"
                                            height="315"
                                            frameborder="0"
                                            scrolling="no"
                                            allowfullscreen="true">
                                    </iframe>
                                </div>
                            </div>

                        <?php } else { ?>

                            <a class="tp-twitch-stream__thumbnail-link" href="<?php echo $stream->get_url(); ?>" target="_blank" rel="nofollow">
                                <img class="tp-twitch-stream__thumbnail" src="<?php echo $stream->get_thumbnail_url( 480, 270 ); ?>" alt="<?php echo $stream->get_thumbnail_alt(); ?>" />
                            </a>

                        <?php } ?>

                    </div>
                    <div class="tp-twitch-stream__body">
                        <span class="tp-twitch-stream__user-avatar">
                            <a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow">
                                <img class="tp-twitch-stream__avatar" src="<?php echo $stream->get_user_avatar_url( 50, 50 ); ?>" alt="<?php echo $stream->get_user_display_name(); ?>" />
                            </a>
                        </span>
                        <span class="tp-twitch-stream__title"><a href="<?php echo $stream->get_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_title(); ?></a></span>
                        <span class="tp-twitch-stream__user">
                            <span class="tp-twitch-icon-user"></span><a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_user_display_name(); ?></a><?php $stream->the_user_verified_icon(); ?>
                        </span>
                        <span class="tp-twitch-stream__game">
                            <span class="tp-twitch-icon-game"></span><a href="<?php echo $stream->get_game_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_game(); ?></a>
                        </span>
                    </div>
                    <div class="tp-twitch-stream__footer">
                        <span class="tp-twitch-stream__viewer">
                            <span class="tp-twitch-icon-viewer"></span><?php echo $stream->get_viewer( true ); ?>
                        </span>
                        <span class="tp-twitch-stream__views">
                            <span class="tp-twitch-icon-views"></span><?php echo $stream->get_views( true ); ?>
                        </span>
                    </div>
                </div>

            <?php } else { ?>

                <div class="<?php $stream->the_classes('tp-twitch-stream'); ?>">
                    <div class="tp-twitch-stream__body">
                    <span class="tp-twitch-stream__user-avatar">
                        <a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow">
                            <img class="tp-twitch-stream__avatar" src="<?php echo $stream->get_user_avatar_url( 50, 50 ); ?>" alt="<?php echo $stream->get_user_display_name(); ?>" />
                        </a>
                    </span>
                        <span class="tp-twitch-stream__title"><a href="<?php echo $stream->get_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_title(); ?></a></span>
                        <span class="tp-twitch-stream__user">
                        <span class="tp-twitch-icon-user"></span><a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_user_display_name(); ?></a><?php $stream->the_user_verified_icon(); ?>
                    </span>
                    </div>
                    <div class="tp-twitch-stream__footer">
                    <span class="tp-twitch-stream__viewer">
                        <span class="tp-twitch-icon-viewer"></span><?php echo $stream->get_viewer( true ); ?>
                    </span>
                        <span class="tp-twitch-stream__views">
                        <span class="tp-twitch-icon-views"></span><?php echo $stream->get_views( true ); ?>
                    </span>
                    </div>
                </div>

            <?php } ?>

	    <?php } ?>

    </div>
</div>