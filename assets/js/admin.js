jQuery(document).ready(function ($) {

    /**
     * Settings: Delete cache
     */
    jQuery( document ).on( 'click', '#tp_twitch_delete_cache_submit', function(event) {
        jQuery('#tp_twitch_delete_cache').val('1');
    });

    /**
     * Widgets
     */
    $('[data-tp-twitch-widget-config-streamer-input]').keyup(function() {

        var value = $(this).val();
        var searchBlock = $(this).parents('.widget-content').find('.tp-twitch-widget-config-search-block');

        if ( value ) {
            searchBlock.hide();
        } else {
            searchBlock.show();
        }
    });

});


