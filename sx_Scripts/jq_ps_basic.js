function checkMobileMedia() {
    if (window.matchMedia("(min-width: 768px)").matches) {
        return false;
    } else {
        return true;
    }
}

function checkTabletMedia() {
    if (window.matchMedia("(min-width: 1280px)").matches) {
        return false;
    } else {
        return true;
    }
}

//var radioMobileMedia = checkMobileMedia()

/*	===========================================================================
    sx - used for all Start functions that Not require windows load
=========================================================================== */
var $sx = jQuery.noConflict();
$sx(function () {
    // General page scroll function
    var sxScrollToTop = function (iTop, iMinus) {
        $sx("html, body").animate(
            {
                scrollTop: iTop - iMinus,
            },
            400
        );
    };

    /*	===========================================================================
          EVENTS TRIGGERED IN EVERY PAGE
          ===========================================================================
      */

    if ($sx(".print_fixed").length) {
        var print = $sx(".print_fixed");
        if (print.css("position") == "fixed") {
            $sx(window).scroll(function () {
                if (
                    $sx(this).scrollTop() <
                    $sx("#footer").offset().top - $sx(window).height()
                ) {
                    print.fadeIn();
                } else {
                    print.fadeOut();
                }
            });
        }
    }

    if ($sx("div.scroll").length) {
        $sx(window).scroll(function () {
            if (
                $sx(this).scrollTop() > 100 &&
                $sx(this).scrollTop() <
                $sx("#footer").offset().top - $sx(window).height()
            ) {
                $sx("div.scroll").fadeIn();
            } else {
                $sx("div.scroll").fadeOut();
            }
        });
    }

    // Scroll TOP function
    if ($sx(".jqScrollup").length) {
        $sx(".jqScrollup").click(function () {
            $sx("html, body").animate(
                {
                    scrollTop: 0,
                },
                600
            );
            return false;
        });
    }

    /*	===========================================================================
          ALL TAB AND ACCORDIN FUNCTIONS
          =========================================================================== 
      */

    if ($sx(".jqToggleNext").length) {
        $sx(".jqToggleNext").on("click", function () {
            $sx(this).next().slideToggle(300);
        });
    }

    if ($sx(".jqToggleDataID").length) {
        $sx(".jqToggleDataID").click(function () {
            $sx("#" + $sx(this).attr("data-id")).slideToggle(300);
        });
    }

    /**
     * LI within UL opens the corresponding LI on the Next UL
     */
    if ($sx(".jqTabs").length) {
        var sxThisDiv = $sx(".jqTabs");
        sxThisDiv.find("> ul:eq(0) > li").click(function () {
            var sxThis = $sx(this);
            sxThis
                .toggleClass("selected")
                .siblings()
                .removeClass("selected")
                .parent()
                .next("ul")
                .children("li")
                .eq(sxThis.index())
                .slideToggle(300)
                .siblings()
                .hide(300);
            $sx("html, body")
                .delay(300)
                .animate(
                    {
                        scrollTop: sxThisDiv.offset().top - 60,
                    },
                    300
                );
        });
    }

    /**
     * ACCORDION Title IN dt Tags TO SHOW/HIDE Content IN NEXT dd Tags
     * Can be placed both in DL or in wrapping DIV
     */

    if ($sx(".jqAccordion").length) {
        var sxThisDiv = $sx(".jqAccordion");
        sxThisDiv.find("dt").click(function () {
            $sx(this)
                .toggleClass("selected")
                .siblings("dt")
                .removeClass("selected")
                .end()
                .next()
                .slideToggle(300)
                .siblings("dd")
                .slideUp(300);
            $sx("html, body")
                .delay(300)
                .animate(
                    {
                        scrollTop: sxThisDiv.offset().top - 60,
                    },
                    400
                );
        });
    }

    /*	===========================================================================
          ALL SELECT-OPTIONS FUNCTIONS
          ===========================================================================
      */
    /*
          Removes Multiple Select options with the same value and then 
          Sorts them according to their type
          - os replaced by PHP-code
      */
    if ($sx(".jqRemoveSortSelect___NU").length > 0) {
        $sx(".jqRemoveSortSelect option").each(function () {
            $sx(this)
                .siblings("[value='" + this.value + "']")
                .remove();
        });
        $sx(".jqRemoveSortSelect").each(function () {
            sxOptions = $sx(this).find("option");
            sxIDName = $sx(this).attr("id");
            sxDataType = $sx(this).attr("data-type");
            sxSortOptions(sxOptions, sxIDName, sxDataType);
        });
        $sx(".jqRemoveSortSelect").change(function () {
            location = this.value;
        });

        // Sort options - Descending for integers and Ascending for strings
        function sxSortOptions(s, id, dataType) {
            var selected = $sx("#" + id).val();
            s.sort(function (a, b) {
                if (dataType == "number") {
                    return b.value > a.value ? 1 : -1;
                } else {
                    return b.value < a.value ? 1 : -1;
                }
            });
            $sx("#" + id)
                .empty()
                .append(s);
            $sx("#" + id).val(selected);
        }
    }

    if ($sx(".jqSubmitSelectChange").length > 0) {
        $sx(".jqSubmitSelectChange").change(function () {
            location = this.value;
        });
    }

    /*	===========================================================================
          ALL SORTING FUNCTIONS
          ===========================================================================
      /**
       * Sort Multiple Lists by class and data-attribute
        * Used in Films
       */
    if ($sx("#jqSortTrigger").length) {
        $sx("#jqSortTrigger button").click(function () {
            var sxSortData = $sx(this).attr("data-id");
            var sxOrder = $sx(this).siblings(":last").prop("class");
            $sx(this).siblings(":last").toggleClass("order_desc order_asc");
            $sx(".jqSortWrapper").each(function () {
                $sx(this)
                    .children()
                    .sort(function (a, b) {
                        if (sxOrder == "order_asc") {
                            return $sx(b).data(sxSortData) > $sx(a).data(sxSortData) ? 1 : -1;
                        } else {
                            return $sx(b).data(sxSortData) < $sx(a).data(sxSortData) ? 1 : -1;
                        }
                    })
                    .appendTo($sx(this));
            });
        });
    }

    // Random Sorting of children of Multiple ellements with same class
    if ($sx(".jqShuffle").length) {
        var sxParent = $sx(".jqShuffle");
        sxParent.each(function () {
            var sxChildren = $sx(this).children();
            while (sxChildren.length) {
                $sx(this).append(
                    sxChildren.splice(Math.floor(Math.random() * sxChildren.length), 1)[0]
                );
            }
        });
    }

    /**
     * Sort entries in Definition List
     */
    if (
        $sx("#jqSortDefinitionList_Target").length &&
        $sx("#jqSortDefinitionList").length
    ) {
        $sx("#jqSortDefinitionList").click(function () {
            var sxDL = $sx("#jqSortDefinitionList_Target");
            var sOrder = sxDL.data("id");
            // Set DD within DT
            sxDL.children("dt").each(function () {
                $sx(this).append($sx(this).next("dd"));
            });
            // Sort the text within DT
            var sortedItems = sxDL.children("dt").sort(function (a, b) {
                var keyA = $sx(a).text();
                var keyB = $sx(b).text();
                if (sOrder == "ASC") {
                    sxDL.data("id", "DESC");
                    return keyA > keyB ? -1 : 1;
                } else {
                    sxDL.data("id", "ASC");
                    return keyA < keyB ? -1 : 1;
                }
            });
            // Extract the DD from the DT
            $sx.each(sortedItems, function (i, dt) {
                sxDL.append(dt);
                sxDL.append($sx(dt).children("dd"));
            });
        });
    }

    if ($sx(".jqSortTableRows").length) {
        sx_LoadSortTableRows();
    }

    /*
          ==========================================================================
          SPECIAL FUNCTIONS
          ==========================================================================
      */

    // Opens book from a table list by searching the respective title in the second column
    if ($sx(".jqTableListBooks__NU").length > 0) {
        var bookTblCell = $sx("#TableListBooks td:nth-child(2)");
        bookTblCell.css({
            color: "#06b",
            cursor: "pointer",
        });
        bookTblCell.click(function () {
            var sxTemp = $sx(this).text().replace(/ /g, "+");
            location = "books.php?select=yes&title=" + sxTemp;
        });
    }

    /*	===========================================================================
          VARIOUS FUNCTIONS
          ===========================================================================
      */

    //Resize text in articles
    if ($sx(".sxTextResizer").length) {
        var sx_RresizeEl = $sx(".text_resizeable");
        var sx_TextFontSize = parseInt(sx_RresizeEl.css("font-size"));
        var sx_StartTextFontSize = sx_TextFontSize;
        $sx(".sxTextResizer").click(function () {
            var $dir = 2;
            if ($sx(this).attr("id") == "decr") {
                $dir = -2;
            }
            var currSize = sx_StartTextFontSize;
            currSize += $dir;
            if (currSize < sx_TextFontSize) currSize = 0;
            if (currSize > sx_TextFontSize + 8) currSize = 0;
            if (currSize != 0) {
                sx_StartTextFontSize = currSize;
                sx_RresizeEl.css({
                    "font-size": currSize + "px",
                });
            }
        });
    }

    $sx(".text").mouseup(function (event) {
        event.stopPropagation();
        var text = "";
        if (window.getSelection) {
            text = window.getSelection().toString();
        } else if (document.selection && document.selection.type != "Control") {
            text = document.selection.createRange().text;
        }
        alert;
        if (text != "") {
            $sx("body").keydown(function (e) {
                if (e.keyCode == 67) {
                    if (text != "") {
                        alert(lngOpenPrintTextToCopy);
                        text = "";
                        //alert = function() {};
                        alert = null;
                    }
                }
            });
        }
    });

    //For Forum, Text, Book and Film and comments - show/hide comment texts
    if ($sx(".jqCommentsToggle").length) {
        $sx(".jqCommentsToggle").click(function () {
            var sxThis = $sx(this);
            var sxParent = sxThis.parent();
            var iTop = sxParent.offset().top;
            sxThis.toggleClass("comment_show comment_hide");
            sxParent.next("div").slideToggle("slow", function () {
                if (sxThis.hasClass("comment_hide")) {
                    $sx("html, body").animate(
                        {
                            scrollTop: iTop - 64,
                        },
                        400,
                        function () {
                            var iTopNew = sxParent.offset().top;
                            if (iTopNew != iTop) {
                                $sx("html, body").animate(
                                    {
                                        scrollTop: iTopNew - 64,
                                    },
                                    400
                                );
                            }
                        }
                    );
                }
            });
        });
    }
    if ($sx("#jqAddComments").length) {
        $sx("#jqAddComments").click(function () {
            $sx("html, body").animate(
                {
                    scrollTop: $sx("#jqAddComments_Targer").offset().top - 64,
                },
                400
            );
        });
    }
    if ($sx("#jqSortComments").length) {
        $sx("#jqSortComments").click(function (e) {
            e.preventDefault();
            var iTop = $sx(this).parent().offset().top;
            var sxUL = $sx("#jqSortComments_Target");
            sxUL.find(">li").each(function () {
                sxUL.prepend($sx(this));
                var sxSubUL = $sx(this).find(">ul");
                if (sxSubUL) {
                    sxSubUL.find(">li").each(function () {
                        sxSubUL.prepend($sx(this));
                    });
                }
            });
            $sx("html, body").animate(
                {
                    scrollTop: iTop - 54,
                },
                400
            );
        });
    }

    if ($sx(".jqAcceptCookies").length) {
        function sx_SetCookie(c_name, c_value, expiredays) {
            var exdate = new Date();
            exdate.setTime(exdate.getTime() + expiredays * 24 * 60 * 60 * 1000);
            document.cookie =
                encodeURIComponent(c_name) +
                "=" +
                encodeURIComponent(c_value) +
                ";path=/" +
                (expiredays == null ? "" : ";expires=" + exdate.toGMTString());
        }

        setTimeout(function () {
            $sx(".jqAcceptCookies").fadeIn(200);
        }, 1000);

        $sx(".jqRemoveAcceptCookies").click(function () {
            sx_SetCookie("cookie_eu", "cookie_eu", 365 * 10);
            $sx(".jqAcceptCookies").remove();
        });
    }

    if ($sx(".jqDialogAds").length) {
        function sx_SetCookie(c_name, c_value, expiredays) {
            var exdate = new Date();
            exdate.setTime(exdate.getTime() + expiredays * 24 * 60 * 60 * 1000);
            document.cookie =
                encodeURIComponent(c_name) +
                "=" +
                encodeURIComponent(c_value) +
                ";path=/" +
                (expiredays == null ? "" : ";expires=" + exdate.toGMTString());
        }

        setTimeout(function () {
            $sx(".jqDialogAds").fadeIn(200);
        }, 5000);

        $sx(".jqRemoveDialogAds").click(function () {
            sx_SetCookie("dialog_ads", "dialog_ads", 365 * 1);
            $sx(".jqDialogAds").remove();
        });
    }

    /**
     * Wraps a link with data-lightbox features
     *      to any image added manually in reszeable text
     *      .text_resizeable img
     */
    if ($sx("article .text img").length) {
        $sx("article .text img").each(function () {
            var s_parent = $sx(this).parent().get(0).tagName;
            if (s_parent.toLowerCase() != "figure") {
                $sx(this).wrap('<figure data-lightbox="imgInText"></figure>');
            } else {
                $sx(this).parent("figure").attr("data-lightbox", "imgInText");
            }
        });
    }

    /**
     * Modal Window: Load and Close functions
     */
    if ($sx(".jq_load_modal_window").length) {
        sx_load_modal_window();
    }

    /**
     * Show/Hide hidden information in DIVs within TDs
     */
    if ($sx("#jqLoadCalendar td .popup").length) {
        sxPopupCalendarEvents();
    }

    // For week programs and events
    if ($sx(".jqWeekTabs li").length) {
        sxLoadWeekTabsFunction();
    }
    // Universal ajax loader
    if ($sx(".jqUniversalAjax").length) {
        sxLoadUniversalAjax();
    }

    /**
     * Copy and print
     */
    if ($sx(".jq_CopyToClipboard").length) {
        sx_CopyToClipboard();
    }
    if ($sx(".jq_PrintDivElement").length) {
        sx_PrintDivElement();
    }

    if ($sx(".jq_PrintDataToPDF").length) {
        sx_PrintDataToPDF();
    }

    if ($sx(".jq_CopyElementToClipboard").length) {
        sx_CopyElementToClipboard();
    }
    if ($sx(".jq_PrintElementToPDF").length) {
        sx_PrintElementToPDF();
    }
    if ($sx(".jq_ExportTableToExcel").length) {
        sx_ExportTableToExcel();
    }
    if ($sx(".jq_ExportElementToWord").length) {
        sx_ExportElementToWord();
    }
    if ($sx(".jq_ExportTableToHTML").length) {
        sx_ExportTableToHTML();
    }
    if ($sx(".jq_ExportElementToCSV").length) {
        sx_ExportElementToCSV();
    }
    sx_LoadDialogMessages();
});

