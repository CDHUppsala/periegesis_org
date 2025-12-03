<?php 

function sx_getCapitals($str) {
    return mb_strtoupper($str, "UTF-8");
}
$lng_MonthNames = array(
    "January", "February", "March", "April", "May", "June", "July",
    "August", "September", "October", "November", "December");

define("lng_MonthNames", $lng_MonthNames);

define("lng_MonthNamesGen", $lng_MonthNames);

$lng_DayNames = array(
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday"
);
define("lng_DayNames", $lng_DayNames);


$sxDays=array();
$sxDays[0]="Sunday";
$sxDays[1]="Monday";
$sxDays[2]="Tuesday";
$sxDays[3]="Wednesday";
$sxDays[4]="Thursday";
$sxDays[5]="Friday";
$sxDays[6]="Saturday";

$sxEuDays=array();
$sxEuDays[0]="Monday";
$sxEuDays[1]="Tuesday";
$sxEuDays[2]="Wednesday";
$sxEuDays[3]="Thursday";
$sxEuDays[4]="Friday";
$sxEuDays[5]="Saturday";
$sxEuDays[6]="Sunday";

$sxMonths=array();
$sxMonths[0]="January";
$sxMonths[1]="February";
$sxMonths[2]="March";
$sxMonths[3]="April";
$sxMonths[4]="May";
$sxMonths[5]="June";
$sxMonths[6]="July";
$sxMonths[7]="August";
$sxMonths[8]="September";
$sxMonths[9]="October";
$sxMonths[10]="November";
$sxMonths[11]="December";

$sxMonthsGen = $sxMonths;

?>