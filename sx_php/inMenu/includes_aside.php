<?php

include __DIR__ ."/nav_dinner.php";
if ($radio_UseLunchMenu) {
    include __DIR__ ."/nav_lunch.php";
}
if ($radio_UseLunchMenu && $radio_ShowTodaysMenu) {
    include __DIR__ ."/nav_lunch_today.php";
}
if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    get_Main_Advertisements("Bottom");
}