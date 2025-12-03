<?php

if ($radio_UseFolderGallery == false) {
    header('Location: index.php');
    exit();
}

/**
 * Define allowed image types and the main gallery folder
 */
define("arr_ImageTypes", array("gif", "png", "jpg", "jpeg", "svg"));
define("str_GalleryFolder", "imgGallery");
$strSubFolderName = "";

/**
 * CHECK FOR LOGIN REQUIREMENTS
 */

$sLoginAnd = "";
if ($radio__UserSessionIsActive == false) {
    $sLoginAnd = " AND (fgg.LoginToView = False OR fgg.LoginToView IS NULL)";
}
define("sLoginAnd", $sLoginAnd);

/**
 * Gallery setup information
 */
$radioUseFolderGallery = False;
$sql = "SELECT * FROM folder_gallery_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radioUseFolderGallery = $rs["UseFolderGallery"];
    $radioLoginToView = $rs["LoginToView"];
    $strFolderGalleryMenuTitle = $rs["FolderGalleryMenuTitle"];
    $strGallerySetupTitle = $rs["SetupTitle"];
    $strLogoReturn = $rs["LogoReturn"];
    $strGallerySetupImage = $rs["SetupImg"];
    $memoGallerySetupNote = $rs["SetupNotes"];
}
$rs = null;
$stmt = null;

if (!$radioUseFolderGallery) {
    header('Location: index.php');
    exit();
}

if ($radioLoginToView && $radio__UserSessionIsActive == false) {
    header("Location: login.php");
    exit();
}

/**
 * Meta information
 */
$str_SiteTitle = $strGallerySetupTitle;
if (!empty($memoGallerySetupNote)) {
    $str_MetaDescription = return_Left_Part_FromText($memoGallerySetupNote, 120);
}

/**
 * Get request variables, if any
 */
$i_FolderGroupID = 0;
$i_FolderGalleryID = 0;
if (isset($_GET["fgid"])) {
    $i_FolderGalleryID = (int) $_GET["fgid"];
}

if (intval($i_FolderGalleryID) > 0) {
    $sql = "SELECT fg.GroupID, 
		fg.GalleryName" . str_LangNr . " AS GalleryName, 
		fg.GalleryNote" . str_LangNr . " AS GalleryNote, 
		fgg.GroupName" . str_LangNr . " AS GroupName, 
		fgg.GroupNote" . str_LangNr . " AS GroupNote, 
		fg.SubFolderName 
    FROM folder_galleries AS fg
        LEFT JOIN folder_gallery_groups AS fgg 
    	ON fg.GroupID = fgg.GroupID 
    WHERE fg.GalleryID = ? 
        AND fg.Hidden = False 
        AND (fgg.Hidden = False OR fgg.Hidden IS NULL) " . sLoginAnd;

    $stmt = $conn->prepare($sql);
    $stmt->execute([$i_FolderGalleryID]);

    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $i_FolderGroupID = $rs["GroupID"];
        $strGalleryName = $rs["GalleryName"];
        $memoGalleryNote = $rs["GalleryNote"];
        $strGroupName = $rs["GroupName"];
        $memoGroupNote = $rs["GroupNote"];
        $strSubFolderName = $rs["SubFolderName"];
    } else {
        $i_FolderGalleryID = 0;
    }
    $rs = null;
    $stmt = null;
}

if (intval($i_FolderGalleryID) > 0) {
    $str_SiteTitle = $strGalleryName;
    if (!empty($memoGalleryNote)) {
        $str_MetaDescription = return_Left_Part_FromText($memoGalleryNote, 120);
    }
}
$str_MetaTitle = $str_SiteTitle;
