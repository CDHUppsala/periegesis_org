<?php


/**
 * Check the uniqueness of primary key in XML-file
 */
function sx_checkXMLForUniquePrimaryKeys($sPKName)
{
	$xmlDoc = new DOMDocument();
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->formatOutput = true;
	$xmlDoc->validateOnParse = false;

	$radioErrors = false;
	$strErrors = "";
	if ($xmlDoc->load(CON_XML_File_Path) === false) {
		$radioErrors = true;
		$strErrors = "Error in XML-File";
	} else {
		$x = $xmlDoc->documentElement;
		$arrPKValues = array();
		$k = 1;
		foreach ($x->childNodes as $item) {
			$sPKField = $item->getElementsByTagName($sPKName);
			$sPKValue = $sPKField->item(0)->nodeValue;
			if (in_array($sPKValue, $arrPKValues)) {
				$radioErrors = true;
				$strErrors .= "Double value in Node: [" . $k . "] Value: [" . $sPKValue . "]<br>";
			};
			$arrPKValues[] = $sPKValue;
			$k++;
		}
	}
	$arrPKValues = null;
	return [$radioErrors, $strErrors];
}

/**
 * Check the uniqueness of the Update/Add Key in table when other than Primary Key
 */
function sx_checkTableForUniquePrimaryKeys($AutoKey, $PrimeKey)
{
	$conn = dbconn();
	$radioErrors = false;
	$strErrors = "";
	$strTblPKValues = "";
	$strSQL = "SELECT " . $AutoKey . ", " . $PrimeKey . "
		FROM " . CON_Table. "
		ORDER BY " . $PrimeKey . " ASC ";
	$rs = $conn->query($strSQL)->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults = $rs;
	}
	$rs = null;

	if (is_array($aResults)) {
		$i_Rows = count($aResults);
		for ($i_Row = 1; $i_Row < $i_Rows; $i_Row++) {
			$loopValue = strval($aResults[$i_Row][1]);
			if ($strTblPKValues == $loopValue) {
				$radioErrors = True;
				$strErrors .= "Αύξοντας Αριθμός: [" . $aResults[$i_Row][0] . "] Τιμή: [" . $loopValue . "]<br>";
			}
			$strTblPKValues = $loopValue;
		}
	}
	$aResults = null;
	$strTblPKValues = null;
	return [$radioErrors,$strErrors];
}



/**
 * Check compatibility for all lines of the XML file
 */
function sx_checkEntireXMLFileCompatibility()
{
	$errTypeAll = false;
	$errTypeAllMsg = "";
	if (isset($_POST["UpdateableFields"])) {
		$arrFields = explode(",", $_POST["UpdateableFields"]);
		$arrTypes = explode(",", $_POST["UpdateableFieldsTypes"]);
	} else {
		echo "Error in Updateable fields";
		exit();
	}

	$xmlDoc = new DOMDocument();
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->formatOutput = true;
	$xmlDoc->validateOnParse = false;

	if ($xmlDoc->load(CON_XML_File_Path) === false) {
		echo "Error in XML-File";
		exit();
	}

	$xml = $xmlDoc->documentElement;
	$iLoop = 1;
	foreach ($xml->childNodes as $item) {
		$iCountFields = count($arrFields);
		for ($f = 0; $f < $iCountFields; $f++) {
			$loopField = $arrFields[$f];
			$loopType = $arrTypes[$f];
			$loopNode = $item->getElementsByTagName($loopField);
			if ($loopNode->length == 0) {
				$errTypeAll = true;
				$errTypeAllMsg .= "The Table Field: [<b>". $loopField ."</b>] does not exist in the XML-File Node: ". $iLoop ."<br>";
			} else {
				$loopValue = $loopNode->item(0)->nodeValue;
				$checkInput = sx_checkTypeCompatibility($loopType, $loopValue);
				if (!$checkInput) {
					$errTypeAll = true;
					$errTypeAllMsg .= "Data Type Error in Node: [<b>". $iLoop ."</b>], Element: [<b>". $loopNode->item(0)->nodeName . "</b>], Type: [<b>". $loopType ."</b>] Value: [" . substr($loopValue, 0, 25) . "]<br>";
				}
			}
		}
		$iLoop++;
	}
	return [$errTypeAll,$errTypeAllMsg];
}

