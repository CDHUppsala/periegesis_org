<?php
require realpath(PROJECT_PATH . "/sx_Conn/connMySQL.php");

/**
 * ACCESS or MySQL
 * The 2 variables are defined dynamically
 */

$strTopRecords = null;
$strLimitRecords = null;
$radioMySQLDatabase = true;
 
if ($radioMySQLDatabase) {
	$sDateSymbol = "'";
	$strLimitRecords_1 = "LIMIT 1";
	$strLimitRecords_100 = "LIMIT 100";
	$strTopRecords_1 = null;
	$strTopRecords_100 = null;
} else {
	$sDateSymbol = "#";
	$strTopRecords_1 = "TOP 1";
	$strTopRecords_100 = "TOP 100";
	$strLimitRecords_1 = null;
	$strLimitRecords_100 = null;
}
?>