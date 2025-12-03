
/**
 * Include here all jQuery functions that use selectors that load with First load of the page
 * Do not include here functions with selectors that are dynamically reloaded by other jQuery functions
 */

/**
 * The following 2 variables make it possible to use all 
 *  scripts (except this one) to both versions of the application:
 *  - as separate application (index.php)
 *  - and within the adminstration program (ajax_index.php) called from 
 *    administration
 * Define as global the top scrolling element ($body_scroll_animation): 
 *  - $('html,body') for the separate application, or
 *  - the highest DOM element that is loaded by AJAX and 
 *      scrolls within a fixed DIV, for use in administration
 *      REDEFINE it in every call to account for the new fiexed DIV of modal window
 */

var $body_highest_element = null;
var $body_scroll_animation = null;

// Check this variable - if it is used externally
var $gradien_flex_element = null;

// Global, for defining the width of colors (not used in HTML version?)
radio_ajax_loaded_page = true;

jQuery(function ($) {
    $body_highest_element = $('#jq_root_color_gradients');
    $body_scroll_animation = $body_highest_element.parent();

    $(".tabs").off("click").on("click", "a", function () {
        var sxThis = $(this);
        if (sxThis.attr("class") == null || sxThis.attr("class") == '') {
            sxThis.addClass("selected")
                .siblings().removeClass("selected")
                .end()
                .parent()
                .siblings().hide(300)
                .siblings("#" + sxThis.data("id")).show(300, function () {
                    if ($("#jq_InsertRootGradients").is(":visible") && index_lastClickedGradientSection > -1) {
                        $('#jq_InsertGradients h3').eq(index_lastClickedGradientSection).click();
                    }
                });
        }
    });

    $('.jg_hide_all').on('click', function () {
        if ($("#jq_InsertRootGradients").is(":visible")) {
            index_lastClickedGradientSection = -1;
            $('.gradients_section').slideUp(200);
            $body_scroll_animation.animate({
                scrollTop: 0
            }, 200)
        } else {
            $('.root_color_wrapper').slideUp(300);
            $body_scroll_animation.animate({
                scrollTop: 0
            }, 300, function () {
                $('.root_color_wrapper').eq(0).slideDown(300);
                //$('.root_color_wrapper').eq(1).slideDown(300);
            });
        }
    });

    $('.jg_scroll_top').on('click', function () {
        $body_scroll_animation.animate({
            scrollTop: 0
        }, 300)
    })

    // In Public Sphere administration, open this programe in wider screen
    $('#jq_width').click();

});

// Is called when ajax is loaded
function design_contentLoadedActions(radio_overlay = false) {

    var scrollColorsTop = false;
    var scrollGradientsTop = false;
    var $color_gradients_width = '';

    function getChildScrollTop() {

        $body_highest_element = $sx_aj('#jq_root_color_gradients');
        $body_margin = $body_highest_element.offset().top;
        $header_height = $sx_aj('#header').height();
        if (radio_overlay) {
            $color_gradients_width = (($body_highest_element.width() / ($sx_aj('body').width())) * 100) + '%';
            $body_margin = 0;
            $header_height = 0;
        } else {
            $color_gradients_width = '';
        }

        //console.log('$body_margin: ' + $body_margin + ' $header_height: ' + $header_height);

        var $color_inputs = null;
        var color_inputs_top = 0;
        var color_inputs_height = 0;
        var $color_inputs_wrapper = null;

        if ($sx_aj("#jq_color_inputs").length) {
            $color_inputs = $sx_aj("#jq_color_inputs");
            $color_inputs_wrapper = $sx_aj("#jq_color_inputs_wrapper");
        }

        var $gradient_inputs = null;
        var $gradient_inputs_Top = 0;
        var $gradient_inputs_height = 0;
        var $gradient_inputs_wrapper = null;

        if ($sx_aj("#jq_gradient_inputs").length) {
            $gradient_inputs = $sx_aj("#jq_gradient_inputs");
            $gradient_inputs_wrapper = $sx_aj("#jq_gradient_inputs_wrapper");
        }

        $body_highest_element.parent().scroll(function () {
            if (radio_overlay === false && $color_gradients_width === '') {
                $color_gradients_width = (($body_highest_element.width() / ($sx_aj('body').width())) * 100) + '%';
            }

            if ($sx_aj("#jq_InsertRootColor").is(":visible")) {
                // Restore gradient inputs
                if ($gradient_inputs.hasClass('jq_sticky')) {
                    $gradient_inputs.removeClass('jq_sticky').css({ 'width': 'auto', 'top': '0' });
                    $gradient_inputs_wrapper.css("height", 'auto');
                }
                if (color_inputs_top === 0) {
                    color_inputs_top = $color_inputs.offset().top - $header_height + 1;
                    color_inputs_height = $color_inputs.height();
                }

                //console.log('color_inputs_top: ' + color_inputs_top + ' $color_inputs.offset().top: ' + $color_inputs.offset().top + ', This scrollTop: ' + $sx_aj(this).scrollTop());

                if (scrollColorsTop == false && $sx_aj(this).scrollTop() >= color_inputs_top) {
                    $color_inputs_wrapper.css("height", color_inputs_height);
                    $color_inputs.addClass('jq_sticky').css({ 'width': $color_gradients_width, 'top': $header_height });
                    scrollColorsTop = true;

                } else if (scrollColorsTop == true && $sx_aj(this).scrollTop() < color_inputs_top) {
                    $color_inputs.removeClass('jq_sticky').css({ 'width': 'auto', 'top': '0' });
                    $color_inputs_wrapper.css("height", 'auto');
                    scrollColorsTop = false;
                }
            } else if ($sx_aj("#jq_InsertRootGradients").is(":visible")) {
                // Restore color inputs
                if ($color_inputs.hasClass('jq_sticky')) {
                    $color_inputs.removeClass('jq_sticky').css('width', 'auto');;
                    $color_inputs_wrapper.css("height", 'auto');
                }

                // Get the height and position of gradients here, as they are hidden with load
                if ($gradient_inputs_Top === 0) {
                    $gradient_inputs_Top = $gradient_inputs.offset().top - $header_height + 1;
                    $gradient_inputs_height = $gradient_inputs.height();
                }

                //console.log('$gradient_inputs_height: ' + $gradient_inputs_height + ', $gradient_inputs_Top: ' + $gradient_inputs_Top + ' / ' + $gradient_inputs.offset().top + ', This scrollTop: ' + $sx_aj(this).scrollTop());

                if (scrollGradientsTop == false && $sx_aj(this).scrollTop() >= $gradient_inputs_Top) {
                    $gradient_inputs_wrapper.css("height", $gradient_inputs_height);
                    $gradient_inputs.addClass('jq_sticky').css({ 'width': $color_gradients_width, 'top': $header_height });
                    scrollGradientsTop = true;
                } else if (scrollGradientsTop == true && $sx_aj(this).scrollTop() < $gradient_inputs_Top) {
                    $gradient_inputs.removeClass('jq_sticky').css({ 'width': 'auto', 'top': '0' });
                    $gradient_inputs_wrapper.css("height", 'auto');
                    scrollGradientsTop = false;
                }
            }
        });
    }

    getChildScrollTop();

};

