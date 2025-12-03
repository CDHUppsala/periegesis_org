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

$message = '';
$radioLocalBackupFile = false;
$radioRemoteBackupFile = false;
$strRemoteBackupFile = "";

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
    if (!empty($_POST["Restore"]) || !empty($_POST["RestoreTop"])) {
        $radioSQL = false;
        $radioGZ = false;
        $radioContinue = true;
        if (!empty($_POST["RemoteBackupFile"])) {
            $strRemoteBackupFile = $_POST["RemoteBackupFile"];
            $array = explode(".", $strRemoteBackupFile);
            $extension = strtolower(end($array));
            if ($extension == 'sql') {
                $radioSQL = true;
            } elseif ($extension == 'gz') {
                $radioGZ = true;
            } else {
                $radioContinue = false;
            }
            $radioRemoteBackupFile = true;
        } elseif (!empty($_FILES["LocalBackupFile"]["name"])) {
            $array = explode(".", $_FILES["LocalBackupFile"]["name"]);
            $extension = strtolower(end($array));
            if ($extension == 'sql') {
                $radioSQL = true;
            } elseif ($extension == 'gz') {
                $radioGZ = true;
            } else {
                $radioContinue = false;
            }
            $radioLocalBackupFile = true;
        } else {
            $message = '<div class="msgError">Please Select A SQL or GZIP File</div>';
        }

        if ($radioContinue && ($radioSQL || $radioGZ)) {
            if ($radioRemoteBackupFile) {
                $filePath = "{$strBackupFolderPath}\\{$strRemoteBackupFile}";
            } else {
                $filePath = $_FILES["LocalBackupFile"]["tmp_name"];
            }
            if ($radioGZ) {
                $mixReturn = sx_importToMySQL_GZ($filePath);
            } else {
                $mixReturn = sx_importToMySQL_SQL($filePath);
            }

            if (is_numeric($mixReturn) && (int)$mixReturn > 0) {
                $message = "<div class=\"msgSuccess\">Database Tables have been Successfully Restored by $mixReturn Queries.</div>";
            } else {
                $message = "<div class=\"msgError\">{$mixReturn}</div>";
            }
        } else {
            $message = '<div class="msgError">Please Select A SQL or GZIP File</div>';
        }
    }
} ?>

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
        <h2>Public Sphere: - Restore Database</h2>
        <div>
            <a class="button" href="index.php?clear=yes">Backup Database</a>
        </div>
        <div>
            <a class="button" href="delete_backups.php?clear=yes">Delete Backups</a>
        </div>
    </header>
    <h1>Restore Database from a SQL or GZIP File</h1>
    <section class="maxWidthWide">
        <form method="POST" name="GetFolderFiles" action="restore.php" enctype="multipart/form-data">
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

        <?php echo $message; ?>
        <div id="WaitingImage" style="display: none; padding: 20px 0; text-align: center"><img style="margin: 5px auto;" src="../images/wait.gif"></div>
    </section>

    <section class="maxWidthWide">
        <form method="POST" name="RestorDatabase" action="restore.php" enctype="multipart/form-data">
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
                        foreach ($arrFiles as $f_Name) {
                            $f_Ext = strtolower(pathinfo($f_Name)["extension"]);
                            if (strtolower($f_Ext) === "sql" || strtolower($f_Ext) === "gz") {
                                $strChecked = "";
                                if ($f_Name == $strRemoteBackupFile) {
                                    $strChecked = "checked ";
                                } ?>
                                <tr>
                                    <td><input type="radio" <?php echo $strChecked ?>name="RemoteBackupFile" value="<?= $f_Name ?>"></td>
                                    <td><?= $f_Name ?></td>
                                    <td><?php echo date("Y-m-d H:i:s", filemtime($strBackupFolderPath . DIRECTORY_SEPARATOR . $f_Name)); ?></td>
                                    <td class="alignRight"><?= number_format(filesize($strBackupFolderPath . DIRECTORY_SEPARATOR . $f_Name), 0, ",", " ") . " KB" ?></td>
                                </tr>
                        <?php
                            }
                        } ?>
                    </table>
                <?php
                } else {
                    echo 'The folder is empty!';
                } ?>
            </fieldset>

            <fieldset class="row flex_align_center">
                <div>
                    <label>Select Local Backup File:</label> <br>
                    <input type="file" name="LocalBackupFile" />
                </div>
                <div>
                    <input class="button" type="submit" name="Restore" id="Restore" value="Restore" />
                </div>
            </fieldset>
        </form>



    </section>
    <script>
        document.getElementById("Restore").addEventListener("click", function() {
            var targetElement = document.getElementById("WaitingImage");
            targetElement.style.display = "block";
        });
    </script>

</body>

</html>