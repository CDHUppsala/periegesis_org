<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

function return_NonUsedTables() {
	$conn = dbconn();
    $sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['NonUsedTables']);
    return $stmt->fetchColumn();
}

$showType = "";
if (isset($_GET["show"])) {
	$showType = $_GET["show"];
}
/**
 * For PDO Connections to MySQL
 * Alternative field information schemata
 * Default is the last one: sx_tablesFields($table)
 */

function sx_tablesFields_schema($tbl)
{
	$conn = dbconn();
	$sql = "SELECT * FROM information_schema.columns
    WHERE TABLE_SCHEMA = ?
	AND table_name = ?
	ORDER BY ORDINAL_POSITION";
	$stmt = $conn->prepare($sql);
	$stmt->execute([sx_TABLE_SCHEMA, $tbl]);
	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "<table style='max-width: 85%'><tr><th>COLUMN_NAME</th><th title='DATA_TYPE'>D TYPE</th><th title='COLUMN_TYPE'>C TYPE</th>
	<th title='NUMERIC_PRECISION'>NP</th><th title='CHARACTER_MAXIMUM_LENGTH'>LEN</th>
	<th title='COLUMN_KEY'>KEY</th>
	<th>EXTRA</th>
	<th title='CHARACTER_SET_NAME'>CHAR</th>
	<th title='IS_NULLABLE'>NULL</th>
	<th title='COLUMN_COMMENT'>COLUMN COMMENT</th>
	</tr>";
	foreach ($rs as $meta) {
		echo "<tr>";
		echo "<td>" . $meta['COLUMN_NAME'] . "</td>";
		echo "<td>" . $meta['DATA_TYPE'] . "</td>";
		echo "<td>" . $meta['COLUMN_TYPE'] . "</td>";
		echo "<td>" . $meta['NUMERIC_PRECISION'] . "</td>";
		echo "<td>" . $meta['CHARACTER_MAXIMUM_LENGTH'] . "</td>";
		echo "<td>" . $meta['COLUMN_KEY'] . "</td>";
		echo "<td title='" . $meta['EXTRA'] . "'>" . substr($meta['EXTRA'], 0, 4) . "</td>";
		echo "<td>" . $meta['CHARACTER_SET_NAME'] . "</td>";
		echo "<td>" . $meta['IS_NULLABLE'] . "</td>";
		echo "<td>" . $meta['COLUMN_COMMENT'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}

function sx_tablesFields_meta($tbl)
{
	$conn = dbconn();
	echo "<table><tr><th>name</th><th>native_type</th><th>len</th><th>flags 0</th><th>flags 1</th><th>flags 2</th></tr>";
	$stmt = $conn->query("SELECT * FROM $tbl");
	$colcount = $stmt->columnCount();
	for ($c = 0; $c < $colcount; $c++) {
		$meta = $stmt->getColumnMeta($c);
		echo "<tr><td>" . $meta['name'] . "</td>";
		echo "<td>" . $meta['native_type'] . "</td>";
		echo "<td>" . $meta['len'] . "</td>";
		echo "<td>" . @$meta['flags'][0] . "</td>";
		echo "<td>" . @$meta['flags'][1] . "</td>";
		echo "<td>" . @$meta['flags'][2] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}

function sx_tablesFields($tbl)
{
	$conn = dbconn();
	echo "<table><tr><th>Field</th><th>Type</th><th>Key</th><th>Extra</th></tr>";
	$stmt = $conn->query("SHOW COLUMNS FROM $tbl");
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $column) {
		echo "<tr><td>" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td><td>" . $column['Extra'] . "</td></tr>";
	}
	echo "</table>";
}


?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Data Base Information</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<link rel="stylesheet" href="css_more.css?v=3">
	<style>
		.links_aside {
			position: fixed;
			top: 0;
			right: 0;
			padding: 15px 25px 0 5px;
			background: #fff;
			white-space: nowrap;
		}
	</style>
</head>

<body>
	<a name="Top">
		<h1>Database Content</h1>
		<h2>List of Tables and their Attributes</h2>
		<?php

		// Show only Used Table
		$arrNonUsedTables = "";
		$strDataMarkNotes = return_NonUsedTables();

		if (!empty($strDataMarkNotes)) {
			$arrNonUsedTables = json_decode($strDataMarkNotes, true);
		}

		$stmt = $conn->query("SHOW TABLES");
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		$stmt = null;
		$list = "";
		foreach ($rs as $table) {
			$LoopTable = $table[0];
			if (empty($arrNonUsedTables) || !in_array($LoopTable, $arrNonUsedTables)) {
				$list .= '<li><a href="#' . $LoopTable . '">' . $LoopTable . '</a></li>';
				echo '<h3><a name="' . $LoopTable . '"></a>' . $LoopTable . '</h3>';
				if ($showType == "schema") {
					sx_tablesFields_schema($LoopTable);
				} elseif ($showType == "meta") {
					sx_tablesFields_meta($LoopTable);
				} else {
					sx_tablesFields($LoopTable);
				}
			}
		}
		$rs = null;

		?>
		<div class="links_aside">
			<b>Show by:</b><br>
			<a href="dbInfo.php">Columns</a> |
			<a href="dbInfo.php?show=meta">Meta</a> |
			<a href="dbInfo.php?show=schema">Schema</a> |
			<a href="#top">Top</a>
		</div>
		<div class="list_tables_aside">

			<ul>
				<?= $list ?>
			</ul>
		</div>
</body>

</html>