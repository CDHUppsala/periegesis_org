<?php
echo '<textarea style="width: 96%; height: 400px" spellcheck="false">';
$iTables = count($arrTables);
for ($t = 0; $t < $iTables; $t++) {
    $sTable = trim($arrTables[$t]);
    if ($sTable != "" && @$_POST["table_" . $t] == "Yes") {
        echo "#" . "\n";
        echo "# " . $t . "." . $sTable . ": Alter table to add field desriptions." . "\n";
        echo "#" . "\n";
        if ($boolSourceMySQL) {
            $arrFields_Source = get_arrFields_MySQL_Meta($sTable, $connSourceMySQL);
        } else {
            $arrFields_Source = get_arrFields_Access($sTable, $connSourceAccess);
        }

        if (is_array($arrFields_Source)) {
            if ($boolSourceMySQL) {
                $arrFieldsSourceDescriprion = get_arrFields_MySQL_Schema($strSourceDBName, $sTable, $connSourceMySQL);
            } else {
                $arrFieldsSourceDescriprion =  get_fieldsAccessDesription($sTable, $arrFields_Source, $connSourceAccess);
            }

            $arrTargetFieldsDescription = get_arrFields_MySQL_Schema($strTargetDBName, $sTable, $connTargetMySQL);

            if (!is_array($arrTargetFieldsDescription)) {
                echo "The Table [" . $sTable . "] does Not Exist in MySQL Database.";
                break;
            }

            /**
             * Updates only fields that contain a description
             */
            $strSQL = "";
            $iCount = count($arrFieldsSourceDescriprion);
            for ($r = 0; $r < $iCount; $r++) {
                $sFName = $arrFieldsSourceDescriprion[$r][0];
                $sFDesc = $arrFieldsSourceDescriprion[$r][4];
                if (!empty($sFDesc)) {
                    $sFDesc = sx_quateCleaner(trim($sFDesc));
                }
                if (!empty($sFDesc) && strtolower($sFName) == strtolower($arrTargetFieldsDescription[$r][0])) {
                    $s_DATA_TYPE = $arrTargetFieldsDescription[$r][1];         //floor,$tinyint,smallint,date,$datetime,$varchar,$mediumtext,$longtext;
                    //$s_COLUMN_KEY = $arrTargetFieldsDescription[$r][3];			//$PRI, $MUL;
                    $s_COLUMN_TYPE = $arrTargetFieldsDescription[$r][5];         //floor(11),$tinyint[1],$smallint[6],date,$datetime,$varchar,$mediumtext,$longtext;
                    $s_extra = $arrTargetFieldsDescription[$r][6];                //auto_increment;
                    //$s_column_default = $arrTargetFieldsDescription[$r][7];		//0,;
                    //$s_CHARACTER_SET_NAME = $arrTargetFieldsDescription[$r][8];	//$utf8mb4,$utf8;
                    $r_IS_NULLABLE = $arrTargetFieldsDescription[$r][9];        //$NO,$YES;

                    if (!empty($strSQL)) {
                        $strSQL .= ", ";
                    }
                    if ($s_extra == "auto_increment") {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " NOT NULL AUTO_INCREMENT COMMENT '" . $sFDesc . "'";
                    } elseif ($s_DATA_TYPE == "int" || $s_DATA_TYPE == "smallint" || $s_DATA_TYPE == "float" || $s_DATA_TYPE == "double" || $s_DATA_TYPE == "tinyint") {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " NULL DEFAULT '0' COMMENT '" . $sFDesc . "'";
                    } elseif ($s_DATA_TYPE == "datetime") {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " NULL DEFAULT CURRENT_TIMESTAMP COMMENT '" . $sFDesc . "'";
                    } elseif (strpos($s_DATA_TYPE, "date") > 0) {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " NULL DEFAULT NULL COMMENT '" . $sFDesc . "'";
                    } elseif ($s_DATA_TYPE == "varchar" || $s_DATA_TYPE == "text" || $s_DATA_TYPE == "mediumtext") {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " CHARACTER SET 'utf8mb4' NULL DEFAULT NULL COMMENT '" . $sFDesc . "'";
                    } else {
                        $strSQL .= " MODIFY COLUMN `" . $sFName . "` " . $s_COLUMN_TYPE . " NULL DEFAULT NULL COMMENT '" . $sFDesc . "'";
                    }
                }
            }
            if (!empty($strSQL) > 0) {
                $strSQL = " ALTER TABLE `" . strtolower($sTable) . "` " . $strSQL . ";";
                $connTargetMySQL->query($strSQL);
                echo str_replace("MODIFY COLUMN", "\n" . "MODIFY COLUMN", $strSQL) . "\n" . "\n";
            } else {
                echo "# NO Field Descriptions exist for this Table." . "\n" . "\n";
            }
        }
    }
}
echo "</textarea>";