/*	
    ===========================================================================
    FUNCTIONS CALLED FROM THE VARIABLE $sx
    =========================================================================== 
*/
var sx_LoadSortTableRows = function () {
    $sx(".jqSortTableRows").on("click", function () {
        var $table = $sx("#" + $sx(this).attr("data-id"));
        var rows = $table.find("tr").get();
        rows.sort(function (a, b) {
            var keyA = $sx(a).attr("data-row");
            var keyB = $sx(b).attr("data-row");
            if (keyA < keyB) return 1;
            if (keyA > keyB) return -1;
            return 0;
        });
        $sx.each(rows, function (index, row) {
            $table.children("tbody").append(row);
        });
    });
};

var sxLoadWeekTabsFunction = function () {
    $sx(".jqWeekTabs li").click(function () {
        var sxThis = $sx(this);
        sxThis.addClass("active").siblings().removeClass("active");
        var sxWLayers = sxThis.closest("div").next("ul").find("li");
        if (sxThis.index() < sxWLayers.length) {
            sxWLayers.eq(sxThis.index()).slideDown("slow").siblings().slideUp("slow");
        } else {
            sxWLayers.slideDown("slow");
        }
        sx_ScrollToTopFixed(sxThis, 56);
    });
    // Show the whole week if the date is outside current week
    if (typeof sx_ClickLastWeekTab != "undefined") {
        if ($sx(".jqWeekTabs li").length) {
            $sx(".jqWeekTabs li:last-child").click();
        }
    }
};

