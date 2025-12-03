<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include __DIR__ . "/functionsTableName.php";

if (isset($request_Table) && $request_Table == "sx_config_tables") {
	$request_Table = "";
	$_SESSION["Table"] = "";
}
//## Get form inputs and add selections to the config Table
if (isset($_GET["configurFields"]) && !empty($request_Table)) {

	//## Get the Form Inputs as an ordered array
	$arrFields = array();
	$arrFields = sx_getTableFields($request_Table);

	if (is_array($arrFields)) {

		//Get the form imputs and put them into arrays
		$arrRequiredFields = array();
		$arrUpdateableFields = array();
		$arrFieldRelations = array();

		$iCount = count($arrFields);
		for ($i = 0; $i < $iCount; $i++) {
			$xName = $arrFields[$i];
			/**
			 * 1. Create an array with required fields
			 */
			if (isset($_POST["required" . $xName]) && !empty($_POST["required" . $xName])) {
				$arrRequiredFields[] = $xName;
			}

			/**
			 * 2. Create an array with field relations
			 */
			if (isset($_POST["relationType" . $xName]) && isset($_POST["relationQuary" . $xName])) {
				$strRelation_Query = trim($_POST["relationQuary" . $xName]);
				$strRelation_Type = $_POST["relationType" . $xName];
				if ($strRelation_Type > 0 && !empty($strRelation_Query)) {
					$strParentClass = "";
					$arr_Temp = array();
					if ($strRelation_Type == 30 || $strRelation_Type == 300) {
						$arrTemp = explode(";", $strRelation_Query);
						$aCount = count($arrTemp);
						$arr_Temp[] = $strRelation_Type;
						for ($a = 0; $a < $aCount; $a++) {
							if ($a == $aCount - 1) {
								$arr_Temp[] = trim($arrTemp[$a]);
							} else {
								$arr_Temp[] = trim($arrTemp[$a]);
							}
						}
					} elseif ($strRelation_Type == 10) {
						$arrTemp = explode("ORDER BY", $strRelation_Query);
						$arr_Temp = array($strRelation_Type, trim($arrTemp[1]), $strRelation_Query);
					} elseif ($strRelation_Type == 100 && strpos($strRelation_Query, ";") > 0) {
						// 2 queries, one for relations and one for replacing IDs with names
						$arrFirst = explode(";", $strRelation_Query);
						$strNames_Query = trim($arrFirst[1]);
						$arrTemp = explode("ORDER BY", $arrFirst[0]);
						$arr_Temp = array($strRelation_Type, trim($arrTemp[1]), trim($arrFirst[0]), $strNames_Query);
					} else {
						$arr_Temp = array($strRelation_Type, "", $strRelation_Query);
					}
					if (is_array($arr_Temp) && !empty($arr_Temp)) {
						$arrFieldRelations[$xName] = $arr_Temp;
					}
				}
			}


			/**
			 * 3. Create an array with updateable fields
			 * The same field name can include the Default Type 5 (set to false) and one of the RADIAL Updateable Types (6-9 and 50 for Not Used))
			 * So, add a Suffix to the Name for the Default Type 5 and use is to get the Default Value
			 */
			if (isset($_POST["defaultFalse" . $xName]) && $_POST["defaultFalse" . $xName] != 0) {
				$arrUpdateableFields[$xName . "_Default"] = $_POST["defaultFalse" . $xName];
			}

			if (isset($_POST["updateable" . $xName]) && $_POST["updateable" . $xName] != 0) {
				$arrUpdateableFields[$xName] = $_POST["updateable" . $xName];
			}

			if (isset($_POST["fieldNotInUse" . $xName]) && $_POST["fieldNotInUse" . $xName] != 0) {
				$arrUpdateableFields[$xName] = $_POST["fieldNotInUse" . $xName];
			}

			if (isset($_POST["updateableInteger" . $xName]) && $_POST["updateableInteger" . $xName] != 0) {
				$arrUpdateableFields[$xName] = $_POST["updateableInteger" . $xName];
			}
		}

		/**
		 * Free add and update relations
		 */

		$arrAddUppdateRelated = array();
		if (isset($_POST["AddToTable"]) && !empty($_POST["AddToTable"])) {
			$strTemp = $_POST["AddToTable"];
			$arrTemp = explode(";", $strTemp);
			$strKey = trim(end($arrTemp));
			$arrAddUppdateRelated["AddToTable"] = array($strKey, $strTemp);
		}
		if (isset($_POST["UpdateTable"]) && !empty($_POST["UpdateTable"])) {
			$strTemp = $_POST["UpdateTable"];
			$arrTemp = explode(";", $strTemp);
			$strKey = trim(end($arrTemp));
			$arrAddUppdateRelated["UpdateTable"] = array($strKey, $strTemp);
		}
		/**
		 * Convert arrays to json
		 */
		$json_requiredFields = "";
		if (!empty($arrRequiredFields)) {
			$json_requiredFields = json_encode($arrRequiredFields, JSON_UNESCAPED_UNICODE);
		}
		$json_UpdateableFields = "";
		if (!empty($arrUpdateableFields)) {
			$json_UpdateableFields = json_encode($arrUpdateableFields, JSON_UNESCAPED_UNICODE);
		}
		$json_FieldRelations = "";
		if (!empty($arrFieldRelations)) {
			$json_FieldRelations = json_encode($arrFieldRelations, JSON_UNESCAPED_UNICODE);
		}

		/*

		echo "<pre>";
		print_r($arrFieldRelations);
		echo "</pre>";
		echo $json_FieldRelations;
		exit;
*/



		$json_AddUppdateRelated = "";
		if (!empty($arrAddUppdateRelated)) {
			$json_AddUppdateRelated = json_encode($arrAddUppdateRelated, JSON_UNESCAPED_UNICODE);
		}

		/**
		 * Add to or Update the sx_config_tables Table
		 */
		$strSQL = "SELECT configID
		FROM sx_config_tables
		WHERE ConfigTableName = '" . $request_Table . "'";
		$rs = $conn->query($strSQL)->fetch(PDO::FETCH_ASSOC);
		$radioExists = false;
		if ($rs) {
			$radioExists = true;
		}
		$rs = null;

		if ($radioExists) {
			$sql = "UPDATE sx_config_tables
		SET
			RequiredFields = ?,
			UpdateableFields = ?,
			RelatedFields = ?,
			AddUppdateRelated = ?
		WHERE configTableName = ? ";
			/*
            echo "<br>". $json_requiredFields . "<hr>" . $json_UpdateableFields . "<hr>" . $json_FieldRelations . "<hr>" . $json_AddUppdateRelated . "<hr>" . $request_Table;
            exit;
             */
			$rs = $conn->prepare($sql);
			$rs->execute([$json_requiredFields, $json_UpdateableFields, $json_FieldRelations, $json_AddUppdateRelated, $request_Table]);

			header("location: configRelatedFields.php");
			exit();
		} else {
			$sql = "INSERT INTO sx_config_tables (ConfigTableName,RequiredFields,UpdateableFields,RelatedFields,AddUppdateRelated)
		VALUES (?,?,?,?,?)";
			$rs = $conn->prepare($sql);
			$rs->execute([$request_Table, $json_requiredFields, $json_UpdateableFields, $json_FieldRelations, $json_AddUppdateRelated]);

			header("location: configRelatedFields.php");
			exit();
		}
	}
}
/**
 * Do not load table configuration arrays when updating the configuration
 */

