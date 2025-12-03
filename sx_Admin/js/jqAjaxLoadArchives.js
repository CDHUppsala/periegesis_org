var $sx_aj = jQuery.noConflict();
$sx_aj(document).ready(function () {
    $sx_aj.ajaxSetup({
        cache: false
    });
    $sx_aj(".jqLoadArchives").click(function () {
        var $Data = $sx_aj(this).attr("data-id");
        var s_URL = "sxUpload/ajax_view_images.php";
        if ($Data == "Accessories") {
            s_URL = "ajax_LoadAccessories.php"
        } else if ($Data == "BookToAuthors") {
            s_URL = "ajax_bookToAuthors.php"
        } else if ($Data == "TextToAuthors") {
            s_URL = "ajax_textToAuthors.php"
        } else if ($Data == "GetColorSchemes") {
            s_URL = "design/gradients/index_ajax.php"
        } else if ($Data == "GetTemplatesList") {
            s_URL = "design/templates/index.php"
        } else if ($Data == "Conferences") {
            s_URL = "sxUpload/ajax_view_conference_files.php"
        } else if ($Data == "Abstracts") {
            s_URL = "ps_conferences/ajax_paperAbstracts.php"
        }
        $sx_aj.ajax({
            url: s_URL,
            cache: false,
            data: $Data,
            dataType: "html",
            scriptCharset: "utf-8",
            type: "GET",
            success: function (result) {
                $sx_aj("#jqLoadArchivesLayer").html(result);
                // Call a function within the loading content after it is loaded
                if (typeof design_contentLoadedActions === 'function') {
                    design_contentLoadedActions();
                }
            },
            error: function (xhr, status, error) {
                $sx_aj("#jqLoadArchivesLayer").html("<h3>Error! Please contact the administrator of the site.</h3><p>Error: "+ status + " " + error);
                console.error('Error:', status, error);
            }
        });
        $sx_aj("#jqLoadArchivesWrapper").fadeIn(400);
        var $Position = $sx_aj("#header").offset().top;
        $sx_aj("html, body").delay(0).animate({
            scrollTop: $Position
        }, 400);
    });
//    http://localhost:4104/dbAdmin/design/gradients/index_ajax.php?GetColorSchemes&_=1733675280879

    $sx_aj("#jqLoadArchivesToggle").click(function () {
        var $this = $sx_aj(this);
        var $wrapper = $sx_aj("#jqLoadArchivesWrapper");
        var $layer = $sx_aj("#jqLoadArchivesLayer");
        if ($this.attr("class") == "aside_hide") {
            $layer.fadeOut(300, function () {
                $wrapper.animate({
                    "width": "25px"
                }, 300);
            })
            $this.removeClass("aside_hide").addClass("aside_show");
        } else {
            $wrapper.animate({
                "width": '500px'
            }, 300, function () {
                $layer.fadeIn(300);
            });
            $this.removeClass("aside_show").addClass("aside_hide");
        }
    });

    $sx_aj("#jq_width").click(function () {
        var $wrapper = $sx_aj("#jqLoadArchivesWrapper");
        if ($wrapper.width() <= 500) {
            $wrapper.animate({
                "width": '50%'
            }, 300);
            $sx_aj(this).removeClass("aside_show").addClass("aside_hide");
        } else {
            $wrapper.animate({
                "width": '500px'
            }, 300);
            $sx_aj(this).removeClass("aside_hide").addClass("aside_show");
        }
    });

    /**
     * You place her all kinds of external variables that you want to add to a Adding/Editing Table records
     * 		Variables are added by First Checking them and then by dublclicking on an input/texarea in the Form
     * Add one or more values to an input field, keeping its original value
     * @ jqLoadArchivesLayer: Common Layer for everything added by Ajax
     * @ .length: Selectors checkd by their .length are added in the Layer by Ajax
     * 			and define the type of the source to be added 
     */
    if ($sx_aj("#jqLoadArchivesLayer").length) {
        $sx_aj("#sxAddEdit input:text, #sxAddEdit textarea").dblclick(function () {
            if ($sx_aj("#jqLoadArchivesLayer .jqAccessories").length || ($sx_aj("#jqLoadArchivesLayer .jqInsertImages").length)) {
                var new_values = "";
                var suffix = ";";
                // If accessories, replace (;) with (,)
                if ($sx_aj("#jqLoadArchivesLayer .jqAccessories").length) {
                    var suffix = ",";
                }
                var $targer = $sx_aj(this);
                var $old_value = $targer.val();
                $sx_aj("#jqLoadArchivesLayer input:checkbox:checked").each(function () {
                    if (new_values != "") {
                        new_values += suffix
                    }
                    new_values += $sx_aj(this).val();
                });
                if (new_values != "") {
                    // Keep allready existing values
                    if ($old_value != "") {
                        $old_value += suffix
                    }
                    $targer.val($old_value + new_values);
                };
            } else if ($sx_aj("#jq_InsertRootVariables").length) {
                $sx_aj(this).val($sx_aj("#jq_InsertRootVariables input[name='RootColors']:checked").val());
            } else if ($sx_aj("#jqLoadArchivesLayer .jqInsertText").length) {
                var $targer = $sx_aj(this);
                var tag_name = $targer[0].tagName;
                var checked_name = $sx_aj("#jqLoadArchivesLayer input:radio:checked").val();
                if (tag_name == 'INPUT') {
                    var new_value = $sx_aj('#jqLoadArchivesLayer input[name="' + checked_name + '"]').val();
                    $targer.val(new_value)
                } else if (tag_name == 'TEXTAREA') {
                    var new_value = $sx_aj('#jqLoadArchivesLayer textarea[name="' + checked_name + '"]').text();
                    $targer.html(new_value)
                }
            }
        });
    }

    $sx_aj('#startClock').click(function () {
        var sThis = $sx_aj(this);
        sThis.closest('form').submit(function () {
            sThis.prop("disabled", true);
        });
        var iSec = sThis.data("sec");
        if (iSec == "" || iSec == null || iSec == undefined) {
            iSec = 660
        }
        var counter = iSec;
        setInterval(function () {
            counter--;
            if (counter >= 0) {
                span = document.getElementById("count");
                span.innerHTML = "You can send Next Email List in <b>" + counter + "</b> seconds";
            }
            if (counter === 0) {
                span.innerHTML = "You can now post <b>Next</b> Email List! Please check if your <b>session</b> is active.";
                sThis.prop("disabled", false);
                clearInterval(counter);
            }
        }, 1000);
    });


    /*
        Initialize functions defined utside $sx_aj() - to be used by ajax-loades pages
        Not neccassary if they are not used in other pages
    */

    sxAjaxLoadArchives();
    sx_LoadSortTableRows();
});

