<?php
/*
*/
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include "functions.php";

$arrError = [];
$strSelectedTable = '';
$arrTableColumnTypes = [];
$strSelectedFields = '';
$strPKeyName = '';
$strFieldName = '';
$mixCurrentValue = '';
$mixNewValue = '';
$strControlField = '';
$mixControlValue = '';

$readyToUpdate = false;
$includeControlStatement = false;


$strTableFieldNames = "Select a Table to get its Field Names.";

$readyForCheckOrUpdate = false;

if (isset($_GET["clear"]) && $_GET["clear"] == "yes") {
    sx_ClearSessions();
}

// 1 select table and keep alle values in sessions
if (!empty($_POST["GetTblFields"]) && !empty($_POST['SelectedTable'])) {
    sx_ClearSessions();
    $strSelectedTable = trim($_POST['SelectedTable']);
    $arrTableColumnTypes = get_TableColumnType($strSelectedTable);
    $strPKeyName = sx_GetPrimaryKey($strSelectedTable);
    $_SESSION["SelectedTable"] = $strSelectedTable;
    $_SESSION["TableColumnTypes"] = $arrTableColumnTypes;
    $_SESSION["PKeyName"] = $strPKeyName;
} elseif (!empty($_SESSION["SelectedTable"])) {
    $strSelectedTable = $_SESSION["SelectedTable"];
    $arrTableColumnTypes = $_SESSION["TableColumnTypes"];
    $strPKeyName = $_SESSION["PKeyName"];
}

if (!empty($arrTableColumnTypes)) {
    $strTableFieldNames = implode(', ', array_keys($arrTableColumnTypes));
}



// 2 Get selected table fields (to create records) and keep values in sessions
$tempSelectedFields = '';
if (!empty($_POST["GetTblRecords"])) {
    if (empty($strSelectedTable)) {
        sx_ClearSessions();
        $arrError[] = 'You must select a Table!';
    } elseif (!empty($_POST["SelectedFields"]) && !empty(trim($_POST["SelectedFields"])) && str_contains($_POST["SelectedFields"], ',')) {
        $tempSelectedFields = rtrim(trim($_POST["SelectedFields"]), ",");
    } else {
        $arrError[] = 'You must select the <b>Field Names</b> that you will use in your <b>Update Statement</b>!';
        unset($_SESSION["SelectedFields"]);
    }
    sx_ClearUpdateSessions();
}

//3. Check selected table fields, if any
if (!empty($tempSelectedFields)) {
    $arrSelectedFields = explode(',', $tempSelectedFields);
    $arrSelectedFields = array_map('trim', $arrSelectedFields);
    $arrSelectedFields = array_filter($arrSelectedFields);
    $radioPK = false;
    $checkMsg = '';
    foreach ($arrSelectedFields as $field) {
        if (!in_array($field, array_keys($arrTableColumnTypes))) {
            $checkMsg = "The selected field <b>{$field}</b> is not a valid field of the selected table.";
            break;
        }
        if ($field === $strPKeyName) {
            $radioPK = true;
        }
    }
    if (!empty($checkMsg)) {
        $arrError[] = $checkMsg;
    } elseif (!$radioPK) {
        $arrError[] = 'You must include the <b>Primary Key Name</b> in the selected fields.';
    } else {
        $strSelectedFields = implode(', ', $arrSelectedFields);
        $tempSelectedFields = $strSelectedFields;
        $_SESSION["SelectedFields"] = $strSelectedFields;
    }
} elseif (!empty($_SESSION["SelectedFields"]) && !empty($strSelectedTable)) {
    $strSelectedFields  = $_SESSION["SelectedFields"];
    $tempSelectedFields = $strSelectedFields;
    $readyForCheckOrUpdate = true;
}


$iNumberUpdates = 0;
$strWhere = "";


