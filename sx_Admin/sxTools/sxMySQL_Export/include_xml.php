<?php

$filename = "xml_" . $strDBTable . "_" . date('Y-m-d') . ".xml";
$filePath = PATH_ToExportFolder . $filename;


if ($rsQuery->rowCount() > 0) {
    // Save the XML file on the server
    $writer = new XMLWriter();
    $writer->openURI($filePath);
    $writer->startDocument('1.0', 'UTF-8');
    $writer->startElement('table');

    while ($row = $rsQuery->fetch(PDO::FETCH_ASSOC)) {
        $writer->startElement($strDBTable);
        foreach ($row as $key => $value) {
            $writer->startElement($key);
            if ($value === null) {
                $writer->text('');
            } elseif (is_numeric($value)) {
                $writer->text($value);
            } elseif (preg_match('/[<>&]/', $value)) {
                $value = cleanArroundText($value);
                $writer->writeCdata($value);
            } else {
                if(!empty($value)) {
                    $value = cleanArroundText($value);
                }
                $writer->text($value);
            }
            $writer->endElement();
        }
        $writer->endElement();
    }

    $writer->endElement();
    $writer->endDocument();
    $writer->flush();

    // Send the file to the client if requested
    if ($radioDownload) {
        if (file_exists($filePath)) {
            $fileSize = filesize($filePath);

            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: max-age=0');

            // Choose method based on file size
            if ($fileSize < 10 * 1024 * 1024) {
                readfile($filePath);
            } else {
                $fp = fopen($filePath, 'rb');
                try {
                    fpassthru($fp);
                } finally {
                    fclose($fp);
                }
            }
        } else {
            throw new RuntimeException("File not found: $filePath");
        }
    }
}
