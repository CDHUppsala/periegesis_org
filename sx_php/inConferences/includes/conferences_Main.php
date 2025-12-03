<?php
/**
 * Check if participant is loged in 
 */
include_once dirname(__DIR__) .'/check_sessions.php';
include_once dirname(__DIR__) . '/functions_Basic.php';
include_once dirname(__DIR__) . "/attachment_Functions.php";

if (!isset($_GET['program'])) {
    if (intval($int_PaperID) > 0) {
        include_once dirname(__DIR__) . "/read_Paper.php";
    } elseif (intval($int_SessionID) > 0) {
        include_once dirname(__DIR__) . "/read_Session.php";
    } elseif (intval($int_ConferenceID) > 0) {
        include_once dirname(__DIR__) . "/read_Conference.php";
    } else {
        header("Location: index.php?p=1");
        exit;
    }
} elseif (intval($int_ConferenceID) > 0) {
    if ($radio_ProgramInTable && ($radio_ProgramInTabsForMobile == false || check_Mobile_Device() == false)) {
        include_once dirname(__DIR__) . "/program_Table.php";
    }
    if ($radio_ProgramInTabs || ($radio_ProgramInTabsForMobile && check_Mobile_Device())) {
        include_once dirname(__DIR__) . "/program_Tabs.php";
    }
    if ($radio_ProgramInSubtabs) {
        include_once dirname(__DIR__) . "/program_Subtabs.php";
    }
} else {
    header("Location: index.php");
    exit();
}
/**
 * ATTACHMENTS can be shown HERE, under the MAIN column, 
 * or in widescreen att the BOTTOM of the page, over FOOTER 
 * (see conferences.php in the public domain)
 */
/*
include_once __DIR__ . "/conferences_Attachments.php";
*/
