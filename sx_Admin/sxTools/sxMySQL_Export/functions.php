<?php

/**
 * MySQL (SQL) data-types to XSD data-types
 */

function sx_getXSD_DataTypes($dbType)
{
	$dbType = strtoupper($dbType);
	switch ($dbType) {
		case "CHARACTER":
		case "STRING":
		case "VAR_STRING":
		case "BLOB":
			$dType = "xsd:string";
			break;
		case "BINARY":
			$dType = "xsd:hexBinary";
			break;
		case "NUMERIC":
		case "DECIMAL":
		case "NEWDECIMAL":
			$dType = "xsd:decimal";
			break;
		case "SMALLINT":
		case "INTEGER":
		case "TINY":
		case "BIGINT":
		case "LONG":
		case "LONGLONG":
		case "SHORT":
		case "INT24":
			$dType = "xsd:integer";
			break;
		case "FLOAT":
		case "REAL":
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
	$sql = "SELECT COLUMN_NAME
	    FROM INFORMATION_SCHEMA.COLUMNS
	    WHERE TABLE_SCHEMA = ?
	    AND TABLE_NAME = ?
	    AND COLUMN_KEY = 'PRI'";
	$fstmt = $conn->prepare($sql);
	$fstmt->execute([sx_TABLE_SCHEMA, $tbl]);
	$frs = $fstmt->fetch();
	if ($frs) {
		return $frs[0];
	} else {
		return NULL;
	}
}
function return_NonUsedTables()
{
	$conn = dbconn();
	$sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute(['NonUsedTables']);
	return $stmt->fetchColumn();
}

function cleanArroundText($text) {
    // Replace multiple spaces, tabs, and newlines with a single space
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Trim leading and trailing spaces
    $text = trim($text);
    
    return $text;
}