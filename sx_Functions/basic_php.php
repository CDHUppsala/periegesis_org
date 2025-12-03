<?php

/**
 * @param string $qstring : the Key name of POST or GET request
 * @return mixed : the value of the request or null
 */
function return_Get_or_Post_Request($qstring)
{
    if (isset($_POST[$qstring])) {
        return trim($_POST[$qstring]);
    } elseif (isset($_GET[$qstring])) {
        return trim($_GET[$qstring]);
    } else {
        return null;
    }
}

/**
 * ======================================
 * INTEGER/FLOAT Functions
 * ======================================
 */

/**
 * @param mixed $int : check if the value is integer
 * @return int : the intiger or zero
 */
function return_Filter_Integer($int)
{
    if (filter_var($int, FILTER_VALIDATE_INT) === 0 || filter_var($int, FILTER_VALIDATE_INT) === false) {
        return 0;
    } else {
        return (int) $int;
    }
}

/**
 * @param mixed $int : check if the value is float
 * @return float : the float or zero
 */
function return_Filter_Float($int)
{
    if (filter_var($int, FILTER_VALIDATE_FLOAT) === 0 || filter_var($int, FILTER_VALIDATE_FLOAT) === false) {
        return 0;
    } else {
        return $int;
    }
}

/**
 * Check if a numeric value is between 2 values
 * @param mixed $int : integer or float
 * @param mixed $max
 * @param mixed $max
 * @return bool : true or false
 */
function return_Check_Int_Between($int, $min, $max)
{
    if (filter_var($int, FILTER_VALIDATE_INT, array("options" => array("min_range" => $min, "max_range" => $max))) === false) {
        return 0;
    } else {
        return 1;
    }
}

/**
 * ======================================
 * DATE Functions
 * ======================================
 */

 /*
- is valid date (DATE/DATETIME) of any format
- is valid DATE of a particular format
- isvalid  DATETIME of a particular format

 */


/**
 * general check if variable is valid date or datetime
 * @param mixed $date
 * @return bool
 */
function sx_isValidDate($date)
{
    try {
        $dt = new DateTime($date);
        return true;
    } catch (Exception $e) {
        return false;
    }
}


function isValidDate($date, $format = 'Y-m-d')
{
    return isValidDateTime($date, $format);
}

function isValidDateTime($date, $format = 'Y-m-d H:i:s'): bool
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

/**
 * @param mixed $date : Checks if a string is a date compatible to a defined format
 * @param string $format Default format: Y-m-d
 * - For the default format, Datetime is transformed to Date before checking
 * @return bool : Returns True or False
 */
function return_Is_Date($date, $format = 'Y-m-d')
{
    if (!empty($date)) {
        if ($format == 'Y-m-d') {
            $date = return_Date_From_Datetime($date);
        }
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    } else {
        return false;
    }
}

/**
 * CHECK THE FOLLOWING
 * ===================================
 */

// Helper function to check if a string is a valid date
function isAnyDate($date)
{
    return (bool)strtotime($date);
}
function isDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * =======================================
 * END CHECK
 */

/**
 * @param $d : date string
 * @return string : Checks if a string contains Datetime and
 *   returns only the Date part
 * Deals only with strings
 */
function return_Date_From_Datetime($d)
{
    if (!empty($d) && trim(strlen($d)) > 10 && strpos($d, " ") > 0) {
        $arrDate = explode(" ", $d);
        $d = trim($arrDate[0]);
    }
    return $d;
}

/**
 * Add/abstract date intervals 
 * @param string $date : date or datetime
 * @param int $int : integer to be added (+ or -)
 * @param string $df : date format - years/months/weeks/days
 * @return string : Returns a new date
 */
function return_Add_To_Date($date, $int, $df = 'days')
{
    $date = new DateTime($date);
    $date = $date->modify($int . " " . $df);
    return $date->format("Y-m-d");
}

/**
 * Excepts for %a and %y all other parameters counts differences in relation to their parent
 * @param string $date_1 : date or datetime
 * @param string $date_2 : date or datetime
 * @param $dateForm : string
 * %a = total number of days 
 * %y = total number of Year 
 * %m = Months in relation to the Year
 * %d = Day in relation to month
 * %h = Hours in relation to Day
 * %i = Minute in relation to Hour
 * %s = Seconds in relation to Minute
 * %r = returns the sign for negative (-) or empty for positive
 * @return mixed : positive or negative number
 */
