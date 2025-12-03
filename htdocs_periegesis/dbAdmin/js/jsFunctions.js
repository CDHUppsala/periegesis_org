// A New centered window opens for every new winName
function openCenteredWindow(strURL, winName, w, h) {
    if (w == 0 || w == '') {
        w = (screen.width - 200);
        var winLeft = 100;
    } else {
        var winLeft = Math.round((screen.width - w) / 2);
    }
    var winTop = 0;
    if (h == 0 || h == '') {
        h = (screen.height);
    }
    winProperties = 'height=' + h + ',width=' + w + ',top=' + winTop + ',left=' + winLeft + ',location=0,status=0,toolbar=0,menubar=0,scrollbars=yes,resizable=yes'
    window.open(strURL, winName, winProperties)
}

// Opens and closes any SINGLE layer
function showBox(boxID) {
    var layerBox = document.getElementById(boxID)
    layerBox.style.display = (layerBox.style.display == 'block') ? 'none' : 'block';
}

//Allow zero value in textbox
function validateZeroNumber(field) {
    var val = field.value;
    if (!/^\d*$/.test(val) || val < 0) {
        alert("You must write a positive number or 0!");
        field.focus();
        field.select();
    }
}

// General, for positive/negative numbers and decimals of any length
function IsAllNumeric(field) {
    var ValidChars = "0123456789.,-";
    var sx = field.value.trim(); // Trim spaces to avoid false negatives

    // Allow empty field (remove this block if an empty field should be invalid)
    if (sx.length === 0) {
        alert("You must write a positive OR negative integer, a decimal number of any length or 0!");
        field.focus();
        field.select();
        return;
    }

    var intDec = 0, intNeg = 0;

    for (var i = 0; i < sx.length; i++) {
        var Char = sx.charAt(i);

        if (ValidChars.indexOf(Char) === -1) {
            alert("You must write a positive or negative integer, a decimal number of any length or 0!");
            field.focus();
            field.select();
            return;
        }

        if (Char === "." || Char === ",") intDec++;
        if (Char === "-") intNeg++;

        // Invalid cases:
        if (
            (i === 0 && (Char === "." || Char === ",")) || // Decimal/comma at start
            (i !== 0 && Char === "-") || // Negative sign in wrong position
            (intDec > 1) || // More than one decimal point
            (intNeg > 1) || // More than one negative sign
            (i === 1 && intNeg === 1 && intDec === 1) // Negative followed by decimal
        ) {
            alert("You must write a positive or negative integer, a decimal number of any length or 0!");
            field.focus();
            field.select();
            return;
        }
    }
}

function sxChangeRadioValue(el, id) {
    radioEl = document.getElementById(id);
    if (el.checked == 1) {
        radioEl.value = 'Yes';
    } else {
        radioEl.value = 'No';
    }
}

function sx_getCSSRootVariables() {
    /**
     * The :root must be in the first Stylshete as the first Rule
     */
    var declaration = document.styleSheets[0].cssRules[0];
    var allVar = declaration.style.cssText.split(";");

    var result = {}
    for (var i = 0; i < allVar.length; i++) {
        var a = allVar[i].split(':');
        if (a[0] !== "")
            result[a[0].trim()] = a[1].trim();
    }
    return result;
}
