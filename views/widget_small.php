<div class="tw">

    <?php foreach ($channels_data as $channel) { ?>

        <div class="tp-tw-small-box-in-wrapper tp-tw-clearfix<?php echo $style; ?>">

            <div>
                <a target="_blank" href="https://www.twitch.tv/<?php echo $channel['username']; ?>/">
                    <img class="tp-tw-small-box-live-pic" src="<?php echo $channel['image'] ?>"/>
                </a>
            </div>

            <div class="tp-tw-small-box-name">
                <a target="_blank" href="https://www.twitch.tv/<?php echo $channel['username'] ?>/">
                    <?php echo $channel['display_name']; ?>


                    <?php if ($twitch_partner == '1') { ''; }
                    else { ?>

                        <?php if ($channel['twitch_partner'] == 'true')
                        { ?>
                            <img style="margin-top: -3px; vertical-align: middle;" src="<?php tp_ttvw_the_assets(); ?>img/verified-small.png"/>
                        <?php }
                        else { echo ''; };
                        ?>
                    <?php } ?>


                </a>
            </div>

            <?php if ($channel['live']) { ?>
                <div class="tp-tw-small-box-viewers"<?php if (!empty($font_color)) echo ' style="color:' . $font_color . ';"'; ?>>
                    <span class="tp-tw-punkt-small-box-online"></span> <?php _e( 'Viewer:', 'tp-tw' ); ?> <?php echo $channel['viewers']; ?>
                </div>
            <?php } else { ?>

                <div class="tp-tw-small-box-viewers">
                    <span class="tp-tw-punkt-small-box-offline"></span> Offline :'(
                </div>

            <?php } ?>

        </div>

    <?php } ?>

</div>