<?php
$iTables = count($arrTables);
for ($t = 0; $t < $iTables; $t++) {
    $sTable = strtolower(trim($arrTables[$t]));
    if (!empty($sTable) && !empty($_POST["table_" . $t]) && $_POST["table_" . $t] == "Yes") {
        if ($boolSourceMySQL) {
            $arrFields_Source = get_arrFields_MySQL_Meta($sTable, $connSourceMySQL);
        } else {
            $arrFields_Source = get_arrFields_Access($sTable, $connSourceAccess);
        }
        $arrFields_Target = get_arrFields_MySQL_Meta($sTable, $connTargetMySQL);

        if (is_array($arrFields_Source)) {
            /**
             * Get the Field Name of the Primary Key to check if its ID-Number exist in Targer Table
             * Get the Numerical order (index) of the PK-Field in the array of Fields
             * Use the Primary Key to order the results from the source table in Descending order when adding records
             */
            $iCount = count($arrFields_Source);
            $iPKFieldIndex = 0;
            for ($col = 0; $col < $iCount; $col++) {
                if ($arrFields_Source[$col][3] == "primary_key") {
                    $str_PKName = $arrFields_Source[$col][0];
                    $iPKFieldIndex = $col;
                    break;
                }
            }
            $intStartFromID = 0;
            if (!empty($_POST["StartInsertFromID"]) && intval($_POST["StartInsertFromID"]) > 0) {
                $intStartFromID = (int) $_POST["StartInsertFromID"];
            }
            $strWhere = "";
            if ($intStartFromID > 0) {
                $strWhere = " WHERE $str_PKName > $intStartFromID ";
            }
            if ($boolSourceMySQL) {
                $arrTypes = array();
                $strSQL = "SELECT * FROM $sTable $strWhere ORDER BY $str_PKName ASC ";
                $stmt = $connSourceMySQL->query($strSQL);
                $colcount = $stmt->columnCount();

                for ($c = 0; $c < $colcount; $c++) {
                    $meta = $stmt->getColumnMeta($c);
                    $arrTypes[] = $meta['native_type'];
                }
            } else {
                $arrResults = get_resultsAccess($sTable, $str_PKName, $connSourceAccess);
            }

            if (!empty($arrTypes)) {
                set_auto_increment($sTable, $connTargetMySQL);
                echo '<textarea style="width: 96%; height: 340px" spellcheck="false">';
                echo "#" . "\n";
                echo "# " . $t . ". " . $sTable . " Dumping data for this table" . "\n";
                echo "#" . "\n";

                /**
                 * Add the source Primary Key to target table or lett it be defined automatically
                 * The PK must be the First Field of the Source Table
                 */
                $iLoopStart = 1;
                if (!empty($_POST["IncludePK"]) && $_POST["IncludePK"] == "Yes") {
                    $iLoopStart = 0;
                }
                /**
                 * Prepare the Insert statement for Target Table
                 */
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $cIndex = 0;
                    $strNames = "";
                    $strValues = "";
                    foreach ($row as $key => $value) {
                        if ($cIndex >= $iLoopStart && strtolower($key) == strtolower($arrFields_Target[$cIndex][0])) {
                            if ($cIndex > $iLoopStart) {
                                $strNames .= ", ";
                                $strValues .= ", ";
                            }
                            $strNames .= "`" . $key . "`";
                            $strValues .= "?";
                        }
                        $cIndex++;
                    }
                    $strInsertSQL = "INSERT INTO " . $sTable . " (" . $strNames . ") VALUES (" . $strValues . ");";
                    $stmtInsert = $connTargetMySQL->prepare($strInsertSQL);
                    break;
                }

                /**
                 * Prepare the statement to check if Primary Key already exists in Target Table
                 */
                $check_sql = "SELECT " . $str_PKName . " FROM " . $sTable . " WHERE " . $str_PKName . " = ?";
                $check_stmt = $connTargetMySQL->prepare($check_sql);

                // Restart the wuery to reset the cursor to row 1
                $stmt = $connSourceMySQL->query($strSQL);
                $total = 0;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    /**
                     * Get the Primary Key value from sources table 
                     * and check if it exists in target table
                     */
                    $int_PKValue = $row[$str_PKName];
                    $check_stmt->execute([$int_PKValue]);
                    $rs = $check_stmt->fetch();
                    $radioGo = true;
                    if ($rs) {
                        $radioGo = false;
                    }
                    $rs = null;

                    if ($radioGo == false) {
                        echo " The Record with ID = $int_PKValue Exists in Table - No Record Added." . "\n";
                    } else {
                        echo " The Record with ID = $int_PKValue has been Added in Table." . "\n";
                        $arrValues = array();
                        $cIndex = 0;
                        foreach ($row as $key => $value) {
                            if ($cIndex >= $iLoopStart && strtolower($key) == strtolower($arrFields_Target[$cIndex][0])) {
                                $arrValues[] = $value;
                            }
                            $cIndex++;
                        }
                        $stmtInsert->execute($arrValues);
                    }
                    $total++;
                }
                echo "</textarea>";
            }
        }
        echo "<p>Table: " . $sTable . ", Rows: " . $total . ", Added Fields: " . $colcount . "</p>";
    }
}
