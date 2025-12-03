<?php
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";
include __DIR__ . "/configFunctions.php";
include __DIR__ . "/functionsImages.php";

const PATH_ToExportFolder = PROJECT_PRIVATE . "/import_export_files/";

/**
 * Export the entire table and its rows to csv, xml and json files
 * The variables $arrColumnNames, $aResults and $request_Table come from the parent file
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["HiddenDBTable"])) {
        $strDBTable = $_POST["HiddenDBTable"];
    }

    if (!empty($_POST["ExportType"])) {
        $str_exportType = $_POST["ExportType"];
    }

    if (!empty($strDBTable) && !empty($str_exportType)) {
        $sql = "SELECT * FROM " . $strDBTable;
        $rsQuery = $conn->prepare($sql);
        $rsQuery->execute();

        if (!empty($rsQuery) && $rsQuery->rowCount() > 0) {

            if ($str_exportType == 'csv') {
                $filename = "csv_" . $request_Table . "_" . date('Y-m-d') . ".csv";

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
                fputcsv($output, $columnNames);

                // Loop through the result set and write each row to CSV file
                while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
                    fputcsv($output, $row);
                }

                // Close output stream
                fclose($output);
                $rsQuery = null;
            } elseif ($str_exportType == 'xml') {

                $filename = "xml_" . $request_Table . "_" . date('Y-m-d') . ".xml";

                $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><table></table>');

                // Fetch data from the query result and add it to the XML document
                while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
                    // Create a new XML element for each row
                    $rowElement = $xml->addChild('row');
                    // Add data from the row to the XML element
                    foreach ($row as $key => $value) {
                        // Convert special characters to XML entities
                        if (!empty($value)) {
                            $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                        }
                        // Add the key-value pair to the XML element
                        $rowElement->addChild($key, $value);
                    }
                }

                header('Content-Type: application/xml; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0'); // Disable caching

                // Output the XML Document
                echo $xml->asXML();
            } elseif ($str_exportType == 'json') {

                $filename = "json_" . $request_Table . "_" . date('Y-m-d') . ".json";
                $data = $rsQuery->fetchAll(PDO::FETCH_ASSOC);

                // Convert data to JSON format with UTF-8 encoding
                $jsonContent = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

                // Set appropriate headers for JSON file download
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                // Output the JSON content
                echo $jsonContent;
            }
        }
    }
}