$radioSuccessfulUpdate = False;
$intUpdates = 0;
$intNonUpdatedRecords = 0;
$strNonUpdatedRecords = "";
function sx_updateArchiveToDatabase()
{
	global $radioSuccessfulUpdate, $intUpdates, $intNonUpdatedRecords, $strNonUpdatedRecords;
    $strAutoField = trim(@$_POST["AutoField"]);
    $strPrimeKey = trim(@$_POST["PrimeKey"]);
    if (isset($_POST["UpdateableFields"])) {
        $arrFields = explode(",", $_POST["UpdateableFields"]);
        $arrTypes = explode(",", $_POST["UpdateableFieldsTypes"]);
    } else {
        echo "Error in Updateable fields";
        exit();
    }

    $xmlDoc = new DOMDocument();
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->formatOutput = true;
    $xmlDoc->validateOnParse = false;

    if ($xmlDoc->load(CON_XML_File_Path) === false) {
        echo "Error in XML-File";
        exit();
    }

    $xml = $xmlDoc->documentElement;
    $iLoop = 1;
    $iCountFields = count($arrFields);
    $conn = dbconn();
    foreach ($xml->childNodes as $item) {
        /**
         * Get both the SQL SET-string and Bind values in arrays
         * Get the SQL-string later by imploding the array
         */
        $arrSetFieldNames = array();
        $arrSetFieldValues = array();
        $mixPrimeKeyValue = 0;
		$radioLoopUpdate = true;
        for ($f = 0; $f < $iCountFields; $f++) {
            $loopField = $arrFields[$f];
            $loopType = $arrTypes[$f];
            $loopNode = $item->getElementsByTagName($loopField);
            if ($loopNode->length == 0) {
                $radioLoopUpdate = false;
                $strNonUpdatedRecords .= "The Table Field: [" . $loopField . "] does not exist in the XML-File Node: ". $iLoop ."<br>";
            } else {
				$loopValue = $loopNode->item(0)->nodeValue;

                if (!empty($loopValue)) {
                    $checkInput = sx_checkTypeCompatibility($loopType, $loopValue);
                }else{
					$checkInput = true;
					$loopValue = null;
				}
                if (!$checkInput) {
                    $radioLoopUpdate = false;
                    $strNonUpdatedRecords .= "Not compatible Node: [" . $iLoop . "] Element: [" . $loopNode->item(0)->nodeName . "] Type: [" . $loopType . "] Value: [" . substr($loopValue, 0, 25) . "]<br>";
                } elseif ($radioLoopUpdate) {
					/**
					 * Do not add Primary Key, if it is the autoincrement key of the table
					 * Then, Don't add autoincrement keys anyway
					 */
                    if (strval($loopField) != strval($strPrimeKey)) {
                        if (strval($loopField) != strval($strAutoField)) {
                            $loopValue = sx_getTypeCompatibleValue($loopType, $loopValue);
                            $arrSetFieldNames[] = $loopField ." = ?";
                            $arrSetFieldValues[] = $loopValue;
                        }
                    } else {
						$mixPrimeKeyValue = $loopValue;
						/**
						 * Check if XML-Primary Key Value exists in Database Table.
						 * Use prepare statements to allow for Upload Keys with string values  
						 */
						$sql = "SELECT ". $strPrimeKey ."
							FROM ". CON_Table." 
							WHERE ". $strPrimeKey ." = ?";
						$pre = $conn->prepare($sql);
						$pre->execute([$loopValue]);
						$rs = $pre->fetchColumn();
						if(!$rs) {
							$radioLoopUpdate = false;
							$strNonUpdatedRecords .= "Primary Key not found in Table: Node: [" . $iLoop . "] Element: [" . $loopNode->item(0)->nodeName . "] Type: [" . $loopType . "] Value: [" . substr($loopValue, 0, 25) . "]<br>";
						}
						$rs = null;
                    }
                }
            }
        }
        $iLoop++;
        /**
         * Update the record
         * Set Primary Key value as the last item in the array
         * Implode the SQL SET string
         */
        if ($radioLoopUpdate) {
            $arrSetFieldValues[] = $mixPrimeKeyValue;
            $strSetFieldName = implode(", ", $arrSetFieldNames);
            $sql = "UPDATE ". CON_Table." 
			SET $strSetFieldName 
			WHERE $strPrimeKey = ?";
			//echo $sql ."<hr>". implode(", ", $arrSetFieldValues);
            $conn->prepare($sql)->execute($arrSetFieldValues);
			$intUpdates++;
			$radioSuccessfulUpdate = true;
		}else{
			$intNonUpdatedRecords++;
		}
    }
}


