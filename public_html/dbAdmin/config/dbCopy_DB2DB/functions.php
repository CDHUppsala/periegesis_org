<?php

$radioAddToMySQLDatabase = True;
$s_DateSymbol = "#";
if ($radioAddToMySQLDatabase) {
    $s_DateSymbol = "'";
}

function sx_quateCleaner($txt)
{
    $txt = apostroph($txt);
    $txt = quoteLatin($txt);
    return $txt;
}
// Replaces apostroph (' BY ’ &#700;).
function apostroph($afix)
{
    return  str_replace("'", "’", trim($afix));
}
// Replaces quote (" BY ”).
function quoteLatin($afix)
{
    return  str_replace('"', "”", trim($afix));
}

function sx_getMySQLTables($cn)
{
    $LoopTable = array();
    $stmt = $cn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    $rec = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
    foreach ($rec as $table) {
        $LoopTable[] = $table[0];
    }
    $rec = null;
    return $LoopTable;
}

function sx_getAccessTables($com)
{
    $arrTables = array();
    $cat = new COM("ADOX.Catalog", NULL, CP_UTF8, NULL);
    if ($cat) {
        $cat->ActiveConnection = $com;
        $tables = $cat->Tables;
        $count = $tables->Count();
        for ($i = 0; $i < $count; $i++) {
            $table = $tables->Item($i);
            $tableName = strtolower($table->Name);
            if (strtolower($table->Type) == "table" && substr($tableName, 0, 1) != "~") {
                $arrTables[] = $tableName;
            }
        }
        $table = null;
        $tables = null;
    }
    $cat = null;
    return $arrTables;
}

/**
 * To be used for adding Field Descriptions in MySQL
 * To show Unicode you must use Provider=Microsoft.ACE.OLEDB.12.0
 */
function get_fieldsAccessDesription($sTable, $arrSortingFields, $cna)
{
    $objCat = new COM("ADOX.Catalog", null, CP_UTF8, null);
    $arr_Temp = array();
    if ($objCat) {
        $objCat->ActiveConnection = $cna;

        $objTable = $objCat->Tables[$sTable];

        $index = $objTable->Indexes;
        $count = $index->count();
        $arrIndexes = array();
        for ($i = 0; $i < $count; $i++) {
            // When an idext includes multiple columns
            // An array with the name of indexes and their respective columns
            foreach ($index[$i]->Columns as $col) {
                $arrIndexes[$col->Name] = $index[$i]->Name;
            }
        }
        $index = null;
        //unset($index);

        $objColumns = $objTable->Columns;
        $iClms = $objColumns->Count();
        for ($c = 0; $c < $iClms; $c++) {
            $Name = $arrSortingFields[$c][0];

            $arr_Temp[$c][0] = $Name;
            $arr_Temp[$c][1] = $objColumns[$Name]->Type;
            $arr_Temp[$c][2] = $objColumns[$Name]->DefinedSize;
            $sIndex = "";
            if (array_key_exists($Name, $arrIndexes)) {
                $sIndex = $arrIndexes[$Name];
                if ($sIndex == "PrimaryKey") {
                    $sIndex = "PRI";
                } else {
                    $sIndex = "MUL";
                }
            }
            $arr_Temp[$c][3] = $sIndex;
            $arr_Temp[$c][4] = $objColumns[$Name]->Properties["Description"]->Value;
        }
    }
    $objColumns = null;
    //unset($objColumns);
    $objTable = null;
    //unset($objTable);
    $objCat = null;
    //unset($objCat);
    return $arr_Temp;
}

function get_fieldsAccessDesription_Schema($sTable, $cn)
{
    $arr_Temp = array();
    $c = 0;
    $col = $cn->OpenSchema(4, array(null, null, $sTable));
    while (!$col->EOF) {
        $Name = $col->Fields["COLUMN_NAME"]->value;
        $arr_Temp[$Name][0] = $Name;
        $arr_Temp[$Name][1] = $col->Fields["DATA_TYPE"]->value;
        $arr_Temp[$Name][2] = $col->Fields["CHARACTER_MAXIMUM_LENGTH"]->value;
        $arr_Temp[$Name][3] = $col->Fields["DESCRIPTION"]->value;

        $c++;
        $col->MoveNext();
    }
    $col = null;
    return $arr_Temp;
}


