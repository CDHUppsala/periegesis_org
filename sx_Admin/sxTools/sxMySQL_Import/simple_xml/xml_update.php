<?php

// Compatibility Check Before Update data
include __DIR__ . "/xml_check.php";

// Proceed with Update if Compatibility Check Passes
if ($radioContinue) {

    $iTotalNodes = count($data->children());
    $iTotalColumns = count($arr_ColumnNames);
    $str_ColumnNamesQuests = implode(",", $arr_ColumnNamesQuestions);

    $sql = "UPDATE $str_DBTableName SET $str_ColumnNamesQuests WHERE $str_PrimaryKeyName = ?";
    $stmt_update = $conn->prepare($sql);

    $intNode = 1;
    $intCountUpdates = 0;
    $intCountNotUpdates = 0;
    $arrNotUpdated = [];
    foreach ($data->children() as $child) {
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

    $stmt_update = null;
    $data = null;

    if ($radioContinue) {
        if ($intCountUpdates > 0) {
            echo "<h3>Successful Updates</h3>";
            echo "<div class='msgSuccess'>Data has been Updated in $intCountUpdates Rows of the database table, of totally $iTotalNodes Nodes in the Import File.</div>";
        }

        if ($intCountNotUpdates > 0) {
            if ($intCountUpdates > 0) {
                echo "<h3>{$intCountNotUpdates} Nodes from the File of totally $iTotalNodes ones have not been updated.</h3>";
            } else {
                echo '<h3>The Database Table has not been Updated</h3>';
            }
            $intNew = 0;
            $strSubSet = "All $intCountNotUpdates";
            $strOfSet = '';
            if (!empty($arrNotUpdated)) {
                $intNew = count($arrNotUpdated);
                $strSubSet = $iTotalNodes - $intNew;
                $strOfSet = "out of <b>$iTotalNodes ones</b>";
            }
            echo '<div class="msgInfo">';
            echo "<p>The <b>content of $strSubSet Nodes</b> $strOfSet in the file is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
            echo "<p>Only different contents between the file and the table are updated.</p>";
            if ($intNew == 0) {
                echo '<p> Your Database Table is <b>up-to-date</b>!</p>';
            } else {
                echo '</div>';
                echo '<h3>Consider to add New Data by clicking on Insert</h3>';
                echo '<div class="msgInfo">';
                echo '<p>The following <b>Primary Key Values</b> in the File does not exist in the Database Table:</p>';
                echo "<p>" . sx_arrayToList($arrNotUpdated) . "</p>";
            }
            echo '</div>';
        }

    }
}
