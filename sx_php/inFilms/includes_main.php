<?php
if (intval($iFilmID) > 0) {
    include __DIR__ . "/films_details.php";
} elseif ($radioGetFilmLists) {
    include __DIR__ . "/films_list.php";
} else {
    //include __DIR__ . "/filmsRecentAccordion.php";
    include __DIR__ . "/films_recent.php";
}
