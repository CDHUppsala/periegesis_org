<?php

$is_AutoincrementPK = false;
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

// Get either field names, with Adding new records, or the record for Editing
$sql = "SELECT * FROM {$request_Table} {$strGetReordsWhere} LIMIT 1";
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
            $is_AutoincrementPK = sx_IsAutoincrement($request_Table, $xName);
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


function sx_getInsertUpdateRecords($action)
{
    $isUpdate = ($action === 'update');

    $arrAddFields = [];
    $arrAddPlaceholders = [];
    $arrAddValues = [];

    $arrUpdateParts = [];
    $arrUpdateValues = [];

    $str_UserPassword = null;

    foreach (ARR_FieldNames as $index => $field) {
        $xName = $field[0];
        $xType = $field[1];

        // Skip primary key (first column)
        if ($index === 0) continue;

        $data = sx_extractPostValue($xName);
        $xValue = $data['value'];

        // Handle related table ONLY for Add-fields
        if ($data['type'] === 'add') {
            $xValue = sx_handleRelatedTable($xName, $xValue);
        }

        // Handle AddToTable / UpdateTable logic (your original "else" block)
        if ($data['type'] === 'normal') {
            $xValue = sx_handleAddUpdateRelated($xName, $xValue);
        }

        // Normalize value by type
        $xValue = sx_normalizeValue($xName, $xType, $xValue, $str_UserPassword);

        if ($isUpdate) {
            $arrUpdateParts[] = "$xName = ?";
            $arrUpdateValues[] = $xValue;
        } else {
            $arrAddFields[] = $xName;
            $arrAddPlaceholders[] = '?';
            $arrAddValues[] = $xValue;
        }
    }

    if ($isUpdate) {
        return [implode(', ', $arrUpdateParts), $arrUpdateValues];
    }

    return [
        implode(', ', $arrAddFields),
        implode(', ', $arrAddPlaceholders),
        $arrAddValues
    ];
}

function sx_extractPostValue($xName)
{
    if (!empty($_POST["Add$xName"])) {
        return ['value' => trim($_POST["Add$xName"]), 'type' => 'add'];
    }

    if (!empty($_POST["Distinct$xName"])) {
        return ['value' => trim($_POST["Distinct$xName"]), 'type' => 'distinct'];
    }

    return ['value' => $_POST[$xName] ?? null, 'type' => 'normal'];
}

function sx_handleRelatedTable($xName, $xValue)
{
    $table = $_POST["hiddenRTable$xName"] ?? '';
    $field = $_POST["hiddenRField$xName"] ?? '';
    $whereField = $_POST["hiddenRWhereName$xName"] ?? '';
    $whereValue = boolval($_POST["hiddenRWhereValue$xName"] ?? 0);

    if (empty($table) || empty($field) || empty($xValue)) {
        return $xValue;
    }

    if (!sx_checkTableAndFieldNames($table) || !sx_checkTableAndFieldNames($field)) {
        throw new Exception("Invalid table or field");
    }

    $conn = dbconn();

    $stmt = $conn->prepare("SELECT $xName FROM $table WHERE $field = ? LIMIT 1");
    $stmt->execute([$xValue]);
    $id = $stmt->fetchColumn();

    if ((int)$id > 0) {
        return $id;
    }

    $stmt = $conn->prepare("INSERT INTO $table ($field, $whereField) VALUES (?, ?)");
    $stmt->execute([$xValue, $whereValue]);

    return $conn->lastInsertId();
}

function sx_handleAddUpdateRelated($xName, $xValue)
{
    // AddToTable logic
    if (isset(arr_AddUppdateRelated["AddToTable"]) && arr_AddUppdateRelated["AddToTable"][0] == $xName) {
        if ((int)$xValue === 0) {
            $arr = explode(";", arr_AddUppdateRelated["AddToTable"][1]);

            $sFirstName = !empty($arr[2]) ? ($_POST[trim($arr[2])] ?? '') : '';
            $sLastName  = !empty($arr[3]) ? ($_POST[trim($arr[3])] ?? '') : '';

            if (!empty($sFirstName) && !empty($sLastName)) {
                $sqlSelect = $arr[0] . " LIMIT 1";
                $sqlInsert = $arr[1];

                return sx_getNewRecordID($sFirstName, $sLastName, $sqlSelect, $sqlInsert);
            }

            return 0;
        }
    }

    // UpdateTable logic
    if (isset(arr_AddUppdateRelated["UpdateTable"]) && arr_AddUppdateRelated["UpdateTable"][0] == $xName) {
        if ((int)$xValue > 0) {
            $arr = explode(";", arr_AddUppdateRelated["UpdateTable"][1]);

            $sqlUpdate = $arr[0];
            $sName = !empty($arr[1]) ? trim($arr[1]) : '';
            $mixValue = $_POST[$sName] ?? '';

            // Detect DATE/DATETIME field
            $loopType = '';
            foreach (ARR_FieldNames as $field) {
                if (in_array('PublishedDate', $field)) {
                    $loopType = $field[1];
                    break;
                }
            }

            if ($loopType === 'DATE' || $loopType === 'DATETIME') {
                $mixValue = date('Y-m-d');
            }

            $conn = dbconn();
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->execute([$mixValue, $xValue]);
        }
    }

    return $xValue;
}

function sx_normalizeValue($xName, $xType, $xValue, &$str_UserPassword)
{
    switch ($xType) {
        case 'LONG':
        case 'LONGLONG':
            return is_numeric($xValue) ? (int)$xValue : 0;

        case 'SHORT':
            $val = is_numeric($xValue) ? (int)$xValue : 0;
            return min($val, 9999);

        case 'DOUBLE':
        case 'FLOAT':
            return is_numeric($xValue) ? sx_replaceCommaToDot($xValue) : 0;

        case 'DATE':
            return sx_IsDate($xValue) ? $xValue : null;

        case 'DATETIME':
            return sx_IsDateTime($xValue) ? $xValue : null;

        case 'STRING':
        case 'VAR_STRING':

            // Handle password FIRST
            if (REQUEST_Table === 'admin_login') {

                if ($xName === 'UserPassword') {
                    if (!empty($xValue)) {
                        $str_UserPassword = trim($xValue);
                    }
                    return null;
                }

                if ($xName === 'UserPasswordHashed') {
                    if (!empty($str_UserPassword)) {
                        return password_hash($str_UserPassword, PASSWORD_DEFAULT);
                    }
                    // if no new password → keep existing value
                    return $xValue ?: null;
                }
            }

            // Normal string handling
            if (empty($xValue)) return null;
            return sx_replaceBothQuotes(trim($xValue));

        case 'BLOB':
            if (empty($xValue)) return null;

            return !empty($_POST['AddPureText'])
                ? sx_formatTextarea($xValue)
                : sx_replaceQuotes($xValue);

        case 'TINY':
            return ($xValue === 'Yes') ? 1 : 0;

        default:
            if (is_numeric($xValue) && strpos($xValue, ',') !== false) {
                return sx_replaceCommaToDot($xValue);
            }
            return $xValue;
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