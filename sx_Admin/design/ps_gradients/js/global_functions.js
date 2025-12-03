var arr_tints = ['var(--tint-0)', 'var(--tint-05)', 'var(--tint-10)', 'var(--tint-20)', 'var(--tint-30)', 'var(--tint-40)', 'var(--tint-50)', 'var(--tint-60)', 'var(--tint-70)', 'var(--tint-80)', 'var(--tint-90)', 'var(--tint-100)'];

var arr_rgba = ['rgba(255, 255, 255, 0.0)', 'rgba(255, 255, 255, 0.05)', 'rgba(255, 255, 255, 0.1)', 'rgba(255, 255, 255, 0.2)', 'rgba(255, 255, 255, 0.3)', 'rgba(255, 255, 255, 0.4)', 'rgba(255, 255, 255, 0.5)', 'rgba(255, 255, 255, 0.6)', 'rgba(255, 255, 255, 0.7)', 'rgba(255, 255, 255, 0.8)', 'rgba(255, 255, 255, 0.9)', 'rgba(255, 255, 255, 1.0)'];

var radio_UseGradientClasses = true;
// To open the last gradien section when moving from root colors to root gradients
var index_lastClickedGradientSection = -1;
/**
 * The default bg for gradients, 
 * Defined in get_colors.js with the first page loading
 * Used when clearing all root colors
 */
var default_background_color = '';
var default_background_variable = '';

/**
 * @param {*} string 
 * @returns replaces all special characters that have special meanings in regular expressions
 */
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/**
 * @param {*} str 
 * @returns A string with cleaned rows, breaks, tabs and double spaces
 */
function clean_rows_tabs_spaces(str) {
    return str.replace(/\s+/g, ' ').trim();
}

/**
 * Sort an object by its key names
 * @param {*} objArr 
 * @returns A sorted object
 */
function sx_sort_an_object_array(objArr) {
    var sorted_objArr = {};
    var sortedKeys = Object.keys(objArr).sort();
    sortedKeys.forEach(function (className) {
        sorted_objArr[className] = objArr[className];
    });

    return sorted_objArr
}

/**
 * Search nd Replace in the values of an object an array of substring by an array of new substrings
 * @param {*} obj 
 * @param {*} substrings : An array of substrings to be replaced
 * @param {*} newStrings : An array of new substrings to replace the above substring
 * @returns An object with replaced values
 */
function replaceSubstringInValues(obj, substrings, newStrings) {
    var replacedObject = {};
    for (var key in obj) {
        if (obj.hasOwnProperty(key)) {
            var value = obj[key];
            // Replace each substring with its corresponding new string
            for (var i = 0; i < substrings.length; i++) {
                var substring = escapeRegExp(substrings[i]);
                var newString = newStrings[i];
                value = value.replace(new RegExp(substring, 'g'), newString);
            }
            replacedObject[key] = value;
        }
    }
    return replacedObject;
}


function sx_getUniqueTints(obj) {
    var uniqueTintsObject = {};
    for (var key in obj) {
        let value = obj[key];
        let arrValue = value.split('tint-');
        let arrTints = [];
        for (t = 1; t < arrValue.length; t++) {
            let loop = arrValue[t].split(')')[0];
            if (arrTints.includes(loop) === false) {
                arrTints.push(loop);
            }
        }
        uniqueTintsObject[key] = arrTints.join(', ');
    }
    return uniqueTintsObject;
}

function sx_getUniqueTintReplacements(obj, curTints, newTints) {
    var uniqueTintsReplacements = {};
    let arrrCur = curTints.join(',');
    arrrCur = arrrCur.replace(new RegExp(escapeRegExp('(--tint-'), 'g'), '');
    arrrCur = arrrCur.replace(new RegExp(escapeRegExp(')'), 'g'), '');
    arrrCur = arrrCur.split(',');

    let arrNew = newTints.join(',');
    arrNew = arrNew.replace(new RegExp(escapeRegExp('(--tint-'), 'g'), '');
    arrNew = arrNew.replace(new RegExp(escapeRegExp(')'), 'g'), '');
    arrNew = arrNew.split(',');

    //console.log('arrrCur',arrrCur)
    //console.log('arrNew',arrNew)

    for (var key in obj) {
        let value = obj[key];
        let arrValue = value.split(',');
        let arrTints = [];
        for (t = 0; t < arrValue.length; t++) {
            let loop = arrValue[t].trim();
            if (arrrCur.includes(loop)) {
                let index = arrrCur.indexOf(loop);
                let loopNew = arrNew[index];
                arrTints.push(loop + ':' + loopNew);
            } else {
                arrTints.push(loop);
            }
        }
        uniqueTintsReplacements[key] = arrTints.join(', ');
    }
    return uniqueTintsReplacements;
}


/**
 * Filters an object by searching for a substring in its key names
 * Not used - replaced by an array of substring
 * @param {*} obj 
 * @param {*} substring 
 * @returns A filtered object that contains only keys that include the substring
 */

function filterKeysBySubstring(obj, substring) {
    var filteredObject = {};
    for (var key in obj) {
        if (obj.hasOwnProperty(key) && key.includes(substring)) {
            filteredObject[key] = obj[key];
        }
    }
    return filteredObject;
}

/**
 * Filters an object by searching for multiple substring in its key names
 * @param {*} obj 
 * @param {*} stringsArr : An array of substrings ti be searched for in the key names
 * @returns A filtered object that contains only keys that include the array of substring
 */
function filterKeysByStringsArray(obj, stringsArr) {
    var filteredObject = {};
    for (var key in obj) {
        if (obj.hasOwnProperty(key) && stringsArr.every(substring => key.includes(substring))) {
            filteredObject[key] = obj[key];
        }
    }
    return filteredObject;
}

/**
 * Set the name and value of a cookie.
 * I days = 0, the cookies expire with the session
 * @param {*} name 
 * @param {*} value 
 * @param {*} days 
 */
function set_Cookie(name, value, days = 0) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * 
 * @param {*} the name of the cookie
 * @returns Retrieves the value of the cookie named
 */
function get_Cookie(name) {
    var nameEQ = name + "=";
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(nameEQ) === 0) {
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}

function cookieExists(name) {
    return get_Cookie(name) !== null;
}

function removeAllCookies_NU() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
    }
}

function delete_Cookie(name) {
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/;";
}

function delete_opacity_cokies() {
    for (c = 0; c < 105; c += 10) {
        delete_Cookie(c);
    }
    delete_Cookie('05');
}

// Function to check if a color is of type RGB
function isRGB(color) {
    return /^rgb\(/.test(color);
}

// Function to check if a color is of type RGBA
function isRGBA(color) {
    return /^rgba\(/.test(color);
}

function isRGBA_string(color) {
    color = color.trim();
    return color.substring(0, 4) === 'rgba';
}
