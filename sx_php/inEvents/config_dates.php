<?php

/**
 * $dDate			' Requested calendar Date;
 * $int_MonthDays		' Days In (Requested) Month;
 * $date_MonthsFirstWeekDay	' Day Of Week that (Requested) month starts on or the Day of Week we are on.;
 * $date_FirstMonthDate	' First date of Requested Month or Week;
 * $date_LastMonthDate	' Last date of Requested Month or Week;
 * $iCurrent		' Variable we use to hold current day of month as we write table;
 * $loopDate		' holds the loop position date as we loop through the calender;
 * $date_ThisMonday, date_ThisSunday	' For week calendar;
 * $date_NextMonday, dPrevMonday	' For week calendar;
 */

/**
 * Week and Month Calendars are independed from each other
 */

$radioWeek = true;
$radioMonth = true;
$radioCalendar = true;

/**
 * 1. Week calendar
 * Get and check requested monday, else, set the monday of current week
 */
if (isset($_GET["monday"]) && return_Is_Date($_GET["monday"])) {
    $date_ThisMonday = $_GET["monday"];
    $tempMondayObj = new DateTime($date_ThisMonday);
    $iMonday = $tempMondayObj->format("w");

    if ($iMonday != 1) {
        $tempMondayObj = new DateTime('monday this week');
        $date_ThisMonday = $tempMondayObj->format('Y-m-d');
    }
} else {
    $tempMondayObj = new DateTime('monday this week');
    $date_ThisMonday = $tempMondayObj->format('Y-m-d');
}

$date_ThisSunday = return_Add_To_Date($date_ThisMonday, +6, 'days');
$date_NextMonday = return_Add_To_Date($date_ThisSunday, +1, 'days');
$date_PrevMonday = return_Add_To_Date($date_ThisMonday, -7, 'days');

/**
 * 2. Month calendar
 * Get Requested date or set current date
 */
if (isset($_GET["date"])) {
    $dDate = $_GET["date"];
    if (!return_Is_Date($dDate)) {
        $dDate = date("Y-m-d");
    }
} elseif (isset($_POST["month"]) && isset($_POST["year"])) {
    $i_Month = (int) $_POST["month"];
    $i_Year = (int) $_POST["year"];
    if (intval($i_Month) > 0 && intval($i_Month) < 13 && intval($i_Year) > 0 && strlen($i_Year) == 4) {
        $dDate = $i_Year . "-" . $i_Month . "-01";
    } else {
        $dDate = date("Y-m-d");
    }
} else {
    $dDate = date("Y-m-d");
}

$date_MonthsFirstWeekDay = return_Months_First_Week_Day($dDate);
$int_MonthDays = return_Total_Days_In_Month($dDate);
$tempDateObj = date_create(return_Year($dDate) . "-" . return_Month_01($dDate) . "-01");
$date_FirstMonthDate = $tempDateObj->format('Y-m-d');
$date_LastMonthDate = return_Add_To_Date($date_FirstMonthDate, $int_MonthDays - 1);

/*
    $date = new DateTime($date_SearchByDate);
    $YearWeek = $date->format("W");
    $week = $date->format("w");
    $year = $date->format("Y");
    $month = $date->format("m");
    $day = $date->format("d");
 */
