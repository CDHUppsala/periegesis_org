<?php

$filename = "json_" . $strDBTable . "_" . date('Y-m-d') . ".json";
$filePath = PATH_ToExportFolder . $filename;

if ($rsQuery->rowCount() > 0) {
    $data = $rsQuery->fetchAll(PDO::FETCH_ASSOC);

    $jsonContent = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $jsonContent = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);


    // Save JSON content to the server
    file_put_contents($filePath, $jsonContent);
    if ($radioDownload) {

        // Set appropriate headers for JSON file download
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . filesize($filePath)); // Set content length for the download

        // Read the file and output to the client
        readfile($filePath);
    }
}
