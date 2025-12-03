<?php
//== Gets the actual/requested Table Name 
$request_Table = "";
if (isset($_GET["Table"])) {
    $request_Table = strtolower($_GET["Table"]);
    if (sx_checkTableAndFieldNames($request_Table) == false) {
        header("Location: main.php?No+way+home");
        exit;
    }
} elseif (isset($_POST["Table"])) {
    $request_Table = $_POST["Table"];
}

if (!empty($request_Table)) {
    $_SESSION["Table"] = $request_Table;

    unset($_SESSION["ArrFieldNames"]);
    unset($_SESSION["DateFieldName"]);
    unset($_SESSION["MinimalDate"]);
    unset($_SESSION["PrimaryKeyName"]);
    unset($_SESSION["SearchTextWhere"]);
    unset($_SESSION["SearchTextDisplay"]);
    unset($_SESSION["SearchDateWhere"]);
    unset($_SESSION["SearchDateDisplay"]);
    unset($_SESSION["SearchFieldNameWhere"]);
    unset($_SESSION["OrderBy"]);
    unset($_SESSION["Order"]);
    unset($_SESSION["OrderByField"]);
    unset($_SESSION["SortPermanent"]);
    unset($_SESSION["initialFieldNameWhere"]);
    unset($_SESSION["Page"]);
    //	unset($_SESSION["GroupByField"]);
    //	unset($_SESSION["inYear"]);
    unset($_SESSION["UpdateableFieldsArray"]);
    unset($_SESSION["SelectedFieldsArray"]);
    unset($_SESSION["UpdateTypeTitle"]);
    unset($_SESSION["ShowImages"]);


    foreach ($_SESSION as $key => $value) {
        if (is_array($value)) {
            unset($_SESSION[$key]);
        } elseif (!empty($value) && (
            strpos($value, "4_") !== false || strpos($value, "40_") !== false || strpos($value, "5_") !== false || strpos($value, "100_") !== false)) {
            unset($_SESSION[$key]);
        }
    }
    $_SESSION["TableConfig"] = False;
} elseif (!empty($_SESSION["Table"])) {
    $request_Table = $_SESSION["Table"];
}
