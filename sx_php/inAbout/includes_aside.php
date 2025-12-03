<?php
if ($radio_UseAdvertises) {
    get_Main_Advertisements("Top");
}
//include __DIR__ ."/nav_about.php";
include __DIR__ . "/nav_about_accordion.php";

if ($radio_UseAdvertises) {
    get_Main_Advertisements_Cycler('BottomSlider','');
    get_Main_Advertisements("Bottom");
}
