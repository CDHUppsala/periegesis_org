<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/config.php";
include __DIR__ . "/functions_backup.php";

//Number of rows to bach before appending to the file
$intBatch = 100;
$radio_Download_zip = false;
$zip_BackupFileName = '';

$arrTables = sx_getMySQLTables($conn);
$arrSelectedTables = [];
$arrSuccess = [];
$arrFailure = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /*
    $radioOnlyTableStructures = false;
    if (isset($_POST["BackuOnlyStructures"]) && $_POST["BackuOnlyStructures"] == "Yes") {
        $radioOnlyTableStructures = true;
    }
*/
    if (is_array($arrTables)) {
        $iRows = count($arrTables);
        for ($t = 0; $t < $iRows; $t++) {
            if (isset($_POST["table_" . $t]) && $_POST["table_" . $t] == "Yes") {
                $arrSelectedTables[] = $arrTables[$t];
            }
        }
    }

    if (is_array($arrSelectedTables) && !empty($arrSelectedTables)) {

        $strFileName = !empty($_POST["FileName"]) ? trim($_POST["FileName"]) : '';
        $strSufix = !empty($_POST["Sufix"]) ? trim($_POST["Sufix"]) : date('Y-m-d') . '_' . date('H-i-s');

        $strTableSchema = sx_TABLE_SCHEMA;
        if (!empty(trim($_POST['NewDatabaseName']))) {
            $strTableSchema = trim($_POST['NewDatabaseName']);
        }
        // Content, structure or Both
        $strBackupMode = $_POST['BackupMode'];
        if (empty($strBackupMode)) {
            $strBackupMode = 'Contents';
        }

        // Tables in a Single File or as Separate files in a folder
        $radioBackupInFolder  = false;
        if (isset($_POST['BackupOptions']) && $_POST['BackupOptions'] === 'SeparateFilesInFolder') {
            $radioBackupInFolder  = true;
        }

        if (isset($_POST['DownloadBackupFile']) && $_POST['DownloadBackupFile'] === 'Yes') {
            $radio_Download_zip = true;
        }

        // Compress in ZIP and download, single file or folder
        $radioGZIP = false;
        if (isset($_POST['CompressByGZIP']) && $_POST['CompressByGZIP'] === 'Yes') {
            $radioGZIP = true;
        }

        // Option 1: Single Backup File: Name and physical Path
        $str_FileName = "{$strFileName}_{$strBackupMode}_{$strSufix}.sql";
        if (empty($strFileName)) {
            $str_FileName = $strTableSchema;
            if (count($arrSelectedTables) != $iRows) {
                if (count($arrSelectedTables) > 1) {
                    $str_FileName .= "_" . $strBackupMode . "_" . $arrSelectedTables[0] . "_" . count($arrSelectedTables) . "_tables_" . $strSufix . ".sql";
                } else {
                    $str_FileName .= "_" . $strBackupMode . "_" . $arrSelectedTables[0] . "_" . $strSufix . ".sql";
                }
            } else {
                $str_FileName .= "_{$strBackupMode}_{$strSufix}.sql";
            }
        }
        $str_FilePath = "{$str_RemoteBackupDirectory}\\{$str_FileName}";

        // Option 2: Backup Folder: Name and physical Path 
        $str_FolderName = "{$strTableSchema}_{$strSufix}";
        $str_FolderPath = "{$str_RemoteBackupDirectory}\\{$str_FolderName}";

        if ($radioBackupInFolder) {
            if (!file_exists($str_FolderPath)) {
                mkdir($str_FolderPath, 0777, true);
            }
            $radioBackup = sx_backup_database_in_folder($str_FolderPath, $arrSelectedTables, $strBackupMode, $strTableSchema, $radioGZIP, $intBatch);
        } else {
            $radioBackup = sx_backup_database_in_file($str_FilePath, $arrSelectedTables, $strBackupMode, $strTableSchema, $radioGZIP, $intBatch);
        }

        if ($radioBackup) {
            if ($radioBackupInFolder) {
                if ($radioGZIP) {
                    $arrSuccess[] = 'A Backup Folder has been created with all selected tables in separate files of GZIP Format.';
                } else {
                    $arrSuccess[] = 'A Backup Folder has been created with all selected tables in separate files of SQL Format.';
                    if ($radio_Download_zip) {
                        $strReturnValue = sx_zip_folder_files($str_FolderPath, "{$str_FolderName}");
                        if ($strReturnValue === 'Success') {
                            $zip_BackupFileName = "{$str_FolderName}.zip";
                            $arrSuccess[] = 'The above Folder has been Compressed and is ready for download in ZIP Format.';
                        } else {
                            $radio_Download_zip = true;
                            $arrFailure[] = "Error creating ZIP File: {$strReturnValue}.";
                        }
                    }
                }
            } else {
                if ($radioGZIP) {
                    $arrSuccess[] = 'A Backup File has been succesivelly created in GZIP Format.';
                } else {
                    $arrSuccess[] = 'A Backup File has been succesivelly created in SQL Format.';
                    if ($radio_Download_zip) {
                        $strReturnValue = zip_single_file($str_FilePath);
                        if ($strReturnValue === 'Success') {
                            $zip_BackupFileName = "{$str_FileName}.zip";
                            $arrSuccess[] = 'The SQL Backup File is Copressed and ready for download in ZIP Format.';
                        } else {
                            $radio_Download_zip = true;
                            $arrFailure[] = "Error creating ZIP File: {$strReturnValue}.";
                        }
                    }
                }
            }
        } else {
            $arrFailure[] = "Error: The Backup of Database Tables could not be created!";
        }
    } else {
        $arrFailure[] = "You must select at least one Table!";
    }
    // Remove or comment all session rows and header(...) to reuse the form for new submitions
    $_SESSION['SuccessBU'] = $arrSuccess;
    $_SESSION['FalureBU'] = $arrFailure;

    $_SESSION['DownloadZip'] = $radio_Download_zip;
    $_SESSION['ZipFileName'] = $zip_BackupFileName;

    header("Location: " . $_SERVER['PHP_SELF']);  // Refresh the page
    exit;
}

