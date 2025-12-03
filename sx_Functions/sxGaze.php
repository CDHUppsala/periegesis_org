<?php

$sxGazeQueryString = $_SERVER["QUERY_STRING"];
/*
$strCharFilter = "',\",--,\,;,DROP,NULL,(,),<,>,%3C,%20,%3c,NUL,[,],{,},|,^,%,' '";

echo $sxGazeQueryString .'<br>';
echo '<pre>';
print_r($arrCF);
echo '</pre>';
*/

$strCharFilter = "',\",--,\,;,DROP,NULL,(,),<,>,NUL,[,],{,},|,^";
$arrCF = explode(",", $strCharFilter);

if (!empty($sxGazeQueryString)) {
    for ($cf = 0; $cf < count($arrCF); $cf++) {
        if (strpos($sxGazeQueryString, $arrCF[$cf], 0) !== false) {
            /*
            echo $sxGazeQueryString ."<br>";
            echo $arrCF[$cf];
            */
            header("Location: index.php?sx=g1");
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["SendOrderValidation"]) && $_POST["SendOrderValidation"] != "Yes") {
        foreach ($_POST as $key => $value) {
            if (strtolower($key) == "supplierinfo") {
                $value = str_replace("<br>", "", $value);
            }
            if (strtolower($key) == "message") {
                $value = str_replace("'", "", $value);
                $value = str_replace("\"", "", $value);
            }
            for ($cf = 0; $cf < count($arrCF); $cf++) {
                if (strpos($value, $arrCF[$cf], 0) > 0) {
                    header("Location: index.php?sx=g2");
                }
            }
        }
    }
}

/**
 * Check user login from any application
 * @return bool
 */
function sx_check__UserSessionIsActive()
{
    if (!empty($_SESSION["User_Token"])) {
        if (isset($_SESSION["Users_" . $_SESSION["User_Token"]]) && $_SESSION["Users_" . $_SESSION["User_Token"]]) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
