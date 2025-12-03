<?php
if ($radioTableIsSet && !empty($import_FileName) && !empty($import_Type) && !empty($file_extension)) {
    if ($import_Type == "Update") {
        include __DIR__ . "/db_update.php";
    } else {
        $radio_truncateTable = false;
        if($import_Type === 'Truncate') {
            $radio_truncateTable = true;
        }
        include __DIR__ . "/db_insert.php";
    }
}
