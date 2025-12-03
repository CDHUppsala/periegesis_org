jQuery(function ($) {

    /**
 * Returns the classes that start with predefiend Prefixes and a requested property,
 * @param {string} property
 * @param {Array} prefixes
 * @returns object array, with the class name as key name
 *  - and the value of the property as key value
 */
    function get_classes_with_specific_property(property, prefixes) {
        var classesWithSpecificProperty = {};

        // Iterate over all stylesheets
        $.each(document.styleSheets, function (index, styleSheet) {
            // Iterate over all rules in each stylesheet
            $.each(styleSheet.cssRules || styleSheet.rules, function (index, rule) {
                // Check if the rule is a CSS style rule (e.g., not a media rule or keyframes rule)
                if (rule instanceof CSSStyleRule) {
                    // Extract class name from selectorText
                    var selector = rule.selectorText;
                    var className = selector.replace(/^\./, ''); // Remove leading dot
                    // Check if the class starts with any of the predefined prefixes
                    $.each(prefixes, function (index, prefix) {
                        if (className.startsWith(prefix)) {
                            // Check if the rule contains the background-color property
                            if (rule.style.getPropertyValue(property)) {
                                // Get the value of the background-color property
                                var backgroundColor = rule.style.getPropertyValue(property);
                                // Store the class name and its background-color property value
                                classesWithSpecificProperty[className] = clean_rows_tabs_spaces(backgroundColor);
                            }
                        }
                    });
                }
            });
        });
        return classesWithSpecificProperty;
        //console.log(classesWithSpecificProperty);
    }

    function get_cleared_title(str) {
        var arrTitle = str.split("-");
        var title = '';
        if (arrTitle[1] == 'linear') {
            title += 'Linear: ';
        } else if (arrTitle[1] == 'ellipse') {
            title += 'Ellipse: ';
        } else if (arrTitle[1] == 'circle') {
            title += 'Circle: ';
        }

        if (arrTitle[2].startsWith('C')) {
            title += arrTitle[2].replace('C', ' Colors: ');
        }
        if (arrTitle[3].startsWith('S')) {
            title += arrTitle[3].replace('S', ', Stops: ');
        }
        if (arrTitle.length > 4) {
            if (arrTitle[4].startsWith('P')) {
                title += arrTitle[4].replace('P', ', Patterns: ');
            }
        }
        return title;
    }

    //  Gradients are loaded first when they become visible with Tab click
    function reload_gradients() {
        load_gradients();
        load_toggle_functions();
    }

    function sx_show_section(q) {
        if (q > -1) {
            setTimeout(function () {
                $('#jq_InsertGradients h3').eq(q).siblings('button').click();
            }, 300);
        }
    }

    /**
     * Selectors that not need reloading
     */

    $('input[name="GPColor"]').on('focus', function () {
        $('.tabs > a:nth-child(1)').click();
    })

    $('.tabs a:last-child').on('click', function () {
        reload_gradients();
    });

    $('.jq_show_types').on('change', function () {
        reload_gradients();
    });

    $('.jq_show_colors').on('change', function () {
        reload_gradients();
    });
    $('.jq_show_stops').on('change', function () {
        reload_gradients();
    });
    $('.jq_show_patterns').on('change', function () {
        reload_gradients();
    });

    $('#ReplaceVariables').on('click', function () {
        reload_gradients();
            sx_show_section(index_lastClickedGradientSection);
    })

    $('#ResettVariables').on('click', function () {
        $('#TintVariable').val('-1');
        $('#TintReplace').val('-1');
        delete_opacity_cokies();
        reload_gradients();
            sx_show_section(index_lastClickedGradientSection);
    })

    $('#ClearRootColors').on('click', function () {
        let bg_title = $('.bg_label').attr('title');
        $('.root_inputs label span').each(function () {
            $(this).css('background-color', '').removeAttr('data-var').removeAttr('data-var_rgb').removeAttr('title');
        })
        $('.bg_label').css('background-color', default_background_color).attr('data-var', default_background_variable).attr('title', bg_title);
        $('.bg_label input').prop('checked', true);
        if ($("#jq_InsertRootGradients").is(":visible")) {
            reload_gradients();
            sx_show_section(index_lastClickedGradientSection);
        }
    })

    $('.change_gradients_width button').on('click', function () {
        let width_percent = $(this).attr('data-id');
        $('.gradients_flex > div').animate({ 'width': width_percent }, 200)
    })

    $('.change_gradients_ratio button').on('click', function () {
        let ratio = $(this).attr('data-id');
        $('.gradients_flex .gradient_img').css({ 'aspect-ratio': ratio });
    })


    /** 
     * RELOADING FUNCTIONS
     * =================================================================================
     */

    var load_gradients = function () {
        var parentElement = $("#jq_InsertGradients");
        parentElement.html('');
        var bg_color = $('.bg_label').css('background-color');
        var basic_color_var = $('.bg_label').attr('data-var');
        var arr_prefixes = ['rrg-', 'rlg-', 'lg-', 'rg-']
        var classesByValues = get_classes_with_specific_property('background-image', arr_prefixes);

        /* SORT function
        classesByValues = sx_sort_an_object_array(classesByValues);
        */

        // Used to show the tints included inevery gradient and their replacement
        var classesByUniqueTints = sx_getUniqueTints(classesByValues);

        /**
         * FILTER
         * Create a new array with keys containing substring(s) to filter classes
         */

        var arrStrings = []; // array
        var g_type = $('.jq_show_types:checked').val();
        if (g_type != "all") {
            arrStrings.push('-' + g_type);
        }
        var g_colors = $('.jq_show_colors:checked').val();
        if (g_colors != "all") {
            arrStrings.push('-' + g_colors);
        }
        var g_stops = $(".jq_show_stops").val();
        if (g_stops != "0") {
            if (g_stops === 'X') {
                arrStrings.push(g_stops);
            } else {
                arrStrings.push('-' + g_stops);
            }
        }
        var g_patterns = $(".jq_show_patterns").val();
        if (g_patterns != "0") {
            arrStrings.push('-' + g_patterns);
        }

        if (arrStrings.length) {
            classesByValues = filterKeysByStringsArray(classesByValues, arrStrings);
        }

        /**
         * SEARCH and REPLACE functions
         * replace tints by color variables (HEX colors) by opacity:
         *  -  (--tint-0 by --tint-20)
         *  -  (--tint-0 by --basic-tint-20)
        */
        var radioReplaceTints = true;

        var radioRootcolors = false;
        var radioRGBA_colors = false;
        // Provide gradient colors as root variables, as HEX colors var(--basic-color) or as RGBA colors rgba(var(--basic_color), 1)
        var classesByValues_variables = null;
        var cur_tint = [];
        var new_tint_var = [];
        var new_rgba_var = [];
        var new_rgba_color = [];

        var $root_color_imputs = $('input[name="GPColor"]')
        $root_color_imputs.each(function () {
            var $parent = $(this).parent();
            if ($parent.attr("data-var") !== undefined && $parent.attr("class") !== 'bg_label') {
                radioRootcolors = true;
                var tint_value = $parent.attr('class');
                var tint_var = $parent.attr('data-var'); // Root color variable name: --basic-color
                var rgba_color = $parent.css('background-color');
                /**
                 * .css('background-color') give RGB when RGBA opacity is equal to 1
                 * So, check if any color contains RGBA to decide if HEX or RGBA color variables will be used 
                 *  - if True, use [new_rgba_var] for the object: classesByValues_variables, 
                 *  - else, use [new_tint_var]
                 */
                if (isRGBA_string(rgba_color)) {
                    radioRGBA_colors = true;
                }
                var rgba_var = $parent.attr('data-var_rgb'); // Root color RGB variable names: --basic_color

                cur_tint.push('var(--' + tint_value + ')');
                new_tint_var.push('var(' + tint_var + ')'); // var(--complement)
                new_rgba_var.push(rgba_var);                // rgba(var(--tetrad_2), 1)
                new_rgba_color.push(rgba_color);            // rgba(0, 136, 204, 0.9) or rgb(0, 136, 204)
            }

        })
        /*
                console.log('cur_tint',cur_tint)
                console.log('new_tint_var',new_tint_var)
                console.log('new_rgb_color',new_rgba_color)
                console.log('new_rgb_var',new_rgba_var)
        */
        if (radioRootcolors) {
            if (radioRGBA_colors) {
                classesByValues_variables = replaceSubstringInValues(classesByValues, cur_tint, new_rgba_var);
            } else {
                classesByValues_variables = replaceSubstringInValues(classesByValues, cur_tint, new_tint_var);
            }
            classesByValues = replaceSubstringInValues(classesByValues, cur_tint, new_rgba_color);
        }

        // console.log('classesByValues_variables',classesByValues_variables)
        // console.log('classesByValues',classesByValues)

        /**
         * SEARCH and REPLACE functions
         * replace tints by other tints:
         *  -  (--tint-0 by --tint-20)
        */
        cur_tint = [];
        var new_tint = [];
        if (radioReplaceTints) {
            var g_tint = $("#TintVariable").val();
            var g_new_tint = $("#TintReplace").val();
            if (g_tint >= 0 && g_new_tint >= 0) {
                set_Cookie(g_tint, g_new_tint);
            }
            for (c = 0; c < 105; c += 10) {
                if (cookieExists(c)) {
                    var cookieValue = get_Cookie(c);
                    cur_tint.push('(--tint-' + c + ')');
                    new_tint.push('(--tint-' + cookieValue + ')');
                }
            }
            if (cookieExists('05')) {
                var cookieValue = get_Cookie('05');
                cur_tint.push('(--tint-05)');
                new_tint.push('(--tint-' + cookieValue + ')');
            }
            if (cur_tint.length) {
                // To show original tints and their replacement
                classesByUniqueTints = sx_getUniqueTintReplacements(classesByUniqueTints, cur_tint, new_tint);
                // Replace remaining tints, if they have not been replaced by colors
                classesByValues = replaceSubstringInValues(classesByValues, cur_tint, new_tint);
                if (classesByValues_variables) {
                    classesByValues_variables = replaceSubstringInValues(classesByValues_variables, cur_tint, new_tint);
                }
            }
        }

        //console.log('classesByUniqueTints',classesByUniqueTints)

        // If there is no replacement of tints (by colors or other tints) the 2 object are equal 
        if ($.isEmptyObject(classesByValues_variables)) {
            classesByValues_variables = classesByValues;
        }

        // Replace remaining tint variables with white rgba colors
        classesByValues = replaceSubstringInValues(classesByValues, arr_tints, arr_rgba);

        // console.log('classesByValues_variables',classesByValues_variables)
        // console.log('classesByValues',classesByValues)

        // Get the number of keys included in the object
        //var numberOfKeys = Object.keys(classesByValues).length;

        var currentSection = null;
        var currentFlexGradients = null;
        var loopPrefix = null;
        // Iterate through the array object
        $.each(classesByValues, function (className, backgroundImage) {
            // Split the key name by the "-" character and Concatenate the first 3 or 4 parts into a string variable
            var parts = className.split("-");
            var keyPrefix = parts.slice(0, 4).join("-");

            if (keyPrefix !== loopPrefix) {
                // If so, close the current section (if exists) and open a new one
                if (currentSection) {
                    currentFlexGradients.appendTo(currentSection);
                    currentSection.appendTo(parentElement);
                }
                loopPrefix = keyPrefix;
                var title = get_cleared_title(className);

                sectionHeader = $('<div>').addClass('gradients_header').appendTo(parentElement);
                $('<h3>').addClass('jq_toggle_next').text(title).appendTo(sectionHeader);
                $('<button>').addClass('jq_toggle_rest').text('Close Others').appendTo(sectionHeader);
                sectionHeader.appendTo(parentElement);

                currentSection = $('<div>').addClass('gradients_section').appendTo(parentElement); // Create and append a new section
                currentFlexGradients = $('<div>').addClass('gradients_flex').appendTo(currentSection);
            }

            var variable_values = classesByValues_variables[className]
            var uniqueTints = classesByUniqueTints[className];

            imgDiv = $('<div>').addClass('gradient_item').appendTo(currentFlexGradients);
            $('<div>').addClass('gradient_img').css({ 'background-color': bg_color, 'background-image': backgroundImage }).appendTo(imgDiv);
            $('<label>').attr('title', 'Select RGB and RGBA colors for general use').html('<input type="radio" name="RootColors" value="' + bg_color + '" /> Background Color: <b>' + bg_color + '</b>').appendTo(imgDiv);
            $('<label>').attr('title', 'Select Color Variables for Templates in Public Sphere applications. The same Templates can then be used with new Color Schemes').html('<input type="radio" name="RootColors" value="' + basic_color_var + '" /> Background Color Variable: <b>' + basic_color_var + '</b>').appendTo(imgDiv);

            if (radio_UseGradientClasses) {
                var collorsAdded = '';
                if (radioRootcolors) {
                    collorsAdded = ' + Colors';
                }
                $('<label>').attr('title', 'Unique CSS Class Name that can be used as Template Name').html('<input type="radio" name="RootColors" value="' + className + '" /> Template: <b>' + className + collorsAdded + '</b>').appendTo(imgDiv);
            }

            $('<label>').attr('title', variable_values).html('<input type="radio" name="RootColors" value="' + variable_values + '" /> Gradient by Color Variables: [' + uniqueTints + ']').appendTo(imgDiv);

            var rgbType = 'Gradient by White RGBA Colors';
            if (radioRootcolors) {
                rgbType = 'Gradient by RGBA Colors';
            }
            $('<label>').attr('title', backgroundImage).html('<input type="radio" name="RootColors" value="' + backgroundImage + '" /> ' + rgbType).appendTo(imgDiv);
            imgDiv.appendTo(currentFlexGradients);
        });

        // Append the last section (if exists) to the parent element
        if (currentSection) {
            currentFlexGradients.appendTo(currentSection);
            currentSection.appendTo(parentElement);
        }
    }

    /**
     * Selectors that need reloading
     */

    var load_toggle_functions = function () {
        $('.jq_toggle_next').on('click', function () {
            if ($body_scroll_animation === null || $body_scroll_animation.is('html') === false) {
                $body_scroll_animation = $body_highest_element.parent();
            }
            var $sxThis = $(this);
            index_lastClickedGradientSection = $sxThis.index('#jq_InsertGradients h3');
            var $sxHeaderHeight = $sxThis.parent().height();
            var wrapper_hight = $("#jq_gradient_inputs_wrapper").height();
            $sxThis.toggleClass('selected').parent().next('.gradients_section').slideToggle(300, function () {
                if ($sxThis.hasClass('selected')) {
                    var thisTop = $(this).offset().top;
                    var parent_top = $body_highest_element.offset().top;
                    $body_scroll_animation.animate({
                        scrollTop: ((thisTop - wrapper_hight - $sxHeaderHeight) + (-1 * parent_top))
                    }, 300);
                    /*
                    console.log('$sxHeaderHeight', $sxHeaderHeight)
                    console.log('wrapper_hight', wrapper_hight)
                    console.log('thisTop', thisTop)
                    console.log('parent_top', parent_top)
                    console.log('total', ((thisTop - wrapper_hight - $sxHeaderHeight) + (-1 * parent_top)))
                    */
                }
            });
        })

        $('.jq_toggle_rest').on('click', function () {
            if ($body_scroll_animation === null || $body_scroll_animation.is('html') === false) {
                $body_scroll_animation = $body_highest_element.parent();
            }

            index_lastClickedGradientSection = $(this).siblings('h3').index('#jq_InsertGradients h3');

            let $sxThis = $(this).parent();
            let $sxHeaderHeight = $sxThis.height();
            let wrapper_hight = $("#jq_gradient_inputs_wrapper").height();
            $sxThis.parent('#jq_InsertGradients').find('h3').removeClass('selected');
            $sxThis
                .find('h3').addClass('selected')
                .end()
                .parent().find('.gradients_section').slideUp(200)
                .end().end()
                .next('.gradients_section').slideDown(200, function () {
                    let thisTop = $(this).offset().top;
                    let parent_top = $body_highest_element.offset().top;
                    $body_scroll_animation.animate({
                        scrollTop: ((thisTop - wrapper_hight - $sxHeaderHeight) + (-1 * parent_top))
                    }, 300);
                });
        })

        $('#jq_InsertRootGradients input[type="radio"][name="RootColors"]').click(function (event) {
            event.stopPropagation();
            let selectedValue = $(this).val();
            if (selectedValue !== 'Unchecked') {
                navigator.clipboard.writeText(selectedValue).then(function () {
                    alert('Value copied to clipboard: ' + selectedValue);
                }, function (err) {
                    alert('Unable to copy value to clipboard: ', err);
                });
            }
        });
    }

});
