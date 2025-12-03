<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

set_time_limit(600);

include __DIR__ . "/dbconn.php";
include __DIR__ . "/functions.php";

$arrTables = null;
if ($boolSourceMySQL) {
	$arrTables = sx_getMySQLTables($connSourceMySQL);
} else {
	$arrTables = sx_getAccessTables($connSourceAccess);
}

$arrTargetTables = null;
if (!empty($strConnectToTargetDB)) {
	if ($boolTargetMySQL) {
		$arrTargetTables = sx_getMySQLTables($connTargetMySQL);
	} else {
		$arrTargetTables = sx_getAccessTables($connTargetAccess);
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>You must manually set the update</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<link rel="stylesheet" href="css.css">
	<script src="../../js/jq/jquery.min.js"></script>
	<script>
		var $sx = jQuery.noConflict();
		$sx(document).ready(function() {
			$sx("#selectall").click(function() {
				$sx("#jgTables [type='checkbox']").prop("checked", "checked");
			});
			$sx("#deselectall").click(function() {
				$sx("#jgTables [type='checkbox']").prop("checked", "");
			});
			$sx("#interval").click(function() {
				var iFrom = parseInt($sx("#From").val(), 10);
				var iTo = parseInt($sx("#To").val(), 10) + 1;
				var i;
				for (i = iFrom; i < (iTo); i++) {
					$sx("#table_" + i).prop("checked", "checked");
				};
			});
			var iTables = <?= count($arrTables) ?>;
			$sx("#checkedInterval").click(function() {
				var i, c_From;
				for (i = 0; i < (iTables); i++) {
					if ($sx("#table_" + i).is(":checked")) {
						c_From = i + 1;
						break;
					};
				};
				for (i = c_From; i < (iTables); i++) {
					if (!$sx("#table_" + i).is(":checked")) {
						$sx("#table_" + i).prop("checked", "checked");
					} else {
						break;
					}
				};
			});
			$sx(".tipsBG").on("mouseenter", function() {
				$sx(this).find(".tips").delay(50).show(300).stop();
			}).on("mouseleave", function() {
				$sx(this).find(".tips").delay(50).hide(300).stop();
			})
		});
	</script>

</head>

<body>
	<?php
	//dim $strActionType;
	$strActionType = @$_POST["ActionType"];
	?>
	<form name="TableList" action="<?= $_SERVER["ORIG_PATH_INFO"] ?>" method="POST">
		<div class="container">
			<div class="third">
				<h2>Transfer Table Records: Acceess or MySQL</h2>
				<?php
				$strCecked = "";
				if ($strActionType == "ViewTables") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="ViewTables">
					<span <?= $strCecked ?>><b>Compare</b> Table Records between Source and Target Database</span>
					<span class="tipsBG">[?]
						<span class="tips">
							- You can compare only the Records of one Table each time.<br>
							- The records from the First Checked Table are shown.
						</span>
					</span>
				</p>
				<?php
				$strCecked = "";
				if ($strActionType == "UpdateSelectTables") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="UpdateSelectTables">
					<span <?= $strCecked ?>><b>Update</b> Table Records from Source to Target Database</span>
					<span class="tipsBG">[?]
						<span class="tips">
							Records are Updated only if their ID Exists in Target Table.<br>
						</span>
					</span>
				</p>
				<?php
				$strCecked = "";
				if ($strActionType == "AddSelectTables") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="AddSelectTables">
					<span <?= $strCecked ?>><b>Add</b> Table Records from Source to Target Database</span>
					<span class="tipsBG">[?]
						<span class="tips">
							Records are Added only if their ID Does Not Exists in Target Table.<br><br>
							Notice that the IDs of added records might differ from the original as they
							usually are of the auto-increment type.
						</span>
					</span>
				</p>
				<p style="margin-left: 1.2rem"><input type="checkbox" name="IncludePK" value="Yes">
					<span <?= $strCecked ?>>Include <b>Primary Key</b> with <b>Add</b> Table Records</span>
					<span class="tipsBG">[?]
						<span class="tips">
							Mark this checkbox if you like to keep the same IDs of the Primary Key (Must to be the <b>First</b> Table Field).<br>
							This is important if you add a classification table and the continuety of IDs is broken.
						</span>
					</span>
				</p>
				<p style="margin-left: 1.2rem;"><input size="6" type="text" name="StartInsertFromID" value="">
					<span <?= $strCecked ?>>Start Inserting from this <b>Primary Key ID</b></span>
					<span class="tipsBG">[?]
						<span class="tips">
							Big tables might consume the tume-out limit for scripts. Note the last added ID
							and restart insering from the next ID
						</span>
					</span>
				</p>
			</div>

			<div class="third">
				<h2>Field Descriptions: Source and Target Database</h2>
				<?php
				$strCecked = "";
				if ($strActionType == "ViewDescription") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="ViewDescription">
					<span <?= $strCecked ?>><b>Compare</b> Fields of checked Source Tables to Target Tables</span>
				</p>
				<?php
				$strCecked = "";
				if ($strActionType == "AddDescription") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="AddDescription">
					<span <?= $strCecked ?>><b>Add</b> Field Descriptions from Checked Source Tables to Target Tables</span>
					<span class="tipsBG">[?]
						<span class="tips">
							Only Fields that contain Descriptions are added to and Modify Tables in the Target Database.<br>
						</span>
					</span>
				</p>

				<h2><span class="color">Clear Checked option</span></h2>
				<p><input checked type="radio" name="ActionType" value="DoNothing"> Clear - No <b>Transfer</b> Action will be taken with Submit</p>

			</div>

			<div class="third">
				<h2>Create Database & Tables: Acceess to MySQL</h2>
				<?php
				$strCecked = "";
				if ($strActionType == "CreateDatabase") {
					$strCecked = 'class="red" ';
				} ?>
				<p><input type="radio" name="ActionType" value="CreateDatabase">
					<span <?= $strCecked ?>><b>Create</b> a New MySQL Database if Not Exists</span>
					<span class="tipsBG">[?]
						<span class="tips">
							- You can only create MySQL Schemas in the default MySQL Database, if you have write permissions.<br>
							- Copy the name of the New Database in the field bellow to connect to it as Target Database.<br>
							- You can then Add Tables and Records to it from the Source Database.
						</span>
					</span>:<br>
					New Database Name: <input type="text" name="NewDatabaseName" value="<?= @$_POST["NewDatabaseName"] ?>">
				</p>
				<?php
				$strCecked = "";
				$strDisabled = "";
				if ($boolTargetConnected) {
					if ($strActionType == "CreateTable") {
						$strCecked = 'class="red" ';
					}
				} else {
					$strDisabled = "disabled ";
				} ?>
				<p><input <?= $strDisabled ?>type="radio" name="ActionType" value="CreateTable">
					<span <?= $strCecked ?>><b>Create</b> checked Source Tables in Target Database</span>
					<span class="tipsBG">[?]
						<span class="tips">
							You must connect to a Target Database to active this option.<br>
						</span>
					</span><br>
					<input type="radio" name="DropExistingTables" value="No" checked> Do Not Drop Tables if they Exist<br>
					<input type="radio" name="DropExistingTables" value="Yes"> Drop Tables if they Exist
				</p>
			</div>
		</div>
		<div class="container">
			<div class="half">
				<?php
				$str_Source_DB = "MySQL";
				if (strpos($strSourceDBName, ".mdb") > 0 || strpos($strSourceDBName, ".accdb") > 0) {
					$str_Source_DB = "MS ACCESS";
				}
				$str_Target_DB = "";
				if (strpos($strTargetDBName, ".mdb") > 0 || strpos($strTargetDBName, ".accdb") > 0) {
					$str_Target_DB = "MS ACCESS";
				} elseif (!empty($strTargetDBName)) {
					$str_Target_DB = "MySQL";
				}
				?>
				<h4>Source Database <span style="color: #000"><?= $str_Source_DB ?>:</span> <span class="color"><?= $strSourceDBName ?></span><br>
					Target Database <span style="color: #000"><?= $str_Target_DB ?>:</span> <span class="color"><?= $strTargetDBName ?></span>
				</h4>
			</div>
			<div class="half">
				<table>
					<tr>
						<td>Connect to an <b>Alternative</b><br>Source DB, Access or MySQL
							<span class="tipsBG">[?]
								<span class="tips">
									<b>For Access Database</b><br>
									If located in the folder <b>/Private</b> of the current site (local or remote),
									write only the Name of the Access Database (with file extention .mdb or .accdb),<br>
									else, write the Full Path.<br>
									<b>For MySQL Databases</b><br>
									Write only the Name of the Table Schema in the current (default) MySQL Database
									for which you have the same administrative rights as the default ones.<br>
									Click then Submit to Connect.
								</span>
							</span>:
						</td>
						<td><input type="text" name="ConnectToSourceDB" placeholder="Database Name" value="">
							<input type="checkbox" name="ClearSourceDB" value="Yes"> Disconnect Source DB
							<span class="tipsBG">[?]
								<span class="tips">
									Only for alternative Source Database.<br>Returns to the Default Administrative Connection.
								</span>
							</span>
							<br>
							<input type="text" name="SourceServer" placeholder="Server" value="">
							<input type="text" name="SourceUID" placeholder="User Name" value="">
							<input type="password" name="SourcePW" placeholder="Password" value="">

						</td>
					</tr>
					<tr>
						<td>Connect to a<br>Target DB, Access or MySQL
							<span class="tipsBG">[?]
								<span class="tips">
									<b>For Access Database</b><br>
									If located in the folder <b>/Private</b> of the current site (local or remote),
									write only the Name of the Access Database (with file extention .mdb or .accdb),<br>
									else, write the Full Path.<br>
									<b>For MySQL Databases</b><br>
									Write only the Name of the Table Schema in the current (default) MySQL Database
									for which you have the same administrative rights as the default ones.<br>
									If you have created a New Database in MySQL, write its name also here.<br>
									Click then Submit to Connect.
								</span>
							</span>:
						</td>
						<td><input type="text" name="ConnectToTargetDB" placeholder="Database Name" value="">
							<input type="checkbox" name="ClearTargetDB" value="Yes"> Disconnect Target DB<br>
							<input type="text" name="TargetServer" placeholder="Server" value="">
							<input type="text" name="TargetUID" placeholder="User Name" value="">
							<input type="password" name="TargetPW" placeholder="Password" value="">
						</td>
					</tr>
				</table>
			</div>
		</div>
		<hr>
		<div class="textXXS">Source Database Tables</div>
		<div id="jgTables" class="container" style="justify-content: center;">
			<div>
				<?php
				/**
				 * Creat Checkboxes for every table in Access Database
				 */
				$iRows = 0;
				$i_Rows = 0;
				if (is_array($arrTables)) {
					$iRows = count($arrTables);
					$iDiv = intval($iRows / 6) + 1;
					$intLoop = 0;
					for ($t = 0; $t < $iRows; $t++) {
						if ($intLoop >= $iDiv) {
							echo "</div><div>";
							$intLoop = 0;
						}
						$sTable = trim($arrTables[$t]);
						$str_Cecked = "";
						if (@$_POST["table_" . $t] == "Yes") {
							$str_Cecked = "checked ";
						}
						$Class = "";
						if (is_array($arrTargetTables) && !in_array($sTable, $arrTargetTables)) {
							$Class = ' class="red" title="The table does Not Exist in Target DB"';
						} ?>
						<input <?= $str_Cecked ?>type="checkbox" id="table_<?= $t ?>" name="table_<?= $t ?>" value="Yes">
						<span<?= $Class ?>><?= $t . " " . $sTable ?></span><br>
					<?php
						$intLoop = $intLoop + 1;
					}
				}
					?>
			</div>
		</div>
		<hr>
		<div class="container">
			<div class="third" style="text-align: center;">
				<button type='button' id='selectall'>Select All</button>
				<button type='button' id='deselectall'>Clear All</button>
			</div>
			<div class="third" style="text-align: center;">
				<button type="button" id="checkedInterval">Select Beteen Checked Intervals</button>
			</div>
			<div class="third" style="text-align: center;">
				From Table: <select id="From" name="From">
					<?php for ($t = 0; $t <= $iRows; $t++) { ?>
						<option value="<?= $t ?>"><?= $t ?></option>
					<?php } ?>
				</select>
				To Table: <select id="To" name="To">
					<?php for ($t = 0; $t <= $iRows; $t++) { ?>
						<option value="<?= $t ?>"><?= $t ?></option>
					<?php } ?>
				</select>
				<button type="button" id="interval">Select Interval</button>
			</div>
			<div class="third" style="text-align: center;">
				<input name="Submit1" type="submit" value="Submit">
			</div>
		</div>
		<hr>
	</form>
	<?php

	$iFields = 0;
	if (is_array($arrTables) && $strActionType == "ViewTables") {
		include __DIR__ . '/ps_ViewTables.php';
	}

	if (is_array($arrTables) && $strActionType == "AddSelectTables") {
		include __DIR__ . '/ps_AddSelectTables.php';
	}

	if (is_array($arrTables) && $strActionType == "UpdateSelectTables") {
		include __DIR__ . '/ps_UpdateSelectTables.php';
	}

	if (is_array($arrTables) && $strActionType == "ViewDescription") {
		include __DIR__ . '/ps_ViewDescription.php';
	}

	if (is_array($arrTables) && $strActionType == "AddDescription") {
		include __DIR__ . '/ps_AddDescription.php';
	}

	$strNewDatabaseName = $_POST["NewDatabaseName"] ? trim($_POST["NewDatabaseName"]) : '';
	if (!empty($conn) && $strActionType == "CreateDatabase" && !empty($strNewDatabaseName)) {
		include __DIR__ . '/ps_CreateDatabase.php';
	}

	$strDropExistingTables = $_POST["DropExistingTables"] ?? false;
	if (is_array($arrTables) && $boolTargetConnected && $strActionType == "CreateTable") {
		include __DIR__ . '/ps_CreateTable.php';
	} ?>

	<h2>Totals</h2>
	<p>Total Tables: <?= @$iRows + 1 ?></p>
	<p>Total Rows: <?= $i_Rows ?>, Total <?= @$strFieldsEffected . ": " . @$iFields ?></p>

	<?php
	echo '<pre>';
	print_r(PDO::getAvailableDrivers());
	echo '</pre>';
	?>
</body>

</html>