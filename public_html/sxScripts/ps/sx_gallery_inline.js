/*
    Responsive inline-block gallery with Full Screen View
    Public Sphere
==================================== */

var $sx = jQuery.noConflict();
$sx(function () {
    /**
     * A page can contain multiple gallery containers with the same class names
     * You open each time only one container as gallery
     */
    if ($sx(".jqps_inline_gallery").length) {
        /**
         * Add classes to give all gallery images the same height, with aspect ratio 16/9
         * object-fit: cover, for images with H/W < 1
         * object-fit: contain, for images with H/W >= 1
         */
        // Don't add classes if gallery contains an SVG image
        var check_galleryHasSvg = $sx('.jqps_inline_gallery img').is(function () {
            return this.src.toLowerCase().endsWith('.svg');
        });

        // set contain/cover classes depanding on the H/W ratio of images
        if (check_galleryHasSvg === false) {
            sx_addClassesToGalleryImages($sx)
        }
        var radio_showImageNumber = false;

        sx_startInlineGallery($sx, radio_showImageNumber);
    }
});

var sx_addClassesToGalleryImages = function ($sx) {
    var gImages = $sx('.jqps_inline_gallery img');

    gImages.each(function () {
        var $img = $sx(this);

        // Define the function to handle the class addition
        var handleImageLoad = function () {
            var originalWidth = this.naturalWidth;
            var originalHeight = this.naturalHeight;
            var aRatio = originalHeight / originalWidth;

            if (aRatio >= 1 || $img.attr('src').startsWith('v')) {
                $img.parent().removeClass('img_cover').addClass('img_contain');
            } else {
                $img.parent().removeClass('img_contain').addClass('img_cover');
            }
            //console.log('Original Width: ' + originalWidth + 'px');
            //console.log('Original Height: ' + originalHeight + 'px');
        };

        // Check if the image is already loaded
        if (this.complete) {
            handleImageLoad.call(this);
        } else {
            // Bind the load event to handle images that are not yet loaded
            $img.on('load', handleImageLoad);
        }
    });
};



