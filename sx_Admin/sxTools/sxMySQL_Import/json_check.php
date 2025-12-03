<?php
$arr_ColumnNames = [];
$arr_ColumnNamesQuests = [];
$arr_ColumnQuests = [];
$arr_ColumnTypes = [];
$radioContinue = true;
$radioAnyUppdate = false;

$data = file_get_contents($import_FileName);
$arr_json = json_decode($data, true);
$data = null;
$strMsgErr = "";
switch (json_last_error()) {
    case JSON_ERROR_NONE:
        break;
    case JSON_ERROR_DEPTH:
        $strMsgErr = "Maximum stack depth exceeded";
        break;
    case JSON_ERROR_STATE_MISMATCH:
        $strMsgErr = "Invalid or malformed JSON";
        break;
    case JSON_ERROR_CTRL_CHAR:
        $strMsgErr = "Control character error";
        break;
    case JSON_ERROR_SYNTAX:
        $strMsgErr = "Syntax error";
        break;
    case JSON_ERROR_UTF8:
        $strMsgErr = "Malformed UTF-8 characters";
        break;
    default:
        $strMsgErr = "Unknown error";
        break;
}

if (empty($arr_json) || !is_array($arr_json) || !empty($strMsgErr)) {
    $radioContinue = false;
    echo '<div class="msgError">';
    echo "<p><b>Failed to loading the JSON File</b></p>";
    echo "<p>$strMsgErr</p>";
    echo '</div>';
}

if ($radioContinue) {

    /**
     * Get the Column Names from the first index of the JSON File
     *  - Check them egainst the name of Table Columns
     *  - Get their Data Type
     *  - Prepare the variables for import statements
     *      1. For update: Exclude Primary Key from the statement 
     *      2. For Insert: include Primary Key in the statement 
     */

    $int_Columns = 0;
    foreach ($arr_json[0] as $name => $value) {
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
            echo '<div class="msgError">';
            echo "<p><b>Error: Incompatible Field and Column Names</b></p>
                <p>The Field Names of the JSON File are not identical to the Columns of the selected Database Table.</p>
                <p>Please check if the selected Database Table and the Import File are correct.</p>
                <p>The field <b>$name</b> does not exists in the Database Table.</p>
            </div>";
            break;
        }
        $int_Columns++;
    }

    // Compatibility Check of the entire file before Update/Inser data
    if ($radioContinue) {
        $primaryKeys = [];
        $intLine = 1;
        foreach ($arr_json as $arrData) {
            // Check if the number of all Node columns is eaqual to header columns
            $iLoopColumns = count($arrData);
            if ($iLoopColumns !== $int_Columns) {
                $radioContinue = false;
                echo '<div class="msgError">';
                echo '<p><b>Error: Incompatible Number of Fields</b></p>';
                echo "<p>The JSON File at <b>Row {$intLine}</b> have only <b>{$iLoopColumns}</b> fields from expected <b>{$int_Columns}</b> ones.</p>";
                echo '</div>';
                break;
            }

            if ($radioContinue) {
                foreach ($arrData as $name => $value) {
                    if ($name == $str_PrimaryKeyName) {
                        // Check for compatible Primary Key value
                        if ($str_PrimaryKeyType === 'INT' && !is_numeric($value)) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                    <p>The Primary Key <b>$str_PrimaryKeyName</b> at <b>Node $intLine</b> is not a valid integer value.</p>
                                    <p>Value: <b>$value</b> is invalid. Import has been stopped.</p>
                                </div>";
                            break 2;
                        }
                        // Check for unique PK values
                        if (isset($primaryKeys[$value])) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                        <p>Duplicate Primary Key found in the JSON File at <b>Node $intLine</b>.</p>
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
                                    <p>Incompatible data type found at <b>Node $intLine</b> for field <b>$name</b>.</p>
                                    <p>Value: <b>$value</b> is not compatible with expected type: <b>$s_Type</b>.</p>
                                    <p>Please ensure that all field values match the required data types.</p>
                                </div>";
                            break 2;
                        }
                    }
                }
            }
            $intLine++;
        }
    }
    $int_Node = 0;

    if ($radioContinue) {
        // The statement checking if PK exists
        $sql_check = "SELECT $str_PrimaryKeyName FROM $str_DBTableName WHERE $str_PrimaryKeyName = ?";
        $stmt_check = $conn->prepare($sql_check);

        $int_Node = $intLine - 1;
        echo '<ol>';
        echo "<li><div class='msgSuccess'>The <b>Field Names</b> of the JSON File are identical to the Table's <b>Column Names</b></div></li>";
        echo "<li><div class='msgSuccess'>All <b>$int_Node Nodes</b> of the JSON File have an equal number of <b>{$int_Columns} Fields</b> each.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>Primary Keys</b> in all $int_Node Nodes of the JSON File have a <b>Valid Data Type</b> and <b>Unique Values</b>.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>{$int_Columns} Field Names</b> and <b>Field Values</b> in all $int_Node Nodes of the JSON File have compatible <b>Names and Data Types</b> to the corresponding Table Columns</div></li>";
        if (!$radio_ImportSubmited) {
            unset($arr_json);
            echo '<li><div class="msgSuccess"><b>You can now safely import the file!</b></div></li>';
        }
        echo '</ol>';
    } else {
        unset($arr_json);
    }
}
