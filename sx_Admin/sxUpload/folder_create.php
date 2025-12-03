<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include "config_upload.php";

if (!SX_allowSingleFolderCreation) {
    header("location: ../main.php");
    exit();
}
if (!empty($_GET["clear"])) {
    unset($_SESSION["ParentFolder"]);
}


/**
 * Create the defined Folder
 */
$errMsg = null;
$strParentFolder = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST["FolderName"])) {
    if (!empty($_POST["ParentFolder"])) {
        $strParentFolder = $_POST["ParentFolder"];
        if (in_array($strParentFolder, ARR_UploadableParentFolders) && is_dir(sx_RootPath . $strParentFolder)) {
            $_SESSION["ParentFolder"] = $strParentFolder;
            $sxFolderName = $_POST["FolderName"];
            $strCretionPath = sx_RootPath . $strParentFolder . "/" . $sxFolderName;

            if (!is_dir($strCretionPath)) {
                mkdir($strCretionPath);
                header("location: folder_create.php");
                exit();
            } else {
                $errMsg = lngTheNameAllreadyExistsInFolder;
            }
        } else {
            $errMsg = "No way home!";
        }
    } else {
        unset($_SESSION["ParentFolder"]);
        $errMsg = "You must select a Parent Folder!";
    }
}

if (isset($_SESSION["ParentFolder"])) {
    $strParentFolder = $_SESSION["ParentFolder"];
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
        <h2><?= lngCreationOfNewSubfolder ?></h2>
        <?php if ($errMsg != "") { ?>
            <div class="errMsg"><?= $errMsg ?></div>
        <?php
        }
        if (is_array(ARR_UploadableParentFolders)) { ?>
            <form action="folder_create.php" method="post" name="createFolders" onsubmit="return CheckStringText(this);">
                <fieldset>
                    <p><b><?= lngSelectParentFolder ?>:</b></p>
                    <input type="radio" name="ParentFolder" checked value="" onclick="displayLayer('layer-1');"> None<br>
                    <?php
                    $iCount = count(ARR_UploadableParentFolders);
                    $arrExistedParentFolders = array();
                    $z = -1;
                    for ($i = 0; $i < $iCount; $i++) {
                        $loodParent = trim(ARR_UploadableParentFolders[$i]);
                        if (is_dir(sx_RootPath . $loodParent)) {
                            $arrExistedParentFolders[] = $loodParent;
                            $z++;
                            $inSelected = "";
                            if (!empty($strParentFolder)) {
                                if ($loodParent == $strParentFolder) {
                                    $inSelected = "checked";
                                }
                            } ?>
                            <input type="radio" name="ParentFolder" value="<?= $loodParent ?>" <?= $inSelected ?> onclick="displayLayer('layer<?= $z ?>');">
                            <?= lngCreateInParentFolders ?>: <b><?= $loodParent ?>/</b><br>
                    <?php
                        }
                    }
                    ?>
                </fieldset>
                <fieldset>
                    <p><b><?= lngDefineNewSubfolder ?>:</b></p>
                    <input type="text" name="FolderName" size="22"><br><?= lngValidFolderNames ?>
                    <p><input class="button" type="submit" value="<?= lngCreateNewSubfolder ?>" name="create"></p>
                </fieldset>
            </form>
        <?php
        } else { ?>
            <div class="errMsg"><?= lngParentFoldersDoNoExist ?>!</div>
        <?php
        }  ?>

        <div id="layer-1" class="coloredBG" style="padding: 1rem 0 0.1rem 2rem; display: block">
            <p><b>Select a Parent Folder to view its Existing Subfolders!</b></p>
        </div>
        <?php
        if (!empty($arrExistedParentFolders)) {
            $iCount = count($arrExistedParentFolders);
            for ($e = 0; $e < $iCount; $e++) {
                $loopParent = $arrExistedParentFolders[$e];

                $strDisplay = "none";
                if (!empty($strParentFolder)) {
                    if ($loopParent == $strParentFolder) {
                        $strDisplay = "block";
                    }
                } ?>
                <div id="layer<?= $e ?>" class="coloredBG" style="padding: 1rem 0 0.1rem 2rem; display: <?= $strDisplay ?>">
                    <p><b><?= lngExistedSubfolders ?>: (<?= $loopParent ?>)</b></p>
                    <p>
                        <?php
                        foreach (ARR_UploadableFolders as $strFolder) {
                            if (strpos($strFolder, $loopParent) !== false) {
                                if ($strFolder != $loopParent) { ?>
                                    <span><?= $strFolder ?> </span><br>
                        <?php
                                }
                            }
                        } ?>
                    </p>
                </div>
        <?php
            }
        }
        echo "<p>" . lngToConnectImagesToSubfoleder . "</p>";

        ?>
    </div>
    <script>
        //Excludes not valid letters
        function CheckStringText(theForm) {
            x = document.createFolders
            TheString = x.FolderName.value

            var valid = 1
            var GoodChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890_"
            var i = 0
            if (TheString == "") {
                valid = 0
                alert("<?= lngPleaseWriteAName ?>")
            }
            for (i = 0; i < TheString.length; i++) {
                if (GoodChars.indexOf(TheString.charAt(i)) == -1) {
                    valid = 0
                    var strErr = (TheString.charAt(i) == " ") ? "space" : TheString.charAt(i);
                    alert("The character: [" + strErr + "] is not valid.")
                }
            }
            if (/^[0-9_]/.test(TheString)) {
                valid = 0
                alert("Folder name should not start with number or underscore!")
            }
            if (valid == 0) {
                return false
            }
        }

        // SX - Change Layers and Link colors by Tabs
        function displayLayer(layer) {
            i = -1
            while (document.getElementById("layer" + i) != null) {
                document.getElementById("layer" + i).style.display = (layer == ("layer" + i)) ? 'block' : 'none';
                i++
            }
        }
    </script>
</body>

</html>