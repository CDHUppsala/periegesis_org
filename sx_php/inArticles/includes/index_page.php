<?php
if (defined('SX_shwoWideSliderInIndexPage') && SX_shwoWideSliderInIndexPage) {
    include PROJECT_PHP . "/sx_Slider/includes_slider.php";
} else {
    if (sx_includeHeaderAds && $radio_UseAdvertises) {
        get_Header_Advertisements();
    }
}
