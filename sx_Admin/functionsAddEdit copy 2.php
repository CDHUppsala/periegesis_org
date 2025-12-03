<?php

$boolIsAuto = false;
$strFormValidation = '';

$radioGetRecords = false;
$strGetReordsWhere = '';
// Get also field values (not only types) to Used in edit.php
$arr_EditResults = '';

/**
 * Get the Field Names and Field Types for adding and editing records
 * Get also records FOR preparing editing - but not FOR executing it (not for updating the edit page)
 * $strIDName is String and $strIDValue is Numeric (change to intIDValue)
 */

if (empty($_POST["Edit"]) && !empty($strIDName) && (int)$strIDValue > 0) {
    $radioGetRecords = true;
    $strGetReordsWhere = " WHERE " . $strIDName . " = " . $strIDValue;
    if ($radio_TablesWithLoginAdminID && intval($intLoginUserLevel) > 1) {
        $strGetReordsWhere .= " AND (LoginAdminID = " . $intLoginAdminID . " OR LoginAdminID = 0) ";
    }
}

$sql = "SELECT * FROM {$request_Table} {$strGetReordsWhere} LIMIT 1";
echo $sql;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_BOTH);
if ($radioGetRecords && $rs) {
    $arr_EditResults = $rs;
}

$maxcol = $stmt->columnCount();
$arrFieldNames = [];
for ($i = 0; $i < $maxcol; $i++) {
    $meta = $stmt->getColumnMeta($i);
    $xName = $meta["name"];
    $xType = $meta["native_type"];
    $arrFieldNames[$i][0] = $xName;
    $arrFieldNames[$i][1] = $xType;

    if (empty($_POST["strAddHTML"]) && empty($_POST["AddPureText"]) && empty($_POST["Edit"])) {
        if ($i == 0) {
            $boolIsAuto = sx_IsAutoincrement($request_Table, $xName);
        }
        /**
         * Generate a javascipt string with the required fields for Form Validation
         */
        if (in_array($xName, $arrRequiredFields)) {
            if (!empty($strFormValidation)) {
                $strFormValidation .= " || ";
            }
            if ($xType == 'LONG' || $xType == 'SHORT' || $xType == 'DOUBLE' || $xType == 'FLOAT' || $xType == 'LONGLONG') {
                if (sx_getRelationType($xName) == 2) {
                    //For relation type 2 - either choice meets the requirement
                    $strFormValidation .= "(form." . $xName . ".value == 0 && form.Add" . $xName . '.value == "")';
                } else {
                    $strFormValidation .= "form." . $xName . ".value == 0";
                }
            } else {
                $strFormValidation .= "form." . $xName . '.value == "" ';
            }
        }
    }
}
$stmt = null;
$rs = null;


/**
 * Get Field Descriptions/Comments from the Table
 * and add them to the above array of the Table Field Names and FieldTypes
 */

if (!empty($arrFieldNames)) {
    $sql = "Select COLUMN_NAME, COLUMN_COMMENT
			FROM Information_schema.columns
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION";
    $stmt = $conn->prepare($sql);
    $stmt->execute([sx_TABLE_SCHEMA, $request_Table]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $cResults = $rs;
    }
    $stmt = null;
    $rs = null;
    $maxRows = count($cResults);
    for ($r = 0; $r < $maxRows; $r++) {
        $xName = $cResults[$r][0];
        $xValue = $cResults[$r][1];
        if ($xName == $arrFieldNames[$r][0]) {
            $arrFieldNames[$r][2] = $xValue;
        }
    }
}

define("ARR_FieldNames", $arrFieldNames);

function sx_getNewRecordID($fn, $ln, $qSelect, $qIncert)
{
    $conn = dbconn();
    $radioTemp = true;
    $fstmt = $conn->prepare($qSelect);
    $fstmt->execute([$fn, $ln]);
    $fid = $fstmt->fetchColumn();
    if (intval($fid) > 0) {
        $radioTemp = false;
        return $fid;
    }
    $fid = null;
    $fstmt = null;

    if ($radioTemp) {
        $fstmt = $conn->prepare($qIncert);
        $fstmt->execute([$fn, $ln]);
        $fid = $conn->lastInsertId();
        $fstmt = null;
        return $fid;
    }
}