/*
Load Universal ajax
Infirmation about URL and ID might be included in a Parent element (DIV or MENU)
...or within the link A
<div data-url="ajaxPage.php" data-id="jqPlaceholderID" class="jqUniversalAjax">
    <ul><li>
        <a href="articles.php?cid=4&tid=5">Link Name</a>
    </li></ul>
</div>
The Closest/Parent DIV contains 
            the data-url= attribute that defines the PAGE to be open by Ajax . 
        the data-id= attribute that defines the ID of the placeholder where Ajax results are to be appanded.
The href= attribute of links
        functions as normal when javascript is disabled
        the query.string is sent to Ajax page.
*/

var sxLoadUniversalAjax = function () {
    $sx(".jqUniversalAjax a").click(function (e) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var arrHREF = $sx(this).attr("href").split("?");
        var sxQuery = arrHREF[1];
        var sxAttr = $sx(this).attr("data-url");
        if (typeof sxAttr !== typeof undefined && sxAttr !== false) {
            var sxURL = $sx(this).attr("data-url");
            var sxDivID = $sx(this).attr("data-id");
        } else {
            var sxURL = $sx(this).closest("div, menu").attr("data-url");
            var sxDivID = $sx(this).closest("div, menu").attr("data-id");
        }
        sxGetUniversalAjax(sxURL, sxQuery, sxDivID);
    });

    $sx(".jqUniversalAjax form").submit(function (e) {
        e.preventDefault();
        var arrHREF = $sx(this).attr("action").split("?");
        var sxQuery = arrHREF[1] + "&" + $sx(this).serialize();
        var sxURL = $sx(this).closest("div").attr("data-url");
        var sxDivID = $sx(this).closest("div").attr("data-id");
        sxGetUniversalAjax(sxURL, sxQuery, sxDivID);
    });
        
    $sx("form.jqUniversalAjax select").change(function (e) {
        e.preventDefault();
        var sxThis = $sx(this).closest('form');
        var arrHREF = sxThis.attr("action").split("?");
        var sxQuery = arrHREF[1] + "&" + sxThis.serialize();
        var sxURL = sxThis.attr("data-url");
        var sxDivID = sxThis.attr("data-id");
        sxGetUniversalAjax(sxURL, sxQuery, sxDivID);
    });

};

