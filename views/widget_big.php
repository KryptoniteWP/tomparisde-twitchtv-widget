<div class="tw">

    <?php foreach ($channels_data as $channel) { ?>

        <div class="tp-tw-big-box-in-wrapper<?php echo $style; ?>">

            <!-- LIVE VIEW OR PICTURE -- TOP -->

            <?php if ($banner_postion == 'top') { ?>
                <div class="tp-tw-big-box-username" style="margin-top: -5px; margin-bottom: 5px">
                    <a target="_blank"
                       href="https://www.twitch.tv/<?php echo $channel['username']; ?>/"> <?php echo $channel['display_name']; ?>

                        <?php if ($twitch_partner == '1') { ''; }
                        else { ?>

                            <?php if ($channel['twitch_partner'] == 'true')
                            { ?>
                                <img style="margin-top: -3px; vertical-align: middle;" src="<?php tp_ttvw_the_assets(); ?>img/verified.png"/>
                            <?php }
                            else { echo ''; };
                            ?>
                        <?php } ?>

                    </a>
                </div>

                <div>
                    <a target="_blank" href="https://www.twitch.tv/<?php echo $channel['username']; ?>/">
                        <?php if ($live_or_picture == 'twitch_picture') { ?>
                            <img class="tp-tw-big-box-live-pic" src="<?php echo $channel['preview_large'] ?>"/>
                        <?php } else { ?>

                            <iframe
                                src="https://player.twitch.tv/?channel=<?php echo $channel['display_name'] ?>&muted=true"
                                height="<?php echo $live_or_picture_height ?>"
                                width="<?php echo $live_or_picture_width ?>"
                                frameborder="0"
                                scrolling="no"
                                allowfullscreen="true">
                            </iframe>

                        <?php } ?>
                    </a>
                </div>
            <?php } ?>

            <!-- LIVE VIEW OR PICTURE -- END -->

            <!-- LIVE VIEW OR PICTURE -- MIDDLE -->

            <?php if ($banner_postion == 'middle') { ?>
                <div>
                    <a target="_blank" href="https://www.twitch.tv/<?php echo $channel['username']; ?>/">
                        <?php if ($live_or_picture == 'twitch_picture') { ?>
                            <img class="tp-tw-big-box-live-pic" src="<?php echo $channel['preview_large'] ?>"/>
                        <?php } else { ?>

                            <iframe
                                src="https://player.twitch.tv/?channel=<?php echo $channel['display_name'] ?>&muted=true"
                                height="<?php echo $live_or_picture_height ?>"
                                width="<?php echo $live_or_picture_width ?>"
                                frameborder="0"
                                scrolling="no"
                                allowfullscreen="true">
                            </iframe>

                        <?php } ?>
                    </a>
                </div>

                <div class="tp-tw-big-box-username" style="margin-top: -10px;">
                    <a target="_blank"
                       href="https://www.twitch.tv/<?php echo $channel['username']; ?>/"> <?php echo $channel['display_name']; ?>


                        <?php if ($twitch_partner == '1') { ''; }
                        else { ?>

                            <?php if ($channel['twitch_partner'] == 'true')
                            { ?>
                                <img style="margin-top: -3px; vertical-align: middle;" src="<?php tp_ttvw_the_assets(); ?>img/verified.png"/>
                            <?php }
                            else { echo ''; };
                            ?>
                        <?php } ?>


                    </a>
                </div>
            <?php } ?>

            <!-- LIVE VIEW OR PICTURE -- END -->

            <?php if ($show_aktiv_game == '1') { ''; }
            else { ?>
                <div class="tp-tw-big-box-is-playing-wrapper" style="margin-top: 14px;">
                    <div class="tp-tw-big-box-is-playing-fix"><?php _e( 'Currently playing:', 'tp-tw' ); ?></div>
                    <div class="tp-tw-big-box-is-playing"><?php echo $channel['aktiv_game']; ?></div>
                </div>
            <?php } ?>

            <?php if ($hide_title == '1') { ''; }
            else { ?>
                <div class="tp-tw-big-box-title-wrapper">
                    <div class="tp-tw-big-box-title-fix"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>><?php _e( 'Title:', 'tp-tw' ); ?></div>
                    <div class="tp-tw-big-box-title"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>><?php echo $channel['channel_title']; ?></div>
                </div>
            <?php } ?>

            <?php if ($hide_stats == '1') { ''; }
            else { ?>
                <div class="tp-tw-big-box-stats">
                <span class="tp-tw-big-box-stats-item"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>><img
                        src="<?php tp_ttvw_the_assets(); ?>img/live.png"/> <?php echo $channel['viewers']; ?></span>
                <span class="tp-tw-big-box-stats-item"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>><img
                        src="<?php tp_ttvw_the_assets(); ?>img/subs.png"/> <?php echo $channel['followers']; ?></span>
                <span class="tp-tw-big-box-stats-item"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>><img
                        src="<?php tp_ttvw_the_assets(); ?>img/views.png"/> <?php echo $channel['views']; ?></span>
                </div>
            <?php } ?>

        </div>

    <?php } ?>

</div>