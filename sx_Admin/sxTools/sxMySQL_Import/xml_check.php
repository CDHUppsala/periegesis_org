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
//$data = simplexml_load_file($import_FileName);

$reader = new XMLReader();

if (!$reader->open($import_FileName)) {
    $radioContinue = false;
    echo '<div class="msgError">';
    echo "<p><b>Failed to load the XML File</b></p>";
    echo '</div>';
}

if ($radioContinue) {
    // Move to the root element
    while ($reader->read() && $reader->nodeType != XMLReader::ELEMENT) {
    }

    // Loop through the first-level child elements (e.g., rows or nodes)
    $int_Columns = 0;
    $intNode = 1;
    $radioFirstNode = true;
    $primaryKeys = [];

    // Loop through the first-level child elements (e.g., rows or nodes)
    while ($reader->read()) {
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->depth == 1) {

            // Create a DOMDocument and import the current node
            $doc = new DOMDocument();

            // Suppress warnings with '@'
            $node = @$reader->expand();

            if ($node === false) {
                $radioContinue = false;
                echo '<div class="msgError">';
                echo '<p><b>Error: Cannot Read XML Node</b></p>';
                echo "<p>Error expanding XML at <b>Node {$intNode}</b>.</p>";
                displayXmlErrors();
                echo '</div>';
                break;
            }

            $importedNode = $doc->importNode($node, true);
            $doc->appendChild($importedNode);

            // Convert the DOMDocument to SimpleXML
            $child = simplexml_import_dom($doc->documentElement);

            if ($child === false) {
                $radioContinue = false;
                echo '<div class="msgError">';
                echo '<p><b>Error converting DOM to SimpleXML</b></p>';
                echo "<p>Conversion error at <b>Node {$intNode}</b>.</p>";
                echo '</div>';
                break;
            }

            /*
            echo "<pre>";
            print_r($child);
            echo "</pre>";
            */

            // Loop through sub-elements (fields or columns)
            if ($radioFirstNode) {
                foreach ($child as $name => $value) {
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
                            <p>No date have been imported to the database table.</p>
                        </div>";
                        break 2;
                    }
                    $int_Columns++;
                }
                $radioFirstNode = false;
            }

            // Compatibility Check of the entire file before Update/Inser data

            // Check if the number of all Node columns is eaqual to header columns
            $iLoopColumns = count($child->children());
            if ($iLoopColumns !== $int_Columns) {
                $radioContinue = false;
                echo '<div class="msgError">';
                echo '<p><b>Error: Incompatible Number of Fields</b></p>';
                echo "<p>The XML File at <b>Node {$intNode}</b> has only <b>{$iLoopColumns}</b> fields from expected <b>{$int_Columns}</b> ones.</p>";
                echo "<p>No date have been imported to the database table.</p>";
                echo '</div>';
                break;
            }

            foreach ($child as $name => $value) {
                $loopValue = trim((string)$value);
                if ($name == $str_PrimaryKeyName) {
                    // Check for compatible Primary Key values
                    if ($str_PrimaryKeyType === 'INT' && !is_numeric($loopValue)) {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                        <p><b>Error: Invalide Primary Key Value</b></p>
                        <p>The Primary Key <b>$str_PrimaryKeyName</b> at <b>Node $intNode</b> is not a valid integer value.</p>
                        <p>Value: <b>$loopValue</b> is invalid. No date have been imported to the database table.</p>
                    </div>";
                        break 2;
                    }
                    // Check for unique PK values
                    if (isset($primaryKeys[$loopValue])) {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                            <p><b>Error: Duplicate Primary Key</b></p>
                            <p>Primary Key: <b>$loopValue</b> at <b>Node $intNode</b> is not unique.</p>
                            <p>Please ensure that all Primary Keys in the XML File are unique.</p>
                            <p>No date have been imported to the database table.</p>
                        </div>";
                        break 2;
                    }
                    $primaryKeys[$loopValue] = true;
                } else {
                    $iKey = array_search($name, $arr_FieldNames);
                    if ($iKey !== false) {
                        $s_Type = $arr_FieldTypes[$iKey];
                        if (!sx_checkTypeCompatibility($s_Type, $loopValue)) {
                            if (empty($loopValue)) {
                                $loopValue = '[Empty Value]';
                            }
                            $radioContinue = false;
                            echo "<div class='msgError'>
                                <p><b>Error: Incompatible Data Type</b></p>
                                <p>Incompatible data type found at <b>Node $intNode</b> for field <b>$name</b>.</p>
                                <p>Value: <b>$loopValue</b> is not compatible with expected type: <b>$s_Type</b>.</p>
                                <p>Please ensure that all field values match the required data types.</p>
                                <p>No date have been imported to the database table.</p>
                            </div>";
                            break 2;
                        }
                    } else {
                        $radioContinue = false;
                        echo "<div class='msgError'>
                                <p><b>Error: Non-Existing Field Name</b></p>
                                <p>The Field Name <b>$name</b> in the XML File at <b>Node $intNode</b> does not exists as Table Column.</p>
                                <p>Please ensure that all field names are equal to the table columns.</p>
                                <p>No date have been imported to the database table.</p>
                                </div>";
                        break 2;
                    }
                }
            }
            $intNode++;
        }
    }
    $reader->close();

    /*
    echo "<pre>";
    print_r($primaryKeys);
    echo "</pre>";
    */

    if ($radioContinue) {
        // The statement to check if PK exists
        $sql_check = "SELECT $str_PrimaryKeyName FROM $str_DBTableName WHERE $str_PrimaryKeyName = ?";
        $stmt_check = $conn->prepare($sql_check);

        $int_Node = $intNode - 1;
        echo '<ol>';
        echo "<li><div class='msgSuccess'>The <b>Field Names</b> of the <b>First Node</b> of the XML File are identical to the Table's <b>Column Names</b></div></li>";

        echo "<li><div class='msgSuccess'>All <b>$int_Node Nodes</b> of the JSON File have an equal number of <b>{$int_Columns} Fields</b> each.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>Primary Keys</b> in all $int_Node Nodes of the XML File have a <b>Valid Data Type</b> and <b>Unique Values</b>.</div></li>";
        echo "<li><div class='msgSuccess'>All <b>{$int_Columns} Field Names</b> and <b>Field Values</b> in all $int_Node Nodes of the XML File have compatible <b>Names and Data Types</b> to the corresponding Table Columns</div></li>";
        if (!$radio_ImportSubmited) {
            $reader->close();
            echo '<li><div class="msgSuccess"><b>You can now safely import the file!</b></div></li>';
        }
        echo '</ol>';
    } else {
        $reader->close();
    }
}
