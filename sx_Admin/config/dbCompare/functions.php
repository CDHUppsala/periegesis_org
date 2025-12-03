<?php

function get_dbConn_MySQL($dbname,$host="localhost",$host_un="root",$host_pw="adminsx")
{
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
    $str_Server = $host;
    $str_Database = $dbname;
    $str_UID = $host_un;
    $str_PW = $host_pw;
    return new PDO("mysql:host=" . $str_Server . ";charset=utf8mb4;dbname=" . $str_Database, $str_UID, $str_PW, $options);
}

function get_dbConn_Access($dbpath)
{
    $acc_com = new COM('ADODB.Connection', NULL, CP_UTF8, NULL);
    //$strConn = "Persist Security Info=False;Provider=Microsoft.ACE.OLEDB.12.0;Jet OLEDB:Database Password=;Data Source=" . $dbpath . ";";
    // The next driver does nor show greek text for column description
    $strConn = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . $dbpath . ";Uid=; Pwd=;charset=utf8mb4";
    $acc_com->Open($strConn);
    return $acc_com;
}

function sx_getMySQLTables($cn)
{
    $LoopTable = array();
    $stmt = $cn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    foreach ($rs as $table) {
        $LoopTable[] = $table[0];
    }
    $stmt = null;
    $rs = null;
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
        //unset($table);
        $tables = null;
        //unset($tables);
    }
    $cat = null;
    //unset($cat);
    return $arrTables;
}

function sx_getAccessTableIndexes($table, $com)
{
    $arrIndexes = array();
    $com_cat = new COM("ADOX.Catalog", NULL, CP_UTF8, NULL);
    if ($com_cat) {
        $com_cat->ActiveConnection = $com;
        // Get all indexes of the requested table
        $index = $com_cat->Tables->Item($table)->Indexes;
        $count = $index->count();
        for ($i = 0; $i < $count; $i++) {
            // When an idext includes multiple columns
            $aTemp = array();
            foreach ($index[$i]->Columns as $col) {
                $aTemp[] = $col;
            }
            if (!empty($aTemp)) {
                $sx = trim($aTemp[0]);
                $arrIndexes[$sx] = $index[$i]->Name;
            } else {
                // An array with the name of indexes and their respective columns
                $arrIndexes[] = $index[$i]->Name;
            }
        }
        $table = null;
        //unset($table);
    }
    $com_cat = null;
    //unset($com_cat);
    $com = null;
    return $arrIndexes;
}

function sx_getAccess_Fields($table, $com)
{
    $aRet = array();
    $strSQL = "SELECT TOP 1 * FROM $table";
    $rs = $com->execute($strSQL);
    if (!$rs->EOF) {
        $i_count = $rs->Fields->Count();
        for ($f = 0; $f < $i_count; $f++) {
            $field = $rs->Fields[$f];
            $aRet[] = array($field->Name, $field->Type);
        }
    }
    $rs->Close();
    $rs = null;
    $com = null;
    return $aRet;
}


function sx_getAccess_FieldNames($Table, $com)
{
    $aRet = array();
    $strSQL = "SELECT TOP 1 * FROM $Table";
    $rs = $com->execute($strSQL);
    if (!$rs->EOF) {
        $iCount = $rs->Fields->Count();
        for ($i = 0; $i < $iCount; $i++) {
            $aRet[] = $rs->Fields($i)->name;
        }
    }
    $rs->Close();
    $rs = null;
    return $aRet;
}

// Get field names and types even for empty tables
function sx_getAccessFields($table, $com)
{
    $rs = new COM("ADODB.Recordset", null, CP_UTF8, null);
    $sql = "SELECT TOP 1 * FROM $table";
    $rs->Open($sql, $com, 3, 3);
    $aRet = array();
    $num = $rs->Fields->Count;
    for ($i = 0; $i < $num; $i++) {
        $aRet[] = array($rs->Fields[$i]->Name, $rs->Fields[$i]->Type);
    }
    $rs->Close();
    $rs = null;
    return $aRet;
}
// Get field names and types even for empty tables
function sx_getAccessFieldNames($table, $com)
{
    $rs = new COM("ADODB.Recordset", null, CP_UTF8, null);
    $sql = "SELECT TOP 1 * FROM $table";
    $rs->Open($sql, $com, 3, 3);
    $aRet = array();
    $num = $rs->Fields->Count;
    for ($i = 0; $i < $num; $i++) {
        $aRet[] = $rs->Fields($i)->Name;
    }
    $rs->Close();
    $rs = null;
    return $aRet;
}

function sx_getMySQLFieldNames($Table, $cn)
{
    $aRet = array();
    $stmt = $cn->query("SHOW COLUMNS FROM $Table");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $column) {
        $aRet[] = $column['Field'];
    }
    $stmt = null;
    $result = null;
    return $aRet;
}


