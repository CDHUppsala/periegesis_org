<?php
if (intval($int_SubjectID) > 0) {
    include __DIR__ . "/answers.php";
} else {
    include __DIR__ .  "/subjects.php";
}
