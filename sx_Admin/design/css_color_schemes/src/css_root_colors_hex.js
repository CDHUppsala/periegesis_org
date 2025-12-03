/**
 * Create Schemes functions
 */

function sx_get_complement(color) {
    colors = tinycolor(color).complement();
    return colors.toHexString();
}

function sx_get_analogous(color) {
    colors = tinycolor(color).analogous();
    colors_map = colors.map(function (t) {
        return t.toHexString();
    });
    return colors_map;
}

function sx_get_monochromatic(color) {
    colors = tinycolor(color).monochromatic();
    colors_map = colors.map(function (t) {
        return t.toHexString();
    });
    return colors_map;
}

function sx_get_triad(color) {
    colors = tinycolor(color).triad();
    colors_map = colors.map(function (t) {
        return t.toHexString();
    });
    return colors_map;
}

function sx_get_tetrad(color) {
    colors = tinycolor(color).tetrad();
    colors_map = colors.map(function (t) {
        return t.toHexString();
    });
    return colors_map;
}

function sx_get_splitcomplement(color) {
    colors = tinycolor(color).splitcomplement();
    colors_map = colors.map(function (t) {
        return t.toHexString();
    });
    return colors_map;
}

/**
 * Convert color to Grayscale
 * - Average Method: Grayscale = (R + G + B ) / 3
 * - Weighted Method: Grayscale = Grayscale = 0.299R + 0.587G + 0.114B
 */

function sx_to_gray_average(obj_hex) {
    var str_RGB = obj_hex.toString('rgb');
    var arr_rgb = str_RGB.split('(')[1].split(')')[0].split(',');
    var average = Math.round((arr_rgb[0] / 3) + (arr_rgb[1] / 3) + (arr_rgb[2] / 3));
    gray_rgb = tinycolor('rgb(' + average + ', ' + average + ', ' + average + ')');
    return gray_rgb.toHexString();
}

function sx_to_gray_weighted(obj_hex) {
    var str_RGB = obj_hex.toString('rgb');
    var arr_rgb = str_RGB.split('(')[1].split(')')[0].split(',');
    var weighted = Math.round((arr_rgb[0] * 0.299) + (arr_rgb[1] * 0.587) + (arr_rgb[2] * 0.114));
    gray_rgb = tinycolor('rgb(' + weighted + ', ' + weighted + ', ' + weighted + ')');
    return gray_rgb.toHexString();
}

function sx_to_gray_equivalend(obj_hex) {
    var str_RGB = obj_hex.toString('rgb');
    var arr_rgb = str_RGB.split('(')[1].split(')')[0].split(',');
    var average = Math.round((arr_rgb[0] / 3) + (arr_rgb[1] / 3) + (arr_rgb[2] / 3));
    return Math.round((average / 255) * 10) * 10;
}

/**
 * Gradient functions
 */

function sx_get_light_gradients_asc(color, scale = scale_light, steps = steps_light) {
    var loop = "";
    for (i = 0; i < steps; i++) {
        s = i * scale;
        var color2 = color.clone();
        loop += tinycolor(color2).lighten(s).toString() + ',';
    }
    return loop.slice(0, -1);
}

function sx_get_dark_gradients_asc(color, scale = scale_dark, steps = steps_dark) {
    var loop = "";
    for (i = 0; i < steps; i++) {
        s = i * scale;
        var color2 = color.clone();
        this_loop = tinycolor(color2).darken(s).toString();
        loop += this_loop + ',';
    }
    return loop.slice(0, -1);
}

function sx_get_light_gradients(color, scale = scale_light, steps = steps_light, half = false) {
    var loop = "";
    var half_scale = 0;
    if (scale == 10 && half) {
        half_scale = 5;
    }
    for (i = steps; i > -1; i--) {
        s = (i * scale) + half_scale;
        half_scale = 0;
        var color2 = color.clone();
        this_loop = tinycolor(color2).lighten(s).toString();
        loop += this_loop + ',';
    }
    return loop.slice(0, -1);
}

