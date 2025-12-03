<?php

include __DIR__ ."/config_events_slider.php";

if ($radioEventsSlider) { ?>
    <section class="hide_in_mobile">
        <?php
        include __DIR__ ."/functions_slider.php";
        include __DIR__ ."/sx_Slider.php";
        ?>
    </section>
<?php
} ?>