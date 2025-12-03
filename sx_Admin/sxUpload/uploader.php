<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include __DIR__ . '/config_upload.php';
include __DIR__ . '/config_variables.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentChunk = isset($_POST['currentChunk']) ? intval($_POST['currentChunk']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 1;
    $filename = $_POST['filename'];
    $prefix = isset($_POST['prefix']) ? (string) $_POST['prefix'] : '';
    $destination = isset($_POST['destination']) ? (string) $_POST['destination'] : '';

    if (!empty($prefix) && sx_checkFileNames($prefix)) {
        $filename = $prefix . '__' . $filename;
    }
    $uploadDir = "";
    if (!empty($destination) && in_array($destination, ARR_UploadableFolders)) {
        $uploadDir =  realpath($_SERVER['DOCUMENT_ROOT']) . '/' . $destination . '/';
    }

    if (!empty($uploadDir) && file_exists($uploadDir) && is_dir($uploadDir)) {

        $chunk = file_get_contents($_FILES['file']['tmp_name']);
        $filePath = $uploadDir . $filename;

        if ($currentChunk == 0) {
            // On the first chunk or non-chunked upload, create or truncate the file
            file_put_contents($filePath, $chunk);
        } else {
            // On subsequent chunks, append to the file
            file_put_contents($filePath, $chunk, FILE_APPEND);
        }

        if ($currentChunk == $totalChunks - 1) {
            // This was the last chunk or a non-chunked upload, upload complete
            echo 'Upload complete for ' . $filename;
        } else {
            // More chunks to go, acknowledge receipt
            echo 'Chunk ' . $currentChunk . ' received for ' . $filename;
        }
    } else {
        echo 'Error: directory does not exists! '. $destination .' / '. $uploadDir;
    }
}
