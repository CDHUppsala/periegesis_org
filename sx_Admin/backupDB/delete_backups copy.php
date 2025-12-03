<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/config.php";
include __DIR__ . "/functions_import.php";

$message = '';
$radioLocalBackupFile = false;
$strRemoteBackupFile = "";
$radioRemoteBackupFile = false;
if (isset($_POST["Restore"])) {
    $radioSQL = false;
    $radioGZ = false;
    if (!empty($_POST["RemoteBackupFile"])) {
        $strRemoteBackupFile = $_POST["RemoteBackupFile"];
        $array = explode(".", $strRemoteBackupFile);
        $extension = strtolower(end($array));
        if ($extension == 'sql') {
            $radioSQL = true;
        } elseif ($extension == 'gz') {
            $radioGZ = true;
        }
        $radioRemoteBackupFile = true;
    } elseif (!empty($_FILES["LocalBackupFile"]["name"])) {
        $array = explode(".", $_FILES["LocalBackupFile"]["name"]);
        $extension = strtolower(end($array));
        if ($extension == 'sql') {
            $radioSQL = true;
        } elseif ($extension == 'gz') {
            $radioGZ = true;
        }
        $radioLocalBackupFile = true;
    } else {
        $message = '<div class="warning">Please Select A SQL File</div>';
    }

    if ($radioSQL || $radioGZ) {
        if ($radioRemoteBackupFile) {
            $filePath = $str_RemoteBackupDirectory . '\\' . $strRemoteBackupFile;
        } else {
            $filePath = $_FILES["LocalBackupFile"]["tmp_name"];
        }
        if ($radioGZ) {
            $mixReturn = sx_importToMySQL_GZ($filePath);
        } else {
            $mixReturn = sx_importToMySQL_SQL($filePath);
        }

        if (is_numeric($mixReturn) && (int)$mixReturn > 0) {
            $message = '<div class="msgSuccess">Database Tables have been Successfully Restored by ' . $mixReturn . ' Queries.</div>';
        } else {
            $message = '<div class="msgError">' . $mixReturn . '</div>';
        }
    } else {
        $message = '<div class="msgError">Invalid File Extention: You can only use SQL and GZIP Files.</div>';
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
            <a class="button" href="index.php">Backup Database</a>
        </div>
    </header>
    <section class="maxWidthWide">
        <h1>Restore Database from a Single SQL or GZIP File</h1>
        <form method="POST" name="RestorDatabase" action="restore.php" enctype="multipart/form-data">
            <fieldset class="row flex_align_center">
                <div>
                    <label>Select Local Backup File:</label> <br>
                    <input type="file" name="LocalBackupFile" />
                </div>
                <div>
                    <label>Select Remote Backup File:</label><br>
                    <select size="1" name="RemoteBackupFile">
                        <option value="">Select SQL or GZ File</option>
                        <?php
                        $arrFiles = scandir($str_RemoteBackupDirectory);
                        if (is_array($arrFiles)) {
                            $iFiles = count($arrFiles);
                            for ($f = 0; $f < $iFiles; $f++) {
                                $f_Name = $arrFiles[$f];
                        ?>
                                <option value="<?= $f_Name ?>"><?= $f_Name ?></option>
                                <?php
                                $f_Ext = strtolower(pathinfo($f_Name)["extension"]);
                                if (strtolower($f_Ext) == "sql" || strtolower($f_Ext) == "gz") {
                                    $strSelected = "";
                                    if ($f_Name == $strRemoteBackupFile) {
                                        $strSelected = "selected ";
                                    } ?>
                                    <option <?= $strSelected ?>value="<?= $f_Name ?>"><?= $f_Name ?></option>
                        <?php
                                }
                            }
                        } ?>
                    </select>
                </div>
                <div>
                    <input class="button" type="submit" name="Restore" id="Restore" value="Restore" />
                </div>
            </fieldset>
        </form>
        <?php echo $message; ?>
        <div id="WaitingImage" style="display: none; padding: 20px 0; text-align: center"><img style="margin: 5px auto;" src="../images/wait.gif"></div>

    </section>
    <script>
        document.getElementById("Restore").addEventListener("click", function() {
            var targetElement = document.getElementById("WaitingImage");
            targetElement.style.display = "block";
        });
    </script>
    <section>
        <form method="POST" name="RestorDatabase" action="restore.php" enctype="multipart/form-data">
            <table>
                <?php
                $arrFiles = get_folder_files($str_RemoteBackupDirectory);
                if (is_array($arrFiles)) {
                    $iFiles = count($arrFiles);
                    for ($f = 0; $f < $iFiles; $f++) {
                        $f_Name = $arrFiles[$f];
                        $f_Ext = strtolower(pathinfo($f_Name)["extension"]);
                        if (strtolower($f_Ext) === "sql" || strtolower($f_Ext) === "gz") {
                            $strSelected = "";
                            if ($f_Name == $strRemoteBackupFile) {
                                $strSelected = "selected ";
                            } ?>
                            <tr>
                                <td><input type="checkbox" name="box_<?= $f ?>" value="ON"></td>
                                <td><input type="hidden" name="file_<?= $f ?>" value="<?= $f_Name ?>"><?= $f_Name ?></td>
                                <td class="alignRight"><?= number_format(filesize($str_RemoteBackupDirectory . DIRECTORY_SEPARATOR . $f_Name), 0, ",", " ") . " kb" ?></td>
                            </tr>

                <?php
                        }
                    }
                } ?>
            </table>
        </form>
    </section>


</body>

</html>