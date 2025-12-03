<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include __DIR__ . "/functionsTableName.php";
include PROJECT_ADMIN . "/configFunctions.php";

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

$strSQL = "SELECT OrderedTableGroupNames, TablesByGroupName 
	FROM sx_config_groups
		WHERE ProjectName = ? AND LanguageCode = ?";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$jsonGroups = $rs["OrderedTableGroupNames"];
	$jsonTables = $rs["TablesByGroupName"];
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
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2025">
	<script src="../js/jq/jquery.min.js?v=2025"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>config/js/jqConfigFunctions.js?v=2025"></script>
	<script src="../tinymce/tinymce.min.js?v=2025"></script>
    <script src="../tinymce/config/help.js?v=2025"></script>
</head>

<body class="body">
	<aside class="absolut_right">
		<h3>Select Table Group</h3>
		<?php
		$strGroupName = "";
		$arrGroups = json_decode($jsonGroups, true);
		$arrTables = json_decode($jsonTables, true);

		$iCount = count($arrGroups);
		for ($z = 0; $z < $iCount; $z++) {
			$loopGroup = trim($arrGroups[$z]);
			if (array_key_exists($loopGroup, $arrTables)) {
				if (intval($intGroupID) == $z) {
					$strGroupName = $loopGroup;
				} ?>
				<a href="configHelpByTables.php?groupID=<?= $z ?>">» <?= $loopGroup ?></a><br>
		<?php }
		} ?>
	</aside>

	<header id="header">
		<h2>TABLES BY GROUP: <?= $strGroupName ?><br>Write Help Information for every Table in the Group</h2>
	</header>
	<section>
		<h2>Select Table Group</h2>
		<?php
		$arrTablesByGroup = array();
		if (array_key_exists($strGroupName, $arrTables)) { ?>
			<p>Help informatio written here has priority to the Table Comments from the database, if any (see right column).<br>
			Add help information if the table comments are empty, if you like to replace them or change their language.</p>
				<p>If help information is empty, comments from the database table will be used.</p>
			<form method="POST" id="configurForm" name="configTables" data-url="ajax_SaveHelpTables.php" action="configHelpByTables.php">
				<table class="tbl_config tbl_width">
					<tr>
						<th>Group | Table Name</th>
						<th>Write Help Information.</th>
						<th>Table Comments from Database</th>
					</tr>
					<?php
					// Get all tables of the currently selected group
					$arrCurrentGroup = $arrTables[$strGroupName];
					$iCount = count($arrCurrentGroup);
					for ($i = 0; $i < $iCount; $i++) {
						$LoopTable = $arrCurrentGroup[$i]["name"];
						$arrTablesByGroup[] = $LoopTable;

						$strTableHelp = sx_getTableHelp($LoopTable);
						$strTableComments = sx_getTableComments($LoopTable);
						/*
						if (empty($strTableHelp) || $strTableHelp == "No help available") {
							$strTableHelp = $strTableComments;
						} */
					?>
						<tr>
							<th class="no_gradient no_wrap alignRight">
								<p><b><?= $strGroupName ?></b><br>
									<?= $LoopTable ?></p>
								<p><input class="button" type="submit" value="Save Help" name="submit_<?= $i ?>"></p>
								<p><a class="button" href="javascript:;" onclick="tinymce.execCommand('mceToggleEditor',false,'<?= $LoopTable ?>');">Toggle Editor</a></p>
							</th>
							<td>
								<textarea id="<?= $LoopTable ?>" spellcheck="true" name="<?= $LoopTable ?>" style="width: 100%; height: 480px">
									<?= $strTableHelp ?>
								</textarea>
							</td>
							<td>
								<?= $strTableComments ?>
							</td>
						</tr>
					<?php
					}
					$strTableNamesByGroup = implode(",", $arrTablesByGroup);
					?>
				</table>
				<input type="hidden" name="TableNamesByGroup" value="<?= $strTableNamesByGroup ?>">
				<input type="hidden" name="groupID" value="<?= $intGroupID ?>">
				<p><input class="button" type="submit" value="Save Help" name="submit"></p>
			</form>
		<?php
		} ?>
	</section>
</body>

</html>