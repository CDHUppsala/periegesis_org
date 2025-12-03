<?php

/**
 * ==========================================
 * 1. CHANGE THE VALUES IF NACCESARY
 * ==========================================
 * Media Not supported by HTML5 = ".mpeg;.mpg"
 *  mp3, mp4, ogg, webm
 */

$arr_allowedFileTypes = [
    ".mp3",
    ".mp4",
    ".ogg",
    ".webm",
    ".wav",
    ".gif",
    ".jpg",
    ".jpeg",
    "webp",
    ".png",
    ".svg",
    ".pdf",
    ".txt",
    ".doc",
    ".docx",
    ".odt",
    ".ods",
    ".xml",
    ".xsd",
    ".xlsx",
    ".xls",
    ".odp",
    ".ppt",
    ".pptx",
    ".ppsx",
    ".csv",
    ".zip",
    ".json",
    ".geojson",
    ".topojson",
    ".kml",
    ".shp"
];

$arrUploadableParentFolders = ["images", "imgProducts", "imgGallery", "imgMedia", "imgPDF", "archives"];
$arrDownloadableFolders = ["assets", "archiveImport"];

/**
 * Exlude (program) folders that begin with /sx, like
 * "images/sx", "images/logo/sx", "images/sx_svg", "imgPDF/sx", "imgPDF/sxTempLoadFolder", "imgMedia/sx", "imgGallery/sx"
 */
const STR_ExcludedFolderPrefix = array('/sx', '/_');

// Used directly for creation and deleting of folders
define("ARR_UploadableParentFolders", $arrUploadableParentFolders);
define("ARR_DownloadableFolders", $arrDownloadableFolders);

/**
 * ==========================================
 * 2. DON'T CHANGE ENYTHING BELLOW THIS LINE
 * ==========================================
 * The physical path to the main directory of the Target Website that includes all relevant folders
 */

Define("sx_RootPath", ROOT_PATH . "/");

/**
 * Checks if the Parent (default) uploadable folders do exist in the Server and get their subfolders,
 * Create then an array with all parent folders and their subfolders, removing their physical path
 */
$arrUploadableFolders = sx_getUploadableFolders(sx_RootPath, ARR_UploadableParentFolders);

$arrRemovPhisicalPath = [];
$iCount = count($arrUploadableFolders);
for ($x = 0; $x < $iCount; $x++) {
    $strLoop = str_replace(sx_RootPath, "", $arrUploadableFolders[$x]);
    $arrRemovPhisicalPath[] = $strLoop;
}
define("ARR_UploadableFolders", $arrRemovPhisicalPath);

/*
echo "<pre>";
print_r(ARR_UploadableFolders);
echo "</pre>";
*/

function sx_getUploadableFolders($path, $aFolders)
{
    $arrDir = array();
    if (is_array($aFolders)) {
        $iCount = count($aFolders);
        for ($i = 0; $i < $iCount; $i++) {
            $arrDir = array_merge($arrDir, sx_getSubDirectories($path . $aFolders[$i], STR_ExcludedFolderPrefix));
        }
    }
    return $arrDir;
}

/**
 * Get subdirctories of a directory and add them to an array
 *  Check to exclude filew begining with a constant
 * @param mixed $subPath : the path to directory
 * @param array $arrExcludedPrefixes : The Prefix of folder names that will be excluded
 * @return array
 */
function sx_getSubDirectories($subPath, $arrExcludedPrefixes)
{
    $subDir = array();
    $directories = glob($subPath, GLOB_ONLYDIR);
    foreach ($directories as $directory) {
        if (sx_checkExcludedFolders($directory, $arrExcludedPrefixes)) {
            continue;
        } else {
            $subDir = array_merge($subDir, [$directory]);
        }
        $subDir = array_merge($subDir, sx_getSubDirectories($directory . '/*', $arrExcludedPrefixes));
    }
    return $subDir;
}

function sx_checkExcludedFolders($folder, $prefixes)
{
    foreach ($prefixes as $prefix) {
        if (str_contains($folder, $prefix)) {
            return true;
        }
    }
    return false;
}

function sx_checkEmptyFolder($dir)
{
    $i_Count = count(scandir($dir));
    if ($i_Count <= 2) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get the Files or Directories of a folder
 * @param mixed $dir
 * @param mixed $callback :  Use "is_file": to get Files, "is_dir": to get Directories

 * @return array|bool
 */
function sx_getFolderContents($dir, $callback)
{
    if ($arFiles = scandir($dir)) {
        $ar_Files = [];
        foreach ($arFiles as $file) {
            if ($file !== '.' && $file !== '..' && $callback($dir . "/" . $file)) {
                $loopFile = mb_convert_encoding($file, 'UTF-8', 'auto');
                $ar_Files[] = $loopFile;
            }
        }
        return $ar_Files;
    } else {
        return false;
    }
}
