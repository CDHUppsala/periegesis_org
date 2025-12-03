<?php

// Check number types
function sx_CheckNummeric($int)
{
    // NzInt
    $_retval = $int;
    if (!isset($int) || empty($int) || !is_numeric($int)) {
        $_retval = 0;
    }
    return $_retval;
}
function sx_CheckInteger($id)
{
    // NzInt
    $_retval = $id;
    if (!isset($id) || empty($id)) {
        $_retval = 0;
    } else {
        $expr = '/^[1-9][0-9]*$/';
        if (!preg_match($expr, $id) || !filter_var($id, FILTER_VALIDATE_INT)) {
            $_retval = 0;
        }
    }
    return $_retval;
}
function sx_Check_Integer($int)
{
    if (filter_var($int, FILTER_VALIDATE_INT) === 0 || filter_var($int, FILTER_VALIDATE_INT) === false) {
        return 0;
    } else {
        return $int;
    }
}

function sx_CheckFloat($int)
{
    // NzInt
    $_retval = $int;
    $expr = '/^[0-9\.]{0,15}$/';
    if (!preg_match($expr, $int) || !filter_var($int, FILTER_VALIDATE_FLOAT)) {
        $_retval = 0;
    }
    return $_retval;
}
function sx_Check_Float($int)
{
    if (filter_var($int, FILTER_VALIDATE_FLOAT) === 0 || filter_var($int, FILTER_VALIDATE_FLOAT) === false) {
        return 0;
    } else {
        return $int;
    }
}
function sx_CheckIntBetween($int, $min, $max)
{
    if (filter_var($int, FILTER_VALIDATE_INT, array("options" => array("min_range" => $min, "max_range" => $max))) === false) {
        return 0;
    } else {
        return 1;
    }
}
function sx_replaceCommaToDot($float)
{
    if (is_numeric($float)) {
        if (strpos($float, ",") > 0) {
            $float = str_replace(",", ".", $float);
        }
    } else {
        $float = 0;
    }
    return $float;
}


/**
 * DATE
 */

// general check if variable is valid date or datetime

function sx_isValidDate($date)
{
    try {
        $dt = new DateTime($date);
        return true;
    } catch (Exception $e) {
        return false;
    }
}


function sx_IsDate($date, $format = 'Y-m-d')
{
    return sx_IsDateTime($date, $format);
}

function sx_IsDateTime($date, $format = 'Y-m-d H:i:s'): bool
{
    if (empty($date)) {
        return false;
    }
    $formats = [$format, 'Y-m-d H:i', 'Y-m-d', 'Y-m-d\TH:i']; // Accept additional formats
    foreach ($formats as $f) {
        $d = DateTime::createFromFormat($f, $date);
        if ($d && $d->format($f) === $date) {
            return true;
        }
    }
    return false;
}


// Get from any date (if not Y-m-d, set the format)
function sx_GetValidDate($d, $f = "Y-m-d")
{
    return DateTime::createFromFormat($f, $d);
}

//Replace the above - check outside, Not within the functions
function sx_getDateTime($d, $df = "Y-m-d")
{
    return DateTime::createFromFormat($df, $d);
}

##Get date differance in (+ or -) intervals for years/months/weeks/days)
function sx_AddToDate($date, $int, $df = 'days')
{
    $date = new DateTime($date);
    $date = $date->modify($int . " " . $df);
    return $date->format("Y-m-d");
}

// days OR y Year m Month d Day h Hours i Minute s Seconds
function sx_dateDifference($date_1, $date_2, $dateForm = 'days')
{
    $dt1 = date_create($date_1);
    $dt2 = date_create($date_2);
    $diff = date_diff($dt1, $dt2);
    $interval = $diff->$dateForm;
    //$interval = $diff->format($dateForm);
    if ($diff->invert) {
        return -1 * $interval;
    } else {
        return $interval;
    }
}

