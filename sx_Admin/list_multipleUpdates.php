<?php

/**
 * Multiple Updates from the List of records
 * Reload the hosting page after update
 */

if (isset($_GET["strMultipleUpdate"])) {
    $strPKName = $_POST["PKName"];
    if (sx_checkTableAndFieldNames($strPKName) == false) {
        header("Location: main.php");
        exit;
    }
    echo $_POST["UsedUpdateableFields"] .'<br>';
    $strUsedUF = $_POST["UsedUpdateableFields"];
    $strUsedUFTypes = $_POST["UsedUpdateableFieldTypes"];
    if (strpos($strUsedUF, ",") == 0) {
        $strUsedUF .= ",";
    }
    $arrUsedUF = explode(",", $strUsedUF);

    if (strpos($strUsedUFTypes, ",") == 0) {
        $strUsedUFTypes .= ",";
    }
    $arrUsedUFTypes = explode(",", $strUsedUFTypes);

    $iCount = count($arrUsedUF);

    //echo $strPKName ." /". $strUsedUF;
    //exit;

    $arrPKValue = $_POST["PKValue"];
    foreach ($arrPKValue as $key => $value) {
        $intPKID = intval($value);
        if ($intPKID == 0) {
            header("Location: main.php");
            exit;
        }
        /*
        LONG,SHORT, TINY, VAR_STRING, DATE, TIME
        */

        $strFieldNameValues = "";

        $arrFieldValues = array();
        for ($i = 0; $i < $iCount; $i++) {
            $strFieldName = trim($arrUsedUF[$i]);
            $strFieldType = trim($arrUsedUFTypes[$i]);
            if (!empty($strFieldName)) {
                $strFieldValue = trim($_POST[$strFieldName][$key]);
                if ($strFieldType == "LONG" || $strFieldType == "SHORT") {
                    $strFieldValue = intval($strFieldValue);
                } elseif ($strFieldType == "TINY") {
                    if ($strFieldValue == "Yes") {
                        $strFieldValue = 1;
                    } else {
                        $strFieldValue = 0;
                    }
                } elseif ($strFieldType == "DATE" || $strFieldType == "DATETIME") {
                    if (DateTime::createFromFormat('Y-m-d', $strFieldValue) === false) {
                        $strFieldValue = null;
                    }
                } elseif (is_numeric($strFieldValue) > 0) {
                    if (strpos($strFieldValue, ",") > 0) {
                        $strFieldValue = str_replace(",", ".", $strFieldValue);
                    }
                }

                if (!empty($strFieldNameValues)) {
                    $strFieldNameValues .= ", ";
                }
                $strFieldNameValues .= $strFieldName . " = ? ";
                $arrFieldValues[] = $strFieldValue;
            }
        }
        if (!empty($strFieldNameValues)) {
            $sql = "UPDATE " . $request_Table . " SET " . $strFieldNameValues . " WHERE " . $strPKName . " = " . $intPKID;
            $conn->prepare($sql)->execute($arrFieldValues);
        }
        /*
        echo $sql . "<br>";
        var_dump($arrFieldValues);
        echo "<br><br>";
        */
    }
    header("Location: list.php");
    exit();
}
