<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";

/**
 * Upload files from external sources to the defauls folder 
 * in the remote server to be used to update database tables 
 */

 $csv_to_book = [
    'vg4xbkht7tblkt' => 'Book_01',
    'q25vx8179944yk' => 'Book_02',
    'qzqqtb52ut59u7' => 'Book_03',
    'iteuia4mpqdfa9' => 'Book_04',
    'o12fg62uwe4a3r' => 'Book_05',
    'dab3yh8cvjciwz' => 'Book_06',
    '2ytcj885cuie6w' => 'Book_07',
    'n1dny8qzasy2ni' => 'Book_08',
    'fh0zinvr8oqmjl' => 'Book_09',
    'bx6x9ozcqcn0d4' => 'Book_10'
];

$docids = [
    "vg4xbkht7tblkt",
    "q25vx8179944yk",
    "qzqqtb52ut59u7",
    "iteuia4mpqdfa9",
    "o12fg62uwe4a3r",
    "dab3yh8cvjciwz",
    "2ytcj885cuie6w",
    "n1dny8qzasy2ni",
    "fh0zinvr8oqmjl",
    "bx6x9ozcqcn0d4"
];

// Path to the default folder of the remote server
$strImportExportFolder = "/import_export_files";
if (defined('SX_PrivateInportExportFilesFolder') && !empty(SX_PrivateInportExportFilesFolder)) {
    $strImportExportFolder = "/". SX_PrivateInportExportFilesFolder;
}

$uploading_dir = PROJECT_PRIVATE . $strImportExportFolder;

//$url = "http://recogito.humlab.umu.se/document/$docid/downloads/annotations/csv";

$errLoad = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_array($docids)) {
    foreach ($docids as $docid) {
        $url = "http://recogito.abm.uu.se/document/$docid/downloads/annotations/csv";

        $prefix = $csv_to_book[$docid];
        $filepath = "$uploading_dir/{$prefix}_{$docid}.csv";
        if (!file_put_contents($filepath, file_get_contents($url))) {
            $errLoad[] = "Downloading $url to $filepath failed.";
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
