jQuery(function ($) {
    var $modal_container = $('#jqLoadArchivesLayer');
    var $modal_open_button = $('#jq_modal_open_button');
    $modal_open_button.on('click', function () {
        $(this).hide();

        var $elementModalOverlay = $('<div id="jq_modal_overlay"></div>');
        var $elementCloseButton = $('<span id="jq_modal_close_button" title="Close Modal Window">X</span>');
        $elementModalOverlay.append($elementCloseButton);
        $elementModalOverlay.append($modal_container.children());

        $('body').append($elementModalOverlay);

        $elementModalOverlay.show(300);
        $('html,body').css('overflow', 'hidden');

        // Attach event listener to close button
        $elementCloseButton.on('click', function () {
            $elementCloseButton.remove();
            $modal_open_button.show();
            $('html,body').css('overflow', 'auto');
            $modal_container.append($elementModalOverlay.children());
            $elementModalOverlay.remove();
        });

    })
});

