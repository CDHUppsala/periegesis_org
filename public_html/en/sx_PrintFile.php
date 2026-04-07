<?php 
include __DIR__ . "/siteLang/sxLang.php"; 

$radioDownload = false;
if (
    isset($_SESSION["Users_" . sx_DefaultSiteLang])
    || isset($_SESSION["Students_" . sx_DefaultSiteLang])
    || isset($_SESSION["Participants_" . sx_DefaultSiteLang])
) {
    $radioDownload = true;
}
$radioDownload = true;

if ($radioDownload == false) {
    header('Location: index.php');
    exit();
}

include PROJECT_PHP ."/sx_DownloadFile.php";

$conn = null;
?>