function sx_get_dark_gradients(color, scale = scale_dark, steps = steps_dark) {
    var loop = "";
    for (i = 0; i < steps; i++) {
        s = (i + 1) * scale;
        var color2 = color.clone();
        this_loop = tinycolor(color2).darken(s).toString();
        loop += this_loop + ',';
    }
    return loop.slice(0, -1);
}

function sx_get_desaturate(color, scale = scale_dark, steps = steps_dark) {
    var loop = "";
    for (i = 0; i < steps; i++) {
        s = i * scale;
        var color2 = color.clone();
        this_loop = tinycolor(color2).desaturate(s).toString();
        loop += this_loop + ',';
    }
    return loop.slice(0, -1);
}

function sx_get_brighten(color, scale = scale_dark, steps = steps_dark) {
    var loop = "";
    for (i = 0; i < steps; i++) {
        s = i * scale;
        var color2 = color.clone();
        this_loop = tinycolor(color2).brighten(s).toString();
        loop += this_loop + ',';
    }
    return loop.slice(0, -1);
}


/** 
 * Call Schemes functions
 */

function sx_get_palettes(arr) {
    var bg = "";
    if (arr.constructor !== Array) {
        arr = arr.split(',');
    }
    for (z = 0; z < arr.length; z++) {
        bg += '<span style="background-color:' + arr[z] + ';"></span>';
    }
    return bg;
}

function sx_get_palettes_text(arr) {
    var bg = "";
    if (arr.constructor !== Array) {
        arr = arr.split(',');
    }
    for (z = 0; z < arr.length; z++) {
        bg += '<span>' + arr[z] + '</span>';
    }
    return bg;
}

/**
 * Call all functions
 */

