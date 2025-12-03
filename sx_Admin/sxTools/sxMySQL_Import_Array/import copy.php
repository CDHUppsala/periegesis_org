<?php
set_time_limit(600);

if ($radioTableIsSet && !empty($import_FileName) && !empty($import_Type) && !empty($file_extension)) {
    if ($file_extension == "xml") {
        if ($import_Type == "Update") {
            include __DIR__ . "/xml_update.php";
        } else {
            $radio_truncateTable = false;
            if($import_Type === 'Truncate') {
                $radio_truncateTable = true;
            }
            include __DIR__ . "/xml_insert.php";
        }
    } elseif ($file_extension == "json") {
        if ($import_Type == "Update") {
            include __DIR__ . "/json_update.php";
        } else {
            $radio_truncateTable = false;
            if($import_Type === 'Truncate') {
                $radio_truncateTable = true;
            }
            include __DIR__ . "/json_insert.php";
        }
    } elseif ($file_extension == "csv") {
        if ($import_Type == "Update") {
            include __DIR__ . "/csv_update.php";
        } else {
            $radio_truncateTable = false;
            if($import_Type === 'Truncate') {
                $radio_truncateTable = true;
            }
            include __DIR__ . "/csv_insert.php";
        }
    }
}
