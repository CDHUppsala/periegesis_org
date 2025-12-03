<?php
set_time_limit(600);

$arr_FieldNames = [];
$arr_FieldTypes = [];
$str_PrimaryKeyName = "";
$str_PrimaryKeyType = '';
$radioTableIsSet = false;

/**
 * The variable $arr_FieldTypes is Not Used yet
 * Can be used in the future for checking field values to be imported.
 * Get table columns to check compatibility to file columns
 * Get Primary Key to exclude in Inserting and use in Updating
 */

if (isset($str_DBTableName) && !empty($str_DBTableName)) {
    $arr_PrimaryKey = sx_GetPrimaryKey($str_DBTableName);
    $str_PrimaryKeyName = $arr_PrimaryKey[0];
    $str_PrimaryKeyType = strtoupper($arr_PrimaryKey[1]);
    $strSQL = "SELECT * FROM $str_DBTableName LIMIT 1";
    $stmt = $conn->query($strSQL);
    if ($stmt) {
        $radioTableIsSet = true;
        $iCountCol = $stmt->columnCount();
        for ($c = 0; $c < $iCountCol; $c++) {
            $meta = $stmt->getColumnMeta($c);
            $arr_FieldNames[] = $meta['name'];
            $arr_FieldTypes[] = $meta['native_type'];
            $arr_FieldNameType[$meta['name']] = $meta['native_type'];
        }
    }
    $stmt = null;
}

if ($radioTableIsSet && !empty($import_FileName) && !empty($import_Type) && !empty($file_extension)) {
    if ($file_extension == "xml") {
        include "xml_check.php";
        if ($radio_ImportSubmited) {
            if ($import_Type == "Update") {
                include "xml_update.php";
            } else {
                $radio_truncateTable = false;
                if ($import_Type === 'Truncate') {
                    $radio_truncateTable = true;
                }
                include "xml_insert.php";
            }
        }
    } elseif ($file_extension == "json") {
        include "json_check.php";
        if ($radio_ImportSubmited) {
            if ($import_Type == "Update") {
                include "json_update.php";
            } else {
                $radio_truncateTable = false;
                if ($import_Type === 'Truncate') {
                    $radio_truncateTable = true;
                }
                include "json_insert.php";
            }
        }
    } elseif ($file_extension == "csv") {
        include "csv_check.php";
        if ($radio_ImportSubmited) {
            if ($import_Type == "Update") {
                include "csv_update.php";
            } else {
                $radio_truncateTable = false;
                if ($import_Type === 'Truncate') {
                    $radio_truncateTable = true;
                }
                include "csv_insert.php";
            }
        }
    }
}
