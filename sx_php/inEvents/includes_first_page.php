<?php

/**
 * Only for test purposes:
 * - Changes the dates of event examples to current month
 * Comment or Remove in real project
 */
//include __DIR__ . "/_update_events.php";

/**
 * For events to be shown in the site's First (default) Page
 * Include link to this file in inTexts_Includes/include_FirstPageAside.php
 */

include_once __DIR__ . "/functions_queries.php";
include_once __DIR__ . "/config_dates.php";

if ($radio_UseEventsByCalendar) {
    $radio_showSmallCalendar = false;
    $radio_showBigCalendar = false;
?>
    <section id="jqLoadCalendar">
        <?php include __DIR__ . "/events_by_calendar.php"; ?>
    </section>
<?php
}

if ($radio_UseEventsList) {
    include __DIR__ . "/events_by_list.php";
} ?>