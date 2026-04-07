/**
 * 	Public Sphere
 *	Full Screen Overlay Gallery Box
 *	Selects all images in the page that have a figure wich have an attribute "data-lightbox"
 *		with the SAME NAME as the figure of the clicked image and creates an Overlay Gallery
 */

var $sx = jQuery.noConflict();
$sx(function () {
    if ($sx("figure[data-lightbox]").length) {
        var sxGalleryObject = $sx("figure[data-lightbox]");
        sxGalleryObject.css("cursor", "zoom-in");
        sx_startBoxGallery(sxGalleryObject);
    }
});


var sx_startBoxGallery = function (sxGalleryObject) {
    sxGalleryObject.on('click', function (event) {
        /**
         * Add a tempora class to the image of the CLICKED figure to find and show it first
         * Images are cloned, so the class will follow with it.
         */
        $sx(event.target).addClass("trace_index");


        /**
         * Create and append to body a fixed box and a GALLERY BOX
         */
        $sx("html body").append('<div class="sx_lightbox_overlay_fixed"></div>');
        var sx_GalleryBoxFixed = $sx(".sx_lightbox_overlay_fixed");
        sx_GalleryBoxFixed.append('<div class="sx_lightbox_overlay"></div>');
        var sx_GalleryBox = $sx(".sx_lightbox_overlay");

        /**
         * A page can contain multiple lightboxes with different attribyte name
         *  - so, you have to define the lightbox that has been clicked,as the sources of the gallery
         * Get all images from figures with the same attribute name as the CLICKED figure and for each one
         * 	- create a figure element, each for every image, and append them to the above GALLERY BOX
         * 	- clone each image and append it to its corresponding figure
         * Basically, you clone all images of figures with the same attribute name
         * In case of manual cycler (img_cycler_manual jqImgCyclerManual), all images are in the same figure
         */
        var source_GalleryObjects = $sx(
            "figure[data-lightbox=" + $sx(this).attr("data-lightbox") + "]"
        ).find('img');

        source_GalleryObjects.each(function () {
            sx_GalleryBox.append("<figure></figure");
            sx_GalleryBox.find("figure:last").append($sx(this).clone());
        });
        /**
         *  change z-index and opacity of images, just in case they came from manual cycler,
         */
        sx_GalleryBox.find('img').css({ 'z-index': 1, 'opacity': 1 });

        /**
         * Now, we have a container with all relevant figures and images
         */
        var sxLength = sx_GalleryBox.find("figure").length;

        /**
         * Trace back the clicked figure through the temporal class of its image
         * 	- define it as aactive
         * 	- get information (Notes) abot the image
         */
        var sxClickedFigure = sx_GalleryBox.find(".trace_index").parent();
        var sxIndex = sxClickedFigure.index() + 1;
        var sxNotes = sxClickedFigure.find("img").attr("alt");
        sxClickedFigure.addClass("active").siblings().removeClass("active");

        /**
         * Remove the temporal class from the DOM-image
         */
        source_GalleryObjects.removeClass("trace_index");

        /**
         * Append elements for Navigation and Notes and Add notes about the active image
         */
        sx_GalleryBox
            .find("figure")
            .fadeOut(0)
            .end()
            .prepend('<div class="nav-close" title="Close Gallery"></div>')
            .append('<ul class="nav"></ul>')
            .find(".nav")
            .append(
                '<li class="nav-prev" title="You can also navigate with the Keyboard Arrows!"></li>' +
                '<li class="notes"></li><li class="nav-next" title="You can also navigate with the Keyboard Arrows!"></li>'
            )
            .find(".notes")
            .html("[<b> " + sxIndex + "/" + sxLength + "</b>] " + sxNotes);

        /**
         * Show the active image in full screen (make the fixed gallery box visible)
         */
        $sx("html body").css("overflow", "hidden");
        sx_GalleryBoxFixed.fadeTo(500, 1, function () {
            sx_GalleryBox.find("figure.active").fadeIn(500);
        });

        /**
         * Manually cyrcle the gallery
         */
        function cycleImages(pos) {
            var sx_active = sx_GalleryBox.find("figure.active");
            if (pos == "prev") {
                var sx_next =
                    sx_active.prev("figure").length > 0
                        ? sx_active.prev("figure")
                        : sx_GalleryBox.find("figure:last");
            } else {
                var sx_next =
                    sx_active.next("figure").length > 0
                        ? sx_active.next("figure")
                        : sx_GalleryBox.find("figure:first");
            }
            sxNotes = sx_next.find("img").attr("alt");
            sx_GalleryBox
                .find(".nav .notes")
                .html("[<b>" + sx_next.index() + "/" + sxLength + "</b>] " + sxNotes);

            sx_active.fadeOut(200, function () {
                sx_active.removeClass("active");
                sx_next.addClass("active").fadeIn(400);
            });
        }

        sx_GalleryBox.find("[class*='nav-']").click(function () {
            if ($sx(this).hasClass("nav-prev")) {
                cycleImages("prev");
            } else {
                cycleImages("next");
            }
        });

        sx_GalleryBox.find("figure img, .nav-close").click(function () {
            $sx(".sx_lightbox_overlay_fixed").fadeTo(500, 0, function () {
                $sx("html body").css("overflow", "auto");
                $sx(".sx_lightbox_overlay_fixed").remove();
            });
        });

        $sx("html body").keydown(function (e) {
            if (e.keyCode == 37) {
                cycleImages("prev");
            } else if (e.keyCode == 39) {
                cycleImages("next");
            } else if (
                (e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 27) &&
                $sx(".sx_lightbox_overlay_fixed").length
            ) {
                sx_GalleryBox.find("figure img").click();
            }
        });
    });
};
