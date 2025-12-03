<?php
if ($radioAllowSurveys || $radioAllowComments) {
    if ($radioAllowSurveys) {
        include __DIR__ . "/survey.php";
    }
    if ($radioAllowComments) {
        include __DIR__ . "/get_comment.php";
        include __DIR__ . "/add_comment.php";
    }
}
