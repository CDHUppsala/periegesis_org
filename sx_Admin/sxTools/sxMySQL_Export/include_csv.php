<?php

$filename = "csv_" . $strDBTable . "_" . date('Y-m-d') . ".csv";
$filePath = PATH_ToExportFolder . $filename;

if ($rsQuery->rowCount() > 0) {

    $columnCount = $rsQuery->columnCount();
    $columnNames = [];
    for ($i = 0; $i < $columnCount; $i++) {
        $columnMeta = $rsQuery->getColumnMeta($i);
        $columnNames[] = $columnMeta['name'];
    }

    $fp = fopen($filePath, 'w');
    fputcsv($fp, $columnNames); // Write headers
    while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($fp, $row);
    }
    fclose($fp);

    if ($radioDownload) {
        $fileSize = filesize($filePath);
        
        // Set headers for file download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . $fileSize); // Send file size in the header
    
        // Choose method based on file size
        if ($fileSize < 10 * 1024 * 1024) { // For files smaller than 10MB
            // Use readfile for small files
            readfile($filePath);
        } else {
            // For larger files, use fpassthru
            $fp = fopen($filePath, 'rb');
            fpassthru($fp);
            fclose($fp);
        }
    
        exit;
    }
    
}