function sx_dateTimeDifference($date_1, $date_2, $dateForm = '%a')
{
    $dt1 = new DateTime($date_1);
    $dt2 = new DateTime($date_2);
    $interval = $dt1->diff($dt2);
    return $interval->format($dateForm);
}

function sx_getYear($fdate)
{

    $y = new DateTime($fdate);
    return $y->format('Y');

    //$y = DateTime::createFromFormat("Y-m-d", $fdate);
    //return $y->format("Y");
}

function sx_getMonth($fdate)
{
    $m = new DateTime($fdate);
    return $m->format('m');
    //$m = DateTime::createFromFormat("Y-m-d", $fdate);
    //return $m->format("m");
}
function sx_getMonthZero($fdate)
{
    $n = new DateTime($fdate);
    return $n->format('n');
    /*
    $m = DateTime::createFromFormat("Y-m-d", $fdate);
    return $m->format("n");
*/
}

function sx_getDay($fdate)
{
    $d = new DateTime($fdate);
    return $d->format('d');
    /*
    $d = DateTime::createFromFormat("Y-m-d", $fdate);
    return $d->format("d");
*/
}
function sx_getDayZero($fdate)
{
    $j = new DateTime($fdate);
    return $j->format('j');
    /*
    $d = DateTime::createFromFormat("Y-m-d", $fdate);
    return $d->format("j");
*/
}

// Mon = 1 Sun = 7
function sx_getWeekDay($fdate)
{
    $N = new DateTime($fdate);
    return $N->format('N');
    /*
    $d = DateTime::createFromFormat("Y-m-d", $fdate);
    return $d->format("N");
*/
}

function sx_getWeekDayZero($fdate)
{
    $w = new DateTime($fdate);
    return $w->format('w');
    /*
    $d = DateTime::createFromFormat("Y-m-d", $fdate);
    return $d->format("w");
*/
}

function sx_getWeekYearNumber($fdate)
{
    $W = new DateTime($fdate);
    return $W->format('W');
    /*
    $d = DateTime::createFromFormat("Y-m-d", $fdate);
    return $d->format("W");
*/
}

function sx_validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d->format($format) == $date;
}

function sx_getUniversalDate($d)
{
    if (!empty($d)) {
        $sufixTime = "";
        if (trim(strlen($d)) > 10 && strpos($d, " ") > 0) {
            $arrDate = explode(" ", $d);
            $d = trim($arrDate[0]);
            $sufixTime = " " . trim($arrDate[1]);
        }
        if (strpos($d, "/") > 0) {
            $date = DateTime::createFromFormat('d/m/Y', $d);
        } elseif (strpos($d, "-") > 0) {
            $date = DateTime::createFromFormat('d-m-Y', $d);
        } elseif (strpos($d, ".") > 0) {
            $date = DateTime::createFromFormat('d.m.Y', $d);
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $d);
        }
        return $date->format('Y-m-d') . $sufixTime;
    } else {
        return $d;
    }
}


function sx_getTimeToMinutes($time)
{
    $t = DateTime::createFromFormat("H:i:s", $time);
    return $t->format("H:i");
}