function ps_get_palletes(color) {

    /**
     * Add basic color
     */
    $('#ps-basic').html('').append(sx_get_palettes([color]));
    $('#ps-basic_text').html('').append(sx_get_palettes_text(String([color])));

    /**
     * Gradients of basic color
     */
    var five_basic = false;
    if ($('#fivebasic').is(':checked')) {
        five_basic = true;
    }

    //Tints - shadow of white by 10%
    var light_tints = sx_get_light_gradients_asc(color, 5, 11, false)
    $('#ps-tints').html('').append(sx_get_palettes(light_tints));
    $('#ps-tints_text').html('').append(sx_get_palettes_text(String(light_tints)));
    var root_basic_tints = light_tints;


    //Shades - shadow of black by 10%
    var dark_shades = sx_get_dark_gradients_asc(color, 5, 11, false)
    $('#ps-shades').html('').append(sx_get_palettes(dark_shades));
    $('#ps-shades_text').html('').append(sx_get_palettes_text(String(dark_shades)));
    var root_basic_shades = dark_shades;

    //Tones - shadows of of gray by 10% (desaturation)
    var desaturate = sx_get_desaturate(color, 10, 11)
    var root_desaturate = desaturate;
    $('#ps-tones').html('').append(sx_get_palettes(desaturate));
    $('#ps-tones_text').html('').append(sx_get_palettes_text(String(desaturate)));


    /**
     * ==================================================================
     * Greate Color Schemes
     * ==================================================================
     * Keep Schemes values in strings to create CSS Root Color Variables
     */

    var array;
    var clr;
    var light_g;
    var dark_g;

    // Monochromatic Schemes
    /*
    var sx_analogous = String(sx_get_monochromatic(color));
    $('#ps-monochromatic').html('').append(sx_get_palettes(sx_analogous));
    $('#ps-monochromatic_text').html('').append(sx_get_palettes_text(sx_analogous));
    var root_mono = sx_analogous;
    */


    // Analog Schemes
    var sx_analogous = String(sx_get_analogous(color));
    $('#ps-analogous').html('').append(sx_get_palettes(sx_analogous));
    $('#ps-analogous_text').html('').append(sx_get_palettes_text(sx_analogous));

    var root_analog = sx_analogous;

    // Triad Schemes
    var sx_triad = String(sx_get_triad(color));
    $('#ps-triad').html('').append(sx_get_palettes(sx_triad));
    $('#ps-triad_text').html('').append(sx_get_palettes_text(sx_triad));
    array = sx_triad.split(',')
    var root_triad = "";
    for (x = 1; x < 3; x++) {
        clr = tinycolor(array[x]);
        light_g = sx_get_light_gradients(clr);
        dark_g = sx_get_dark_gradients(clr);

        $('#ps-triad_' + x).html('').append(sx_get_palettes(light_g));
        $('#ps-triad_' + x).append(sx_get_palettes(dark_g));
        $('#ps-triad_' + x + '_text').html('').append(sx_get_palettes_text(light_g));
        $('#ps-triad_' + x + '_text').append(sx_get_palettes_text(dark_g));
        root_triad += light_g + ',' + dark_g + ';';
    }

    // Tetrad Schemes
    var sx_tetrad = String(sx_get_tetrad(color));
    $('#ps-tetrad').html('').append(sx_get_palettes(sx_tetrad));
    $('#ps-tetrad_text').html('').append(sx_get_palettes_text(sx_tetrad));
    array = String(sx_tetrad).split(',')
    var root_tetrad = '';
    for (x = 1; x < 4; x++) {
        clr = tinycolor(array[x]);
        light_g = sx_get_light_gradients(clr)
        dark_g = sx_get_dark_gradients(clr)
        $('#ps-tetrad_' + x).html('').append(sx_get_palettes(light_g));
        $('#ps-tetrad_' + x).append(sx_get_palettes(dark_g));
        $('#ps-tetrad_' + x + '_text').html('').append(sx_get_palettes_text(light_g));
        $('#ps-tetrad_' + x + '_text').append(sx_get_palettes_text(dark_g));
        root_tetrad += light_g + ',' + dark_g + ';';
    }

    // Split Schemes
    var sx_split = String(sx_get_splitcomplement(color));
    $('#ps-split').html('').append(sx_get_palettes(sx_split));
    $('#ps-split_text').html('').append(sx_get_palettes_text(sx_split));
    array = String(sx_split).split(',')
    var root_split = '';
    for (x = 1; x < 3; x++) {
        clr = tinycolor(array[x]);
        light_g = sx_get_light_gradients(clr)
        dark_g = sx_get_dark_gradients(clr)
        $('#ps-split_' + x).html('').append(sx_get_palettes(light_g));
        $('#ps-split_' + x).append(sx_get_palettes(dark_g));
        $('#ps-split_' + x + '_text').html('').append(sx_get_palettes_text(light_g));
        $('#ps-split_' + x + '_text').append(sx_get_palettes_text(dark_g));
        root_split += light_g + ',' + dark_g + ';';
    }

    // Gray Schemes
    var sx_gray = sx_to_gray_average(color) + ',' + sx_to_gray_weighted(color);
    $('#ps-gray').html('').append(sx_get_palettes(sx_gray));
    $('#ps-gray_text').html('').append(sx_get_palettes_text(sx_gray));
    array = String(sx_gray).split(',')
    var root_gray = '';
    var five_gray = false;
    if ($('#fivegray').is(':checked')) {
        five_gray = true;
    }
    for (x = 0; x < 2; x++) {
        clr = tinycolor(array[x]);
        light_g = sx_get_light_gradients(clr, 10, 5, five_gray)
        dark_g = sx_get_dark_gradients(clr)
        $('#ps-gray_' + x).html('').append(sx_get_palettes(light_g));
        $('#ps-gray_' + x).append(sx_get_palettes(dark_g));
        $('#ps-gray_' + x + '_text').html('').append(sx_get_palettes_text(light_g));
        $('#ps-gray_' + x + '_text').append(sx_get_palettes_text(dark_g));
        root_gray += light_g + ',' + dark_g + ';';
    }


    /**
     * ==================================================================
     * Create CSS Root Variables
     * ==================================================================
     */
    var root_variables = $('#root_variables');
    root_variables.html('').append(':root {\n\n');

    //Tints - shadows of white by 10%
    var arr_basic_tints = root_basic_tints.split(',');
    root_variables.append("--basic-color: " + arr_basic_tints[0] + ";\n\n");
    for (r = 1; r < arr_basic_tints.length; r++) {
        sufix = r * 10;
        root_variables.append("--basic-tint-" + sufix + ": " + arr_basic_tints[r] + ";\n");
    }

    //Tones - shadowss of of gray by 10% (desaturation)
    var arr_desaturate = root_desaturate.split(',');
    root_variables.append("\n");
    for (r = 1; r < arr_desaturate.length; r++) {
        sufix = r * 10;
        root_variables.append("--basic-tone-" + sufix + ": " + arr_desaturate[r] + ";\n");
    }

    //Shades - shadows of black by 10%
    var arr_basic_shades = root_basic_shades.split(',');
    root_variables.append("\n");
    for (r = 1; r < arr_basic_shades.length; r++) {
        sufix = r * 10;
        root_variables.append("--basic-shade-" + sufix + ": " + arr_basic_shades[r] + ";\n");
    }


    //Brightness
    var brighten = sx_get_brighten(color, 10, 11)
    var root_brighten = brighten;
    $('#ps-brighten').html('').append(sx_get_palettes(brighten));
    $('#ps-brighten_text').html('').append(sx_get_palettes_text(String(brighten)));

    // Append Brightness to root
    var arr_brighten = root_brighten.split(',');
    root_variables.append("\n");
    for (r = 1; r < arr_brighten.length; r++) {
        sufix = r * 10;
        root_variables.append("--basic-bright-" + sufix + ": " + arr_brighten[r] + ";\n");
    }



    // Monochromatic Colors
    /*
    var arr_mono = root_mono.split(',');
    root_variables.append("\n");
    var length = arr_mono.length - 1;
    for (r = length; r > 0; r--) {
        root_variables.append("--monochrom_" + r + ": " + arr_mono[r] + ";\n");
    }
    */

    // Analog Colors
    var arr_analog = root_analog.split(',');
    root_variables.append("\n");
    var iLoop = 1;
    for (r = 1; r < arr_analog.length; r++) {
        if (r < 3) {
            suffix = "left-" + iLoop
        } else {
            suffix = "right-" + iLoop
        }
        if (r != 3) {
            root_variables.append("--analog-" + suffix + ": " + arr_analog[r] + ";\n");
            iLoop++;
        } else {
            iLoop = 1;
        }
    }

    // Split Colors
    var arr_split = root_split.split(';');
    for (c = 0; c < 2; c++) {
        arr_c = arr_split[c].split(',');
        arr_length = arr_c.length;
        prefix = '';
        if (c == 0) {
            root_variables.append("\n--split-left: " + arr_c[steps_light] + ";\n");
            prefix = 'left';
        }
        if (c == 1) {
            root_variables.append("\n--split-right: " + arr_c[steps_light] + ";\n");
            prefix = 'right';
        }
        var arrLength = arr_length;
        for (r = 0; r < arrLength; r++) {
            root_variables.append("--split-" + prefix + '-' + (arr_length * 10) + ": " + arr_c[r] + ";\n");
            arr_length--;
        }
    }

    // Triad Colors
    var arr_triad = root_triad.split(';');
    for (c = 0; c < 2; c++) {
        arr_c = arr_triad[c].split(',');
        arr_length = arr_c.length;
        prefix = '';
        if (c == 0) {
            root_variables.append("\n--triad-left: " + arr_c[steps_light] + ";\n");
            prefix = 'left';
        }
        if (c == 1) {
            root_variables.append("\n--triad-right: " + arr_c[steps_light] + ";\n");
            prefix = 'right';
        }
        var arrLength = arr_length;
        for (r = 0; r < arrLength; r++) {
            root_variables.append("--triad-" + prefix + '-' + (arr_length * 10) + ": " + arr_c[r] + ";\n");
            arr_length--;
        }
    }

    // Tetrad Colors
    var arr_tetrad = root_tetrad.split(';');
    for (c = 0; c < 3; c++) {
        arr_c = arr_tetrad[c].split(',');
        arr_length = arr_c.length;
        prefix = '';
        if (c == 0) {
            root_variables.append("\n--tetrad-left: " + arr_c[steps_light] + ";\n");
            prefix = '--tetrad-left';
        }
        if (c == 1) {
            root_variables.append("\n--complement: " + arr_c[steps_light] + ";\n");
            prefix = '--complement';
        }
        if (c == 2) {
            root_variables.append("\n--tetrad-right: " + arr_c[steps_light] + ";\n");
            prefix = '--tetrad-right';
        }
        var arrLength = arr_length;
        for (r = 0; r < arrLength; r++) {
            root_variables.append(prefix + '-' + (arr_length * 10) + ": " + arr_c[r] + ";\n");
            arr_length--;
        }
    }

    // Gray Colors: select Average or Wheighted Method
    var arr_split = root_gray.split(';');
    if ($('#weighted').is(':checked')) {
        arr_c = arr_split[1].split(',');
    } else {
        arr_c = arr_split[0].split(',');
    }
    arr_length = arr_c.length;
    root_variables.append("\n--gray: " + arr_c[5] + ";\n");
    for (r = 0; r < arr_c.length; r++) {
        sufix = arr_length * 10;
        root_variables.append("--gray-" + sufix + ": " + arr_c[r] + ";\n");
        arr_length--;
    }

    // Opacities of gray
    var arr_opacities = [];
    root_variables.append("\n");
    for (r = 0; r < 11; r++) {
        opacity = (r / 10).toFixed(1);
        suffix = opacity * 100;
        str_color = 'rgba(128, 128, 128, ' + opacity + ')';
        root_variables.append("--tone-" + suffix + ": " + str_color + ";\n");
        arr_opacities.push(str_color);
    }
    $('#ps-gray_opacities')
        .css('background-color', color)
        .html('').append(sx_get_palettes(arr_opacities));
    $('#ps-gray_opacities_text').html('').append(sx_get_palettes_text(arr_opacities));


    // Opacities of white
    var arr_opacities = [];
    root_variables.append("\n");
    for (r = 0; r < 11; r++) {
        opacity = (r / 10).toFixed(1);
        suffix = opacity * 100;
        str_color = 'rgba(255, 255, 255, ' + opacity + ')';
        root_variables.append("--tint-" + suffix + ": " + str_color + ";\n");
        arr_opacities.push(str_color);
        if (r == 0) {
            root_variables.append("--tint-05: rgba(255 255, 255, 0.05);\n");
        }
    }
    $('#ps-white_opacities')
        .css('background-color', color)
        .html('').append(sx_get_palettes(arr_opacities));
    $('#ps-white_opacities_text').html('').append(sx_get_palettes_text(arr_opacities));


    // Opacities of black
    var arr_opacities = [];
    root_variables.append("\n");
    for (r = 0; r < 11; r++) {
        opacity = (r / 10).toFixed(1);
        suffix = opacity * 100;
        str_color = 'rgba(0, 0, 0, ' + opacity + ')';
        root_variables.append("--shade-" + suffix + ": " + str_color + ";\n");
        arr_opacities.push(str_color);
    }
    $('#ps-black_opacities')
        .css('background-color', color)
        .html('').append(sx_get_palettes(arr_opacities));
    $('#ps-black_opacities_text').html('').append(sx_get_palettes_text(arr_opacities));

    root_variables.append('\n\n}');

}