<?php
function get_dbConn_Access($dbpath)
{
    $conn_access = new COM('ADODB.Connection', NULL, CP_UTF8, NULL);
    $strConn = "Persist Security Info=False;Provider=Microsoft.ACE.OLEDB.12.0;Jet OLEDB:Database Password=;Data Source=" . $dbpath . ";";
    //$strConn = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . $dbpath . ";Uid=; Pwd=;charset=utf8mb4";
    $conn_access->Open($strConn);
    return $conn_access;
}

function get_dbConn_MySQL($dbname)
{
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
    $str_Server = "localhost";
    $str_Database = $dbname;
    $str_UID = "root";
    $str_PW = "adminsx";
    return new PDO("mysql:host=" . $str_Server . ";charset=utf8mb4;dbname=" . $str_Database, $str_UID, $str_PW, $options);
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
    $cat = null;
    $cat = null;
    $cat = new COM("ADOX.Catalog", NULL, CP_UTF8, NULL);
    if ($cat) {
        $cat->ActiveConnection = $com;
        $tables = $cat->Tables;
        $count = $tables->Count();
        for ($i = 0; $i < $count; $i++) {
            $table = $tables->Item($i);
            $tableName = strtolower($table->Name);
            if ((strtolower($table->Type) == "table"
                    || strtolower($table->Type) == "view")
                && substr($tableName, 0, 1) != "~"
            ) {
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

function sx_getPrimaryKey_Access($table, $cn)
{
    $sPK = null;
    $rec = new COM("ADODB.Recordset", null, CP_UTF8, null);
    $sql = "SELECT TOP 1 * FROM $table";
    $rec->Open($sql, $cn, 3, 3);
    if ($rec) {
        $max_col = $rec->Fields->Count;
        for ($i = 0; $i < $max_col; $i++) {
            if ($rec->Fields[$i]->Properties["IsAutoincrement"]->Value) {
                $sPK = $rec->Fields[$i]->Name;
                break;
            }
        }
    }
    $rec->close;
    return $sPK;
}

function sx_getPrimaryKey_MySQL($tbl, $cn)
{
    $sPK = null;
    $stmt = $cn->query("SHOW COLUMNS FROM $tbl");
    $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rec as $column) {
        if ($column['Key'] == "PRI") {
            $sPK = $column['Field'];
            break;
        }
    }
    $rec = null;
    return $sPK;
}

function sx_getFieldAttributes_Access($table, $cn)
{
    $arr = null;
    $rec = new COM("ADODB.Recordset", null, CP_UTF8, null);
    $sql = "SELECT TOP 1 * FROM $table";
    $rec->Open($sql, $cn, 3, 3);
    if ($rec) {
        $arr = array();
        $max_col = $rec->Fields->Count;
        for ($i = 0; $i < $max_col; $i++) {
            $arr[$i][0] = $rec->Fields[$i]->Name;
            $arr[$i][1] = $rec->Fields[$i]->Type;
            if ($rec->Fields[$i]->Properties["IsAutoincrement"]->Value) {
                $arr[$i][2] = "PRI-AUTO";
            } else {
                $arr[$i][2] = "";
            }
        }
    }
    $rec->close;
    return $arr;
}

function sx_getFieldAttributes_MySQL($tbl, $cn)
{
    $arr = array();
    $stmt = $cn->query("SHOW COLUMNS FROM $tbl");
    $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rec as $column) {
        $arr[] = array($column['Field'], $column['Type'], $column['Key']);
    }
    $rec = null;
    return $arr;
}
