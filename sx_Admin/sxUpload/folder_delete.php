<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include "config_upload.php";

if (!SX_allowSingleFolderCreation) {
    header("location: ../main.php");
    exit();
}

/**
 * Delete requested Folder
 */
$errMsg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["SubFolder"])) {
    if (empty($_POST["SubFolder"])) {
        $errMsg = 'You must select an empty folder to delete.';
    } else {
        $sxSubFolder = $_POST["SubFolder"];
        $strParentFolder = '';
        if (strpos($sxSubFolder, '/') > 0) {
            $strParentFolder = explode('/', $sxSubFolder)[0];
        }

        if (!empty($strParentFolder) && in_array($strParentFolder, ARR_UploadableParentFolders)) {
            $strPathToSubFolder = sx_RootPath . $sxSubFolder;
            echo $strPathToSubFolder;

            if (is_dir($strPathToSubFolder)) {
                if (sx_checkEmptyFolder($strPathToSubFolder)) {
                    if (!rmdir($strPathToSubFolder)) {
                        $errMsg = lngUnspecifiedErrorNoFoldersCreated;
                    } else {
                        header("location: folder_delete.php?msg=yes");
                        exit();
                    }
                } else {
                    $errMsg = "The subfolder is not empty!";
                }
            } else {
                $errMsg = "No way home A";
            }
        } else {
            $errMsg = "No way home B";
        }
    }
}
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SX CMS - Create Singel Subfolder</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body id="bodyUpload" class="body">
    <?php include "nav_top.php"; ?>
    <div class="maxWidth">
        <h2><?= lngFolderDelete ?></h2>
        <p><?= lngFolderEmptyToDelete ?></p>
        <?php
        if (!empty($errMsg)) { ?>
            <div class="msgError"><?= $errMsg ?></div>
        <?php
        }
        if (!empty($_GET["msg"])) { ?>
            <div class="msgInfo"><?= lngTheSubfolderHasBeenDeleted ?></div>
        <?php
        }

        if (empty(ARR_UploadableFolders)) { ?>
            <div class="msgError"><?= lngParentFoldersDoNoExist ?>!</div>
        <?php
        } else { ?>
            <form action="folder_delete.php" method="post" name="DeleteFolders">
                <fieldset>
                    <p><b><?= lngSelectFolder ?>:</b></p>
                    <input type="radio" name="SubFolder" checked value=""> <b>None</b><br>

                    <?php
                    $iCount = count(ARR_UploadableFolders);
                    $iLine = 0;
                    for ($i = 0; $i < $iCount; $i++) {
                        $loopFolder = trim(ARR_UploadableFolders[$i]);
                        if (!in_array($loopFolder, ARR_UploadableParentFolders)) {
                            if (sx_checkEmptyFolder(sx_RootPath . $loopFolder)) { ?>
                                <input type="radio" name="SubFolder" value="<?= $loopFolder ?>">
                                <?= lngFolderDelete ?>: <b><?= $loopFolder ?></b> <br>
                            <?php
                            } else { ?>
                                Not Empty: <?= $loopFolder ?> <br>
                <?php
                            }
                            $iLine = $i + 1;
                        } elseif ($i > 0 && $i == $iLine) {
                            echo '<br>';
                        }
                    }
                }
                ?>
                </fieldset>
                <fieldset>
                    <p><input class="button" type="submit" value="<?= lngFolderDelete ?>" name="delete"></p>
                </fieldset>
            </form>
    </div>
</body>

</html>