function return_Date_Difference($date_1, $date_2, $dateForm = '%r%a')
{
    $dt1 = new DateTime($date_1);
    $dt2 = new DateTime($date_2);
    $interval = $dt1->diff($dt2);
    return $interval->format($dateForm);
}

/**
 * The procedural version of the function return_Date_Difference(),
 * to count difference in days only
 * @param string $date_1 : date or datetime
 * @param string $date_2 : date or datetime
 * @return mixed : the difference of days as a positive or negative number
 */
function return_Date_Difference_days($date_1, $date_2)
{
    $dt1 = date_create($date_1);
    $dt2 = date_create($date_2);
    $diff = date_diff($dt1, $dt2);
    $interval = $diff->days;
    if ($diff->invert) {
        return -1 * $interval;
    } else {
        return $interval;
    }
}

/**
 * Counts total diffrences between 2 DateTime strings
 * @param $date_1 : date string
 * @param $date_2 : date string
 * @param $dateForm : string with one of following values:
 *  - years : Total years,
 *  - months : Total months,
 *  - days : Total days (default),
 *  - hours : Total hours,
 *  - minutes : Tootal minuts,
 *  - seconds : Total seconds,
 * @return mixed : the difference as a positive or negative number
 */
function return_Date_Time_Total_Difference($date_1, $date_2, $dateForm = 'days')
{
    $dt1 = new DateTime($date_1);
    $dt2 = new DateTime($date_2);
    $interval = $dt1->diff($dt2);

    //$return_days = $interval->format(%a);
    $return_value = $interval->days;
    if ($dateForm !== 'days') {
        if ($dateForm == 'years' || $dateForm == 'months') {
            $return_value = $interval->y;
            if ($dateForm == 'months') {
                $return_value = ($return_value * 12) + $interval->m;
            }
        } else {
            if ($dateForm == 'hours') {
                $return_value = ($return_value * 24) + $interval->h;
            } elseif ($dateForm == 'minutes') {
                $return_value = ($return_value * 24 * 60) + ($interval->h * 60) + $interval->i;
            } elseif ($dateForm == 'seconds') {
                $return_value = ($return_value * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            }
        }
    }
    return $return_value;
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the Year part of a date
 */
function return_Year($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("Y");
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the Month of a date, 1 through 12
 */
function return_Month($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("n");
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the Month of a date with leading zero, 01 through 12
 */
function return_Month_01($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("m");
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the Month day of a date, 1 to 31
 */
function return_Month_Day($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("j");
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the Month day of a date with leading zero, 01 to 31
 */
function return_Month_Day_01($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("d");
}

/**
 * @param string $fdate : date or datetime
 * @return mixed : the week number: Mon = 1 Sun = 7
 */
function return_Week_Day_1_7($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("N");
}

/**
 * @param string $fdate : date or datetime 
 * @return : the week number: Sunday = 0 Saturday = 6
 */
function return_Week_Day_0_6($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("w");
}

/**
 * @param string $fdate : date or datetime 
 * @return : Sunday through Saturday
 */
function return_Week_Day_Name($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("l");
}

/**
 * @param string $fdate : date or datetime 
 * @return : the week in the year, e.g. 42, week starts on monday
 */
function return_Week_In_Year($fdate)
{
    $d = new DateTime($fdate);
    return $d->format("W");
}

/**
 * @param int $week : week in the year
 * @param int $year : the year
 * @return : an array with the start and end date of any week in a year (ISO Date)
 */
function return_Week_Start_End_Dates($week, $year)
{
    $dt = new DateTime();
    $aRet[0] = $dt->setISODate($year, $week)->format('Y-m-d');
    $aRet[1] = $dt->modify('+6 days')->format('Y-m-d');
    return $aRet;
}

/**
 * @param string $time : a time string (H:i:s)
 * @return string : the Hours:Minus part of a Time string
 */
function return_Time_Minutes($time)
{
    if (!empty($time)) {
        $time = trim($time);
    }
    if (!empty($time) && strlen($time) >= 8) {
        return substr($time, 0, 5);
    } else {
        return $time;
    }
}

/**
 * @param string $dAnyDate : Date or DateTime as string
 * @return mixed : Month's first week day Mon = 1 to Sun = 7
 */
function return_Months_First_Week_Day($dAnyDate)
{
    $dt = new DateTime($dAnyDate);
    $dt = new DateTime($dt->format('Y') . "-" . $dt->format('m') . "-01");
    return $dt->format("N");
}

/**
 * @param string $dAnyDate : Date or DateTime as string
 * @return mixed : Month's total days 28-31
 */
function return_Total_Days_In_Month($dAnyDate)
{
    $dt = new DateTime($dAnyDate);
    return $dt->format("t");
}


/**
 * ======================================
 * VARIOUS STRING Functions
 * ======================================
 */

/**
 * @param string $strKey : the key name of a query string
 * @param string $strQuery : the enire query string
 * @return string : the left part of query string until the key parameter.
 *   Removes the right part of a query string from the key parameter.
 *   Basically, cleans messages if current location will be saved in session
 */
function remove_Right_Query_From_Key($strKey, $strQuery)
{
    $_retval = $strQuery;
    if (!empty($strKey) && !empty($strQuery)) {
        if (strpos($strQuery, $strKey, 0) >= 0) {
            $pos = strpos($strQuery, $strKey, 0);
            if (intval($pos) > 0) {
                $_retval = substr($strQuery, 0, $pos - 1);
            }
        } else {
            $_retval = $strQuery;
        }
    }
    return $_retval;
}

/**
 * @param string $txt : the string to be shorten
 * @param int $x : the number of characters to be returned + to dot (.)
 * @return string : returns the requested number of characters from a string
 */
function return_Left_Part_FromText($txt, $x = 200)
{
    if (!empty($txt)) {
        $txt = sx_Remove_Quotes(strip_tags($txt));
    }
    if (!empty($txt) && strlen($txt) > $x) {
        /*
        $intPos = strpos($txt, ".", $x);
        if ($intPos === false) {
            $intPos = strpos($txt, " ", $x);
        }
        */
        $intPos = strpos($txt, " ", $x);
        if ($intPos === false) {
            $intPos = $x;
        }
        if ($intPos > 0) {
            $txt = trim(substr($txt, 0, $intPos));
        }
    }
    return $txt;
}

/**
 * @param string $tbl : the Table name
 * @param string $fieldName : the Field Name to be SELECTED to return its value
 * @param string $pkName : the Field Name of the Primary Key
 * @param int $pkID : the ID value of the Primary Key
 * @return mixed : the value of the selected field
 */
function return_Field_Value_From_Table($tbl, $fieldName, $pkName, $pkID)
{
    $pursue = true;
    $_retval = "";
    if (empty($tbl) || empty($fieldName) || empty($pkName)) {
        $pursue = false;
    }

    if (!sx_checkTableAndFieldNames($tbl) || !sx_checkTableAndFieldNames($fieldName) || !sx_checkTableAndFieldNames($pkName)) {
        $pursue = false;
    }
    if (intval($pkID) == 0) {
        $pursue = false;
    } else {
        $pkID = (int)($pkID);
    }
    if ($pursue) {
        $conn = dbconn();
        $sxSQL = "SELECT " . $fieldName . " AS FieldName FROM " . $tbl . " WHERE " . $pkName . " = " . $pkID;
        $sxrs = $conn->query($sxSQL);
        $_retval = $sxrs->fetch(PDO::FETCH_COLUMN);
        $sxrs = null;
    }
    return $_retval;
}



/**
 * @param string $str : the address to be scripted
 * @param string $name : the receiver name
 * @return mixed : the address in javascript
 */
function get_Email_In_Script($str, $name)
{
    if (!empty($str) && strpos($str, "@", 0) > 0 && strrpos($str, ".", 0) > 5) {
        $arrTD = explode("@", $str);
        $strL = $arrTD[0];
        $strR = $arrTD[1];
        if (strpos($strR, ".", 0) > 0) {
            $arrTDR = explode(".", $strR);
            $strRL = $arrTDR[0];
            $strRR = $arrTDR[1];
            $strRRR = "";
            if (count($arrTDR) == 3) {
                $strRRR = '.' . $arrTDR[2];
            }
        }
        $strNameL = strrev($strL);
        $strNameRL = strrev($strRL);
        $strNameRR = strrev($strRR);
        $strNameRRR = "";
        if (!empty($strRRR)) {
            $strNameRRR = strrev($strRRR);
            $strNameRRR = "<spa' + 'n c' + 'lass=' + 'str_dir' + '>' + tdCNV + '" . $strNameRRR . "' + tdANV + '<' + '/sp' + 'an>";
        }

        echo "<script>";
        echo "
  var tdN = '$name';
  var tdNL = '$strNameL';
  var tdNRL = '$strNameRL';
  var tdNRR = '$strNameRR';
  
  var tdL = '$strL';
  var tdRL = '$strRL';
  var tdRR = '$strRR';
  var tdRRR = '$strRRR';
  var tdANV = '';
  var tdBNV = '';
  var tdCNV = ''\n";
        if (!empty($name)) {
            echo "document.write('<'+''+ tdANV + ''+''+'a '+''+ tdBNV + ''+'hr' + 'ef'+''+ tdCNV + ''+'='+'ma'+'il' + 'to:' + tdL + tdBNV +'&'+ tdCNV +'#'+ tdANV +'6'+ tdBNV +'4;' + tdRL + tdANV + '.' + tdBNV + tdRR + tdBNV + tdRRR + '>' + tdN + '<'+'/'+'a>' + '')";
        } else {
            echo "document.write('<'+''+ tdANV + ''+''+'a '+''+ tdBNV + ''+'hr' + 'ef'+''+ tdCNV + ''+'='+'ma'+'il' + 'to:' + tdL + tdBNV +'&'+ tdCNV +'#'+ tdANV +'6'+ tdBNV +'4;' + tdRL + tdANV + '.' + tdBNV + tdRR + tdBNV + tdRRR + '>" . $strNameRRR . "<spa'+'n c'+'lass='+'str_dir' + '>' + tdCNV + tdNRR + tdANV + '<'+'/sp'+'an>' + '.' + '<spa'+'n c'+'lass='+'str_dir' + '>' + tdNRL + '<'+'/sp'+'an>' + '&'+ tdANV +'#'+ tdBNV +'6'+ tdCNV +'4;' + '<spa'+'n c'+'lass='+'str_dir' + '>' + tdNL + '<'+'/sp'+'an><'+'/'+'a>' + '')";
        }
        echo "</script>";
    } else {
        echo " ";
    }
}

/**
 * @param mixed $haystack : the total string
 * @param mixed $needle : the start part of the string to be checked
 * @return bool : false or true
 */
function check_Starts_With($haystack, $needle)
{
    if (!empty($haystack) && !empty($needle)) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    } else {
        return false;
    }
}

/**
 * @param mixed $haystack : the total string
 * @param mixed $needle : the end part of the string to be checked
 * @return bool : false or true
 */
function check_Ends_With($haystack, $needle)
{
    if (empty($haystack) || empty($needle)) {
        return false;
    } else {
        $length = strlen($needle);
    }
    return (substr(trim($haystack), -$length) === $needle);
}


/**
 * @return bool : checks if the site is open in mobile devise
 */
function check_Mobile_Device()
{
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


function sx_check_image_suffix($str)
{
    if (!empty($str)) {
        if (
            str_contains($str, ".jpg")  ||
            str_contains($str, ".jpeg") ||
            str_contains($str, ".png") ||
            str_contains($str, ".webp") ||
            str_contains($str, ".gif") ||
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

function sx_separateWordsWithCamelCase($header)
{
    $header = trim(implode(' ', preg_split('/(?=[A-Z])/', $header)));
    if (str_contains($header, 'I D')) {
        $header = str_replace('I D', 'ID', $header);
    }
    if (str_contains($header, 'U R L')) {
        $header = str_replace('U R L', 'URL', $header);
    }
    return $header;
}

function sx_getFileType(string $filename)
{
    return strtolower(substr($filename, strrpos($filename, '.') + 1));
}


/**
 * Used for intentionally formed multimedia file name. Takes:
 * - the left part of (.)
 * - the right part of last (/)
 * - removes the left part of (__)
 * - replaces (_) by space
 * Transforms a file name in URL to Title used to link to that file
 * @param string $file : the File Name
 * @return string : the File Name as Link Name or Title
 */
function get_Link_Title_From_File_Name($file)
{
    $file = substr($file, 0, strrpos($file, '.'));
    if (str_contains($file, '/')) {
        $file = substr($file, strrpos($file, '/') + 1);
    }
    if (str_contains($file, '__')) {
        $file = substr($file, strrpos($file, '__') + 2);
    }
    $file = str_replace("_", " ", str_replace("-", " ", $file));
    return $file;
}

/**
 * Transforms any file name or string to Title
 *  - Rremoves the right part of (.) and Repaces (_) by space
 * @param mixed $str
 * @return mixed
 */
function sx_get_title_from_string($str)
{
    if (!empty($str)) {
        if (str_contains($str, '/')) {
            $str = explode('/', $str)[1];
        }
        if (str_contains($str, '.')) {
            $str = explode('.', $str)[0];
        }
        if (str_contains($str, '__')) {
            $str = explode('__', $str)[1];
        }
        if (str_contains($str, '_')) {
            $str = str_replace('_', ' ', $str);
        }
        $str = ucwords($str);
    }
    return $str;
}
