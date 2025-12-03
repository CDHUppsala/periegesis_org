
/**
 * Clear all cookies when loading the page
 */
delete_opacity_cokies()

jQuery(function ($) {

    /**
     * Get the CSS Root Color Variables as an object
     * Use the first function if the stylesheet with variables
     *  is the first stylesheet file in your application
     *  Otherwise, use the second function
     */

    var root_variables = sx_getRootColorVariables();
    //var root_variables = get_all_styleSheets_variables();
    var insertRootColor = $("#jq_InsertRootColor");
    var last_type = "";
    var last_key = "";
    var append_string = '';
    //var const_basic_color = "";
    //var const_basic_color_var = "";
    var basic_variants = [
        "--lighten-shade", "--brighten-shade",
        "--darken-shade", "--desaturate-shade",
        "--monochrom-1-shade", "--monochrom-2-shade",
        "--monochrom-3-shade", "--monochrom-4-shade", "--monochrom-5-shade",
        "--analog-1-shade", "--analog-2-shade",
        "--analog-3-shade", "--analog-4-shade"
    ];

    for (var key in root_variables) {
        var i_pos = key.lastIndexOf("-");
        var curr_key = key.substring(0, i_pos);
        var opacity_style = "";
        var curr_type = '';
        var opacity_class = "";
        var title_message = "";
        var value = root_variables[key];
        if (key.indexOf("--basic-color") > -1) {
            curr_type = 'Basic Color';
            //const_basic_color = value;
            //const_basic_color_var = key;
            $('.bg_label').css({
                'background-color': value
            })
            $('.bg_label').attr('data-var', key);
            default_background_color = value;
            default_background_variable = key;
        } else if (key.indexOf("--basic-") > -1) {
            curr_type = 'Basic color Variants';
        } else if (key.indexOf("--lighten") > -1
            || key.indexOf("--darken") > -1
            || key.indexOf("--desaturate") > -1
            || key.indexOf("--brighten") > -1) {
            curr_type = 'Basic color Variants by 20%';
            opacity_class = ' root_variants';
        } else if (key.indexOf("--monochrom") > -1) {
            curr_type = 'Monochromatic Variants';
            opacity_class = ' root_variants';
        } else if (key.indexOf("--analog") > -1) {
            curr_type = 'Analog Variants';
            opacity_class = ' root_variants';
        } else if (key.indexOf("--triad") > -1) {
            curr_type = 'Triad Colors';
        } else if (key.indexOf("--complement") > -1) {
            curr_type = 'Complement';
        } else if (key.indexOf("--tetrad") > -1) {
            curr_type = 'Tetrad Colors';
        } else if (key.indexOf("--split") > -1) {
            curr_type = 'Split Colors';
        } else if (key.indexOf("--tint") > -1
            || key.indexOf("--shade") > -1
            || key.indexOf("--tone") > -1) {
            curr_type = 'Tints, Shades and Tones';
            opacity_style = 'background: var(--basic-color);';
            opacity_style += 'border: 1px solid var(--basic-tone-80)';
            opacity_style = ' style="' + opacity_style + '"';
            opacity_class = ' root_opacity';
            title_message = ' title="Opacities of White, Black and Grey applied, as example, on Basic Color."';
        } else if (key.indexOf("--grey") > -1) {
            curr_type = 'Gray Conversions';
        } else {
            curr_type = 'Undefined Color';
        }

        if (last_type != curr_type) {
            if (last_type != "") {
                append_string += '</div></div>';
            }
            append_string += '<h3' + title_message + '>' + curr_type + '</h3>';
            append_string += '<div class="root_color_wrapper">';
            append_string += '<div class="root_flex">';
        } else if (last_key != curr_key && basic_variants.includes(curr_key) === false) {
            append_string += '</div>';
            append_string += '<div class="root_flex">';
        }

        var rgb_variable = sx_get_RGB_variable(key);
        append_string += '<div class="root_color' + opacity_class + '">';
        append_string += '<div' + opacity_style + '>';
        append_string += '<div class="root_color_box" data-var="' + key + '" data-var_rgb="' + rgb_variable + '" style="background-color: ' + value + ';"> </div>';
        append_string += '</div>';
        append_string += '<div class="root_color_inputs">' +
            '<input type="radio" name="RootColors" value="' + key + '" /> <span>' + key + '</span>' +
            '<input type="radio" name="RootColors" value="' + value + '" /> <span>' + value + '</span></div>';
        append_string += '</div>';

        if (curr_type === 'Basic Color') {
            append_string += '<div class="root_color">';
            append_string += '<div>';
            append_string += '<div class="root_color_box" data-var="--tint-100" data-var_rgb="--tint_100" style="background-color: rgba(255, 255, 255, 1);"> </div>';
            append_string += '</div>';
            append_string += '<div class="root_color_inputs">' +
                '<input type="radio" name="RootColors" value="--tint-100" /> <span>--tint-100</span>' +
                '<input type="radio" name="RootColors" value="#ffffff" /> <span>#ffffff</span></div>';
            append_string += '</div>';
            append_string += '<div class="root_color">';
            append_string += '<div>';
            append_string += '<div class="root_color_box" data-var="--shade-100" data-var_rgb="--shade_100" style="background-color: rgba(0, 0, 0, 1);"> </div>';
            append_string += '</div>';
            append_string += '<div class="root_color_inputs">' +
                '<input type="radio" name="RootColors" value="--shade-100" /> <span>--shade-100</span>' +
                '<input type="radio" name="RootColors" value="#000000" /> <span>#000000</span></div>';
            append_string += '</div>';

        }

        last_type = curr_type;
        last_key = curr_key;
    }
    append_string += '</div></div>';
    insertRootColor.append(append_string);


    $('#jq_InsertRootColor input[type="radio"][name="RootColors"]').click(function (event) {
        event.stopPropagation();
        var selectedValue = $(this).val();
        if (selectedValue !== 'Unchecked') {
            navigator.clipboard.writeText(selectedValue).then(function () {
                alert('Value copied to clipboard: ' + selectedValue);
            }, function (err) {
                alert('Unable to copy value to clipboard: ', err);
            });
        }
    });


    /**
     * ===========================================================
     * CALL FUNCTIONS
     * ===========================================================
     */

    $('.root_insert_colors h3').on('click', function () {
        $(this).toggleClass('selected')
            .next('div').slideToggle(300);
    });

    $('#jq_InsertRootColor h3:nth-of-type(1)').click();
    $('#jq_InsertRootColor h3:nth-of-type(2)').click();

    /**
     * Replace tints (opacities of white) 
     *  - with colors (with or whithout opacity), or
     *  - with variables defining rgb colors (with or whithout opacity)
     */
    $('.root_color_box').on('click', function () {
        var selected_color = $(this).css('background-color');
        var selected_var = $(this).attr("data-var"); // ---basic-color
        var selected_rgb_var = 'var(' + $(this).attr("data-var_rgb") + ')';

        //alert(selected_color + ' ' + selected_var + ' ' + selected_rgb_var)

        var apply_to_input = $('input[name="GPColor"]:checked');
        var input_value = null;
        if (apply_to_input.length) {
            input_value = apply_to_input.val();

            /**
             * The color will not contain opacity if aplied to background color of gradients
             * All other tint replacements wil be defined in rgba color with opacity 1 as default
             */
            if (input_value == 'bg') {
                $('.bg_label').css({
                    'background-color': selected_color
                }).attr('data-var', selected_var);
            } else {
                var color_opacity = $('#opacity').val();
                if(selected_var.indexOf('--tint') === 0 || selected_var.indexOf('--shade') === 0 || selected_var.indexOf('--tone') === 0) {
                    // Get the opacity of the selecte tint/shade/tone colors
                        var tempArr = selected_rgb_var.split('_');
                        color_opacity = parseFloat(tempArr[tempArr.length - 1]) / 100;
                    if (selected_rgb_var.indexOf('var(--tint') === 0) {
                        selected_rgb_var = '255, 255, 255';
                    } else if (selected_rgb_var.indexOf('var(--shade') === 0) {
                        selected_rgb_var = '0, 0, 0';
                    } else if (selected_rgb_var.indexOf('var(--tone') === 0) {
                        selected_rgb_var = '128, 128, 128'
                    }

                }else {
                    selected_color = selected_color.replace(')', ', ' + color_opacity + ')');
                    selected_color = selected_color.replace('rgb(', 'rgba(');
                }

                selected_rgb_var = 'rgba(' + selected_rgb_var + ', ' + color_opacity + ')';
                apply_to_input.parent().css({
                    'background-color': selected_color
                }).attr('data-var', selected_var).attr('data-var_rgb', selected_rgb_var).attr('title', 'Selected color variable: ' + selected_var);
                //alert(selected_color + ' ' + selected_var + ' ' + selected_rgb_var)

            }
        }
    })
    $('.root_color').addClass('flex_20')

});

