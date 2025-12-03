<?php
$iTables = count($arrTables);
for ($t = 0; $t < $iTables; $t++) {
    $r = 0;
    $c = 0;
    $sTable = strtolower(trim($arrTables[$t]));
    if ($sTable != "" && @$_POST["table_" . $t] == "Yes") {
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
             */
            $iCount = count($arrFields_Source[0]);
            $iPKFieldIndex = 0;
            for ($col = 0; $col < $iCount; $col++) {
                if ($arrFields_Source[$col][3] == "primary_key") {
                    $str_PKName = $arrFields_Source[$col][0];
                    $iPKFieldIndex = $col;
                    break;
                }
            }

            if ($boolSourceMySQL) {
                $arrResults = get_resultsMySQL($sTable, $str_PKName, $connSourceMySQL);
            } else {
                $arrResults = get_resultsAccess($sTable, $str_PKName, $connSourceAccess);
            }

            if (is_array($arrResults)) {
                echo "<h3>Table " . $t . ": " . $sTable . "</h3>";
                $iRows = count($arrResults);
                for ($r = 0; $r < $iRows; $r++) {
                    /**
                     * Get the ID-value of the Primary Key
                     * Use the Numerical order (index) of the PK-Field to get the value
                     * The query bellow is still not fetched, so we check the result by counting rows
                     */
                    $int_PKValue = $arrResults[$r][$iPKFieldIndex][2];
                    $sql = "SELECT " . $str_PKName . " FROM " . $sTable . " WHERE " . $str_PKName . " = " . $int_PKValue;
                    $rs = $connTargetMySQL->query($sql);
                    $radioGo = False;
                    if ($rs->rowCount() > 0) {
                        $radioGo = True;
                    }
                    $rs = null;

                    echo $sql;
                    if ($radioGo == False) {
                        echo ' <span class="red">The Record does Not Exists in Table - No Record Updated</span><br>';
                    } else {
                        echo ' <span class="blue">The Record has been Updated</span><br>';
                        $strSQL = "UPDATE `" . $sTable . "` SET ";
                        $arrValues = array();
                        $iColumns = count($arrResults[0]);
                        for ($c = 1; $c < $iColumns; $c++) {
                            $sFName = $arrResults[$r][$c][0];
                            if (strtolower($sFName) == strtolower($arrFields_Target[$c][0])) {
                                $sFType = $arrResults[$r][$c][1];
                                $sFValue = $arrResults[$r][$c][2];
                                $sFValue = get_fieldValue($sFType, $sFValue);
                                if ($c > 1) {
                                    $strSQL .= ", ";
                                }
                                $strSQL .= "`" . $sFName . "` = ?";
                                $arrValues[] = $sFValue;
                            }
                        }
                        $strSQL .= " WHERE `" . $str_PKName . "` = ?";
                        //$arrValues = array_push($arrValues, $int_PKValue);
                        $arrValues[] = $int_PKValue;
                        $stmt = $connTargetMySQL->prepare($strSQL);
                        $stmt->execute($arrValues);
                        echo $strSQL . "<hr>";
                    }
                }
            }
        }
        $strFieldsEffected = "Updated Fields: ";
        echo "<h4>Table: " . $t . ": " . $sTable . ", Rows: " . $r . ", " . $strFieldsEffected . $c . "</h4>";
        $i_Rows = $i_Rows + $r;
        $iFields =  $iFields + $c;
    }
}
