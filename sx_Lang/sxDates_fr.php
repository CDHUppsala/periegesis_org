<?php 

function sx_getCapitals($str) {
    //return strtoupper($str);
    return mb_strtoupper($str, "UTF-8");
}
$lng_MonthNames = array(
    "janvier",
    "février",
    "mars",
    "avril",
    "mai",
    "juin",
    "juillet",
    "août",
    "septembre",
    "octobre",
    "novembre",
    "décembre");
define("lng_MonthNames", $lng_MonthNames);

define("lng_MonthNamesGen", $lng_MonthNames);

$lng_DayNames = array(
    "lundi",
    "mardi",
    "mercredi",
    "jeudi",
    "vendredi",
    "samedi",
    "dimanche");
define("lng_DayNames", $lng_DayNames);

$sxDays=array();
$sxDays[0]="dimanche";
$sxDays[1]="lundi";
$sxDays[2]="mardi";
$sxDays[3]="mercredi";
$sxDays[4]="jeudi";
$sxDays[5]="vendredi";
$sxDays[6]="samedi";

$sxEuDays=array();
$sxEuDays[0]="lundi";
$sxEuDays[1]="mardi";
$sxEuDays[2]="mercredi";
$sxEuDays[3]="jeudi";
$sxEuDays[4]="vendredi";
$sxEuDays[5]="samedi";
$sxEuDays[6]="dimanche";

$sxMonths=array();
$sxMonths[0]="janvier";
$sxMonths[1]="février";
$sxMonths[2]="mars";
$sxMonths[3]="avril";
$sxMonths[4]="mai";
$sxMonths[5]="juin";
$sxMonths[6]="juillet";
$sxMonths[7]="août";
$sxMonths[8]="septembre";
$sxMonths[9]="octobre";
$sxMonths[10]="novembre";
$sxMonths[11]="décembre";

$sxMonthsGen = $sxMonths;

?>