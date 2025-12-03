<?php
include realpath(dirname(dirname(__DIR__)) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";

include "config.php";
include "functions.php";

/**
 * Enable user error handling
 */
libxml_use_internal_errors(true);

$strXMLFile = new DOMDocument();

$strXSDFile = "";
if (isset($_POST["XMLFile"]) && !empty($_POST["XMLFile"])) {
	$strXMLFile->load($s_FolderPath ."/". $_POST["XMLFile"]);
}elseif (isset($_FILES['XML_File']['name']) && !empty($_FILES['XML_File']['name'])) {
	$strXMLFile->load($_FILES['XML_File']['tmp_name']);
}

if (isset($_POST["XSDFile"]) && !empty($_POST["XSDFile"])) {
	$strXSDFile = $s_FolderPath ."/". $_POST["XSDFile"];
}elseif (isset($_FILES['XSD_File']['name']) && !empty($_FILES['XSD_File']['name'])) {
	$strXSDFile = $_FILES['XSD_File']['tmp_name'];
}

if (!$strXMLFile->schemaValidate($strXSDFile)) {
    echo '<div class="msgError">';
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    libxml_display_errors();
    echo "</div>";
}else{
    echo '<div class="msgSuccess">The XML-File has been successfully validated by the XSD-File.';
}
$strXMLFile = null;
?>