/**
 * ===========================================
 * Functions used by the above jQuery instance
 * ===========================================
 */


function sx_get_RGB_variable(str) {
    str = str.replace('--', '##');
    if (str.indexOf('-') > -1) {
        str = str.replace('-', '_');
    } else {
        str = str + '_00';
    }
    return str.replace('##', '--');
}


/**
 * The :root must be in the first Stylsheet with the first cssRules[0]
 * @returns An object with all css variables 
 *  starting with '--' without '_' in their name
 */
function sx_getRootColorVariables() {
    var cssVariables = [];
    var css_root = document.styleSheets[0].cssRules[0];
    if (css_root.style && css_root.style.length) {
        // Iterate over all properties in the rule
        for (var i = 0; i < css_root.style.length; i++) {
            var propertyName = css_root.style[i];
            // Check if the property is a CSS variable
            if (propertyName.startsWith('--') && propertyName.includes('_') === false) {
                var variableName = propertyName;
                var variableValue = css_root.style.getPropertyValue(propertyName);
                cssVariables[variableName] = variableValue.trim();
            }
        }
        //console.log(cssVariables);
        return cssVariables;
    }
}

/**
 * Use it anly if your css variables are not in the first stylesheet
 * @returns An object with all css variables from all stylesheets
 *  starting with '--' without '_' in their name
*/
function get_all_styleSheets_variables() {
    var cssVariables = {};
    var styleSheets = document.styleSheets;
    for (var i = 0; i < styleSheets.length; i++) {
        var styleSheet = styleSheets[i];

        // Check if stylesheet is accessible (avoid cross-origin issues)
        if (!styleSheet || !styleSheet.cssRules) continue;

        for (var j = 0; j < styleSheet.cssRules.length; j++) {
            var cssRule = styleSheet.cssRules[j];

            // Check if the rule is a CSSStyleRule (e.g., not a keyframes rule)
            if (cssRule instanceof CSSStyleRule) {
                // Get the CSSStyleDeclaration of the rule
                var style = cssRule.style;

                for (var k = 0; k < style.length; k++) {
                    var propertyName = style[k];
                    // Check if the property is a CSS variable
                    if (propertyName.startsWith('--') && propertyName.includes('_') === false) {
                        cssVariables[propertyName] = style.getPropertyValue(propertyName).trim();
                    }
                }
            }
        }
    }

    return cssVariables;
}
