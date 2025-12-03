<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include __DIR__ ."/functionsTableName.php";

if (!empty(@$_GET["chooseTable"])) {
	$request_Table = @$_POST["Table"];
	$_SESSION["Table"] = $request_Table;
} elseif (isset($_SESSION["Table"])) {
	$request_Table = $_SESSION["Table"];
}

/**
 * Do not load table configuration arrays when updating the configuration
 */
if (isset($_GET["chooseFields"])) {
	/**
	 * Get form inputs and add selections to the config Table
	 * Get the Form Inputs as an ordered array
	 */
	$arrAliasNames = array();
	$arrSelectedFields = array();
	$strOrderByField = "";

	$strSQL = "SELECT * FROM " . $request_Table . " LIMIT 1";
	$rs = $conn->query($strSQL);
	$iTemp = $rs->columnCount();
	for ($i = 0; $i < $iTemp; $i++) {
		$meta = $rs->getColumnMeta($i);
		$xName = $meta["name"];
		$xType = $meta["native_type"];
		$xPK = @$meta['flags'][1]; //Primary key
		if (!empty(@$_POST["strAsName" . $xName])) {
			//$arrAliasNames[] = array($xName  => $_POST["strAsName" . $xName]);
			$arrAliasNames[$xName] = $_POST["strAsName" . $xName];
		}

		if ($xPK == 'primary_key') { // Primary key is allways included
			$arrSelectedFields[] = $xName;
		} elseif (!empty(@$_POST["box" . $xName])) {
			$arrSelectedFields[] = $xName;
		}

		if (@$_POST["orderByPK"] == "[DESC]" . $xName) {
			$strOrderByPK = $xName . " DESC";
		} elseif (@$_POST["orderByPK"] == "[ASC]" . $xName) {
			$strOrderByPK = "";
		}
		if (@$_POST["desc"] == $xName) {
			$strOrderByField = $xName . " DESC";
		}
	}
	$rs = null;

	if ($strOrderByPK != "") {
		if ($strOrderByField == "") {
			$strOrderByField = $strOrderByPK;
		} else {
			$strOrderByField = $strOrderByField . ", " . $strOrderByPK;
		}
	}

	/**
	 * Insert or update the configuration table
	 */
	$boolExists = false;
	$strSQL = "SELECT ConfigID
		FROM sx_config_tables 
		WHERE ConfigTableName = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$request_Table]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	if ($rs) {
		$boolExists = true;
	}
	$stmt = null;
	$rs = null;

	if (!empty($arrSelectedFields)) {
		$json_SelectedFields = json_encode($arrSelectedFields, JSON_UNESCAPED_UNICODE);
	}
	if (!empty($arrAliasNames)) {
		$json_AliasNames = json_encode($arrAliasNames, JSON_UNESCAPED_UNICODE);
	}

	if ($boolExists) {
		$sql = "UPDATE sx_config_tables SET 
			SelectedFields = ?,
			OrderByField = ?
			WHERE configTableName = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$json_SelectedFields, $strOrderByField, $request_Table]);
	} else {
		$sql = "INSERT INTO sx_config_tables 
			(ConfigTableName,SelectedFields, OrderByField)
			VALUES (?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$request_Table, $json_SelectedFields, $strOrderByField]);
	}

	/**
	 * Insert or update the Help and Language Table
	 */
	$boolExists = false;
	$strSQL = "SELECT HelpByTableID
		FROM sx_help_by_table 
		WHERE TableName = ? AND LanguageCode = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$request_Table, sx_DefaultAdminLang]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	if ($rs) {
		$boolExists = true;
	}
	$stmt = null;
	$rs = null;

	if ($boolExists) {
		$sql = "UPDATE sx_help_by_table SET 
			AliasNameOfFields = ?,
			TableName = ?
			WHERE TableName = ? AND LanguageCode = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$json_AliasNames, $request_Table, $request_Table, sx_DefaultAdminLang]);
	} else {
		$sql = "INSERT INTO sx_help_by_table 
			(LanguageCode, TableName, AliasNameOfFields)
			VALUES (?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([sx_DefaultAdminLang, $request_Table, $json_AliasNames]);
	}
}

include PROJECT_ADMIN ."/configFunctions.php";


?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Config Table Fields</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<script src="../js/jq/jquery.min.js"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js"></script>
</head>

