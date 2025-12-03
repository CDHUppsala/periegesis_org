<?php
if (intval($intEventID) > 0 && is_array(($arr_EventByID))) {
    include __DIR__ . "/default.php";
} elseif (!empty($strEventsTitle)) { ?>
    <article>
        <h1 class="head"><span><?= $strEventsTitle ?></span></h1>
        <div class="text"><div class="text_max_width"><?= $memoEventsNotes ?></div></div>
    </article>
<?php
}

/**
 * The TAG section is placed here to load content by ajax
 */
if ($radio_UseEventsByMonth) { ?>
    <section id="jqLoadMonth">
        <?php
        include __DIR__ . "/events_by_month.php";
        ?>
    </section>
<?php
} ?>