var sxGetUniversalAjax = function (sxURL, sxQuery, sxDivID) {
    if ($sx("#jqNavMainCloner").length) {
        $sx("#jqNavMainCloner").slideUp(300);
    }
    if ($sx("#jqNavSideCloner").length) {
        $sx("#jqNavSideCloner").slideUp(300);
    }

    $sx("html, body")
        .removeClass("no-scroll")
        .animate(
            {
                scrollTop: $sx("#" + sxDivID).offset().top - 66,
            },
            400,
            function () {
                $sx("#" + sxDivID)
                    .css("position", "relative")
                    .prepend(
                        '<div style="position:absolute; left: 0; top:0; right: 0;  background: #fff; text-align: center; padding-top: 100px; height: 360px"><img style="margin: 0 auto;" src="../imgPG/loading_blue.gif"></div>'
                    );
                $sx.ajax({
                    url: sxURL,
                    data: sxQuery,
                    cache: false,
                    dataType: "html",
                    scriptCharset: "utf-8",
                    type: "GET",
                    success: function (result) {
                        $sx("#" + sxDivID)
                            .css("position", "static")
                            .html(result);
                    },
                    error: function () {
                        $sx("#" + sxDivID)
                            .css("position", "static")
                            .html(
                                "<h3>Undefined Error! Please contact the administrator of the site.</h3>"
                            );
                    },
                });
                $sx("html, body")
                    .delay(601)
                    .animate(
                        {
                            scrollTop: $sx("#" + sxDivID).offset().top - 66,
                        },
                        300
                    );
            }
        );
};

