jQuery(document).ready(function(a) {
    jQuery(document).on("click", "#tp_twitch_delete_cache_submit", function(a) {
        jQuery("#tp_twitch_delete_cache").val("1");
    }), jQuery(document).on("click", "#tp-twitch-data-toggle", function(a) {
        jQuery("#tp-twitch-data-container").toggle();
    }), jQuery(document).on("click", "#tp-twitch-delete-log-submit", function(a) {
        jQuery("#tp-twitch-delete-log").val("1");
    }), a("[data-tp-twitch-widget-config-streamer-input]").keyup(function() {
        var b = a(this).val(), c = a(this).parents(".widget-content").find(".tp-twitch-widget-config-search-block");
        b ? c.hide() : c.show();
    });
});