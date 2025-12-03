<?php
if (isset($_GET["LunchMenu"]) && $radio_UseLunchMenu) {
    include __DIR__ . "/menu_lunch.php";
} else {
    include __DIR__ . "/menu_dinner.php";

    //include __DIR__ . "/reservations/calendar.php";
}