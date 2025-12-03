<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include "config_upload.php";

$levelsBack = "../../";

/**
 * Get the selected folder number (as number in the array of uploadable folder)
 * To show the first folder by default set value to 0
 */
$intFolder = 0;
if (!empty($_POST["selectedFolder"])  && intval($_POST["selectedFolder"]) > 0) {
    $intFolder = (int) $_POST["selectedFolder"];
}

/**
 * Get the selected folder name (from the folder number above)
 */
$strSelectedFolder = "";
$strSelectedFolderPath = "";
$iCount = count(ARR_DownloadableFolders);
for ($d = 0; $d < $iCount; $d++) {
    if ($d == $intFolder) {
        $strSelectedFolderPath = trim(ARR_DownloadableFolders[$d]);
        if (substr($strSelectedFolderPath, -1) != "/") {
            $strSelectedFolderPath = $strSelectedFolderPath . "/";
        }
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SX CMS - Download Files</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body id="bodyUpload" class="body">
    <?php include "nav_top.php"; ?>
    <div class="maxWidthWide">
        <h2><?= lngDownloadFiles ?></h2>
        <form action="download_files.php" method="post" name="DownloadFiles">
            <fieldset>
                <b><?= lngSelectFolder ?>: </b>
                <select name="selectedFolder">
                    <?php
                    $iCount = count(ARR_DownloadableFolders);
                    for ($sx = 0; $sx < $iCount; $sx++) {
                        $strSelected = "";
                        if ($sx == intval($intFolder)) {
                            $strSelected = " selected";
                        } ?>
                        <option value="<?= $sx ?>" <?= $strSelected ?>><?= trim(ARR_DownloadableFolders[$sx]) ?></option>
                    <?php
                    } ?>
                </select>
                <input type="submit" value="<?= lngShowFiles ?>" name="viewThisFolder">
            </fieldset>
        </form>
        <div class="text">
            <?php
            $arrFiles = [];
            if (!empty($strSelectedFolderPath)) {
                $arr_Files = scandir(sx_RootPath . $strSelectedFolderPath);
                $iFiles = count($arr_Files);
                for ($f = 0; $f < $iFiles; $f++) {
                    $loopFile = $arr_Files[$f];
                    if ($loopFile != "." && $loopFile != ".." && !is_dir($strSelectedFolderPath . $loopFile)) {
                        $arrFiles[] = $loopFile;
                    }
                }
            } else {
                echo '<div class="msgInfo">1' . lngTheRequestedFolderDoesNotExist . '</div>';
            }
            if (!empty($arrFiles)) {
                echo "<h3>" . lngClickOnAFileToDownloadIt . "</h3>";
                $iFiles = count($arrFiles);
                for ($f = 0; $f < $iFiles; $f++) {
                    $loopFile = $arrFiles[$f]; ?>
                    <ul>
                        <li><a target="_New" class="color" href="<?= $levelsBack . $strSelectedFolderPath . "/" . $loopFile ?>"><b><?= $loopFile ?></b></a>
                            (<?= number_format(filesize(sx_RootPath . $strSelectedFolderPath . $loopFile), 0, ",", " ") . "kb" ?>)</li>
                    </ul>
            <?php }
            } else {
                echo '<div class="msgInfo">' . lngTheFolderIsEmpty . '</div>';
            } ?>
        </div>
    </div>
</body>

</html>