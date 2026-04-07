<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

function app_getRecordAsJSON($fieldName, $fieldType, $fieldValue, $numb)
{
	$x = "{";
	for ($i = 0; $i < $numb; $i++) {
		$val = $fieldValue[$i];
		$n = "";
		for ($j = 0; $j < strlen($val); $j++) {
			$c = substr($val, $j, 1);
			if ($c == "<") {
				$c = "#1001;";
			}
			if ($c == ">") {
				$c = "#1002;";
			}
			if ($c == "&") {
				$c = "#1003;";
			}
			$n .= $c;
		}
		$x .= "\"" . $fieldName[$i] . "\" : \"" . $n . "\"";
		if ($i < $numb - 1) $x .= ",";
	}
	$x .= "}";
	return $x;
}
function app_formatDate($txt, $appformat)
{
	if (app_nullTest($txt)) {
		return "";
	}
	if (trim($txt) == "") {
		return "";
	}
	$date = date_create($txt);
	$y = date_format($date, "Y");
	$m = date_format($date, "m");
	$d = date_format($date, "d");
	$h = date_format($date, "H");
	$n = date_format($date, "i");
	$s = date_format($date, "s");
	$c = substr($y, 2, 2);
	if (strlen($m) < 2) {
		$m = "0" . $m;
	}
	if (strlen($d) < 2) {
		$d = "0" . $d;
	}
	if (strlen($h) < 2) {
		$h = "0" . $h;
	}
	if (strlen($n) < 2) {
		$n = "0" . $n;
	}
	if (strlen($s) < 2) {
		$s = "0" . $s;
	}
	$t = $appformat;
	$t = str_replace("yyyy", $y, $t);
	$t = str_replace("mm", $m, $t);
	$t = str_replace("dd", $d, $t);
	$t = str_replace("yy", $c, $t);
	$t = str_replace("hh", $h, $t);
	$t = str_replace("nn", $n, $t);
	$t = str_replace("ss", $s, $t);
	return $t;
}
function app_error($errtxt)
{
	echo "{\"error\" : \"" . $errtxt . "\"}";
	exit();
}
function app_getElementValue($element, $id)
{
	$res = "";
	$uelement = strtoupper($element);
	$uid = strtoupper($id);
	$ipos1 = _instr(0, $uelement, "<" . $uid, 0);
	if ($ipos1 > 0) {
		$ipos2 = _instr(0, substr($uelement, $ipos1 - 1), ">", 0);
		$ipos3 = _instr(0, $uelement, "</" . $uid . ">", 0);
		if ($ipos2 > 0 && $ipos3 > $ipos1) {
			$res = trim(substr($element, $ipos1 + $ipos2 - 1, $ipos3 - $ipos2 - $ipos1));
		}
	}
	return $res;
}
function app_getElementArray($element, $id)
{
	$x = array();
	$uid = strtoupper($id);
	$xelement = $element;
	$l = 0;
	for ($i = 1; $i <= 10000; $i++) {
		$uelement = strtoupper($xelement);
		$ipos1 = _instr(0, $uelement, "<" . $uid, 0);
		$ipos3 = _instr(0, $uelement, "</" . $uid . ">", 0);
		if ($ipos1 > 0 && $ipos3 > $ipos1) {
			$l++;
			$x[$l] = substr($xelement, $ipos1 - 1, $ipos3 - $ipos1 + strlen($id) + 3);
			$xelement = substr($xelement, $ipos3 + strlen($id) + 2);
		} else {
			break;
		}
	}
	return $x;
}
function app_arrayLength($arr)
{
	if (is_array($arr)) {
		return count($arr);
	}
	return 0;
}
function app_loadTextFromFile($fname)
{
	$tmp = "";
	$ext = ".js";
	if (_instr(0, $fname, ".", 0) > 0) {
		$ext = "";
	}
	if (!file_exists($fname . $ext)) {
		app_error("No such file or directory: $fname.$ext");
	}
	return file_get_contents($fname . $ext);
}
function app_nullTest($x)
{
	$cc = false;
	if (!isset($x)) {
		$cc = true;
	} else {
		if (empty($x)) {
			$cc = true;
		}
	}
	return $cc;
}
function app_dbRead($conn, $sql, $count, $p)
{
	$xarr = array();
	$xsql = app_prepSQL($sql, $count);
	$stmt = $conn->stmt_init();
	$stmt->prepare($xsql);
	if ($count > 0) {
		$xarr[0] = "";
		for ($j = 1; $j <= $count; $j++) {
			$xarr[0] .= "s";
			$xarr[$j] = &$p[$j];
		}
		call_user_func_array(array($stmt, 'bind_param'), $xarr);
	}
	if (!$stmt->execute()) {
		app_error($conn->error);
	}
	if (!$dbRecordset = $stmt->get_result()) {
		app_error($conn->error);
	}
	return $dbRecordset;
}
function app_getDbConnection($dbcon, $model)
{
	$arr = array();
	$xdbcon = strtoupper($dbcon);
	$arr = $model;
	$txt = "";
	if (is_array($arr)) {
		$l = app_arrayLength($arr);
		for ($i = 0; $i < $l; $i++) {
			$x = $arr[$i]->connection;
			if (strtoupper($x) == $xdbcon) {
				$txt = $arr[$i];
				break;
			}
		}
	} else {
		$txt = $arr;
	}
	if ($txt == "") {
		app_error("APPML_ERR_UKNOWN_DB: " . $dbcon);
	}
	$dbHost = $txt->host;
	$dbName = $txt->dbname;
	$dbUser = $txt->username;
	$dbPass = $txt->password;
	$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
	if (mysqli_connect_errno()) {
		app_error("Failed to connect to MySQL: " . mysqli_connect_error());
	}
	$conn->set_charset('utf8');
	return $conn;
}
function app_dbRowcount($recset)
{
	return $recset->num_rows;
}
function app_prepSQL($sql, $count)
{
	$mysql = $sql;
	for ($i = 1; $i <= $count; $i++) {
		$mysql = str_replace("@" . (string)($i - 1), "?", $mysql);
	}
	return $mysql;
}
function app_getType($txt)
{
	$ret = "number";
	if ($txt == 252) {
		$ret = "binary";
	}
	if ($txt == 7 || $txt == 10 || $txt == 11 || $txt == 12 || $txt == 13) {
		$ret = "date";
	}
	if ($txt == 253 || $txt == 254) {
		$ret = "string";
	}
	return $ret;
}
// Helper Function
function _instr($start, $str1, $str2, $mode)
{
	if ($mode) {
		$str1 = strtolower($str1);
		$str2 = strtolower($str2);
	}
	$retval = strpos($str1, $str2, $start);
	return ($retval === false) ? 0 : $retval + 1;
}
function app_decodeJSON($txt, $bool)
{
	try {
		$jsonObj = json_decode($txt, $bool);
	} catch (Exception $e) {
		$jsonObj = "";
	}
	return $jsonObj;
}
function app_getProperty($obj, $prop)
{
	if (isset($obj->$prop)) {
		return $obj->$prop;
	}
	return "";
}
function app_getData($modDatabase, $modConfig, $txtModel, $modDateFormat, $sql)
{
	$params = array();
	$fName = array();
	$fValue = array();
	$fType = array();
	$paramCount = 0;
	$dbConnection = app_getDbConnection(app_getProperty($modDatabase, "connection"), $modConfig->databases);
	$dataOut = "";
	$dbRecordset = app_dbRead($dbConnection, $sql, $paramCount, $params);
	$totalCounter = app_dbRowcount($dbRecordset);
	$dbRecordset->close();
	$dbRecordset = app_dbRead($dbConnection, $sql, $paramCount, $params);
	$fCount = $dbRecordset->field_count;
	$i = 0;
	while ($x = $dbRecordset->fetch_field()) {
		$fType[$i] = app_getType($x->type);
		$fName[$i] = $x->name;
		$fValue[$i] = "";
		$i++;
	}
	$row = $dbRecordset->fetch_array(MYSQLI_BOTH);
	$nn = 0;
	while ($nn < $totalCounter) {
		$nn = $nn + 1;
		if ($nn >= 1 and $nn <= $totalCounter) {
			$cc = "";
			$i = count($row) / 2;
			for ($j = 0; $j < count($row) / 2; $j++) {
				$txt = $row[$j];
				if ($txt == NULL) {
					$txt = "";
				}
				if (app_getType($fType[$j]) == "date") {
					$txt = app_formatDate($txt, $modDateFormat);
				}
				$fValue[$j] = $txt;
			}
			if ($dataOut != "") {
				$dataOut .= ",";
			}
			$dataOut = $dataOut . app_getRecordAsJSON($fName, $fType, $fValue, $fCount);
		}
		$row = $dbRecordset->fetch_array(MYSQLI_BOTH);
	}
	if ($nn == 0) {
		$totalCounter = 0;
		$dataOut = $dataOut . app_getRecordAsJSON($fName, $fType, $fValue, $fCount);
	}
	$dbRecordset->close();
	$cc = <<<EOT
, "records" : [$dataOut]
EOT;
	return $cc;
}
