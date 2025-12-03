<?php

$filename = "xsd_" . $strDBTable . "_" . date('Y-m-d') . ".xsd";
$filePath = PATH_ToExportFolder . $filename;


/**
 * Get the array of used fields
 */
$arrTableFileds = $_POST["TableFields"];
if (is_array($arrTableFileds) && is_array($arrDataTypes)) {


    $query = "DESCRIBE {$strDBTable}";
    $stmt = $conn->query($query);

    if ($stmt) {
        // Create a new XMLWriter instance
        $writer = new XMLWriter();
        $writer->openURI($filePath);  // Open file to write
        $writer->startDocument('1.0', 'UTF-8');  // Start XML document

        // Start XSD structure
        $writer->startElement('xs:schema');
        $writer->writeAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');

        // Add the element for the table
        $writer->startElement('xs:element');
        $writer->writeAttribute('name', $strDBTable);

        // Add complex type (the table structure)
        $writer->startElement('xs:complexType');
        $writer->startElement('xs:sequence');

        // Loop through the columns and create elements with data types
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columnName = $row['Field'];
            $columnType = $row['Type'];

            // Convert MySQL column types to XML Schema types (basic example)
            $xsdType = sx_getXSD_DataTypes($columnType);

            // Create an element for each column
            $writer->startElement('xs:element');
            $writer->writeAttribute('name', $columnName);
            $writer->writeAttribute('type', $xsdType);
            $writer->endElement();  // Close xs:element
        }

        // Close XSD structure
        $writer->endElement();  // Close xs:sequence
        $writer->endElement();  // Close xs:complexType
        $writer->endElement();  // Close xs:element
        $writer->endElement();  // Close xs:schema

        // Close the XML writer
        $writer->endDocument();
    } else {
        echo "Error: Could not describe the table.";
    }

    if ($radioDownload) {
        if (file_exists($filePath)) {
            $fileSize = filesize($filePath);

            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . $fileSize); // File size for accuracy
            header('Cache-Control: max-age=0');
            readfile($filePath);
        } else {
            echo "<h2>The table has been exported to the default folder of the remote server</h2>";
        }
    }
}