function sx_getBoolean($bool)
{
    if (boolval($bool) == True) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Get random code
 */
function sx_GetRandomCode($x)
{
    $_retval = "";
    for ($i = 0; $i < $x; $i++) {
        $intNumber = random_int(0, 1000);
        $iChar = 65 + intval(($intNumber / 1000) * 25);
        $intNumber = random_int(0, 1000);
        $iChar2 = 97 + intval(($intNumber / 1000) * 25);
        $int_Number = random_int(0, 9);
        $_retval .= chr($iChar) . $int_Number . chr($iChar2);
    }
    return $_retval;
}

function sx_GetRandomToken($x)
{
    $token = bin2hex(random_bytes($x));
    return $token;
}

function sx_generate_form_token($formName, $x = 64)
{
    $token = sx_GetRandomToken($x);
    $_SESSION[$formName . '_sx_token'] = $token;
    return $token;
}

function sx_valid_form_token($formName, $token)
{
    if (!isset($_SESSION[$formName . '_sx_token'])) {
        return false;
    }
    if ($_SESSION[$formName . '_sx_token'] !== $token) {
        return false;
    }
    unset($_SESSION[$formName . '_sx_token']);

    return true;
}

//  Get and Check Client IP

function sx_Get_UserIP()
{
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $user_ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $user_ip = null;
    }
    return $user_ip;
}
function sx_GetUserIP()
{
    // HTTP... are not secure as they can be set by the client
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

/**
 * Count Records for any valid SQL statement
 */
function sx_getRecordCount($sql)
{
    $conn = dbconn();
    $arSql = explode("ORDER BY", $sql);
    $arrSql = explode("FROM ", $arSql[0]);
    $strSql = "SELECT count(*) FROM " . $arrSql[1];
    $stmt = $conn->prepare($strSql);
    $stmt->execute();
    $iCount = $stmt->fetchColumn();
    $stmt = null;
    if ($iCount) {
        return $iCount;
    } else {
        return 0;
    }
}

/**
 * Count Records for the entire SQL statement, when it includes joins
 * @param mixed $sql
 * @return int
 */
function sx_getJoinRecordCount($sql)
{
    $conn = dbconn();
    $iCount = 0;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $arrRows = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
    if ($arrRows) {
        $iCount = count($arrRows);
    }
    $arrRows = null;
    return $iCount;
}

function sx_getPrimaryKeyName($tbl)
{
    $conn = dbconn();
    $sTemp = "";
    $sql = "SHOW KEYS 
        FROM " . $tbl . "
        WHERE Key_name = 'PRIMARY'";
    $fstmt = $conn->prepare($sql);
    $fstmt->execute();
    $rs = $fstmt->fetch();
    if ($rs) {
        $sTemp = $rs["Column_name"];
    }
    $fstmt = null;
    $rs = null;
    return $sTemp;
}

function sx_getPrimaryKeyName_isc($tbl)
{
    $conn = dbconn();
    $sql = "SELECT COLUMN_NAME 
        FROM information_schema.columns 
        WHERE TABLE_SCHEMA = ?
        AND table_name = ?
        AND COLUMN_KEY = 'PRI'";
    $fstmt = $conn->prepare($sql);
    $fstmt->execute([sx_TABLE_SCHEMA, $tbl]);
    return $fstmt->fetch(PDO::FETCH_COLUMN);
}

function sx_IsUniquePKey($tbl, $column)
{
    $conn = dbconn();
    $sql = "SHOW KEYS 
        FROM " . $tbl . "
        WHERE Column_name = ?
        AND NOT Non_unique ";
    $fstmt = $conn->prepare($sql);
    $fstmt->execute([$column]);
    $rs = $fstmt->fetch();
    if ($rs) {
        $fstmt = null;
        $rs = null;
        return true;
    } else {
        $fstmt = null;
        $rs = null;
        return false;
    }
}


function sx_IsAutoincrement($tbl, $column)
{
    $conn = dbconn();
    $sql = "SELECT EXTRA
        FROM information_schema.columns 
        WHERE TABLE_SCHEMA = ?
        AND table_name = ? 
        AND COLUMN_NAME = ?
        AND Extra = 'auto_increment'";
    $fstmt = $conn->prepare($sql);
    $fstmt->execute([sx_TABLE_SCHEMA, $tbl, $column]);
    $sTemp = $fstmt->fetch(PDO::FETCH_COLUMN);
    if ($sTemp) {
        $fstmt = null;
        return true;
    } else {
        $fstmt = null;
        return false;
    }
}
function sx_getTableComments($request_Table)
{
    $conn = dbconn();
    $sql = "SELECT TABLE_COMMENT FROM information_schema.tables
    WHERE TABLE_SCHEMA = ?
    AND table_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([sx_TABLE_SCHEMA, $request_Table]);
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

function sx_getColumnComment($schema, $table, $column)
{
    $conn = dbconn();
    $sql = "SELECT COLUMN_COMMENT FROM information_schema.columns 
    WHERE TABLE_SCHEMA = ?
    AND table_name = ?
    AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$schema, $table, $column]);
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

function sx_getColumnsTypeComments($schema, $table)
{
    $conn = dbconn();
    $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT 
    FROM information_schema.columns
    WHERE TABLE_SCHEMA = ?
    AND table_name = ?
    ORDER BY ORDINAL_POSITION";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$schema, $table]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function sx_getTableFields($table)
{
    $conn = dbconn();
    $sql = "SELECT * FROM " . $table . " LIMIT 1";
    $fstmt = $conn->query($sql);
    $frs = $fstmt->fetch(PDO::FETCH_ASSOC);
    $ret = null;
    if (is_array($frs)) {
        $ret = array_keys($frs);
        $frs = null;
    } else {
        // For empty tables
        $frs = null;
        $ret = array();
        $colcount = $fstmt->columnCount();
        for ($c = 0; $c < $colcount; $c++) {
            $ret[] = $fstmt->getColumnMeta($c)['name'];
        }
    }
    return $ret;
}

function sx_writeToLog($error_type)
{
    $strPhPath = realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/private/");

    $sUserIP = sx_Get_UserIP();
    $strEditTextFile = date('Y-m-d H:i:s');
    $strEditTextFile .= "\t | " . $error_type;
    $strEditTextFile .= "\t | " . $sUserIP;
    $strEditTextFile .=  "\t | " . gethostbyaddr($sUserIP);
    $strEditTextFile .=  "\t | " . $_SERVER["HTTP_USER_AGENT"];

    $myFile = $strPhPath . "/sxBadRobots_64.txt";
    if (file_exists($myFile)) {
        $fh = fopen($myFile, 'a');
    } else {
        $fh = fopen($myFile, 'w');
    }
    fwrite($fh, $strEditTextFile . "\n");
    fclose($fh);
}

//  Get the field value of any field from any table
function sx_GetAnyFieldValue($tbl, $rqFN, $idFN, $id)
{
    $conn = dbconn();
    $pursue = true;
    $_retval = "";
    if (empty($tbl) || empty($rqFN) || empty($idFN)) {
        $pursue = false;
    }

    if (!sx_checkTableAndFieldNames($tbl) || !sx_checkTableAndFieldNames($rqFN) || !sx_checkTableAndFieldNames($idFN)) {
        $pursue = false;
    }
    if (intval($id) == 0) {
        $pursue = false;
    } else {
        $id = intval($id);
    }
    if ($pursue) {
        $sxSQL = "SELECT " . $rqFN . " AS FieldName FROM " . $tbl . " WHERE " . $idFN . " = " . $id;
        $sxrs = $conn->query($sxSQL);
        $_retval = $sxrs->fetch(PDO::FETCH_COLUMN);
        $sxrs = null;
    }
    return $_retval;
}

/**
 * @param string $txt : the string to be shorten
 * @param int $x : the number of characters to be returned + to dot (.)
 * @return string : returns the requested number of characters from a string
 */
function sx_get_Left_Part($txt, $x)
{
    if (!empty($txt)) {
        $txt = sx_removeQuotes(strip_tags($txt));
        if (strlen($txt) > $x) {
            $intPos = strpos($txt, " ", $x);

            if ($intPos > 0) {
                $txt = trim(substr($txt, 0, $intPos));
            }
        }
    }
    return $txt;
}

function sx_check_image_suffix($str)
{
    if (!empty($str)) {
        if (
            str_contains($str, ".jpg")  ||
            str_contains($str, ".jpeg") ||
            str_contains($str, ".gif") ||
            str_contains($str, ".png") ||
            str_contains($str, ".webp") ||
            str_contains($str, ".svg")
        ) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function sx_check_color_prefix($str)
{
    if (!empty($str)) {
        if (
            str_contains($str, "#")  ||
            str_contains($str, "rgb(") ||
            str_contains($str, "rgba(")
        ) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
