/**
 * Manual image cycler, usually on the top of articles
 * You can have one or more in the same page
 * The first image is static and define the height of the cycler
 *  - All other images has absolut position and are animated by opacity and z-index
 */
var $sx = jQuery.noConflict();
$sx(function () {
    if ($sx('.jqImgCyclerManual').length) {
        $sx('.jqImgCyclerManual').each(function () {
            $sx(this).sxLoadManualImgCycler();
        });
    };
});

$sx.fn.sxLoadManualImgCycler = function () {
    var sx_This = $sx(this);

    /**
     * Select the first image and the first Thumb
     *  - Not neccessary if they are allready defined in HTML and css, but just in case.
     */
    sx_This.find('figure img').each(function () {
        $sx(this).css({
            "opacity": 0,
            "z-index": -1
        })
    }).eq(0).css({
        position: "static",
        opacity: 1,
        "z-index": 1
    });
    sx_This.find("li li:first-child").addClass('selected');

    /**
     * Check if the images contains data attributes and get them as notes
     * Only cyclers created by use of the Table Multi Data contain data attributes
     */

    var imgNotes = '';
    var imgLoop = sx_This.find('figure img').eq(0);
    if (imgLoop.attr('data-title') !== undefined) {
        imgNotes = imgLoop.attr('data-title');
    }
    if (imgLoop.attr('data-notes') !== undefined) {
        if (imgNotes != '') {imgNotes += '<br>';}
        imgNotes += imgLoop.attr('data-notes');
    }


    if (imgNotes != '') {
        sx_This.find('div').html(imgNotes);
    }


    /**
     * Cycler the images
     */
    sx_This.find('li li').click(function () {
        $sx(this)
            .addClass("selected")
            .siblings().removeClass("selected")
            .end()
            .closest("div")
            .find("figure img").eq($sx(this).index()).css({
                "z-index": 1
            }).stop().animate({
                opacity: 1
            }, 300)
            .siblings().stop().animate({
                opacity: 0,
                "z-index": -1
            }, 300);

        // Check for data attributes, if any
        imgLoop = sx_This.find('figure img').eq($sx(this).index());

        if (imgLoop.attr('data-title') !== undefined) {
            imgNotes = imgLoop.attr('data-title');
        }
        if (imgLoop.attr('data-notes') !== undefined) {
            if (imgNotes != '') {imgNotes += '<br>';}
            imgNotes += imgLoop.attr('data-notes');
        }
    

        if (imgNotes != '') {
            sx_This.find('div').html(imgNotes);
        }
    });

    sx_This.find("li[class*='more-']").click(function () {
        var parent = $sx(this).parent();
        var list = parent.find("li li");
        var listLength = list.length;
        var listSelected = parent.find("li.selected").index();
        var nextList = listSelected - 1
        if ($sx(this).attr("class") == "more-next") {
            nextList = listSelected + 1
        };
        if (nextList < 0) {
            nextList = (listLength - 1)
        };
        if (nextList > (listLength - 1)) {
            nextList = 0
        };
        list.eq(nextList).click();
    });
};