

/**
 * Search in all stylesheets for classes that have a requested property
 * @param {string} property 
 * @returns An object with all css classes with all their properties
 *  if classes have the required property
*/
function get_css_classes_and_properties_by_property(property) {
    // Object to store class names and their properties with specific property
    var classesWithProperty = {};
    //var specificStyleSheet = document.styleSheets[n];

    // Iterate over all stylesheets
    $.each(document.styleSheets, function (index, stylesheet) {
        // Iterate over all rules in each stylesheet
        $.each(stylesheet.cssRules || stylesheet.rules, function (index, rule) {
            // Check if the rule is a CSS style rule (e.g., not a media rule or keyframes rule)
            if (rule instanceof CSSStyleRule) {
                // Extract class name from selectorText
                var className = rule.selectorText;
                if (className.startsWith(".")) {
                    className = selector.substring(1); // Remove the leading dot
                }
                // Create an object to store properties for this class
                classesWithProperty[className] = {};
                // Check if the rule contains the specific property
                if (rule.style.getPropertyValue(property)) {
                    // Iterate over all properties in the rule
                    for (var i = 0; i < rule.style.length; i++) {
                        var propertyName = rule.style[i];
                        var propertyValue = rule.style.getPropertyValue(propertyName);
                        // Store property and value in the object
                        classesWithProperty[className][propertyName] = propertyValue;
                    }
                }
            }
        });
    });

    return classesWithProperty;
}

/**
 * Serch in all stylesheets for classes
 * @returns An object with all classes with ALL theire properties and values
*/
function get_css_classes_and_properies() {
    // Object to store class names and their properties
    var classProperties = {};

    // Iterate over all stylesheets
    $.each(document.styleSheets, function (index, stylesheet) {
        // Iterate over all rules in each stylesheet
        $.each(stylesheet.cssRules || stylesheet.rules, function (index, rule) {
            // Check if the rule is a CSS style rule (e.g., not a media rule or keyframes rule)
            if (rule instanceof CSSStyleRule) {
                // Extract class name from selectorText
                var selector = rule.selectorText;
                var className = selector.substring(1); // Remove the leading dot
                // Create an object to store properties for this class
                classProperties[className] = {};
                // Iterate over all properties in the rule
                for (var i = 0; i < rule.style.length; i++) {
                    var propertyName = rule.style[i];
                    var propertyValue = rule.style.getPropertyValue(propertyName);
                    // Store property and value in the object
                    classProperties[className][propertyName] = propertyValue;
                }
            }
        });
    });

    return classProperties;
}

/**
 * Adds opacity to HEX color
 * @param {*} hexColor 
 * @param {*} opacityPercent 
 * @returns An HEXA color
 */
function hexColorWithOpacity(hexColor, opacityPercent) {
    // Convert opacity percentage to alpha value (0 to 255)
    var alpha = Math.round(opacityPercent / 100 * 255);
    // Convert alpha value to hexadecimal string
    var alphaHex = alpha.toString(16).padStart(2, '0');
    // Append alpha value to the HEX color
    var colorWithOpacity = hexColor + alphaHex;

    return colorWithOpacity;
}

/**
 * Transforms HEX color to RGB color and adds Alpha opacity
 * @param {*} hexColor 
 * @param {*} opacityPercent 
 * @returns An RGBA color
 */
function hexToRgba(hexColor, opacityPercent) {
    // Remove '#' if present
    hexColor = hexColor.replace('#', '');

    // Convert HEX to RGBA
    var r = parseInt(hexColor.substring(0, 2), 16);
    var g = parseInt(hexColor.substring(2, 4), 16);
    var b = parseInt(hexColor.substring(4, 6), 16);
    var alpha = opacityPercent / 100;

    // Construct the RGBA string
    var rgbaColor = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';

    return rgbaColor;
}

/**
 * Replace all occurencies of a substring in object values by a new substring
 * @param obj 
 * @param search : A string
 * @param replace : A string
 * @returns An object with replaced strings in its values
 */
function sx_search_replace_string_in_object(obj, search, replace) {
    // Function to replace all occurencies of a substring by another
    function replaceColorInString(string, search, replace) {
        //replaces all special characters with special meanings in regular expressions
        search = search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return string.replace(new RegExp(search, 'g'), replace);
    }

    for (var keyName in obj) {
        if (obj.hasOwnProperty(keyName)) {
            var keyValue = obj[keyName];
            if (keyValue.includes(search)) {
                obj[keyName] = replaceColorInString(keyValue, search, replace);
            }
        }
    }
    return obj;
}


/**
 * The design variables (.main{}) must be in the second Stylsheet, with the second cssRules[1]
 */
function sx_getDesignVariables() {
    var css_root = document.styleSheets[1].cssRules[0];
    var arrVar = css_root.style.cssText.split(";");

    var variables = {};
    for (var i = 0; i < arrVar.length; i++) {
        var a = arrVar[i].split(':');
        if (a[0] !== "")
            variables[a[0].trim()] = a[1].trim();
    }
    return variables;
}







/**
 * ==================================================================
 * NOT Used functions - Can be directly used in the code
 * ==================================================================
 */

//To check if a variable is an array (var myArray = [1, 2, 3];):
function sx_check_is_array(myArray) {
    if (Array.isArray(myArray)) {
        console.log("myArray is an array.");
    } else {
        console.log("myArray is not an array.");
    }
    /* jQuery
    if ($.isArray(myArray)) {
        return true;
    } else {
        return false
    }
    */
}

//To check if an array is empty var myArray = [];):
function sx_check_empty_array(myArray) {
    if (myArray.length === 0) {
        return true;
    } else {
        return false
    }
}
//To check if a string is empty:
function sx_check_empty_string(myString) {
    if (myString.trim() === "") {
        console.log("myString is empty.");
    } else {
        console.log("myString is not empty.");
    }
    /* jQuery
    if ($.trim(myString) === "") {
        return true;
    } else {
        return false
    }
    */
}
// to check if a variable is an object
function check_is_object(myObject) {
    if (typeof myObject === 'object') {
        return true;
    } else {
        return false
    }

    /*
    if(myObject instanceof Object) {}
    */
}

//To check if an object is empty (var myObject = {};):
function sx_check_empty_object(myObject) {
    // If the object is not defined, gives an error
    if (Object.keys(myObject).length === 0) {
        console.log("myObject is empty.");
    } else {
        console.log("myObject is not empty.");
    }
    /* 
    // jQuery does not generate the above error
    // Returns false even when the object is not defined
    if ($.isEmptyObject(myObject)) {
        console.log("myObject is empty.");
    } else {
        console.log("myObject is not empty.");
    }
    */
}