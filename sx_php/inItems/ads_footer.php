<?php

if (sx_includeFooterSlider) {
    /**
     * First parameter: The place av navigation: cycler_nav_middle (default), cycler_nav_bottom
     * Second parameter: The moving mode: move_left_right (default), move_right_left
     */
    get_Footer_Advertisements_Slider();

    /**
     * No parameters: automatic cicling of multiple cards
     */
    get_Footer_Advertisements_Cycler();
}

if (sx_includeFooterAds) {
    /**
     * First parameter: Footer, FooterMore
     */
    get_Footer_Advertisements('Footer');
    get_Footer_Advertisements('FooterMore');
}
