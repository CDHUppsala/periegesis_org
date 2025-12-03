var $sx = jQuery.noConflict();
$sx(function () {

    // Change the class of the clicked element and slidetoggles any next element
    if ($sx('.jqToggleClassNext').length) {
        $sx('.jqToggleClassNext').click(function () {
            $sx(this).toggleClass("selected").next().slideToggle('fast');
        });
    };

    if ($sx('.jqToggleClassesNext').length) {
        $sx('.jqToggleClassesNext').click(function () {
            $sx(this).toggleClass('slide_down slide_up').next().slideToggle('slow');
        });
    };

    if ($sx('.jqToggleNextAll').length) {
        $sx('.jqToggleNextAll').click(function () {
            $sx(this)
                .toggleClass('slide_down slide_up')
                .siblings(".jqToggleNextAll")
                .removeClass('slide_up').addClass('slide_down')
                .end()
                .next().slideToggle('fast')
                .siblings("div, ul").slideUp("fast");

            $sx("html, body").animate({
                scrollTop: $sx(this).closest("section").offset().top - ($sx("nav").height())
            }, 400);
        });
    };

    if ($sx('#jqPopupAnyForm').length) {
        $sx('#jqPopupAnyForm').submit(function (e) {
            var winLeft = Math.round((screen.width - 400) / 2);
            window.open('', 'popupFomt', 'width=400,height=400,top=100,left=' + winLeft + ',resizeable,scrollbars');
            this.target = 'popupFomt';
        });
    };

    // Not used - but functional!

    // Togle the element in data-x
    if ($sx('.jqToggleElement').length) {
        $sx('.jqToggleElement').click(function () {
            $sx($sx(this).attr("data-x")).slideToggle('slow');
        });
    };
    // Toggles the element with class/id data-x and hides all elements with class/id included in data-y 
    if ($sx('.jqToggleXHideYs').length) {
        $sx('.jqToggleXHideYs').click(function () {
            $sx($sx(this).attr("data-y")).slideUp('slow');
            $sx($sx(this).attr("data-x")).slideToggle('slow');
        });
    };

    if ($sx("#cookiesEULaw").length) {
        function sx_SetCookie(c_name, c_value, expiredays) {
            var exdate = new Date()
            exdate.setTime(exdate.getTime() + (expiredays * 24 * 60 * 60 * 1000));
            document.cookie = encodeURIComponent(c_name) +
                "=" + encodeURIComponent(c_value) +
                ";path=/" +
                ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString())
        }
        setTimeout(function () {
            $sx("#cookiesEULaw").fadeIn(200);
        }, 1000);
        $sx("#removeCookies").click(function () {
            sx_SetCookie('cookie_eu', 'cookie_eu', 365 * 10)
            $sx("#cookiesEULaw").remove();
        });
    };

    if ($sx("#cookiesAds").length) {
        function sx_SetCookie(c_name, c_value, expiredays) {
            var exdate = new Date()
            exdate.setTime(exdate.getTime() + (expiredays * 24 * 60 * 60 * 1000));
            document.cookie = encodeURIComponent(c_name) +
                "=" + encodeURIComponent(c_value) +
                ";path=/" +
                ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString())
        }

        setTimeout(function () {
            $sx("#cookiesAds").fadeIn(200);
        }, 5000);

        $sx("#removeCookiesAds").click(function () {
            var sxNames = $sx(this).closest("div").attr("data-id");
            var sxDays = $sx(this).closest("div").attr("data-days");
            sx_SetCookie(sxNames, sxNames, sxDays)
            $sx("#cookiesAds").remove();
        });
    };
});