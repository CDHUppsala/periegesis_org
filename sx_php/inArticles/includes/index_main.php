<?php
/**
 * If not wide slider in .page, check if there is any for .main
 */
if (defined('SX_shwoWideSliderInIndexPage') && SX_shwoWideSliderInIndexPage === false) {
    include PROJECT_PHP . "/sx_Slider/includes_slider.php";
}

if (SX_IncludeArticlesInIndexAside) {
    include  dirname(__DIR__) . "/read_more.php";
}