// Remove or comment all session rows and header(...) to reuse the form for new submitions
$arrFailure = isset($_SESSION['FalureBU']) ? $_SESSION['FalureBU'] : [];
$arrSuccess = isset($_SESSION['SuccessBU']) ? $_SESSION['SuccessBU'] : [];

$radio_Download_zip = isset($_SESSION['DownloadZip']) ? $_SESSION['DownloadZip'] : false;
$zip_BackupFileName = isset($_SESSION['ZipFileName']) ? $_SESSION['ZipFileName'] : '';

unset($_SESSION['SuccessBU']);
unset($_SESSION['FalureBU']);

unset($_SESSION['DownloadZip']);
unset($_SESSION['ZipFileName']);

?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere - Backup Small Database</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <link rel="stylesheet" href="ps.css">

    <script src="../js/jq/jquery.min.js"></script>
    <script>
        var $sx = jQuery.noConflict();
        $sx(document).ready(function() {
            $sx("#selectall").click(function() {
                $sx("#jgTables [type='checkbox']").prop("checked", "checked");
            });
            $sx("#deselectall").click(function() {
                $sx("#jgTables [type='checkbox']").prop("checked", "");
            });
            $sx("#interval").click(function() {
                var iFrom = parseInt($sx("#From").val(), 10);
                var iTo = parseInt($sx("#To").val(), 10) + 1;
                var i;
                for (i = iFrom; i < (iTo); i++) {
                    $sx("#table_" + i).prop("checked", "checked");
                };
            });
            var iTables = <?= count($arrTables) ?>;
            $sx("#checkedInterval").click(function() {
                var i, c_From;
                for (i = 0; i < (iTables); i++) {
                    if ($sx("#table_" + i).is(":checked")) {
                        c_From = i + 1;
                        break;
                    };
                };
                for (i = c_From; i < (iTables); i++) {
                    if (!$sx("#table_" + i).is(":checked")) {
                        $sx("#table_" + i).prop("checked", "checked");
                    } else {
                        break;
                    }
                };
            });
            $sx(".tipsBG").on("mouseenter", function() {
                $sx(this).find(".tips").delay(50).show(300).stop();
            }).on("mouseleave", function() {
                $sx(this).find(".tips").delay(50).hide(300).stop();
            })

            $sx(".jq_toggleNext").on('click', function() {
                $sx(this).toggleClass("selected").next().slideToggle('fast');
            });
        });
    </script>
