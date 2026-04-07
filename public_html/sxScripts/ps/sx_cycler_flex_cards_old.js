var $sx = jQuery.noConflict();

$sx(function () {
    if ($sx('.jq_CyclerFlexCards').length) {
        $sx('.jq_CyclerFlexCards').each(function () {
            $sx(this).sxLoadMultiCycler();
        });
    }
});

$sx.fn.sxLoadMultiCycler = function () {
    var sxCycler = $sx(this);
    var sxCard = sxCycler.find('.flex_cards');
    var sxCardFigures = sxCard.find('figure');
    var currentPage = 1;
    var intervalLoop = false;
    var sxPlace = sxCycler.attr('data-place') || '';
    var sxMode = sxCycler.attr('data-mode') || 'move_right_left';

    var cycleInterval;

    sxCycler.append('<ul><li class="nav-prev"></li><li class="description"></li><li class="nav-next"></li></ul>');

    if (sxPlace === "cycler_nav_bottom") {
        sxCycler.addClass('cycler_flex__nav_bottom');
    }

    function startInterval() {
        if (cycleInterval) clearInterval(cycleInterval); // Ensure no duplicate intervals.
        cycleInterval = setInterval(function () {
            intervalLoop = true;
            sxCycleImages();
        }, 4000);
    }

    function stopInterval() {
        clearInterval(cycleInterval);
    }

    function stopInterval() {
        if (cycleInterval) clearInterval(cycleInterval); // Clear any running intervals.
    }

    function sxCycleImages() {
        var totalFigures = sxCardFigures.length;
        var widthScreen = sxCard.prop('offsetWidth');
        var widthFigures = sxCardFigures.outerWidth(true);
        var showByPage = Math.round(widthScreen / widthFigures);
        var totalPages = Math.ceil(totalFigures / showByPage);
        var sliceFirst = (currentPage - 1) * showByPage;
        var sliceSecond = sliceFirst + showByPage;
        var sliceActive = sxCardFigures.slice(sliceFirst, sliceSecond);
        var residualFigures = sliceSecond - totalFigures;

        if (intervalLoop) {
            sxGetEffects(sliceActive, residualFigures);
            currentPage = (currentPage >= totalPages) ? 1 : currentPage + 1;
        }
        sxCycler.find('.description').text(currentPage + ' / ' + totalPages);
        if (sxPlace === "") {
            sxCycler.find("[class^='nav-']").attr('title', currentPage + '/' + totalPages);
        }
    }

    function sxGetEffects(sliceActive, residualFigures) {
        sxCard.append('<div class="cycler_flex_absolute"></div>');
        var sxFixedLayer = sxCard.find('.cycler_flex_absolute');
        sxFixedLayer.append(sliceActive.clone(true, false));

        if (residualFigures > 0) {
            var sxNextRes = sxCardFigures.slice(0, residualFigures);
            sxFixedLayer.append(sxNextRes.clone(true, false));
        }

        sxCard.append(sliceActive);

        var animationProperties = (sxMode === 'move_left_right') ? { 'left': '100%', 'right': '-100%' } : { 'left': '-100%', 'right': '100%' };
        sxFixedLayer.stop().animate(animationProperties, 600, function () {
            sxFixedLayer.remove();
        });
    }

    $sx(window).on('beforeunload pagehide', function () {
        stopInterval(); // Clean up when leaving the page.
    });

    sxCycleImages();
    startInterval();

    // Updated event handling with .on()
    sxCycler
        .off('mouseover.sxCycler mouseout.sxCycler') // Remove previous handlers.
        .on('mouseover.sxCycler', stopInterval)
        .on('mouseout.sxCycler', function () {
            if (sxCycler.attr('data-mode')) {
                sxMode = sxCycler.attr('data-mode');
            }
            stopInterval();
            startInterval();
        });

    sxCycler.find("[class^='nav-']").click(function () {
        intervalLoop = true;
        sxMode = $sx(this).hasClass('nav-prev') ? 'move_right_left' : 'move_left_right';
        sxCycleImages();
    });

    $sx(window).on("focus", startInterval).on("blur", stopInterval);
};
