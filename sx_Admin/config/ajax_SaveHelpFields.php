<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/functionsDBConn.php";
include __DIR__ ."/functionsTableName.php";
//include "functionsDBConn.php";

//## Get form inputs and add selections to the config Table
if (!empty($request_Table)) {
	//## Get the Form Inputs for every field as an ordered array
	$arrFields = sx_getTableFields($request_Table);
	$arrFieldsHelp = array();
	$iCount = count($arrFields);
	for ($i = 0; $i < $iCount; $i++) {
		$sFieldName = $arrFields[$i];
		$xFieldValue = @$_POST["help" . $sFieldName];
        if (!empty($xFieldValue)) {
            $arrFieldsHelp[$sFieldName] = $xFieldValue;
        }
	}
	$strFieldsHelp = json_encode($arrFieldsHelp, JSON_UNESCAPED_UNICODE);

	//## Add to or Update the sx_config_tables Table
	$radioExists = False;
	$strSQL = "SELECT HelpByTableID 
		FROM sx_help_by_table WHERE TableName = ? AND LanguageCode = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$request_Table,sx_DefaultAdminLang]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	if ($rs) {
		$radioExists = True;
	}
	$rs = null;

	if ($radioExists) {
		$sql = "UPDATE sx_help_by_table 
		SET HelpByField = ?
		WHERE TableName = ? AND LanguageCode = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strFieldsHelp, $request_Table, sx_DefaultAdminLang]);
	} else {
		$sql = "INSERT INTO sx_help_by_table 
		(LanguageCode, TableName, HelpByField)
		VALUES (?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([sx_DefaultAdminLang, $request_Table, $strFieldsHelp]);
	}
	header("location: configHelpByFields.php");
	exit();
}?>
