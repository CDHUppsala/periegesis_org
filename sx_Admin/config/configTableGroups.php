<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

function sx_get_savedTableGroups()
{
	$conn = dbconn();
	$str_SQL = "SELECT GroupName 
		FROM sx_help_by_group 
		WHERE LanguageCode = ? 
		ORDER BY GroupName ASC";
	$stmt = $conn->prepare($str_SQL);
	$stmt->execute([sx_DefaultAdminLang]);
	$frs = $stmt->fetchAll(PDO::FETCH_NUM);
	$f = 0;
	foreach ($frs as $frow) {
		if ($f > 0) {
			echo ", ";
		}
		echo $frow[0];
		$f++;
	}
	$frs = null;
}

function sx_getTablesGroupAndSort($s_Table)
{
	if (!empty(arr_Tables)) {
		foreach (arr_Tables as $groupName => $groupTables) {
			foreach ($groupTables as $index => $value) {
				if ($value["name"] == $s_Table) {
					return array($groupName, $value["sort"]);
				}
			}
		}
		return  array("noGrouped", 0);
	} else {
		return  array("noGrouped", 0);
	}
}


function sx_getAddNewRecord($s_Table)
{
	if (!empty(arr_AddUpdate)) {
		if (!empty(arr_AddUpdate["add"])) {
			if (isset(arr_AddUpdate["add"][$s_Table])) {
				return arr_AddUpdate["add"][$s_Table];
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
}

function sx_getUpdateRecords($s_Table)
{
	if (!empty(arr_AddUpdate)) {
		if (!empty(arr_AddUpdate["update"])) {
			if (isset(arr_AddUpdate["update"][$s_Table])) {
				return arr_AddUpdate["update"][$s_Table];
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
}

function sx_getGroupNameOptions($s_Table, $s_Group)
{
	echo '<select name="GroupName' . $s_Table . '">';
	if (empty($s_Group) || $s_Group == "noGrouped") {
		echo '<option value="noGrouped" selected>No Grouped</option>';
	} else {
		echo '<option value="noGrouped">No Grouped</option>';
	}
	if (!empty(arr_Groups)) {
		$iCount = count(arr_Groups);
		for ($i = 0; $i < $iCount; $i++) {
			$strLoop = trim(arr_Groups[$i]);
			if ($s_Group == $strLoop) {
				echo '<option selected value="' . $strLoop . '">' . $strLoop . "</option>";
			} else {
				echo '<option value="' . $strLoop . '">' . $strLoop . "</option>";
			}
		}
	}
	echo "</select>";
}

/**
 * Get Source and Target Group Configuration Project Name
 */
$radio_reloadTop = false;
if (!empty($_POST["SourceProjectName"])) {
	$radio_reloadTop = true;
	$strSourceProjectName = $_POST["SourceProjectName"];
	$_SESSION["SourceProjectName"] = $strSourceProjectName;
} elseif (isset($_SESSION["SourceProjectName"]) && !empty($_SESSION["SourceProjectName"])) {
	$strSourceProjectName = $_SESSION["SourceProjectName"];
} else {
	$strSourceProjectName = $str_ConfigProjectName;
}


if (isset($_POST["NewTargetProjectName"]) && !empty($_POST["NewTargetProjectName"])) {
    $strTargetProjectName = $_POST["NewTargetProjectName"];
    $_SESSION["TargetProjectName"] = $strTargetProjectName;
} elseif (isset($_POST["TargetProjectName"]) && !empty($_POST["TargetProjectName"])) {
	$strTargetProjectName = $_POST["TargetProjectName"];
	$_SESSION["TargetProjectName"] = $strTargetProjectName;
} elseif (isset($_SESSION["TargetProjectName"]) && !empty($_SESSION["TargetProjectName"])) {
	$strTargetProjectName = $_SESSION["TargetProjectName"];
} else {
	$strTargetProjectName = $str_ConfigProjectName;
}

/**
 * Not updae Default Project (as Target) from another Source Project
 */
if ($strTargetProjectName == $str_ConfigProjectName) {
	if ($strSourceProjectName != $str_ConfigProjectName) {
		$strSourceProjectName = $str_ConfigProjectName;
		unset($_SESSION["SourceProjectName"]);
	}
}

if ($radio_reloadTop) {
	echo "<script>top.window.location = 'default.php?relaod=yes'</script>";
	die;
}

/**
 * SAVE Configuration SETTINGS
 */
if (!empty($_POST["ConfigTables"])) {
	$rs = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
	$arrTables = $rs;
	$rs = null;

	$arrAliasTableNames = array();
	$arrTablesByGroup = array();
	$arrAddAndUpdate = array();
	$arrAdd = array();
	$arrUpdate = array();

	foreach ($arrTables as $table) {
		$sTableName = strtolower($table[0]);
		/**
		 * Create array (to json) with alias names of tables
		 */
		if (!empty($_POST["AliasNameOfTables" . $sTableName])) {
			$arrAliasTableNames[$sTableName] = sx_replaceQuotes($_POST["AliasNameOfTables" . $sTableName]);
		}
		/**
		 * Create array (to json) with table groups and their tables
		 */
		if (!empty($_POST["GroupName" . $sTableName])) {
			$mixSorting = @$_POST["Sorting" . $sTableName];
			$sGroupName = $_POST["GroupName" . $sTableName];
			if ($mixSorting == "") {
				$mixSorting = 0;
			}
			if (array_key_exists($sGroupName, $arrTablesByGroup)) {
				array_push($arrTablesByGroup[$sGroupName], array("sort" => $mixSorting, "name" => $sTableName));
			} else {
				$arrTablesByGroup[$sGroupName][] = array("sort" => $mixSorting, "name" => $sTableName);
			}
		}
		/**
		 * Create array (to json) for adding and updating relating tables
		 */
		if (!empty($_POST["AddNewRecord" . $sTableName])) {
			if (array_key_exists("add", $arrAdd)) {
				$arrAdd["add"] = array_merge($arrAdd["add"], array($sTableName => sx_replaceQuotes($_POST["AddNewRecord" . $sTableName])));
			} else {
				$arrAdd["add"] = array($sTableName => sx_replaceQuotes($_POST["AddNewRecord" . $sTableName]));
			}
		}
		if (!empty($_POST["UpdateInList" . $sTableName])) {
			if (array_key_exists("update", $arrUpdate)) {
				$arrUpdate["update"] = array_merge($arrUpdate["update"], array($sTableName => sx_replaceQuotes($_POST["UpdateInList" . $sTableName])));
			} else {
				$arrUpdate["update"] = array($sTableName => sx_replaceQuotes($_POST["UpdateInList" . $sTableName]));
			}
		}
	}
	$arrAddAndUpdate = array_merge($arrAdd, $arrUpdate);
	$arrAdd = null;
	$arrUpdate = null;
	$arrTables = null;

	if (!empty($_POST["TableGroupNames"])) {
		$strTableGroupNames = sx_replaceQuotes($_POST["TableGroupNames"]);
	}

	/**
	 * Sort Group Names and Tables within every Group Name
	 * Remove spaces from Group Names
	 */
	$arrGroupNames = explode(",", $strTableGroupNames);
	$arr_GroupNames = array();
	$arr_TablesByGroup = array();
	$iGroups = count($arrGroupNames);
	for ($i = 0; $i < $iGroups; $i++) {
		$strGroupName = trim($arrGroupNames[$i]);
		$arr_GroupNames[] = $strGroupName;
		foreach ($arrTablesByGroup as $key => $value) {
			if ($key == $strGroupName) {
				$arrSort  = array_column($value, 'sort');
				$arrName = array_column($value, 'name');
				array_multisort($arrSort, SORT_ASC, $arrName, SORT_ASC, $value);
				$arr_TablesByGroup[$strGroupName] = $value;
				break;
			}
		}
	}
	if (array_key_exists("noGrouped", $arrTablesByGroup)) {
		$arr_TablesByGroup["noGrouped"] = $arrTablesByGroup["noGrouped"];
	}
	$arrTablesByGroup = $arr_TablesByGroup;
	$arr_TablesByGroup = null;
	$arrGroupNames = $arr_GroupNames;
	$arr_GroupNames = null;

	/**
	 * Convert arrays to json
	 */
	$jsonAliasTableNames = json_encode($arrAliasTableNames, JSON_UNESCAPED_UNICODE);
	$jsonGroupNames =  json_encode($arrGroupNames, JSON_UNESCAPED_UNICODE);
	$jsonTablesByGroup = json_encode($arrTablesByGroup, JSON_UNESCAPED_UNICODE);
	$jsonAddAndUpdate = json_encode($arrAddAndUpdate, JSON_UNESCAPED_UNICODE);

	$arrAliasTableNames = null;
	$arrGroupNames = null;
	$arrTablesByGroup = null;
	$arrAddAndUpdate = null;

	$radioInsert = true;
	$strSQL = "SELECT ConfigGroupID FROM sx_config_groups 
			WHERE ProjectName = ? 
			AND LanguageCode = ? ";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$strTargetProjectName, sx_DefaultAdminLang]);
	$rs = $stmt->fetch();
	if ($rs) {
		$radioInsert = false;
	}
	$rs = null;

	if ($radioInsert) {
		$sql = "INSERT INTO sx_config_groups 
		(LanguageCode, ProjectName, AliasNameOfTables,OrderedTableGroupNames,TablesByGroupName,AddAndUpdateTitles)
		VALUES (?, ?, ?, ?, ?, ?)";
		$conn->prepare($sql)->execute([sx_DefaultAdminLang, $strTargetProjectName, $jsonAliasTableNames, $jsonGroupNames, $jsonTablesByGroup, $jsonAddAndUpdate]);
	} else {
		$sql = "UPDATE sx_config_groups SET 
				LanguageCode = ?,
				AliasNameOfTables = ?,
				OrderedTableGroupNames = ?,
				TablesByGroupName = ?,
				AddAndUpdateTitles = ? 
			WHERE ProjectName = ? 
			AND LanguageCode = ? ";
		$conn->prepare($sql)->execute([sx_DefaultAdminLang, $jsonAliasTableNames, $jsonGroupNames, $jsonTablesByGroup, $jsonAddAndUpdate, $strTargetProjectName, sx_DefaultAdminLang]);
	}

	//header("location: configTableGroups.php?strReload=Yes");
	//exit();
}

/**
 * GET BASIC VARIABLES
 */
$strSQL = "SELECT * FROM sx_config_groups 
	WHERE ProjectName = ?
	AND LanguageCode = ? ";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$getAlias = $rs["AliasNameOfTables"];
	$getGroups = $rs["OrderedTableGroupNames"];
	$getTables = $rs["TablesByGroupName"];
	$getAddUpdate = $rs["AddAndUpdateTitles"];
} else {
	$getAlias = "";
	$getGroups = "";
	$getTables = "";
	$getAddUpdate = "";
}
$rs = null;


//echo $strSQL .'<br>';
//echo $getGroups;

$arrAlias = json_decode($getAlias, true);
$arrGroups = json_decode($getGroups, true);
$arrTables = json_decode($getTables, true);
$arrAddUpdate = json_decode($getAddUpdate, true);

define("arr_Groups", $arrGroups);
define("arr_Tables", $arrTables);
define("arr_AddUpdate", $arrAddUpdate);


/**
 * Define if tables will be ordered by Group
 */

if (!empty($_POST["OrderByTable"])) {
	$_SESSION["OrderByGroup"] = False;
	$radioOrderByGroup = False;
}
if (!empty($_POST["OrderByGroup"]) || !empty($_SESSION["OrderByGroup"])) {
	$_SESSION["OrderByGroup"] = True;
	$radioOrderByGroup = True;
} else {
	$_SESSION["OrderByGroup"] = False;
	$radioOrderByGroup = False;
}


$arrListOfTables = array();
$arrTablesInGroup = array();

/**
 * Get the list of all tables in alphabetic order.
 * If available, get the group name and the sorting of tables within every group 
 */
$rs = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
$arrTables = $rs;
$rs = null;
$x = 0;
foreach ($arrTables as $table) {
	$tbl_Name = strtolower($table[0]);
	$arrGroupSort = sx_getTablesGroupAndSort($tbl_Name);
	$strAddNewRecord = sx_getAddNewRecord($tbl_Name);
	$strUpdateRecords = sx_getUpdateRecords($tbl_Name);
	$arrListOfTables[$x][0] = $arrGroupSort[0];
	$arrListOfTables[$x][1] = $tbl_Name;
	$arrListOfTables[$x][2] = $arrGroupSort[1];
	$arrListOfTables[$x][3] = $strAddNewRecord;
	$arrListOfTables[$x][4] = $strUpdateRecords;
	$x++;
}


//Create arrays of tables by group and tables in avery group
$z = 0;
$iTablesByGroups = count($arrListOfTables);
if (!empty($arrGroups)) {
	$iCount = count($arrGroups);
	for ($i = 0; $i < $iCount; $i++) {
		$strLoop = trim($arrGroups[$i]);
		for ($x = 0; $x < $iTablesByGroups; $x++) {
			if ($arrListOfTables[$x][0] == $strLoop) {
				$arrTablesInGroup[$z][0] =  $arrListOfTables[$x][0];
				$arrTablesInGroup[$z][1] =  $arrListOfTables[$x][1];
				$arrTablesInGroup[$z][2] =  $arrListOfTables[$x][2];
				$arrTablesInGroup[$z][3] =  $arrListOfTables[$x][3];
				$arrTablesInGroup[$z][4] =  $arrListOfTables[$x][4];
				$z++;
			}
		}
	}
}
for ($x = 0; $x < $iTablesByGroups; $x++) {
	if ($arrListOfTables[$x][0] == "noGrouped") {
		$arrTablesInGroup[$z][0] =  $arrListOfTables[$x][0];
		$arrTablesInGroup[$z][1] =  $arrListOfTables[$x][1];
		$arrTablesInGroup[$z][2] =  $arrListOfTables[$x][2];
		$arrTablesInGroup[$z][3] =  $arrListOfTables[$x][3];
		$arrTablesInGroup[$z][4] =  $arrListOfTables[$x][4];
		$z++;
	}
}
/*
echo '<pre>';
print_r($arrTablesInGroup);
echo '</pre>';
exit;
*/
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Config Table Groups</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<script src="../js/jq/jquery.min.js"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js"></script>
</head>

<body class="body">
	<header id="header">
		<h2>Define Table Groups and Table Alias Name</h2>
		<div class="margin_right_auto">
			<input class="button jqHelpButton" type="button" value="SHOW CONFIG DB" name="getSearchHelp" data-id="showConfigDB">
			<input class="button jqHelpButton" type="button" value="HELP" name="getSearchHelp" data-id="getTableGroupsHelp">
		</div>
	</header>

	<?php

	$strGroups = "";
	if (is_array($arrGroups)) {
		$strGroups = implode(", ", $arrGroups);
	}

	$arrProjects = null;
	$sql = "SELECT ProjectName 
		FROM sx_config_groups 
		WHERE LanguageCode = ? 
		ORDER BY ProjectName ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([sx_DefaultAdminLang]);
	$rs = $stmt->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$arrProjects = $rs;
	}
	$stmt = null;
	$rs = null;
	?>
	<section>
		<div class="row flex_align_center">
			<h2>Select Projects with Table Groups:</h2>
			<a class="button jqHelpButton" data-id="ProjectHelp" href="javascript:void(0)">Show Project Help</a>
		</div>
		<form method="POST" name="ChooseProjects" action="configTableGroups.php">
			<input type="hidden" name="ConfigProjects" value="Yes">
			<div class="formBG">
				<div class="row flex_justify_start" style="margin: 5px;">
					<?php
					if (is_array($arrProjects)) { ?>
						<label>Source Project:<br>
							<select name="SourceProjectName">
								<?php
								foreach ($arrProjects as $row) {
									$value = $row[0];
									$strSelected = "";
									if ($strSourceProjectName == $value) {
										$strSelected = " selected";
									} ?>
									<option value="<?= $value ?>" <?= $strSelected ?>><?= $value ?></option>
								<?php
								} ?>
							</select>
						</label>
						<label>Target Project:<br>
							<select name="TargetProjectName">
								<?php
								$radioSelected = false;
								foreach ($arrProjects as $row) {
									$value = $row[0];
									$strSelected = "";
									if ($strTargetProjectName == $value) {
										$radioSelected = true;
										$strSelected = " selected";
									} ?>
									<option value="<?= $value ?>" <?= $strSelected ?>><?= $value ?></option>
								<?php
								}
								if (!$radioSelected) { ?>
									<option value="<?= $strTargetProjectName ?>" selected><?= $strTargetProjectName ?></option>
								<?php
								} ?>
							</select>
						</label>
					<?php
					} ?>
					<label><input type="text" name="NewTargetProjectName" placeholder="New Project Name"></label>
					<label><input class="button" type="submit" value="Select Project" name="Select Project"></label>
				</div>

				<div id="ProjectHelp" style="display: none;">
					<h4>Select a Project to configuer or create and configure a new one</h4>
					<p>You can create different Navigation Menus for different Projects.
						You can also use a saved project (as Source Project):</p>
					<ul>
						<li>to change it (as Target Project) or</li>
						<li>to change another saved projject (as Target Project) or</li>
						<li>create and configur a New Projcet.</li>
					</ul>
					<p>The Default Project is defined by the site's initial configuration (in sx_language.php).</p>
					<ul>
						<li>Notice that you can change the initial configuration of the default project (as Target Project) only by starting from it as Source Project.</li>
					</ul>
				</div>
			</div>
		</form>


		<div class="row flex_align_center">
			<h2>Define the names of Table Groups:</h2>
			<a class="button jqHelpButton" data-id="SavedGroups" href="javascript:void(0)">Show Saved Table Groups</a>
		</div>

		<form method="POST" name="ChooseTableGroups" action="configTableGroups.php">
			<input type="hidden" name="ConfigTables" value="Yes">

			<textarea name="TableGroupNames" style="width: 100%; height: 80px;"><?= $strGroups ?></textarea>

			<div id="SavedGroups" class="bg" style="display: none;">
				<h4>Reuse saved Group Names to recall saved classifications of Tables into Groups and saved Help Informations for respective group:</h4>
				<?php sx_get_savedTableGroups() ?>
			</div>
			<div class="text_small">
				<b>Default Language: </b> <?= sx_DefaultAdminLang ?>,
				<b>Default Project: </b> <?= $str_ConfigProjectName ?>,
				<b>Source Project: </b> <?= $strSourceProjectName ?>,
				<b>Target Project: </b> <?= $strTargetProjectName ?>
			</div>
			<div class="row flex_align_center" style="padding: 1rem 0">
				<?php if ($_SESSION["OrderByGroup"]) { ?>
					<input class="button" type="submit" value="Order by Table Name" name="OrderByTable">
				<?php } else { ?>
					<input class="button" type="submit" value="Order by Group Name" name="OrderByGroup">
				<?php } ?>
				<input class="button" type="submit" value="Save Group Names" name="tableNames">
			</div>
			<table>
				<tr>
					<th>Table Name</th>
					<th>Sorting</th>
					<th>Alias Name</th>
					<th>Add New Record</th>
					<th>Update in List</th>
					<th>Select Group Name</th>
				</tr>

				<?php
				$newTableGroupArr = $arrListOfTables;
				if ($radioOrderByGroup) {
					$newTableGroupArr = $arrTablesInGroup;
				}
				if (is_array($newTableGroupArr)) {
					$iCount = count($newTableGroupArr);
					$LastGroupName = "";
					for ($x = 0; $x < $iCount; $x++) {
						$name_group = $newTableGroupArr[$x][0];
						$name_table = $newTableGroupArr[$x][1];
						$sort_table = $newTableGroupArr[$x][2];
						$addTo_table = $newTableGroupArr[$x][3];
						$update_table = $newTableGroupArr[$x][4];

						if ($radioOrderByGroup) {
							if ($LastGroupName == "") {
								$cellColor = "#db8";
							} elseif ($LastGroupName == $name_group) {
								if ($cellColor == "#db8") {
									$cellColor = "#db8";
								} else {
									$cellColor = "#6bd";
								}
							} else {
								if ($cellColor == "#db8") {
									$cellColor = "#6bd";
								} else {
									$cellColor = "#db8";
								}
							}
							$cellBG = 'style="background: ' . $cellColor . '"';
						} else {
							$cellBG = "";
						}

						$strAliasName = $name_table;
						if (!empty($arrAlias[$name_table])) {
							$strAliasName = ucwords($arrAlias[$name_table]);
						}
                        if(str_contains($strAliasName,'_')) {
                            $strAliasName = str_replace('_',' ',$strAliasName);
                            $strAliasName = ucwords($strAliasName);
                        }
				?>
						<tr>
							<td>
								<input type="text" disabled="disable" name="<?= $name_table ?>" value="<?= $name_table ?>">
							</td>
							<td <?= $cellBG ?>>
								<input type="text" name="Sorting<?= $name_table ?>" value="<?= $sort_table ?>" size="5">
							</td>
							<td <?= $cellBG ?>>
								<input type="text" name="AliasNameOfTables<?= $name_table ?>" value="<?= $strAliasName ?>" size="26">
							</td>
							<td>
								<input type="text" name="AddNewRecord<?= $name_table ?>" value="<?= $addTo_table ?>" size="16">
							</td>
							<td>
								<input type="text" name="UpdateInList<?= $name_table ?>" value="<?= $update_table ?>" size="16">
							</td>
							<td>
								<?php
								sx_getGroupNameOptions($name_table, $name_group)
								?>
							</td>
						</tr>

				<?php
						$LastGroupName = $name_group;
					}
				}
				$connClose;
				?>
			</table>
			<br><input class="button" type="submit" value="Save Names & Groups" name="tableNames"><br><br>

		</form>
	</section>
	<?php include "sxHelpFiles/helpTableGroups.php"; ?>

	<div id="showConfigDB" class="sxHelp text">
		<b>Alias Names of Tables:</b><br><?= json_encode($arrAlias, JSON_UNESCAPED_UNICODE) ?><br>
		<hr>
		<b>Group names of tables:</b><br><?= json_encode(arr_Groups, JSON_UNESCAPED_UNICODE) ?><br>
		<hr>
		<b>Tables by their Group Name:</b><br><?= json_encode(arr_Tables, JSON_UNESCAPED_UNICODE) ?><br>
		<hr>
		<b>Add to and Update Tables from the Menu List:</b><br><?= json_encode(arr_AddUpdate, JSON_UNESCAPED_UNICODE) ?><br>
		<br>
	</div>
</body>

</html>