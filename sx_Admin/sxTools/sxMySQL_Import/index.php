<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/functions.php";

@set_time_limit(600);
@ini_set('memory_limit', '256M');

if (defined('SX_PrivateInportExportFilesFolder') && !empty(SX_PrivateInportExportFilesFolder)) {
    Define("PATH_ToImportFolder", PROJECT_PRIVATE . "/" . SX_PrivateInportExportFilesFolder . "/");
} else {
    Define("PATH_ToImportFolder", PROJECT_PRIVATE . "/import_export_files/");
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Import CSV, XML and JSON Data into MySQL Tables</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
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

$radio_ImportSubmited = false;

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
            $import_FileName = $_FILES['LocalFile']['name'];
        } elseif (isset($_POST["ServerFile"]) && !empty($_POST["ServerFile"])) {
            $import_FileName = PATH_ToImportFolder . $_POST["ServerFile"];
        } else {
            $outputErr .= '<p>Please select a Source File to Import</p>';
        }

        if (!empty($import_FileName)) {
            $file_data = explode('.', $import_FileName);
            $file_extension = strtolower(end($file_data));
            if (in_array($file_extension, $valid_extension) === false) {
                $outputErr = '<p>Invalid File Extension</p>';
            }
            $arrTemp = explode('/', $import_FileName);
            $base_FileName = end($arrTemp);
        }

        if (isset($_POST["ImportType"])) {
            $import_Type = $_POST["ImportType"];
        } else {
            $outputErr .= '<p>Please select the Import Type</p>';
        }
        /**
         * If local file, use the temporal name which includes the path to windows temporal files
         * But keep the file name for information purposes
         */
        $LocalFileName = '';
        if ($radioLocalFile) {
            $LocalFileName = $import_FileName;
            $import_FileName = $_FILES['LocalFile']['tmp_name'];
        }
    }

    if (!empty($_POST['submit']) && $_POST['submit'] === 'Import') {
        $radio_ImportSubmited = true;
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
    </header>
    <h2>Import Data from XML, JSON and CSV Files to Databse Tables</h2>
    <div class="maxWidthWide">

        <form method="POST" name="importForm" action="index.php" enctype="multipart/form-data">
            <fieldset class="row">
                <div><label>Select Database Table:</label><br>
                    <select size="1" name="DatabaseTable">
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
                $strXMLFiles = sx_getFolderContents(PATH_ToImportFolder, "is_file");
                if (is_array($strXMLFiles)) {  ?>
                    <div><label>Select Remote Server File:</label><br>
                        <select id="ServerFile" name="ServerFile">
                            <option VALUE="">Select Remote File</option>
                            <?php
                            $iCount = count($strXMLFiles);
                            for ($i = 0; $i < $iCount; $i++) {
                                $loopFile = $strXMLFiles[$i];
                                $strSelected = "";
                                if ($radioLocalFile === false && !empty($base_FileName)) {
                                    if ($loopFile == $base_FileName) {
                                        $strSelected = "selected";
                                    }
                                } ?>
                                <option VALUE="<?= $loopFile ?>" <?= $strSelected ?>><?= $loopFile ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                <?php
                } ?>
                <div><label>Select Local File</label><br>
                    <input type="file" name="LocalFile" id="LocalFile" />
                    <?php
                    if (!empty($LocalFileName)) {
                        echo '<br>';
                        echo "<span class=\"text_xsmall\">File: $LocalFileName</span>";
                    } ?>
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
            <fieldset class="row" style="align-items: center">
                <div><label>Import Type: </label>
                    <span><input type="radio" name="ImportType" value="Update" id="jq_Update" <?php echo $updateChecked ?>> Update </span>
                    <span><input type="radio" name="ImportType" value="Insert" <?php echo $insertChecked ?>> Insert</span>
                    <span><input id="TruncateToInsert" type="radio" name="ImportType" value="Truncate" <?php echo $truncateChecked ?>> Truncate and Insert</span>
                </div>
                <div><input type="submit" name="submit" id="SubmitImport" value="Import" title="Check the file before import" /></div>
                <div><input type="submit" name="check" id="SubmitCheck" value="Check First" /></div>
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
            <b>Max Size</b> for JSON Files: <?php echo (int)(ini_get('memory_limit')) / 10; ?>M.
            Potentially <b>No Limits</b> for CSV and XML Files.
        </div>

        <div id="WaitingImage" style="display: none; padding: 20px 0; text-align: center"><img src="../../images/wait.gif"></div>

        <script>
            document.getElementById("SubmitImport").addEventListener("click", function() {
                var targetElement = document.getElementById("WaitingImage");
                targetElement.style.display = "block";
            });
            document.getElementById("TruncateToInsert").addEventListener("click", function() {
                var message = "Are Yo Sure?\nYou are going to truncate a Table!\n" +
                    "To avod errors in the Site, set it first in Update Mode.\n" +
                    " - From the Group Initial Settings, click on Change Site Mode.";
                alert(message);
            });
        </script>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // For errors in this page
            if (!empty($outputErr)) {
                echo '<div class="msgError">' . $outputErr . '</div>';
            } else {
                include __DIR__ . "/import.php";
            }
        }
        include __DIR__ . "/info.php";
        ?>
    </div>

</body>

</html>