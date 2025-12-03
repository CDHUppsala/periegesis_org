<?php
if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    get_Main_Advertisements("Top");
}
if (intval($int_ProjectID) > 0) {
    include __DIR__ . "/nav_reports.php";
}
include __DIR__ . "/nav_projects.php";

if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    get_Main_Advertisements("Bottom");
}
