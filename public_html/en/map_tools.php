<?php
include __DIR__ . "/siteLang/sxLang.php";

$page = $_GET['p'] ?? '';

$mapTool = [
    'validate-csv-to-geojson' => 'validate_csv_to_geojson',
    'filter-csv-to-csv-geojson-kml' => 'filter_csv_to_csv_geojson_kml',
    'convert-geojson-to-csv' => 'convert_geojson_to_csv',
    'convert-geojson-to-kml' => 'convert_geojson_to_kml',
    'convert-kml-to-geojson-to-csv' => 'convert_kml_to_geojson_to_csv',
    'convert-gpx-to-geojson' => 'convert_gpx_to_geojson',
    'split-geojson-by-book-to-geojson-kml' => 'split_geojson_by_book_to_geojson_kml',
    'filter-any-csv-json-xml' => 'filter_any_csv_json_xml'
];

if (isset($mapTool[$page])) {
    //include PROJECT_PHP . "/app_maps/map_tools/{$mapTool[$page]}.html";
    include dirname(__DIR__) ."/sxApps/ps_maps/map_tools/{$mapTool[$page]}.html";
} else {
    header('Location: map_search.php');
    exit();
}
