<?php

include dirname(__DIR__) . "/functionsLanguage.php";
include dirname(__DIR__) . "/login/lockPage.php";
include dirname(__DIR__) . "/functionsTableName.php";
include dirname(__DIR__) . "/functionsDBConn.php";

/**
 * Get variables from the configuration of table groups, if they are avaliable
 */

function sx_alter_table_comments($table, $commnet)
{
    if (!empty($commnet) && !empty($table)) {
        $commnet = str_replace(array("\r", "\n"), '', $commnet);
        $commnet = preg_replace("/\s+/u", " ", $commnet);
        if (!empty($commnet)) {
            $strLength = strlen($commnet);
            if ($strLength > 2048) {
                echo "\n - Help has NOT BEEN added as Comment to the table: $table
                \n - It contains $strLength character with max allowed: 2048";
            } else {
                $conn = dbconn();
                $sql = "ALTER TABLE ". $table ." COMMENT = '". $commnet ."';";
                $conn->exec($sql);
                echo "\n - Help HAS BEEN added as Comment to the table: $table";
            }
        } else {
            echo "\n - Help is EMPTY and has NOT BEEN added as Comment to the table: $table";
        }
    } else {
        echo "\n - Either Help or Table Name is EMPTY!";
    }
}


if (!empty($_POST["HelpByTableName"]) && !empty($_POST["TableName"])) {
    $strTableName = trim($_POST["TableName"]);
    $strTableHelp = "";
    if (!empty($_POST[$strTableName])) {
        $strTableHelp = trim(sx_replaceQuotes($_POST[$strTableName]));
    } else {
        echo "\n - Error in Table Name $strTableName";
        exit;
    }
    //## Add to or Update the sx_help_by_group Table
    $radioExist = False;
    $strSQL = "SELECT TableName FROM sx_help_by_table WHERE TableName  = ? AND LanguageCode = ?";
    $stmt = $conn->prepare($strSQL);
    $stmt->execute([$strTableName, sx_DefaultAdminLang]);
    $rs = $stmt->fetch(PDO::FETCH_NUM);
    if ($rs) {
        $radioExist = True;
    }
    $rs = null;

    if ($radioExist == False) {
        $sql = "INSERT INTO sx_help_by_table (LanguageCode, TableName, TableHelp) 
				VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([sx_DefaultAdminLang, $strTableName, $strTableHelp]);
        echo "\n - Help to Table $strTableName has been Inserted";
    } else {
        $sql = "UPDATE sx_help_by_table 
				SET  TableHelp = ? 
				WHERE TableName = ?
				AND LanguageCode = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$strTableHelp, $strTableName, sx_DefaultAdminLang]);
        echo "\n - Help to Table $strTableName has been Updated";
    }
    if(!empty($_POST["AlterTableComment"]) && $_POST["AlterTableComment"] == 'Yes') {
        sx_alter_table_comments($strTableName,$strTableHelp);
    }
}

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
