<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/functionsTableName.php";
//include "functionsDBConn.php";

/**
 * Get variables from the configuration of table groups, if they are avaliable
 */


if (!empty($_POST["TableNamesByGroup"])) {
    $strTableNames = $_POST["TableNamesByGroup"];
    if (strpos($strTableNames, ",") == 0) {
        $strTableNames .= ",";
    }

    $arrTableName = explode(",", $strTableNames);
    $iNumber = count($arrTableName);
    for ($i = 0; $i < $iNumber; $i++) {
        $strTableHelp = "";
        $strTName = trim($arrTableName[$i]);
        if (!empty($strTName)) {
            $strTableHelp = trim(sx_replaceQuotes(@$_POST[$strTName]));
        } else {
            break;
        }
        //## Add to or Update the sx_help_by_group Table
        $radioExist = False;
        $strSQL = "SELECT TableName FROM sx_help_by_table WHERE TableName  = ? AND LanguageCode = ?";
        $stmt = $conn->prepare($strSQL);
        $stmt->execute([$strTName, sx_DefaultAdminLang]);
        $rs = $stmt->fetch(PDO::FETCH_NUM);
        if ($rs) {
            $radioExist = True;
        }
        $rs = null;

        if ($radioExist == False) {
            $sql = "INSERT INTO sx_help_by_table (LanguageCode, TableName, TableHelp) 
				VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([sx_DefaultAdminLang, $strTName, $strTableHelp]);
        } else {
            $sql = "UPDATE sx_help_by_table 
				SET  TableHelp = ? 
				WHERE TableName = ?
				AND LanguageCode = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$strTableHelp, $strTName, sx_DefaultAdminLang]);
        }
    }
}
