<?php
$strXMLTable = "";
$arr_ColumnNames = [];
$arr_ColumnNamesQuestions = [];
$arr_ColumnQuestions = [];
$arr_ColumnTypes = [];
$radioContinue = true;
$radioAnyUpdate = false;

/*
echo '<pre>';
print_r($arr_FieldNames);
print_r($arr_FieldTypes);
print_r($arr_FieldNameType);
echo '</pre>';
echo  $str_PrimaryKeyName .'<br>';
echo  $str_PrimaryKeyType .'<br>';
*/

libxml_use_internal_errors(true);
$data = simplexml_load_file($import_FileName);

//$strUpdateMsg = "";
if ($data === false) {
    $radioContinue = false;
    echo '<div class="msgError">';
    echo "<p><b>Failed to load the XML File</b></p>";
    foreach (libxml_get_errors() as $error) {
        echo $error->message . "<br>";
    }
    libxml_clear_errors();
    echo '</div>';
}

if ($radioContinue) {
    // Process the first child to validate and prepare
    $int_Columns = 0;
    foreach ($data->children()[0] as $child) {
        $name = $child->getName();
        $iKey = array_search($name, $arr_FieldNames);
        if ($iKey !== false) {
            if ($import_Type == "Update") {
                // variables for update
                if ($name != $str_PrimaryKeyName) {
                    $arr_ColumnNames[] = $name;
                    $arr_ColumnNamesQuestions[] = "$name = ?";
                    $arr_ColumnTypes[] = $arr_FieldTypes[$iKey];
                }
            } else {
                // variables for insert
                $arr_ColumnNames[] = $name;
                $arr_ColumnQuestions[] = "?";
                $arr_ColumnTypes[] = $arr_FieldTypes[$iKey];
            }
        } else {
            $radioContinue = false;
            echo "<div class='msgError'>
                    <p><b>Error in XML File</b></p>
                    <p>The Field Name <b>$name</b> in the Import File does not exist as Column Name in the Database Table.</p>
                    <p>Please check if the selected Database Table and the Import File are correct.</p>
                </div>";
            break;
        }
        $int_Columns++;
    }
}

// Compatibility Check of the entire file before Update/Inser data
if ($radioContinue) {
    echo "<div class='msgSuccess'>The <b>Field Names</b> of the <b>First Node</b> of the XML File are identical to the Table's <b>Column Names</b></div>";
    $primaryKeys = [];
    $intNode = 1;
    foreach ($data->children() as $child) {
        // Check if the number of all Node columns is eaqual to header columns
        $iLoopColumns = count($child);
        if ($iLoopColumns !== $int_Columns) {
            $radioContinue = false;
            echo '<div class="msgError">';
            echo '<p><b>Error: Incompatible Number of Fields</b></p>';
            echo "<p>The XML File at <b>Node {$intNode}</b> have only <b>{$iLoopColumns}</b> fields from expected <b>{$int_Columns}</b> ones.</p>";
            echo '</div>';
            break;
        }
        if ($radioContinue) {

            foreach ($child as $name => $value) {
                $loopValue = trim((string)$value);
                if ($name == $str_PrimaryKeyName) {
                    // Check for compatible Primary Key values
                    if ($str_PrimaryKeyType === 'INT' && !is_numeric($loopValue)) {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                        <p>The Primary Key <b>$str_PrimaryKeyName</b> at <b>Node $intNode</b> is not a valid integer value.</p>
                        <p>Value: <b>$loopValue</b> is invalid. Import has been stopped.</p>
                    </div>";
                        break 2;
                    }
                    // Check for unique PK values
                    if (isset($primaryKeys[$loopValue])) {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                            <p>Duplicate Primary Key found in the XML File at <b>Node $intNode</b>.</p>
                            <p>Primary Key: <b>$loopValue</b> is not unique.</p>
                            <p>Please ensure all Primary Keys in the XML File are unique.</p>
                        </div>";
                        break 2;
                    }
                    $primaryKeys[$loopValue] = true;
                } else {
                    $iKey = array_search($name, $arr_FieldNames);
                    if ($iKey !== false) {
                        $s_Type = $arr_FieldTypes[$iKey];
                        if (!sx_checkTypeCompatibility($s_Type, $loopValue)) {
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                <p>Incompatible data type found at <b>Node $intNode</b> for field <b>$name</b>.</p>
                                <p>Value: <b>$loopValue</b> is not compatible with expected type: <b>$s_Type</b>.</p>
                                <p>Please ensure all field values match the required data types.</p>
                            </div>";
                            break 2;
                        }
                    } else {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                                <p>The Field Name <b>$name</b> in the XML File at <b>Node $intNode</b> does not exists as Table Column.</p>
                                <p>Please ensure that all field names are equal to the table columns.</p>
                                </div>";
                        break 2;
                    }
                }
            }
        }
        $intNode++;
    }
    if ($radioContinue) {
        // The statement checking if PK exists
        $sql_check = "SELECT $str_PrimaryKeyName FROM $str_DBTableName WHERE $str_PrimaryKeyName = ?";
        $stmt_check = $conn->prepare($sql_check);

        $int_Node = $intNode - 1;
        echo "<div class='msgSuccess'>All <b>$int_Node Nodes</b> of the JSON File have an equal number of <b>{$int_Columns} Fields</b> each.</div>";
        echo "<div class='msgSuccess'>All <b>Primary Keys</b> in all $intNode Nodes of the XML File have a <b>Valid Data Type</b> and <b>Unique Values</b>.</div>";
        echo "<div class='msgSuccess'>All <b>{$int_Columns} Field Names</b> and <b>Field Values</b> in all $intNode Nodes of the XML File have compatible <b>Names and Data Types</b> to the corresponding Table Columns</div>";
    } else {
        $data = null;
    }
}
