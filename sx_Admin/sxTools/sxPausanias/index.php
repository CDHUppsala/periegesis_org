<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/functions_tables.php";

@set_time_limit(600);
@ini_set('memory_limit', '256M');

// path for saving temporally local files 
define("SX_TempUploadPath", PROJECT_PRIVATE . "/temp_uploads/");

/**
 * Path to remote server folder that includes files to be imported
 * The default folder is import_export_files.
 * Change it by a COSTANT, if neccessar, for specific sites 
 */
if (defined('SX_PrivateInportExportFilesFolder') && !empty(SX_PrivateInportExportFilesFolder)) {
    define("PATH_ToImportFolder", PROJECT_PRIVATE . "/" . SX_PrivateInportExportFilesFolder . "/");
} else {
    define("PATH_ToImportFolder", PROJECT_PRIVATE . "/import_export_files/");
} ?>

<!DOCTYPE html>
<html>

<head>
    <title>Import Array CSV, XML and JSON Data into MySQL Tables</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <?php
    echo '<style>';
    include __DIR__ . '/array_to_table.css';
    echo '</style>';
    ?>
    <script src="../../js/jq/jquery.min.js"></script>
    <script>
        jQuery(function($) {
            $(".jq_toggleNext").on('click', function() {
                $(this).toggleClass("selected").next().slideToggle('fast');
            });

        });
    </script>
</head>

<?php

$valid_extension = array('xml', 'json', 'csv');

// For errors in this page
$outputErr = '';
$str_DBTableName = "";
$import_FileName = "";
$base_FileName = "";
$import_Type = "";
$file_extension = "";
$radioLocalFile = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST["DatabaseTable"]) && !empty($_POST["DatabaseTable"])) {
        $str_DBTableName = $_POST["DatabaseTable"];
    } else {
        $outputErr .= '<p>Please select a Destination Table</p>';
    }

    /**
     * Get the extention of the imported source file and check its validity
     * Check the type of import: Update or Insert
     */

    if ($str_DBTableName) {
        if (isset($_FILES['LocalFile']) &&  $_FILES['LocalFile']['error'] === UPLOAD_ERR_OK) {
            $radioLocalFile = true;
            $temp_Path = $_FILES['LocalFile']['tmp_name'];
            $LocalFileName = basename($_FILES['LocalFile']['name']);
            $temp_UploadedLocalFile = SX_TempUploadPath . $LocalFileName;
            if (move_uploaded_file($temp_Path, $temp_UploadedLocalFile)) {
                $_SESSION['TempPathToLocalFile'] = $temp_UploadedLocalFile;
                $import_FileName = $temp_UploadedLocalFile;
            }
        } elseif (isset($_POST["ServerFile"]) && !empty($_POST["ServerFile"])) {
            $import_FileName = PATH_ToImportFolder . $_POST["ServerFile"];
            if (isset($_SESSION['TempPathToLocalFile'])) {
                unlink($_SESSION['TempPathToLocalFile']);
                unset($_SESSION['TempPathToLocalFile']);
            }
        } elseif (isset($_SESSION['TempPathToLocalFile']) && !empty($_SESSION['TempPathToLocalFile'])) {
            $radioLocalFile = true;
            $import_FileName = $_SESSION['TempPathToLocalFile'];
            $LocalFileName = basename($import_FileName);
        } else {
            $outputErr .= '<p>Please select a Source File to Import</p>';
        }

        if (!empty($import_FileName)) {
            $file_extension = strtolower(pathinfo($import_FileName, PATHINFO_EXTENSION));
            if (!in_array($file_extension, $valid_extension)) {
                $outputErr = '<p>Invalid File Extension</p>';
            }
            if (!$radioLocalFile) {
                $base_FileName = basename($import_FileName);
            }
        }

        if (isset($_POST["ImportType"])) {
            $import_Type = $_POST["ImportType"];
        } else {
            $outputErr .= '<p>Please select the Import Type</p>';
        }
    }

    $arr_FieldNames = [];
    $arr_FieldTypes = [];
    $arr_FieldNameType = [];
    $str_PrimaryKeyName = "";
    $radioTableIsSet = false;

    /**
     * The variable $arr_FieldTypes is Not Used yet
     * Can be used in the future for checking field values to be imported.
     * Get table columns to check compatibility to file columns
     * Get Primary Key to exclude in Inserting and use in Updating
     */

    if (isset($str_DBTableName) && !empty($str_DBTableName)) {
        $arr_PrimaryKeyName = sx_GetPrimaryKey($str_DBTableName);
        $str_PrimaryKeyName = $arr_PrimaryKeyName[0];
        $str_PrimaryKeyType = $arr_PrimaryKeyName[1];
        $radio_PKisAutoIncrement = $arr_PrimaryKeyName[2];
        $strSQL = "SELECT * FROM $str_DBTableName LIMIT 1";
        $stmt = $conn->query($strSQL);
        if ($stmt) {
            $radioTableIsSet = true;
            $iCountCol = $stmt->columnCount();
            for ($c = 0; $c < $iCountCol; $c++) {
                $meta = $stmt->getColumnMeta($c);
                $arr_FieldNames[] = $meta['name'];
                $arr_FieldTypes[] = $meta['native_type'];
                $arr_FieldNameType[$meta['name']] = $meta['native_type'];
            }
        }
        $stmt = null;
    }
}


