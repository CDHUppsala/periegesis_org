<?php 

function sx_getCapitals($str) {
    return mb_strtoupper($str, "UTF-8");
}

$lng_MonthNames = array(
    "Januari",
    "Februari",
    "Mars",
    "April",
    "Maj",
    "Juni",
    "Juli",
    "Augusti",
    "September",
    "Oktober",
    "November",
    "December");
define("lng_MonthNames", $lng_MonthNames);
define("lng_MonthNamesGen", $lng_MonthNames);

$lng_DayNames = array(
    "Måndag",
    "Tisdag",
    "Onsdag",
    "Torsdag",
    "Fredag",
    "Lördag",
    "Söndag");
define("lng_DayNames", $lng_DayNames);

$sxDays=array();
$sxDays[0]="Söndag";
$sxDays[1]="Måndag";
$sxDays[2]="Tisdag";
$sxDays[3]="Onsdag";
$sxDays[4]="Torsdag";
$sxDays[5]="Fredag";
$sxDays[6]="Lördag";

$sxEuDays=array();
$sxEuDays[0]="Måndag";
$sxEuDays[1]="Tisdag";
$sxEuDays[2]="Onsdag";
$sxEuDays[3]="Torsdag";
$sxEuDays[4]="Fredag";
$sxEuDays[5]="Lördag";
$sxEuDays[6]="Söndag";

$sxMonths=array();
$sxMonths[0]="Januari";
$sxMonths[1]="Februari";
$sxMonths[2]="Mars";
$sxMonths[3]="April";
$sxMonths[4]="Maj";
$sxMonths[5]="Juni";
$sxMonths[6]="Juli";
$sxMonths[7]="Augusti";
$sxMonths[8]="September";
$sxMonths[9]="Oktober";
$sxMonths[10]="November";
$sxMonths[11]="December";

$sxMonthsGen = $sxMonths;

?>