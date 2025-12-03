<?php

/**
 * Clean HTML text transfered by javaScript
 */
function sx_getCleanedJavaText($txt)
{
	$txt = str_replace('"', "”", $txt . "");
	$txt = str_replace("'", "’", $txt . "");
	$txt = str_replace("<P>&nbsp;</P>", "", $txt . "");
	$txt = str_replace("> <", "><", $txt . "");
	$txt = str_replace("\n", " ", $txt . "");
	return $txt;
}


/**
 * Check if User is logged in from sxGaze.php
 */
$radio__UserSessionIsActive = sx_check__UserSessionIsActive();

/**
 *  CHECK FOR LOGIN REQUIREMENTS
 */

$strLogin_And = "";
$strLogin_AliasAnd = "";
$strLogin_AliasAndNull = "";
if ($radio__UserSessionIsActive == false && sx_includeUsersLogin) {
	$strLogin_And = " AND gallery_groups.LoginToView = False ";
	$strLogin_AliasAnd = " AND gg.LoginToView = False ";
	$strLogin_AliasAndNull = " AND (gg.LoginToView = False OR gg.LoginToView IS NULL)";
}

/**
 * MULTILINQUAL
 */

$iLanguageID = 0;
if (sx_includeMultilinqual) {
	$sql = "SELECT LanguageID FROM languages WHERE LanguageCode = ? ";
	$rs = $conn->prepare($sql);
	$rs->execute([sx_CurrentLanguage]);
	if ($rs) {
		$iLanguageID = $rs->fetchColumn();
	}
	$rs = null;
	if (intval($iLanguageID) === 0) {
		$iLanguageID = 0;
	}
}

$strLangNr = "";
if (intval($iLanguageID) > 1) {
	$strLangNr = "_{$iLanguageID}";
}
define("STR__LangNr", $strLangNr);

$strLanguageWhere = "";
$strLanguageAnd = "";
if (intval($iLanguageID) > 0) {
	$strLanguageWhere = " WHERE (LanguageID = {$iLanguageID} OR LanguageID = 0)";
	$strLanguageAnd = " AND (LanguageID = {$iLanguageID} OR LanguageID = 0)";
}

/**
 * Site Information
 */
$sql = "SELECT SiteTitle, LogoImageSmall, MetaDescription 
	FROM site_setup 
	WHERE SubOffice = False {$strLanguageAnd}
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

$radio_UseGallery = False;
$sql = "SELECT * 
	 FROM gallery_setup " . $strLanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radio_UseGallery = $rs["UseGallery"];
	$radioLoginToView = $rs["LoginToView"];
	$strGalleryMenuTitle = $rs["GalleryMenuTitle"];
	$strGalleryTitle = $rs["GallerySetupTitle"];
	$strLogoReturn = $rs["LogoReturn"];
	$strGallerySetupImage = $rs["GallerySetupImg"];
	$memoGallerySetupNote = $rs["GallerySetupNote"];
}
$rs = null;
$stmt = null;


if ($radio_UseGallery == False) {
	header('Location: index.php');
	exit();
}
if ($radioLoginToView && $radio__UserSessionIsActive == false) {
	header(header: "Location: login.php");
	exit();
}

if (empty($strLogoReturn) && !empty($str_LogoImageSmall)) {
	$strLogoReturn = $str_LogoImageSmall;
}

/**
 * Get Requested queries 
 */

$iPhotoID = $_GET["pid"] ?? 0;
if (intval($iPhotoID) == 0) {
	$iPhotoID = 0;
}
$int0 = $_GET["int0"] ?? 0;
$int1 = $_GET["int1"] ?? 0;

if (intval($iPhotoID) > 0) {
	$sql = "SELECT GroupID, GalleryID 
		FROM gallery_photos 
	 	WHERE PhotoID = " . $iPhotoID;
	$stmt = $conn->query($sql);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$int0 = $rs["GroupID"];
		$int1 = $rs["GalleryID"];
	}
	$rs = null;
	$stmt = null;
}
if (intval($int0) == 0) {
	$int0 = 0;
}
if (intval($int1) == 0) {
	$int1 = 0;
}

if (intval($int1) > 0) {
	$sql = "SELECT g.GroupID, 
		g.GalleryName{$strLangNr} AS GalleryName, 
	 	g.GalleryNote{$strLangNr} AS GalleryNote, 
		gg.GroupName{$strLangNr} AS GroupName, 
		gg.GroupNote{$strLangNr} AS GroupNote 
	 FROM galleries AS g LEFT JOIN gallery_groups AS gg 
	 ON g.GroupID = gg.GroupID 
	 WHERE g.GalleryID = " . $int1 . $strLogin_AliasAndNull;
	$stmt = $conn->query($sql);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$int0 = $rs["GroupID"];
		$strGalleryName = $rs["GalleryName"];
		$strGalleryNote = $rs["GalleryNote"];
		$strGroupName = $rs["GroupName"];
		$strGroupNote = $rs["GroupNote"];
	}
	$rs = null;
	$stmt = null;
	if (intval($int0) == 0) {
		$int0 = 0;
	}
} elseif (intval($int0) > 0) {
	$sql = "SELECT
		GroupName{$strLangNr} AS GroupName, 
		GroupNote{$strLangNr} AS GroupNote 
	 FROM gallery_groups 
	 WHERE GroupID = " . $int0 . $strLogin_And;
	$stmt = $conn->query($sql);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$strGroupName = $rs["GroupName"];
		$strGroupNote = $rs["GroupNote"];
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
if (!empty($strGalleryNote)) {
	$strPageMetaDescription = $strGalleryNote;
} elseif (!empty($strGroupNote)) {
	$strPageMetaDescription = $strGroupNote;
}
