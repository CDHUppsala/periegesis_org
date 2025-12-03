/*
V 2022-03
Image Ratio:	0.5, 0.36, 0.4, 0.5625, 0.6, 0.75, 1, 1.5 
Image Size:     cover, contain
Efffect Mode:   fade_both, fade_active, move_left_right, move_right_left, move_top_bottom, start_top_left, end_top_left, end_top_right
Thumps Type:    box, number, image
Thumps Place:   default_place image_left image_center image_right
Description Place:	default_place image_bottom 
*/

var $psx = jQuery.noConflict();
$psx(function () {
    if ($psx('#jq_sx_slider').length) {
        $psx('#jq_sx_slider').sx_get_slider()
    };
});

$psx.fn.sx_get_slider = function () {
    var sx_slider = $psx(this);
    var sxContainer = sx_slider.find('.sx_container');
    var sxFigure = sx_slider.find('figure');
    var sxImages = sx_slider.find('figure img');
    var sxImagesLength = sxImages.length;
    var sxFirstImage = sxImages.eq(0);

    /*
        1. Set the H/W ratio for the image
            - Check first if there exist a defined H/W ratio (0.5, 0.28, 0.36, 0.4, 0.5625, 0.6, 0.75, 1, 1.5)
            - If not, get the ratio from the First Image
        Use this ratio to set the padding of the element <figure>
            - Images are desplayed in absolut position within that element
        Finally, make the first image visible and hide the rest
    */
    if (sx_slider.attr('data-ratio')) {
        var sxPadding = Math.floor(sx_slider.attr('data-ratio') * 100);
    } else {
        var sxPadding = Math.floor((sxFirstImage.height() / sxFirstImage.width()) * 100);
    }
    sxFirstImage.css({
        'z-index': 2
    }).addClass('active')
        .siblings('img').css({
            'z-index': -1,
            'display': 'none'
        });
    sx_slider.find('figure').animate({
        'padding-top': sxPadding + '%'
    }, 400);

    /*
        2. Append alla required HTML Elements:
            - For Thembs navigation
            - For Arrow navigation
        Create than the default Thumps Type (rectagle including numbers)
    */
    sx_slider.append('<ul class="sx_thumbs"></ul>');
    sx_slider.append('<ul class="sx_nav"><li class="nav-prev"></li><li class="description"></li><li class="nav-next"></li></ul>')
    var sxThumbs = sx_slider.find(".sx_thumbs");
    for (i = 1; i < sxImagesLength + 1; i++) {
        sxThumbs.append('<li class="thamp_list"><span>' + i + '</span></li>');
    }

    /*
        3. Change the default content of Thumps if required:
            - to include text, if the source of slider is Events
            - to include images, if the Thumps Type is images
            - to hide numbers (within <span>), if the Thumps Type is Box
        Set always the first Thump to Active
    */
    var sxThumbsList = sxThumbs.find('li.thamp_list');

    if (sx_slider.attr('data-source') == 'Events') {
        sxThumbs.addClass('thumbs_as_text')
        for (i = 0; i < sxImagesLength; i++) {
            if (sxImages.eq(i).attr('data-thumb').length) {
                sxThumbsList.eq(i).html(sxImages.eq(i).attr('data-thumb'));
            }
        }
    } else if (sx_slider.attr('data-type') == 'image') {
        sxThumbs.addClass('thumbs_as_imgs')
        for (i = 0; i < sxImagesLength; i++) {
            sxThumbsList.eq(i).html('<img src="' + sx_slider.find('figure img').eq(i).attr('src') + '" attr="">');
        }
    } else if (sx_slider.attr('data-type') == 'box') {
        sxThumbsList.addClass('hide_numbers');
    }
    sxThumbsList.eq(0).addClass("active");

    /*
        4. Change the value of sx_mode to switch between alternative effect modes:
            - fade_both, fade_active, move_left_right, move_top_bottom, 
                start_top_left, end_top_left, end_top_right
        You can define the default mode here or place it as variable 
            in the data-mode attribute of the cycler container 
    */
    var sx_mode = 'move_left_right';
    if (sx_slider.attr('data-mode')) {
        sx_mode = sx_slider.attr('data-mode');
    }

    /*
        5. Define the required place of Image Description and Thumbs Navigation
            - The default palces (default_place) are on the margin uder the images
                - set placies to empty if they have default values
            - Alternative places for Thumps signify predifined css classes
    */
    var sx_desc_place = "";
    if (sx_slider.attr('data-desc_place')) {
        sx_desc_place = sx_slider.attr('data-desc_place');
        if (sx_desc_place == "default_place") {
            sx_desc_place = "";
        }
    }
    var sx_thumbs_place = "";
    if (sx_slider.attr('data-thamp_place')) {
        sx_thumbs_place = sx_slider.attr('data-thamp_place');
        if (sx_thumbs_place == "default_place") {
            sx_thumbs_place = "";
        }
    }

    /*
        6. Detach and move (appent or prepend) Description and Thumps in
            requested places if they have values other than the defaults'
            - They are moved to the class .sx_container, on the bottom of <figure>
            - Default places are under the <figure>
                Thumps: default_place image_left image_center image_right
                Description: default_place image_bottom
            - Description is set to default if Thumps place is image_center
    */
    if (sx_thumbs_place == "image_center") {
        sxThumbs.addClass(sx_thumbs_place)
            .detach().appendTo(sxContainer);
        var sx_desc_place = "";
        var sx_thumbs_place = "";
    }

    if (sx_desc_place != "" && sx_thumbs_place == "") {
        var sx_desc = sx_slider.find('.description')
        sx_desc.detach().appendTo(sxContainer);
    }

    if (sx_thumbs_place != "") {
        if (sx_desc_place == "image_bottom") {
            var sx_desc = sx_slider.find('.description');
            if (sx_thumbs_place == "image_left") {
                sx_desc.addClass('description_right')
                    .detach().appendTo(sxThumbs);
            } else if (sx_thumbs_place == "image_right") {
                sx_desc.addClass('description_left')
                    .detach().prependTo(sxThumbs);
            }
        }
        sxThumbs.addClass(sx_thumbs_place).detach().appendTo(sxContainer);
    }

    var sx_title = "";
    if (sxFirstImage.attr('data-title').length) {
        sx_title = sxFirstImage.attr('data-title');
        if (sxFirstImage.attr('data-href') != undefined && sxFirstImage.attr('data-href').length) {
            sx_title = '<a href="' + sxFirstImage.attr('data-href') + '">' + sx_title + '</a>';
        }
        sx_slider.find('.description').html('<h1>' + sx_title + '</h1>');
        if (sxFirstImage.attr('data-notes') != undefined && sxFirstImage.attr('data-notes').length) {
            sx_slider.find('.description').append('<p>' + sxFirstImage.attr('data-notes') + '</p>');
        }
        if (sxFirstImage.attr('data-datetime') != undefined && sxFirstImage.attr('data-datetime').length) {
            sx_slider.find('.description p').append('<span> ' + sxFirstImage.attr('data-datetime') + '</span>');
        }
    }

    var sx_sliderInterval;

    function startInterval() {
        sx_sliderInterval = setInterval(function () {
            sx_cycleImages();
        }, 3000)
    };

    function stopInterval() {
        clearInterval(sx_sliderInterval);
    };

    if (typeof sx_sliderInterval != "undefined") {
        stopInterval();
    }
    startInterval();

    sx_slider
        .bind('mouseenter', function () {
            stopInterval();
        })
        .bind('mouseleave', function () {
            if (sx_slider.attr('data-mode')) {
                sx_mode = sx_slider.attr('data-mode');
            }
            stopInterval();
            startInterval();
        });

    sxThumbs
        .bind('mouseenter', function () {
            if (sx_slider.attr('data-mode')) {
                sx_mode = sx_slider.attr('data-mode');
            }
        })

    sx_slider.find("[class^='nav-']").click(function () {
        if ($psx(this).hasClass('nav-prev')) {
            sx_mode = 'move_right_left';
            sx_cycleImages('prev');
        } else {
            sx_mode = 'move_left_right';
            sx_cycleImages('next');
        }
    });

    sx_slider.find("li.thamp_list").click(function () {
        if (!$psx(this).hasClass('active')) {
            sx_cycleImages($psx(this).index("li.thamp_list"));
        }
    });

    $psx(window).on("focus", function () {
        stopInterval();
        startInterval();
    }).on("blur", function () {
        stopInterval();
    });

    /**
     * Touch Functions for mobiles
     */

    function restrat_after_touch() {
        if (sx_slider.attr('data-mode')) {
            sx_mode = sx_slider.attr('data-mode');
        }
        stopInterval();
        startInterval();
    }

    var startX, endX;
    var threshold = 50; // Minimum distance for a swipe

    sxImages.on('touchstart', function (e) {
        var touch = e.touches[0];
        startX = touch.clientX;
    });

    sxImages.on('touchmove', function (e) {
        var touch = e.touches[0];
        endX = touch.clientX;
    });

    sxImages.on('touchend', function (e) {
        var distance = endX - startX;
        var radioTuchEffect = false;
        if (distance > threshold) {
            stopInterval();
            sx_mode = 'move_left_right';
            sx_cycleImages("prev");
            radioTuchEffect = true;
        } else if (distance < -threshold) {
            stopInterval();
            sx_mode = 'move_right_left';
            sx_cycleImages("next");
            radioTuchEffect = true;
        }
        if (radioTuchEffect) {
            setTimeout(restrat_after_touch, 6000);
        }
    });

    /*
        Cycle images with Alternative effect modes:
            You can remove the sx_mode variable and then 
            delete all alternatives except the one you prefere.
    */

    function sx_cycleImages(pos) {
        var sx_active = sx_slider.find('figure img.active');
        if ($psx.isNumeric(pos)) {
            var sx_next = sx_slider.find('figure img').eq(pos);
        } else if (pos == 'prev') {
            var sx_next = (sx_active.prev('figure img').length > 0) ? sx_active.prev('figure img') : sx_slider.find('figure img:last');
        } else {
            var sx_next = (sx_active.next('figure img').length > 0) ? sx_active.next('figure img') : sx_slider.find('figure img:first');
        };


        sx_slider.find(".sx_thumbs li.thamp_list").eq(sx_next.index())
            .stop()
            .addClass('active')
            .siblings()
            .removeClass('active')


        if (sx_next.attr('data-title').length) {
            sx_title = sx_next.attr('data-title');
            if (sx_next.attr('data-href').length) {
                sx_title = '<a href="' + sx_next.attr('data-href') + '">' + sx_title + '</h2>'
            }
            sx_slider.find('.description').stop(true, false).fadeOut(300, function () {
                $psx(this).stop().html('<h1>' + sx_title + '</h1>');
                if (sx_next.attr('data-notes').length) {
                    $psx(this).stop().append('<p>' + sx_next.attr('data-notes') + '</p>');
                }
                if (sx_next.attr('data-datetime').length) {
                    $psx(this).find('p').stop().append('<span> ' + sx_next.attr('data-datetime') + '</span>');
                }
            }).fadeIn(300);
        }

        /*
            Alternative effect modes:
        */
        if (sx_mode == 'fade_both') {
            sx_active.fadeOut(400, function () {
                sx_active.css({
                    'z-index': -1
                }).removeClass('active');

            });
            sx_next.css({
                'z-index': 1
            }).fadeIn(400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
            });
        } else if (sx_mode == 'fade_active') {
            sx_next.css({
                'z-index': 1
            }).fadeIn();
            sx_active.fadeOut(600, function () {
                sx_active.css({
                    'z-index': -1
                }).removeClass('active');
                sx_next.css({
                    'z-index': 2
                }).addClass('active');

            });
        } else if (sx_mode == 'move_left_right') {
            sx_next.css({
                'left': '-100%',
                'display': 'block',
                'z-index': 3
            }).stop().animate({
                left: '0'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
            });
            sx_active.stop().animate({
                'left': '100%'
            }, 400, function () {
                sx_active.css({
                    'z-index': -1,
                    'left': 0,
                    'display': 'none'
                }).removeClass('active');
            })
        } else if (sx_mode == 'move_right_left') {
            sx_next.css({
                'left': '100%',
                'display': 'block',
                'z-index': 3
            }).stop().animate({
                left: '0'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
            });
            sx_active.stop().animate({
                'left': '-100%'
            }, 400, function () {
                sx_active.css({
                    'z-index': -1,
                    'left': 0,
                    'display': 'none'
                }).removeClass('active');
            })

        } else if (sx_mode == 'move_top_bottom') {
            sx_next.css({
                'top': '-100%',
                'display': 'block',
                'z-index': 3
            }).animate({
                top: '0'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
            });
            sx_active.animate({
                'top': '100%'
            }, 400, function () {
                sx_active.css({
                    'z-index': -1,
                    'top': 0,
                    'display': 'none'
                }).removeClass('active');
            })
        } else if (sx_mode == 'start_top_left') {
            sx_next.css({
                'width': '0',
                'display': 'block',
                'z-index': 3
            }).animate({
                width: '100%'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
                sx_active.css({
                    'z-index': -1,
                    'display': 'none'
                }).removeClass('active');
            });
        } else if (sx_mode == 'end_top_left') {
            sx_next.css({
                'z-index': 1
            }).fadeIn();
            sx_active.animate({
                width: '0',
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
                sx_active.css({
                    'z-index': -1,
                    'width': '100%',
                    'display': 'none'
                }).removeClass('active');
            });
        } else if (sx_mode == 'end_top_right') {
            sx_next.css({
                'z-index': 1
            }).fadeIn();
            sx_active.animate({
                width: '0',
                left: '100%'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
                sx_active.css({
                    'z-index': -1,
                    'width': '100%',
                    'left': 0,
                    'display': 'none'
                }).removeClass('active');
            });

        } else if (sx_mode == 'end_bottom_right') {
            sx_next.css({
                'z-index': 1
            }).fadeIn();
            sx_active.animate({
                left: '100%',
                top: '100%'
            }, 400, function () {
                sx_next.css({
                    'z-index': 2
                }).addClass('active');
                sx_active.css({
                    'z-index': -1,
                    'top': 0,
                    'left': 0,
                    'display': 'none'
                }).removeClass('active');
            });

        }
    }
}