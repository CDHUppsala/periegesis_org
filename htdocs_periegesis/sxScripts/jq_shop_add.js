/*	
    ===========================================================================
    ALL SHOPPING FUNCTIONS
    ===========================================================================
*/
var $sx = jQuery.noConflict();
$sx(document).ready(function () {
    if ($sx(".jqButtonAdd").length) {
        sxAjaxAddToCard();
    }
    if ($sx(".jqRadioAsides input[type=radio]").length) {
        sxAjaxAddAccessiries();
    }
    if ($sx(".jq_AddSelectedAccessory").length) {
        sx_AddSelectedAccessiries();
    }
    if ($sx(".jq_AddProductToFavorites").length) {
        sxAddProductToFavorites();
    }
});

var sxAddProductToFavorites = function () {
    $sx(document).on('click', '.jq_AddProductToFavorites', function (event) {
        //$sx('.jq_AddProductToFavorites').on('click', function (event) {
        event.preventDefault();
        let nocache = new Date().getTime();
        let productId = $sx(this).data('id');
        let thisHasSpanChild = $sx(this).has('span').length > 0;
        // Store 'this' in a variable before the AJAX call
        var $element = $sx(this);

        $sx.ajax({
            url: 'orderAdd.php',
            type: 'POST',
            data: {
                addToFavorites: true,
                ProductID: productId
            },
            success: function (response) {

                if (thisHasSpanChild) {
                    // Favorites are added/removed from product carts, which all have the SPAN element
                    var $span = $element.find('span');
                    if ($span.hasClass('star_icon')) {
                        // Since there might be multiple carts with the same product
                        $sx('.jq_AddProductToFavorites[data-id="' + productId + '"] span').attr('class', 'checkmark_icon');
                    } else {
                        $sx('.jq_AddProductToFavorites[data-id="' + productId + '"] span').attr('class', 'star_icon');
                    }
                } else {
                    /*
                        Favorites are removed from the Fovorites List, which do not have the SPAN element
                        So, change the icon in the SPAN element within all related product carts (which have the same Product ID)
                    */
                    $sx('.jq_AddProductToFavorites[data-id="' + productId + '"] span').attr('class', 'star_icon');
                }

                let ajaxResponse; // Variable to store the response

                // Perform a single AJAX request to get the response, instead of 2 load requests
                $sx.ajax({
                    url: "ajax_Update.php?pg=favorites&cache=" + nocache,
                    type: "GET",
                    success: function (response) {
                        ajaxResponse = response; // Store the response

                        if ($sx("#ajaxUpdateTopFavorites").length) {
                            $sx('#ajaxUpdateTopFavorites').html(ajaxResponse);
                        }

                        if ($sx("#ajaxUpdateMainFavorites").length) {
                            $sx('#ajaxUpdateMainFavorites').html(ajaxResponse);
                        }
                    },
                    error: function (xhr) {
                        console.error("Error loading favorites:", xhr.status, xhr.statusText);
                        alert("An error occurred while loading favorites. Please try again later.");
                    }
                });
            },
            error: function (xhr) {
                console.error("Error adding favorites:", xhr.status, xhr.statusText);
                alert("An error occurred while adding favorites. Please try again later.");
            }
        });
    });
}
var sxAjaxAddAccessiries = function () {
    $sx(".jqRadioAsides input[type=radio]").on("click", function () {
        sx_countPrice()
    });
    sx_countPrice();

    function sx_countPrice() {
        var startPrice = parseFloat($sx("#StartAccessoryPrice").val().replace(",", "."));
        var sxSelects = $sx(".jqRadioAsides input[type=radio]:checked")
        var intAddPice = 0;
        var lastID = "";
        if (sxSelects.length) {
            sxSelects.each(function (i) {
                var sID = $sx(this).data("id");
                var sNotes = $sx(this).data("notes");
                var sName = $sx(this).data("name");
                var sImage = $sx(this).data("image");
                if (sImage != "") {
                    if (sID != lastID) {
                        $sx("#accessoryImage").append("<img style='display:none' id='" + sID + "' src='../imgPG/spacer.gif'>")
                        //sxImagePreview()
                    }
                    $sx("#" + sID)
                        .attr({
                            "src": "../imgProducts/" + sImage,
                            "alt": sName,
                            "title": sNotes
                        })
                        .show(300);
                } else if (typeof $sx("#" + sID) != 'undefined') {
                    $sx("#" + sID)
                        .attr({
                            "src": "../imgPG/spacer.gif",
                            "alt": "",
                            "title": ""
                        })
                        .hide(300);
                }
                var iThis = $sx(this).data("price");
                if (/,/.test(iThis)) {
                    iThis = iThis.replace(/,/g, ".");
                }
                intAddPice += parseFloat(iThis);
                lastID = sID
            });
        }
        intAddPice += startPrice;
        $sx("#SumAccessoryPrices").val(intAddPice);
        $sx("#ShowSumAccessoryPrice span").text(intAddPice + ' €');
        $sx("#ShowSumAccessoryPrice").show(300);
        if (startPrice == intAddPice) {
            $sx("#ShowSumAccessoryPrice").hide(300);
        }
    };
};

