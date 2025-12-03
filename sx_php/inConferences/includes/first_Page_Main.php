<?php

include_once dirname(__DIR__) . "/functions_Basic.php";

include dirname(__DIR__) . "/default.php";

if (sx_includeFooterMain) {
    /**
     * If CONST sx_includeFooterSlider = false cycler is transformd to
     * Cart Advertises
     */
    get_Footer_Advertisements_Slider('cycler_nav_bottom', 'move_right_left');
}

/**
 * First parameter: The place av navigation: cycler_nav_middle (default), cycler_nav_bottom
 * Second parameter: The moving mode: move_left_right (default), move_right_left
 * Third parameter: If a Read More link will appear at the bottom of the Card (true, false = default)
 */

if (sx_includeFooterSlider) {
    get_Footer_Advertisements_Slider();
}

/**
 * First parameter: Footer, FooterMore
 */

if (sx_includeFooterAds) {
    get_Footer_Advertisements('Footer');
    get_Footer_Advertisements('FooterMore');
}
