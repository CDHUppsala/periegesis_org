<?php
$radioDownload = false;
if (
    (!empty($radio__UserSessionIsActive) && $radio__UserSessionIsActive)
    || (!empty($radio__StudentSessionIsActive) && $radio__StudentSessionIsActive)
    || (!empty($radio__ParticipantSessionIsActive) && $radio__ParticipantSessionIsActive)
) {
    $radioDownload = true;
}

if ($radioDownload == false) {
    sleep(5);
    echo "<h1>No way home!</h1>";
    exit();
}

if (empty(sx_PrivateArchivesPath)) {
    sleep(5);
    echo "<h1>No way home!</h1>";
    exit();
}

$strFileName = "";
if (isset($_GET["fn"])) {
    $strFileName = $_GET["fn"];
}

if (empty($strFileName) || strpos($strFileName, " ") > 0 || strpos($strFileName, "./") !== false) {
    sleep(5);
    echo "<h1>No way home!</h1>";
    exit();
} else {
    $FileToDownload = realpath(PROJECT_PRIVATE . sx_PrivateArchivesPath  . $strFileName);
    if (file_exists($FileToDownload)) {
        $fileSize = filesize($FileToDownload);
        header("Cache-Control: private");
        header("Content-Type: application/stream");
        header("Content-Length: " . $fileSize);
        header("Content-Disposition: attachment; filename=" . $strFileName);
        readfile($FileToDownload);
        //exit();
    } else {
        sleep(5);
        echo "<h1>No way home!</h1>";
        exit();
    }
}
