<?php

include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include __DIR__ ."/functionsTableName.php";
include PROJECT_ADMIN ."/configFunctions.php";


//#### Get variables from the configuration of table groups, if they are avaliable

function getGroupHelp($groupName)
{
	$conn = dbconn();
	$strSQL = "SELECT GroupHelp 
		FROM sx_help_by_group 
		WHERE GroupName  = ? 
		AND LanguageCode = ? ";
	$fstmt = $conn->prepare($strSQL);
	$fstmt->execute([$groupName, sx_DefaultAdminLang]);
	$frs = $fstmt->fetch(PDO::FETCH_ASSOC);
	if ($frs) {
		return  $frs["GroupHelp"];
	} else {
		return  "No help available";
	}
}

$strSQL = "SELECT OrderedTableGroupNames 
	FROM sx_config_groups 
	WHERE ProjectName = ? AND LanguageCode = ?";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$jsonGroups = $rs["OrderedTableGroupNames"];
}
$stmt = null;
$rs = null;

if (isset($_GET["groupID"])) {
	$intGroupID = $_GET["groupID"];
} elseif (isset($_POST["groupID"])) {
	$intGroupID = $_POST["groupID"];
}
if (!isset($intGroupID) || !is_numeric($intGroupID)) {
	$intGroupID = 0;
}

//## Get form inputs and add selections to the config Table
if (!empty($_GET["configGroups"])) {
	$sGroupName = @$_POST["GroupName"];
	$strGroupHelp = trim(sx_replaceQuotes(@$_POST["GroupHelp"]));

	//## Add to or Update the sx_help_by_group Table
	$radioExists = False;
	$strSQL = "SELECT GroupName 
		FROM sx_help_by_group 
		WHERE GroupName  = ? 
		AND LanguageCode = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$sGroupName, sx_DefaultAdminLang]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	if ($rs) {
		$radioExists = True;
	}
	$stmt = null;
	$rs = null;

	if ($radioExists == False) {
		$sql = "INSERT INTO sx_help_by_group (LanguageCode, GroupName, GroupHelp) VALUES (?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([sx_DefaultAdminLang, $sGroupName, $strGroupHelp]);
	} else {
		$sql = "UPDATE sx_help_by_group SET GroupHelp = ? WHERE GroupName = ? AND LanguageCode = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strGroupHelp, $sGroupName, sx_DefaultAdminLang]);
	}
	header("location: configHelpByGroups.php?groupID=" . $intGroupID);
	exit();
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Help Information by Table Groups</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
	<script src="../tinymce/tinymce.min.js?v=2024"></script>
	<script src="../tinymce/config/help.js?v=2024"></script>

</head>

<body class="body">
	<header id="header">
		<h2>TABLE GROUPS:<br>Write Help Information for every Table Group</h2>
	</header>
	<aside class="absolut_right">
		<h3>Select Table Group</h3>
		<?php
		$strGroupName = "";
		$arrGroups = json_decode($jsonGroups);
		$iCount = count($arrGroups);
		for ($z = 0; $z < $iCount; $z++) {
			if ($intGroupID == $z) {
				$strGroupName = trim($arrGroups[$z]);
			} ?>
			<a href="configHelpByGroups.php?groupID=<?= $z ?>">» <?= $arrGroups[$z] ?></a><br>
		<?php } ?>
	</aside>

	<?php if ($strGroupName != "") { ?>
		<section>
			<form method="POST" name="configurFields" action="configHelpByGroups.php?configGroups=yes">
				<input type="hidden" name="GroupName" value="<?= $strGroupName ?>">
				<input type="hidden" name="GroupHelp" value="<?= $intGroupID ?>">
				<table class="tbl_config">
					<tr>
						<th>Name of Table Group</th>
						<th>Write Help Information</th>
					</tr>
					<tr>
						<th class="no_gradient no_wrap alignRight">
							<p><b><?= $strGroupName ?></b></p>
							<p><input class="button" type="submit" value="Save Help" name="submit"></p>
							<p><a class="button" href="javascript:;" onclick="tinymce.execCommand('mceToggleEditor',false,'<?= $strGroupName ?>');">Toggle Editor</a></p>
						</th>
						<td class="rightCell">
							<textarea id="<?= $strGroupName ?>" spellcheck="true" name="GroupHelp" style="width: 900px; height: 740px"><?= getGroupHelp($strGroupName) ?></textarea>
						</td>
					</tr>
				</table>
			</form>
		</section>
	<?php
	} ?>
</body>

</html>