/**
 * Copy to clipboard or Print to PDF
 */

var sx_CopyToClipboard = function () {
    $sx(".jq_CopyToClipboard").on("click", function () {
        $sx("body").append(
            '<div id="jq_CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100"><div>'
        );
        var cloned = $sx("#" + $sx(this).attr("data-id")).clone();
        cloned.attr("id", "sx_ClonedID").appendTo("#jq_CloneTarget");
        $sx("#sx_ClonedID").find("aside").remove();
        $sx("#sx_ClonedID").find(".jq_NoPrint").remove();
        $sx("#sx_ClonedID").find("video").unwrap().remove();
        $sx("#sx_ClonedID")
            .find(".jq_PrintNext")
            .next("div")
            .css("display", "block");

        if (navigator.clipboard) {
            // Remove hidden hyphens
            var text_to_copy = document
                .getElementById("sx_ClonedID")
                .innerText.replace(/­/g, "");
            navigator.clipboard
                .writeText(text_to_copy)
                .then(function () {
                    alert("The article has been copied to your clipboard as Plain Text!");
                })
                .catch(function () {
                    alert(
                        "Error! The article could not be copied to your clipboar. Please selecet other alternatives!"
                    );
                });
        } else {
            alert(
                "General Error! The article could not be copied to your clipboar. Please selecet other alternatives!"
            );
        }
        $sx("#jq_CloneTarget").remove();
    });
};

var sx_PrintDivElement = function () {
    $sx(".jq_PrintDivElement").on("click", function () {
        $sx("body").append(
            '<div id="jq_CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100;"><div>'
        );
        var cloned = $sx("#" + $sx(this).attr("data-id")).clone();
        cloned
            .attr("id", "sx_ClonedID")
            .css("text-align", "justify")
            .appendTo("#jq_CloneTarget");

        $sx("#sx_ClonedID").find("aside").remove();
        $sx("#sx_ClonedID").find(".jq_NoPrint").remove();
        $sx("#sx_ClonedID").find("video").unwrap().remove();
        $sx("#sx_ClonedID")
            .find(".jq_PrintNext")
            .next("div")
            .css("display", "block");
        $sx("#sx_ClonedID").find("a").contents().unwrap();
        $sx("#sx_ClonedID")
            .find(".img_cycler_manual")
            .find("ul")
            .remove()
            .end()
            .find("figure")
            .unwrap()
            .find("span")
            .remove()
            .end()
            .find("img")
            .removeAttr("style");
        $sx("#sx_ClonedID").find("img").removeAttr("style").css("max-width", "40%");

        var newWin = window.open("", "Print-Window");
        newWin.document.open();
        newWin.document.write(
            '<html><head><style>body {font-size: 18px;}</style></head><body onload="window.print();window.close();">' +
            $sx("#jq_CloneTarget").html() +
            "</body></html>"
        );
        newWin.document.close();

        $sx("#jq_CloneTarget").remove();
    });
};

