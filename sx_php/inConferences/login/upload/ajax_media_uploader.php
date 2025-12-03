<?php
/**
 * If session is already distroyed, session_destroy generates a warning
 * So, restart the session!
 * If session is already started, the following wil be ignored without warning when using @
 */
@session_start();

include dirname(dirname(__DIR__)) . '/check_sessions.php';
include __DIR__ . '/upload_functions.php';

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    session_destroy();
    echo 'Session_timed_out!';
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentChunk = isset($_POST['currentChunk']) ? (int) ($_POST['currentChunk']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? (int) ($_POST['totalChunks']) : 1;
    $intConferenceID = isset($_POST['conferenceID']) ? (int) ($_POST['conferenceID']) : 0;
    $filename = $_POST['filename'];

    $prefix = 'pid_' . $int_ParticipantID .'__';
    $filename = $prefix . $filename;

    if ($intConferenceID == 0) {
        session_destroy();
        echo 'No_Way_Home!';
        exit();
    }

    // Check, but only once, upload rights for participant in this conference
    if (!isset($_SESSION['UploadRight'])) {
        $radioUploadRights = sx_checkUploadMediaRights($intConferenceID, $int_ParticipantID,);
        if ($radioUploadRights) {
            $_SESSION['UploadRight'] = true;
        } else {
            session_destroy();
            echo 'No_Way_Home!';
            exit();
        }
    }
    
    $destination = '';
    $uploadDir = "";
    // just in case of errors in check function without exit!
    if (isset($_SESSION['UploadRight']) && $_SESSION['UploadRight'] === true) {
        $destination = "/imgMedia/conf_" . $intConferenceID;
        $uploadDir =  realpath($_SERVER['DOCUMENT_ROOT']) . '/' . $destination . '/';
    }else{
        session_destroy();
        echo 'No_Way_Home!';
        exit();
    }

    $radioUpload = false;

    if (!empty($uploadDir) && file_exists($uploadDir) && is_dir($uploadDir)) {
        $filePath = $uploadDir . $filename;
        $radioFileExists = sx_checkIfFileExists($filePath);
        if($radioFileExists) {
            $radioUpload = true;
        }else{
            $arrCheckMaxUploads = sx_checkMaxUploadsIsReached($uploadDir, $prefix, $int_MaxMediaUploads);
            if($arrCheckMaxUploads[0]) {
                $radioUpload = false;
                unset($_SESSION['UploadRight']);
                echo 'Max allowed files uploaded!';
                echo ' File Names:<br>';
                for($f =1; $f < count($arrCheckMaxUploads); $f++ ) {
                    echo ' - '. $arrCheckMaxUploads[$f] . '<br>';
                }
                echo 'Use the same File Name if you want to replace or update a file.';
                exit;
            }else{
                $radioUpload = true;
            }
        }
    } else {
        unset($_SESSION['UploadRight']);
        echo 'Error_Directory_Does_not_exists!';
        exit;
    }

    if($radioUpload) {
        $chunk = file_get_contents($_FILES['file']['tmp_name']);

        if ($currentChunk == 0) {
            // On the first chunk or non-chunked upload, create or truncate the file
            file_put_contents($filePath, $chunk);
        } else {
            // On subsequent chunks, append to the file
            file_put_contents($filePath, $chunk, FILE_APPEND);
        }

        if ($currentChunk == $totalChunks - 1) {
            // This was the last chunk or a non-chunked upload, upload complete
            unset($_SESSION['UploadRight']);
            echo 'Upload complete for ' . $filename;
        } else {
            // More chunks to go, acknowledge receipt
            echo 'Chunk ' . $currentChunk . ' received for ' . $filename;
        }
    }
}
