var $sx = jQuery.noConflict();
$sx(document).ready(function () {
    var rightToggle = 0;
    var leftToggle = 0;
    $sx("#jqNavRight").click(function () {
        if (rightToggle == 0) {
            $sx("main .right").animate({
                left: '20%'
            }, 400);
            rightToggle = 1;
        } else if (rightToggle == 1) {
            $sx("main .right").animate({
                left: '100%'
            }, 400);
            rightToggle = 0;
        };
        $sx("main .left").animate({
            right: '100%'
        }, 400)
        leftToggle = 0;
    });
    $sx("#jqNavLeft").click(function () {
        if (leftToggle == 0) {
            $sx("main .left").animate({
                right: '20%'
            }, 400);
            leftToggle = 1;
        } else if (leftToggle == 1) {
            $sx("main .left").animate({
                right: '100%'
            }, 400);
            leftToggle = 0;
        };
        $sx("main .right").animate({
            left: '100%'
        }, 400)
        rightToggle = 0;
    });

    if ($sx("#jqAccordionNav div").length) {
        $sx("#jqAccordionNav div").click(function () {
            $sx(this).toggleClass("open")
                .next("ul").slideToggle(400)
                .end()
                .parent()
                .siblings()
                .find("div").removeClass("open")
                .end()
                .find("ul").hide(400);
        });
    }

});

$sx(window).on('load', function () {
    if ($sx("#thumpsBG li").length) {
        var $currpos = 0;
        var $elCount = $sx("#thumpsBG li").length - 1;
        $sx("#arrowPhotoNav").css("display", "block");
        $sx("#thumpsBG li").click(function () {
            $currpos = $sx(this).index();
            var $thump = $sx(this);
            var $ph_src = $thump.attr('photo_src');
            var $ph_info = $thump.attr('photo_info');

            var $ph_el = $sx("#photoBG");
            $ph_el.fadeOut(500, function () {
                $ph_el.css({
                    "background-image": "url(" + $ph_src + ")",
                    "display": "none"
                }).fadeIn(600);
            });
            $sx("#photoInfo").html($ph_info);
        });

        //Show first thump
        if (int_PhotoID === "undefined") {int_PhotoID = 0};
        if (int_PhotoID > 0) {
            $sx("#img_" + int_PhotoID).click()
        } else {
            $sx("#thumpsBG > li:eq(0)").click();
        }

        //Change image by arrows
        $sx("#arrowPhotoNav .navArrows").click(function () {
            var $arrow = $sx(this);
            if ($arrow.attr("id") == "left") {
                if ($currpos == 0) {
                    $sx("#thumpsBG li:eq(" + $elCount + ")").click();
                } else {
                    $sx("#thumpsBG li:eq(" + ($currpos - 1) + ")").click();
                }
            } else {
                if ($currpos == $elCount) {
                    $sx("#thumpsBG li:eq(0)").click();
                } else {
                    $sx("#thumpsBG li:eq(" + ($currpos + 1) + ")").click();
                }
            }
        });
    }

    if ($sx("#jqBigPhoto").length) {
        $sx("#jqBigPhoto").click(function () {
            var sxMain = $sx("main");
            var sxMiddle = $sx("main .middle");
            var radioMobile = $sx(".navMarker").is(":visible");
            if (parseInt(sxMain.css("top"), 10) > 0) {
                sxMain.animate({
                    top: "0",
                    bottom: "0"
                }, 400);
                if (radioMobile === false) {
                    sxMiddle.css({
                        "z-index": "200"
                    }).animate({
                        left: "0",
                        right: "0"
                    }, 400);
                } else {
                    sxMiddle.css({
                        "z-index": "200"
                    });
                };
            } else {
                sxMain.animate({
                    top: "54px",
                    bottom: "100px"
                }, 400, function () {
                    if (radioMobile === false) {
                        sxMiddle.animate({
                            left: "20%",
                            right: "20%"
                        }, 400, function () {
                            sxMiddle.css({
                                "z-index": 0
                            });
                        });
                    } else {
                        sxMiddle.css({
                            "z-index": 0
                        });
                    };
                });
            };
        });

        $sx("body").keydown(function (e) {
            if (e.keyCode == 37) {
                $sx("#left").click();
            } else if (e.keyCode == 39) {
                $sx("#right").click();
            } else if (e.keyCode == 38 || e.keyCode == 40) {
                $sx("#jqBigPhoto").click();
            }
        });
    }

});