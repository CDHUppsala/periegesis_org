<?php


/**
 * Check if User is logged in from sxGaze.php
 */
$radio__UserSessionIsActive = sx_check__UserSessionIsActive();

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
    if ($rs) {
        $iLanguageID = $rs["LanguageID"];
    }
    $stmt = null;
    $rs = null;
}

$strLangNr = "";
if (intval($iLanguageID) > 1) {
    $strLangNr = "_{$iLanguageID}";
}
define("STR__LangNr", $strLangNr);

$strLanguageWhere = "";
$strLanguageAnd = "";
if (intval($iLanguageID) > 0) {
    $strLanguageWhere = " WHERE (LanguageID =  {$iLanguageID} OR LanguageID = 0)";
    $strLanguageAnd = " AND (LanguageID = {$iLanguageID} OR LanguageID = 0)";
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

$radioUseMedia = false;
$sql = "SELECT *  FROM media_setup " . $strLanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($rs)) {
    $radioUseMedia = $rs["UseMedia"];
    $radioLoginToView = $rs["LoginToView"];
    $radioMediaByCategory = $rs["MediaByCategory"];
    $strByCategoryTitle = $rs["ByCategoryTitle"];
    $radioMediaByYear = $rs["MediaByYear"];
    $strByYearTitle = $rs["ByYearTitle"];
    $strMediaTitle = $rs["MediaTitle"];
    $strMediaMenuTitle = $rs["MediaMenuTitle"];
    $strLogoReturn = $rs["LogoReturn"];
    $strmediasetupImage = $rs["MediaSetupImage"];
    $strMediaNote = $rs["MediaNote"];
}
$stmt = null;
$rs = null;

if ($radioUseMedia == false) {
    header('Location: index.php');
    exit();
}
if ($radioLoginToView && $radio__UserSessionIsActive == false) {
    header("Location: login.php");
    exit();
}

if (empty($strLogoReturn) && !empty($str_LogoImageSmall)) {
    $strLogoReturn = $str_LogoImageSmall;
}

if (empty($strByCategoryTitle)) {
    $strByCategoryTitle = lngByCategory;
}
if (empty($strByYearTitle)) {
    $strByYearTitle = lngByYear;
}

$strPageTitle = $strSiteTitle . " | " . $strMediaMenuTitle . " | " . $strMediaTitle;

/**
 * Get requested Archive, if any
 */

$iArchID = 0;
if (isset($_GET["archID"])) {
    $iArchID = intval($_GET["archID"]);
}
define("INT__ArchID", $iArchID);

$iCategoryID = 0;
$dInsertDate = null;
$iYear = 0;
if (intval(INT__ArchID) > 0) {
    $sql = "SELECT 
		ma.CategoryID, 
		ma.ArchiveName, 
		ma.ArchiveNotes, 
		ma.ArchiveURL, 
		ma.InsertDate 
	FROM media_categories AS mc
		INNER JOIN media_archives AS ma
		ON mc.CategoryID = ma.CategoryID 
	WHERE ArchiveID = ?
		AND mc.Hidden = False 
		AND ma.Hidden = False " . STR__LanguageAnd;
    $stmt = $conn->prepare($sql);
    $stmt->execute([INT__ArchID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $iCategoryID = $rs["CategoryID"];
        $strMediaTitle = $rs["ArchiveName"];
        $strMediaNote = $rs["ArchiveNotes"];
        $sArchiveURL = $rs["ArchiveURL"];
        $dInsertDate = $rs["InsertDate"];
    }
    $stmt = null;
    $rs = null;
}

if (return_Is_Date($dInsertDate)) {
    $tempDate = DateTime::createFromFormat("Y-m-d", $dInsertDate);
    $iYear = $tempDate->format("Y");
}

define("INT__CategoryID", $iCategoryID);
define("INT__Year", $iYear);
