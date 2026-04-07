<?php

/**
 * Include here all externa applications that you might use
 * to insert information in the 2 multimedia fields of articles
 * 1. The application that transforms CSV files to tables
 * 2. The table multi_data, that provide title and description of different typs of media
 * 3. Prepared views from the database
 * 4. etc.
 */

function get_apps_files_to_table($str_SourceFileNames)
{
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


function get___apps_geo_to_map($str_SourceFileNames, $loade_MediaMapScritps)
{
    if (empty($str_SourceFileNames)) {
        header("Location: index.php");
        exit();
    }

    include dirname(__DIR__) . "/app_maps/map_in_media/index.php";
}


function get_apps_geo_to_map($str_SourceFilePaths, $loade_MediaMapScritps)
{
    $allowedExtensions = ['geojson', 'kml', 'topojson', 'gpx', 'csv', 'json'];
    static $mapCounter = 0;

    $file_Paths = array_filter(array_map('trim', explode(';', $str_SourceFilePaths)));

    foreach ($file_Paths as $file_Path) {
        if (empty($file_Path)) {
            continue;
        }

        $extension = strtolower(pathinfo($file_Path, PATHINFO_EXTENSION));

        if(!file_exists('../imgMedia/'. $file_Path) || !is_file('../imgMedia/'. $file_Path)) {
            continue; 
        }

        if (!in_array($extension, $allowedExtensions)) {
            continue; 
        }

        $source_FilePath = $file_Path;

        $mapCounter++;
        $map_ID = 'map_' . $mapCounter;

        // Include the map template (index.php)
        include dirname(__DIR__) . "/app_maps/maps_in_articles/index.php";
        $loade_MediaMapScritps = false;
    }
}

