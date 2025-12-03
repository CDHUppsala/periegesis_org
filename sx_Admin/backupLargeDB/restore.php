<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";
include __DIR__ . "/functions.php";

$message = '';

if (isset($_POST["Restore"])) {
    if (!empty($_POST["RemoteBackupFolder"])) {
        $strRemoteBackupFolder = $_POST["RemoteBackupFolder"];
        $strBackupFolderPath = $str_RemoteBackupDirectory . '\\' . $strRemoteBackupFolder;
        $arrFiles = scandir($strBackupFolderPath);

        if (is_array($arrFiles)) {
            $iTablesImported = 0;
            $iTablesCount = 0;
            foreach ($arrFiles as $file) {
                $filePath = $strBackupFolderPath . "\\" . $file;
                if (!is_dir($filePath)) {
                    $array = explode(".", $file);
                    $extension = strtolower(end($array));
                    if ($extension == 'sql' || $extension == 'gz') {
                        if ($extension == 'sql') {
                            $return = sx_importToMySQL_SQL($filePath);
                        }else{
                            $return = sx_importToMySQL_GZIP($filePath);
                        }

                        if (!empty($return)) {
                            $message .= '<div class="msgError">There is an error in Restoring Table: ' . $file . ' (' . $iTablesCount . '): ' . $return . '</div>';
                        } else {
                            $iTablesImported++;
                        }
                        $iTablesCount++;
                    } else {
                        $message .= '<label class="msgWarning">The file ' . $file . ' (' . $iTablesCount . ') is not a valid SQL or GZIP Files.</label>';
                    }
                }
            }
            if ($iTablesImported > 0) {
                $message .= '<div class="msgSuccess">Successful Import of ' . $iTablesImported . ' Tables.</div>';
            }
        } else {
            $message .= '<label class="msgWarning">Please Select A Folder with SQL or GZIP Files</label>';
        }
    }
} ?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere - Restore Large Database</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <link rel="stylesheet" href="ps.css">
</head>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Restore Large Database from Backup Folder with a SQL or GZIP File by Table</h2>
        <div>
            <a class="button" href="index.php">Open Backup Database</a>
        </div>
    </header>
    <section class="maxWidth">
        <h1>Restore Database from Backup Folder</h1>
        <?php if (!empty($message)) echo $message; ?>
        <form class="form_grid" method="POST" name="RestorDatabase" action="restore.php" enctype="multipart/form-data">
            <fieldset>
                <label>Select Remote Backup Folder: </label>
                <select size="1" name="RemoteBackupFolder">
                    <option value="">Select SQL or GZ Folder</option>
                    <?php
                    $arrFolders = scandir($str_RemoteBackupDirectory);

                    if (is_array($arrFolders)) {
                        foreach ($arrFolders as $folder) {
                            if (is_dir($str_RemoteBackupDirectory . "\\" . $folder)) {
                                if (substr($folder, 0, 1) != ".") {
                                    $strSelected = "";
                                    if ($folder == $strRemoteBackupFolder) {
                                        $strSelected = "selected ";
                                    } ?>
                                    <option <?= $strSelected ?>value="<?= $folder ?>"><?= $folder ?></option>
                    <?php
                                }
                            }
                        }
                    } ?>
                </select>
            </fieldset>
            <p class="alignCenter">
                <input class="button" type="submit" name="Restore" value="Restore" />
            </p>
        </form>
    </section>
</body>

</html>