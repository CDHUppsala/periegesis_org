<?php
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
    if (strlen($intYear) != 4 || intval($intYear) < 1970) {
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



?>