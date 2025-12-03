<?php
include __DIR__ . "/search_films.php";
include __DIR__ . "/films_nav.php";
if ($radio_UseAdvertises) {
    get_Main_Advertisements("Bottom");
}