if ($readyForCheckOrUpdate && isset($_POST["CheckOrUpdate"]) && $_POST["CheckOrUpdate"] == "Yes") {
    $readyToUpdate = true;
    $strFieldName = !empty($_POST["FieldName"]) ? trim($_POST["FieldName"]) : '';
    // Use isset() to allow for zero (0) values
    $mixCurrentValue = isset($_POST["CurrentValue"]) ? trim($_POST["CurrentValue"]) : '';
    $mixNewValue = isset($_POST["NewValue"]) ? trim($_POST["NewValue"]) : '';
    $strControlField = !empty($_POST["ControlField"]) ? trim($_POST["ControlField"]) : '';
    $mixControlValue = isset($_POST["ControlValue"]) ? trim($_POST["ControlValue"]) : '';

    // Check the Field Name to be updated and its two Values, as well as teh compatibility of their data type
    // if the Field Name and its Values are numeric, allow for zero (0) values to counteract the function empty())
    if (empty($strFieldName) || (!is_numeric($mixCurrentValue) && empty($mixCurrentValue)) || (!is_numeric($mixCurrentValue) && empty($mixNewValue))) {
        $arrError[] = "The Field Name to be update or at least one of its values is empty.";
        $readyToUpdate = false;
    } elseif (!in_array($strFieldName, array_keys($arrTableColumnTypes))) {
        $arrError[] = "The Field Name <b>{$strFieldName}</b> to be updated is not a valid field of the selected table.";
        $readyToUpdate = false;
    } else {
        // Check data type compatiblility
        $strFieldType = $arrTableColumnTypes[$strFieldName];
        if ($strFieldType == "BLOB") {
            $arrError[] = "Please check the Field Name <b>{$strFieldName}</b>. You cannot update a Field of Text Type <b>BLOB</b>.";
            $readyToUpdate = false;
        } else {
            $radioCheck = sx_checkTypeCompatibility($strFieldType, $mixCurrentValue);
            if ($radioCheck) {
                $mixCurrentValue = sx_getTypeCompatibleValue($strFieldType, $mixCurrentValue);
            } else {
                $arrError[] = "The Current Field Value <b>{$mixCurrentValue}</b> is not compatible to the Data Type <b>{$strFieldType}</b> of the Field Name <b>{$strFieldName}</b>.";
                $readyToUpdate = false;
            }
            $radioCheck = sx_checkTypeCompatibility($strFieldType, $mixNewValue);
            if ($radioCheck) {
                $mixNewValue = sx_getTypeCompatibleValue($strFieldType, $mixNewValue);
            } else {
                $arrError[] = "The New Field Value <b>{$mixNewValue}</b> is not compatible to the Data Type <b>{$strFieldType}</b> of the Field Name <b>{$strFieldName}</b>.";
                $readyToUpdate = false;
            }
        }

        if ($readyToUpdate) {
            $_SESSION["FieldName"] = $strFieldName;
            $_SESSION["CurrentValue"] = $mixCurrentValue;
            $_SESSION["NewValue"] = $mixNewValue;
        } else {
            unset($_SESSION["FieldName"]);
            unset($_SESSION["CurrentValue"]);
            unset($_SESSION["NewValue"]);
        }
    }

    // Check the use of Control Field, its name and the data type compatibility of its value
    // If the field is numeric, allow for zero (0) values 
    $includeControlStatement = true;
    if (empty($strControlField)) {
        $includeControlStatement = false;
        $strControlField = '';
        $mixControlValue = '';
        unset($_SESSION["ControlField"]);
        unset($_SESSION["ControlValue"]);
    } elseif (empty($mixControlValue) && !is_numeric($mixControlValue)) {
        $arrError[] = "The Control Field Name is set but its Value is <b>Empty</b>. Please set a Value or remove the Control Field Name.";
        $mixControlValue = '';
        unset($_SESSION["ControlValue"]);
        $readyToUpdate = false;
    } else {
        if (!in_array($strControlField, array_keys($arrTableColumnTypes))) {
            $arrError[] = "The Control Field Name <b>{$strControlField}</b> is not a valid field of the selected table.";
            $readyToUpdate = false;
        } else {
            $strFieldType = $arrTableColumnTypes[$strControlField];
            if ($strFieldType == "BLOB") {
                $arrError[] = "Please check the Control Field Name <b>{$strControlField}</b>. You cannot use as Control a Field of Text Type <b>BLOB</b>.";
                $readyToUpdate = false;
            } else {
                $radioCheck = sx_checkTypeCompatibility($strFieldType, $mixControlValue);
                if ($radioCheck) {
                    $mixControlValue = sx_getTypeCompatibleValue($strFieldType, $mixControlValue);
                } else {
                    $arrError[] = "The Control Field Value <b>{$mixControlValue}</b> is not compatible to the Data Type <b>{$strFieldType}</b> of the Control Field Name <b>{$strControlField}</b>.";
                    $readyToUpdate = false;
                }
            }
        }
        if ($readyToUpdate) {
            $_SESSION["ControlField"] = $strControlField;
            $_SESSION["ControlValue"] = $mixControlValue;
        } else {
            unset($_SESSION["ControlField"]);
            unset($_SESSION["ControlValue"]);
        }
    }
} elseif (isset($_SESSION["FieldName"]) && isset($_SESSION["CurrentValue"]) && isset($_SESSION["NewValue"])) {
    // Keep variables for pagination and ordering 
    $readyToUpdate = true;
    $strFieldName = $_SESSION["FieldName"];
    $mixCurrentValue = $_SESSION["CurrentValue"];
    $mixNewValue = $_SESSION["NewValue"];
    if (isset($_SESSION["ControlField"]) && isset($_SESSION["ControlValue"])) {
        $includeControlStatement = true;
        $strControlField = $_SESSION["ControlField"];
        $mixControlValue = $_SESSION["ControlValue"];
    }
}

