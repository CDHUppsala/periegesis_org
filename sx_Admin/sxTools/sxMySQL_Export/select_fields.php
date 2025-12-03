<?php

$arr_FieldNames = array();
$arr_FieldTypes = array();
$strAutoField = sx_GetPrimaryKey($strDBTable);

$strSQL = "SELECT * FROM $strDBTable LIMIT 1";
$stmt = $conn->query($strSQL);
$iCountCol = $stmt->columnCount();
for ($c = 0; $c < $iCountCol; $c++) {
	$meta = $stmt->getColumnMeta($c);
	$arr_FieldNames[] = $meta['name'];
	$arr_FieldTypes[] = $meta['native_type'];
}
$stmt = null;

/**
 * Define the selected table fields and their types
 */
$arr_ExportedFields = array();
$arr_ExportedFieldsTypes = array();
$radioSelectAllFields = false;
$radioExportDisable = true;
/**
 * When all table fields are selected
 */
if (isset($_POST["SelectAllFields"]) && $_POST["SelectAllFields"] == "Yes") {
	$arr_ExportedFields = $arr_FieldNames;
	$arr_ExportedFieldsTypes = $arr_FieldTypes;
	$radioSelectAllFields = true;
	$radioExportDisable = false;
} elseif (isset($_POST["TableFields"])) {
	$arr_ExportedFields = $_POST["TableFields"];
	$radioExportDisable = false;
}
?>

<h2>Select the Table Fields to be Exported</h2>
<form method="POST" name="chooseFields" action="">
	<input type="hidden" name="HiddenDBTable" value="<?= $strDBTable ?>" />
	<fieldset>
		<table>
			<tr>
				<th>Table Fields</th>
				<th>Select</th>
				<th>Data Type</th>
			</tr>
			<?php
			for ($i = 0; $i < $iCountCol; $i++) {
				$xName = $arr_FieldNames[$i];
				$xType = $arr_FieldTypes[$i];
				$radioCheckThisField = false;
				if ($radioSelectAllFields) {
					$radioCheckThisField = true;
				} elseif (is_array($arr_ExportedFields) && in_array($xName, $arr_ExportedFields)) {
					$arr_ExportedFieldsTypes[] = $xType;
					$radioCheckThisField = true;
				}
				$checkBox = "";
				if ($radioCheckThisField) {
					$checkBox = "checked";
				} ?>
				<tr>
					<td><?= $xName ?> </td>
					<td>
						<input type="checkbox" name="TableFields[]" value="<?= $xName ?>" <?= $checkBox ?>><?= @$_POST[$xName] ?>
					</td>
					<td><?= $xType ?>
						<?php if ($xName == $strAutoField) {
							echo '| <b style="color: #d60; cursor: pointer" title="Primary Key">PK</b>';
						} ?>
					</td>
				</tr>
			<?php
			}

			$strChecked = "";
			if ($radioSelectAllFields) {
				$strChecked = "checked ";
			} ?>
		</table>
		<input type="hidden" name="DataTypes" value="<?= implode(",", $arr_ExportedFieldsTypes) ?>">
	</fieldset>
	<fieldset class="row flex_align_center">
		<p>Select all Table Fields: <input <?= $strChecked ?>type="checkbox" name="SelectAllFields" value="Yes"></p>
		<input type="submit" value="Select Fields" id="select" name="Select Fields">
	</fieldset>
	<fieldset>
		<table classs="no_bg">
			<tr>
				<td>Τύπος Αρχείου:</td>
				<td>
					<input type="radio" name="ExportType" value="xml" checked>XML
					<input type="radio" name="ExportType" value="xsd">XSD
					<input type="radio" name="ExportType" value="json">JSON
					<input type="radio" name="ExportType" value="csv">CSV
				</td>
			</tr>
			<tr>
				<td>Statement WHERE: </td>
				<td><input type="text" name="Where" value="" placeholder="Field1 > X AND Field2 = Y" size="42"></td>
			</tr>
			<tr>
				<td>Statement ORDER BY: </td>
				<td><input type="text" name="OrderBy" placeholder="Field1 DESC, Field2 ASC" value="" size="42"></td>
			</tr>
			<tr>
				<td>Statement LIMIT: </td>
				<td><input type="text" name="Limit" value="" placeholder="Default or 0 = No Limit" size="42"></td>
			</tr>
		</table>
	</fieldset>
	<div class="msgSuccess display_none" id="SaveMessage"></div>
	<fieldset class="row flex_align_center">
		<label>Save on Remote Server:
			<input type="radio" name="SaveType" value="remote" checked></label>
		<label>Save and Download:
			<input type="radio" name="SaveType" value="download"></label>
		<?php
		$strDisabled = "";
		if ($radioExportDisable) {
			$strDisabled = "disabled ";
		} ?>
		<p><input <?= $strDisabled ?>type="submit" value="Export Table" id="export" name="Export Table"></p>
	</fieldset>
</form>