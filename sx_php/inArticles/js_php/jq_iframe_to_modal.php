<script>
    jQuery(function($) {
        sx_iFrameToModalWindow($, int_radom);

    });

    var sx_iFrameToModalWindow = function($, int_radom) {
        var $iframe_container = $('#iframe_container_'+ int_radom);
        var $iframe_modal_button = $('#iframe_modal_button_'+ int_radom);

        $iframe_modal_button.on('click', openModal);

        function openModal() {
            // Create modal elements
            var $iframeModalOverlay = $('<div class="iframe_modal-overlay"></div>');
            var $iframeModalContent = $('<div class="iframe_modal-content"></div>');
            var $iframeCloseButton = $('<span class="iframe_close-button" title="Close Modal Window">X</span>');

            $iframeModalContent.append($iframeCloseButton);
            $iframeModalOverlay.append($iframeModalContent);
            $('body').append($iframeModalOverlay);

            // Move the iframe into the modal content when opening
            $iframeModalContent.append($iframe_container.children());
            // When the iframe is reloaded
            $iframeModalContent.find('.loading_message').css({
                'z-index': 1
            });

            // Show the modal
            $iframeModalOverlay.show(300);
            $('html,body').css('overflow', 'hidden');
            setTimeout(function() {
                $iframeModalContent.find('.loading_message').css({
                    'z-index': -1
                });
            }, 3000);

            // Attach event listener to close button
            $iframeCloseButton.on('click', function() {
                $iframeCloseButton.remove();
                $('html,body').css('overflow', 'auto');
                // Move the iframe back to its original container when closing
                $iframe_container.append($iframeModalContent.children());
                // Remove the modalOverlay and its content
                $iframeModalOverlay.remove();
            });

        }
    }
</script>