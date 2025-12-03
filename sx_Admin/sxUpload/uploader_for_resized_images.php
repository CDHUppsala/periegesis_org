<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include "config_upload.php";

/**
 * This file is opened with XMLHttpRequest from the javascript file upload_CanvasFunctions.js
 */
function sx_SaveBase64Image($sImageSources, $sFileNames, $sDestinationFolder)
{
    $arr_images = explode(",", $sImageSources);
    $sImage = base64_decode($arr_images[1]);
    $sImageFile = fopen($sDestinationFolder . "/" . $sFileNames, 'wb');
    fwrite($sImageFile, $sImage);
    fclose($sImageFile);
}

/**
 * Upload to the private folder of the server
 */
$radioUpload = true;
if (isset($_POST["DestinationFolder"])) {
    $strDestinationFolder = $_POST["DestinationFolder"];
} else {
    $radioUpload = false;
}

if (isset($_POST["ImageSources"])) {
    $strImageSources = $_POST["ImageSources"];
} else {
    $radioUpload = false;
}

if (isset($_POST["FileNames"])) {
    $strFileNames = $_POST["FileNames"];
} else {
    $radioUpload = false;
}

// DEVELOP: check destination folder before uploading

/*
if ($radioUpload) {
if (!empty($strDestinationFolder) && in_array($strDestinationFolder, ARR_UploadableFolders)) {
    $strDestinationPhysicalPath = sx_RootPath . $strDestinationFolder;
    sx_SaveBase64Image($strImageSources, $strFileNames, $strDestinationPhysicalPath);
}
}
*/


if ($radioUpload) {
    $strDestinationPhysicalPath = sx_RootPath . $strDestinationFolder;
    sx_SaveBase64Image($strImageSources, $strFileNames, $strDestinationPhysicalPath);
}
