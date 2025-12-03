<?php

if ($radio_UseEvents == false || sx_includeEvents == false) {
    header("location: index.php?v=2");
    exit();
}
// Is here to get metatag information about an event
include __DIR__ . "/functions_queries.php";

/**
 * Variable intTextID comes fron sx_config.php
 * Used for events that are allready described in text table, to avoid doublications
 */
if (!isset($int_TextID) || intval($int_TextID) == 0) {
    $int_TextID = 0;
}

$intEventID = 0;
if (isset($_GET["eid"])) {
    $intEventID = (int) $_GET["eid"];
}

/**
 * If intTextID > 0 intEventID cannot (shold not) be 0
 */
if (intval($int_TextID) > 0 && intval($intEventID) == 0) {
    header("location: events.php?v=2");
    exit();
}

$radio_RegisterToEvent = false;
$int_RegisterToEventID = 0;
if (isset($_GET["reg_eid"]) && intval($_GET["reg_eid"]) > 0) {
    $radio_RegisterToEvent = true;
    $int_RegisterToEventID = (int) $_GET["reg_eid"];
}

/*
 * Not open Big Calender OR Slider in events.php when an event is requested
 */

$radioFirstEventPage = false;
if (intval($int_TextID) == 0 && intval($intEventID) == 0) {
    $radioFirstEventPage = true;
}

include __DIR__ . "/config_dates.php";

/**
 * Setup Details and the very use of events is checked in sx_config.php
 * Define the metatag variables from
 *  - Title and Notes for the Events Calendar
 *  - OR the title and subtitle/notes of the requested event
 */

$strEventsTitle = "";
$memoEventsNotes = "";
$radio_StopMailWithBlacklistedIP = false;
$sql = "SELECT StopMailWithBlacklistedIP, EventsTitle, EventsNotes
    FROM events_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radio_StopMailWithBlacklistedIP = $rs["StopMailWithBlacklistedIP"];;
    $strEventsTitle = $rs["EventsTitle"];
    $memoEventsNotes = $rs["EventsNotes"];
}
$rs = null;

/**
 * Get the array for the requested event and use it in default page
 */
$arr_EventByID = null;
if (intval($intEventID) > 0) {
    $arr_EventByID = sx_getEventByID($intEventID);
}

$str_PropertyType = "Event";

if (is_array($arr_EventByID)) {
    $str_SiteTitle = $arr_EventByID[0]['EventTitle'];
    $str_MetaTitle = $str_SiteTitle;
    if (!empty($arr_EventByID[0]['EventTitle'])) {
        $sMediaURL = $arr_EventByID[0]['MediaURL'];
        if (!empty($sMediaURL)) {
            if (str_contains($sMediaURL, ';')) {
                $sMediaURL = explode(";", $sMediaURL)[0];
            }
            $str_PropertyImage = "images/" . trim($sMediaURL);
        }
    }
    if (!empty($arr_EventByID[0]['EventSubTitle'])) {
        $str_MetaDescription = $arr_EventByID[0]['EventSubTitle'];
    } elseif (!empty($arr_EventByID[0]['Notes'])) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($arr_EventByID[0]['Notes']), 120);
    }
} else {
    $str_SiteTitle = $strEventsTitle;
    $str_MetaTitle = $str_SiteTitle;
    if (!empty($memoEventsNotes)) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($memoEventsNotes), 120);
    }
}
