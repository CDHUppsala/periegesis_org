<?php

if (intval($int_ArticleID) > 0 && SX_includeClassArticlesInCards) { 
    include  dirname(__DIR__) . "/read_more.php";
}

if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    //get_Main_Advertisements("Bottom");
}

if (sx_includeFooterSlider) {
    get_Footer_Advertisements_Slider();
}
if (sx_includeFooterAds) {
    get_Footer_Advertisements('Footer');
    //get_Footer_Advertisements('FooterMore');
}
