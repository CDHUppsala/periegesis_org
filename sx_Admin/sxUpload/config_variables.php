<?php


function sx_checkFileNames($strText)
{
    $VALID_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.";
    $radioResult = false;
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_TABLE, $strChar, 0) !== false) {
                $radioResult = true;
            } else {
                $radioResult = false;
                break;
            }
        }
    }
    return $radioResult;
}
function post_max_size()
{
    static $max_size;
    if (empty($max_size) || $max_size < 0) {
        $post_max = (int)(ini_get('post_max_size'));
        if ($post_max > 0) {
            $max_size = $post_max;
        }else{
            $max_size = 8;
        }
    }
    return $max_size;
}

function upload_max_size()
{
    static $max_size;
    if (empty($max_size) || $max_size < 0) {
        $upload_max = (int)(ini_get('upload_max_filesize'));
        if ($upload_max > 0) {
            $max_size = $upload_max;
        }else{
            $max_size = 2;
        }
    }
    return $max_size;
}

$intMaxPostSize = post_max_size();
$intMaxFileSize = upload_max_size();

// Get the minimum of the two values
$maxFileSize = min($intMaxPostSize, $intMaxFileSize);

// Transform to bytes if value is set to MB
if($maxFileSize <= 2000000) {
    $maxFileSizeInBytes = $maxFileSize * 1024 * 1024;
}else{
    $maxFileSizeInBytes = $maxFileSize;
}

// Default destination folder
$str_DestinationFolder = 'images';

/*
if (!empty($_POST["Destination"])) {
    $str_DestinationFolder = $_POST["Destination"];
}

*/
