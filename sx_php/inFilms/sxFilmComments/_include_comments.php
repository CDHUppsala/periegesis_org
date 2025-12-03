<?php
include "_functions_comments.php";
include "getComment.php";
include "addComment.php";

$intNr = sx_GetNumberOfCommentsFilm($intFilmID);
if (intval($intNr) > 0) { 
    sx_getComents($intFilmID, $intNr); ?>
<?php }
sx_addComments();