jQuery(document).ready(function ($) {

    /**
     * Settings: Delete cache
     */
    $(document).on( 'click', '#tp_twitch_delete_cache_submit', function(event) {
        $('#tp_twitch_delete_cache').val('1');
    });

    /**
     * Settings: Toggle Data Container
     */
    $(document).on( 'click', '#tp-twitch-data-games-toggle', function(event) {
        $('#tp-twitch-data-games-container').toggle();
    });

    $(document).on( 'click', '#tp-twitch-data-languages-toggle', function(event) {
        $('#tp-twitch-data-languages-container').toggle();
    });

    /**
     * Settings: Delete Log
     */
    $(document).on( 'click', '#tp-twitch-delete-log-submit', function(event) {
        $('#tp-twitch-delete-log').val('1');
    });

    /**
     * Widgets
     */
    $('[data-tp-twitch-widget-config-streamer-input]').on( 'keyup', function() {

        var value = $(this).val();
        var searchBlock = $(this).parents('.widget-content').find('.tp-twitch-widget-config-search-block');

        if ( value ) {
            searchBlock.hide();
        } else {
            searchBlock.show();
        }
    });

});