<?php


/**
 * Get the Table Column Names from the File Fields,
 * which might be less than the columns of the table
 */
$arr_ColumnNames = [];
$arr_ColumnNamesQuests = [];
$arrFirstRow = $arrData[0];
foreach ($arrFirstRow as $key => $value) {
    // Already checked, but just in case
    if (in_array($key, $arr_FieldNames)) {
        // Exclude the PK
        if ($key !== $str_PrimaryKeyName) {
            $arr_ColumnNames[] = $key;
            $arr_ColumnNamesQuests[] = "$key = ?";
        }
    }
}
$str_ColumnNames = implode(",", $arr_ColumnNames);
$str_ColumnNamesQuests = implode(",", $arr_ColumnNamesQuests);

// Prepare the Update statement
$sql = "UPDATE $str_DBTableName 
SET $str_ColumnNamesQuests 
WHERE $str_PrimaryKeyName = ?";
$stmt_update = $conn->prepare($sql);

// Loop through the PHP Array to update rows in the Table
$intLine = 1;
$intCountUpdates = 0;
$arrNotUpdated = [];
foreach ($arrData as $row) {
    // Get the row values, add the value of PK and update the row
    $arrColumnValues = [];
    $PK_value = '';
    foreach ($row as $key => $value) {
        if (in_array($key, $arr_FieldNames)) {
            $strTyp = $arr_FieldNameType[$key];
            $newValue = sx_getTypeCompatibleValue($strTyp, $value);
            if ($key === $str_PrimaryKeyName) {
                $PK_value = $newValue;
            } else {
                $arrColumnValues[] = $newValue;
            }
        }
    }
    // Add lastly the value of the PK
    $arrColumnValues[] = $PK_value;
    $stmt_update->execute($arrColumnValues);

    if ($stmt_update->rowCount() == 0) {
        $arrNotUpdated[] = "Line $intLine with PK Value $PK_value Not updated.";
    } else {
        $intCountUpdates++;
    }
    $intLine++;
}

$reasons = "<p>There are two reasons for that:
    <ul><li>The field values of every line in the File are identical to the corresponding rows in the table. <b>Identical</b> values are not updated.
    <ul><li>This means that the existing file is already up to date - if not the second reason, bellow, is valid.</li></ul></li>
    <li>Primary Key values from the file <b>does not exist</b> in the Table. Only table rows with existed Primary Key values are updated.
    <ul><li>If you expect that the file includes new records, click on <b>Insert</b> to add them.</li>
    <li>If you are not sure, you can still click on Insert, as only non-existed Primary Key values are added.</ul>";

if ($intCountUpdates > 0) {

    $intLine--;
    echo "<div class='maxWidthWide msgSuccess'>Totally $intCountUpdates rows have been updated in the Table $str_DBTableName of totaly $intLine lines in the File.</div>";
} else {
    echo '<div class="maxWidthWide bg">';
    echo '<h2>No row has been updated in the Database Table</h2>';
    echo $reasons;
    echo '</div>';
}

if (!empty($arrNotUpdated) && $intCountUpdates > 0) {
    echo '<div class="maxWidthWide bg">';
    echo "<h2>". count($arrNotUpdated) ." lines in the File have Not been Updated</h2>";
    echo $reasons;
    echo '</div>';
    //echo '<ul><li>' . implode('</li><li>', $arrNotUpdated) . '</li></ul>';
}

$arrNotUpdated = null;
unset($arrData);
