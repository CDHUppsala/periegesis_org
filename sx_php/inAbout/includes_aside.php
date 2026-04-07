<?php
if ($radio_UseAdvertises) {
    get_Main_Advertisements("Top");
}

$showAboutNavMenu = true;
if (defined("SX_HideAboutNavMenu") && SX_HideAboutNavMenu) {
    $showAboutNavMenu = false;
}

if ($showAboutNavMenu) {
    //include __DIR__ ."/nav_about.php";
    include __DIR__ . "/nav_about_accordion.php";
}

if ($radio_UseAdvertises) {
    get_Main_Advertisements_Cycler('BottomSlider', '');
    get_Main_Advertisements("Bottom");
}
