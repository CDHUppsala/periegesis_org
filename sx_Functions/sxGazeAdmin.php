<?php
$sxGazeURL = strtolower($_SERVER["PATH_INFO"]);
if (strpos($sxGazeURL, "/dbadmin/", 0) > 0) {
    $sxGazeURL = substr($sxGazeURL, 0, strpos($sxGazeURL, "/dbadmin/", 0));
    $sxGazeURL.="dbadmin/login/accessDenied.php";
}else{
    $sxGazeURL = strtolower($_SERVER["DOCUMENT_ROOT"]);
}

$sxGazeQueryString=$_SERVER["QUERY_STRING"];

// SQL-Injenction AND Cross-Site scripting - Filter query string names

$strRequestCharFilter="',\",--,\,NULL,(,),<,%3C,%3c";
$arrRCF = explode(",", $strRequestCharFilter);
if ($sxGazeQueryString != "") {
    if (strpos(strtolower($sxGazeQueryString), "seccessfulbooking", 0) > 0) {
        $sxGazeQueryString = str_replace("\\", "", $sxGazeQueryString);
    }
    if (strpos(strtolower($sxGazeQueryString), "orderby=", 0) > 0 && (
        strpos(strtolower($sxGazeQueryString), "sum(", 0) > 0 ||
        strpos(strtolower($sxGazeQueryString), "avg(", 0) > 0 ||
        strpos(strtolower($sxGazeQueryString), "count(", 0) > 0)
        ) {
        $sxGazeQueryString=str_replace("(", "", $sxGazeQueryString);
        $sxGazeQueryString=str_replace(")", "", $sxGazeQueryString);
    }
    for ($cf=0; $cf<=count($arrRCF)-1; $cf++) {
        if (strpos($sxGazeQueryString, $arrRCF[$cf], 0) > 0) {
            header("Location: ".$sxGazeURL);
        }
    }
}

// SQL-Injenction AND Cross-Site scripting - Sanitize query form names

$strFormCharFilter="<script,<object,%3cscript,%3cobject,https://,http://,www.";
$arrFCF=explode(",", $strFormCharFilter);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $value=strtolower($value);
        if (strtolower($key) == "hotelmapurl") {
              $value=str_replace("http://", "", $value);
              $value=str_replace("www.", "", $value);
        }
        for ($cf=0; $cf<=count($arrFCF)-1; $cf++) {
            if (strpos($value, $arrFCF[$cf], 0) > 0) {
                header("Location: ".$sxGazeURL);
            }
        }
    }
}