/*
    Define here, outside the $sx_aj(), the functions used by ajax-loaded pages
    Initialize them at the end of those pages
    Initialize them in $sx_aj() only if they are also used in ordinary loaded pages
*/

var sx_LoadSortTableRows = function () {
    $sx_aj(".jqSortTableRows").on("click", function () {
        var $table = $sx_aj("#" + $sx_aj(this).attr("data-id"))
        var rows = $table.find('tr').get();
        rows.sort(function (a, b) {
            var keyA = $sx_aj(a).attr('data-row');
            var keyB = $sx_aj(b).attr('data-row');
            if (keyA < keyB) return 1;
            if (keyA > keyB) return -1;
            return 0;
        });
        //alert(rows)
        $sx_aj.each(rows, function (index, row) {
            $table.children('tbody').append(row);
        });
    });
}


/*
    A forms that triggers an ajax-loaded page 
    Must be initialized both here and in the ajax-loaded page
*/
var sxAjaxLoadArchives = function () {
    $sx_aj('#jqLoadSelectForm, .jqLoadSelectForm').submit(function (e) {
        e.preventDefault();
        var frm = $sx_aj(this);
        var $data = frm.serialize();
        var $typ = frm.attr('method');
        var $url = frm.attr('action');
        var $layer = frm.data('url');
        if ($layer == "" || $layer == null || $layer == undefined) {
            $layer = "jqLoadArchivesLayer"
        }
        $sx_aj("#" + $layer).html('<div style="padding: 20px 0; text-align: center"><img src="images/wait.gif" border: 0;"></div>');
        $sx_aj.ajax({
            cache: false,
            type: $typ,
            url: $url,
            data: $data,
            dataType: "html",
            scriptCharset: "utf-8",
            success: function (result) {
                $sx_aj("#" + $layer).html(result);
            },
            error: function (xhr, status, error) {
                $sx_aj("#" + $layer).html("<h3>Error! Please contact the administrator of the site.</h3>");
                    console.error('Error:', status, error);
            }
        });
    });

    // Add multiple Author IDs, for both Book and Text Authors 
    if ($sx_aj(".jqAddAuthor").length && $sx_aj("#jqInsertAthors").length) {
        var strInsertAthors = $sx_aj("#jqInsertAthors");
        var addInput = "";
        var delInput = "";
        var sxSign = ",";
        $sx_aj(".jqAddAuthor input[type=checkbox]").on('change', function () {
            var self = $sx_aj(this);
            if (self.is(":checked")) {
                if (addInput != "") {
                    addInput += sxSign
                }
                addInput += self.val();
            } else {
                delInput = self.val()
                if (addInput == delInput) {
                    addInput = ""
                } else {
                    var arr = addInput.split(",");
                    arr.splice($sx_aj.inArray(delInput, arr), 1);
                    addInput = arr.join(",")
                }
            }
            strInsertAthors.val(addInput)
        });
    }



    /**
     * Search for distinct values which belong to a particular ID, defined from a Selection Input, 
     * and place the results in an other Selection Input
     */
    if ($sx_aj(".jqGetDistinctWhereValues").length) {
        var strInsertAthors = $sx_aj("#jqInsertAthors");
        var addInput = "";
        var delInput = "";
        var sxSign = ",";
        $sx_aj(".jqAddAuthor input[type=checkbox]").on('change', function () {
            var self = $sx_aj(this);
            if (self.is(":checked")) {
                if (addInput != "") {
                    addInput += sxSign
                }
                addInput += self.val();
            } else {
                delInput = self.val()
                if (addInput == delInput) {
                    addInput = ""
                } else {
                    var arr = addInput.split(",");
                    arr.splice($sx_aj.inArray(delInput, arr), 1);
                    addInput = arr.join(",")
                }
            }
            strInsertAthors.val(addInput)
        });
    };


    $sx_aj("#jq_selectTemplateID input[name='SectionTemplateID']").on('click', function () {
        var int_TemplateID = $sx_aj(this).val();
        var str_TemplateID = $sx_aj(this).attr('data-id');
        var replace = false;
        if (confirm("Are you sure you want to Set or Change the Option Value\n " +
            "for the Template ID to " + int_TemplateID + ": (" + str_TemplateID + ")?")) {
            replace = true;
        } else {
            replace = false;
        }
        if (replace) {
            $sx_aj("#sxAddEdit select[name='TemplateID'] option[value='" + int_TemplateID + "']").prop("selected", true)
        }
    });

};

/*
    Loads email lists from a textares to an input element of the Form for sending multiple newsletters 
    - used only by ajax-loaded pages, where it is initialized.
    - Not nedd to initialize it here
*/
var sxAddEmailLists = function () {
    if ($sx_aj("#jqLoadEmailLayer").length) {
        $sx_aj("#jqLoadEmailLayer button").click(function () {
            var sTArea = $sx_aj("#textarea_" + $sx_aj(this).data("id"))
            var strAreaText = sTArea.val();
            $sx_aj("#jqEmailList").val(strAreaText);
            $sx_aj("#jqEmailListSource").val($sx_aj(this).data("source"))
            sTArea.css({
                "background": "#aaffdd"
            });
            var arrAT = strAreaText.split(";")
            var strMails = ""
            var i, r;
            for (i = 0; i < arrAT.length; ++i) {
                r = arrAT[i].split(",")
                if (strMails != "") {
                    strMails = strMails + ", "
                }
                strMails = strMails + r[1]
            }
            $sx_aj("#jqEmailListView").text(strMails);
        })
    }
};