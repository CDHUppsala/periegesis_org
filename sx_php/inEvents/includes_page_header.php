<?php
$radio_showSmallCalendar = true;
$radio_showBigCalendar = false;
if ($int_TextID == 0 && $intEventID == 0) {
    $radio_showSmallCalendar = false;
    $radio_showBigCalendar = true;
}

if ($radio_UseEventsSlider && $radio_showBigCalendar) {
    include PROJECT_PHP . "/sx_Slider/includes_events_slider.php";
}

if ($radio_UseEventsByCalendar && $radio_UseEventsByBigCalendar && $radio_showBigCalendar) { ?>
    <section class="hide_in_mobile" id="jqLoadCalendar">
        <?php
        include PROJECT_PHP . "/inEvents/events_by_calendar.php";
        ?>
    </section>
<?php
} ?>