$radioSuccessfulAdding = False;
$intAdds = 0;
$intDouble = 0;
$intNonAddedRecords = 0;
$strNonAddedRecords = "";
function sx_addArchiveToDatabase() {
	global $radioSuccessfulAdding, $intAdds, $intDouble, $intNonAddedRecords, $strNonAddedRecords;
    $strAutoField = trim(@$_POST["AutoField"]);
    $strPrimeKey = trim(@$_POST["PrimeKey"]);
    if (isset($_POST["UpdateableFields"])) {
        $arrFields = explode(",", $_POST["UpdateableFields"]);
        $arrTypes = explode(",", $_POST["UpdateableFieldsTypes"]);
    } else {
        echo "Error in Updateable fields";
        exit();
    }

    $xmlDoc = new DOMDocument();
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->formatOutput = true;
    $xmlDoc->validateOnParse = false;

    if (@$xmlDoc->load(CON_XML_File_Path) === false) {
        echo "Error in XML-File";
        exit();
    }

    $xml = $xmlDoc->documentElement;
    $iLoop = 1;
    $iCountFields = count($arrFields);
    $conn = dbconn();
    foreach ($xml->childNodes as $item) {
        /**
         * Get both the SQL SET-string and Bind values in arrays
         * Get the SQL-string later by imploding the array
         */
		$arrSetFieldNames = array();
		$arrSetQuestions = array();
        $arrSetFieldValues = array();
        $mixPrimeKeyValue = 0;
		$radioLoopAdd = true;
        for ($f = 0; $f < $iCountFields; $f++) {
            $loopField = $arrFields[$f];
            $loopType = $arrTypes[$f];
            $loopNode = $item->getElementsByTagName($loopField);
            if ($loopNode->length == 0) {
                $radioLoopAdd = false;
                $strNonAddedRecords .= "Table Field: [" . $loopField . "] does not exist in the XML-File Node: ". $iLoop ."<br>";
            } else {
                $loopValue = $loopNode->item(0)->nodeValue;


                if (!empty($loopValue)) {
                    $checkInput = sx_checkTypeCompatibility($loopType, $loopValue);
                }else{
					$checkInput = true;
					$loopValue = null;
				}

                if (!$checkInput) {
                    $radioLoopAdd = false;
                    $strNonAddedRecords .= "Not compatible Node: [" . $iLoop . "] Element: [" . $loopNode->item(0)->nodeName . "] Type: [" . $loopType . "] Value: [" . substr($loopValue, 0, 25) . "]<br>";
                } elseif ($radioLoopAdd) {
					/**
					 * Do not add Primary Key, if it is the autoincrement key of the table
					 * Then, Don't add autoincrement keys anyway
					 */
					if (strval($loopField) != strval($strPrimeKey)) {
                        if (strval($loopField) != strval($strAutoField)) {
                            $loopValue = sx_getTypeCompatibleValue($loopType, $loopValue);
                            $arrSetFieldNames[] = $loopField;
                            $arrSetQuestions[] = "?";
                            $arrSetFieldValues[] = $loopValue;
                        }
                    } else {
						$mixPrimeKeyValue = $loopValue;
						/**
						 * Check if XML-Primary Key Value exists in Database Table. 
						 * Use prepare statements to allow for Upload Keys with string values  
						 */
						$sql = "SELECT ". $strPrimeKey ."
							FROM ". CON_Table." 
							WHERE ". $strPrimeKey ." = ?";
						$pre = $conn->prepare($sql);
						$pre->execute([$loopValue]);
						$rs = $pre->fetchColumn();
						if($rs) {
							$radioLoopAdd = false;
							$intDouble ++;
							$strNonAddedRecords .= "The Primary Key already exists in Table: Node: [" . $iLoop . "] Element: [" . $loopNode->item(0)->nodeName . "] Type: [" . $loopType . "] Value: [" . substr($loopValue, 0, 25) . "]<br>";
						}
						$rs = null;
						/**
						 * If Update Key is other than Primary Key, 
						 * Add allso the value of the Update Key
						 */
						if ($radioLoopAdd && $strAutoField != $strPrimeKey) {
							$arrSetFieldNames[] = $loopField;
							$arrSetQuestions[] = "?";
							$arrSetFieldValues[] = $loopValue;
						}
                    }
                }
            }
        }
        $iLoop++;
        /**
         * Add only XML-records with PK-value that does not exist in the database table
         * Implode the SQL Queries
         */
        if ($radioLoopAdd) {
            $strSetFieldName = implode(", ", $arrSetFieldNames);
            $strSetQuestions = implode(", ", $arrSetQuestions);
            $sql = "INSERT INTO ". CON_Table." 
			($strSetFieldName) 
			VALUES ($strSetQuestions) ";
			//echo $sql ."<hr>". implode(", ", $arrSetFieldValues) ."<hr>";
            $conn->prepare($sql)->execute($arrSetFieldValues);
			$intAdds++;
			$radioSuccessfulAdding = true;
		}else{
			$intNonAddedRecords++;
		}
    }
}
