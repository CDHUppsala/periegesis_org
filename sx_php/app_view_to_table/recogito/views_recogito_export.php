<?php

/**
 * Server-side download of csv, xml and json files
 * No defined limits for their size
 */

$rsQuery = array();
$strExportType = '';
$str_requestedRecogitoView = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['ExportType'])) {
        $strExportType = $_POST['ExportType'];
    }
    if (!empty($_POST['RecogitoView'])) {
        $str_requestedRecogitoView = $_POST['RecogitoView'];
    }


    switch ($str_requestedRecogitoView) {
        case 'views_books_grouped_by_chapter_sections':
            $sql = "SELECT Book, Chapter, Sections
                FROM views_books_grouped_by_chapter_sections";
            break;

        case 'views_wiki_persons':
            $sql = "SELECT Wikidata, PersonInWikidata, PersonInPausanias, Description, Entity, Wikipedia, FatherLabel, FatherURL, MotherLabel, MotherURL
                FROM views_wiki_persons";
            break;

        case 'views__events_by_alphabet':
            $sql = "SELECT Books, Events, Links, TagsAndComments
                FROM views__events_by_alphabet";
            break;

        case 'views__events_by_section':
            $sql = "SELECT Books, Events, Links, TagsAndComments
                FROM views__events_by_section";
            break;

        case 'views__events_within_grouped_sections':
            $sql = "SELECT Events, Links, TagsAndComments, Books
                FROM views__events_within_grouped_sections";
            break;

        case 'views__events_grouped_within_sections':
            $sql = "SELECT Books, Events 
                FROM views__events_grouped_within_sections";
            break;

        case 'views__persons_by_alphabet':
            $sql = "SELECT Books, Persons, Links, TagsAndComments
                FROM views__persons_by_alphabet";
            break;

        case 'views__persons_by_section':
            $sql = "SELECT Books, Persons, Links, TagsAndComments
                FROM views__persons_by_section";
            break;

        case 'views__persons_within_grouped_sections':
            $sql = "SELECT Persons, Links, TagsAndComments, Books
                FROM views__persons_within_grouped_sections";
            break;

        case 'views__persons_grouped_within_sections':
            $sql = "SELECT Books, Persons
                FROM views__persons_grouped_within_sections";
            break;

        case 'views__places_by_alphabet':
            $sql = "SELECT Books, Places, Links, LatLng, TagsAndComments
                FROM views__places_by_alphabet";
            break;

        case 'views__places_by_section':
            $sql = "SELECT Books, Places, Links, LatLng, TagsAndComments
                FROM views__places_by_section";
            break;

        case 'views__places_within_grouped_sections':
            $sql = "SELECT Places, Links, LatLng, TagsAndComments, Books
                FROM views__places_within_grouped_sections";
            break;

        case 'views__places_grouped_within_sections':
            $sql = "SELECT Books, Places, Maps
                FROM views__places_grouped_within_sections";
            break;

        default:
            $sql = '';
    }

    if (!empty($sql)) {
        $rsQuery = $conn->prepare($sql);
        $rsQuery->execute();
    }
}

if (!empty($rsQuery) && $rsQuery->rowCount() > 0) {
    if ($strExportType === 'csv') {

        $filename = "csv_" . $str_requestedRecogitoView . "_" . date('Y-m-d') . ".csv";

        // Fetch column names from the query result
        $columnCount = $rsQuery->columnCount();
        $columnNames = array();
        for ($i = 0; $i < $columnCount; $i++) {
            $columnMeta = $rsQuery->getColumnMeta($i);
            $columnNames[] = $columnMeta['name'];
        }

        // Set headers to indicate that a CSV file is being downloaded
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write column headers to CSV file
        fputcsv($output, $columnNames, ',', '"', "\\");


        // Loop through the result set and write each row to CSV file
        while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row, ',', '"', "\\");
        }

        // Close output stream
        fclose($output);
    } elseif ($strExportType === 'xml') {

        $filename = "xml_" . $str_requestedRecogitoView . "_" . date('Y-m-d') . ".xml";

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><table></table>');
        while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
            $rowElement = $xml->addChild('row');
            foreach ($row as $key => $value) {
                // Convert special characters to XML entities
                if (!empty($value)) {
                    $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                }
                $rowElement->addChild($key, $value);
            }
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0'); // Disable caching

        // Output the XML Document 
        echo $xml->asXML();
    } elseif ($strExportType === 'json') {

        $filename = "json_" . $str_requestedRecogitoView . "_" . date('Y-m-d') . ".json";
        $viewdata = $rsQuery->fetchAll(PDO::FETCH_ASSOC);

        // Convert data to JSON format with UTF-8 encoding
        $jsonContent = json_encode($viewdata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        unset($viewdata);

        // Set appropriate headers for JSON file download
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the JSON content 
        echo $jsonContent;
    }
}
unset($rsQuery);
exit();

/*
$arr_RecogitoViews = [
    'views_wiki_persons',
    'view_animals',
    'view_artworks',
    'view_attributes',
    'view_epithets',
    'view_focalisations',
    'view_interventions',
    'view_materials',
    'view_measures',
    'view_movements',
    'view_objects',
    'view_quotes',
    'view_transformations',
    'view_txs',
    'view_works',
    'views__events_by_alphabet',
    'views__events_by_section',
    'views__events_within_grouped_sections',
    'views__persons_by_alphabet',
    'views__persons_by_section',
    'views__persons_within_grouped_sections',
    'views__persons_grouped_within_sections',
    'views__places_by_alphabet',
    'views__places_by_section',
    'views__places_within_grouped_sections'
];
*/
