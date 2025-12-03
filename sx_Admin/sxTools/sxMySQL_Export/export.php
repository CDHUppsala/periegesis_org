<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include "functions.php";

/**
 * Path to remote server folder that includes files to be imported
 * The default folder is import_export_files.
 * Change it by a COSTANT, if neccessar, for specific sites 
 */
if (defined('SX_PrivateInportExportFilesFolder') && !empty(SX_PrivateInportExportFilesFolder)) {
    define("PATH_ToExportFolder", PROJECT_PRIVATE . "/" . SX_PrivateInportExportFilesFolder . "/");
} else {
    define("PATH_ToExportFolder", PROJECT_PRIVATE . "/import_export_files/");
}

$strDBTable = "";
if (!empty($_POST["DBTable"])) {
    $strDBTable = $_POST["DBTable"];
}
// From selected DB Table, as hidden input
if (!empty($_POST["HiddenDBTable"])) {
    $strDBTable = $_POST["HiddenDBTable"];
}

$strTableFileds = "";
if (isset($_POST["SelectAllFields"])) {
    $strTableFileds = "*";
} elseif (isset($_POST["TableFields"])) {
    $strTableFileds = implode(", ", $_POST["TableFields"]);
}
/**
 * Check if required variables are present
 */
if (empty($strDBTable) || empty($strTableFileds)) {
    echo "<h3>Please, select a Table or Table Fields</h3>";
    exit();
}

$arrDataTypes = array();
if (isset($_POST["DataTypes"]) && !empty($_POST["DataTypes"])) {
    $arrDataTypes = explode(",", $_POST["DataTypes"]);
}

$radioDownload = false;
if(isset($_POST['SaveType']) && $_POST['SaveType'] == 'download') {
    $radioDownload = true;
}

$strWhere = "";
if (isset($_POST["Where"]) && !empty($_POST["Where"])) {
    $strWhere = " WHERE " . $_POST["Where"];
}
$strOrderBy = "";
if (isset($_POST["OrderBy"]) && !empty($_POST["OrderBy"])) {
    $strOrderBy = " ORDER BY " . $_POST["OrderBy"];
}
$strLimit = "";
if (isset($_POST["Limit"]) && is_numeric($_POST["Limit"])) {
    $strLimit = " LIMIT " . $_POST["Limit"];
}

if ($_POST["ExportType"] != "xsd") {
    $sql = "SELECT " . $strTableFileds . " FROM " . $strDBTable . $strWhere . $strOrderBy . $strLimit;
    $rsQuery = $conn->prepare($sql);
    $rsQuery->execute();
}

if ($_POST["ExportType"] == "xml") {
    include "include_xml.php";
} elseif ($_POST["ExportType"] == "xsd") {
    include "include_xsd.php";
} elseif ($_POST["ExportType"] == "json") {
    include "include_json.php";
} elseif ($_POST["ExportType"] == "csv") {
    include "include_csv.php";
}
$rsQuery = null;
