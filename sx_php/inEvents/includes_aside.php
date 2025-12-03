<?php
/**
 * The TAG section is placed here to load content by ajax
 */
if ($radio_UseEventsByCalendar && $radio_showSmallCalendar) { ?>
    <section class="jqNavMainToBeCloned" id="jqLoadCalendar">
        <?php include __DIR__ . "/events_by_calendar.php"; ?>
    </section>
<?php
}
if ($radio_UseEventsList) {
    include __DIR__ . "/events_by_list.php"; 
}
if ($radio_UseEventsByWeek) { ?>
    <section class="jqNavMainToBeCloned" id="jqLoadWeek">
        <?php include __DIR__ . "/events_by_week.php"; ?>
    </section>
<?php
}
if ($radio_UseAdvertises) {
    get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
    get_Main_Advertisements("Bottom");
} ?>