/**
 * Load Modal Window with multiple forms
 * and submit the forms by ajax request
 */
var sx_load_modal_window = function () {
    $sx(".jq_load_modal_window").submit(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var form = $sx(this);
        if (form.find(".jq_submit").length) {
            form
                .find(".jq_submit")
                .attr("disabled", "disabled")
                .css("cursor", "wait");
        }

        // Serialize form data and append the nocache parameter
        // remove 'sx=hermes' as the check for ajax request is Relying on standard HTTP headers 
        var data = form.serialize() + `&sx=hermes&nocache=${new Date().getTime()}`;
        // Set the target URL for the AJAX request
        var url = "ajax_modal.php";

        $sx.post(url, data, function (data, status) {
            // Load the response into the modal content
            $sx("#jq_ModalContent").html(data);
        });

        // Show the modal
        $sx("#jq_Modal").show();
    });

    // Close the modal and clear its content
    $sx("#jq_ModalClose").click(function () {
        $sx("#jq_ModalContent").html("");
        $sx("#jq_Modal").hide();
    });
};

var sx_PrintDataToPDF = function () {
    $sx(".jq_PrintDataToPDF").on("click", function () {
        $sx("body").append(
            '<div id="CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100;"></div>'
        );
        var main = $sx("#" + $sx(this).attr("data-id"));

        if (main.closest("article").find("h1").first().length) {
            $sx("#CloneTarget").append(
                "<h1>" + main.closest("article").find("h1").first().text() + "</h1>"
            );
        }
        if (main.closest("article").find("h2").first().length) {
            $sx("#CloneTarget").append(
                "<h2>" + main.closest("article").find("h2").first().text() + "</h2>"
            );
        }

        var cloned = main.clone();
        cloned.attr("id", "Element_Cloned").appendTo("#CloneTarget");
        var el_Cloned = $sx("#Element_Cloned");

        if (el_Cloned.find("table").length) {

            el_Cloned.find("table").css({ "width": "100%" });
            el_Cloned.find("th,td").css({
                borderBottom: "1px solid #bbbbbb",
                padding: "4px",
            });
            el_Cloned.find("th").css({
                "text-align": "left",
            });
            if (el_Cloned.find("td img").length) {
                el_Cloned
                    .find("td img")
                    .css({ "max-width": "100%", height: "auto", marginBottom: 'auto' })
                    .parent("td")
                    .css({ width: "40%", border: "0", verticalAlign: 'top' })
                    .siblings()
                    .css({ verticalAlign: "top", border: "0" });
            }
            el_Cloned.find('table').each(function() {
                if ($sx(this).hasClass('page_break_after')) {
                    $sx(this).css({'page-break-after': 'always'});
                }
            });

        }

        var newWin = window.open("", "Print-Window");
        newWin.document.open();
        newWin.document.write(
            '<html><body onload="window.print();window.close();">' +
            $sx("#CloneTarget").html() +
            "</body></html>"
        );
        newWin.document.close();

        $sx("#CloneTarget").remove();
    });
};

function sx_clear_cloned_html(source, type) {
    $sx("body").append(
        '<div id="CloneTarget" style="position: absolute; top: 0; left: 0; opacity: 0; z-index: -100"><div>'
    );
    source.attr("id", "Cloned_Table").appendTo("#CloneTarget");
    var el_cloned = $sx("#Cloned_Table");

    el_cloned.find("div").contents().unwrap();
    el_cloned.find("h1, h2, h3, h4").removeAttr("style, class");
    el_cloned.find("th span, td span").contents().unwrap();
    el_cloned.find("th a").contents().unwrap();
    el_cloned.find("th").css({ 'text-align': 'left', 'background': '#EEEEEE' });
    el_cloned.find("td map").contents().unwrap();
    el_cloned.find("br").remove();
    el_cloned.find("img").remove();
    el_cloned.find("a svg").remove();
    el_cloned.find("tr").removeAttr("style").find("td, th").removeAttr("style");
    el_cloned
        .find("tr").css({ 'vertical-align': 'top', 'border-bottom': '1px solid #EEEEEE' });
    el_cloned.find("thead, tbody").removeAttr("class");
    el_cloned.removeAttr("class").css('border-collapse', 'collapse');
    if (el_cloned.find("ul.jqTabsList").length) {
        el_cloned.find("ul.jqTabsList").remove()
    }

    if (type == "text") {
        return document.getElementById("Cloned_Table").innerText;
    } else {
        return $sx("#CloneTarget").html();
    }
}

