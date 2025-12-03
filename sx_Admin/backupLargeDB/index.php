<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";
include __DIR__ . "/functions.php";

$int_MaxPacket = $conn->query('SELECT @@global.max_allowed_packet')->fetchColumn();
$int_MaxPacket = number_format(($int_MaxPacket / 1024), 0, ',', ' ');
define("int_MaxPacket", $int_MaxPacket);

$arrTables = sx_getMySQLTables($conn);
$arrSelectedTables = array();
$msg = "";
$str_DatabaseName = "";
$intMaxRows = 1000;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (is_array($arrTables)) {
        $iRows = count($arrTables);
        for ($t = 0; $t < $iRows; $t++) {
            if (isset($_POST["table_" . $t]) && $_POST["table_" . $t] == "Yes") {
                $arrSelectedTables[] = $arrTables[$t];
            }
        }
    }

    $radioCompressToGZIP = false;
    $nameSuffix = "_SQL";
    if (isset($_POST['CompressToGZIP']) && $_POST['CompressToGZIP'] == 'Yes') {
        $radioCompressToGZIP = true;
        $nameSuffix = "_GZIP";
    }

    $strDatabaseName = sx_TABLE_SCHEMA;
    if (!empty($_POST['NewDatabaseName'])) {
        $str_DatabaseName = $_POST['NewDatabaseName'];
        $strDatabaseName = $str_DatabaseName;
    }

    $postBackupFolderName = trim($_POST["BackupFolderName"]);
    $strBackupFolderName = $strDatabaseName;
    if (empty($postBackupFolderName)) {
        if (!empty($arrSelectedTables) && count($arrSelectedTables) < $iRows) {
            if (count($arrSelectedTables) > 1) {
                $strBackupFolderName .= "_" . $arrSelectedTables[0]  . "_" . count($arrSelectedTables) . "_tables";
            } else {
                $strBackupFolderName .= "_" . $arrSelectedTables[0];
            }
        }
    } else {
        $strBackupFolderName .=  "_" . str_replace(" ", "_", $postBackupFolderName);
    }

    if (!empty($_POST['MaxRows']) && is_numeric($_POST['MaxRows'])) {
        $intMaxRows = (int) $_POST['MaxRows'];
    }

    if (!empty($arrSelectedTables)) {
        require_once __DIR__ . '/db_class.php';
        set_time_limit(900);
        ini_set('memory_limit', '-1');

        $backup = new ps_Export_DB($str_RemoteBackupDirectory, $strBackupFolderName, $nameSuffix, $strDatabaseName, $intMaxRows);
        $result = $backup->backupTables($arrSelectedTables, $radioCompressToGZIP);
        if ($result) {
            $msg = "<b>Script executed OK</b><br>";
            $msg .= $backup->output;
        }
        if (isset($_POST['DownloadBackupFolder']) && $_POST['DownloadBackupFolder'] == 'Yes') {
            sx_downloadBackupFolder($backup->backup_path);
        }
    } else {
        $msg = "You must select one or more Tables to backup!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere - Backup Large Database</title>
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
        });
    </script>
</head>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Backup Large Database to a Folder with a SQL or GZIP File for every Table</h2>
        <div>
            <a class="button" href="restore.php">Restore Database</a>
        </div>
    </header>

    <section>
        <h1>Select Tables to Backup</h1>
        <?php if (!empty($msg)) { ?>
            <div class="msgInfo"><?= $msg ?></div>
        <?php } ?>
        <form method="POST" name="chooseTable" action="index.php">
            <fieldset class="container">
                <label>Backup Folder Name:<br>
                    <input type="text" name="BackupFolderName" value="" placeholder="Default: Database Name + Current Datae_Time" size="30" />
                </label>
                <label>Max Rows for Backup Files:<br>
                    <input type="text" name="MaxRows" value="<?= $intMaxRows ?>" size="12" />
                </label>
                <label>New Database Name:<br>
                    <input type="text" name="NewDatabaseName" value="<?= $str_DatabaseName ?>" placeholder="Defaul Database Name" size="17" />
                </label>
                <label>Compress Files with gzip:
                    <input type="checkbox" name="CompressToGZIP" value="Yes" checked />
                </label>
                <label>Download the Backup Folder:
                    <input type="checkbox" name="DownloadBackupFolder" value="Yes" />
                </label>
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
                            if (@$_POST["table_" . $t] == "Yes") {
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
                    <input class="button" name="Submit1" type="submit" value="Submit">
                </div>
            </div>
            <hr>

        </form>
    </section>

    <section class="container">
        <div>
            <h3>Select Tables</h3>
            <ul>
                <li>You can back up a <b>Sole</b> Table, any <b>Set</b> of Tables or the <b>Entire</b> Database.</li>
                <li>If you select a Sole Table or a Set of Tables, please also include all other tables that might be <b>related</b> to the selected ones.</li>
                <li>Usually, although not necessary and not exclusively, you might include all tables <b>within a successive color group</b>.</li>
            </ul>
            <h3>The Name of the Backup Folder</h3>
            <ul>
                <li>The default name of the backup Folder is the <b>Database Name</b> followed by a <b>suffix</b> with the current <b>Data and Time</b>.</li>

                <li>If you select only a set of tables, the first table name and the number of selected tables will be automatically added to the Database Name, together with the above suffix
                    (<b>database_name_text_authors_7_tables_2021-11-14_17-21-15.sql</b>).</li>
                <li>However, You can set whatever name you like. The Suffix will be added automatically. </li>
            </ul>
        </div>
        <div>
            <h3>The Content of the Backup Folder</h3>
            <ul>
                <li>Every Table is backed up in a <b>Separate SQL File</b>, with the Table Name as the Backup File Name. If files are <b>compressed by GZIP</b>, the extension .gz will be added to the extension .sql.</li>
                <li><b>Every Backup File</b> contains the following SQL statements:
                    <ul>
                        <li>USE `database_name`;.</li>
                        <li>SET foreign_key_checks = 0</li>
                        <li>DROP TABLE IF EXISTS `table_name`;</li>
                        <li>CREATE TABLE `table_name`</li>
                        <li>INSERT INTO `table_name` VALUES </li>
                    </ul>
                </li>
                <li><b>The First Backup File</b> also includes the statement:
                    <ul>
                        <li>CREATE DATABASE IF NOT EXISTS `database_name`</li>
                    </ul>
                </li>
                <li>The Database Name is the <b>Default Database Name</b>. I f you want to copy the database with another name, write the <b>New Database Name</b> in the corresponding input.</li>
            </ul>
            <h3>Define Max Rows for Backup Files</h3>
            <ul>
                <li>Backup files for large tables, with thousand of rows including more or less lengthy texts, might exceed the <b>Max Allowed Packet</b> of the MySQL Server, which is currently: <b><?= int_MaxPacket ?> KB</b>.</li>
                <li>Define the <b>Max Rows</b> for Backup Files to <b>split</b> tables into multiple Backup Files. None of these files should exceed the Max Allowed Packet size.</li>
            </ul>
        </div>
    </section>
</body>

</html>