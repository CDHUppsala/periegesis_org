<?php

// Compatibility Check Before Updating data
// include __DIR__ . "/json_check.php";

if ($radioContinue) {
    $iTotalColums = count($arr_ColumnNames);
    $str_ColumnNamesQuests = implode(",", $arr_ColumnNamesQuests);

    // The update statement
    $sql = "UPDATE $str_DBTableName SET $str_ColumnNamesQuests WHERE $str_PrimaryKeyName = ?";
    $stmt_update = $conn->prepare($sql);

    $iTotalNodes = count($arr_json);
    $intNode = 1;
    $intCountUpdates = 0;
    $intCountNotUpdates = 0;
    $arrNotUpdated = [];

    foreach ($arr_json as $json) {
        $mixPKValue = NULL;
        $arr_LoopValues = [];
        foreach ($json as $name => $value) {
            if ($name == $str_PrimaryKeyName) {
                if ($str_PrimaryKeyType === 'INT') {
                    $mixPKValue = is_numeric($value) ? (int)$value : null;
                } else {
                    $mixPKValue = !empty($value) ? trim($value) : '';
                }
            } else {
                $s_Type = $arr_FieldNameType[$name];
                $arr_LoopValues[] = sx_getTypeCompatibleValue($s_Type, $value);
            }
        }

        // Execute the update if PK value not empty (integer or string)
        if (!empty($mixPKValue)) {
            $arr_LoopValues[] = $mixPKValue;
            $stmt_update->execute($arr_LoopValues);
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
            echo "<p>The JSON File at <b>Node {$intNode}</b> seems to lack a valid <b>Value</b> for the Primary Key: <b>{$str_PrimaryKeyName}</b>.</p>";
            if ($intNode > 1) {
                echo '<p>Update <b>stoped</b> at that node. Previous nodes have been updated.</p>';
            }
            echo '<p>Please check the File and try again.</p>';
            echo '</div>';
            break;
        }
        $intNode++;
    }
    unset($arr_json);
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
