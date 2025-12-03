var $ps = jQuery.noConflict();

$ps(function () {
    /**
     * Multiple Image Cycler Advertises
     */
    if ($ps('.jqCyclerAds').length) {
        $ps('.jqCyclerAds').each(function () {
            $ps(this).sxLoadCyclerAds();
        });
    }
});

$ps.fn.sxLoadCyclerAds = function (options) {
    const defaults = {
        mode: 'start_top_left',
        interval: 4000,
    };

    const settings = $ps.extend({}, defaults, options);

    const cycler = $ps(this);
    const firstImage = cycler.find('img:first');

    if (!firstImage.length) {
        console.error('No images found in the cycler container.');
        return;
    }

    firstImage.on('load', function () {
        const padding = Math.floor((this.height / this.width) * 100);
        cycler.find('figure').css('padding-top', padding + '%');
    }).each(function () {
        if (this.complete) $ps(this).trigger('load');
    });

    firstImage.css({
        'z-index': 2
    }).addClass('active')
    .siblings().css({
        'z-index': -1,
        'display': 'none'
    });

    cycler.append('<ul><li class="nav-prev"></li><li class="description"></li><li class="nav-next"></li></ul>');

    let mode = settings.mode;
    if (cycler.attr('data-mode')) {
        mode = cycler.attr('data-mode');
    }

    let cycleInterval;

    function startInterval() {
        cycleInterval = setInterval(function () {
            cycleImages('');
        }, settings.interval);
    }

    function stopInterval() {
        clearInterval(cycleInterval);
    }

    function determineNextImage(pos) {
        const active = cycler.find('img.active');
        if (pos === 'prev') {
            return active.prev('img').length > 0 ? active.prev('img') : cycler.find('img:last');
        } else {
            return active.next('img').length > 0 ? active.next('img') : cycler.find('img:first');
        }
    }

    const effects = {
        fade_both: function (active, next) {
            active.stop().fadeOut(400, function () {
                active.css({ 'z-index': -1 }).removeClass('active');
            });
            next.css({ 'z-index': 1 }).stop().fadeIn(400, function () {
                next.css({ 'z-index': 2 }).addClass('active');
            });
        },
        fade_active: function (active, next) {
            next.css({ 'z-index': 1 }).fadeIn();
            active.stop().fadeOut(600, function () {
                active.css({ 'z-index': -1 }).removeClass('active');
                next.css({ 'z-index': 2 }).addClass('active');
            });
        },
        move_left_right: function (active, next) {
            next.css({ 'left': '-100%', 'display': 'block', 'z-index': 3 })
                .stop().animate({ left: '0' }, 400, function () {
                    next.css({ 'z-index': 2 }).addClass('active');
                });
            active.stop().animate({ 'left': '100%' }, 400, function () {
                active.css({ 'z-index': -1, 'left': 0, 'display': 'none' }).removeClass('active');
            });
        },
        move_right_left: function (active, next) {
            next.css({ 'left': '100%', 'display': 'block', 'z-index': 3 })
                .stop().animate({ left: '0' }, 400, function () {
                    next.css({ 'z-index': 2 }).addClass('active');
                });
            active.stop().animate({ 'left': '-100%' }, 400, function () {
                active.css({ 'z-index': -1, 'left': 0, 'display': 'none' }).removeClass('active');
            });
        },
        move_top_bottom: function (active, next) {
            next.css({ 'top': '-100%', 'display': 'block', 'z-index': 3 })
                .stop().animate({ top: '0' }, 400, function () {
                    next.css({ 'z-index': 2 }).addClass('active');
                });
            active.stop().animate({ 'top': '100%' }, 400, function () {
                active.css({ 'z-index': -1, 'top': 0, 'display': 'none' }).removeClass('active');
            });
        },
        start_top_left: function (active, next) {
            next.css({ 'width': '0', 'display': 'block', 'z-index': 3 })
                .stop().animate({ width: '100%' }, 800, function () {
                    next.css({ 'z-index': 2 }).addClass('active');
                    active.css({ 'z-index': -1, 'display': 'none' }).removeClass('active');
                });
        }
    };

    function cycleImages(pos) {
        const active = cycler.find('img.active');
        const next = determineNextImage(pos);

        if (next.attr('data-title')) {
            let title = next.attr('data-title');
            if (next.attr('data-href')) {
                title = '<a href="' + next.attr('data-href') + '">' + title + '</a>';
            }
            cycler.find('.description').html('<h4>' + title + '</h4>');
            if (next.attr('data-notes')) {
                cycler.find('.description').append('<p>' + next.attr('data-notes') + '</p>');
            }
        }

        if (effects[mode]) {
            effects[mode](active, next);
        }
    }

    cycleImages('');
    startInterval();

    cycler.on('mouseover', stopInterval).on('mouseout', function () {
        mode = cycler.attr('data-mode') || settings.mode;
        startInterval();
    });

    cycler.find("[class^='nav-']").click(function () {
        if ($ps(this).hasClass('nav-prev')) {
            mode = 'move_right_left';
            cycleImages('prev');
        } else {
            mode = 'move_left_right';
            cycleImages('next');
        }
    });

    $ps(window).on("focus.sxCycler", startInterval).on("blur.sxCycler", stopInterval);
};
