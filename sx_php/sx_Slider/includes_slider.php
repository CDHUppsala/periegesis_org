<?php

include __DIR__ ."/config_slider.php";

/**
 * Top Main (First Page) Sider
 * To use the slider only in the first page the variable $radio_DefaultSliderPage 
 *      must be defined externally: it comes from inEvents/functions_calendar.php
 */
if ($radio_DefaultSliderPage) {
    include __DIR__ ."/functions_slider.php";

    if ($radio_DefaultSliderPage) {
        include __DIR__ ."/sx_slider.php";
    }
}