var sx_CopyElementToClipboard = function () {
    $sx(".jq_CopyElementToClipboard").on("click", function () {
        var cloned = $sx("#" + $sx(this).attr("data-id")).clone();
        var html = sx_clear_cloned_html(cloned, "text");

        if (navigator.clipboard) {
            //var text_to_copy = document.getElementById("Cloned_Table").innerText;
            navigator.clipboard
                .writeText(html)
                .then(function () {
                    alert("The content has been copied to your clipboard as Plain Text!");
                })
                .catch(function () {
                    alert(
                        "Error! The content could not be copied to your clipboar.\nPlease selecet other alternatives!"
                    );
                });
        } else {
            alert(
                "General Error! No support for Clipboard.\nThe content could not be copied to your clipboar.\nPlease selecet other alternatives!"
            );
        }
        $sx("#CloneTarget").remove();
    });
};

var sx_PrintElementToPDF = function () {
    $sx(".jq_PrintElementToPDF").on("click", function () {
        var main = $sx("#" + $sx(this).attr("data-id"));
        var cloned = main.clone();
        var html = sx_clear_cloned_html(cloned, "html");

        var Title = "";
        var SubTitle = "";
        if (main.parents("main").find("h1").first().length) {
            Title = "<h1>" + main.parents("main").find("h1").first().text() + "</h1>";
        }
        if (main.parents("main").find("h2").first().length) {
            SubTitle =
                "<h2>" + main.parents("main").find("h2").first().text() + "</h2>";
        }

        var newWin = window.open("", "Print-Window");
        newWin.document.open();
        newWin.document.write(
            '<html><body onload="window.print();window.close();">' +
            Title +
            SubTitle +
            html +
            "</body></html>"
        );
        newWin.document.close();

        $sx("#CloneTarget").remove();
    });
};

var sx_ExportTableToExcel = function () {
    $sx(".jq_ExportTableToExcel").on("click", function () {
        var cloned = $sx("#" + $sx(this).attr("data-id")).clone();
        var html = sx_clear_cloned_html(cloned, "html");

        window.open(
            "data:application/vnd.ms-excel;charset=utf-8,\uFEFF" +
            encodeURIComponent(html)
        );
        $sx("#CloneTarget").remove();
    });
};

var sx_ExportTableToHTML = function () {
    $sx(".jq_ExportTableToHTML").on("click", function () {
        var main = $sx("#" + $sx(this).attr("data-id"));
        var cloned = main.clone();
        var html = sx_clear_cloned_html(cloned, "html");

        var Title = "";
        var SubTitle = "";
        if (main.parents("main").find("h1").first().length) {
            Title = "<h1>" + main.parents("main").find("h1").first().text() + "</h1>";
        }
        if (main.parents("main").find("h2").first().length) {
            SubTitle =
                "<h2>" + main.parents("main").find("h2").first().text() + "</h2>";
        }

        var element = document.createElement("a");
        element.setAttribute(
            "href",
            "data:text/plain;charset=utf-8," +
            encodeURIComponent(
                "<html><body>" + Title + SubTitle + html + "</body></html>"
            )
        );
        element.setAttribute("download", $sx(this).attr("data-id") + ".html");
        element.style.display = "none";
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);

        $sx("#CloneTarget").remove();
    });
};

var sx_ExportElementToWord = function () {
    $sx(".jq_ExportElementToWord").on("click", function () {
        var cloned = $sx("#" + $sx(this).attr("data-id")).clone();

        var main = $sx("#" + $sx(this).attr("data-id"));
        var cloned = main.clone();
        var html = sx_clear_cloned_html(cloned, "html");

        var Title = "";
        var SubTitle = "";
        if (main.parents("main").find("h1").first().length) {
            Title = "<h1>" + main.parents("main").find("h1").first().text() + "</h1>";
        }
        if (main.parents("main").find("h2").first().length) {
            SubTitle =
                "<h2>" + main.parents("main").find("h2").first().text() + "</h2>";
        }

        window.open(
            "data:application/msword;charset=utf-8,\uFEFF" +
            encodeURIComponent(Title + SubTitle + html)
        );

        $sx("#CloneTarget").remove();
    });
};