// Show only Used Table
$arrNonUsedTables = "";
$strDataMarkNotes = return_NonUsedTables();
if (!empty($strDataMarkNotes)) {
    $arrNonUsedTables = json_decode($strDataMarkNotes, true);
}
?>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Import to Database Tables</h2>
        <form action="load_external_files.php" method="post">
            <button id="LoadExternalFiles" class="button" type="submit">Load All Annotation Files</button>
        </form>
    </header>
    <h2>Import Array Data from XML, JSON and CSV Files to Database Tables</h2>
    <div class="maxWidthWide">

        <form method="POST" name="importForm" action="index.php" enctype="multipart/form-data">
            <fieldset class="row">
                <div><label>Select Database Table:</label><br>
                    <select size="1" name="DatabaseTable" id="DatabaseTable">
                        <option value="">Select Table</option>
                        <?php
                        $stmt = $conn->prepare("
                        SELECT table_name
                        FROM information_schema.tables
                        WHERE table_schema = ?
                        AND table_type = 'BASE TABLE'
                    ");

                        $stmt->execute([$conn->query('select database()')->fetchColumn()]);

                        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                        foreach ($tables as $table) {
                            if (empty($arrNonUsedTables) || !in_array($table, $arrNonUsedTables)) {
                                $strSelected = "";
                                if ($table == $str_DBTableName) {
                                    $strSelected = "selected ";
                                } ?>
                                <option <?= $strSelected ?>value="<?= $table ?>"><?= $table ?></option>
                        <?php }
                        }
                        $rs = null; ?>
                    </select>
                </div>

                <?php
                $arrRemoteFiles = sx_getFolderContents(PATH_ToImportFolder, "is_file");
                if (is_array($arrRemoteFiles)) { ?>
                    <div><label>Select Remote Server File:</label><br>
                        <select id="ServerFile" name="ServerFile">
                            <option VALUE="">Select Remote File</option>
                            <?php
                            foreach ($arrRemoteFiles as $entry) {
                                $strSelected = "";
                                if (!empty($base_FileName)) {
                                    if ($entry == $base_FileName) {
                                        $strSelected = "selected";
                                    }
                                } ?>
                                <option VALUE="<?= $entry ?>" <?= $strSelected ?>><?= $entry ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                <?php
                } ?>
                <div>
                    <label>Select Local File
                        <?php
                        if (!empty($LocalFileName)) {
                            echo "<span>({$LocalFileName})</span>";
                        } ?>
                    </label><br>
                    <input type="file" name="LocalFile" id="LocalFile" />
                </div>
            </fieldset>
            <?php
            $updateChecked = ' checked';
            $insertChecked = '';
            $truncateChecked = '';
            if (!empty($import_Type)) {
                if ($import_Type === 'Insert') {
                    $insertChecked = ' checked';
                    $updateChecked = '';
                } elseif (($import_Type === 'Truncate')) {
                    $truncateChecked = ' checked';
                    $updateChecked = '';
                }
            } ?>
            <fieldset class="row" style="justify-content: space-between; align-items: stretch">
                <div><label>Import Type: </label>
                    <span><input type="radio" name="ImportType" value="Update" <?php echo $updateChecked ?>> Update </span>
                    <span><input type="radio" name="ImportType" value="Insert" <?php echo $insertChecked ?>> Insert</span>
                    <span><input id="TruncateToInsert" type="radio" name="ImportType" value="Truncate" <?php echo $truncateChecked ?>> Truncate and Insert</span>
                </div>
                <div><input title="Check the file before import" type="submit" name="submit" id="SubmitImport" value="Import" disabled /></div>
                <div><input type="submit" name="check" id="SubmitCheck" value="Check" /></div>
            </fieldset>
        </form>
        <?php
        // Verify if changes in memory and time were accepted
        $verifyMemory = '';
        $verifyTime = '';
        if (ini_get('memory_limit') < 512 * 1024 * 1024) {
            $verifyMemory = " (<b>Lower</b> Server Memory!)";
        } 
        if (ini_get('max_execution_time') < 600) {
            $verifyTime = " (<b>Lower</b> Server Execution Time!)";
        }
        ?>
        <div>
            Server <b>Memory</b>: <?php echo ini_get('memory_limit') . $verifyMemory ?>.
            Server <b>Execution</b> Time: <?php echo ini_get('max_execution_time') . $verifyTime ?>.
            <b>Max Size</b> for CSV, XML and JSON Files: <?php echo number_format((int)(ini_get('memory_limit')) / 10,1); ?>M.
        </div>

        <div id="WaitingImage" style="display: none; padding: 20px 0; text-align: center"><img src="../../images/wait.gif"></div>

        <script>
            document.getElementById("SubmitImport").addEventListener("click", function() {
                var targetElement = document.getElementById("WaitingImage");
                targetElement.style.display = "block";
            });
            document.getElementById("LoadExternalFiles").addEventListener("click", function() {
                var targetElement = document.getElementById("WaitingImage");
                targetElement.style.display = "block";
            });
            document.getElementById("TruncateToInsert").addEventListener("click", function() {
                var message = "Are Yo Sure?\nYou are going to truncate a Table!\n" +
                    "To avod errors in the Site, set it first in Update Mode.\n" +
                    " - From the Group Initial Settings, click on Change Site Mode.";
                alert(message);
            });
            // Disable the submit import button when a new file is selected
            document.getElementById('ServerFile').addEventListener('change', function() {
                document.getElementById('SubmitImport').disabled = true;
            });
            document.getElementById('LocalFile').addEventListener('change', function() {
                document.getElementById('SubmitImport').disabled = true;
            });
            document.getElementById('DatabaseTable').addEventListener('change', function() {
                document.getElementById('SubmitImport').disabled = true;
            });
        </script>

    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $radioImportSubmited = false;
        if (!empty($outputErr)) {
            echo '<div class="msgError">' . $outputErr . '</div>';
        } else {
            if (!empty($_POST['submit']) && $_POST['submit'] === 'Import') {
                $radioImportSubmited = true;
            }
            include __DIR__ . "/create_table.php";
            if ($radioImportSubmited) {
                include __DIR__ . "/import.php";
            }
        }
    }

    // Delete temporally saved local file, if any
    if (!empty($_POST['submit']) && $_POST['submit'] === 'Import') {
        if (isset($_SESSION['TempPathToLocalFile'])) {
            unlink($_SESSION['TempPathToLocalFile']);
            unset($_SESSION['TempPathToLocalFile']);
        }
    }
    include __DIR__ . '/info.php'
    ?>

</body>

</html>