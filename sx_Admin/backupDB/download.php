<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/config.php";

if (isset($_GET['file'])) {
    $zipFile = $_GET['file'];
    $zip_BackupFilePath = "{$str_RemoteBackupDirectory}\\{$zipFile}";

    // Security check to prevent directory traversal
    if (strpos($zip_BackupFilePath, '..') !== false || !file_exists($zip_BackupFilePath)) {
        exit("Invalid file request {$zip_BackupFilePath}.");
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $zipFile);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_BackupFilePath));

    readfile($zip_BackupFilePath);
    unlink($zip_BackupFilePath);
    exit;
}
