<?php

if ($radio_truncateTable) {
    $conn->exec("TRUNCATE TABLE $str_DBTableName");
}

/**
 * Get the Table Column Names from the File Fields,
 * which might be less than the columns of the table
 */
$arr_ColumnNames = [];
$arr_ColumnQuests = [];
$arrFirstRow = $arrData[0];
foreach ($arrFirstRow as $key => $value) {
    // Already checked, but just in case
    if (in_array($key, $arr_FieldNames)) {
        $arr_ColumnNames[] = $key;
        $arr_ColumnQuests[] = '?';
    }
}
$str_ColumnNames = implode(",", $arr_ColumnNames);
$str_ColumnQuest = implode(",", $arr_ColumnQuests);

// Prepare the Incert statement
$sql = "INSERT INTO  $str_DBTableName  ($str_ColumnNames) 
    VALUES($str_ColumnQuest)";
$stmt_insert = $conn->prepare($sql);

// Prepare to Check if PK exists
$sql_check = "SELECT $str_PrimaryKeyName FROM $str_DBTableName 
    WHERE $str_PrimaryKeyName = ?";
$stmt_check = $conn->prepare($sql_check);

// Loop through the PHP Array to add rows in the Table
$intLine = 1;
$intCountIncerted = 0;
$arrNotUnique = [];
foreach ($arrData as $row) {
    // 1. Check if PK is unique
    $PK_value = trim($row[$str_PrimaryKeyName]);
    $radioContinue = true;
    if (!empty($PK_value)) {
        if ($str_PrimaryKeyType === 'INT') {
            $PK_value = (int) $PK_value;
        }
        $stmt_check->execute([$PK_value]);
        $mixReturn = $stmt_check->fetchColumn();
        if ($mixReturn) {
            $radioContinue = false;
            $arrNotUnique[] = "Line: {$intLine} - PK {$PK_value} exists in Database Table";
        }
    } else {
        $radioContinue = false;
        $arrNotUnique[] = "Line: {$intLine} - PK is empty";
    }

    // 2. Insert rows with unique PK
    if ($radioContinue) {
        $arrColumnValues = [];
        foreach ($row as $key => $value) {
            if (in_array($key, $arr_FieldNames)) {
                $strTyp = $arr_FieldNameType[$key];
                $newValue = sx_getTypeCompatibleValue($strTyp, $value);
                $arrColumnValues[] = $newValue;
            }
        }
        $stmt_insert->execute($arrColumnValues);
        $intCountIncerted++;
    }
    $intLine++;
}



if ($intCountIncerted > 0) {
    // Reset auto increment if exists
    if ($radio_PKisAutoIncrement) {
        $sql = "ALTER TABLE " . $str_DBTableName . " AUTO_INCREMENT = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt = null;
    }

    $intLine--;
    echo "<div class='maxWidthWide msgSuccess'>Totally $intCountIncerted rows have been Inserted in the Table $str_DBTableName of totaly $intLine Lines in the CSV File.</div>";
}else{
    echo '<div class="maxWidthWide bg">';
    echo "<h2>No row has been inserted in the Database Table</h2>";
    echo "<p>The reason is that all Pimary Key values from the File Lines already exist in the Database Table. So, the Table is already up to date!</p>";
    echo "</div>";
}

if (!empty($arrNotUnique) && $intCountIncerted > 0) {
    echo "<h2>". count($arrNotUnique) ." lines in the File have not been inserted in the Database Table</h2>";
    echo "<p>The reason is that the Pimary Key values from the other File Lines already exist in the Database Table.</p>";
    //echo '<ul><li>' . implode('</li><li>', $arrNotUnique) . '</li></ul>';
}
$arrNotUnique = null;
unset($arrData);
