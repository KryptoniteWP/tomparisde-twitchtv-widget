<?php
/**
 * Widget small template
 *
 * @var TP_Twitch_Stream $stream
 */

if ( ! isset( $streams ) || ! isset( $template_args ) )
	return;
?>
<div class="tp-twitch">
    <div class="tp-twitch-streams tp-twitch-streams--widget-small">
	    <?php foreach ( $streams as $stream ) { ?>
            <div class="<?php $stream->the_classes('tp-twitch-stream'); ?>">
                <div class="tp-twitch-stream__body">
                    <span class="tp-twitch-stream__user-avatar">
                        <a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow">
                            <img class="tp-twitch-stream__avatar" src="<?php echo $stream->get_user_avatar_url( 50, 50 ); ?>" alt="<?php echo $stream->get_user_display_name(); ?>" />
                        </a>
                    </span>
                    <span class="tp-twitch-stream__title"><a href="<?php echo $stream->get_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_title(); ?></a></span>
                    <span class="tp-twitch-stream__user">
                        <span class="tp-twitch-icon-user"></span><a href="<?php echo $stream->get_user_url(); ?>" target="_blank" rel="nofollow"><?php echo $stream->get_user_display_name(); ?></a>
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
    </div>
</div>