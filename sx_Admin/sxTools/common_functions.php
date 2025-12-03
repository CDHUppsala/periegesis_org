<?php

/**
 * MySQL (SQL) data-types to XSD data-types
 */

function sx_getXSD_DataTypes($dbType)
{
	switch ($dbType) {
		case "CHARACTER":
			$dType = "xsd:string";
			break;
		case "STRING":
			$dType = "xsd:string";
			break;
		case "VAR_STRING":
			$dType = "xsd:string";
			break;
		case "BLOB":
			$dType = "xsd:string";
			break;
		case "BINARY":
			$dType = "xsd:hexBinary";
			break;
		case "NUMERIC":
			$dType = "xsd:decimal";
			break;
		case "DECIMAL":
			$dType = "xsd:decimal";
			break;
		case "NEWDECIMAL":
			$dType = "xsd:decimal";
			break;
		case "SMALLINT":
			$dType = "xsd:integer";
			break;
		case "INTEGER":
			$dType = "xsd:integer";
			break;
		case "TINY":
			$dType = "xsd:integer";
			break;
		case "BIGINT":
			$dType = "xsd:integer";
			break;
		case "LONG":
			$dType = "xsd:integer";
			break;
		case "LONGLONG":
			$dType = "xsd:integer";
			break;
		case "SHORT":
			$dType = "xsd:integer";
			break;
		case "INT24":
			$dType = "xsd:integer";
			break;
		case "FLOAT":
			$dType = "xsd:float";
			break;
		case "REAL":
			$dType = "xsd:float";
			break;
		case "DOUBLE":
			$dType = "xsd:float";
			break;
		case "BOOLEAN":
			$dType = "xsd:boolean";
			break;
		case "DATE":
			$dType = "xsd:date";
			break;
		case "TIME":
			$dType = "xsd:time";
			break;
		case "DATETIME":
			$dType = "xsd:dateTime";
			break;
		case "TIMESTAMP":
			$dType = "xsd:dateTime";
			break;
		default:
			$dType = "xsd:string";
	}
	return $dType;
}

function getXSDvalidation_NU($elType, $elValue)
{
	$convertType = "";
	$errXMLMsg = "";
	switch (strtolower($elType)) {
		case "integer":
			if (!is_int($elValue)) {
				$errXMLMsg = "Wrong Integer format";
			} else {
				$convertType = floatval($elValue);
			}
		case "decimal":
			if (strpos($elValue, ".") !== false) {
				return $elValue;
			} else {
				$errXMLMsg = "Wrong Decimal format";
			}
		case "float":
			if (!is_float($elValue)) {
				$errXMLMsg = "Wrong Float format";
			} else {
				$convertType = floatval($elValue);
			}
		case "double":
			if (!is_double($elValue)) {
				$errXMLMsg = "Wrong Double format";
			} else {
				$convertType = doubleval($elValue);
			}
		case "date":
			if (!sx_IsDate($elValue)) {
				$errXMLMsg = "Wrong Date format";
			} else {
				$convertType = $elValue;
			}
		case "boolean":
			if (is_bool($elValue)) {
				$errXMLMsg = "Wrong Boolean format";
			} else {
				$convertType = boolval($elValue);
			}
		default:
			$convertType = $elValue;
	}
	if ($errXMLMsg <> "") {
		return  $errXMLMsg;
	} else {
		return  $convertType;
	}
}

function sx_checkTypeCompatibility($sType, $mixValue)
{
	switch ($sType) {
		case "NUMERIC":
		case "DECIMAL":
		case "NEWDECIMAL":
		case "FLOAT":
		case "REAL":
		case "DOUBLE":
			// Replace comma with dot for decimal values
			$mixValue = str_replace(",", ".", $mixValue);
			return is_numeric($mixValue);

		case "SMALLINT":
		case "INTEGER":
		case "TINY":
		case "BIGINT":
		case "LONG":
		case "LONGLONG":
		case "SHORT":
		case "INT24":
		case "YEAR":
		case "BIT":
			// Check if the value is a valid integer
			return is_numeric($mixValue) && (string)(int)$mixValue === (string)$mixValue;

		case "BOOLEAN":
			// Accept true, false, 1, and 0 as valid boolean values
			return in_array(strtolower((string)$mixValue), ['true', 'false', '1', '0'], true);

		case "DATE":
			// Check if the value matches the YYYY-MM-DD format
			$date = DateTime::createFromFormat('Y-m-d', $mixValue);
			return $date && $date->format('Y-m-d') === $mixValue;

		case "TIME":
			// Check for HH:MM:SS format
			$time = DateTime::createFromFormat('H:i:s', $mixValue);
			return $time && $time->format('H:i:s') === $mixValue;

		case "DATETIME":
		case "TIMESTAMP":
			// Check for YYYY-MM-DD HH:MM:SS format
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mixValue);
			return $dateTime && $dateTime->format('Y-m-d H:i:s') === $mixValue;

		case "CHARACTER":
		case "STRING":
		case "VAR_STRING":
		case "BLOB":
		default:
			// Strings are considered valid by default
			return true;
	}
}

