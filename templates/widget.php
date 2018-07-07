<?php
/**
 * Widget template
 *
 * @var TP_Twitch_Stream $stream
 */

if ( ! isset( $streams ) || ! isset( $template_args ) )
	return;
?>
<div class="tp-twitch">
    <div class="tp-twitch-streams tp-twitch-streams--widget">
	    <?php foreach ( $streams as $stream ) { ?>
            <div class="<?php $stream->the_classes('tp-twitch-stream'); ?>">
                <div class="tp-twitch-stream__header">
                    <a class="tp-twitch-stream__thumbnail-link" href="<?php echo $stream->get_url(); ?>" target="_blank" rel="nofollow">
                        <img class="tp-twitch-stream__thumbnail" src="<?php echo $stream->get_thumbnail_url( 480, 270 ); ?>" alt="<?php echo $stream->get_thumbnail_alt(); ?>" />
                    </a>
                </div>
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
	    <?php } ?>
    </div>
</div>