function sx_tablesFields_MySQL($tbl, $con, $aFields)
{
    echo "<table><tr><th>Field</th><th>Type</th><th>Key</th><th>Extra</th></tr>";
    $stmt = $con->query("SHOW COLUMNS FROM $tbl");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $column) {
        $class = "";
        if (is_array($aFields) && !empty($aFields)) {
            if (!in_array($column['Field'], $aFields)) {
                $class = ' class="color"';
            }
        }
        echo "<tr><td" . $class . ">" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td><td>" . $column['Extra'] . "</td></tr>";
    }
    echo "</table>";
}

/**
 * COLUMN_KEY =: PRI,MUL
 * EXTRA => auto_increment
 * CHARACTER_SET_NAME => utf8mb4
 */
function sx_tablesFields_MySQL_Schema($shema, $table, $cn, $aFields, $com = null)
{
    $arrComFields = array();
    if (!empty($com)) {
        $rs = new COM("ADODB.Recordset", null, CP_UTF8, null);
        $sql = "SELECT TOP 1 * FROM $table";
        $rs->Open($sql, $com, 3, 3);
        $num = $rs->Fields->Count;
        for ($i = 0; $i < $num; $i++) {
            $arrComFields[$rs->Fields[$i]->Name] = $rs->Fields[$i]->Type;
        }
        $rs->Close();
        $rs = null;
    }

    $stmt = $cn->query("SELECT * FROM $table LIMIT 1");
    $colcount = $stmt->columnCount();
    $arrFields = array();
    for ($c = 0; $c < $colcount; $c++) {
        $meta = $stmt->getColumnMeta($c);
        $arrFields[$c] = $meta['native_type'];
    }
    $stmt = null;

    if (empty($shema)) {
        $shema = sx_TABLE_SCHEMA;
    }

    echo "\n<table id='types'><tr><th>Field</th><th>COM</th><th>Type</th><th>Data Type</th><th>Col Type</th><th>Len</th><th>Def</th><th>Key</th><th>Extra</th><th>Char</th><th>Null</th></tr>";

    $sqlmy = "SELECT
        COLUMN_NAME,
        DATA_TYPE,
        COLUMN_TYPE,
        CHARACTER_MAXIMUM_LENGTH,
        COLUMN_DEFAULT,
        COLUMN_KEY,
        EXTRA,
        CHARACTER_SET_NAME,
        IS_NULLABLE
		FROM Information_schema.columns 
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
        ORDER BY ORDINAL_POSITION";
    $fstmt = $cn->prepare($sqlmy);
    $fstmt->execute([$shema, $table]);
    $frs = $fstmt->fetchAll();
    if ($frs) {
        $iLopp = 0;
        foreach ($frs as $column) {
            $loopName = $column['COLUMN_NAME'];
            $class = "";
            if (is_array($aFields) && !empty($aFields)) {
                if (!in_array($loopName, $aFields)) {
                    $class = ' class="color"';
                }
            }
            $iComType = "";
            if(!empty($arrComFields) && array_key_exists($loopName,$arrComFields)) {
                $iComType = $arrComFields[$loopName] ." ";
            }
            echo "<tr><td" . $class . ">" . $loopName . "</td><td class='text_small'>" . $iComType . "</td><td class='text_small'>" . $arrFields[$iLopp] . "</td><td>" . $column['DATA_TYPE'] . "</td><td>" . $column['COLUMN_TYPE'] . "</td><td>" . $column['CHARACTER_MAXIMUM_LENGTH'] . "</td><td>" . $column['COLUMN_DEFAULT'] . "</td><td>" . $column['COLUMN_KEY'] . "</td><td>" . substr($column['EXTRA'], 0, 4) . "</td><td>" . $column['CHARACTER_SET_NAME'] . "</td><td>" . $column['IS_NULLABLE'] . "</td></tr>";
            $iLopp++;
        }
    }
    $frs = null;
    $fstmt = null;
    $arrFields = null;
    $arrComFields = null;
    echo "</table>\n";
}

function sx_tablesFields_access($table, $com, $aFields)
{
    $arrIndex = sx_getAccessTableIndexes($table, $com);
    echo "<table><tr><th>Field</th><th>Type</th><th>Key</th><th>Extra</th></tr>";
    $rs = new COM("ADODB.Recordset", null, CP_UTF8, null);
    $sql = "SELECT TOP 1 * FROM $table";
    $rs->Open($sql, $com, 3, 3);
    $num = $rs->Fields->Count;
    for ($i = 0; $i < $num; $i++) {
        $field = $rs->Fields[$i];
        $sPro = "";
        if ($field->Properties['ISAUTOINCREMENT']->Value) {
            $sPro = "Auto";
        }
        $class = "";
        $fName = $field->Name;
        if (is_array($aFields) && !empty($aFields)) {
            $class = "";
            if (!in_array($fName, $aFields)) {
                $class = ' class="color"';
            }
        }
        $index = "";
        if (is_array($arrIndex) && (array_key_exists($fName, $arrIndex) || in_array($fName, $arrIndex))) {
            $index = "MUL";
            if (!empty($sPro)) {
                $index = "PRI";
            }
        }

        echo "<tr><td" . $class . ">" . $fName . "</td><td>" . $field->Type . "</td><td>$index</td><td>$sPro</td></tr>";
    }
    echo "</table>";
    $rs->Close();
    $rs = null;
}
