<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include __DIR__ ."/functions.php";

$sTable = @$_POST["Table"];
$iGreaterThanID = @$_POST["GreaterThanID"];
if (intval($iGreaterThanID) == 0) {
	$iGreaterThanID = 0;
}
$iLesserThanID = @$_POST["LesserThanID"];
if (intval($iLesserThanID) == 0) {
	$iLesserThanID = 0;
}
$sPathToSourceDB = @$_POST["PathToSourceDB"];

if (!empty($sPathToSourceDB)) {
	$_SESSION["PathToSourceDB"] = $sPathToSourceDB;
} elseif (@$_SESSION["PathToSourceDB"]) {
	$sPathToSourceDB = $_SESSION["PathToSourceDB"];
}
$connSource = null;
$boolSourseIsMySQL = true;
if (!empty($sPathToSourceDB)) {
	if (strpos($sPathToSourceDB, "/") > 0 || strpos($sPathToSourceDB, "\\") > 0) {
		$connSource = get_dbConn_Access($sPathToSourceDB);
		$boolSourseIsMySQL = false;
	} else {
		$connSource = get_dbConn_MySQL($sPathToSourceDB);
	}
}

$sAutoFieldName = null;
if (!empty($sTable) && !empty($connSource) && !empty(@$_POST["Upadate"])) {
	if ($boolSourseIsMySQL) {
		$sAutoFieldName = sx_getPrimaryKey_MySQL($sTable, $connSource);
	} else {
		$sAutoFieldName = sx_getPrimaryKey_Access($sTable, $connSource);
	}

	$radioGo = True;
	if (empty($sAutoFieldName)) {
		$radioGo = False;
	}

	$sGetRecordsWhere = null;
	if (intval($iGreaterThanID) > 0 && $radioGo) {
		$sGetRecordsWhere = " WHERE " . $sAutoFieldName . " > " . $iGreaterThanID;
		if (intval($iLesserThanID) > 0) {
			$sGetRecordsWhere = $sGetRecordsWhere . " AND " . $sAutoFieldName . " < " . $iLesserThanID;
		}
	} elseif (intval($iLesserThanID) > 0 and $radioGo) {
		$sGetRecordsWhere = " WHERE " . $sAutoFieldName . " < " . $iLesserThanID;
	}

	$aResults = array();
	if ($boolSourseIsMySQL) {
		$sql = "SELECT * FROM " . $sTable . $sGetRecordsWhere;
		$stmt = $conn->query($sql);

		$colcount = $stmt->columnCount();
		$arrMeta = array();
		for ($c = 0; $c < $colcount; $c++) {
			$meta = $stmt->getColumnMeta($c);
			$arrMeta[$c] = array($meta['name'], $meta['native_type'], @$meta['flags'][1]);
		}

		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$x = 0;
		foreach ($rs as $row) {
			$c = 0;
			foreach ($row as $fieldName => $fieldValue) {
				$aResults[$x][$c] = array($fieldName, $fieldValue, $arrMeta[$c][1], $arrMeta[$c][2]);
				$c++;
			}
			$x++;
		}
		$arrMeta = null;
	} else {
		$rs = new COM("ADODB.Recordset", null, CP_UTF8, null);
		$sql = "SELECT * FROM " . $sTable . $sGetRecordsWhere;
		$rs->Open($sql, $connSource, 3, 3);
		$num = $rs->Fields->Count;
		$x = 0;
		while (!$rs->EOF) {
			for ($i = 0; $i < $num; $i++) {
				$field = $rs->Fields[$i];
				$aResults[$x][] = array($field->Name, $field->Value, $field->Type, $field->Properties["IsAutoincrement"]->value);
			}
			$x++;
			$rs->MoveNext();
		}
		$rs = null;
	}



	/*
echo "<pre>";
var_export($aResults);
echo "</pre>";
exit;
*/

	if (is_array($aResults) && $radioGo) {
		$iRows = count($aResults);
		$iCells = count($aResults[0]);
		$sql = "UPDATE " . $sTable . " SET ";
		for ($c = 1; $c < $iCells; $c++) {
			if ($c > 1) {
				$sql .= ", ";
			}
			$sql .= $aResults[0][$c][0] . " = ?";
		}
		$sql .= " WHERE " . $sAutoFieldName . " = ?";

		/**
		 * Loop through the records of the source table
		 */
		for ($r = 0; $r < $iRows; $r++) {
			$iLoop = $aResults[$r][0][1];
			/**
			 * Check if current record ID exists in destination table
			 */
			$strSQL = "SELECT " . $sAutoFieldName . " FROM " . $sTable . " WHERE " . $sAutoFieldName . " = " . $iLoop;
			$rs = $conn->query($strSQL);
			$radioGo = False;
			if ($rs) {
				$radioGo = True;
			}
			$rs = null;
			$strSQL = null;
			/**
			 * Update current record in destination table
			 */
			if ($radioGo) {
				$arrValues = array();
				for ($c = 1; $c < $iCells; $c++) {
					$sxName = $aResults[$r][$c][0];
					$sxValue = $aResults[$r][$c][1];
					$sxType = $aResults[$r][$c][2];
					if ($sxType == 11) {
						$sxValue = sx_getBoolean($sxValue);
					} elseif ($sxType == 7 || $sxType == 135) {
						$sxValue = sx_getUniversalDate($sxValue);
					}
					$arrValues[] = $sxValue;
				}
				$arrValues[] = $iLoop;
				$stmt = $conn->prepare($sql);
				$stmt->execute($arrValues);
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Acces to SQL Update</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<style>
		td td {
			border-bottom: 1px solid #999;
		}
	</style>
</head>

<body>
	<h2>Update Tables from Source Database to Target (Current) Database</h2>
	<div class="maxWidth">
		<?php
		if (@$iRows > 0) { ?>
			<div class="message success">
				Successfull updated of <b><?= @$iCells ?> Fields</b> in <b> <?= @$iRows ?> Records</b>.
			</div>
		<?php } ?>
		<p>The Source and Target Tables are not <b>automatically</b> checked as to the compatibility of their field-names and field-types</p>
		<form method="POST" name="chooseTable" action="index.php">
			<h4>Select a Source Database - MS Access or MySQL</h4>
			<fieldset>
				<p> <input type="file" value="<?= @$sPathToSourceDB ?>" name="File"></p>
				<p>
				<div style="display: block; margin-bottom: 12px">
					Write the <b>Physical Path</b> to an Access Database or the <b>Name</b> of a Local MySQL Database:</div>
				<input style="width: 100%" type="text" value="<?= @$sPathToSourceDB ?>" name="PathToSourceDB">
				</p>
				<div class="alignRight"> <input type="submit" value="Load Source DB" name="LoadDB"></div>
			</fieldset>
			<h4>Select a Table to Update</h4>
			<fieldset>
				<table class="no_bg">
					<tr>
						<td>ID Greater then:</td>
						<td><input type="number" size="5" name="GreaterThanID" value="<?= $iGreaterThanID ?>"></td>
					</tr>
					<tr>
						<td>ID Lesser then:</td>
						<td><input type="number" size="5" name="LesserThanID" value="<?= $iLesserThanID ?>"></td>
					</tr>
					<tr>
						<td>Tables common in both Databases:</td>
						<td>
							<?php
							if (!empty($connSource)) {
								if ($boolSourseIsMySQL) {
									$arrTables = array_intersect(sx_getMySQLTables($connSource), sx_getMySQLTables($conn));
								} else {
									$arrTables = array_intersect(sx_getAccessTables($connSource), sx_getMySQLTables($conn));
								} ?>
								<select size="1" name="Table">
									<option value="">Select Table</option>
									<?php
									foreach ($arrTables as $Tables) {
										$slected = "";
										if ($Tables == $sTable) {
											$slected = " selected";
										} ?>
										<option<?= $slected ?> value="<?= $Tables ?>"><?= $Tables ?></option>
										<?php
									} ?>
								</select>
							<?php
							} else { ?>
								<p class="message warning">Select a Source Database</p>
							<?php } ?>
						</td>
					</tr>
				</table>
				<div class="alignRight"> <input type="submit" value="Compare Source & Target Tables" name="CompareFields"></div>
			</fieldset>

			<?php
			$arrSourceFields = array();
			$arrTargetFields = array();
			if (!empty($sTable) && !empty($connSource) && (!empty(@$_POST["CompareFields"]) || !empty(@$_POST["Upadate"]))) {
				if ($boolSourseIsMySQL) {
					$arrSourceFields = sx_getFieldAttributes_MySQL($sTable, $connSource);
				} else {
					$arrSourceFields = sx_getFieldAttributes_Access($sTable, $connSource);
				}
				$arrTargetFields = sx_getFieldAttributes_MySQL($sTable, $conn);
			}
			if (!empty($arrSourceFields)) { ?>
				<p>Checke the compatibility of field-names and field-types between Source and Target Database</p>
				<fieldset>
					<table class="no_bg">
						<tr>
							<td>
								<h4>Source Table</h4>
								<table>
									<tr>
										<td>Include</td>
										<td>Name</td>
										<td>Type</td>
										<td>Key</td>
									</tr>
									<?php
									$iCount = count($arrSourceFields);
									for ($f = 0; $f < $iCount; $f++) { ?>
										<tr>
											<td><input type="checkbox" name="<?= $arrSourceFields[$f][0] ?>" value="Yes">
											<td><?= $arrSourceFields[$f][0] ?></td>
											<td><?= $arrSourceFields[$f][1] ?></td>
											<td><?= $arrSourceFields[$f][2] ?></td>
										</tr>
									<?php } ?>
								</table>
							</td>
							<td style="padding-left: 20px;">
								<h4>Target Table</h4>
								<table>
									<tr>
										<td>Name</td>
										<td>Type</td>
										<td>Key</td>
									</tr>
									<?php
									$iCount = count($arrTargetFields);
									for ($f = 0; $f < $iCount; $f++) { ?>
										<tr>
											<td><?= $arrTargetFields[$f][0] ?></td>
											<td><?= $arrTargetFields[$f][1] ?></td>
											<td><?= $arrTargetFields[$f][2] ?></td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			<?php }
			?>

			<p class="alignRight"> <input type="submit" value="Upadate" name="Upadate"></p>

		</form>
	</div>

</body>

</html>