var sx_ExportElementToCSV = function () {
    $sx(".jq_ExportElementToCSV").click(function () {
        var tableObj = $sx('#' + $sx(this).attr("data-id"))
        filename = "Table_To_CSV_" + new Date().toISOString().slice(0, 10) + '_' + Math.floor((Math.random() * 900) + 100) + ".csv"
        var csvData = [];

        var rowData = [];
        var cols = tableObj.find('th');
        for (var j = 0; j < cols.length; j++) {
            var cellData = cols[j].innerText.replace(/"/g, '');
            rowData.push(cellData);
        }

        csvData.push(rowData.join(','));

        var rows = tableObj.find('tbody tr');
        for (var i = 0; i < rows.length; i++) {
            var rowData = [];
            var cols = $sx(rows[i]).find('td');
            for (var j = 0; j < cols.length; j++) {
                //var cellData = escapeCsvValue(cols[j].innerHTML.trim());
                var cellData = escapeCsvValue(cols[j].innerText.trim());
                rowData.push(cellData);
            }
            csvData.push(rowData.join(','));
        }

        csvData = csvData.join('\n');
        csvData = 'data:text/csv;charset=utf-8,' + csvData;
        var link = document.createElement("a");
        link.href = csvData;
        link.setAttribute("download", filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

    })

};

function escapeCsvValue(value) {
    if (value.includes('\r\n')) {
        value = value.split('\r\n');
        value = value.join(' ');
    }
    if (value.includes('\n')) {
        value = value.split('\n');
        value = value.join(' ');
    }
    value = value.replace(/"/g, '""');
    value = value.replace(/,/g, ' ');

    // include comma here to sett double quotes if you want to keep commas
    if (value.includes('"') || value.includes(' ')) {
        value = '"' + value + '"';
    }

    return value;
}

var sx_modal_dialog = "";
var sx_modal_overlay = "";
// Common closer of dialog box
var sxCloseDialogWindow = function () {
    sx_modal_dialog.on("click", ".jqClose", function () {
        sx_modal_dialog.fadeOut(function () {
            sx_modal_overlay.fadeOut(function () {
                sx_modal_dialog.remove();
                sx_modal_overlay.remove();
            });
        });
    });
}

var sx_LoadDialogMessages = function (msg) {
    var sxMsg = msg;
    if (typeof sxMsg === "undefined") {
        sxMsg = ""
    }
    if (sxMsg.length) {
        $sx("body").append('<div id="modal_overlay"></div>');
        $sx("body").append('<div id="modal_dialog"></div>');
        sx_modal_overlay = $sx("#modal_overlay");
        sx_modal_dialog = $sx("#modal_dialog");
        sx_modal_dialog.html(sxMsg + '<p><button class="button-grey button-gradient jqClose">&#10006;</button>');
        sx_modal_overlay.fadeIn(function () {
            sx_modal_dialog.fadeIn();
        });
        sxCloseDialogWindow();
    };
};

var sxPopupCalendarEvents = function () {
    if ($sx("#jqLoadCalendar td .popup").length > 0) {
        // Event delegation for hover effects
        $sx("#jqLoadCalendar").on("mouseenter", "td a", function () {
            var eDiv = $sx(this).closest("td").find(".popup");
            if (eDiv.is(":hidden") && !eDiv.data("fixed")) { // Only show if not fixed
                eDiv.stop(true, true).fadeIn(400);
            }
        }).on("mouseleave", "td a", function () {
            var eDiv = $sx(this).closest("td").find(".popup");
            if (!eDiv.data("fixed")) { // Only hide if not fixed
                eDiv.stop(true, true).fadeOut(100);
            }
        });

        // Click functionality to fix the popup
        $sx("#jqLoadCalendar").on("click", "td>a", function (e) {
            e.preventDefault();
            var eDiv = $sx(this).closest("td").find(".popup");
            
            // Toggle fixed state
            if (eDiv.data("fixed")) {
                eDiv.data("fixed", false).stop(true, true).fadeOut(100);
            } else {
                // Unfix all other popups
                $sx("#jqLoadCalendar td div").data("fixed", false).fadeOut(100);

                // Fix the clicked popup
                eDiv.data("fixed", true).stop(true, true).fadeIn(400);
            }
        });

        // Close button functionality
        $sx("#jqLoadCalendar").on("click", ".popup_close", function (e) {
            e.preventDefault();
            var eDiv = $sx(this).closest("div");

            eDiv.data("fixed", false).fadeOut(100);
        });
    }
}