function sx_getTypeCompatibleValue($sType, $mixValue)
{
	$mixValue = trim($mixValue);

	switch ($sType) {
		case "NUMERIC":
		case "DECIMAL":
		case "NEWDECIMAL":
		case "FLOAT":
		case "REAL":
		case "DOUBLE":
			// Replace comma with dot for decimal values
			$mixValue = str_replace(",", ".", $mixValue);
			return is_numeric($mixValue) ? $mixValue : 0;

		case "SMALLINT":
		case "INTEGER":
		case "TINY":
		case "BIGINT":
		case "LONG":
		case "LONGLONG":
		case "SHORT":
		case "INT24":
		case "BIT":
			// Ensure integer conversion
			return is_numeric($mixValue) ? (int) $mixValue : 0;

		case "BOOLEAN":
			// Convert common truthy/falsy values to boolean equivalents
			$lowerValue = strtolower($mixValue);
			if (in_array($lowerValue, ['true', '1', 'yes'], true)) {
				return true;
			} elseif (in_array($lowerValue, ['false', '0', 'no'], true)) {
				return false;
			}
			return null; // Or a default boolean value if needed

		case "DATE":
			// Validate and format date (YYYY-MM-DD)
			$date = DateTime::createFromFormat('Y-m-d', $mixValue);
			return $date && $date->format('Y-m-d') === $mixValue ? $mixValue : null;

		case "TIME":
			// Validate and format time (HH:MM:SS)
			$time = DateTime::createFromFormat('H:i:s', $mixValue);
			return $time && $time->format('H:i:s') === $mixValue ? $mixValue : null;

		case "DATETIME":
		case "TIMESTAMP":
			// Validate and format datetime (YYYY-MM-DD HH:MM:SS)
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mixValue);
			return $dateTime && $dateTime->format('Y-m-d H:i:s') === $mixValue ? $mixValue : null;

		case "CHARACTER":
		case "STRING":
		case "VAR_STRING":
		case "BLOB":
			// Decode common HTML entities - but only if they are decoded, no effect otherwise
			// Ensure you're storing raw, sanitized data
			// Decode only if you're processing imports or expecting encoded HTML
			return htmlspecialchars_decode($mixValue, ENT_QUOTES);
		default:
			return $mixValue;
	}
}

/**
 * List of DB Tables
 */

function sx_getTableList()
{
	$conn = dbconn();
	$result = $conn->query("SHOW TABLES");
	return $result->fetchAll(PDO::FETCH_NUM);
}

function sx_GetPrimaryKey($tbl)
{
	$conn = dbconn();
	$sql = "SELECT COLUMN_NAME, DATA_TYPE, EXTRA
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
        AND TABLE_NAME = ?
        AND COLUMN_KEY = 'PRI'";
	$fstmt = $conn->prepare($sql);
	$fstmt->execute([sx_TABLE_SCHEMA, $tbl]);
	$frs = $fstmt->fetch();

	$isAutoIncrement = strpos($frs['EXTRA'], 'auto_increment') !== false;

	return [$frs['COLUMN_NAME'], $frs['DATA_TYPE'], $isAutoIncrement];
}

function sx_getFolderFilesByExtention($dir, $extention)
{
	if ($arFiles = scandir($dir)) {
		$ar_Files = array();
		$c = count($arFiles);
		for ($f = 0; $f < $c; $f++) {
			$loopFile = $arFiles[$f];
			if ($loopFile != "." && $loopFile != ".." && is_file($dir . "/" . $loopFile)) {
				$loopArr = explode(".", $loopFile);
				$loopExt = end($loopArr);
				if (strtolower($loopExt) == strtolower($extention)) {
					$ar_Files[] = $loopFile;
				}
			}
		}
		return $ar_Files;
	} else {
		return false;
	}
}
function sx_getFolderContentsGlob($dir, $isFileOrDir)
{
	/**
	 * $isFileOrDir = "is_file": to get Files, "is_dir": to get Directories
	 */
	if ($files = array_filter(glob($dir . "/*"), $isFileOrDir)) {
		return $files;
	} else {
		return false;
	}
}

function sx_getFolderContents($dir, $isFileOrDir)
{
	/**
	 * $isFileOrDir = "is_file": to get Files, "is_dir": to get Directories
	 */
	if ($arFiles = scandir($dir)) {
		$ar_Files = [];
		$c = count($arFiles);
		for ($f = 0; $f < $c; $f++) {
			$loopFile = $arFiles[$f];
			if ($loopFile != "." && $loopFile != ".." && $isFileOrDir($dir . "/" . $loopFile)) {
				$ar_Files[] = $loopFile;
			}
		}
		return $ar_Files;
	} else {
		return false;
	}
}
function sx_getFieldNamesAndTypes($sTble, $sFileds = '*')
{
	$conn = dbconn();
	$fieldNames = array();
	$fieldTypes = array();
	$strSQL = "SELECT " . $sFileds . " FROM " . $sTble;
	$stmt = $conn->query($strSQL);
	$maxcol = $stmt->columnCount();
	for ($c = 0; $c < $maxcol; $c++) {
		$meta = $stmt->getColumnMeta($c);
		$fieldNames[] = $meta['name'];
		$fieldTypes[] = $meta['native_type'];
	}
	$stmt = null;
	return [$fieldNames, $fieldTypes];
}

function return_NonUsedTables()
{
	$conn = dbconn();
	$sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute(['NonUsedTables']);
	return $stmt->fetchColumn();
}
