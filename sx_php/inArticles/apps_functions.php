<?php
/**
 * Include here all externa applications that you might use
 * to insert information in the 2 multimedia fields of articles
 * 1. The application that transforms CSV files to tables
 * 2. The table multi_data, that provide title and description of different typs of media
 * 3. Prepared views from the database
 * 4. etc.
 */

function get_apps_files_to_table($str_SourceFileNames) {
    if (empty($str_SourceFileNames)) {
        header("Location: index.php");
        exit();
    }
    include dirname(__DIR__) . "/app_files_to_table/index.php";

}

function get_apps_multi_data($int_DataGroupID)
{
    if (empty($int_DataGroupID) || (int)$int_DataGroupID === 0) {
        header("Location: index.php");
        exit();
    }
    include  dirname(__DIR__) . "/app_multi_data/index.php";
}

function get_apps_database_views($strViewName, $radioFirstSelect = false)
{
    include dirname(__DIR__) . "/app_view_to_table/recogito/index.php";
}
