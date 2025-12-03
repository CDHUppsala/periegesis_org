<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include __DIR__ . "/functions.php";

/*
foreach ($rs as $table) {
	$loop_table = $table[0];
	echo '<h3><a name="' . $loop_table . '"></a>' . $loop_table . '</h3>';
	//sx_tablesFields_schema($LoopTable);
	//sx_tablesFields_meta($LoopTable);
	sx_tablesFields($loop_table);
}
*/

$strExport = @$_POST["export"];

/**
 * Connection to Main DB
 */
$conn_main = $conn;
$strMainDB = @$_POST["MainDB"];
$boolMainMySQL = true;
if (!empty($strMainDB)) {
	if (strpos($strMainDB, "/") == 0 && strpos($strMainDB, "\\") == 0) {
		$conn_main = get_dbConn_MySQL($strMainDB);
	} else {
		$boolMainMySQL = false;
		$conn_main = get_dbConn_Access($strMainDB);
	}
}

/**
 * Connection to Compare DB
 */
$conn_comp = null;
$strCompareDB = @$_POST["CompareDB"];
$boolCompareMySQL = true;
if (!empty($strCompareDB)) {
	if (strpos($strCompareDB, "/") === false && strpos($strCompareDB, "\\") === false) {
		$strServerName = trim(@$_POST["ServerName"]);
		$strUserName = trim(@$_POST["UserName"]);
		$UserPassword = trim(@$_POST["UserPassword"]);
		if (!empty($strServerName) && !empty($strUserName) && !empty($UserPassword)) {
			$conn_comp = get_dbConn_MySQL($strCompareDB, $strServerName, $strUserName, $UserPassword);
		} else {
			$conn_comp = get_dbConn_MySQL($strCompareDB);
		}
	} else {
		$boolCompareMySQL = false;
		$conn_comp = get_dbConn_Access($strCompareDB);
	}
}
$radioExistingTables = false;
if(!empty($_POST['ExistingTables']) && $_POST['ExistingTables'] == 'Yes') {
	$radioExistingTables = true;
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Studiox x Content Management System - Database Information</title>
	<?php if ($strExport == "") { ?>
		<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
		<link rel="stylesheet" href="css_more.css?v=3">
		<script src="../../js/jq/jquery.min.js"></script>
	<?php
	} else {
		include __DIR__ . "/css_print.php";
	} ?>
</head>

<body>
	<a name="Top"></a>

	<h2>Compare Databases: Tables, Fields, Field Types and Indexes.</h2>
	<?php if ($strExport == "") { ?>
		<div style="width: 70%; margin-bottom: 40px">
			<form name="selectDB" method="post" action="dbCompare.php">
				<fieldset>
					<p>You can compare any Access or Local MySQL databse with any Access or MySQL databse, Local or Remote.</p>
					<table class="no_bg">
						<tr>
							<td>Get path to locla <b>Access Database</b>:</td>
							<td><input type="file" name="IgnoredName" style="width: 100%;"></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<h3>Main Database: <span style="font-size: 0.75em">Path to Access Database or the Name of a Local MySQL Database. If empty, the default connection will be used.</span></h3>
					<p><input type="text" name="MainDB" value="<?= $strMainDB ?>" style="width: 100%;"></p>
				</fieldset>
				<fieldset>
					<h3>Secondary Database: <span style="font-size: 0.75em">Path to Access Database or the Name of a Local or Remote MySQL Database</span></h3>
					<p><input type="text" name="CompareDB" value="<?= $strCompareDB ?>" style="width: 100%;"></p>
					<p>For Remote MySQL database (automatically added Username and Password are ingnored if Server Name is empty):</p>
					<div class="row">
						<table class="no_bg">
							<tr>
								<td>Server Name:</td>
								<td><input type="text" name="ServerName" value="<?= @$strServerName ?>" style="width: 160px;"> </td>
							</tr>
							<tr>
								<td>Username:</td>
								<td><input type="text" name="UserName" value="<?= @$strUserName ?>" style="width: 160px;"> </td>
							</tr>
							<tr>
								<td>Password:</td>
								<td><input type="Password" name="UserPassword" value="" style="width: 160px;"></td>
							</tr>
						</table>
						<div>
							<?php
							$strChecked = '';
							if ($radioExistingTables) {
								$strChecked = ' checked';
							} ?>
							<input type="checkbox" name="ExistingTables" value="Yes"<?php echo $strChecked ?>> Show only existing Tables
						</div>
					</div>
				</fieldset>
				<div style="float: right">
					<input type="checkbox" name="export" value="Yes"> Compare and Save in HTML
					<input type="submit" name="NewSubmit" value="Compare">
				</div>
				<p style="position: fixed; top: 0; right: 40px;">
					<input class="button" type="submit" name="submit" value="Compare">
					<a class="button" href="#Top">Top</a>
				</p>
			</form>
		</div>
	<?php } ?>
	<?php
	/**
	 * Get the names of the databases
	 */
	if ($boolMainMySQL) {
		if (!empty($strMainDB)) {
			$sMainDB = $strMainDB;
		} else {
			$sMainDB = "Default";
		}
		$strFileName = $sMainDB;
	} else {
		$sMainDB = substr($strMainDB, - (strlen($strMainDB) - strrpos($strMainDB, "\\")) + 1);
		$strFileName = substr($sMainDB, 0, (strrpos($sMainDB, ".")));
	}
	if ($boolCompareMySQL) {
		$sCompareDB = $strCompareDB;
		$strFileName .= "_" . $strCompareDB;
	} else {
		$sCompareDB = substr($strCompareDB, - (strlen($strCompareDB) - strrpos($strCompareDB, "\\")) + 1);
		$strFileName .= "_" . substr($sCompareDB, 0, (strrpos($sCompareDB, ".")));
	}
	?>
	<h2>
		Main Database: <span class="color"><?= $sMainDB ?></span><br>
		Compare to Secondary Database: <span class="color"><?= $sCompareDB ?></span>
	</h2>
	<?php
	/**
	 * List the tables and their fields
	 */

	$list = "";
	if ($boolMainMySQL) {
		$arrMainTables = sx_getMySQLTables($conn_main);
	} else {
		$arrMainTables = sx_getAccessTables($conn_main);
	}
	$arrCompareTables = null;
	$functionCompareName = "";
	if (!empty($strCompareDB)) {
		if ($boolCompareMySQL) {
			$arrCompareTables = sx_getMySQLTables($conn_comp);
			//$functionCompareName = "sx_tablesFields_MySQL";
			$functionCompareName = "MySQL";
		} else {
			$arrCompareTables = sx_getAccessTables($conn_comp);
			//$functionCompareName = "sx_tablesFields_access";
			$functionCompareName = "access";
		}
	}

	foreach ($arrMainTables as $table) {
		if ($radioExistingTables && !in_array($table, $arrCompareTables)) {
			continue;
		}
		/**
		 * Get the field names of both tables
		 */
		if ($boolMainMySQL) {
			$arr_MainFields = sx_getMySQLFieldNames($table, $conn_main);
		} else {
			$arr_MainFields = sx_getAccessFieldNames($table, $conn_main);
		}
		$arr_CompareFields = null;
		if (!empty($strCompareDB) && in_array($table, $arrCompareTables)) {
			if ($boolCompareMySQL) {
				$arr_CompareFields = sx_getMySQLFieldNames($table, $conn_comp);
			} else {
				$arr_CompareFields = sx_getAccessFieldNames($table, $conn_comp);
			}
		}

		echo "<table><tr><td>";
		/**
		 * Main Database
		 */
		$list .= '<li><a href="#' . $table . '">' . $table . '</a></li>';
		echo '<h3><a name="' . $table . '"></a>' . $table . '</h3>';
		if ($boolMainMySQL) {
			sx_tablesFields_MySQL($table, $conn_main, $arr_CompareFields);
		} else {
			sx_tablesFields_access($table, $conn_main, $arr_CompareFields);
		}

		echo "</td><td>";


		if ($boolMainMySQL && empty($strCompareDB)) {
			echo "<h3> $table</h3>";
			sx_tablesFields_MySQL_Schema($strMainDB, $table, $conn_main, $arr_CompareFields);
		}

		/**
		 * Compare database
		 */
		if (!empty($functionCompareName) && in_array($table, $arrCompareTables)) {
			echo "<h3> $table</h3>";
			$sx_conn_main = null;
			if ($boolCompareMySQL) {
				if (!$boolMainMySQL) {
					$sx_conn_main = $conn_main;
				}
				sx_tablesFields_MySQL_Schema($strCompareDB, $table, $conn_comp, $arr_MainFields, $sx_conn_main);
			} else {
				if ($functionCompareName == "access") {
					sx_tablesFields_access($table, $conn_comp, $arr_MainFields);
				} else {
					sx_tablesFields_MySQL($table, $conn_comp, $arr_MainFields);
				}
			}
		} else {
			if (!empty($strCompareDB)) {
				echo "<h3> $table </h3>";
				echo "This Table does Not Exist!";
			}
		}
		echo "</td></tr></table>\n";
	}
	?>


	<div class="list_tables_aside">
		<h4>Select Table to Compare</h4>
		<ul>
			<?= $list ?>
		</ul>
	</div>
</body>

</html>
<?php if ($strExport == "Yes") {
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=compare_" . $strFileName . ".html");
	header("Content-Type: text/html; ");
} ?>