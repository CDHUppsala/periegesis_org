<?php

$intLastGroupNumber = 0;
$strExport = null;
if (isset($_GET["export"])) {
	$strExport = $_GET["export"];
}
if ($strExport == "html") {
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=content_" . sx_DefaultAdminLang . ".php");
	header("Content-Type: text/xtml; ");
}

$strHref = "exportMainPage.php";
$strLevel = "../";
$strToConfig = "";
if (!empty($strExport)) {
	$strHref = "main.php";
	$strLevel = "";
	$strToConfig = "config/";
}
define("str_Href", $strHref);
define("str_Level", $strLevel);
define("str_Export", $strExport);

//#### Get variables from the configuration of table groups, if they are avaliable

$radioConfigGroupsExist = false;
$strSQL = "SELECT * FROM sx_config_groups 
	WHERE ProjectName = ?
	AND LanguageCode = ? ";
//echo '<br><br><br>'. $strSQL .'<br>';
//echo $strSourceProjectName . " / ". sx_DefaultAdminLang;
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioConfigGroupsExist = true;
	$getAlias = $rs["AliasNameOfTables"];
	$getGroups = $rs["OrderedTableGroupNames"];
	$getTables = $rs["TablesByGroupName"];
	$getAddUpdate = $rs["AddAndUpdateTitles"];
}
$rs = null;

$arrAlias = "";
$arrGroups = "";
$arrTables = "";
$arrAddUpdate = "";

if ($radioConfigGroupsExist) {
	$arrAlias = json_decode($getAlias, true);
	$arrGroups = json_decode($getGroups, true);
	$arrTables = json_decode($getTables, true);
	$arrAddUpdate = json_decode($getAddUpdate, true);
}
define("arr_Alias", $arrAlias);
define("arr_Groups", $arrGroups);
define("arr_Tables", $arrTables);
define("arr_AddUpdate", $arrAddUpdate);

function sx_getTableNamesByGroups()
{
	// To get the last group number (for tagging Tools)
	global $intLastGroupNumber;
	//Get the order of group names
	if (!empty(arr_Tables)) {
		$radioFirstGroup = true;
		$i = 1;
		foreach (arr_Tables as $GroupName => $GroupTables) {
			if ($GroupName == "noGrouped") {
				$arrSort  = array_column($GroupTables, 'sort');
				$arrName = array_column($GroupTables, 'name');
				array_multisort($arrName, SORT_ASC, $arrSort, SORT_DESC, $GroupTables); ?>
				<h2><span></span><a href="javascript:void(0)"><?= $GroupName ?></a></h2>
				<div style="display: none">
				<?php
			} else {
				ksort($GroupTables);
				if ($radioFirstGroup) {
					$strImg = ' class="selected"';
					$strDisplay = ' style="display: display"';
				} else {
					$strImg = "";
					$strDisplay = ' style="display: none"';
				}
				$radioFirstGroup = false

				?>
					<h2>
						<span<?= $strImg ?>></span><a href="<?= str_Href ?>?intTab=<?= $i ?>"><?= $GroupName ?></a>
					</h2>
					<div<?= $strDisplay ?>>
		<?php
				$i++;
				$intLastGroupNumber = $i;
			}
			$LastTableName = "";

			foreach ($GroupTables as $subkey => $subvalue) {
				$TableName = $subvalue["name"];

				foreach (arr_AddUpdate as $action => $object) {
					if (is_array($object) && array_key_exists($TableName, $object)) {
						foreach ($object as $sub_key => $sub_value) {
							if ($sub_key == $TableName) {
								if ($action == "add") {
									echo '<a href="' . str_Level . "add.php?RequestTable=" . $sub_key . '">' . $sub_value . " »</a><br>" . "\n";
								} else {
									echo '<a href="' . str_Level . "list.php?RequestTable=" . $sub_key . '&updateMode=yes">' . $sub_value . " »</a><br>" . "\n";
									echo '<span class="line"> </span>' . "\n";
								}
							}
						}
					}
				}
				if (!empty($LastTableName)) {
					if (substr($LastTableName, 0, 3) != substr($TableName, 0, 3)) {
						echo '<span class="line"></span>' . "\n";
					}
				}
				$LastTableName = $TableName;
				$AliasTableName = $TableName;
				if (is_array(arr_Alias) && array_key_exists($TableName, arr_Alias)) {
					$AliasTableName = arr_Alias[$TableName];
				}
				echo '<a href="' . str_Level . "list.php?RequestTable=" . $TableName . '">' . $AliasTableName . " »</a><br>" . "\n";
			}
			echo "</div>";
		}
	}
}


