<?php

define("ARR__ImageTypes", array("gif", "png", "jpg", "jepg", "svg"));
define("STR__GalleryFolder", "imgGallery");
$strSubFolderName = "";


/**
 * Check if User is logged in from sxGaze.php
 */
$radio__UserSessionIsActive = sx_check__UserSessionIsActive();

/**
 * CHECK FOR LOGIN REQUIREMENTS
 */

$sLoginAnd = "";
if ($radio__UserSessionIsActive == false) {
	$sLoginAnd = " AND (fgg.LoginToView = False OR fgg.LoginToView IS NULL)";
}
define("STR__LoginAnd", $sLoginAnd);

/**
 * Language and site information
 */
$iLanguageID = 0;
if (sx_includeMultilinqual) {
	$sql = "SELECT LanguageID FROM languages WHERE LanguageCode = ? ";
	$rs = $conn->prepare($sql);
	$rs->execute([sx_CurrentLanguage]);
	$obj = $rs->fetchObject();
	if ($obj) {
		$iLanguageID = $obj->LanguageID;
	}
	$rs = null;
	$obj = null;
}

$strLangNr = "";
if (intval($iLanguageID) > 1) {
	$strLangNr = "_" . $iLanguageID;
}
define("STR__LangNr", $strLangNr);

$strLanguageWhere = "";
$strLanguageAnd = "";
if (intval($iLanguageID) > 0) {
	$strLanguageWhere = " WHERE (LanguageID = {$iLanguageID} OR LanguageID = 0)";
	$strLanguageAnd = " AND (LanguageID = {$iLanguageID} OR LanguageID = 0)";
}

$sql = "SELECT SiteTitle, LogoImageSmall, MetaDescription 
	FROM site_setup 
	WHERE SubOffice = False " . $strLanguageAnd . "
	ORDER BY SiteID ASC ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$strSiteTitle = $rs["SiteTitle"];
	$str_LogoImageSmall = $rs["LogoImageSmall"];
	$strMetaDescription = $rs["MetaDescription"];
}
$rs = null;
$stmt = null;

/**
 * Gallery setup information
 */
$radioUseGallery = False;
$sql = "SELECT * 
	FROM folder_gallery_setup " . $strLanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioUseGallery = $rs["UseFolderGallery"];
	$radioLoginToView = $rs["LoginToView"];
	$strFolderGalleryMenuTitle = $rs["FolderGalleryMenuTitle"];
	$strGallerySetupTitle = $rs["SetupTitle"];
	$strLogoReturn = $rs["LogoReturn"];
	$strGallerySetupImage = $rs["SetupImg"];
	$memoGallerySetupNote = $rs["SetupNotes"];
}
$rs = null;
$stmt = null;

if (!$radioUseGallery) {
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

/**
 * Get request variables
 */

$int0 = 0;
if (isset($_GET["int0"])) {
    $int0 = (int) $_GET["int0"];
    if (intval($int0) == 0) {
        $int0 = 0;
    }
}

$int1 = 0;
if (isset($_GET["int1"])) {
    $int1 = (int) $_GET["int1"];
    if (intval($int1) == 0) {
        $int1 = 0;
    }
}

if (intval($int1) > 0) {
    $sql = "SELECT fg.GroupID, 
            fg.GalleryName{$strLangNr} AS GalleryName, 
            fg.GalleryNote{$strLangNr} AS GalleryNote, 
            fgg.GroupName{$strLangNr} AS GroupName, 
            fgg.GroupNote{$strLangNr} AS GroupNote, 
            fg.SubFolderName 
        FROM folder_galleries AS fg
            LEFT JOIN folder_gallery_groups AS fgg 
            ON fg.GroupID = fgg.GroupID 
        WHERE fg.GalleryID = ? 
            AND fg.Hidden = False 
            AND (fgg.Hidden = False OR fgg.Hidden IS NULL) " . STR__LoginAnd;

    $stmt = $conn->prepare($sql);
    $stmt->execute([$int1]);

    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int0 = $rs["GroupID"];
        $strGalleryName = $rs["GalleryName"];
        $memoGalleryNote = $rs["GalleryNote"];
        $strGroupName = $rs["GroupName"];
        $memoGroupNote = $rs["GroupNote"];
        $strSubFolderName = $rs["SubFolderName"];
    } else {
        $int0 = 0;
        //$int1 = 0;
    }
    $rs = null;
    $stmt = null;
} elseif (intval($int0) > 0) {
    $sql = "SELECT GroupImg, 
            GroupName{$strLangNr} AS GroupName, 
            GroupNote{$strLangNr} AS GroupNote 
        FROM folder_gallery_groups  
        WHERE GroupID = ? AND Hidden = False " . STR__LoginAnd;
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int0]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $strGalleryGroupImg = $rs["GroupImg"];
        $strGroupName = $rs["GroupName"];
        $memoGroupNote = $rs["GroupNote"];
    } else {
        $int0 = 0;
        $int1 = 0;
    }
    $rs = null;
    $stmt = null;
}

/**
 * Meta information
 */
if (empty($strSiteTitle)) {
	$strSiteTitle = lngHomePage;
}

$strPageTitle = $strSiteTitle;
if (!empty($strGroupName)) {
	$strPageTitle = $strGroupName . " | " . $strPageTitle;
}
if (!empty($strGalleryName)) {
	$strPageTitle = $strGalleryName . " | " . $strPageTitle;
}

$strPageMetaDescription = $strMetaDescription;
if (isset($memoGalleryNote) && !empty($memoGalleryNote)) {
	$strPageMetaDescription = $memoGalleryNote;
} elseif (isset($memoGroupNote) && !empty($memoGroupNote) > 0) {
	$strPageMetaDescription = $memoGroupNote;
}