var sx_AddSelectedAccessiries = function () {
    var jq_basic = $sx(".jq_AddSelectedAccessory");

    if (jq_basic.find("select").length) {

        var jq_Select = jq_basic.find("select");
        var FirstAccessID = parseInt(jq_Select.find(":selected").val());
        var FirstPrice = jq_Select.find(":selected").data("price");
        if (FirstAccessID > 0) {
            sx_AddToPrice(FirstPrice, JSON.stringify([FirstAccessID]));
        }
        jq_Select.on("change", function () {
            var arrAccess = [];
            var AddPice = 0;
            var jsonAccess = '';
            jq_Select.each(function (i) {
                var intAccess = $sx(this).find(":selected").val();
                if (intAccess > 0) {
                    arrAccess.push(parseInt(intAccess));
                }
                jsonAccess = JSON.stringify(arrAccess)
                AddPice += $sx(this).find(":selected").data("price");
            });
            sx_AddToPrice(AddPice, jsonAccess)
            arrAccess = [];
        });

    } else if (jq_basic.find("input[type=radio]").length) {
        var FirstAccessID = parseInt(jq_basic.find("input[type=radio]:checked").val());
        var FirstPrice = jq_basic.find("input[type=radio]:checked").data("price");
        if (FirstAccessID > 0) {
            sx_AddToPrice(FirstPrice, JSON.stringify([FirstAccessID]));
        }
        jq_basic.find("input[type=radio]").on("click", function () {
            var arrAccess = [];
            var AddPice = 0;
            var jsonAccess = '';
            jq_basic.each(function (i) {
                var intAccess = $sx(this).find("input[type=radio]:checked").val();
                if (intAccess > 0) {
                    arrAccess.push(parseInt(intAccess));
                }
                jsonAccess = JSON.stringify(arrAccess)
                AddPice += $sx(this).find("input[type=radio]:checked").data("price");
            });
            sx_AddToPrice(AddPice, jsonAccess)
            arrAccess = [];
        });
    }

    function sx_AddToPrice(intAddPice, jsonAccess) {
        if (/,/.test(intAddPice)) {
            intAddPice = intAddPice.replace(/,/g, ".");
        }
        intAddPice = parseFloat(intAddPice);

        $sx("#JsonAccessoryIDs").val(jsonAccess);
        $sx("#SumAccessoryPrice").val(intAddPice);
        $sx("#ShowSumAccessoryPrice span").text(intAddPice + ' €')
        if (parseInt(intAddPice) === 0) {
            $sx("#ShowSumAccessoryPrice").hide(300);
        } else {
            $sx("#ShowSumAccessoryPrice").show(300);
        }
    }
};

var sxAjaxAddToCard = function () {
    var addRequest;
    var addType;
    $sx(".jqButtonAdd").click(function () {
        addRequest = "&addToCart=yes";
        addType = "Cart";
    });
    $sx(".jqButtonWishes").click(function () {
        addRequest = "&addToWishes=yes"
        addType = "Wishes";
    });
    // Sends the form values to orderAdd.php (url=form action) and than uppdates relevant elements
    $sx('form.jqAddToCartWishes').submit(function (e) {
        e.preventDefault();
        var nocache = new Date().getTime();
        var frm = $sx(this);
        var $data = frm.serialize() + addRequest;
        $sx.ajax({
            cache: false,
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: $data,
            success: function (result) {
                if (addType == "Cart") {
                    if ($sx("#ajaxUpdateOrder").length > 0) {
                        $sx('#ajaxUpdateOrder').load("ajax_Update.php?pg=cart&cache=" + nocache, function (response, status, xhr) {
                            if (status == "error") {
                                alert("Error1: " + xhr.status + " " + xhr.statusText);
                            }
                        });
                    };
                    if ($sx("#ajaxUpdateOrderTop").length > 0) {
                        $sx('#ajaxUpdateOrderTop').load("ajax_Update.php?pg=cartTop&cache=" + nocache, function (response, status, xhr) {
                            if (status == "error") {
                                alert("Error2: " + xhr.status + " " + xhr.statusText);
                            }
                        });
                    };
                } else {
                    if ($sx("#ajaxUpdateWishes").length > 0) {
                        $sx('#ajaxUpdateWishes').load("ajax_Update.php?pg=wishes&cache=" + nocache, function (response, status, xhr) {
                            if (status == "error") {
                                alert("Error3: " + xhr.status + " " + xhr.statusText);
                            }
                        });
                    };
                };
                //Appands the results from tne url:orderAdd.php - actually a javascript message
                $sx('body').append(result);
            },
            error: function (xhr, status, error) {
                alert('Request Faild' + error)
            }
        });
    });
};