/**
 * Check update is always included
 *  - It is executed and displayed on the end of the page, but get its WHERE-statement here
 * Pursue Update is executed here, if the button is clicked
 */
if ($readyToUpdate) {

    /**
     * WHERE statement is used:
     *  - To get an array fo PK values that will be updated
     *  - To check and display results on the bottom of the page
     */
    if (is_numeric($mixCurrentValue)) {
        $strWhere = " WHERE $strFieldName = {$mixCurrentValue}";
    } else {
        $strWhere = " WHERE $strFieldName = '{$mixCurrentValue}'";
    }
    if ($includeControlStatement) {
        if (is_numeric($mixControlValue)) {
            $strWhere .= " AND $strControlField = $mixControlValue";
        } else {
            $strWhere .= " AND $strControlField = '{$mixControlValue}'";
        }
    }
}

/**
 * For check of records to be updated, use only the $strWhere variable
 * When update is requested, set $strWhere to Null
 */
$radioPursueUpdate = isset($_POST["PursueUpdate"]) && !empty($_POST["PursueUpdate"]) ? true : false;
if ($radioPursueUpdate && $readyToUpdate) {
    $arrPKeys = [];
    $sql = "SELECT $strPKeyName FROM $strSelectedTable $strWhere";
    //secho $sql . "<hr>";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);

    if ($rs) {
        $arrPKeys = $rs;
    }
    $rs = null;
    $strWhere = null;

    if (is_array($arrPKeys) && !empty($arrPKeys)) {
        $sql = "UPDATE $strSelectedTable SET $strFieldName = ? WHERE $strPKeyName = ?";
        $stmt = $conn->prepare($sql);

        $iNumberUpdates = count($arrPKeys);
        foreach ($arrPKeys as $key) {
            $stmt->execute([$mixNewValue, $key[0]]);
        }
    }
}
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Creator" content="FTh">
    <title>Public Sphere - Update Records</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <style>
        body {
            padding-top: 5rem !important;
        }

        td .button {
            font-size: 1.2em;
        }
    </style>
    <script src="../js/jq/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($(".jqHelpButton").length) {
                $(".jqHelpButton").click(function() {
                    var sxThisID = $(this).attr("data-id");
                    $("#" + sxThisID).slideToggle(300);
                });
            };
        });
    </script>
</head>

