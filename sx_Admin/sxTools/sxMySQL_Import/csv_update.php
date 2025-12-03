<?php
// Compatibility Check Before Updating data
// include __DIR__ . "/csv_check.php";


if ($radioContinue) {

    $str_ColumnNamesQuests = implode(",", $arr_ColumnNamesQuests);
    $sql = "UPDATE $str_DBTableName SET $str_ColumnNamesQuests WHERE $str_PrimaryKeyName = ?";
    $stmt_update = $conn->prepare($sql);

    $line = 1;
    $intCountUpdates = 0;
    $intCountNotUpdates = 0;
    $arrNotUpdated = [];

    // Reset the file pointer to the beginning of the file
    rewind($data);
    // The fgetcsv() reads the CSV file by row and creates an array of fields
    while (($arrLineData = fgetcsv($data, 0, $columnSeparator,'"','\\')) !== false) {
        // Jumb to line 2
        if ($line ===  1) {
            $line++;
            continue;
        }

        /**
         * Get the values of each filed from the other rows of the CSV file
         * Loop through used fields and get their values
         * Primary Key can be both number and string
         */
        $intPKValue = NULL;
        $arr_LoopValues = [];
        for ($c = 0; $c < $int_Columns; $c++) {
            $name = $arr_Headers[$c];
            $value = $arrLineData[$c];
            if ($name == $str_PrimaryKeyName) {
                if ($str_PrimaryKeyType === 'INT') {
                    $intPKValue = is_numeric($value) ? (int)$value : null;
                } else {
                    $intPKValue = !empty($value) ? trim($value) : '';
                }
            } else {
                $s_Type = $arr_FieldNameType[$name];
                $arr_LoopValues[] = sx_getTypeCompatibleValue($s_Type, $value);
            }
        }

        // Execute the update if PK value not empty (integer or string)
        if (!empty($intPKValue)) {
            $arr_LoopValues[] = $intPKValue;
            $stmt_update->execute($arr_LoopValues);
            if ($stmt_update->rowCount() === 0) {
                // Check if PK exists, if not, equal values are not updated
                $stmt_check->execute([$intPKValue]);
                if (!$stmt_check->fetchColumn()) {
                    $arrNotUpdated[] = "The <b>PK Value $intPKValue</b> in <b>Line $line</b>.";
                }
                $intCountNotUpdates++;
            } else {
                $intCountUpdates++;
            }
        } else {
            // Not propable, as the file's PK has been checked, but just in case
            $radioContinue = false;
            echo '<div class="msgError">';
            echo "<p>The CSV File at <b>Line {$line}</b> seems to lack a valid <b>Value</b> for the Primary Key: <b>{$str_PrimaryKeyName}</b>.</p>";
            if ($line > 2) {
                echo '<p>Update <b>stoped</b> at that line. Previous lines have been updated.</p>';
            }
            echo '<p>Please check the CSV File and try again.</p>';
            echo '</div>';
            break;
        }
        $line++;
    }
    $stmt_update = null;
    $stmt_check = null;
    fclose($data);

    if ($radioContinue) {
        $iTotalLines = $line - 2;
        $intNew = 0;
        if (!empty($arrNotUpdated)) {
            $intNew = count($arrNotUpdated);
        }
        if ($intCountUpdates > 0) {
            echo "<h3>Successful Updates</h3>";
            echo "<div class='msgSuccess'>Data has been Updated in $intCountUpdates Rows of the database table, of totally $iTotalLines lines in the Import File.</div>";
        }

        if ($intCountNotUpdates > 0) {
            if ($intCountUpdates === 0 && $intNew === 0) {
                echo '<h3>The Database Table has not been Updated</h3>';
                echo '<div class="msgInfo">';
                echo "<p>The <b>content</b> of all $iTotalLines lines in the file is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                echo "<p>Only different contents between the file and the table are updated.</p>";
                echo '<p> Your Database Table is <b>up-to-date</b>!</p>';
                echo '</div>';
            } else {
                echo "<h3>A set of {$intCountNotUpdates} lines in the File of totally $iTotalLines ones have not been updated.</h3>";

                if ($intNew === 0) {
                    echo '<div class="msgInfo">';
                    echo "<p>The <b>content</b> of $intCountNotUpdates lines in the file is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                    echo "<p>Only different contents between the file and the table are updated.</p>";
                    echo '<p>Your Database Table is <b>up-to-date</b>!</p>';
                    echo '</div>';
                } else {
                    if ($intNew < $intCountNotUpdates) {
                        $iSub = $intCountNotUpdates - $intNew;
                        echo '<div class="msgInfo">';
                        echo "<p>The <b>content</b> of $iSub lines in the file, out of $intCountNotUpdates non-updated ones, is <b>identical</b> to the content of the corresponding rows in the database table.</p>";
                        echo "<p>Only different contents between the file and the table are updated.</p>";
                        echo "<p>The content of <b>{$intNew} lines</b> in the file does not exist in the database table.</p>";
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
