function get_StyleSheetsFromFile(url) {
    $.get(url, function (data) {
        $('#DefaultButtonClasses').append(data);
    });
}

function getClassList() {
    var classes = document.styleSheets[1].cssRules
    var variables = [];
    for (var x = 0; x < classes.length; x++) {
        variables.push((classes[x].cssText) ? (classes[x].cssText) : (classes[x].style.cssText));
    }
    return variables;
}

function getClassStyles(className) {
    var classes = document.styleSheets[1].cssRules;
    var str_Var = "";
    var loop = 0;
    var loopClass = 0;
    var loopBetween = 0;
    for (var x = 0; x < classes.length; x++) {
        select = classes[x].selectorText;
        if (select == className || select == className + ':hover') {
            str_Var += (classes[x].cssText) ? (classes[x].cssText) : (classes[x].style.cssText);
            loopClass++;
            loopBetween = loop + 1;
        }
        loop++;
        if (loopClass > 1 && loop > loopBetween) {
            break;
        }
    }
    return str_Var;
}

$(function () {

    get_StyleSheetsFromFile($('link#ButtonClasses').attr('href'));

    function get_button_classes(clicked_class) {
        let dark_normal = '';
        let dark_hover = '';
        if ($('#DarkNormalColor').is(':checked')) {
            dark_normal = 'color: var(--shade-90);\n'
        }
        if ($('#DarkHoverColor').is(':checked')) {
            dark_hover = 'color: var(--shade-90);\n'
        }

        let str = '/* Changes to the Class .' + clicked_class + '. Remove unchanged values and classes.*/';
        $('#MyButtonClasses').val($('#MyButtonClasses').val() + str + '\n\n');

        if (clicked_class.includes(' ')) {
            clicked_class = clicked_class.split(' ')[0];
        }
        let styles = getClassStyles('.' + clicked_class.trim());
        styles = styles.replace(/{/g, '{\n   ');
        styles = styles.replace(/;/g, ';\n   ');
        styles = styles.replace('}', dark_normal + '}\n\n');
        if (dark_hover != '') {
            let pos = styles.lastIndexOf('}');
            styles = styles.substring(0, pos) + dark_hover + '}\n\n';
        }
        $('#MyButtonClasses').val($('#MyButtonClasses').val() + styles + '\n\n');

    }

    $('#primary_classes button').on('click', function () {
        let primary_class = $(this).attr('class');
        get_button_classes(primary_class);
    })

    $('code.clickable').on('click', function () {
        let secondary_class = $(this).text().slice(1);
        alert(secondary_class)
        get_button_classes(secondary_class);
    })

    $('#AppendToCSS').on('click', function () {
        $('#DefaultButtonClasses').val($('#DefaultButtonClasses').val() + '\n\n/*\nMy Button Classes\n*/\n\n' + $('#MyButtonClasses').val());
    })

    $("#color_schemes a").click(function (e) {
        e.preventDefault();
        $("head link#ps_cs").attr("href", $(this).attr('rel'));
    });

    $('#CheckStyles').on('click', function () {
        let changed_styles = $('#MyButtonClasses').val();
        $('<style>').attr('id', 'changed_styles').text(changed_styles).appendTo('head');
    })
    $('#RemoveStyles').on('click', function () {
        $('#changed_styles').remove();
    })

    $("#color_schemes a").click(function () {
        $("head link#ps_cs").attr("href", $(this).attr('rel'));
        return false;
    });

    $('#jq_about_usage').on('click', function() {
        $('#about_usage').slideToggle(300);
    })

    /**
     * Copy Textarea
     */
    $("#copy").click(function () {
        //const text_to_copy = document.getElementById("DefaultButtonClasses").value;
        const text_to_copy = document.querySelector("#DefaultButtonClasses").value;
        navigator.clipboard.writeText(text_to_copy)
            .then(() => {
                console.log('Text copied to clipboard');
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
            });

    });


    /**
     * Save Textarea
     */
    $('#download').click(function () {
        // Create temporal <a> link - can be called från any click
        const textcontent = document.getElementById("DefaultButtonClasses").value;
        let downloadableLink = document.createElement('a');
        downloadableLink.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(textcontent));
        downloadableLink.download = "sx_Buttons.css";
        document.body.appendChild(downloadableLink);
        downloadableLink.click();
        document.body.removeChild(downloadableLink);
    });

})