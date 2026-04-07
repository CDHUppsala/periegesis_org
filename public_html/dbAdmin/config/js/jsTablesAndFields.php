<script>
	var arrayTables = new Array();
	<?php
	$rs = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
	$arrTables = $rs;
	$rs = null;

	$i = 0;
	foreach ($arrTables as $table) {
		echo "arrayTables[" . $i . "] = '" . $table[0] . "'\n";
		$i++;
	} ?>

	var arrayFields = new Array();
	<?php
	$i = 0;
	foreach ($arrTables as $table) {
		$sql = "SELECT * FROM " . $table[0] . " LIMIT 0";
		$rs = $conn->query($sql);
		$iCount = $rs->columnCount();
		for ($x = 0; $x < $iCount; $x++) {
			$col = $rs->getColumnMeta($x);
			$name = $col['name'];
			$type = $col['native_type'];
			echo "arrayFields[$i] = '" . $table[0] . "|" . $type . "|" . $name . "'\n";
			$i++;
		}
		$rs = null;
	}
	?>
</script>