function set_auto_increment($sTable, $cn)
{
    $strSQL = " ALTER TABLE " . strtolower($sTable) . " AUTO_INCREMENT = 1";
    $cn->query($strSQL);
}

//change to access
function get_PrimaryKey_Access($tbl, $acn)
{
    $rs = new COM("ADODB.Recordset", NULL, CP_UTF8, NULL);
    $sql = "SELECT TOP 1 * FROM $tbl";
    $rs->Open($sql, $acn, 3, 3);
    $iCols = $rs->Fields->Count;
    $ret = null;
    for ($i = 0; $i < $iCols; $i++) {
        if ($rs->Fields[$i]->Properties["IsAutoincrement"]) {
            $ret =  $rs->Fields[$i]->Name;
            break;
        }
    }
    $rs->Close();
    $rs = null;
    return $ret;
}

function get_PrimaryKey_MySQL($tbl, $cn)
{
    $ret = "";
    $sql = "SHOW KEYS FROM $tbl WHERE Key_name = 'PRIMARY'";
    $fstmt = $cn->prepare($sql);
    $fstmt->execute();
    $frs = $fstmt->fetch();

    if ($frs) {
        $ret = $frs["Column_name"];
    }
    $fstmt = null;
    $frs = null;
    return $ret;
}

/**
 * Used together with get_arrFields_MySQL_Meta
 */
function get_arrFields_Access($sTable, $acn)
{
    $rs = new COM("ADODB.Recordset", NULL, CP_UTF8, NULL);
    $sql = "SELECT TOP 1 * FROM " . $sTable;
    $rs->Open($sql, $acn, 3, 3);
    $maxcol = $rs->Fields->Count();
    $arrFields = array();
    for ($i = 0; $i < $maxcol; $i++) {
        $arrFields[$i][0] = $rs->Fields[$i]->Name;
        $arrFields[$i][1] = $rs->Fields[$i]->Type;
        $arrFields[$i][2] = $rs->Fields[$i]->DefinedSize;
        $strPrimaryKye = "";
        if ($rs->Fields[$i]->Properties["IsAutoincrement"]->Value) {
            $strPrimaryKye = "primary_key";
        }
        $arrFields[$i][3] = $strPrimaryKye;
    }
    $rs->Close();
    $rs = null;
    return  $arrFields;
}

function get_arrFields_MySQL($sTable, $cn)
{

    //    $stmt = $cn->query("DESCRIBE " . $sTable);
    //    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $cn->query("SHOW COLUMNS FROM " . $sTable);
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $l = 0;
    $arrFields = array();
    foreach ($rs as $column) {
        $arrFields[$l][0] = $column['Field'];
        $arrFields[$l][1] = $column['Type'];
        $arrFields[$l][2] = $column['Key'];
        $l++;
    }
    $stmt = null;
    $rs = null;
    return  $arrFields;
}
/**
 * Alternative to the above
 * Used together with get_arrFields_Access
 */
function get_arrFields_MySQL_Meta($sTable, $cn)
{
    $stmt = $cn->query("SELECT * FROM $sTable LIMIT 1");
    $colcount = $stmt->columnCount();
    $arrFields = array();
    for ($c = 0; $c < $colcount; $c++) {
        $meta = $stmt->getColumnMeta($c);
        $arrFields[$c][0] = $meta['name'];
        $arrFields[$c][1] = $meta['native_type'];
        $arrFields[$c][2] = $meta['len'];
        $arrFields[$c][3] = @$meta['flags'][1]; //primary_key
        $arrFields[$c][4] = @$meta['flags'][0]; //not_null, multiple_key
        $arrFields[$c][5] = @$meta['flags'][2]; //unique_key
    }
    $stmt = null;
    return  $arrFields;
}

/**
 * COLUMN_KEY =: PRI,MUL
 * EXTRA => auto_increment
 * CHARACTER_SET_NAME => utf8mb4
 */