include PROJECT_ADMIN . "/configFunctions.php";

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Configure Related Fields</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<script src="../js/jq/jquery.min.js"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js"></script>
	<?php
	include "js/jsTablesAndFields.php";
	?>
	<script src="<?php echo sx_ADMIN_DEV ?>config/js/jsRelations.js"></script>
</head>

<body class="body">

	<header id="header">
		<h2>TABLE: <?= ($request_Table) ?><br>Configuration of Field Relations</h2>
		<form method="POST" name="chooseTable" action="configRelatedFields.php?chooseTable=yes">
			<select size="1" name="Table">
				<option value="">Select Table</option>
				<?php
				$result = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
				$rs = $result->fetchAll(PDO::FETCH_NUM);
				foreach ($rs as $table) {
					$loopTable = $table[0];
					$strSelected = "";
					if ($loopTable == $request_Table) {
						$strSelected = " selected ";
					} ?>
					<option<?= $strSelected ?> value="<?= $loopTable ?>"><?= $loopTable ?></option>
					<?php
					$rs = null;
				} ?>
			</select>
			<input class="button" type="submit" value="»»»" name="submit">
		</form>
		<div class="margin_right_auto">
			<button class="button jqHelpButton" data-id="ConfigRelatedFields">HELP</button>
			<button class="button jqHelpButton" data-id="viewRelatedFields">VIEW RELATED</button>
		</div>
	</header>

	<?php if (empty($request_Table)) { ?>
		<h1>Please select a table from the above list.</h1>
	<?php
	} else { ?>
		<section>
			<form method="POST" name="configurFields" action="configRelatedFields.php?configurFields=yes">
				<table class="tbl_borders">
					<tr>
						<th style="white-space:nowrap">Field Name and Type</th>
						<th>Type of Relation</th>
						<th>SQL Search/Field Values</th>
					</tr>
					<?php
					$arrFieldNames = array();
					$arrFieldTypes = array();

					$strSQL = "SELECT * FROM " . $request_Table . " LIMIT 1";
					$stmt = $conn->query($strSQL);
					$iColumns = $stmt->columnCount();
					for ($i = 0; $i < $iColumns; $i++) {
						$meta = $stmt->getColumnMeta($i);
						$xName = $meta["name"];
						$xType = $meta["native_type"];
						$arrFieldNames[] = $xName;
						$arrFieldTypes[] = $xType;
					}
					$stmt = null;

					$strPrimaryKeyName = sx_getPrimaryKeyName($request_Table);

					for ($i = 0; $i < $iColumns; $i++) {
						$xName = $arrFieldNames[$i];
						$xType = $arrFieldTypes[$i];

						$xAuto = false;
						if ($strPrimaryKeyName == $xName) {
							$xAuto = true;
						}

						if (($xType == "INTEGER"
							|| $xType == "BIGINT"
							|| $xType == "LONG"
							|| $xType == "LONGLONG"
							|| $xType == "INT24") && $xAuto != true) {
							$intOption = sx_getRelationType($xName) ?>
							<tr>
								<td>
									<?= $xName ?><br>
									<?= $xType ?>
									<p>
										Required: <input type="checkbox" name="required<?= $xName ?>" value="ON" <?= sx_checkRequiredFields($xName) ?>>
									</p>
								</td>
								<td>
									<select size="1" name="relationType<?= $xName ?>" onChange="return(getTableOptions(this.value,'setupRelations','relationQuary<?= $xName ?>','<?= $xName ?>','<?= $request_Table ?>'));">
										<option value="0" <?php if ($intOption == 0) { ?>selected<?php } ?>>Choose</option>
										<option value="1" <?php if ($intOption == 1) { ?>selected<?php } ?>>1 Get From Related</option>
										<option value="2" <?php if ($intOption == 2) { ?>selected<?php } ?>>2 Add To Related</option>
										<option value="10" <?php if ($intOption == 10) { ?>selected<?php } ?>>10 Get SubCateories</option>
										<option value="100" <?php if ($intOption == 100) { ?>selected<?php } ?>>100 Exlud-Include SubCateories</option>
									</select>
									<p>
										<?php
										$strCheckInput = "";
										if (sx_getUpdateableFieldType($xName) == 50) {
											$strCheckInput = "checked";
										} ?>
										<input type="checkbox" name="fieldNotInUse<?= $xName ?>" value="50" <?= $strCheckInput ?>> 50 Field not in Use
									</p>
								</td>
								<td>
									<textarea rows="5" id="relationQuary<?= $xName ?>" name="relationQuary<?= $xName ?>" cols="48"><?= sx_getRelatedFields($xName) ?></textarea>
								</td>
							</tr>
						<?php
						} elseif (
							$xType == "NUMERIC"
							|| $xType == "DECIMAL"
							|| $xType == "NEWDECIMAL"
							|| $xType == "SHORT"
							|| $xType == "SMALLINT"
							|| $xType == "FLOAT"
							|| $xType == "REAL"
							|| $xType == "DOUBLE"
						) { ?>
							<tr>
								<td>
									<?= $xName ?><br>
									<?= $xType ?>
									<p>
										Required: <input type="checkbox" name="required<?= $xName ?>" value="ON" <?= sx_checkRequiredFields($xName) ?>>
									</p>
								</td>
								<td>
									<p>
										<?php
										$strCheckInput = "";
										if (sx_getUpdateableFieldType($xName) == 50) {
											$strCheckInput = "checked";
										} ?>
										<input type="checkbox" name="fieldNotInUse<?= $xName ?>" value="50" <?= $strCheckInput ?>> 50 Field not in Use
										<?php
										$optionChecked = "";
										if (sx_getUpdateableFieldType($xName) == 13) {
											$optionChecked = "checked";
										} ?>
										<br><input type="checkbox" name="updateableInteger<?= $xName ?>" value="13" <?= $optionChecked ?>> 13 Updateable in list
									</p>
								</td>
								<td> </td>
							</tr>

						<?php
						} elseif ($xType == "VAR_STRING" || $xType == "STRING" || $xType == "CHARACTER") {
							$intOption = sx_getRelationType($xName);
						?>
							<tr>
								<td>
									<?= $xName ?><br>
									<?= $xType ?>
									<p>
										Required: <input type="checkbox" name="required<?= $xName ?>" value="ON" <?= sx_checkRequiredFields($xName) ?>>
									</p>
								</td>
								<td>
									<select size="1" name="relationType<?= $xName ?>" onChange="return(getTableOptions(this.value,'setupRelations','relationQuary<?= $xName ?>','<?= $xName ?>','<?= $request_Table ?>'));">
										<option value="0" <?php if ($intOption == 0) { ?>selected<?php } ?>>Choose</option>
										<option value="1" <?php if ($intOption == 1) { ?>selected<?php } ?>>1 Get From Related</option>
										<option value="3" <?php if ($intOption == 3) { ?>selected<?php } ?>>3 Get Distinct Values</option>
										<option value="30" <?php if ($intOption == 30) { ?>selected<?php } ?>>30 Get Distinct Groups</option>
										<option value="300" <?php if ($intOption == 300) { ?>selected<?php } ?>>300 Get Distinct SubGroups</option>
										<option title="Define alternative values. More then 3 appear in Select Drop Down Menu, else, as Radial Inputs" value="4" <?php if ($intOption == 4) { ?>selected<?php } ?>>4 Set Radial/Select Values</option>
										<option value="40" <?php if ($intOption == 40) { ?>selected<?php } ?>>40 Set Box Values</option>
									</select>
									<p> <?php
										$strCheckInput = "";
										if (sx_getUpdateableFieldType($xName) == 50) {
											$strCheckInput = "checked";
										} ?>
										<input type="checkbox" name="fieldNotInUse<?= $xName ?>" value="50" <?= $strCheckInput ?>> 50 Field not in Use
									</p>
								</td>
								<td>
									<?php
									$loopText = "";
									if ($intOption == 30 || $intOption == 300) {
										$loopText = sx_getRelatedFieldsSlice($xName);
									} else {
										$loopText = sx_getRelatedFields($xName);
									} ?>
									<textarea rows="5" id="relationQuary<?= $xName ?>" name="relationQuary<?= $xName ?>" cols="48"><?= $loopText ?></textarea>
								</td>
							</tr>
						<?php
						} elseif ($xType == "TINY" || $xType == "BOOLEAN") {

							$strCheckDefaultFalse = "";
							$strCheckUpdate6 = "";
							$strCheckUpdate7 = "";
							$strCheckUpdate8 = "";
							$strCheckUpdate9 = "";
							$strCheckUpdate0 = "";
							$strCheckUpdate50 = "";
							$intUpdateValue = sx_getUpdateableFieldType($xName);

							if (sx_getUpdateableFieldType($xName . "_Default") == 5) {
								$strCheckDefaultFalse = "checked";
							}
							if ($intUpdateValue == 6) {
								$strCheckUpdate6 = "checked";
							}
							if ($intUpdateValue == 7) {
								$strCheckUpdate7 = "checked";
							}
							if ($intUpdateValue == 8) {
								$strCheckUpdate8 = "checked";
							}
							if ($intUpdateValue == 9) {
								$strCheckUpdate9 = "checked";
							}
							if ($intUpdateValue == 50) {
								$strCheckUpdate50 = "checked";
							}
							if ($intUpdateValue == 0) {
								$strCheckUpdate0 = "checked";
							} ?>
							<tr>
								<td>
									<?= $xName ?><br>
									<?= $xType ?>
									<p>
										Required: <input type="checkbox" name="required<?= $xName ?>" value="ON" <?= sx_checkRequiredFields($xName) ?>>
									</p>
								</td>
								<td>
									<input type="checkbox" name="defaultFalse<?= $xName ?>" value="5" <?= $strCheckDefaultFalse ?>> 5 Default value: No
								<td>
									<input type="radio" name="updateable<?= $xName ?>" value="6" <?= $strCheckUpdate6 ?>> 6 YES Selected
									&amp; Updateable in List<br>
									<input type="radio" name="updateable<?= $xName ?>" value="7" <?= $strCheckUpdate7 ?>> 7 NO Selected
									&amp; Updateable in List<br>
									<input type="radio" name="updateable<?= $xName ?>" value="8" <?= $strCheckUpdate8 ?>> 8 YES/NO Selected
									&amp; Updateable in List<br>
									<input type="radio" name="updateable<?= $xName ?>" value="9" <?= $strCheckUpdate9 ?>> 9 YES/NO Updateable
									&amp; not Selected in List<br>
									<input type="radio" name="updateable<?= $xName ?>" value="0" <?= $strCheckUpdate0 ?>> Not Updateable<br>
									<input type="radio" name="updateable<?= $xName ?>" value="50" <?= $strCheckUpdate50 ?>> 50 Field not in Use
								</td>
							</tr>
						<?php
						} else { ?>
							<tr>
								<td>
									<?= $xName ?><br>
									<?php
									echo $xType;
									if ($xAuto == true) {
										echo " | AUTO";
									}
									if (!$xAuto) { ?>
										<p>
											Required: <input type="checkbox" name="required<?= $xName ?>" value="ON" <?= sx_checkRequiredFields($xName) ?>>
										</p>
									<?php } ?>
								</td>
								<td>
									<?php
									$strCheckInput = "";
									if (trim(sx_getUpdateableFieldType($xName)) == 50) {
										$strCheckInput = "checked";
									}
									?>
									<input type="checkbox" name="fieldNotInUse<?= $xName ?>" value="50" <?= $strCheckInput ?>> 50 Field not in Used
									<?php
									if ($xType == "TIME") {
										$optionChecked = "";
										if (trim(sx_getUpdateableFieldType($xName)) == "TIME") {
											$optionChecked = "checked";
										}
									?>
										<br><input type="checkbox" name="updateable<?= $xName ?>" value="TIME" <?= $optionChecked ?>> TIME Updateable in List
									<?php
									} elseif ($xType == "DATE") {
										$optionChecked = "";
										if (trim(sx_getUpdateableFieldType($xName)) == "DATE") {
											$optionChecked = "checked";
										}
									?>
										<br><input type="checkbox" name="updateable<?= $xName ?>" value="DATE" <?= $optionChecked ?>> DATE Updateable in List
									<?php
									}
									?>
								</td>
								<td>&nbsp;</td>
							</tr>
					<?php
						}
					} ?>
					<tr>
						<td colspan="2" valign="top">
							<div style="float: right"><input type="button" value="Set Relations" onClick="return(getTableOptions(11,'setupRelations','AddToTable','Add to Related','<?= $request_Table ?>'));"></div>
							<div>11. Add records in related table</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both">
								<code>
									SELECT RelatedID_FieldName FROM RelatedTable<br>
									WHERE RelatedField1 = ThisField1<br>
									AND RelatedField2 = ThisField2<br>
									INSERT INTO RelatedTable (RelatedField1,RelatedField2)<br>
									VALUES(ThisField1, ThisField2);<br>
									RelatedID_FieldName; ThisID_FieldName
								</code>
							</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both">
								<code>
									SELECT AuthorID FROM text_authors<br>
									WHERE FirstName = ? AND LastName = ?;<br>
									INSERT INTO text_authors (FirstName,LastName)<br>
									VALUES(?,?);<br>
									FirstName;LastName;AuthorID
								</code>
							</div>
						</td>
						<td>
							<textarea rows="10" id="AddToTable" name="AddToTable" cols="48"><?= sx_getUnboundedRelations("AddToTable") ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="top">
							<div style="float: right; margin-left: 25px;"><input type="button" value="Set Relations" onClick="return(getTableOptions(12,'setupRelations','UpdateTable','Update Related','<?= $request_Table ?>'));"></div>
							<div>12. Update records in related tables</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both">
								<code>
									UPDATE RelatedTable <br>
									SET FieldInRelatedTable = FieldInThisTable <br>
									WHERE RelatedID_FieldName = ThisID_FieldName;
								</code>
							</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both">
								<code>
									UPDATE themes<br>
									SET LastInDate = ?<br>
									WHERE ThemeID = ?; PublishedDate; ThemeID
								</code>
							</div>
						</td>
						<td>
							<textarea rows="10" id="UpdateTable" name="UpdateTable" cols="48"><?= sx_getUnboundedRelations("UpdateTable") ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div>30 and 300. When GroupID is in a Related Table,
								but Categories and Subcategories are fields in Current Table</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div>30. Get DISTINCT Chapters of a Project ID<br>JSON value in DB:</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both; max-width: 500px; overflow: auto;">
								<code>
									"ChapterName":["30","ProjectID","SortingChapters",
									"SELECT DISTINCT ChapterName, SortingChapters
									FROM reports
									WHERE ProjectID = ?
									ORDER BY ProjectID DESC, SortingChapters DESC"]
								</code>
							</div>
						</td>
						<td>
							<div>Insert Relations of Type 30 as follows:</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both; max-width: 500px; overflow: auto;">
								<code>
									ProjectID;SortingChapters;SELECT DISTINCT ChapterName, SortingChapters FROM reports WHERE ProjectID = ? ORDER BY ProjectID DESC, SortingChapters DESC
								</code>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div>300. Get DISTINCT Subchapters (sections) of a Chapter<br>JSON value in DB:</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both; max-width: 500px; overflow: auto;">
								<code>
									"SubChapterName":["300","ProjectID","ChapterName","SortingSubChapters",
									"SELECT DISTINCT SubChapterName, SortingSubChapters
									FROM reports
									WHERE ProjectID = ? AND ChapterName = ?
									ORDER BY SortingSubChapters DESC"]
								</code>
							</div>
						</td>
						<td>
							<div>Insert Relations of Type 300 as follows:</div>
							<div style="background: #fff; padding: 8px 10px; margin-top: 16px; clear: both; max-width: 500px; overflow: auto;">
								<code>
									ProjectID; ChapterName; SortingSubChapters; SELECT DISTINCT SubChapterName, SortingSubChapters FROM reports WHERE ProjectID = ? AND ChapterName = ? ORDER BY SortingSubChapters DESC
								</code>
							</div>
						</td>
					</tr>

				</table>
				<br><input type="submit" value="Save Relations" name="submit"><br><br>
			</form>
		</section>
	<?php
	} ?>
	<div class="sxHelp text" id="viewRelatedFields" style="display: none">
		<h2>Fields in sx_config_tables for Table: <?= $request_Table ?></h2>
		<h3>Alias Names of Fields:</h3>
		<pre><?php print_r(@$arrAliasNames) ?></pre>
		<h3>Selected Fields:</h3>
		<pre><?php print_r(@$arrSelectedFields) ?></pre>
		<h3>Fields for Default Ordering:</h3>
		<p><?= $str_OrderByField ?></p>
		<h3>Required Fields:</h3>
		<pre><?php print_r(@$arrRequiredFields)
				?></pre>
		<h3>Updateable Fields:</h3>
		<pre><?php print_r(@$arrUpdateableFields)
				?></pre>
		<h3>Related Fields:</h3>
		<pre><?php print_r(@$arrFieldRelations)
				?></pre>
		<h3>Free Field Relations:</h3>
		<pre><?php print_r(@$arrAddUppdateRelated)
				?></pre>
		<div style="text-align: right; margin: 5">
			<input class="button jqHelpButton" data-id="viewRelatedFields" type="button" value="Close">
		</div>
	</div>
	<div style="clear: both">
		<?php include "sxHelpFiles/helpRelatedFields.php"; ?>
	</div>

	<div id="floatdiv" style="display: none;">
		<div style="text-align: right; padding: 3px 0; font-size: 0.8em">
			<button class="floatLeft" onClick="floatingMenu.hide(300)">Close</button>
			<button title="move upper left" onClick="move_upper_left()">UL</button>
			<button title="move lower left" onClick="move_lower_left()">LL</button>
			<button title="move lower right" onClick="move_lower_right()">LR</button>
			<button title="move upper right" onClick="move_upper_right()">UR</button>
		</div>
		<div id="setupRelations">
			&nbsp;
		</div>
	</div>

	<script language="JavaScript" src="<?php echo sx_ADMIN_DEV ?>config/js/jsFloating.js"></script>

</body>

</html>