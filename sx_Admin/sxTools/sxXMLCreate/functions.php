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
	return $frs[0];
}