function get_arrFields_MySQL_Schema($shema, $table, $cn)
{
    $ret = null;
    $sqlmy = "Select
        COLUMN_NAME,
        DATA_TYPE,
        CHARACTER_MAXIMUM_LENGTH,
        COLUMN_KEY,
        COLUMN_COMMENT,
        COLUMN_TYPE,
        EXTRA,
        COLUMN_DEFAULT,
        CHARACTER_SET_NAME,
        IS_NULLABLE,
        NUMERIC_PRECISION,
        NUMERIC_SCALE,
        DATETIME_PRECISION
    FROM Information_schema.columns 
    WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
    ORDER BY ORDINAL_POSITION ";
    $fstmt = $cn->prepare($sqlmy);
    $fstmt->execute([$shema, $table]);
    $frs = $fstmt->fetchAll();
    if ($frs) {
        $ret = $frs;
    }
    $frs = null;
    $fstmt = null;
    return $ret;
}


function get_resultsAccess($sTable, $PKName, $cna, $radioSubstr = false)
{
    $arrRet = null;
    $strOrderBy = "";
    if (!empty($PKName)) {
        $strOrderBy = " ORDER BY $PKName ASC ";
    }
    $strSQL = "SELECT * FROM " . $sTable . $strOrderBy;
    //echo $strSQL;
    $rs = new COM("ADODB.Recordset", NULL, CP_UTF8, NULL);
    $rs->Open($strSQL, $cna, 3, 3);
    if (!$rs->EOF) {
        $arrRet = array();
        $intCoount = $rs->Fields->Count;
        $row = 0;
        while (!$rs->EOF) {
            for ($cl = 0; $cl < $intCoount; $cl++) {
                $s_Type = $rs->Fields[$cl]->Type;
                $s_Value = (string) $rs->Fields[$cl]->Value;
                $arrRet[$row][$cl][0] = $rs->Fields[$cl]->Name;
                $arrRet[$row][$cl][1] = $s_Type;
                if ($radioSubstr && intval($s_Type) > 200) {
                    if (strlen($s_Value) > 64) {
                        $s_Value = substr(strip_tags($s_Value), 0, 64) . "... (" . strlen($s_Value) . ")";
                    }
                }
                $arrRet[$row][$cl][2] = $s_Value;
            }
            $row++;
            $rs->MoveNext;
        }
    }
    $rs->Close();
    $rs = null;
    return $arrRet;
}

//Dim arrFieldNames
function get_resultsMySQL($sTable, $PKName, $cn, $radioSubstr = false)
{
    $arrRet = null;
    $strOrderBy = "";
    if (!empty($PKName)) {
        $strOrderBy = " ORDER BY $PKName ASC ";
    }
    $strSQL = "SELECT * FROM " . $sTable . $strOrderBy;
    $stmt = $cn->query($strSQL);
    $colcount = $stmt->columnCount();
    $arrTypes = array();
    for ($c = 0; $c < $colcount; $c++) {
        $meta = $stmt->getColumnMeta($c);
        $arrTypes[] = $meta['native_type'];
    }

    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        $arrRet = array();
        $iRow = 0;
        foreach ($rs as $row) {
            $c = 0;
            foreach ($row as $sFName => $sFValue) {
                $sFType = $arrTypes[$c];
                $arrRet[$iRow][$c][0] = $sFName;
                $arrRet[$iRow][$c][1] = $sFType;
                if ($radioSubstr && ($sFType == "BLOB" || $sFType == "VAR_STRING" || $sFType == "varchar" || $sFType == "varchar(255)" || $sFType == "mediumtext")) {
                    if (strlen($sFValue) > 64) {
                        $sFValue = substr(strip_tags($sFValue), 0, 64) . "... (" . strlen($sFValue) . ")";
                    }
                }
                $arrRet[$iRow][$c][2] =  $sFValue;
                $c++;
            }
            $iRow++;
        }
    }
    $stmt = null;
    $rs = null;
    return $arrRet;
}

