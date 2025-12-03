<?php

$arr_ColumnNames = [];
$arr_ColumnNamesQuests = [];
$arr_ColumnQuests = [];
$arr_ColumnTypes = [];

$columnSeparator = ';';
$int_Columns = 0;

$radioContinue = true;
$radioAnyUppdate = false;

$data = fopen($import_FileName, "r");

$strUpdateMsg = "";
if ($data === false) {
    echo '<div class="msgError">';
        echo "<p><b>Failed to loading the CSV File</b></p>";
        echo '<p>Could not read the CSV File!</p>';
    echo '</div>';
} else {

    // The first row contains the Filed Names of the CSV file
    // Get the separator, assuming either comma or semicolon
    $header = fgets($data);
    $columnSeparator = strpos($header, ",") !== false ? "," : ";";

    $arr_Headers = explode($columnSeparator, $header);
    $arr_Headers = array_map('trim', $arr_Headers);

    $arr_FirstRowFields = $arr_Headers;

    $int_Columns = count($arr_Headers);
    /**
     * Get the Field Names from the first row of the CSV File
     *  - Check them egainst the name of Table Columns
     *  - Get their Data Type
     *  - Prepare the variables for import statements
     *      1. For update: Exclude Primary Key from the statement 
     *      2. For Insert: include Primary Key in the statement 
     */

    echo '<pre>';
    print_r($arr_Headers);
    print_r($arr_FieldNames);
    echo '</pre>';
    
    for ($c = 0; $c < $int_Columns; $c++) {
        $name = $arr_Headers[$c];
        $iKey = array_search($name, $arr_FieldNames);
        if ($iKey !== false) {
            if ($import_Type == "Update") {
                if ($name != $str_PrimaryKeyName) {
                    $arr_ColumnNames[] = $name;
                    $arr_ColumnNamesQuests[] = $name . " = ?";
                    $arr_ColumnTypes[] = $arr_FieldTypes[$iKey];
                }
            } else {
                $arr_ColumnNames[] = $name;
                $arr_ColumnQuests[] = "?";
                $arr_ColumnTypes[] = $arr_FieldTypes[$iKey];
            }
        } else {
            $radioContinue = false;
            echo "<div class='msgError'>
                <p><b>Error: Incompatible Field and Column Names</b></p>
                <p>The fields of the CSV File are not identical to the Columns of the selected Database Table.</p>
                <p>Please check if the selected Database Table and the Import File are correct.</p>
                <p>The field <b>$name</b> does not exists in the Database Table.</p>
            </div>";
            break;
        }
    }

    // Compatibility Check of the entire file before Update/Inser data
    if ($radioContinue) {
        $primaryKeys = [];
        $intLine = 1;
        // Continue from line 2
        while (($arrLineData = fgetcsv($data, 0, $columnSeparator,'"','\\')) !== false) {
            // Check if line columns are eaqual to header columns
            $iLineColumns = count($arrLineData);
            if ($iLineColumns !== $int_Columns) {
                $radioContinue = false;
                echo '<div class="msgError">';
                echo '<p><b>Error: Incompatible Number of Fields</b></p>';
                echo "<p>The CSV File at <b>Line {$intLine}</b> have only <b>{$iLineColumns}</b> fields from expected <b>{$int_Columns}</b> ones.</p>";
                echo '</div>';
                break;
            }

            if ($radioContinue) {
                for ($c = 0; $c < $int_Columns; $c++) {
                    $name = $arr_Headers[$c];
                    $value = $arrLineData[$c];
                    if ($name == $str_PrimaryKeyName) {
                        // Check for compatible Primary Key value
                        if ($str_PrimaryKeyType === 'INT' && !is_numeric($value)) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                <p>The Primary Key <b>$str_PrimaryKeyName</b> at <b>Line $intLine</b> is not a valid integer value.</p>
                                <p>Value: <b>$value</b> is invalid. Import has been stopped.</p>
                            </div>";
                            break 2;
                        }
                        // Check for unique PK values
                        if (isset($primaryKeys[$value])) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                    <p>Duplicate Primary Key found in the CSV File at <b>Line $intLine</b>.</p>
                                    <p>Primary Key: <b>$value</b> is not unique.</p>
                                    <p>Please ensure that all Primary Keys in the File are unique.</p>
                                </div>";
                            break 2;
                        }
                        $primaryKeys[$value] = true;
                    } else {
                        $iKey = array_search($name, $arr_FieldNames);
                        $s_Type = $arr_FieldTypes[$iKey];
                        if (!sx_checkTypeCompatibility($s_Type, $value)) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                <p>Incompatible data type found at <b>Line $intLine</b> for field <b>$name</b>.</p>
                                <p>Value: <b>$value</b> is not compatible with expected type: <b>$s_Type</b>.</p>
                                <p>Please ensure all field values match the required data types.</p>
                            </div>";
                            break 2;
                        }
                    }
                }
            }
            $intLine++;
        }
    }
    $int_Lines = 0;
    if ($radioContinue) {

        // The statement checking if PK exists
        $sql_check = "SELECT $str_PrimaryKeyName FROM $str_DBTableName WHERE $str_PrimaryKeyName = ?";
        $stmt_check = $conn->prepare($sql_check);

        $int_Lines = $intLine - 1;
        echo '<ol>';
        echo "<li><div class='msgSuccess'>The <b>Field Names</b> of the CSV File are identical to the Table's <b>Column Names</b></div></li>";
        echo "<li><div class='msgSuccess'>All <b>$int_Lines Lines</b> of the CSV File have an equal number of <b>{$int_Columns} Fields</b> each.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>Primary Keys</b> in all $int_Lines Lines of the CSV File have a <b>Valid Data Type</b> and <b>Unique Values</b>.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>{$int_Columns} Field Values</b> in all $int_Lines Lines of the CSV File have compatible <b>Data Types</b> to the corresponding Table Columns</div></li>";
        if (!$radio_ImportSubmited) {
            fclose($data);
            echo '<li><div class="msgSuccess"><b>You can now safely import the file!</b></div></li>';
        }
        echo '</ol>';
    } else {
        fclose($data);
    }
}
