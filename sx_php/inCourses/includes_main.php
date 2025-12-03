<?php
    if (intval($intCourseID) > 0 && is_array($arr_Course)) {
        include __DIR__ . "/course.php";
    } else {
        include __DIR__ . "/default.php";
    }
