<?php

/**
 * Sub function 1 - Page Navigator with arrows
 */
function sx_arrowPageNav($strPath)
{
    global $currentPage, $pageCount;
    echo '<a title="First Page" HREF="' . $strPath . '1"><<<< </a> | ';
    if ($currentPage > 1) {
        echo '<a title="Previous Page" HREF="' . $strPath . ($currentPage - 1) . '"> << </a> | ';
    } else {
        echo ' << ';
    }
    if ($currentPage < $pageCount) {
        echo '<a title="Next Page" HREF="' . $strPath . ($currentPage + 1) . '"> >> </a> | ';
    } else {
        echo ' >> | ';
    }
    echo '<a title="Las Page" HREF="' . $strPath . $pageCount . '"> >>>></a>';
    echo '<hr>';
}

/**
 * Sub function 2 - Form to Go to a Page Number
 */
function sx_goToPageNav($strPath)
{
    global $currentPage, $pageCount; ?>
    <form action="<?= $strPath ?>" name="goToPage" method="post" style="margin-bottom:-2px">
        <input type="submit" name="goTopage" value="GO"> TO PAGE
        <input type="text" size="2" name="page" value="<?= $currentPage ?>"> OF <?= $pageCount ?>
    </form>
    <?php
}

/**
 * Sub function 3 - Navigate accross a list with all page numbers (Not used here)
 */
function sx_numberOfPageNav($strPath)
{
    global $currentPage, $pageCount;
    for ($i = 1; $i <= $pageCount; $i++) {
        if ($i == $currentPage) { ?>
            <b><?= $i ?></b>|
        <?php
        } else { ?>
            <a title="Go to Page" HREF="<?= $strPath . $i ?>"><?= $i ?></a>|
<?php
        }
    }
}

/**
 * List of DB Tables
 */

function sx_getTableList()
{
    $conn = dbconn();
    $result = $conn->query("SHOW TABLES");
    return $result->fetchAll(PDO::FETCH_NUM);
}

function return_NonUsedTables() {
	$conn = dbconn();
    $sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['NonUsedTables']);
    return $stmt->fetchColumn();
}

function sx_GetPrimaryKey($tbl)
{
    $conn = dbconn();
    $sql = "SELECT COLUMN_NAME
        FROM information_schema.columns 
        WHERE TABLE_SCHEMA = ?
        AND table_name = ?
        AND COLUMN_KEY = 'PRI'";
    $fstmt = $conn->prepare($sql);
    $fstmt->execute([sx_TABLE_SCHEMA, $tbl]);
    return $fstmt->fetchColumn();
}

function get_TableColumnType($tbl)
{
    $conn = dbconn();
    $arrColumnTypes = [];
    $select = $conn->query("SELECT * FROM $tbl");
    $colcount = $select->columnCount();

    for ($c = 0; $c < $colcount; $c++) {
        $meta = $select->getColumnMeta($c);
        $arrColumnTypes[$meta['name']] = $meta['native_type'];
    }
    return $arrColumnTypes;
}

function sx_getTableColumnType($tbl)
{
    $conn = dbconn();
    $select = $conn->query("SELECT * FROM $tbl");
    $colcount = $select->columnCount();
    $strPK = sx_GetPrimaryKey($tbl);
    echo "\n";
    echo '<table id="' . $tbl . '" data-id="' . $strPK . '">';
    echo '<tr><th>Name</th><th>Type</th></tr>';
    for ($c = 0; $c < $colcount; $c++) {
        $meta = $select->getColumnMeta($c);
        echo "<tr><td>" . $meta['name'] . "</td>";
        echo "<td>" . $meta['native_type'] . "</td>";
        //echo "<td>" . $meta['len'] . "</td>;
        echo "</tr>";
    }
    echo "</table>";
}

function sx_ClearSessions()
{
    unset($_SESSION["SelectedTable"]);
    unset($_SESSION["TableColumnTypes"]);
    unset($_SESSION["PKeyName"]);
    unset($_SESSION["SelectedFields"]);
    unset($_SESSION["FieldName"]);
    unset($_SESSION["CurrentValue"]);
    unset($_SESSION["NewValue"]);
    unset($_SESSION["ControlField"]);
    unset($_SESSION["ControlValue"]);
    unset($_SESSION["sorting"]);
    unset($_SESSION["strSortBy"]);
	unset($_SESSION["OrderBy"]);
}