<body class="body">
    <header id="header">
        <h2>Update a Field in Multiple Records of Any Table</h2>
        <div class="margin_right_auto">
            <button class="button jqHelpButton" data-id="helpSearch">HELP</button>
        </div>
    </header>

    <?php
    include __DIR__ . '/help.html';
    if (!empty($arrError)) { ?>
        <div class="msgError"><?php echo implode('<br>', $arrError) ?></div>
    <?php
    }

    if ($iNumberUpdates > 0) { ?>
        <div class="msgSuccess">Total number of updated fields: <?= $iNumberUpdates ?></div>
    <?php
    } ?>

    <?php

    $strNonUsedTables = return_NonUsedTables() ?? '';
    if (!empty($strNonUsedTables)) {
        $arrNonUsedTables = json_decode($strNonUsedTables, true);
    } ?>
    <section>

        <h2>1. Select Table and Fields</h2>
        <p>Select a table and click on <b>Get Table Fields</b> to get its fields. Copy then and paste in <b>Selected Table Fields</b> only fields that are relevant for the update,<br>
            including the Primary Key Name. Click then on <b>Get Table Records</b> to review the values that you want to change.</p>
        <form name="tblRecords" method="POST" action="default.php">
            <fieldset class="flex">
                <label><b>Table Name:</b></label>
                <select name="SelectedTable" id="SelectedTable" onchange="sx_getFields(this.value)">
                    <option value="">Select Table</options>
                        <?php
                        $arrTableList = sx_getTableList();
                        foreach ($arrTableList as $table) {
                            $strTable = $table[0];
                            if (empty($arrNonUsedTables) || !in_array($strTable, $arrNonUsedTables)) {
                                $strSelected = '';
                                if ($strTable === $strSelectedTable) {
                                    $strSelected = ' selected';
                                }
                                echo " <option value=\"{$strTable}\"{$strSelected}>{$strTable}</options>";
                            }
                        } ?>

                </select>
                <input class="button" type="submit" value="Get Table Fields" name="GetTblFields">
                <input class="button" type="submit" value="Get Table Records" name="GetTblRecords">
                <a class="button" href="default.php?clear=yes">RESET ALL</a>
            </fieldset>

            <fieldset class="flex">
                <label>Selected Field Names:</label>
                <input type="text" style="width: 100%" value="<?= $tempSelectedFields ?>" name="SelectedFields" placeholder="Copy and Pastate only Field Names that you will use in your Update Statement, including the Primary Key. Separate them by a comma (,)." id="SelectedFields" title="Separate Field Names by a comma (,)">
            </fieldset>
            </table>
        </form>
        <div class="infoMsg"><?php echo $strTableFieldNames ?></div>
        <?php
        if (isset($_SESSION["SelectedTable"])) { ?>
            <script>
                sx_getFieldsOnly('<?= $_SESSION["SelectedTable"] ?>');
            </script>
        <?php } ?>

        <h2>2. Update a Table Field</h2>
        <p> Copy from the above <b>Selected Field Names</b> both the <b>Field Name</b>, to be updated, and the <b>Control Field Name</b>.<br>
            If you do not define the Name and Value for a <b>Control Field</b>, all <b>Current Values</b> of the selected <b>Field Name</b> will be replaced with the <b>New Value</b>.</p>
        <p>The Update Statement: <code>SET <b>Field Name = New Field Value</b> WHERE <b>Field Name = Current Field Value</b> AND <b>Control Field Name = Control Field Value</b></code>.</p>
        <form action="default.php" method="post" name="sxUpdateForm">
            <input type="hidden" name="CheckOrUpdate" value="Yes">
            <table class="no_bg">
                <tr>
                    <th>Primary Key</th>
                    <th>Field Name</th>
                    <th>Current Field Value</th>
                    <th>New Field Value</th>
                    <th>Control Field Name</th>
                    <th>Control Field Value</th>
                </tr>
                <tr>
                    <td><input type="text" readonly size="20" value="<?= $strPKeyName ?>" name="PKey" placeholder="Primary Key" title="Read Only: Primary Key" id="PKey"></td>
                    <td><input type="text" size="20" value="<?= $strFieldName ?>" name="FieldName"></td>
                    <td><input type="text" size="20" value="<?= $mixCurrentValue ?>" name="CurrentValue"></td>
                    <td><input type="text" size="20" value="<?= $mixNewValue ?>" name="NewValue"></td>
                    <td><input type="text" size="20" value="<?= $strControlField ?>" name="ControlField"></td>
                    <td><input type="text" size="20" value="<?= $mixControlValue ?>" name="ControlValue"></td>
                </tr>
                <tr>
                    <td colspan="5"> </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left;" title="Select a Table and then Relevant Table Fields and click finally to Get Tablle Reords to be able to update.">
                        <input class="button" type="submit" value="Update Field" name="PursueUpdate">
                    </td>
                    <td colspan="4" style="text-align: right;" title="Select a Table and then Relevant Table Fields and click finally to Get Tablle Reords to be able to update.">
                        <input class="button" type="submit" value="Check Affected Records Before Update" name="CheckUpdate">
                    </td>
                </tr>
            </table>

        </form>
    </section>
    <section>

        <?php
        $radioFields = false;
        $arrFields = null;
        if (!empty($strSelectedFields)) {
            $radioFields = true;
            if (strpos($strSelectedFields, ",") > 0) {
                $arrFields = explode(",", $strSelectedFields);
            }
        }

        if ($radioFields && !empty($strSelectedTable)) {
            $strName = isset($_GET["sortName"]) && !empty($_GET["sortName"]) ? trim($_GET["sortName"]) : '';

            if (!empty($strName)) {
                if (!empty($_SESSION["Sorting"])) {
                    if ($_SESSION["Sorting"] == "ASC") {
                        $sort = "DESC";
                    } else {
                        $sort = "ASC";
                    }
                } else {
                    $sort = "ASC";
                }
                $strOrderBy = " ORDER BY " . $strName . " " . $sort;
                $_SESSION["Sorting"] = $sort;
                $_SESSION["OrderBy"] = $strOrderBy;
            } elseif (isset($_SESSION["OrderBy"]) && !empty($_SESSION["OrderBy"])) {
                $strOrderBy = $_SESSION["OrderBy"];
            } else {
                $strOrderBy = "";
            }

            $sql = "SELECT $strSelectedFields FROM $strSelectedTable $strWhere $strOrderBy";

            $intRecordCount = sx_getRecordCount($sql);

            if ($intRecordCount > 0) {
                $intPageSize = 40;

                $pageCount = ceil($intRecordCount / $intPageSize);
                if ($pageCount < $intRecordCount / $intPageSize) {
                    $pageCount++;
                }

                $currentPage = isset($_GET["page"]) && !empty($_GET["page"]) ? (int)($_GET["page"]) : 0;

                if ($currentPage < 1) {
                    $currentPage = 1;
                }
                if ($currentPage > $pageCount) {
                    $currentPage = $pageCount;
                }

                $iStartRecord = $intPageSize * $currentPage - $intPageSize;
                $sql .= " LIMIT {$iStartRecord}, $intPageSize";
            } else {
                $pageCount = 1;
            }
            echo "<hr>$sql<hr>";
            //exit();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aResults = null;
            if ($rows) :
                $aResults = $rows;
            endif;
            $stmt = null;
            $rows = null;
            /*
            echo '<pre>';
            print_r($aResults);
            echo '</pre>';
            */

            if ($pageCount > 0) { ?>
                <p><b>Total: <?php echo $intRecordCount ?> records</b>
                    | Click on Table Headers to <b>Sort</b> records by a Field Name.
                    Reclick to change between <b>Ascending</b> and <b>Descending</b> order.</p>
                <table>
                    <tr>
                        <?php
                        foreach ($arrFields as $field) { ?>
                            <th><a title="Order by this field" href="default.php?sortName=<?= $field ?>"><?= $field ?></a></th>
                        <?php
                        } ?>
                    </tr>
                    <?php
                    if (is_array($aResults) && !empty($aResults)) {
                        foreach ($aResults as $row) {
                            echo '<tr>';
                            foreach ($row as $field) {
                                echo "<td>{$field}</td>";
                            }
                            echo '</tr>';
                        }
                    } ?>
                </table>

                <?php
                if ($intRecordCount === 0) {
                    echo "<p><b>Nor records found</b></p>";
                } else { ?>
                    <div style="margin-top: 20px; font-weight: bold; font-size: 1.4em"><?php sx_arrowPageNav("default.php?page=") ?></div>
                    <div>Page: <?php sx_numberOfPageNav("default.php?page=") ?></div>
        <?php
                }
            }
            $aResults = null;
        } ?>

        <?php
        if (!empty($strSelectedTable)) {
            echo "<hr><h4>Field Names and Data Types for Table: {$strSelectedTable}</h4><hr>";
            sx_getTableColumnType($strSelectedTable);
        } ?>
    </section>
</body>

</html>