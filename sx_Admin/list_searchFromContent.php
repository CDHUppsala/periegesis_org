<?php

/**
 * Search from the Content Page for any Field Value in any Table
 * searchFieldName=PublishInColumn&searchFieldValue=Bottom
 * Dangerous inputs - consider to validate them
 */

if (!empty($_GET["initialFieldName"]) && !empty($_GET["initialFieldValue"])) {
    $tempName = $_GET["initialFieldName"];
    $tempValue = $_GET["initialFieldValue"];
    /**
     * Equal Value is either date, number or alphanumeric string 
     * with Latin characters and no space
     */
    if(sx_checkTableAndFieldNames($tempName) == false) {
        header("Location: main.php?msg=No+way+home");
        exit;
    }

    if(sx_IsDate($tempValue)) {
        $tempValue = "'" . $tempValue ."' ";
    } elseif (intval($tempValue) > 0) {
        $tempValue = intval($tempValue);
    } elseif (sx_checkTableAndFieldNames($tempValue)) {
            $tempValue = "'" . $tempValue ."' ";
    }else{
        header("Location: main.php?msg=No+way+home");
        exit;
    }
    $initialFieldNameWhere = " AND " . $tempName . " = " . $tempValue ;
    $_SESSION["initialFieldNameWhere"] = $initialFieldNameWhere;
} elseif (!empty($_SESSION["initialFieldNameWhere"]) && empty($_GET["RequestTable"])) {
    $initialFieldNameWhere = $_SESSION["initialFieldNameWhere"];
} else {
    $initialFieldNameWhere = "";
    unset($_SESSION["initialFieldNameWhere"]);
}

if (!empty($_GET["greaterFName"]) && !empty($_GET["greaterFValue"])) {
    $tempName = $_GET["greaterFName"];
    if(sx_checkTableAndFieldNames($tempName) == false) {
        header("Location: main.php?msg=No+way+home");
        exit;
    }
    /**
     * Compare Value is either date or number
     */
    $tempValue = $_GET["greaterFValue"];
    if(sx_IsDate($tempValue)) {
        $tempValue = "'" . $tempValue ."' ";
    } elseif (intval($tempValue) > 0) {
        $tempValue = intval($tempValue);
    }else{
        header("Location: main.php?msg=No+way+home");
        exit;
    }

    $greaterFNameWhere = " AND " . $tempName . " >= " . $tempValue ;
    $_SESSION["greaterFNameWhere"] = $greaterFNameWhere;
} elseif (!empty($_SESSION["greaterFNameWhere"]) && empty($_GET["RequestTable"])) {
    $greaterFNameWhere = $_SESSION["greaterFNameWhere"];
} else {
    $greaterFNameWhere = "";
    unset($_SESSION["greaterFNameWhere"]);
}
