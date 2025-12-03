<?php
if (intval($iBookID) > 0) {
    include __DIR__ ."/book_details.php";
    include __DIR__ ."/reviews//include_reviews.php";
} elseif ($radioGetBookLists) {
    include __DIR__ ."/books_list.php";
} else {
    include __DIR__ ."/books_recent_accordion.php";
    //include __DIR__ ."/books_recent.php";
}
