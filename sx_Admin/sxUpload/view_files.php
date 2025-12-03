<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include __DIR__ . '/config_upload.php';

$arrImgExtensions = ['jpeg', 'jpg', 'gif', 'png', 'webp', 'apng', 'svg', 'bmp', 'ico'];
$levelsBack = "../../";

// Change between showing only files or showing also the images of image files
// Save the request in the form for reuse
$radioShowImages = false;
$strTempTitle = lngViewFolderFiles;
$formRequest = "";
if (isset($_GET["images"]) && $_GET["images"] == "yes") {
    $formRequest = 'yes';
    $radioShowImages = true;
    $strTempTitle = lngViewFolderImages;
}

/**
 * Get the selected folder number (as number in the array of uploadable folder)
 * To show the first folder by default set value to 0
 */
$intFolder = 0;
if (isset($_POST["selectedFolder"])) {
    $intFolder = (int) $_POST["selectedFolder"];
    $_SESSION["iFolder"] = $intFolder;
} elseif (isset($_SESSION["iFolder"])) {
    $intFolder = $_SESSION["iFolder"];
}

/**
 * Get the selected folder name from the above index number of the folder
 * Define the phisical path of the folder to be used only for checking and deleting functions
 */
$strSelectedFolder = "";
$strSelectedPhysicalPath = "";
$iCount = count(ARR_UploadableFolders);
for ($d = 0; $d < $iCount; $d++) {
    if ($d == $intFolder) {
        $strSelectedFolder = trim(ARR_UploadableFolders[$d]);
        if (substr($strSelectedFolder, -1) != "/") {
            $strSelectedFolder = $strSelectedFolder . "/";
        }
        $strSelectedPhysicalPath = sx_RootPath . $strSelectedFolder;
        break;
    }
}

/**
 * Delet files from selected folder
 */
static $errMsg = "";
function sx_DeleteFile($strFolder, $strFileName)
{
    global $errMsg;
    $sFile = $strFolder . $strFileName;
    if (file_exists($sFile)) {
        if (!unlink($sFile)) {
            $errMsg .= ("The file $strFileName cannot be deleted due to an error.") . "<br>";
        } else {
            $errMsg .= ("The file $strFileName has been deleted.") . "<br>";
        }
    } else {
        $errMsg .= ("The file $strFileName does not exist in the folder.") . "<br>";
    }
}

if (!empty($_POST["deletImg"]) && !empty($strSelectedPhysicalPath)) {
    foreach ($_POST as $name => $value) {
        if ($value == "ON") {
            $sFileName = trim($_POST[str_replace("box_", "file_", $name)]);
            sx_DeleteFile($strSelectedPhysicalPath, $sFileName);
        }
    }
}
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SX CMS - View Files</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="../js/jq/jquery.min.js"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js"></script>
</head>

<body id="bodyUpload" class="body">
    <?php
    include "nav_top.php";
    ?>
    <div class="maxWidthWide">
        <h2><?= $strTempTitle ?> </h2>
        <form action="view_files.php?images=<?= $formRequest ?>" method="post" name="ToSelectFolder">
            <fieldset>
                <b><?= lngSelectFolder ?></b>:
                <select name="selectedFolder">
                    <?php
                    /**
                     * The selection of folders is by their index in the array, not by their name
                     */
                    $strLastParent = "";
                    for ($f = 0; $f < $iCount; $f++) {
                        $strLoopFolder = trim(ARR_UploadableFolders[$f]);

                        $strSelected = "";
                        if ($f == intval($intFolder)) {
                            $strSelected = "selected";
                            $strSelectedFolder = $strLoopFolder;
                        }
                        $strLoopParent = explode("/", $strLoopFolder)[0];
                        if ($strLoopParent != $strLastParent) {
                            if ($f > 0) {
                                echo "</optgroup>";
                            }
                            echo '<optgroup label="' . $strLoopParent . '">';
                        } ?>
                        <option value="<?= $f ?>" <?= $strSelected ?>><?= $strLoopFolder ?></option>
                    <?php
                        $strLastParent = $strLoopParent;
                    } ?>
                </select>
                <input class="button" type="submit" value="<?= lngOpenFolder ?>" name="viewThisFolder">
            </fieldset>

            <?php
            if (!empty($errMsg)) { ?>
                <div class="msgInfo">
                    <?= $errMsg ?>
                </div>
            <?php
            } ?>
            <p><?= lngCheckTheImagesYouWantToDelete ?></p>
            <fieldset>
                <?php
                $arrFiles = [];
                if (!empty($strSelectedPhysicalPath)) {
                    $arrFiles = scandir($strSelectedPhysicalPath);
                } else {
                    echo '<div class="msgInfo">' . lngTheRequestedFolderDoesNotExist . '</div>';
                }
                if (!empty($arrFiles)) {
                    $iFiles = count($arrFiles);
                    for ($f = 0; $f < $iFiles; $f++) {
                        $loopFile = $arrFiles[$f];
                        if ($loopFile != "." && $loopFile != ".." && !is_dir($strSelectedPhysicalPath . $loopFile)) { ?>
                            <div class="row view_files" style="border-bottom: 2px solid #fff">
                                <div style="width:60% flex = 1;">
                                    <p>
                                        <input type="checkbox" name="box_<?= $f ?>" value="ON">
                                        <input type="hidden" name="file_<?= $f ?>" value="<?= $loopFile ?>">
                                        <a class="imgPreview" href="<?= $levelsBack . $strSelectedFolder . "/" . $loopFile ?>">
                                            <b><?= $loopFile ?></b></a>
                                        (<?= number_format(filesize($strSelectedPhysicalPath . $loopFile), 0, ",", " ") . "kb" ?>)
                                        <a download="<?= $loopFile ?>" href="<?= $levelsBack . $strSelectedFolder . "/" . $loopFile ?>">Download</a>
                                    </p>
                                    <?php
                                    if ($radioShowImages) {
                                        $strFolderToCopy = "";
                                        if ($pos = strpos($strSelectedFolder, "/")) {
                                            $strFolderToCopy = substr($strSelectedFolder, $pos + 1) . "/";
                                        } ?>
                                        <p>
                                            <input type="text" value="<?= $strFolderToCopy . $loopFile ?>" name="<?= "Copy_" . $f ?>">
                                        </p>
                                    <?php
                                    } ?>
                                </div>
                                <?php
                                if ($radioShowImages) {
                                    $arrLoop = explode('.', $loopFile);
                                    $ext = strtolower(end($arrLoop));
                                    if (in_array($ext, $arrImgExtensions)) { ?>
                                        <div style="width:40%">
                                            <img class="imgPreview" src="<?= $levelsBack . $strSelectedFolder . "/" . $loopFile ?>">
                                        </div>
                                <?php
                                    }
                                } ?>
                            </div>
                <?php
                        }
                    }
                } else {
                    echo '<div class="msgInfo">' . lngTheFolderIsEmpty . '</div>';
                } ?>
            </fieldset>
            <fieldset>
                <input class="button" type="submit" value="<?= lngDeleteCheckedBoxes ?>" name="deletImg">
                <input class="button" type="reset" value="<?= lngCleanCheckedBoxes ?>" name="B2"></p>
            </fieldset>
        </form>
    </div>
    <div id="shoewImage" style="display: none; position: absolute; top: 0; right: 0; width: 50%">
        <img style="max-width: 100%; height: auto;" src="">
    </div>
    <div id="imgPreview"><img src=""></div>
</body>

</html>