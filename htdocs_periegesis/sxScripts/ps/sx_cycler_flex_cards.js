var $sx = jQuery.noConflict();

$sx(function () {
    $sx('.jq_CyclerFlexCards').each(function () {
        $sx(this).sxLoadMultiCycler();
    });
});

$sx.fn.sxLoadMultiCycler = function () {
    var sxCycler = $sx(this);
    var sxCard = sxCycler.find('.flex_cards');
    var sxCardFigures = sxCard.find('figure');
    var currentPage = 1;
    var sxPlace = sxCycler.attr('data-place') || '';
    var sxMode = sxCycler.attr('data-mode') || 'move_right_left';
    var cycleInterval = null;

    // Add navigation UI
    sxCycler.append('<ul><li class="nav-prev"></li><li class="description"></li><li class="nav-next"></li></ul>');
    if (sxPlace === "cycler_nav_bottom") {
        sxCycler.addClass('cycler_flex__nav_bottom');
    }

    /** -------------------
     * Interval handling
     * ------------------- */
    function startInterval() {
        stopInterval(); // Prevent duplicate intervals
        cycleInterval = setInterval(sxCycleImages, 4000);
    }
    function stopInterval() {
        if (cycleInterval) {
            clearInterval(cycleInterval);
            cycleInterval = null;
        }
    }

    /** -------------------
     * Core cycling logic
     * ------------------- */
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

        // Do the animation
        sxAnimateCycle(sliceActive, residualFigures);

        // Update current page
        currentPage = (currentPage >= totalPages) ? 1 : currentPage + 1;

        // Update description / tooltip
        sxCycler.find('.description').text(currentPage + ' / ' + totalPages);
        if (!sxPlace) {
            sxCycler.find("[class^='nav-']").attr('title', currentPage + '/' + totalPages);
        }
    }

    function sxAnimateCycle(sliceActive, residualFigures) {
        // Create temp layer
        var sxFixedLayer = $sx('<div class="cycler_flex_absolute"></div>');
        sxCard.append(sxFixedLayer);

        // Clone active cards
        sxFixedLayer.append(sliceActive.clone(true, false));

        // Handle wrap-around residuals
        if (residualFigures > 0) {
            var sxNextRes = sxCardFigures.slice(0, residualFigures);
            sxFixedLayer.append(sxNextRes.clone(true, false));
        }

        // Append originals to end for looping effect
        sxCard.append(sliceActive);

        // Set animation direction
        var animationProperties =
            (sxMode === 'move_left_right')
                ? { 'left': '100%', 'right': '-100%' }
                : { 'left': '-100%', 'right': '100%' };

        // Animate + cleanup
        sxFixedLayer.animate(animationProperties, 600, function () {
            sxFixedLayer.remove();
        });
    }

    /** -------------------
     * Event handling
     * ------------------- */
    sxCycler
        .off('.sxCycler') // clear previous handlers in case of re-init
        .on('mouseover.sxCycler', stopInterval)
        .on('mouseout.sxCycler', startInterval);

    sxCycler.find("[class^='nav-']").on('click', function () {
        sxMode = $sx(this).hasClass('nav-prev') ? 'move_right_left' : 'move_left_right';
        sxCycleImages();
    });

    $sx(window).on("focus", startInterval).on("blur", stopInterval);
    $sx(window).on('beforeunload pagehide', stopInterval);

    /** -------------------
     * Init
     * ------------------- */
    sxCycleImages();
    startInterval();
};
