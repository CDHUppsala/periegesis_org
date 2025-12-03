<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";

/**
 * Prepared to be used, if needed
 * Upload files from external sources to the defauls folder 
 * in the remote server to be used to update database tables 
 */

$arrFilePaths = [
];

// Path to the default folder of the remote server
$strImportExportFolder = "/import_export_files";
if (defined('SX_PrivateInportExportFilesFolder') && !empty(SX_PrivateInportExportFilesFolder)) {
    $strImportExportFolder = "/". SX_PrivateInportExportFilesFolder;
}

$uploading_dir = PROJECT_PRIVATE . $strImportExportFolder;

$errLoad = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_array($arrFilePaths)) {
    foreach ($arrFilePaths as $url) {

        $prefix = date('Y-m-d');
        $sFileName = basename($url);
        $arrTemp = explode(".", $url);
		$sFileExtention = end($arrTemp);
        $sFilepath = "$uploading_dir/{$prefix}_{$sFileName}.{$sFileExtention}";
        if (!file_put_contents($sFilepath, file_get_contents($url))) {
            $errLoad[] = "Downloading $url to $sFilepath failed.";
        }
    }
}

if (!empty($errLoad)) {
    echo '<h2>Downloading failed for following files</h2>';
    echo "<p>" . implode('<br>', $errLoad) . "</p>";
} else {
    header("Location: index.php");
    exit;
}