<body class="body">
	<header id="header">
		<h2>TABLE: <?= strtoupper($request_Table) ?><br>Configuration of Fields</h2>
		<form method="POST" name="chooseTable" action="configTableFields.php?chooseTable=yes">
			<?php

			$arr_NotUsedTables = array();
			$strSQL = "SELECT tablesByGroupName 
				FROM sx_config_groups 
				WHERE ProjectName = ? AND LanguageCode = ?";
			$stmt = $conn->prepare($strSQL);
			$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
			$strTablesByGroupName = $stmt->fetchColumn();
			$stmt = null;
			if (!empty($strTablesByGroupName)) {
				$arrNotUsedTables = json_decode($strTablesByGroupName, true)["noGrouped"];
				if (!empty($arrNotUsedTables)) {
					foreach ($arrNotUsedTables as $index => $value) {
						$arr_NotUsedTables[$index] = $value["name"];
					}
				}
			} ?>
			<select size="1" name="Table">
				<option value="">Select Table</option>
				<?php
				$result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
				while ($row = $result->fetch(PDO::FETCH_NUM)) {
					$sTbl = $row[0];
					if (!in_array($sTbl, $arr_NotUsedTables)) {
						$slectedTbl = "";
						if ($sTbl == $request_Table) {
							$slectedTbl = "selected";
						} ?>
						<option <?= $slectedTbl ?> value="<?= $sTbl ?>"><?= $sTbl ?></option>
				<?php }
				} ?>
			</select>
			<input class="button" type="submit" value="»»»" name="B3">
			<?php
			$result = null;
			?>
		</form>
		<div class="margin_right_auto">
			<button class="button jqHelpButton" data-id="helpTableFields">HELP</button>
		</div>
	</header>

	<section class="section_config" style="position: relative">
		<?php if (empty($request_Table)) { ?>
			<h2>Please select a table from the above list.</h2>
		<?php } else { ?>
			<form method="POST" name="chooseFields" action="configTableFields.php?chooseFields=yes">
				<table>
					<tr>
						<th>Fields</th>
						<th>Visible</th>
						<th>Field Type</th>
						<th>Order By</th>
						<th>Alias Name</th>
					</tr>
					<?php
					$stmt = $conn->query("SELECT * FROM " . $request_Table ." LIMIT 1");
					$colcount = $stmt->columnCount();
					for ($c = 0; $c < $colcount; $c++) {
						$meta = $stmt->getColumnMeta($c);
						/**
						 * $meta['name'], $meta['native_type'], $meta['len']
						 * @$meta['flags'][0], @$meta['flags'][1], @$meta['flags'][2]
						 * 1. primary_key	2. unique_key
						 */
						$xName = $meta['name'];
						$xType = $meta['native_type'];
						$xAuto = @$meta['flags'][1] == "primary_key" ? true : false; ?>
						<tr>
							<th class="no_gradient"><?= $xName ?></th>
							<td>
								<?php
								if ($xType != 'BLOB' && !$xAuto) { ?>
									<input type="checkbox" name="box<?= $xName ?>" value="<?= $xName ?>" <?= sx_checkSelected($xName) ?>>
								<?php } else {
									echo "&nbsp;";
								} ?>
							</td>
							<td>
								<?= $xType ?>
								<?php
								if ($xAuto) { ?>
									<input type="hidden" name="<?= $xName ?>" value="<?= $xName ?>" size="20">AUTO
								<?php } else {
									echo " ";
								} ?>
							</td>
							<td>
								<?php
								if ($c == 0) {
									$checkASC = "checked";
									if (!empty(sx_checkOrderBy($xName))) {
										$checkASC = "";
									} ?>
									<input type="radio" value="[DESC]<?= $xName ?>" name="orderByPK" <?= sx_checkOrderBy($xName) ?>>DESC<br>
									<input type="radio" value="[ASC]<?= $xName ?>" name="orderByPK" <?= $checkASC ?>>ASC<br>
									<input type="radio" value="none" name="desc">ONLY PK
								<?php
								} else { ?>
									<input type="radio" value="<?= $xName ?>" name="desc" <?= sx_checkOrderBy($xName) ?>>
								<?php
								} ?>
							</td>
							<td>
								<input type="text" name="strAsName<?= $xName ?>" value="<?= sx_checkAsName($xName) ?>" size="48">
							</td>
						</tr>
					<?php
					}
					$stmt = null;
					?>
				</table>
				<br><input type="submit" value="Save Fields" name="B1"><br><br>
			</form>
	</section>
<?php }
		include "sxHelpFiles/helpTableFields.php";
?>
</body>

</html>