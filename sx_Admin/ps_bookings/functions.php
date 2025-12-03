<?php
$strCurrentURL = "index.php";

function sx_GetRequest($parameter)
{
    if (isset($_POST[$parameter])) {
        return $_POST[$parameter];
    } elseif (isset($_GET[$parameter])) {
        return $_GET[$parameter];
    } else {
        return null;
    }
}

$intYear = 0;
$intMonth = 0;
$intWeek = 0;
$intDay = 0;

if (!empty(sx_GetRequest("year"))) {
    $intYear = sx_GetRequest("year");
    if (empty($intYear) || strlen($intYear) != 4 || intval($intYear) < 1970) {
        $intYear = 0;
    }
}
if (!empty(sx_GetRequest("month"))) {
    $intMonth = sx_GetRequest("month");
    if (intval($intMonth) > 12 || intval($intMonth) < 1) {
        $intMonth = 0;
    }
}
if (!empty(sx_GetRequest("week"))) {
    $intWeek = sx_GetRequest("week");
    if (intval($intWeek) > 53 || intval($intWeek) < 1) {
        $intWeek = 0;
    }
}
if (!empty(sx_GetRequest("day"))) {
    $intDay = sx_GetRequest("day");
    if (intval($intDay) > 31 || intval($intDay) < 1) {
        $intDay = 0;
    }
}

if (strlen($intYear) == 4 && sx_CheckIntBetween($intMonth, 1, 12)) {
    $strMonth = $intMonth;
    if (strlen($strMonth) == 1) {
        $strMonth = "0" . $strMonth;
    }
    $strDay = $intDay;
    if ($strDay == 0) {
        $strDay = '01';
    }elseif (strlen($strDay) == 1) {
        $strDay = "0" . $strDay;
    }

    $date_RequestedDate = $intYear . "-" . $strMonth . "-". $strDay;
} else {
    $date_RequestedDate = date("Y-m-d");
}


if (isset($date_RequestedDate) && sx_isDate($date_RequestedDate)) {
    $sxCurrDate = $date_RequestedDate;
} else {
    $sxCurrDate = date("Y-m-d");
}

$date_time = new DateTime($sxCurrDate);
$iThisYear = $date_time->format("Y");
$iThisMonth = $date_time->format("n");

$iPrevYear = $iThisYear;
$iNextYear = $iThisYear;
$iPrevMonth = $iThisMonth - 1;
$iNextMonth = $iThisMonth + 1;

if ($iPrevMonth == 0) {
    $iPrevMonth = 12;
    $iPrevYear = $iThisYear - 1;
}
if ($iNextMonth == 13) {
    $iNextMonth = 1;
    $iNextYear = $iThisYear + 1;
}

if (strlen($iPrevMonth) == 1) {
    $iPrevMonth = "0".$iPrevMonth;
}
if (strlen($iNextMonth) == 1) {
    $iNextMonth = "0".$iNextMonth;
}

$iThisMonthZero = $iThisMonth;
if (strlen($iThisMonthZero) == 1) {
    $iThisMonthZero = '0' . $iThisMonth;
}


/**
 * Just incase: get the searched date with the first day of the searched month
 */
$objMonthFirstDay = new DateTime($iThisYear . "-" . $iThisMonthZero . "-01");
$iMonthDays = $objMonthFirstDay->format('t');
$iFirstMonthWeek = intval($objMonthFirstDay->format('W'));
$objMonthLastDay = new DateTime($iThisYear . "-" . $iThisMonthZero . "-" . $iMonthDays);

/**
 * For search in database
 */
$dMonthFirstDay = $iThisYear . "-" . $iThisMonthZero . "-01";
$dFirstRequestDay = sx_AddToDate($dMonthFirstDay,-1,'months');
$dLastRequestDay = sx_AddToDate($dMonthFirstDay,1,'months');

/**
 * To transform to monday = 0
 */
$iWeekStartDay = $objMonthFirstDay->format('w') - 1;

if ($iWeekStartDay < 0) {
    $iWeekStartDay = 6;
}

$objPrevMonthFirstDay = new DateTime($iPrevYear . "-" . $iPrevMonth . "-01");
$iPrevMonthDays = $objPrevMonthFirstDay->format('t');

$objNextMonthFirstDay = new DateTime($iNextYear . "-" . $iNextMonth . "-01");
$iWeekStartDayNext = $objNextMonthFirstDay->format('w') - 1;

if ($iWeekStartDayNext < 0) {
    $iWeekStartDayNext = 6;
}
if ($iWeekStartDayNext == 0) {
    $iWeekStartDayNext = 7;
}

$iTotalCalendarDays = $iMonthDays + $iWeekStartDay + (7 - $iWeekStartDayNext);

$iDaysForward = 0;
$iWeeksForward = 0;
if($intWeek > $iFirstMonthWeek) {
    $iWeeksForward = $intWeek - $iFirstMonthWeek;
    if($iWeeksForward > 3) {
        $iWeeksForward = 3; // No more than 3 weeks forward
    }
    $iFirstMonthWeek += $iWeeksForward;
    $iDaysForward = $iWeeksForward * 7;
}

$iTotalCalendarDays += $iDaysForward;
$iFrom = $iDaysForward;
?>