</head>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Backup Small Database to a single SQL or GZIP File</h2>
        <div>
            <a class="button" href="restore.php?clear=yes">Restore Database</a>
        </div>
        <div>
            <a class="button" href="delete_backups.php?clear=yes">Delete Backups</a>
        </div>
    </header>
    <section style="position: relative">
        <h1>Select Tables to Backup</h1>
        <form method="POST" name="chooseTable" action="index.php">
            <div class="row" style="position: absolute; top: 0; right: 4rem">
                <div><b>Backup Options:</b></div>
                <div>
                <input type="radio" name="BackupOptions" value="SingleFile" checked>
                All Selected Tables in a <b>Single</b> Backup File<br>
                <input type="radio" name="BackupOptions" value="SeparateFilesInFolder">
                <b>Folder</b> with all Selected Tables in <b>Separate</b> Backup Files
                </div>
            </div>

            <fieldset class="container">
                <label>Backup File/Folder Name:<br><input type="text" name="FileName" value="" placeholder="Default: Database Name + Suffix" size="25" /></label>
                <label>Sufix: Current Data_Time<br><input type="text" name="Sufix" value="" placeholder="<?= date('Y-m-d') . '_' . date('H-i-s') ?>" title="Current Date and Time" size="16" /></label>
                <div class="text_small">
                    <label>Backup Only Contents: <input type="radio" name="BackupMode" value="Contents" checked /></label><br>
                    <label>Backup Both Structures and Contents: <input type="radio" name="BackupMode" value="Both" /></label><br>
                    <label>Backup Only Table Structures: <input type="radio" name="BackupMode" value="Structures" /></label>
                </div>
                <label>New Database Name:<br>
                    <input type="text" name="NewDatabaseName" value="" placeholder="Default: Current Name" size="16" /></label>

                <div>
                    <label>Compress Backup by GZIP:
                        <input type="checkbox" name="CompressByGZIP" id="CompressByGZIP" value="Yes" />
                    </label><br>
                    <label>Download Backup (in ZIP):
                        <input type="checkbox" name="DownloadBackupFile" id="DownloadBackupFile" value="Yes" />
                    </label>
                </div>
            </fieldset>
            <div id="jgTables" class="container">
                <div>
                    <?php
                    /**
                     * Creat Checkboxes for every table in Access Database
                     */
                    $iRows = 0;
                    $sLastPrefix = "";
                    $strBG = "";
                    if (is_array($arrTables)) {
                        $iRows = count($arrTables);
                        $iDiv = intval($iRows / 5) + 1;
                        $intLoop = 0;
                        for ($t = 0; $t < $iRows; $t++) {
                            $p = 1;
                            if ($t == ($iRows - 1)) {
                                $p = 0;
                            }
                            if ($intLoop >= $iDiv) {
                                echo "</div><div>";
                                $intLoop = 0;
                            }
                            $sTable = trim($arrTables[$t]);
                            $sNextTable = trim($arrTables[$t + $p]);
                            $sPrefix = substr($sTable, 0, 4);
                            if ($sPrefix == substr($sNextTable, 0, 4)) {
                                if ($sPrefix != $sLastPrefix) {
                                    if (strpos($strBG, '_green') > 0) {
                                        $strBG = ' class="background_red"';
                                    } elseif (strpos($strBG, '_red') > 0) {
                                        $strBG = ' class="background_green"';
                                    } else {
                                        $strBG = ' class="background_green"';
                                    }
                                }
                            } else {
                                if ($sPrefix != $sLastPrefix) {
                                    $strBG = "";
                                }
                            }
                            $str_Cecked = "";
                            if (isset($_POST["table_{$t}"]) && $_POST["table_{$t}"] == "Yes") {
                                $str_Cecked = "checked ";
                            } ?>
                            <input <?= $str_Cecked ?>type="checkbox" id="table_<?= $t ?>" name="table_<?= $t ?>" value="Yes">
                            <span<?= $strBG ?>><?= $t . " " . $sTable ?></span><br>
                        <?php
                            $intLoop = $intLoop + 1;
                            $sLastPrefix = $sPrefix;
                        }
                    }
                        ?>
                </div>
            </div>
            <hr>
            <div class="container">
                <div class="flex_auto" style="text-align: center;">
                    <button class="button" type='button' id='selectall'>Select All</button>
                    <button class="button" type='button' id='deselectall'>Clear All</button>
                </div>
                <div class="flex_auto" style="text-align: center;">
                    <button class="button" type="button" id="checkedInterval">Select Beteen Checked Intervals</button>
                </div>
                <div class="flex_auto" style="text-align: center;">
                    From Table: <select id="From" name="From">
                        <?php for ($t = 0; $t <= $iRows; $t++) { ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php } ?>
                    </select>
                    To Table: <select id="To" name="To">
                        <?php for ($t = 0; $t <= $iRows; $t++) { ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php } ?>
                    </select>
                    <button class="button" type="button" id="interval">Select Interval</button>
                </div>
                <div class="flex_auto" style="text-align: center;">
                    <input class="button" name="Submit1" id="SubmitBackup" type="submit" value="Submit">
                </div>
            </div>
            <hr>
        </form>
        <div id="WaitingImage" style="display: none; padding: 10px 0; text-align: center"><img src="../images/wait.gif"></div>
        <?php
        if (!empty($arrFailure)) { ?>
            <div class="msgError"><?= implode('<br>', $arrFailure) ?></div>
        <?php
        }
        if (!empty($arrSuccess)) { ?>
            <div class="msgSuccess"><?= implode('<br>', $arrSuccess) ?></div>
        <?php
        } ?>

    </section>
    <script>
        document.getElementById("SubmitBackup").addEventListener("click", function() {
            var targetElement = document.getElementById("WaitingImage");
            targetElement.style.display = "block";
        });
        document.addEventListener("DOMContentLoaded", function() {
            var mainCheckbox = document.getElementById("CompressByGZIP");
            var otherCheckbox = document.getElementById("DownloadBackupFile");

            mainCheckbox.addEventListener("change", function() {
                otherCheckbox.disabled = mainCheckbox.checked;
            });

            otherCheckbox.addEventListener("change", function() {
                mainCheckbox.disabled = otherCheckbox.checked;

            });
        });
    </script>
    <?php
    include __DIR__ . "/info.html";

    if ($radio_Download_zip) { ?>
        <script>
            const downloadFile = "<?php echo $zip_BackupFileName; ?>";
            if (downloadFile) {
                window.location.href = "download.php?file=" + encodeURIComponent(downloadFile);
            }
        </script>
    <?php
    } ?>
</body>

</html>