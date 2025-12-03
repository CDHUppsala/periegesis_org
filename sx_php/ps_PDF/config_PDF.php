<?php
function sx_LoadRequestedFile($strFile)
{
    $FileToDownload = PROJECT_PHP . "/" . sx_PrivateArchivesPath . $strFile;
    if (file_exists($FileToDownload)) {
        $fileSize = filesize($FileToDownload);
        header("Cache-Control: private");
        header("Content-Type: application/stream");
        header("Content-Length: " . $fileSize);
        header("Content-Disposition: attachment; filename=" . $strFile);
        readfile($FileToDownload);
    }
}

CONST SX__radioHideInsertDate = false;

/**
 * Check if User is logged in from sxGaze.php
 */
$radio__UserSessionIsActive = sx_check__UserSessionIsActive();

/**
 * CHECK FOR LOGIN REQUIREMENTS
 * The constant variable is used in menu functions
 */

$strLoginAnd = "";
if ($radio__UserSessionIsActive == false) {
    $strLoginAnd = " AND g.LoginToRead = False ";
}
define("STR__LoginAnd", $strLoginAnd);

$iGroupID = 0;
$iCategoryID = 0;
$intArchID = 0;
if (isset($_GET["archID"])) {
    $intArchID = $_GET["archID"];
}
if (return_Filter_Integer($intArchID) == 0) {
    $intArchID = 0;
}

/**
 * MULTILINQUAL
 */

$iLanguageID = 0;
if (sx_includeMultilinqual) {
    $sql = "SELECT LanguageID 
		FROM languages  
		WHERE LanguageCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([sx_CurrentLanguage]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (($rs)) {
        $iLanguageID = $rs["LanguageID"];
    }
    $stmt = null;
    $rs = null;
}

$strLangNr = "";
if (intval($iLanguageID) > 1) {
    $strLangNr = "_$iLanguageID";
}
define("STR__LangNr", $strLangNr);

$strLanguageWhere = "";
$strLanguageAnd = "";
if (intval($iLanguageID) > 0) {
    $strLanguageWhere = " WHERE (LanguageID = $iLanguageID OR LanguageID = 0)";
    $strLanguageAnd = " AND (LanguageID = $iLanguageID OR LanguageID = 0)";
}

define("STR__LanguageAnd", $strLanguageAnd);

$sql = "SELECT SiteTitle, MetaTitle, LogoImageSmall, MetaDescription 
	FROM site_setup 
	WHERE SubOffice = False " . STR__LanguageAnd . "
	ORDER BY SiteID ASC ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $strSiteTitle = $rs["SiteTitle"];
    $strMetaTitle = $rs["MetaTitle"];
    $str_LogoImageSmall = $rs["LogoImageSmall"];
    $strMetaDescription = $rs["MetaDescription"];
}
$stmt = null;
$rs = null;

$radioUsePDF = false;
$sql = "SELECT UsePDF, PDFMenuForm, LoginToView, PDFMenuTitle, PDFTitle, LogoReturn, PDFImage, PDFNotes 
	FROM pdf_setup $strLanguageWhere";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($rs)) {
    $radioUsePDF = $rs["UsePDF"];
    $strPDFMenuForm = $rs["PDFMenuForm"];
    $radioLoginToView = $rs["LoginToView"];
    $strMenuTitle = $rs["PDFMenuTitle"];
    $strPDFTitle = $rs["PDFTitle"];
    $strLogoReturn = $rs["LogoReturn"];
    $strPDFImage = $rs["PDFImage"];
    $memoNote = $rs["PDFNotes"];
}
$stmt = null;
$rs = null;

if (empty($strPDFTitle)) {
    $strPDFTitle = $strMenuTitle;
}

if ($radioUsePDF == false) {
    header('Location: index.php');
    exit();
}
if ($radioLoginToView && $radio__UserSessionIsActive == false) {
    header("Location: login.php");
    exit();
}

$strPageTitle = $strMetaTitle;
if (!empty($strPDFTitle)) {
    $strPageTitle = $strPDFTitle . " - " . $strPageTitle;
}

$strPDFArchiveName = "";
$iYear = 0;

if (intval($intArchID) > 0) {
    $sql = "SELECT a.GroupID, 
		a.CategoryID, 
		a.ArchiveName, 
		a.HiddenFilesName, 
		a.ArchiveURL, 
		a.PublicationYear, 
		a.InsertDate, 
		a.MetaNote, 
		g.LoginToRead 
	FROM pdf_groups AS g INNER JOIN pdf_archives AS a ON g.GroupID = a.GroupID 
	WHERE a.ArchiveID = ?
		AND a.Hidden = False 
		AND g.Publish = True " . $strLoginAnd . STR__LanguageAnd;
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute([$intArchID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $iGroupID = $rs["GroupID"];
        $iCategoryID = $rs["CategoryID"];
        $strArchiveName = $rs["ArchiveName"];
        $strHiddenFilesName = $rs["HiddenFilesName"];
        $strArchiveURL = $rs["ArchiveURL"];
        $intPublicationYear = $rs["PublicationYear"];
        $dateInsertDate = $rs["InsertDate"];
        $strMetaNote = $rs["MetaNote"];
        $radioLoginToRead = $rs["LoginToRead"];
    } else {
        $intArchID = 0;
    }
    $stmt = null;
    $rs = null;

    if (return_Filter_Integer($iGroupID) == 0) {
        $iGroupID = 0;
    }
    if (return_Filter_Integer($iCategoryID) == 0) {
        $iCategoryID = 0;
    }
    if (intval($intPublicationYear) > 0) {
        $iYear = $intPublicationYear;
    }

    if (!empty($strArchiveName)) {
        $strPageTitle = $strArchiveName . " - " . $strPageTitle;
    }
    if ($strMetaNote != "") {
        $strMetaDescription = $strMetaNote;
    }
}