/*
Mapp of usual Data Types
2 = SHORT : smallint
3 = LONG : int
4 = FLOAT : float
5 = DOUBLE : double
7 = DATE : date
11 = TINY : tinyint
202 = VAR_STRING : varchar
203 = BLOB : mediumtext
*/

function get_fieldValue($f_Type, $f_Value)
{
    switch ($f_Type) {
        case 3:
        case 20:
            //Auto Number/Long integer, Big Integer
            if (intval($f_Value) > 0) {
                $f_Value = intval($f_Value);
            } else {
                $f_Value = 0;
            }
            break;
        case 2:
            //Small integer type;
            if (intval($f_Value) > 0) {
                $f_Value = intval($f_Value);
            } else {
                $f_Value = 0;
            }
            break;
        case 5:
        case 131:
            //Double number, DECIMAL[xx,xx];
            if (intval($f_Value) > 0) {
                if (strpos($f_Value, ",") > 0) {
                    $f_Value = str_replace(",", ".", $f_Value);
                }
            } else {
                $f_Value = 0;
            }
            break;
        case 6:
        case 4:
            //Currency type, Single/Float
            if (intval($f_Value) == 0) {
                $f_Value = 0;
            } else {
                if (strpos($f_Value, ",") > 0) {
                    $f_Value = str_replace(",", ".", $f_Value);
                }
            }
            break;
        case 7:
        case 133:
        case 135:
            //Date type
            if (!empty($f_Value)) {
                /**
                 * If not is a Date of the Default Format ('Y-m-d')
                 * Transform date (and time) to that format.
                 * Valid for all date formats eccept the amerikan ('m/d/Y')
                 */
                if (!sx_IsDate($f_Value)) {
                    $f_Value = sx_getUniversalDate($f_Value);
                }
            } else {
                $f_Value = null;
            }
            break;
        case 202:
        case 203:
            //Small Text, Long Text/Memo type
            if (!empty(trim($f_Value))) {
                $f_Value = apostroph($f_Value);
            } else {
                $f_Value = "";
            }
            break;
        case 11:
        case 16:
            //Yes/No OR BIT[1], TINYINT;
            if (boolval($f_Value) == true) {
                $f_Value = 1;
            } else {
                $f_Value = 0;
            }
            break;
        default:
            if (is_numeric($f_Value)) {
                if (strpos($f_Value, ",") > 0) {
                    $f_Value = str_replace(",", ".", $f_Value);
                }
            } else {
                $f_Value = $f_Value;
            }
    }
    return $f_Value;
}