function sx_ClearUpdateSessions()
{
    unset($_SESSION["FieldName"]);
    unset($_SESSION["CurrentValue"]);
    unset($_SESSION["NewValue"]);
    unset($_SESSION["ControlField"]);
    unset($_SESSION["ControlValue"]);
    unset($_SESSION["sorting"]);
    unset($_SESSION["strSortBy"]);
}

function sx_checkTypeCompatibility($sType, $mixValue)
{
	switch ($sType) {
		case "NUMERIC":
		case "DECIMAL":
		case "NEWDECIMAL":
		case "FLOAT":
		case "REAL":
		case "DOUBLE":
			// Replace comma with dot for decimal values
			$mixValue = str_replace(",", ".", $mixValue);
			return is_numeric($mixValue);

		case "SMALLINT":
		case "INTEGER":
		case "TINY":
		case "BIGINT":
		case "LONG":
		case "LONGLONG":
		case "SHORT":
		case "INT24":
		case "YEAR":
		case "BIT":
			// Check if the value is a valid integer
			return is_numeric($mixValue) && (string)(int)$mixValue === (string)$mixValue;

		case "BOOLEAN":
			// Accept true, false, 1, and 0 as valid boolean values
			return in_array(strtolower((string)$mixValue), ['true', 'false', '1', '0'], true);

		case "DATE":
			// Check if the value matches the YYYY-MM-DD format
			$date = DateTime::createFromFormat('Y-m-d', $mixValue);
			return $date && $date->format('Y-m-d') === $mixValue;

		case "TIME":
			// Check for HH:MM:SS format
			$time = DateTime::createFromFormat('H:i:s', $mixValue);
			return $time && $time->format('H:i:s') === $mixValue;

		case "DATETIME":
		case "TIMESTAMP":
			// Check for YYYY-MM-DD HH:MM:SS format
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mixValue);
			return $dateTime && $dateTime->format('Y-m-d H:i:s') === $mixValue;

		case "CHARACTER":
		case "STRING":
		case "VAR_STRING":
		case "BLOB":
		default:
			// Strings are considered valid by default
			return true;
	}
}

function sx_getTypeCompatibleValue($sType, $mixValue)
{
	$mixValue = !empty($mixValue) ? trim($mixValue) : '';

	switch ($sType) {
		case "NUMERIC":
		case "DECIMAL":
		case "NEWDECIMAL":
		case "FLOAT":
		case "REAL":
		case "DOUBLE":
			// Replace comma with dot for decimal values
			$mixValue = str_replace(",", ".", $mixValue);
			return is_numeric($mixValue) ? $mixValue : 0;

		case "SMALLINT":
		case "INTEGER":
		case "TINY":
		case "BIGINT":
		case "LONG":
		case "LONGLONG":
		case "SHORT":
		case "INT24":
		case "BIT":
			// Ensure integer conversion
			return is_numeric($mixValue) ? (int) $mixValue : 0;

		case "BOOLEAN":
			// Convert common truthy/falsy values to boolean equivalents
			$lowerValue = strtolower($mixValue);
			if (in_array($lowerValue, ['true', '1', 'yes'], true)) {
				return true;
			} elseif (in_array($lowerValue, ['false', '0', 'no'], true)) {
				return false;
			}
			return null; // Or a default boolean value if needed

		case "DATE":
			// Validate and format date (YYYY-MM-DD)
			$date = DateTime::createFromFormat('Y-m-d', $mixValue);
			return $date && $date->format('Y-m-d') === $mixValue ? $mixValue : null;

		case "TIME":
			// Validate and format time (HH:MM:SS)
			$time = DateTime::createFromFormat('H:i:s', $mixValue);
			return $time && $time->format('H:i:s') === $mixValue ? $mixValue : null;

		case "DATETIME":
		case "TIMESTAMP":
			// Validate and format datetime (YYYY-MM-DD HH:MM:SS)
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mixValue);
			return $dateTime && $dateTime->format('Y-m-d H:i:s') === $mixValue ? $mixValue : null;

		case "CHARACTER":
		case "STRING":
		case "VAR_STRING":
		case "BLOB":
			if(empty($mixValue)) {
				return null;
			}
			// Decode common HTML entities - but only if they are decoded, no effect otherwise
			// Ensure you're storing raw, sanitized data
			// Decode only if you're processing imports or expecting encoded HTML
			return htmlspecialchars_decode($mixValue, ENT_QUOTES);
		default:
			return $mixValue;
	}
}

?>