/**
 * Gets Form Variables and Form Values based on the above array of Field Names.
 * Check and correct the Form Values agains the above array of Field Types.
 * Prepare the SQL-string for Adding or Updating the record
 */
//
function sx_getInsertUpdateRecords($action)
{
    $arrAddFields = '';
    $arrAddPrepareValues = '';
    $arrAddValues = [];
    $strUppdatePrepare = '';
    $arrUppdateValues = [];
    $isUpdate = ($action === 'update');

    if (is_array(ARR_FieldNames) && !empty(ARR_FieldNames)) {

        $iCount = count(ARR_FieldNames);

        for ($i = 0; $i < $iCount; $i++) {
            $xName = ARR_FieldNames[$i][0];
            $xType = ARR_FieldNames[$i][1];

            /**
             * Get the Form Value of every Field, using its name
             * Start with fields with Relation Type 2 and 3, which have 2 inputs:
             *      . An Input field with Name = Prefix "Add" + Field Name
             *      . A Selection Box with Name = Field Name
             * - Type 2: add new record in related table and get its ID
             *      . if an ID is already selected in Selection Box, don't add any record
             * - Type 3: Get all distinct values from a field and Add it as new value
             */
            if (isset($_POST["Add" . $xName]) && !empty($_POST["Add" . $xName])) {
                $xValue = trim($_POST["Add" . $xName]);
                // Add a new record in the related table and get its ID-number
                $strRelatedTableName = $_POST["hiddenRTable" . $xName] ?? '';
                $strRelatedFieldName = $_POST["hiddenRField" . $xName] ?? '';
                // Adds other fields value as they are defined in WHERE-condition, if any
                $strRelatedWhereFieldName = $_POST["hiddenRWhereName" . $xName] ?? '';
                $strRelatedWhereFieldValue = boolval($_POST["hiddenRWhereValue" . $xName] ?? 0);

                /*        
                <input type="hidden" name="hiddenRWhereNameThemeID" value="Actual">
                <input type="hidden" name="hiddenRWhereValueThemeID" value="True">
                <input type="hidden" name="hiddenRTableThemeID" value="themes">
                <input type="hidden" name="hiddenRFieldThemeID" value="ThemeName">
                <input type="text" size="40" name="AddThemeID" value=""> 
                */

                if (!empty($strRelatedTableName) && !empty($strRelatedFieldName)) {
                    $conn = dbconn();
                    /**
                     * Check if the record in the related table already exists and get its ID
                     */
                    $tempID = 0;
                    $sqlCheck = "SELECT  $xName FROM $strRelatedTableName 
                    WHERE $strRelatedFieldName = ? LIMIT 1";
                    $fstmt = $conn->prepare($sqlCheck);
                    $fstmt->execute([$xValue]);
                    $tempID = $fstmt->fetchColumn();
                    $fstmt = null;
                    if ((int) $tempID > 0) {
                        $xValue = $tempID;
                    } else {
                        $sql = "INSERT INTO $strRelatedTableName 
					($strRelatedFieldName, $strRelatedWhereFieldName)
                    VALUES(?,?)";
                        $fstmt = $conn->prepare($sql);
                        $fstmt->execute([$xValue, $strRelatedWhereFieldValue]);
                        $xValue = $conn->lastInsertId();
                        $fstmt = null;
                    }
                }
            } elseif (isset($_POST["Distinct" . $xName]) && !empty($_POST["Distinct" . $xName])) {
                $xValue = trim($_POST["Distinct" . $xName]);
            } else {
                $xValue = $_POST[$xName] ?? '';
                if (isset(arr_AddUppdateRelated["AddToTable"]) && arr_AddUppdateRelated["AddToTable"][0] == $xName) {
                    if (intval($xValue) == 0) {
                        /**
                         * If Records in Text Table include Authors:,
                         * get the Author ID, if exists, or add a new Author and get its ID
                         * Not for Books and advanced Texts 
                         * with separate table for multiple text to author relations
                         */
                        $arr = explode(";", arr_AddUppdateRelated["AddToTable"][1]);
                        /*
                    [0]->SELECT AuthorID FROM text_authors WHERE FirstName = ? AND LastName = ?;
                    [1]->INSERT INTO text_authors (FirstName,LastName) VALUES(?,?)
                    [2]->FirstName
                    [3]->LastName
                    */

                        $sFirstName = $_POST[trim($arr[2])] ?? '';
                        $sLastName = $_POST[trim($arr[3])] ?? '';
                        if (!empty($sFirstName) && !empty($sLastName)) {
                            $sqlSelect = trim($arr[0]) . " LIMIT 1 ";
                            $sqlIncert = trim($arr[1]);
                            $xValue = sx_getNewRecordID($sFirstName, $sLastName, $sqlSelect, $sqlIncert);
                        } else {
                            $xValue = 0;
                        }
                    }
                }
                if (isset(arr_AddUppdateRelated["UpdateTable"]) && arr_AddUppdateRelated["UpdateTable"][0] == $xName) {
                    /*
                        "UpdateTable":[
                        "ThemeID",
                        "UPDATE themes SET LastInDate = ? WHERE ThemeID = ?; PublishedDate; ThemeID"]}
                     */
                    if (intval($xValue) > 0) {
                        $arr = explode(";", arr_AddUppdateRelated["UpdateTable"][1]);
                        /*
                             [0]-> UPDATE themes SET LastInDate = ? WHERE ThemeID = ?
                             [1]-> PublishedDate
                             [2]-> ThemeID 
                        */
                        $sqlUpdate = $arr[0];
                        $sName = trim($arr[1]);
                        $mixValue = $_POST[$sName] ?? '';
                        $loopType = "";
                        for ($z = 0; $z < $iCount; $z++) {
                            if (in_array("PublishedDate", ARR_FieldNames[$z])) {
                                $loopType = ARR_FieldNames[$z][1];
                                break;
                            }
                        }
                        if ($loopType == "DATE" || $loopType == "DATETIME") {
                            $mixValue = date('Y-m-d');
                        }
                        $conn = dbconn();
                        $stmt = $conn->prepare($sqlUpdate);
                        $stmt->execute([$mixValue, $xValue]);
                    }
                }
            }
echo $xType . '='. $xValue .'<br>';
            // echo $i ."=>". $xType ." : ". $xName  ." : ". $xValue ."<br>";
            if ($i > 0) {
                switch ($xType) {
                    case 'LONG':
                    case 'LONGLONG':
                        $xValue = is_numeric($xValue) ? (int)$xValue : 0;
                    case 'SHORT':
                        $xValue = is_numeric($xValue) ? (int)$xValue : 0;
                        $xValue = $xValue > 9999 ? 9999 : $xValue;
                    case 'DOUBLE':
                    case 'FLOAT':
                        $xValue = is_numeric($xValue) ? sx_replaceCommaToDot($xValue) : 0;
                    case 'DATE':
                    case 'DATETIME':
                        $xValue = (sx_IsDate($xValue) || sx_IsDateTime($xValue)) ? $xValue : null;
                    case 'STRING':
                    case 'VAR_STRING':
                        if (!empty(($xValue))) {
                            $xValue = sx_replaceBothQuotes($xValue);
                        } else {
                            $xValue = null;
                        }
                    case 'BLOB':
                        if (!empty($xValue)) {
                            if (!empty($_POST["AddPureText"])) {
                                $xValue = sx_formatTextarea($xValue);
                            } else {
                                $xValue = sx_replaceQuotes($xValue);
                            }
                        } else {
                            $xValue = null;
                        }
                    case 'TINY':
                        $xValue = $xValue === "Yes" ? 1 : 0;
                }

                if ($isUpdate) {
                    if (!empty($strUppdatePrepare)) {
                        $strUppdatePrepare .= ", ";
                    }
                } else {
                    if (!empty($arrAddFields)) {
                        $arrAddFields .= ",";
                    }
                    if (!empty($arrAddPrepareValues)) {
                        $arrAddPrepareValues .= ",";
                    }
                }

                if ($isUpdate) {
                    $strUppdatePrepare .= $xName . " = ?";
                    $arrUppdateValues[] = $xValue;
                } else {
                    $arrAddFields .= " " . $xName;
                    $arrAddPrepareValues .= " ?";
                    $arrAddValues[] = $xValue;
                }
            }
        }

        if ($isUpdate) {
            /*
        echo $strUppdatePrepare ."<hr>";
        print_r($arrUppdateValues);
        exit;
        */
            return array($strUppdatePrepare, $arrUppdateValues);
        } else {
            /*
        echo $arrAddFields ."<hr>";
        echo $arrAddPrepareValues ."<hr>";
        print_r($arrAddValues);
        exit;
        */
            return array($arrAddFields, $arrAddPrepareValues, $arrAddValues);
        }
    }
}

