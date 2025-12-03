<?php
if (isset($_SESSION["Students_" . sx_DefaultSiteLang])) { ?>
    <section>
        <h2 class="head"><?= lngWelcome ?>
            <span><?= mb_substr(@$_SESSION["Students_FirstName"], 0, 1) . ". " . mb_substr(@$_SESSION["Students_LastName"], 0, 1) . "." ?></span>
        </h2>
    </section>
<?php
}

include __DIR__ . "/nav_courses.php";

if ($radio_UseStudentsLogin) {
    include __DIR__ . "/login/nav_login.php";
}

if ($radio_UseAdvertises) {
    get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
    get_Main_Advertisements("Bottom");
} ?>