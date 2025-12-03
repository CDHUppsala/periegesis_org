jQuery(function ($) {
    var $modal_container = $('#jqLoadArchivesLayer');
    var $modal_open_button = $('#jq_modal_open_button');
    var $modal_screen_img = $modal_open_button.find('img');
    var $modal_open_img = $modal_screen_img.attr('src');
    var $modal_close_img = $modal_screen_img.data('close');

    $modal_open_button.on('click', function () {

        var $check_modal_overlay = $('#jq_modal_overlay');
        if ($check_modal_overlay.length) {
            radio_modal_overlay = false;
            $modal_screen_img.attr('src', $modal_open_img);
            $modal_container.append($check_modal_overlay.children());
            $('.root_color').addClass('flex_20')
            $check_modal_overlay.hide(300, function () {
                design_contentLoadedActions(false);
                $('html,body').css('overflow', 'auto');
                $check_modal_overlay.remove();
            });

        } else {
            $modal_screen_img.attr('src', $modal_close_img);
            var $elementModalOverlay = $('<div id="jq_modal_overlay"></div>');
            $elementModalOverlay.append($modal_container.children());

            radio_modal_overlay = true;

            $('body').append($elementModalOverlay);
            $('.root_color').removeClass('flex_20')

            $elementModalOverlay.show(300, function () {
                design_contentLoadedActions(true);
            });
            $('html,body').css('overflow', 'hidden');
        }

    })
});

