<?php 

function sx_getCapitals($str) {
    return mb_strtoupper($str, "UTF-8");
}

$lng_MonthNames = array(
    "tammikuu",
    "helmikuu",
    "maaliskuu",
    "huhtikuu",
    "toukokuu",
    "kesäkuu",
    "heinäkuu",
    "elokuu",
    "syyskuu",
    "lokakuu",
    "marraskuu",
    "joulukuu");
define("lng_MonthNames", $lng_MonthNames);

define("lng_MonthNamesGen", $lng_MonthNames);

$lng_DayNames = array(
    "maanantai",
    "tiistai",
    "keskiviikko",
    "torstai",
    "perjantai",
    "lauantai",
    "sunnuntai");
define("lng_DayNames", $lng_DayNames);


$sxDays=array();
$sxDays[0]="sunnuntai";
$sxDays[1]="maanantai";
$sxDays[2]="tiistai";
$sxDays[3]="keskiviikko";
$sxDays[4]="torstai";
$sxDays[5]="perjantai";
$sxDays[6]="lauantai";

$sxEuDays=array();
$sxEuDays[0]="maanantai";
$sxEuDays[1]="tiistai";
$sxEuDays[2]="keskiviikko";
$sxEuDays[3]="torstai";
$sxEuDays[4]="perjantai";
$sxEuDays[5]="lauantai";
$sxEuDays[6]="sunnuntai";

$sxMonths=array();
$sxMonths[0]="tammikuu";
$sxMonths[1]="helmikuu";
$sxMonths[2]="maaliskuu";
$sxMonths[3]="huhtikuu";
$sxMonths[4]="toukokuu";
$sxMonths[5]="kesäkuu";
$sxMonths[6]="heinäkuu";
$sxMonths[7]="elokuu";
$sxMonths[8]="syyskuu";
$sxMonths[9]="lokakuu";
$sxMonths[10]="marraskuu";
$sxMonths[11]="joulukuu";

$sxMonthsGen = $sxMonths;

?>