function get_Access2MySQL_FieldTypes($f_Type, $radioPK, $iSize)
{
    if (empty($iSize)) {
        $iSize = 255;
    }
    $f_MySQL = null;
    switch ($f_Type) {
        case 3:
        case 20:
        case "int":
            //Auto Number/Long integer, Big Integer
            if ($radioPK) {
                //$f_MySQL = "int(11) NOT NULL AUTO_INCREMENT";
                $f_MySQL = "int NOT NULL AUTO_INCREMENT";
            } else {
                //$f_MySQL = "int(11) DEFAULT '0'";
                $f_MySQL = "int DEFAULT '0'";
            }
            break;
        case "bigint":
            //$f_MySQL = "bigint(11) DEFAULT '0'";
            $f_MySQL = "bigint DEFAULT '0'";
        case "mediumint":
            //$f_MySQL = "mediumint(4) DEFAULT '0'";
            $f_MySQL = "mediumint DEFAULT '0'";
        case 2:
        case "smallint":
            //Small integer type
            //$f_MySQL = "smallint(6) DEFAULT '0'";
            $f_MySQL = "smallint DEFAULT '0'";
            break;
        case 4:
        case "float":
            //Float type;
            $f_MySQL = "float DEFAULT '0'";
            break;
        case 5:
        case "double":
            //Double number $type[$xx,$xx];
            $f_MySQL = "double DEFAULT '0'";
            break;
        case 131:
        case "decimal":
            //Decimal type[$xx,$xxxx];
            $f_MySQL = "decimal(10,4) DEFAULT '0'";
            break;
        case 6:
        case "decimal":
            //Currency type;
            $f_MySQL = "decimal(10,4) DEFAULT '0'";
            break;
        case 7:
        case 133:
        case "date":
            //Date $type;
            $f_MySQL = "date DEFAULT NULL";
            break;
        case 135:
        case "datetime":
            //Date type;
            $f_MySQL = "datetime DEFAULT CURRENT_TIMESTAMP";
            break;
        case "time":
            //Date type;
            $f_MySQL = "time(6) DEFAULT NULL";
            break;
        case "timestamp":
            //Date type;
            $f_MySQL = "timestamp NULL DEFAULT NULL";
            break;
        case 202:
        case "varchar":
            //Text type;
            $f_MySQL = "varchar(" . $iSize . ") CHARACTER SET utf8mb4 DEFAULT NULL";
            break;
        case "char":
            //Text type;
            $f_MySQL = "char(" . $iSize . ") CHARACTER SET utf8mb4 DEFAULT NULL";
            break;
        case 203:
        case "mediumtext":
            //Memo type;
            $f_MySQL = "mediumtext CHARACTER SET utf8mb4";
            break;
        case "longtext":
            $f_MySQL = "longtext CHARACTER SET utf8mb4";
            break;
        case "text":
            $f_MySQL = "text CHARACTER SET utf8mb4";
            break;
        case "tinytext":
            $f_MySQL = "tinytext CHARACTER SET utf8mb4";
            break;
        case "blob":
            $f_MySQL = "blob";
            break;
        case "longblob":
            $f_MySQL = "longblob";
            break;
        case "mediumblob":
            $f_MySQL = "mediumblob";
            break;
        case "tinyblob":
            $f_MySQL = "tinyblob";
            break;
        case "bit":
            $f_MySQL = "bit(64) DEFAULT NULL";
        case 204:
        case "binary":
            $f_MySQL = "binary(10) DEFAULT NULL";
        case "varbinary":
            $f_MySQL = "varbinary(10) DEFAULT NULL";
        case 11:
        case 16:
        case "tinyint":
            //Yes/No, TINYINT type;
            $f_MySQL = "tinyint DEFAULT '0'";
            break;
        default:
            //All other $types;
            $f_MySQL = "varchar(" . $iSize . ") CHARACTER SET utf8mb4 DEFAULT NULL";
    }
    return  $f_MySQL;
}

/**
 * Get Index Keys of a Table from Access
 * Index Key -> PrimaryKey or the Name of the Column...
 * ... or Any Key Name, which will be replaced by Column name
 */
function get_TableIndexesAccess($tbl, $acn)
{
    $arrIndexes = array();
    $cat_index = new COM("ADOX.Catalog", NULL, CP_UTF8, NULL);
    if ($cat_index) {
        $cat_index->ActiveConnection = $acn;
        // Get all indexes of the requested table
        $index = $cat_index->Tables[$tbl]->Indexes;

        $count = $index->count();
        for ($i = 0; $i < $count; $i++) {
            // When an idext includes multiple columns
            // An array with the name of indexes and their respective columns
            foreach ($index[$i]->Columns as $col) {
                $arrIndexes[$col->Name] = $index[$i]->Name;
            }
        }
        $index = null;
        //unset($index);
    }
    $cat_index = null;
    return $arrIndexes;
}

/**
 * Get Index Keys of a Table from MySQL
 * Index Key -> PRI or MUL
 */
function get_TableIndexesMySQL($tbl, $cn)
{
    $arrIndexes = array();
    $sql   = "SHOW COLUMNS FROM $tbl";
    $fstmt = $cn->query($sql);
    while ($frs = $fstmt->fetch(PDO::FETCH_ASSOC)) {
        $name = $frs['Field'];
        $key = $frs['Key'];
        if (!empty($key)) {
            $arrIndexes[$name] = $key;
        }
    }
    $fstmt = null;
    $frs = null;
    return $arrIndexes;
}

function sx_existInMultyArray($array, $key, $val)
{
    foreach ($array as $item)
        if (isset($item[$key]) && $item[$key] == $val)
            return true;
    return false;
}
