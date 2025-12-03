<?php

// Compatibility Check Before Update data
// include __DIR__ . "/xml_check.php";

// Proceed with Update if Compatibility Check Passes
if ($radioContinue) {

    $iTotalColumns = count($arr_ColumnNames);
    $str_ColumnNamesQuests = implode(",", $arr_ColumnNamesQuestions);

    $sql = "UPDATE $str_DBTableName SET $str_ColumnNamesQuests WHERE $str_PrimaryKeyName = ?";
    $stmt_update = $conn->prepare($sql);

    $intNode = 1;
    $intCountUpdates = 0;
    $intCountNotUpdates = 0;
    $arrNotUpdated = [];

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

                $mixPKValue = '';
                $arr_LoopValues = [];
                foreach ($child as $name => $value) {
                    // $arr_ColumnNames does not include PK Name
                    $iKey = array_search($name, $arr_ColumnNames);
                    $loopValue = (string) $value;
                    if ($iKey !== false) {
                        $s_Type = $arr_ColumnTypes[$iKey];
                        $NewValue = sx_getTypeCompatibleValue($s_Type, $loopValue);
                        $arr_LoopValues[] = $NewValue;
                    } elseif ($name == $str_PrimaryKeyName) {
                        if ($str_PrimaryKeyType === 'INT') {
                            $mixPKValue = is_numeric($loopValue) ? (int)$loopValue : null;
                        } else {
                            $mixPKValue = !empty($loopValue) ? trim($loopValue) : '';
                        }
                    }
                }
                // Execute the update if PK value not empty (integer or string)
                if (!empty($mixPKValue)) {
                    $arr_LoopValues[] = $mixPKValue;
                    !$stmt_update->execute($arr_LoopValues);

                    if ($stmt_update->rowCount() === 0) {
                        // Check if PK exists, if not, equal values are not updated
                        $stmt_check->execute([$mixPKValue]);
                        if (!$stmt_check->fetchColumn()) {
                            $arrNotUpdated[] = "The <b>PK Value $mixPKValue</b> in <b>Node $intNode</b>.";
                        }
                        $intCountNotUpdates++;
                    } else {
                        $intCountUpdates++;
                    }
                } else {
                    // Not propable, as the file's PK has been checked, but just in case
                    $radioContinue = false;
                    echo '<div class="msgError">';
                    echo "<p>The XML File at <b>Node $intNode</b> seems to lack a <b>Valid Value</b> for the Primary Key: <b>$str_PrimaryKeyName</b>.</p>";
                    if ($intNode > 1) {
                        echo "<p>Update <b>stopped</b> at that node. Previous nodes have been updated.</p>";
                    }
                    echo '<p>Please check the XML File and try again.</p>';
                    echo '</div>';
                    break;
                }
                $intNode++;
            }
        }
    }
    $reader->close();
    $stmt_update = null;
    $stmt_check = null;

    if ($radioContinue) {
        $iTotalNodes = $intNode - 1;
        $intNew = 0;
        if (!empty($arrNotUpdated)) {
            $intNew = count($arrNotUpdated);
        }
        if ($intCountUpdates > 0) {
            echo "<h3>Successful Updates</h3>";
            echo "<div class='msgSuccess'>Data has been Updated in $intCountUpdates Rows of the database table, of totally $iTotalNodes Nodes in the Import File.</div>";
        }

        if ($intCountNotUpdates > 0) {
            if ($intCountUpdates === 0 && $intNew === 0) {
                echo '<h3>The Database Table has not been Updated</h3>';
                echo '<div class="msgInfo">';
                echo "<p>The <b>content</b> of all $iTotalNodes nodes in the file is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                echo "<p>Only different contents between the file and the table are updated.</p>";
                echo '<p> Your Database Table is <b>up-to-date</b>!</p>';
                echo '</div>';
            } else {
                echo "<h3>A set of {$intCountNotUpdates} Nodes in the File of totally $iTotalNodes ones have not been updated.</h3>";

                if ($intNew === 0) {
                    echo '<div class="msgInfo">';
                    echo "<p>The <b>content</b> of $intCountNotUpdates nodes in the file is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                    echo "<p>Only different contents between the file and the table are updated.</p>";
                    echo '<p>Your Database Table is <b>up-to-date</b>!</p>';
                    echo '</div>';
                } else {
                    if ($intNew < $intCountNotUpdates) {
                        $iSub = $intCountNotUpdates - $intNew;
                        echo '<div class="msgInfo">';
                        echo "<p>The <b>content</b> of $iSub nodes in the file, out of $intCountNotUpdates non-updated ones, is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                        echo "<p>Only different contents between the file and the table are updated.</p>";
                        echo "<p>The content of <b>{$intNew} nodes</b> in the file does not exist in the database table.</p>";
                        echo '</div>';
                    }
                    echo '<h3>Consider to add New Data by clicking on Insert</h3>';
                    echo '<div class="msgInfo">';
                    echo "<p>The following <b>$intNew Primary Key Values</b> in the File does not exist in the Database Table:</p>";
                    echo "<p>" . sx_arrayToList($arrNotUpdated) . "</p>";
                    echo '</div>';
                }
            }
        }
    }
}
