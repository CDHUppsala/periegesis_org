<?php

include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";

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
        if (!empty($strTName) && !empty($_POST[$strTName])) {
            $strTableHelp = trim(sx_replaceQuotes($_POST[$strTName]));
        } else {
            echo "\n - Error in Table Name $strTName";
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
            echo "\n - Help to Table $strTName has been Inserted";
        } else {
            $sql = "UPDATE sx_help_by_table 
				SET  TableHelp = ? 
				WHERE TableName = ?
				AND LanguageCode = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$strTableHelp, $strTName, sx_DefaultAdminLang]);
            echo "\n - Help to Table $strTName has been Updated";
        }
    }
}


//## Get form inputs and add selections to the config Table
if (!empty($_POST["HelpByGroupName"]) && !empty($_POST["GroupName"])) {
	$sGroupName = $_POST["GroupName"];
	$strGroupHelp = trim(sx_replaceQuotes($_POST["GroupHelp"]));

	//## Add to or Update the sx_help_by_group Table
	$radioExists = False;
	$strSQL = "SELECT GroupName 
		FROM sx_help_by_group 
		WHERE GroupName  = ? 
		AND LanguageCode = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$sGroupName, sx_DefaultAdminLang]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	if ($rs) {
		$radioExists = True;
	}
	$stmt = null;
	$rs = null;

	if ($radioExists == False) {
		$sql = "INSERT INTO sx_help_by_group (LanguageCode, GroupName, GroupHelp) VALUES (?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([sx_DefaultAdminLang, $sGroupName, $strGroupHelp]);
        echo "\n - Help to Group $sGroupName has been Inserted";
	} else {
		$sql = "UPDATE sx_help_by_group SET GroupHelp = ? WHERE GroupName = ? AND LanguageCode = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strGroupHelp, $sGroupName, sx_DefaultAdminLang]);
        echo "\n - Help to Group $sGroupName has been Updated";
	}
}