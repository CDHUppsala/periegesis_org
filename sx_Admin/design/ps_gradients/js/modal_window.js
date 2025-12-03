jQuery(function ($) {
    var $modal_container = $('#jqLoadArchivesLayer');
    var $modal_open_button = $('#jq_modal_open_button');
    $modal_open_button.on('click', function () {

        var $check_opened_modal = $('#jq_modal_overlay');
        if ($check_opened_modal.length) {
            $modal_container.append($check_opened_modal.children());
            $('.root_color').addClass('flex_20')
            $check_opened_modal.hide(300, function() {
                design_contentLoadedActions(false);
            $('html,body').css('overflow', 'auto');
            $check_opened_modal.remove();
            });

        } else {
            var $elementModalOverlay = $('<div id="jq_modal_overlay"></div>');
            $elementModalOverlay.append($modal_container.children());

            $('body').append($elementModalOverlay);
            $('.root_color').removeClass('flex_20')

            $elementModalOverlay.show(300, function() {
                design_contentLoadedActions(true);
            });
            $('html,body').css('overflow', 'hidden');
        }

    })
});

