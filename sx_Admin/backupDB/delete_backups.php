<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/config.php";
include __DIR__ . "/functions_import.php";

if (isset($_GET['clear']) && $_GET['clear'] === 'yes') {
    unset($_SESSION['BackupFolderName']);
}

$arrMessage = [];
$radioLocalBackupFile = false;
$radioDleteBackupFile = false;
$strDleteBackupFile = "";

$strBackupFolderName = 'Default';
$strBackupFolderPath = $str_RemoteBackupDirectory;

if (isset($_SESSION['BackupFolderName']) && !empty($_SESSION['BackupFolderName'])) {
    $strBackupFolderName = $_SESSION['BackupFolderName'];
    $strBackupFolderPath = "{$str_RemoteBackupDirectory}\\{$strBackupFolderName}";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST["GetFolderFiles"])) {
        if (isset($_POST['BackupFolderName']) && !empty($_POST['BackupFolderName'])) {
            $strBackupFolderName = $_POST['BackupFolderName'];
            if ($strBackupFolderName !== 'Default') {
                $_SESSION['BackupFolderName'] = $strBackupFolderName;
                $strBackupFolderPath = "{$str_RemoteBackupDirectory}\\{$strBackupFolderName}";
            } else {
                unset($_SESSION['BackupFolderName']);
                $strBackupFolderName = 'Default';
                $strBackupFolderPath = $str_RemoteBackupDirectory;
            }
        }
    }

    if (isset($_POST["DeleteFiles"])) {
        if (isset($_POST['DleteBackupFile'])) {
            $checkedFiles = $_POST['DleteBackupFile'];

            foreach ($checkedFiles as $file) {
                $filePath = $strBackupFolderPath . DIRECTORY_SEPARATOR . $file;

                if (is_file($filePath)) {
                    if (unlink($filePath)) {
                        $arrMessage[] = "Deleted file: " . htmlspecialchars($file);
                    } else {
                        $arrMessage[] =  "Could not delete file: " . htmlspecialchars($file);
                    }
                } else {
                    $arrMessage[] =  "File does not exist: " . htmlspecialchars($file);
                }
            }
        } elseif (isset($_POST['DeleteBackupFolder']) && !empty($_POST['DeleteBackupFolder'])) {
            $strDeleteBackupFolder = trim($_POST['DeleteBackupFolder']);
            if ($strDeleteBackupFolder !== 'Default' && str_contains($strBackupFolderPath, $strDeleteBackupFolder)) {
                $subdirectory = $strBackupFolderPath;
                if (rmdir($subdirectory)) {
                    $arrMessage[] =  "The Backup Folder <b>{$strDeleteBackupFolder}</b> has been deleted successfully.";
                    unset($_SESSION['BackupFolderName']);
                    $strBackupFolderName = 'Default';
                    $strBackupFolderPath = $str_RemoteBackupDirectory;
                } else {
                    $arrMessage[] =  "Could not delete the Backup Folder <b>{$strDeleteBackupFolder}</b>. Make sure it is empty.";
                }
            } else {
                $arrMessage[] =  "Could not delete the Backup Folder <b>{$strDeleteBackupFolder}</b>. Make sure it is empty.";
            }
        } else {
            $arrMessage[] =  "No files selected.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere - Restore Small Database</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <link rel="stylesheet" href="ps.css">
</head>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Delete Backup Files</h2>
        <div>
            <a class="button" href="index.php?clear=yes">Backup Database</a>
        </div>
        <div>
            <a class="button" href="restore.php?clear=yes">Restore Backups</a>
        </div>
    </header>
    <h1>Delete Old Backup Files and Folders</h1>
    <section class="maxWidthWide">
        <form method="POST" name="GetFolderFiles" action="delete_backups.php" enctype="multipart/form-data">
            <fieldset class="row">
                <div>
                    <label>Select a Backup Folder to Get its Backup Files:</label><br>
                    <select size="1" name="BackupFolderName">
                        <option value="Default">Default Backup Folder</option>
                        <?php
                        $arrFolders = get_folder_subfolders($str_RemoteBackupDirectory);
                        if (is_array($arrFolders)) {
                            foreach ($arrFolders as $folder) {
                                $strSelected = "";
                                if ($folder === $strBackupFolderName) {
                                    $strSelected = "selected ";
                                } ?>
                                <option <?= $strSelected ?>value="<?= $folder ?>"><?= $folder ?></option>
                        <?php
                            }
                        } ?>
                    </select>
                </div>
                <div>
                    <input class="button" type="submit" name="GetFolderFiles" id="GetFolderFiles" value="Get Backup Files from Folder" />
                </div>
            </fieldset>
        </form>
        <?php if (!empty($arrMessage)) { ?>
            <div class="msgInfo">
                <?php echo implode('<br>', $arrMessage); ?>
            </div>
        <?php } ?>
        <div id="WaitingImage" style="display: none; padding: 20px 0; text-align: center"><img style="margin: 5px auto;" src="../images/wait.gif"></div>
    </section>

    <section class="maxWidthWide">
        <form method="POST" name="DeleteBackups" action="delete_backups.php" enctype="multipart/form-data">
            <fieldset>
                <?php
                $arrFiles = get_folder_files($strBackupFolderPath);
                if (is_array($arrFiles) && !empty($arrFiles)) { ?>
                    <table>
                        <th>Restore</th>
                        <th>File Name</th>
                        <th>Last Change</th>
                        <th>Size</th>
                        <?php
                        foreach ($arrFiles as $f_Name) { ?>
                            <tr>
                                <td><input type="checkbox" name="DleteBackupFile[]" value="<?= $f_Name ?>"></td>
                                <td><?= $f_Name ?></td>
                                <td><?php echo date("Y-m-d H:i:s", filemtime($strBackupFolderPath . DIRECTORY_SEPARATOR . $f_Name)); ?></td>
                                <td class="alignRight"><?= number_format(filesize($strBackupFolderPath . DIRECTORY_SEPARATOR . $f_Name), 0, ",", " ") . " KB" ?></td>
                            </tr>
                        <?php
                        } ?>
                    </table>
                <?php
                } else {
                    echo '<p><b>The folder is empty!</b></p>';
                    if ($strBackupFolderName !== 'Default') {
                        echo '<input type="checkbox" name="DeleteBackupFolder" value="' . $strBackupFolderName . '" />';
                        echo "Click on the Box to delete the Backup Folder: <b>{$strBackupFolderName}</b>!";
                    }
                } ?>
            </fieldset>

            <fieldset class="row flex_align_center">
                <div>
                    <input class="button" type="reset" value="<?= lngCleanCheckedBoxes ?>" name="Reset">
                </div>
                <div>
                    <input type="button" value="Check All Boxes" onclick="checkAllCheckboxes()">
                </div>
                <div>
                    <input class="button" type="submit" name="DeleteFiles" id="DeleteFiles" value="Delete Files or Folder" />
                </div>
            </fieldset>
        </form>



    </section>
    <script>
        document.getElementById("Restore").addEventListener("click", function() {
            var targetElement = document.getElementById("WaitingImage");
            targetElement.style.display = "block";
        });

        function checkAllCheckboxes() {
            var checkboxes = document.getElementsByName('DleteBackupFile[]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
            }
        }
    </script>

</body>

</html>