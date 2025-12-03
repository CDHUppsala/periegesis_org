<?php
$str_RemoteBackupDirectory = realpath(PROJECT_PATH . '/private/dbBackup');
define('STR_RemoteBackupDirectory', $str_RemoteBackupDirectory);

function get_folder_files($directory)
{
    $files = [];
    if (is_dir($directory)) {
        foreach (scandir($directory) as $file) {
            if (is_file($directory . DIRECTORY_SEPARATOR . $file)) {
                $files[] = $file;
            }
        }
    }
    return $files;
}

function get_folder_subfolders($directory)
{
    $subfolders = [];
    if (is_dir($directory)) {
        foreach (scandir($directory) as $subfolder) {
            if ($subfolder !== '.' && $subfolder !== '..' && is_dir($directory . DIRECTORY_SEPARATOR . $subfolder)) {
                $subfolders[] = $subfolder;
            }
        }
    }

    return $subfolders;
}
