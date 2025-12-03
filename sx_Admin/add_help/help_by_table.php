<?php

include dirname(__DIR__) . "/functionsLanguage.php";
include dirname(__DIR__) . "/login/lockPage.php";
include dirname(__DIR__) . "/functionsTableName.php";
include dirname(__DIR__) . "/functionsDBConn.php";
include dirname(__DIR__) . "/configFunctions.php";


//#### Get variables from the configuration of table groups, if they are avaliable
$strTableNamesByGroup = "";

if (isset($_GET["groupID"])) {
    $intGroupID = $_GET["groupID"];
} elseif (isset($_POST["groupID"])) {
    $intGroupID = $_POST["groupID"];
}
if (!isset($intGroupID) || !is_numeric($intGroupID)) {
    $intGroupID = 0;
}

$strSQL = "SELECT OrderedTableGroupNames, TablesByGroupName, AliasNameOfTables
	FROM sx_config_groups
		WHERE ProjectName = ? AND LanguageCode = ?";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $jsonGroups = $rs["OrderedTableGroupNames"];
    $jsonTables = $rs["TablesByGroupName"];
    $jsonAlias = $rs["AliasNameOfTables"];
}
$stmt = null;
$rs = null;

function sx_getTableHelp($strName)
{
    $conn = dbconn();
    $strSQL = "SELECT TableHelp 
		FROM sx_help_by_table 
		WHERE TableName  = ?
		AND LanguageCode = ?";
    $fstmt = $conn->prepare($strSQL);
    $fstmt->execute([$strName, sx_DefaultAdminLang]);
    $frs = $fstmt->fetchColumn();

    //echo $strSQL .' / ' . $strName .' / '. sx_DefaultAdminLang;

    if (!empty($frs)) {
        return  $frs;
    } else {
        return  "";
    }
}

/*
	 * Get all tables of the current group and 
	 * loop to update help information
	 * Is done by Ajax!
	 * Uncomment the inlude to replace Ajax.
*/

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere CMS - Help Information by Table</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
    <script src="../js/jq/jquery.min.js"></script>
    <script src="../tinymce/tinymce.min.js?v=2024"></script>
    <script src="../tinymce/config/help.js?v=2024"></script>
    <?php
    include __DIR__ . "/functions_jq.php";
    ?>
</head>

<body class="body">
    <aside class="absolut_right">
        <h3>Select Table Group</h3>
        <?php
        $strGroupName = "";
        $arrGroups = json_decode($jsonGroups, true);
        $arrTables = json_decode($jsonTables, true);
        $arrAlias = json_decode($jsonAlias, true);

        $iCount = count($arrGroups);
        for ($z = 0; $z < $iCount; $z++) {
            $loopGroup = trim($arrGroups[$z]);
            if (array_key_exists($loopGroup, $arrTables)) {
                if (intval($intGroupID) == $z) {
                    $strGroupName = $loopGroup;
                } ?>
                <a href="help_by_table.php?groupID=<?= $z ?>">» <?= $loopGroup ?></a><br>
        <?php }
        } ?>
    </aside>

    <header id="header">
        <h2>TABLES BY GROUP: <?= $strGroupName ?><br>Write Help Information for every Table in the Group</h2>
    </header>
    <section>
        <div class="maxWidthWide">
            <h2>Select Table Group</h2>
            <p>Help informatio written here has priority to the Table Comments from the database, if any (see right column).<br>
                Add help information if the table comments are empty, if you like to replace them or change their language.</p>
            <p>If help information is empty, comments from the database table will be used.</p>
            <?php
            $arrTablesByGroup = array();
            if (array_key_exists($strGroupName, $arrTables)) { ?>
                <?php
                // Get all tables of the currently selected group
                $arrCurrentGroup = $arrTables[$strGroupName];
                $iCount = count($arrCurrentGroup);
                for ($i = 0; $i < $iCount; $i++) {
                    $LoopTable = $arrCurrentGroup[$i]["name"];
                    $arrTablesByGroup[] = $LoopTable;
                    $LoopAlias = $arrAlias[$LoopTable];

                    $strTableHelp = sx_getTableHelp($LoopTable);
                    $strTableComments = sx_getTableComments($LoopTable);
                    if (empty($strTableHelp) || $strTableHelp == "No help available") {
                        $strTableHelp = $strTableComments;
                    } ?>
                    <form method="POST" class="jq_updateHelpByGroupTable" name="configTables_<?php echo $i ?>" data-url="ajax_SaveHelp.php" action="help_by_table.php">
                        <div class="write_help_bar">
                            <div class="row">
                                <div><a class="button" href="javascript:;" onclick="tinymce.execCommand('mceToggleEditor',false,'<?= $LoopTable ?>');">Toggle Editor</a>
                                    <input class="button" type="submit" value="Save Help" name="submit_<?= $i ?>">
                                </div>
                                <h3> Group: <b><?= $strGroupName ?></b>, Table: <b><?= $LoopAlias ?></b></h3>
                            </div>

                            <div class="row">
                                <span>Write Help Information</span>
                                <span><input type="checkbox" name="AlterTableComment" value="Yes"> Alter Table Comment</span>
                                <span>Comments from Database Table</span>
                            </div>
                        </div>
                        <div class="row">
                            <div style="width: 50%;">
                                <textarea id="<?= $LoopTable ?>" spellcheck="true" name="<?= $LoopTable ?>" style="width: 100%; height: 600px">
									<?= $strTableHelp ?></textarea>
                            </div>
                            <div style="width: 50%; padding-left: 0.75rem">
                                <?= $strTableComments ?>
                            </div>
                        </div>
                        <!-- The value of this hidden input is equal to the name of textarea -->
                        <input type="hidden" name="HelpByTableName" value="<?= $LoopTable ?>">
                        <input type="hidden" name="TableName" value="<?= $LoopTable ?>">
                        <!-- Not Used, the initial guery string for the Table Group -->
                        <input type="hidden" name="groupID" value="<?= $intGroupID ?>">
                    </form>
            <?php
                }
            } ?>
        </div>
    </section>
</body>

</html>