//#### Gets the table names from the main DB
function sx_TableNames()
{
	$strLeftText = null;
	$conn = dbconn();
	$result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
	$rs = $result->fetchAll(PDO::FETCH_NUM);
	foreach ($rs as $table) {
		$loopTable = $table[0];
		if ($strLeftText != substr($loopTable, 0, 3)) {
			echo '<span class="line"> </span><br>';
		}
		echo '<a target="main" href="' . str_Level . "list.php?RequestTable=" . $loopTable . '">' . $loopTable . "</a>";
		echo "<br>";

		$strLeftText = substr($loopTable, 0, 3);
	}
	$rs = null;
}

$strBackToAdmin = null;
$strBackToAdminBlank = null;
if (empty($strExport)) {
	$strBackToAdmin = '<a title="Back to DB-admin" style="float: left; padding: 0 8px;" target="_top" href="../default.php">«</a>';
	$strBackToAdminBlank = '<a title="Open DB-admin in New Window" style="float: right; padding: 0 8px;" target="_blank" href="../default.php">»</a>';
} ?>
		<div id="menuHeader" class="logoBox">
			<div>PUBLIC SPHERE CMS</div>
			<div><?= $strBackToAdmin . $strBackToAdminBlank ?><a target="_top" title="Refresh the Page" href="default.php?clear=yes">TABLE GROUPS</a></div>
		</div>

		<div id="menuBG">
			<?php

			if (is_array($arrGroups) && is_array($arrTables)) {
				sx_getTableNamesByGroups();
			} else { ?>

				<h2>Tables</h2>
				<div>
					<?php sx_TableNames() ?>
				</div>
			<?php
			} ?>
			<h2><span></span>
				<a href="<?= $strHref ?>?intTab=<?= ($intLastGroupNumber) ?>"><?= lngTools ?></a>
			</h2>
			<div style="display: none">
				<a href="sxUpload/form_upload_files.php?clear=yes"><?php echo lngUploadFiles ?> »</a><br>
				<a href="sxUpload/form_upload_large_files.php?clear=yes"><?php echo lngUploadLargeFiles ?> »</a><br>
				<a href="sxUpload/form_resize_upload_images.php?clear=yes">Resize, Crop & Upload Images »</a><br>
				<span class="line"> </span>
				<a target="_blank" href="<?= $strLevel ?>sxCleanText/sxCleanText.php" onclick="openCenteredWindow(this.href, 'CleanText', 900, ''); return false"><?= lngCleanText ?> »</a><br>
				<a target="_blank" href="<?= $strLevel ?>sxCleanText/sxPreserveFormedText.php" onclick="openCenteredWindow(this.href, 'CleanWord', 980, ''); return false"><?= lngCleanPreserveFormedText ?> »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>sxVisitStats/default.php?clear=yes"><?= lngVisitsStatistics ?> »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>sitemaps/create_xml.php?clear=yes"><?= lngCreateXMLSiteMaps ?> »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>backupDB/index.php?clear=yes">Backup Database »</a><br>
				<a href="<?= $strLevel ?>backupDB/restore.php?clear=yes">Restore Database »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>sxHashing/sx_getHashedCode.php?clear=yes" target="_blank">Hash Passwords »</a><br>
			</div>

			<h2><span></span>
				<a href="javascript:void(0)">Configuration Tools</a>
			</h2>
			<div style="display: none">
				<a href="<?= $strToConfig ?>configTableFields.php">Visible Fields »</a><br>
				<a href="<?= $strToConfig ?>configRelatedFields.php">Related Fields »</a><br>
				<span class="line"> </span>
				<a href="<?= $strToConfig ?>dbCompare/dbInfo.php">DB Tables »</a><br>
				<span class="line"> </span>
				<a href="<?= $strLevel ?>add_help/help_by_group.php?clear=yes">Help by Group »</a><br>
				<a href="<?= $strLevel ?>add_help/help_by_table.php?clear=yes">Help by Table »</a><br>
			</div>

			<h2><span></span>
				<a href="javascript:void(0)">Import & Export Tools</a>
			</h2>
			<div style="display: none">
				<a href="<?= $strLevel ?>sxTools/sxMySQL_Export/index.php?clear=yes">Export from MySQL »</a><br>
				<a href="<?= $strLevel ?>sxTools/sxMySQL_Import/index.php?clear=yes">Import to MySQL »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>sxTools/sxMySQL_Import_Array/index.php?clear=yes">Import by Array to MySQL »</a><br>
				<a href="<?= $strLevel ?>sxTools/sxXMLUpload/default.php?clear=yes">Import Fields from XML to MySQL »</a><br>
				<span class="line"></span>
				<a href="<?= $strLevel ?>backupDB/index.php?clear=yes">Backup Database »</a><br>
				<a href="<?= $strLevel ?>backupDB/restore.php?clear=yes">Restore Database »</a><br>
				<span class="line"> </span>
				<a href="<?= $strLevel ?>sxMultipleUpdates/default.php?clear=yes">Update Single Table Fields »</a><br>
			</div>

			<?php
			if (empty($strExport)) { ?>
				<span class="line"> </span>
				<h2><span></span><a href="javascript:void(0)">Configure Database</a></h2>
				<div style="display: none">
					<a href="configTableFields.php">Table Fields »</a><br>
					<a href="configRelatedFields.php">Related Fields »</a><br>
					<span class="line"> </span>
					<a href="configTableGroups.php">Table Groups »</a><br>
					<span class="line"> </span>
					<a href="configHelpByGroups.php">Help by Groups »</a><br>
					<a href="configHelpByTables.php">Help by Tables »</a><br>
					<a href="configHelpByFields.php">Help by Table Fields »</a><br>
					<span class="line"> </span>
					<a href="exportContentPage.php?export=html">Export Content Page »</a><br>
					<a href="exportMainPage.php?export=html">Export Main Page »</a><br>
					<a href="saveNonUsedTables.php?export=html">Save Non Used Tables »</a><br>
				</div>
				<h2><span></span><a href="javascript:void(0)">Compare Databases</a></h2>
				<div style="display: none">
					<a href="dbCompare/dbInfo.php">DB Tables »</a><br>
					<a href="dbCompare/dbCompare.php">DB Compare »</a><br>
					<span class="line"> </span>
					<a href="dbCopy_TBL2TBL/index.php?clear=yes">Update Tables between Databases »</a><br>
					<a href="dbCopy_DB2DB/default.php">Copy and Update Databases »</a><br>
				</div>
				<h2><span></span><a href="javascript:void(0)">Various</a></h2>
				<div style="display: none">
					<a href="sxTextToFiles/index.php">SVG in Text to SVG-Files »</a><br>
					<a href="sxTextToFiles/lorem.php">Lorem Ipsum »</a><br>
					<a href="sessions.php">Sessions »</a><br>
				</div>
			<?php
			} ?>
		</div>
		<?php
			if (!empty($strExport)) { ?>
			<div id="menuFooter" class="logoBox">
				<div><a target="_blank" href="../default.php">HOME PAGE</a></div>
				<div><a target="_top" href="login/logout.php">LOG OUT</a></div>
			</div>
		<?php
		} ?>