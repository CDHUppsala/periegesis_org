<?php
/**
 * This function might replace the next one as check is not 
 * required (is made elsewhere) for other types of upload
 */

 $int_MaxMediaUploads = 2;
 $int_MaxDocummentUploads = 3;
 $int_MaxImageUploads = 4;

function sx_checkUploadMediaRights($confID, $partID)
{
    $conn = dbconn();
    $sql = "SELECT a.ToUploadMedia
        FROM conf_rights AS a
            INNER JOIN conf_to_participants AS b
            ON a.ConferenceID = b.ConferenceID AND a.ParticipantID = b.ParticipantID
        WHERE a.ConferenceID = ?
            AND a.ParticipantID = ?
            AND a.AllowRights = 1
            AND (a.WithdrawRightsDate >= CURDATE()
                OR a.WithdrawRightsDate IS NULL)
            AND b.AsksToUploadFiles = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$confID, $partID]);
    $radioUpload = $stmt->fetchColumn();
    if ($radioUpload) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check rights to upload different types of files - Not used
 * @param int $confID : conference ID
 * @param int $partID : participant ID
 * @param int $confID : file type: File (documment), Image, Media
 * @return bool 
 */
function sx_checkUploadRights_NU($confID, $partID, $type)
{
    $conn = dbconn();
    $sql = "SELECT
			a.ToUploadImages,
			a.ToUploadDocuments,
			a.ToUploadMedia
        FROM conf_rights AS a
            INNER JOIN conf_to_participants AS b
            ON a.ConferenceID = b.ConferenceID AND a.ParticipantID = b.ParticipantID
        WHERE a.ConferenceID = ?
            AND a.ParticipantID = ?
            AND a.AllowRights = 1
            AND (a.WithdrawRightsDate >= CURDATE()
                OR a.WithdrawRightsDate IS NULL)
            AND b.AsksToUploadFiles = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$confID, $partID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_ToUploadImages = $rs["ToUploadImages"];
        $radio_ToUploadDocuments = $rs["ToUploadDocuments"];
        $radio_ToUploadMedia = $rs["ToUploadMedia"];
        if ($type == 'File') {
            if ($radio_ToUploadDocuments) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == 'Image') {
            if ($radio_ToUploadImages) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == 'Media') {
            if ($radio_ToUploadMedia) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Check if a file exists
 */
function sx_checkIfFileExists($filePath)
{
    if (is_file($filePath) && !is_dir($filePath)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if files with a particular prefix exist in a directory 
 *  and if the number of files have reached a max allowed number
 * Returns an array with the first valuee being a bool
 *  - if false, max number is not reached (or exceeded)
 *  - if true, max number has been reached, 
 *      with the following values includning the names of files.
 */
function sx_checkMaxUploadsIsReached($directory, $prefix, $max = 2) {
    $files = scandir($directory);
    
    $matchingFiles = [];
    foreach ($files as $file) {
        if (strpos($file, $prefix) === 0) {
            $matchingFiles[] = str_replace($prefix,'',$file);
        }
    }
    
    // Output the matching files
    $output = [];
    if (count($matchingFiles) >= $max) {
        $output[] = true;
        foreach ($matchingFiles as $matchingFile) {
            $output[] = $matchingFile;
        }
    } else {
        $output[] = false;
    }
    return $output;
}
