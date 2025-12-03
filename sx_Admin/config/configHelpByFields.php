<?php

include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/functionsTableName.php";
//include "functionsDBConn.php";

if (strtolower($request_Table) == "sx_config_tables" || strtolower($request_Table) == "sx_config_groups") {
	unset($_SESSION["Table"]);
}
include PROJECT_ADMIN . "/configFunctions.php";

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Help Information by Field</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
	<script src="../js/jq/jquery.min.js?v=2024"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>config/js/jqConfigFunctions.js?v=2024"></script>
	<script src="../tinymce/tinymce.min.js?v=2025-09"></script>
    <script src="../tinymce/config/help_fields.js?v=2025-09"></script>
</head>

<body class="body">

	<header id="header" class="header">
		<h2>TABLE: <?= ($request_Table) ?><br>Write Help Information for every Field</h2>
		<form method="POST" name="chooseTable" action="configHelpByFields.php?chooseTable=yes">
			<select name="Table">
				<option value="">Select Table</option>
				<?php
				$result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
				$rs = $result->fetchAll(PDO::FETCH_NUM);
				foreach ($rs as $table) {
					$loopTable = $table[0];
					$strSelected = "";
					if ($loopTable == @$request_Table) {
						$strSelected = " selected ";
					} ?>
					<option<?= $strSelected ?> value="<?= $loopTable ?>"><?= $loopTable ?></option>
					<?php
					$rs = null;
				} ?>
			</select>
			<input class="button" type="submit" value="»»»" name="SelectTable">
		</form>
	</header>

	<?php if (empty($request_Table)) { ?>
		<h2>Please select a table from the above list.</h2>
	<?php } else { ?>
		<h3>Help Information here has Priority to Field Descriptions in Database Tables</h3>
		<p>Primarily, fields are expected to be described in their respective database table (see right column).<br>
			Add help information here if a field description is empty, if you like to replace it or change its language.</p>
		<p>If help information is empty, descriptions of fields from the database table will be used.</p>
		<section>
			<form method="POST" id="configurForm" name="configurFields" data-url="ajax_SaveHelpFields.php" action="configHelpByFields.php">
				<table class="tbl_config tbl_width">
					<tr>
						<th>Field Name | Type</th>
						<th>Write Help Information.</th>
						<th>Field Description from Database Table</th>
					</tr>
					<?php
					$arrColumnSchema = sx_getColumnsTypeComments(sx_TABLE_SCHEMA, $request_Table);
					$iCount = count($arrColumnSchema);
					for ($i = 0; $i < $iCount; $i++) {
						$xName = $arrColumnSchema[$i]["COLUMN_NAME"];
						$xType = $arrColumnSchema[$i]["COLUMN_TYPE"];
						$xComments = $arrColumnSchema[$i]["COLUMN_COMMENT"];

						$xHelp = "";
						if (is_array($arrHelpByField) && array_key_exists($xName, $arrHelpByField)) {
							$xHelp = $arrHelpByField[$xName];
						} else {
							// Remove when help by field for all tables has been updated
							if (isset($js_HelpByField) && strpos($js_HelpByField, "[" . $xName . "]") !== false) {
								$arrTemp = explode($xName . "]", $js_HelpByField);
								$arrTemp = explode("}", $arrTemp[1]);
								$xHelp = trim(str_replace("{", "", $arrTemp[0]));
							}
						} ?>
						<tr>
							<th class="no_gradient no_wrap alignRight">
								<p><b><?= $xName ?></b><br>
									<?= $xType ?></p>
								<p><input class="button" type="submit" value="Save Help" name="submit_<?= $i ?>"></p>
								<p><a class="button" href="javascript:;" onclick="tinymce.execCommand('mceToggleEditor',false,'<?= $xName ?>');">Toggle Editor</a></p>
							</th>
							<td>
								<textarea id="<?= $xName ?>" name="help<?= $xName ?>" style="height: 280px; width: 100%"><?= $xHelp ?></textarea>
							</td>
							<td>
								<?php if (!empty($xComments)) { ?>
									<?= $xComments ?>
								<?php } ?>
							</td>
						</tr>
					<?php
					} ?>
				</table>
				<p><input class="button" type="submit" value="Save Help" name="submit"></p>
				<br>
			</form>
		</section>
	<?php
	}
	?>
</body>

</html>
<?php
$connClose;
?>