var sx_startInlineGallery = function ($sx, radio_showImageNumber, repeat_cycler = false) {
    var $radio_repeat_cycler = repeat_cycler;
    var sx_Gallery = null;
    var sxLength = 0;
    $sx('.jqps_inline_gallery img').on('click', function () {
        if (sx_Gallery === null) {
            // Get the initial gallery container (of multiple possible)
            sx_Gallery = $sx(this).closest('div');
            sxLength = sx_Gallery.find("figure").length;
        }
        // Close the gallery if it is open, else open it
        if ($sx("#sxFixedScreen").length) {
            $sx("#sxFixedScreen").fadeTo(500, 0, function () {
                $sx("html body").css("overflow", "auto");
                $sx("#sxFixedScreen").remove();
            });
            // Resett default variable values
            sx_Gallery = null;
            sxLength = 0;
        } else {
            // Get from ckicked image the parent figure
            var sxFigure = $sx(this).parent();
            sxFigure.addClass("active").siblings().removeClass("active");
            var sxIndex = sxFigure.index();

            /**
             * Check if the attributes data-title and data-notes exist i figcaption
             * Galleries that do not use data-source do not include these attributes
             * If they exist, use them, else get the HTML of the figcaptions
             * which usually is the image name transformed to title.
             */
            var data_title = '';
            if (sxFigure.find("figcaption").attr('data-title')) {
                data_title = $sx.trim(sxFigure.find("figcaption").attr('data-title'));
            }
            var caption_notes = ""
            if (sxFigure.find("figcaption").length) {
                caption_notes = sxFigure.find("figcaption").html();
            }
            var data_notes = '';
            if (sxFigure.find("figcaption").attr('data-notes')) {
                data_notes = $sx.trim(sxFigure.find("figcaption").attr('data-notes'));
            }

            var sxNotes = '';
            if (data_notes == '') {
                if (caption_notes != '') {
                    sxNotes = caption_notes
                }
                if (data_title != '') {
                    if (sxNotes != '') {
                        sxNotes = '<b>' + data_title + '</b><br>' + sxNotes;
                    } else {
                        sxNotes = data_title;
                    }
                }
            } else {
                sxNotes = data_notes;
                if (data_title !== '') {
                    sxNotes = '<b>' + data_title + '</b><br> ' + sxNotes;
                } else if (caption_notes != '') {
                    sxNotes = '<b>' + caption_notes + '</b><br> ' + sxNotes;
                }
            }

            if (radio_showImageNumber || sxNotes == '') {
                sxNotes = "image " + (sxIndex + 1) + " / " + sxLength + ': ' + sxNotes
            }

            $sx("body").append('<div id="sxFixedScreen"></div>');
            var sx_FixedScreen = $sx("#sxFixedScreen");

            /**
             * Clone the initial Gallery to the Fixed Screen
             *  - and redefine the Gallery object by the cloned element.
             * Replace default static styling classes with the absolute one.
             * Add navigation HTML
             * Show the clicked image
            */
            sx_FixedScreen.append(sx_Gallery.clone(true, true));
            var sxGallery = $sx("#sxFixedScreen .jqps_inline_gallery");
            sxGallery
                .removeClass("ps_inline_gallery")
                .addClass("ps_inline_gallery_absolute")
                .append('<ul class="nav"></ul>')
                .find("figure")
                .fadeOut(0)
                .end()
                .find(".nav")
                .append(
                    '<li id="jq_nav-prev" class="nav-prev" title="You can also navigate with the Keyboard Arrows!"></li>' +
                    '<li class="notes"></li>' +
                    '<li id="jq_nav-next" class="nav-next" title="You can also navigate with the Keyboard Arrows!"></li>'
                )
                .append('<li class="nav-close" title="You can also close the gallery with the Page Up/Dn Keyboard Arrows!"></li>')
                .find(".notes")
                .html(sxNotes);
                
            sxGallery.find("figure figcaption").css("display", "none");

            sx_FixedScreen.fadeTo(500, 1, function () {
                $sx("html body").css("overflow", "hidden");
                sxGallery.find("figure.active").fadeIn(500);
            });

            var $active = sxGallery.find("figure.active");
            if ($radio_repeat_cycler === false) {
                if ($active.prev("figure").length === 0) {
                    sxGallery.find('#jq_nav-prev').css('visibility', 'hidden');
                }
                if ($active.next("figure").length === 0) {
                    sxGallery.find('#jq_nav-next').css('visibility', 'hidden');
                }
            }

            function cycleImages(pos) {
                var $radio_complete = false;
                $active = sxGallery.find("figure.active");

                if (pos == "prev") {
                    if ($active.prev("figure").length > 0) {
                        var $next = $active.prev("figure");
                        $radio_complete = true;
                    } else if ($radio_repeat_cycler) {
                        var $next = sxGallery.find("figure:last");
                        $radio_complete = true;
                    }
                } else if (pos == "next") {
                    if ($active.next("figure").length > 0) {
                        var $next = $active.next("figure")
                        $radio_complete = true;
                    } else if ($radio_repeat_cycler) {
                        var $next = sxGallery.find("figure:first");
                        $radio_complete = true;
                    }
                } else {
                    // Close the gallery
                    sxGallery.find("figure.active").find("img").click();
                }
                if ($radio_complete) {
                    sxGallery.find(".nav").fadeOut(300);
                    $active.fadeOut(300, function () {

                        var data_title = '';
                        if ($next.find("figcaption").attr('data-title')) {
                            data_title = $sx.trim($next.find("figcaption").attr('data-title'));
                        }
                        var caption_notes = ""
                        if ($next.find("figcaption").length) {
                            caption_notes = $next.find("figcaption").html();
                        }
                        var data_notes = '';
                        if ($next.find("figcaption").attr('data-notes')) {
                            data_notes = $sx.trim($next.find("figcaption").attr('data-notes'));
                        }

                        var sxNotes = '';
                        if (data_notes == '') {
                            if (caption_notes != '') {
                                sxNotes = caption_notes
                            }
                            if (data_title != '') {
                                if (sxNotes != '') {
                                    sxNotes = '<b>' + data_title + '</b><br>' + sxNotes;
                                } else {
                                    sxNotes = data_title;
                                }
                            }
                        } else {
                            sxNotes = data_notes;
                            if (data_title !== '') {
                                sxNotes = '<b>' + data_title + '</b><br> ' + sxNotes;
                            } else if (caption_notes != '') {
                                sxNotes = '<b>' + caption_notes + '</b><br> ' + sxNotes;
                            }
                        }

                        if (radio_showImageNumber) {
                            sxNotes = "image " + (sxIndex + 1) + " / " + sxLength + ': ' + sxNotes
                        }

                        sxGallery
                            .find(".nav")
                            .find(".notes")
                            .html(sxNotes)
                            .end()
                            .fadeIn(300);
                        $active.removeClass("active");
                        $next.addClass("active").fadeIn(300);
                        $active = $next;
                    });

                    if ($radio_repeat_cycler === false) {
                        if ($next.prev("figure").length === 0) {
                            sxGallery.find('#jq_nav-prev').css('visibility', 'hidden');
                        } else {
                            sxGallery.find('#jq_nav-prev').css('visibility', 'visible');
                        }

                        if ($next.next("figure").length === 0) {
                            sxGallery.find('#jq_nav-next').css('visibility', 'hidden');
                        } else {
                            sxGallery.find('#jq_nav-next').css('visibility', 'visible');
                        }
                    }

                }
            }

            $sx("body").keydown(function (e) {
                if ($sx("#sxFixedScreen").length) {
                    if (e.keyCode == 37) {
                        cycleImages("prev");
                    } else if (e.keyCode == 39) {
                        cycleImages("next");
                    } else if ((e.keyCode == 38 || e.keyCode == 40)) {
                        sxGallery.find("figure.active").find("img").click();
                    }
                }
            });

            sxGallery.find("[class*='nav-']").click(function () {
                if ($sx(this).hasClass("nav-prev")) {
                    cycleImages("prev");
                } else if ($sx(this).hasClass("nav-next")) {
                    cycleImages("next");
                } else {
                    cycleImages("close");
                }
            });

            var startX, endX;
            var threshold = 50; // Minimum distance for a swipe

            $sx('#sxFixedScreen').on('touchstart', function (e) {
                var touch = e.touches[0];
                startX = touch.clientX;
            });

            $sx('#sxFixedScreen').on('touchmove', function (e) {
                var touch = e.touches[0];
                endX = touch.clientX;
            });

            $sx('#sxFixedScreen').on('touchend', function (e) {
                var distance = endX - startX;

                if (distance > threshold && $active.prev("figure").length > 0) {
                    // Swipe right (previous image)
                    cycleImages("prev");
                } else if (distance < -threshold && $active.next("figure").length > 0) {
                    // Swipe left (next image)
                    cycleImages("next");
                }
            });

        }
    })

};
