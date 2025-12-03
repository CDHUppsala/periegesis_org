<?php
$iTables = count($arrTables);
for ($t = 0; $t < $iTables; $t++) {
    $sTable = trim($arrTables[$t]);
    if ($sTable != "" && @$_POST["table_" . $t] == "Yes") {
        if ($t > 0) {
            echo "<p></p>";
        }

        if ($boolSourceMySQL) {
            //$arrFields_Source = get_arrFields_MySQL($sTable, $connSourceMySQL);
            //$arrFields_Source = get_arrFields_MySQL_Schema(sx_TABLE_SCHEMA, $sTable, $connSourceMySQL);
            $arrFields_Source = get_arrFields_MySQL_Meta($sTable, $connSourceMySQL);
        } else {
            $arrFields_Source = get_arrFields_Access($sTable, $connSourceAccess);
        }

        if (!empty($strTargetDBName)) {
            if ($boolTargetMySQL) {
                //$arrFields_Target = get_arrFields_MySQL($sTable, $connTargetMySQL);
                //$arrFields_Target = get_arrFields_MySQL_Schema($strTargetDBName, $sTable, $connTargetMySQL);
                $arrFields_Target = get_arrFields_MySQL_Meta($sTable, $connTargetMySQL);
            } else {
                $arrFields_Target = get_arrFields_Access($sTable, $connTargetAccess);
            }
        }


        if (is_array($arrFields_Source)) {
            echo '<div class="grid-container">';
            /**
             * Send the arrFields_Source to get fields from Access DB
             * in the same order as in Table (Not alphabetically)
             */
            if ($boolSourceMySQL) {
                $arrFieldsSourceDescriprion = get_arrFields_MySQL_Schema($strSourceDBName, $sTable, $connSourceMySQL);
            } else {
                $arrFieldsSourceDescriprion =  get_fieldsAccessDesription($sTable, $arrFields_Source, $connSourceAccess);
            }
            if (is_array($arrFieldsSourceDescriprion)) {
                echo '<div>';
                echo "<h3>Source Database Table: $sTable</h3>";
                echo "<table>";
                echo "<tr><th>Field Name</th><th>Type</th><th>Size</th><th>Index</th><th>Field Description</th></tr>";
                $iCount = count($arrFieldsSourceDescriprion);
                for ($i = 0; $i < $iCount; $i++) {
                    $strName = ($arrFieldsSourceDescriprion[$i][0]);
                    $strType = ($arrFieldsSourceDescriprion[$i][1]);
                    $strMaxLength = ($arrFieldsSourceDescriprion[$i][2]);
                    $strKeyIndex = ($arrFieldsSourceDescriprion[$i][3]);
                    $strDesc = ($arrFieldsSourceDescriprion[$i][4]);


                    $strClass = "";
                    $strClassType = "";
                    if (!empty($strTargetDBName)) {
                        if (!in_array($strName, array_column($arrFields_Target, '0'))) {
                            $strClass = ' class="red"';
                        }
                        if (!in_array($strType, array_column($arrFields_Target, '1'))) {
                            $strClassType = ' class="red"';
                        }
                    }
                    echo "<tr>
								<td$strClass><b>$strName</b></td>
								<td$strClassType>$strType</td>
								<td>$strMaxLength</td>
								<td>$strKeyIndex</td>
								<td>$strDesc</td>
								</tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div>The Table <b> $sTable </b> does Not Exist in Access Database.</div>";
            }
            $arrTargetFieldsDescription = null;
            if (!empty($strTargetDBName)) {
                if ($boolTargetMySQL) {
                    $arrTargetFieldsDescription = get_arrFields_MySQL_Schema($strTargetDBName, $sTable, $connTargetMySQL);
                } else {
                    $arrTargetFieldsDescription =  get_fieldsAccessDesription($sTable, $arrFields_Target, $connTargetAccess);
                }
            }
            if (is_array($arrTargetFieldsDescription)) {
                echo '<div>';
                echo "<h3>Target Database Table: $sTable </h3>";
                echo "<table>";
                echo "<tr><th>Field Name</th><th>Type</th><th>Size</th><th>Index</th><th>Field Description</th></tr>";
                $iCount = count($arrTargetFieldsDescription);
                for ($r = 0; $r < $iCount; $r++) {
                    $strName = ($arrTargetFieldsDescription[$r][0]);
                    $strType = ($arrTargetFieldsDescription[$r][1]);
                    $strMaxLength = ($arrTargetFieldsDescription[$r][2]);
                    $strKey = ($arrTargetFieldsDescription[$r][3]);
                    $strDesc = ($arrTargetFieldsDescription[$r][4]);

                    $strClass = "";
                    if (!in_array($strName, array_column($arrFields_Source, '0'))) {
                        $strClass = ' class="red"';
                    }
                    $strClassType = "";
                    if (!in_array($strType, array_column($arrFields_Source, '1'))) {
                        $strClassType = ' class="red"';
                    }

                    echo "<tr>
							<td$strClass><b>$strName</b></td>
							<td$strClassType>$strType</td>
							<td>$strMaxLength</td>
							<td>$strKey</td>
							<td>$strDesc</td></tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo '<div>The Table <b>' . $sTable . "</b> does Not Exist in Target Database - or No Target Database is defined.</div>";
            }
            echo "</div>";
        }
    }
}