function getBookAuthorsNames($id)
{
    $sNames = "";
    $conn = dbconn();
    $sql = "SELECT a.FirstName, a.LastName FROM book_to_authors AS b
		INNER JOIN book_authors AS a ON b.AuthorID = a.AuthorID WHERE b.BookID = ?
		ORDER BY b.AuthorOrdinal ASC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        $iTemp = count($rs);
        for ($r = 0; $r < $iTemp; $r++) {
            if (!empty($sNames)) {
                $sNames = $sNames . ", ";
            }
            $sNames = $sNames . $rs[$r]["FirstName"] . " " . $rs[$r]["LastName"];
        }
        $rs = null;
        $stmt = null;
        return $sNames;
    }
}

function sx_getBookAuthorsNotes()
{
?>
    <div class="text maxWidth" style="display: none; white-space: normal; border-radius: 5px; margin-left: auto; margin-bottom: 0; text-align: left; padding: 8px; background: #fff; color: #09c; font-weight: normal;">
        <ul class="nerrow">
            <li>The Books and their Authors are entered into two different tables: <b>Books</b> and <b>BookAuthors</b>.</li>
            <li>Their relations are enter into a third table: <b>BookToAuthors</b>.</li>
            <li>In that way, you can relate a book to <b>multiple</b> authors.</li>
            <li>The <b>Temporal Field</b> above is used to add the ID(s) of one or more Book Authors.</li>
            <li>When you save the book, the Book ID and the ID(s) of Book Authors are <b>automatically</b> added into the <b>BookToAuthors</b> table.</li>
            <li>You can also <b>eddit</b> the <b>BookToAuthors</b> table directly, like any table.</li>
        </ul>
    </div>
<?php
}
function sx_getBookAuthorsInput($radioNotes, $required, $strIDs)
{
    $strRequired = "";
    $strErrorMessage = 'If <b>Error</b> on authors, ';
    if ($required) {
        $strRequired = 'required';
        $strErrorMessage = "";
    } ?>
    <tr>
        <th colspan="2">
            <div class="text alignRight">
                <?php echo $strErrorMessage ?> Click on <b>Load Authors</b> to insert the ID of one or more Authors:
                <input type="text" id="jqInsertAthors" name="BookToAuthors" placeholder="Temporal Field" value="<?php echo $strIDs ?>" class="smal_input" <?php echo $strRequired ?>>
                <?php if ($radioNotes) { ?>
                    <span class="infoWhite jqInfoToggle">?</span>
                <?php } ?>
            </div>
            <?php if ($radioNotes) {
                sx_getBookAuthorsNotes();
            } ?>
        </th>
    </tr>
<?php
} ?>