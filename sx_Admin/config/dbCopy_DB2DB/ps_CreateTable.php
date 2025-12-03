<?php
echo '<textarea style="width: 96%; height: 540px" spellcheck="false">';
$iTables = count($arrTables);
for ($t = 0; $t < $iTables; $t++) {
    $sTable = trim($arrTables[$t]);
    if ($sTable != "" && @$_POST["table_" . $t] == "Yes") {
        echo "--" . "\n";
        echo "-- " . $t . ". " . $sTable . " Table structure" . "\n";
        echo "--" . "\n";

        $sql_Add = "";
        if ($boolSourceMySQL) {
            $arrFields_Source = get_arrFields_MySQL($sTable, $connSourceMySQL);
        } else {
            $arrFields_Source = get_arrFields_Access($sTable, $connSourceAccess);
        }

        //arrFields_Access(c,2) = PK
        if (is_array($arrFields_Source)) {
            // Send the arrFields_Source to get fields in the same order as in Table (Not alphabetically)
            if ($boolSourceMySQL) {
                $arrFieldsSourceDescriprion =  get_arrFields_MySQL_Schema($strSourceDBName, $sTable, $connSourceMySQL);
            } else {
                $arrFieldsSourceDescriprion =  get_fieldsAccessDesription($sTable, $arrFields_Source, $connSourceAccess);
            }
            if (is_array($arrFieldsSourceDescriprion)) {
                $iCount = count($arrFieldsSourceDescriprion);
                for ($i = 0; $i < $iCount; $i++) {
                    $xName = ($arrFieldsSourceDescriprion[$i][0]);
                    $strType = ($arrFieldsSourceDescriprion[$i][1]);
                    $strMaxLength = ($arrFieldsSourceDescriprion[$i][2]);
                    $strKeyIndex = ($arrFieldsSourceDescriprion[$i][3]);
                    $strDesc = ($arrFieldsSourceDescriprion[$i][4]);
                    if (!empty($strDesc)) {
                        $strDesc = sx_quateCleaner($strDesc);
                        $strDesc = " COMMENT '" . $strDesc . "'";
                    }
                    $rPK = False;
                    //if ($i == 0) {
                    if ($strKeyIndex == "PRI") {
                        $rPK = True;
                        $sPK = $xName;
                    }
                    $strParameters = get_Access2MySQL_FieldTypes($strType, $rPK, $strMaxLength);
                    if (!empty($sql_Add)) {
                        $sql_Add = $sql_Add . "," . "\n";
                    }
                    $sql_Add = $sql_Add . " `" . $xName . "` " . $strParameters . $strDesc;
                }
            }
        }
        $sql_Add = $sql_Add . "\n";
        if ($boolSourceMySQL) {
            $arrTableIndexes = get_TableIndexesMySQL($sTable, $connSourceMySQL);
        } else {
            $arrTableIndexes = get_TableIndexesAccess($sTable, $connSourceAccess);
        }
        if (is_array($arrTableIndexes)) {
            foreach ($arrTableIndexes as $Column => $Index) {
                if ($Index == "PrimaryKey" || $Index == "PRI") {
                    /**
                     * The Primary Key ($sPK) is defined here from the above function (from a previouw solution)
                     * However, it must be equal to $Column
                     */
                    $sql_Add = $sql_Add . ", PRIMARY KEY (" . $sPK . ")" . "\n";
                    $sql_Add = $sql_Add . ", UNIQUE KEY " . $sPK . "_UNIQUE (" . $sPK . ")" . "\n";
                } else {
                    /**
                     * Index contains the KEY NAME of the index, which can be different from the Column Name
                     * However, we add the Column Name as Index Name
                     */
                    $sql_Add = $sql_Add . ", KEY `" . $Column . "` (`" . $Column . "`)" . "\n";
                }
            }
        }
        $sTable = strtolower($sTable);
        if ($strDropExistingTables == "Yes") {
            $sql_Drop = "DROP TABLE IF EXISTS " . $strTargetDBName . "." . $sTable . ";";
            echo $sql_Drop . "\n" . "\n";
            $connTargetMySQL->exec($sql_Drop);
        }
        $sql_Add = "CREATE TABLE IF NOT EXISTS `" . $strTargetDBName . "`.`" . $sTable . "` (" . "\n" . $sql_Add;
        $sql_Add = $sql_Add . ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
        echo $sql_Add . "\n" . "\n";

        $connTargetMySQL->exec($sql_Add);
    }
}
echo "</textarea>";
