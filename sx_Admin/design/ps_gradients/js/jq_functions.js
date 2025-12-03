
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

jQuery(function ($) {
    $body_highest_element = $('#jq_root_color_gradients');
    $body_scroll_animation = $('html, body');

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
            $('html, body').animate({
                scrollTop: 0
            }, 200)
        } else {
            $('.root_color_wrapper').slideUp(300);
            $('html, body').animate({
                scrollTop: 0
            }, 300, function () {
                $('.root_color_wrapper').eq(0).slideDown(300);
                $('.root_color_wrapper').eq(1).slideDown(300);
            });
        }
    })

    $('.jg_scroll_top').on('click', function () {
        $('html, body').animate({
            scrollTop: 0
        }, 300);
    })

    var inputs_height = $("#jq_color_inputs").height();
    var nav_root_top = $("#jq_color_inputs").offset().top;
    var scrollColorsTop = false;
    var scrollGradientsTop = false;

    $(window).scroll(function () {

        if ($("#jq_InsertRootColor").is(":visible")) {
            $("#jq_gradient_inputs").removeClass('jq_sticky');
            $("#jq_gradient_inputs_wrapper").css("height", 'auto');


            if (scrollColorsTop == false && $(this).scrollTop() >= nav_root_top) {
                $("#jq_color_inputs_wrapper").css("height", inputs_height);
                $("#jq_color_inputs").addClass('jq_sticky');
                scrollColorsTop = true;

            } else if (scrollColorsTop == true && $(this).scrollTop() < nav_root_top) {
                $("#jq_color_inputs").removeClass('jq_sticky');
                $("#jq_color_inputs_wrapper").css("height", 'auto');
                scrollColorsTop = false;
            }
        } else if ($("#jq_InsertRootGradients").is(":visible")) {
            $("#jq_color_inputs").removeClass('jq_sticky');
            $("#jq_color_inputs_wrapper").css("height", 'auto');

            var $nav_gradients_Top = $("#jq_gradient_inputs_wrapper").offset().top;

            if (scrollGradientsTop == false && $(this).scrollTop() >= $nav_gradients_Top) {
                $("#jq_gradient_inputs_wrapper").css("height", $("#jq_gradient_inputs_wrapper").height());
                $("#jq_gradient_inputs").addClass('jq_sticky');

                scrollGradientsTop = true;
            } else if (scrollGradientsTop == true && $(this).scrollTop() < $nav_gradients_Top) {
                $("#jq_gradient_inputs").removeClass('jq_sticky');
                $("#jq_gradient_inputs_wrapper").css("height", 'auto');
                scrollGradientsTop = false;
            }
        }
    });

});

