<?php

function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}


function sx_getFolderFilesByExtention($dir, $extention)
{
    if ($arFiles = scandir($dir)) {
        $ar_Files = [];
        $c = count($arFiles);
        for ($f = 0; $f < $c; $f++) {
			$loopFile = $arFiles[$f];
            if ($loopFile != "." && $loopFile != ".." && is_file($dir ."/". $loopFile)) {
				$loopArr = explode(".",$loopFile);
				$loopExt = end($loopArr);
                if (strtolower($loopExt) == strtolower($extention)) {
                    $ar_Files[] = $loopFile;
                }
            }
        }
        return $ar_Files;
    } else {
        return false;
    }
}

?>
