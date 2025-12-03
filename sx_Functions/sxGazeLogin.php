<?php

$sxGazeQueryString = $_SERVER["QUERY_STRING"];
$strCharFilter = "',\",--,/,\,;,NULL,(,),<,%3C,%3c";
$arrCF = explode(",", $strCharFilter);

// SQL-Injenction AND Cross-Site scripting - Filter names in query string

if ($sxGazeQueryString() != "") {
    for ($cf = 0; $cf <= count($arrCF) - 1; $cf++) {
        if (_instr(0, $sxGazeQueryString(), $arrCF[$cf], 0) > 0) {
            header("Location: accessDenied.php");
        }
    }
}

// SQL-Injenction AND Cross-Site scripting - Check form names

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        for ($cf = 0; $cf < count($arrCF) - 1; $cf++) {
            if (strpos($value, $arrCF[$cf], 0) > 0 || strpos($key, $arrCF[$cf], 0) > 0) {
                header("Location: accessDenied.php");
            }
        }
    }
}
