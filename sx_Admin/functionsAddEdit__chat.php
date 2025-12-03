<?php
if (!empty($_POST["Edit"]) && !empty($strIDName) && (int)$strIDValue > 0) {
    $boolGetRecords = true;
    $strGetReordsWhere = " WHERE {$strIDName} = :idValue";
    if ($radio_TablesWithLoginAdminID && (int)$intLoginUserLevel > 1) {
        $strGetReordsWhere .= " AND (LoginAdminID = {$intLoginUserLevel} OR LoginAdminID = 0)";
    }
}

$sql = "SELECT * FROM {$request_Table} {$strGetReordsWhere} {$strLimitRecords_1}";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':idValue', $strIDValue, PDO::PARAM_INT);
$stmt->execute();


$xType = match ($xType) {
    'LONG', 'SHORT', 'DOUBLE', 'FLOAT', 'LONGLONG' => 'number',
    default => 'text',
};

$sql = "SELECT COLUMN_NAME, COLUMN_COMMENT 
        FROM information_schema.columns 
        WHERE TABLE_SCHEMA = :schema 
        AND TABLE_NAME = :table 
        ORDER BY ORDINAL_POSITION";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':schema' => sx_TABLE_SCHEMA,
    ':table' => $request_Table
]);

/**
 * =============================================
 */

function processFieldValue($type, $value)
{
    switch ($type) {
        case 'LONG':
        case 'LONGLONG':
            return is_numeric($value) ? intval($value) : 0;
        case 'SHORT':
            $value = is_numeric($value) ? intval($value) : 0;
            return $value > 9999 ? 9999 : $value;
        case 'DOUBLE':
        case 'FLOAT':
            return is_numeric($value) ? sx_replaceCommaToDot($value) : 0;
        case 'DATE':
        case 'DATETIME':
            return (sx_IsDate($value) || sx_IsDateTime($value)) ? $value : null;
        case 'STRING':
        case 'VAR_STRING':
            return trim($value) ?: null;
        case 'BLOB':
            return !empty($value) ? sx_replaceQuotes($value) : null;
        case 'TINY':
            return trim($value) === "Yes" ? 1 : 0;
        default:
            return $value;
    }
}

function fetchRelatedId($conn, $tableName, $fieldName, $value)
{
    $sql = "SELECT id FROM $tableName WHERE $fieldName = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$value]);
    return $stmt->fetchColumn();
}

function sx_getInsertUpdateRecords($action)
{
    $isUpdate = ($action === 'update');
    $arrFields = [];
    $arrValues = [];
    $sqlParts = [];

    if (!is_array(ARR_FieldNames) || empty(ARR_FieldNames)) {
        throw new Exception("Field definitions are not properly configured.");
    }

    foreach (ARR_FieldNames as $field) {
        $fieldName = $field[0];
        $fieldType = $field[1];
        $fieldValue = $_POST[$fieldName] ?? '';

        // Process field value
        $fieldValue = processFieldValue($fieldType, $fieldValue);

        if ($isUpdate) {
            $sqlParts[] = "$fieldName = ?";
        } else {
            $arrFields[] = $fieldName;
            $arrValues[] = '?';
        }

        $arrValues[] = $fieldValue;
    }

    if ($isUpdate) {
        $sql = "UPDATE tableName SET " . implode(", ", $sqlParts) . " WHERE id = ?";
        return [$sql, $arrValues];
    } else {
        $sql = "INSERT INTO tableName (" . implode(", ", $arrFields) . ") VALUES (" . implode(", ", $arrValues) . ")";
        return [$sql, $arrValues];
    }
}
