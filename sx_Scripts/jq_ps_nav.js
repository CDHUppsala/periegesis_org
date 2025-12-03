/*
  ===========================================================================
  $sxnav - used for all Navigation Function in All Horizontal and Vertical Menus
  ===========================================================================
*/
var $sx = jQuery.noConflict();
$sx(document).ready(function () {
    /*	
      ===========================================================================
      For both versions (drop-down and widescreen) of Horizontal Header Menu
      Posision = static (not absolute) means that the menu is in desktop version
      ===========================================================================
    */

    $sx("#jqNavHeadMenu > ul > li").hover(
        function () {
            var $currentMenuItem = $sx(this);
            var $subList = $currentMenuItem.find(">ul");
            if ($subList.length && $sx("#jqNavHeadMenu").css("position") == "static") {
                var menuItemIndex = $currentMenuItem.index();
                var siblingCount = $currentMenuItem.siblings().length;
                var halfSiblings = Math.round((siblingCount - 2) / 2);

                if (menuItemIndex <= halfSiblings || $subList.hasClass("wide")) {
                    $subList.stop(true, true).delay(100).slideDown(300);
                } else {
                    $subList
                        .css({
                            left: "auto",
                            right: "0",
                            "text-align": "left",
                        })
                        .stop(true, true)
                        .delay(200)
                        .slideDown(300);
                }
            }
        },
        function () {
            var $currentMenuItem = $sx(this);
            var $subList = $currentMenuItem.find(">ul");
            if ($subList.length && $sx("#jqNavHeadMenu").css("position") == "static") {
                $subList.stop(true, true).delay(100).slideUp(300);
            }
        }
    );

    /**
     * #header: Optional, if present, always cantains the  Big Logotype
     * #nav_head: Necessary, its height must be set by this script; 
     *  - might contain the Big Logotype (if #header is not present)
     *  - always contains the small logotype (if it exists)
     * .jq_NavHeadFixed toggle the position of .nav_head_fixed between fixed/relative
     */
    if ($sx(".jq_NavHeadFixed").length) {
        const fixedNavElement = $sx(".jq_NavHeadFixed");
        const navHead = $sx("#nav_head");
        const navMenu = $sx(".nav_head_menu");
        const navMarks = $sx(".nav_marks_flex_between");

        // Prevent window jump when navigation becomes fixed
        navHead.css('height', navHead.outerHeight(true));

        // Determine the top offset where navigation becomes fixed
        let navHeadOffset = navMenu.is(":visible")
            ? navMenu.offset().top
            : navMarks.is(":visible")
                ? navMarks.offset().top
                : 0;

        // Handle logo visibility and positioning
        const hasSmallLogo = $sx("#logo_small").length > 0;
        const smallLogo = hasSmallLogo ? $sx("#logo_small") : null;
        const bigLogo = $sx("#logo");
        const hasHeader = $sx("#header").length > 0;

        // Fix navigation on page load if already scrolled past offset
        const fixNavigation = () => {
            if (hasSmallLogo) {
                if (!hasHeader) bigLogo.hide();
                smallLogo.show();
            }
            fixedNavElement.css("position", "fixed");
        };

        const resetNavigation = () => {
            if (hasSmallLogo) {
                if (!hasHeader) bigLogo.show();
                smallLogo.hide();
            }
            fixedNavElement.css("position", "relative");
        };

        if ($sx(window).scrollTop() > navHeadOffset) {
            fixNavigation();
        }

        // Toggle navigation fixed/relative on scroll
        $sx(window).scroll(function () {
            const scrolled = $sx(this).scrollTop();
            if (scrolled >= navHeadOffset) {
                fixNavigation();
            } else {
                resetNavigation();
            }
        });
    }

    /*	
      ===========================================================================
      ALL Top and Main NAVIGATION FUNCTIONS
      ===========================================================================
        */

    // Toggles the Next element of a selector and hides all Next elements of selectors with same class name
    if ($sx('.jqToggleNextHideNexts').length) {
        $sx('.jqToggleNextHideNexts > div').click(function () {
            $sx(this).parent()
                .next().slideToggle('fast')
                .end()
                .siblings(".jqToggleNextHideNexts")
                .next().slideUp("fast");
        });

        $sx(document).click(function () {
            $sx('.jqToggleNextHideNexts').next().slideUp("fast");
        });

        $sx('.jqToggleNextHideNexts').click(function (event) {
            // Prohibits the click event to propagate to the document and close the popup 
            event.stopPropagation();
        });
        $sx('.jqToggleNextHideNexts').next().click(function (event) {
            if (!$sx(event.target).is('button')) {
                event.stopPropagation();
            }
        });
    };

    // Toggles all next DIVs, NAVs, ULs
    if ($sx(".jqToggleNextRight").length) {
        $sx(".jqToggleNextRight").click(function () {
            $sx(this)
                .toggleClass("slide_down slide_up")
                .next("div, nav, ul, ol")
                .slideToggle(300);
        });
    }

    if ($sx(".jqToggleNextTagRight").length) {
        $sx(".jqToggleNextTagRight").click(function () {
            $sx(this).toggleClass("slide_down slide_up").next().slideToggle(400);
        });
    }

    if ($sx(".jqToggleNextLeft").length) {
        $sx(".jqToggleNextLeft").click(function () {
            $sx(this)
                .toggleClass("slide_left_down slide_left_up")
                .next("div, nav, ul")
                .slideToggle(300);
        });
    }

    // Toggles next tbodies from TR/TH
    if ($sx(".jqToggleNextTbody").length) {
        $sx(".jqToggleNextTbody").click(function () {
            $sx(this)
                .toggleClass("slide_down slide_up")
                .parents("thead")
                .next()
                .toggle(300);
        });
    }

    if ($sx(".jqNavAsideToggleNext").length) {
        $sx(".jqNavAsideToggleNext div").click(function () {
            $sx(this).toggleClass("open").next("ul").slideToggle("slow");
            sx_ScrollToTopFixed($sx(this), 56);
        });
    }

    if ($sx(".jqAccordionByDiv").length) {
        $sx(".jqAccordionByDiv > div").click(function () {
            $sx(this)
                .toggleClass("open")
                .siblings("div")
                .removeClass("open")
                .end()
                .next("ul")
                .slideToggle("400")
                .siblings("ul")
                .hide("400");
            sx_ScrollToTopFixed($sx(this), 56);
        });
    }

    // Accordions for Main navigation NO links to text - from 1 to 3 levels
    if ($sx(".jqAccordionNav").length) {
        $sx(".jqAccordionNav div").click(function () {
            $sx(this)
                .toggleClass("open")
                .next("ul")
                .slideToggle(400)
                .end()
                .parent()
                .siblings()
                .find("div")
                .removeClass("open")
                .end()
                .find("ul")
                .hide(400);
            sx_ScrollToTopFixed($sx(this), 56);
        });
    }

    /**
     * As jqAccordionNav, although it opens only the next element
     * without closing the already opened ones
     */
    if ($sx(".jqAccordionNavNext").length) {
        $sx(".jqAccordionNavNext div").click(function () {
            $sx(this).toggleClass("open").next("ul").slideToggle("slow");
            sx_ScrollToTopFixed($sx(this), 56);
        });
    }

    // Tabs for Text navigation (sxNav_MainListsTabs.css)
    // Tabs IN a separate U-List show/hide multiple NEXT Layers of U-Lists or DIVs
    if ($sx(".jqNavTabs").length) {
        $sx(".jqNavTabs li").click(function () {
            var sxThis = $sx(this);
            sxThis
                .toggleClass("selected")
                .siblings()
                .removeClass("selected")
                .end()
                .closest("div")
                .next("div")
                .find(">ul,>div")
                .eq(sxThis.index())
                .slideToggle("slow")
                .siblings()
                .slideUp("slow");
        });
    }


    // General TABS IN U-List that show/hide multiple SIBLING LAYERS of any element (UL or DIV) WITHOUT CLASS
    // TABS and LAYERS can include each other as parents and children
    if ($sx('.jqTabsList li').length) {
        $sx(".jqTabsList li").click(function (e) {
            var sxThis = $sx(this);
            var targetSection = sxThis.closest("section");
            var scrollPosition = targetSection.offset().top - $sx("#nav_head").height();
            var currentScrollPosition = $sx(window).scrollTop();
            var tolerance = 2; // Define a small tolerance value

            // Check if the scroll positions are effectively equal within the tolerance
            if (Math.abs(currentScrollPosition - scrollPosition) > tolerance) {
                $sx("html, body").animate({
                    scrollTop: scrollPosition
                }, 500, function () {
                    sx_updateUI(sxThis);
                });
            } else {
                sx_updateUI(sxThis);
            }

            function sx_updateUI(sxThis) {
                var targetIndex = sxThis.index();
                sxThis
                    .addClass('selected')
                    .siblings().removeClass('selected')
                    .closest('div')
                    .siblings("div", "ul").eq(targetIndex).fadeIn(200)
                    .end()
                    .not(":eq(" + targetIndex + ")").hide();
            }
        });


        // Stick the Tabs (jqTabsListSticky) on the top when scrolling
        // Define the height of their container (wrapper) to avoid jumps
        if ($sx('.jqTabsListSticky').length) {
            var $list = $sx('.jqTabsListSticky');
            var listHeight = $list.outerHeight();
            var $listWrapper = $sx('.tabs_list_wrapper');
            $listWrapper.css('height', listHeight)
            var parentSection = $list.closest('section');
            var navHeadHeight = $sx('#nav_head').outerHeight();

            $sx(window).scroll(function () {
                var listOffsetTop = $listWrapper.offset().top - navHeadHeight;
                var scrollTop = $sx(window).scrollTop();
                var sectionBottom = parentSection.offset().top + parentSection.outerHeight();

                if (scrollTop >= listOffsetTop) {
                    $list.addClass('fixed');
                } else {
                    $list.removeClass('fixed');
                }

                // Hide the sticky list if section bottom approaches the bottom of the list
                if (sectionBottom <= scrollTop + (3 * listHeight)) {
                    $list.css('visibility', 'hidden');
                } else {
                    $list.css('visibility', 'visible');
                }
            });
        }

    };


    /*	
      ===========================================================================
      ALL NAVIGATION FUNCTIONS
      ===========================================================================
    */

    if ($sx("#jqNavTopAppsMarker").length) {
        $sx("#jqNavTopAppsMarker").click(function () {
            if ($sx("#jqNavMainCloner").length) {
                $sx("#jqNavMainCloner").slideUp("fast");
            }
            if ($sx("#jqNavAsideMenusCloner").length) {
                $sx("#jqNavAsideMenusCloner").slideUp("fast");
            }
            if ($sx("#jqNavHeadMenu").length) {
                $sx("#jqNavHeadMenu").slideUp("fast");
            }

            $sx("#jqNavTopApps").delay(50).slideToggle("slow");
        });
    }

    if ($sx("#jqNavHeadMenuMarker").length) {
        $sx("#jqNavHeadMenuMarker").click(function () {
            var nav = $sx("#jqNavHeadMenu");
            var sxHeight = $sx(window).height() - $sx("#nav_head").outerHeight(true);
            if ($sx("#jqNavMainCloner").length) {
                $sx("#jqNavMainCloner").slideUp("slow");
            }
            if ($sx("#jqNavAsideMenusCloner").length) {
                $sx("#jqNavAsideMenusCloner").slideUp("slow");
            }
            if ($sx("#jqNavTopApps").length) {
                $sx("#jqNavTopApps").slideUp("fast");
            }

            nav.css("height", sxHeight).delay(100).slideToggle("slow");
        });
    }

    if ($sx(".jqNavMainToBeCloned").length) {
        if ($sx("#jqNavMainMenuMarker").length) {
            var sx_MainNavCloned = false;
            $sx("#jqNavMainMenuMarker").click(function () {
                if (!sx_MainNavCloned) {
                    $sx("#jqNavMainCloner").append(
                        $sx(".jqNavMainToBeCloned").clone(true, true)
                    );
                    sx_MainNavCloned = true;
                }

                var nav = $sx("#jqNavMainCloner");
                var sxHeight = $sx(window).height() - $sx("#nav_head").height();
                $sx("#jqNavHeadMenu").slideUp("slow");
                if ($sx("#jqNavAsideMenusCloner").length) {
                    $sx("#jqNavAsideMenusCloner").slideUp("slow");
                }
                if ($sx("#jqNavTopApps").length) {
                    $sx("#jqNavTopApps").slideUp("fast");
                }
                nav.css("height", sxHeight).slideToggle("slow");
            });
        }
    } else {
        $sx("#jqNavMainMenuMarker").css("display", "none");
    }

    if ($sx(".jqNavSideToBeCloned").length) {
        if ($sx("#jqNavAsideMenusMarker").length) {
            var sx_SideNavCloned = false;
            $sx("#jqNavAsideMenusMarker").click(function () {
                if (!sx_SideNavCloned) {
                    $sx("#jqNavAsideMenusCloner").append(
                        $sx(".jqNavSideToBeCloned").clone(true, true)
                    );
                    sx_SideNavCloned = true;
                }
                var nav = $sx("#jqNavAsideMenusCloner");
                var sxHeight = $sx(window).height() - $sx("#nav_head").height();
                $sx("#jqNavHeadMenu").slideUp("slow");
                if ($sx("#jqNavMainCloner").length) {
                    $sx("#jqNavMainCloner").slideUp("slow");
                }
                if ($sx("#jqNavTopApps").length) {
                    $sx("#jqNavTopApps").slideUp("fast");
                }
                nav.css("height", sxHeight).slideToggle("slow", function () {
                    nav.is(":visible")
                        ? $sx("html, body").addClass("no-scroll")
                        : $sx("html, body").removeClass("no-scroll");
                });
            });
        }
    } else {
        $sx("#jqNavAsideMenusMarker").css("display", "none");
    }
});

var sx_ScrollToTop = function (iTop, iMinus) {
    $sx("html, body").animate(
        {
            scrollTop: iTop - iMinus,
        },
        400
    );
};

var sx_ScrollToTopFixed = function (obj, iMinus) {
    var sxEl = obj.closest("section");
    var iTop = sxEl.offset().top;
    $sx("html, body").animate(
        {
            scrollTop: iTop - iMinus,
        },
        400,
        function () {
            // To account for Fixed Header Navigation
            var iTopNew = sxEl.offset().top;
            if (iTop > iTopNew) {
                sx_ScrollToTop(iTopNew, iMinus);
            }
        }
    );
};

var sx_reorderPublishedByClass = function (id) {
    var sxDIV = $sx(".jqNavMainToBeCloned .sxAccordionNav > ul").eq(0);
    if (id > 0) {
        sxDIV.children("li").eq(id).prependTo(sxDIV);
    }
};
