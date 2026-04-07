var $sx = jQuery.noConflict();
$sx(document).ready(function () {

    //Popup Absolute Help for fields in all editing pages
    $sx(".jqHelpButtonPopup").click(function () {
        var sxThis = $sx(this);
        var sxThisID = sxThis.attr("data-id");
        var sxThisName = sxThis.attr("data-name");
        var sxDWidth = $sx(document).width();
        var sxRight = sxDWidth - sxThis.offset().left;
        var sxHelp = $sx("#absoluteHelp");
        var sxHelpWidth = parseInt(sxHelp.css("width"));
        if (sxRight > sxDWidth / 2) {
            sxRight = sxRight - sxHelpWidth - 24;
        }
        if (sxHelp.attr("data-name") != sxThisName) {
            sxHelp.hide(300, function () {
                sxHelp.attr("data-name", sxThisName)
                    .css({
                        "top": (sxThis.offset().top + 32),
                        "right": sxRight
                    })
                    .html(sxThisID)
                    .show(300);
            });
        } else {
            sxHelp.slideToggle(400);
        };
    });

    if ($sx(".jqHelpButton").length) {
        $sx(".jqHelpButton").click(function () {
            /*
            var sxThis = $sx(this);
            var sxThisID = sxThis.attr("data-id");
            var sxHelp = $sx("#" + sxThisID);
            sxHelp.slideToggle(300);
            */
            $sx("#" + $sx(this).attr("data-id")).slideToggle(300);
        });
    };

    // Sort Table Rows
    if ($sx('.jqTableList').length) {
        var sx_dir;
        $sx(".jqTableList thead th div").click(function () {
            var col = $sx(this).parent().index() + 1;
            var rows = $sx('.jqTableList tbody  tr').get();
            if (!sx_dir || sx_dir == 'desc') {
                sx_dir = 'asc';
            } else {
                sx_dir = 'desc';
            }
            rows.sort(function (a, b) {
                var A = $sx(a).children('td').eq(col).text().toUpperCase();
                var B = $sx(b).children('td').eq(col).text().toUpperCase();
                if (A < B) {
                    if (sx_dir == 'asc') {
                        return -1;
                    } else {
                        return 1;
                    }
                }
                if (A > B) {
                    if (sx_dir == 'asc') {
                        return 1;
                    } else {
                        return -1;
                    }
                }
                return 0;
            });
            $sx.each(rows, function (index, row) {
                $sx('.jqTableList').children('tbody').append(row);
            });
        });
    };

    if ($sx('#imgPreview').length && $sx('.imgPreview').length) {
        $sx(".imgPreview").click(function (e) {
            e.preventDefault();
            if ($sx(this).attr('src')) {
                sxURL = $sx(this).attr('src');
            } else {
                sxURL = $sx(this).attr('href');
            }
            sxImgDiv = $sx("#imgPreview");
            sxImgDiv.find('img').attr('src', sxURL);
            sxImgDiv.slideToggle(400);
        });
        $sx('#imgPreview').click(function () {
            $sx(this).hide(400);
        });
    };


    /*
        Initialize functions defined utside $sx() - to be used by ajax-loades pages
        Not neccassary if they are not used in other pages
        if a function is used both in ajax-loaded pages and the first loaded page
        it will propagate - repeat themeselves - with reinitialization, 
        so, use .off("click").on("click", xxx) to avoid it
    */
    sxReloadTabs();
    sxReloadInfoToggle();
    sxToggleNext();

    if ($sx('.jqProcessOrdersTable').length) {
        sx_LoadProcessOrdersElements();
    }

    if ($sx('#jqSelectClasses').length) {
        sx_LodSelectClasses();
    }

    if ($sx(".jq_CopyToClipboard").length) {
        sx_CopyToClipboard();
    }
    if ($sx(".jq_PrintDivElement").length) {
        sx_PrintDivElement();
    }
    if ($sx(".jq_ExportTableIntoExcel").length) {
        sx_ExportTableIntoExcel();
    }

    if ($sx(".jq_Tooltip").length) {
        sx_Tooltip();

    }

});

var sx_Tooltip = function () {
    $sx('span.jq_Tooltip')
        .bind('mouseover', function (e) {
            e.stopPropagation();
            if (!$sx(this).find('div').length) {
                $sx(this).append('<div>' + $sx(this).attr("data-title") + '</div>');
            }
            $sx(this).find('div').show(300);
        })
        .bind('mouseout', function (e) {
            e.stopPropagation();
            $sx(this).find('div').stop(true, true).hide(300);
        });
}
/*
    Define here, outside the $sx(), the functions used by ajax-loaded pages
    Initialize them at the end of those pages
*/
/*
    Show/Hide information with different Tabs
    Initialize this function both here and in the ajax-loaded pages
*/
var sxReloadTabs = function () {
    $sx("#tabs").off("click").on("click", "a", function () {
        var sxThis = $sx(this);
        if (sxThis.attr("class") == null || sxThis.attr("class") == '') {
            sxThis.addClass("selected")
                .siblings().removeClass("selected")
                .end()
                .parent()
                .siblings().hide(400)
                .siblings("#" + sxThis.data("id")).show(400);
        }
    });
}

