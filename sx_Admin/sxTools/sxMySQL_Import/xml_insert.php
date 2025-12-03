<?php

// Compatibility Check Before Inser data
//include __DIR__ . "/xml_check.php";

if ($radioContinue) {

    if ($radio_truncateTable) {
        $conn->exec("TRUNCATE TABLE $str_DBTableName");
    }

    $iTotalColums = count($arr_ColumnNames);
    $str_ColumnNames = implode(",", $arr_ColumnNames);
    $str_ColumnQuest = implode(",", $arr_ColumnQuestions);

    // Prepare the incert statement
    $sql = "INSERT INTO $str_DBTableName ($str_ColumnNames) VALUES($str_ColumnQuest)";
    $stmt_insert = $conn->prepare($sql);

    $intNode = 1;
    $intImported = 0;
    $intNotImported = 0;


    $reader = new XMLReader();

    if ($reader->open($import_FileName)) {
        // Move to the root element
        while ($reader->read() && $reader->nodeType != XMLReader::ELEMENT) {
        }
        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->depth == 1) {

                // Create a DOMDocument and import the current node
                $doc = new DOMDocument();
                $node = $reader->expand();
                $importedNode = $doc->importNode($node, true);
                $doc->appendChild($importedNode);

                // Convert the DOMDocument to SimpleXML
                $child = simplexml_import_dom($doc->documentElement);

                $radioInsert = true;
                $mixReturnedPKValue = NULL;
                if (isset($child->$str_PrimaryKeyName)) {
                    $mixPK_Value = (string) $child->$str_PrimaryKeyName;
                    $mixPK_Value = !empty($mixPK_Value) ? trim($mixPK_Value) : '';
                    if (!empty($mixPK_Value)) {
                        $stmt_check->execute([trim($mixPK_Value)]);
                        $mixReturnedPKValue = $stmt_check->fetchColumn();
                    }
                    if ($mixReturnedPKValue) {
                        $radioInsert = false;
                    }
                }else{
                    $radioInsert = false;
                    echo 'Non PK<br>';
                }

                // Insert if value don't exists
                if ($radioInsert) {
                    $arr_LoopValues = array();
                    foreach ($child as $name => $value) {
                        $iKey = array_search($name, $arr_ColumnNames);
                        if ($iKey !== false) {
                            $s_Type = $arr_ColumnTypes[$iKey];
                            $NewValue = sx_getTypeCompatibleValue($s_Type, (string) $value);
                            $arr_LoopValues[] = $NewValue;
                        }
                    }
                    $stmt_insert->execute($arr_LoopValues);
                    $intImported++;
                } else {
                    $intNotImported++;
                }
                $intNode++;
            }
        }
    }
    $reader->close();
    $stmt_insert = null;
    $stmt_check = null;

    if ($intImported > 0) {
        $iTatalRows = $intNode - 1;
        // Reset auto increment
        $sql = "ALTER TABLE $str_DBTableName AUTO_INCREMENT = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt = null;

        if ($radio_truncateTable) {
            echo '<h3>Your Table has been Trancated and Repopulated by New Data</h3>';
        } else {
            echo '<h3>Successful Insertion of New Data</h3>';
        }
        echo '<div class="msgSuccess">';
        echo "<p>Data has been Inserted for <b>$intImported Rows</b> in the Table from totaly <b>$iTatalRows Nodes</b> in the File.</p>";
        if ($intNotImported > 0) {
            echo "<p><b>$intNotImported Nodes</b> with Primary Key values that already exist in the Database Table have Not been Inserted.</p>";
        }
        echo '</div>';
    } else {
        echo '<div class="msgInfo">';
        echo '<p><b>The File has Not Been Imported</b></p>';
        echo "<p>All Nodes in the File have a <b>Prmary Key Value</b> that already exists in the Database Table. Your Table is <b>up-to-date</b>!</p>";
        echo '</div>';
    }
}
