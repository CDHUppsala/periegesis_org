<?php
if (intval($int_ReportID) > 0) {
    include __DIR__ . "/reports.php";
} else {
    include __DIR__ . "/default.php";
}