/*
    Show/Hide any information in a single NEXT Element (usually DIV or UL)
    Initialize here and in every ajax-loaded page
    To avoid repeating the function with reinitialization, set .off()
*/
var sxReloadInfoToggle = function () {
    $sx(".jqInfoToggle").off("click").on("click", function () {
        $sx(this).parent().next().slideToggle('fast');
    });
};
var sxToggleNext = function () {
    $sx(".jqHelpToggle h4").on("click", function () {
        $sx(this).toggleClass("selected").next().slideToggle('slow');
        $sx("html, body").animate({
            scrollTop: $sx(this).offset().top
        }, 500);
    });

};

var sx_LoadProcessOrdersElements = function () {
    var cancelDIV = '<div title="Clear Color" class="colorCancel"></div>';
    //	$sx(".jqProcessOrdersTable tr").on("input", function(){
    $sx(".jqProcessOrdersTable tr input").change(function () {
        var secondTD = $sx(this).closest("tr").find("td:nth-child(2)");
        secondTD.addClass("checkColor");
        if (!secondTD.find(".colorCancel").length) {
            secondTD.append(cancelDIV);
        }
        sx_RemoveProcessOrdersElements();
    })
}

var sx_RemoveProcessOrdersElements = function () {
    $sx(".colorCancel").on("click", function () {
        $sx(this).closest("td")
            .removeClass("checkColor")
            .remove(cancelDIV);
    });
}

var sx_LodSelectClasses = function () {
    $sx("#jqSelectClasses").on("change", "select", function (e) {
        var sFieldName = encodeURIComponent($sx(e.target).attr("name"));
        var iFieldValue = encodeURIComponent($sx(e.target).val());
        if (parseInt(iFieldValue) > 0) {
            window.location.href = "list.php?searchFieldName=" + sFieldName + "&searchFieldValue=" + iFieldValue;
        } else {
            window.location.href = "list.php?searchFieldName=" + sFieldName + "&searchFieldValue=" + iFieldValue;
        }
    });
};


var sx_CopyToClipboard = function () {
    $sx(".jq_CopyToClipboard").on("click", function () {
        $sx('body').append('<div id="CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100"><div>');
        var cloned = $sx("#" + $sx(this).attr('data-id')).clone();
        cloned.attr('id', 'Cloned_Table').appendTo('#CloneTarget');

        $sx('#Cloned_Table').find('div').remove();
        $sx('#Cloned_Table').find('span').remove();
        $sx('#Cloned_Table').find('img').remove();
        $sx('#Cloned_Table').find('a').contents().unwrap();
        $sx('#Cloned_Table').find('tr').removeAttr('style').find('td, th').removeAttr('style');
        $sx('#Cloned_Table').removeAttr('class');

        if (!navigator.clipboard) {
            var HTML_to_copy = document.getElementById('Cloned_Table');
            var range = document.createRange();
            range.selectNode(HTML_to_copy);
            window.getSelection().addRange(range);
            document.execCommand('copy');
            alert('The text has been copied to your clipboard!');
        } else {
            var text_to_copy = document.getElementById("Cloned_Table").innerText;
            navigator.clipboard.writeText(text_to_copy).then(
                function () {
                    alert("The text has been copied to your clipboard as Plain Text!");
                })
                .catch(
                    function () {
                        alert("Error! The text could not be copied to your clipboar. Please selecet other alternatives!");
                    });

        }
        $sx('#CloneTarget').remove();

    });
}

var sx_PrintDivElement = function () {
    $sx(".jq_PrintDivElement").on("click", function () {
        $sx('body').append('<div id="CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100"><div>');
        var cloned = $sx("#" + $sx(this).attr('data-id')).clone();
        cloned.attr('id', 'Cloned_Table').appendTo('#CloneTarget');

        $sx('#Cloned_Table').find('div').remove();
        $sx('#Cloned_Table').find('span').remove();
        $sx('#Cloned_Table').find('img').remove();
        $sx('#Cloned_Table').find('a').contents().unwrap();
        $sx('#Cloned_Table').find('tr').removeAttr('style').find('td, th').removeAttr('style');
        $sx('#Cloned_Table').removeAttr('class');

        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<html><body onload="window.print();window.close();">' + $sx('#CloneTarget').html() + '</body></html>');
        newWin.document.close();

        $sx('#CloneTarget').remove();
    });
}

var sx_ExportTableIntoExcel = function () {
    $sx(".jq_ExportTableIntoExcel").on("click", function () {
        $sx('body').append('<div id="CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100"><div>');
        var cloned = $sx("#" + $sx(this).attr('data-id')).clone();
        cloned.attr('id', 'Cloned_Table').appendTo('#CloneTarget');

        $sx('#Cloned_Table').find('div').remove();
        $sx('#Cloned_Table').find('span').remove();
        $sx('#Cloned_Table').find('br').remove();
        $sx('#Cloned_Table').find('img').remove();
        $sx('#Cloned_Table').find('a').contents().unwrap();
        $sx('#Cloned_Table').find('tr').removeAttr('style').find('td, th').removeAttr('style');
        $sx('#Cloned_Table').removeAttr('class');

        window.open('data:application/vnd.ms-excel;charset=utf-8,\uFEFF' + encodeURIComponent($sx('#CloneTarget').html()));
        $sx('#CloneTarget').remove();
    });
}
