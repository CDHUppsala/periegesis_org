<?php

$strConnectToSourceDB = trim(@$_POST["ConnectToSourceDB"]);
$strConnectToTargetDB = trim(@$_POST["ConnectToTargetDB"]);
$strClearSourceDB = trim(@$_POST["ClearSourceDB"]);
$strClearTargetDB = trim(@$_POST["ClearTargetDB"]);
/*
$strClearSourceDB = "Yes";
$strClearTargetDB = "Yes";
*/
if (!empty($strConnectToSourceDB)) {
    $_SESSION["ConnectToSourceDB"] = $strConnectToSourceDB;
} elseif (!empty(@$_SESSION["ConnectToSourceDB"])) {
    $strConnectToSourceDB = $_SESSION["ConnectToSourceDB"];
}
if ($strClearSourceDB == "Yes") {
    unset($_SESSION["ConnectToSourceDB"]);
    $strConnectToSourceDB = null;
}

if (!empty($strConnectToTargetDB)) {
    $_SESSION["ConnectToTargetDB"] = $strConnectToTargetDB;
} elseif (strlen(@$_SESSION["ConnectToTargetDB"]) > 0) {
    $strConnectToTargetDB = $_SESSION["ConnectToTargetDB"];
}
if ($strClearTargetDB == "Yes") {
    unset($_SESSION["ConnectToTargetDB"]);
    $strConnectToTargetDB = null;
}

/**
 * Connection to Source database:
 * - To the default administration connection, wich is a Local or Remote MySQL, or
 * - To a LOCAL alternative MySQL or to a LOCAL or Remote MS Access Database
 * - Remote Access Database must be in the folder /private/
 */
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];

$boolSourceMySQL = true;
$sSourceMySQLPath = null;
$sSourceAccessPath = null;
$strSourceDBName = sx_TABLE_SCHEMA;
if (!empty($strConnectToSourceDB)) {
    if (strpos($strConnectToSourceDB, ".mdb") > 0 || strpos($strConnectToSourceDB, ".accdb") > 0) {
        $boolSourceMySQL = false;
        if (strpos($strConnectToSourceDB, "\\") > 0 || strpos($strConnectToSourceDB, "/") > 0) {
            $sxPath = $strConnectToSourceDB;
            $strSourceDBName = basename($sxPath);
        } else {
            $sxPath = $_SERVER['DOCUMENT_ROOT'] . "/../private/" . $strConnectToSourceDB;
            $strSourceDBName = $strConnectToSourceDB;
        }
        $sSourceAccessPath = "Persist Security Info=False;Provider=Microsoft.ACE.OLEDB.12.0;Jet OLEDB:Database Password=;Data Source=" . $sxPath . ";";
        //$sSourceAccessPath = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . $sxPath . ";Uid=; Pwd=;charset=utf8mb4";

        $connSourceAccess = new COM('ADODB.Connection', null, CP_UTF8, null);
        $connSourceAccess->Open($sSourceAccessPath);
    } else {


        if (!empty($_POST["ClearSourceDB"]) || isset($_GET["clear"])) {
            unset($_SESSION["SourceServer"]);
            unset($_SESSION["SourceUID"]);
            unset($_SESSION["SourcePW"]);
        }
        $strSourceServer = null;
        if (!empty($_POST["SourceServer"])) {
            $strSourceServer = trim($_POST["SourceServer"]);
            $strSourceUID = trim($_POST["SourceUID"]);
            $strSourcePW = trim($_POST["SourcePW"]);
            $_SESSION["SourceServer"] = $strSourceServer;
            $_SESSION["SourceUID"] = $strSourceUID;
            $_SESSION["SourcePW"] = $strSourcePW;
        } elseif (isset($_SESSION["SourceServer"])) {
            $strSourceServer = $_SESSION["SourceServer"];
            $strSourceUID = $_SESSION["SourceUID"];
            $strSourcePW = $_SESSION["SourcePW"];
        }
        if (!empty($strSourceServer) && !empty($strSourceUID) && !empty($strSourcePW)) {
            $str_Server = $strSourceServer;
            $str_UID = $strSourceUID;
            $str_PW = $strSourcePW;

            //$connSourceMySQL = new PDO("mysql:host=$str_Server;charset=utf8mb4;dbname=" . $strConnectToSourceDB, $str_UID, $str_PW, $options);
            $connSourceMySQL = new PDO("mysql:host=$str_Server;charset=utf8mb4;dbname=" . $strConnectToSourceDB, $str_UID, $str_PW, $options);
        } else {
            $strSourceDBName = $strConnectToSourceDB;
            $str_Server = "localhost";
            $str_UID = "root";
            $str_PW = "adminsx";
            $connSourceMySQL = new PDO("mysql:host=" . $str_Server . ";charset=utf8mb4;dbname=" . $strConnectToSourceDB, $str_UID, $str_PW, $options);
        }
    }
} else {
    // The default administration connection to database
    $connSourceMySQL = dbconn();
}

/**
 * Connect to Target Database
 * A Local or Remote MySQL, or
 * A Lacal or Remote MS Access Database
 */
$connTargetAccess = null;
//$connTargetMySQL = null;

$boolTargetMySQL = true;
$strTargetDBName = null;
$sTargetAccessPath = null;
$sTargetMySQLPath = null;
if (!empty($strConnectToTargetDB)) {

    $strTargetDBName = $strConnectToTargetDB;
    if (strpos($strConnectToTargetDB, ".mdb") > 0 || strpos($strConnectToTargetDB, ".accdb") > 0) {
        $boolTargetMySQL = false;
        if (strpos($strConnectToTargetDB, "\\") > 0 || strpos($strConnectToTargetDB, "/") > 0) {
            $sxPath = $strConnectToTargetDB;
            $strTargetDBName = basename($sxPath);
        } else {
            $sxPath = $_SERVER['DOCUMENT_ROOT'] . "/../private/" . $strConnectToTargetDB;
        }
        $sTargetAccessPath = "Persist Security Info=False;Provider=Microsoft.ACE.OLEDB.12.0;Jet OLEDB:Database Password=;Data Source=" . $sxPath . ";";
        // The next driver does nor show greek text for column description
        //$sTargetAccessPath = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . $sxPath . ";Uid=; Pwd=;charset=utf8mb4";
        $connTargetAccess = new COM('ADODB.Connection', null, CP_UTF8, null);
        $connTargetAccess->Open($sTargetAccessPath);
    } else {
        if (!empty($_POST["ClearTargetDB"]) || isset($_GET["clear"])) {
            unset($_SESSION["TargetServer"]);
            unset($_SESSION["TargetUID"]);
            unset($_SESSION["TargetPW"]);
        }
        $strTargetServer = null;
        if (!empty($_POST["TargetServer"])) {
            $strTargetServer = trim($_POST["TargetServer"]);
            $strTargetUID = trim(@$_POST["TargetUID"]);
            $strTargetPW = trim(@$_POST["TargetPW"]);
            $_SESSION["TargetServer"] = $strTargetServer;
            $_SESSION["TargetUID"] = $strTargetUID;
            $_SESSION["TargetPW"] = $strTargetPW;
        } elseif (isset($_SESSION["TargetServer"])) {
            $strTargetServer = $_SESSION["TargetServer"];
            $strTargetUID = $_SESSION["TargetUID"];
            $strTargetPW = $_SESSION["TargetPW"];
        }

        if (!empty($strTargetServer) && !empty($strTargetUID) && !empty($strTargetPW)) {
            $str_Server = $strTargetServer;
            $str_UID = $strTargetUID;
            $str_PW = $strTargetPW;
            $connTargetMySQL = new PDO("mysql:host=$str_Server;charset=utf8mb4;dbname=" . $strConnectToTargetDB, $str_UID, $str_PW, $options);
        } else {
            $str_Server = "localhost";
            $str_UID = "root";
            $str_PW = "adminsx";
            $connTargetMySQL = new PDO("mysql:host=$str_Server;charset=utf8mb4;dbname=" . $strConnectToTargetDB, $str_UID, $str_PW, $options);
        }
    }
}

$boolTargetConnected = false;
if (!empty($connTargetMySQL) || !empty($connTargetAccess)) {
    $boolTargetConnected = true;
}
/*
define("bool_SourceMySQL", $boolSourceMySQL);
define("conn_SourceMySQL", $connSourceMySQL);
define("conn_SourceAccess", $connSourceAccess);

define("bool_TargetMySQL", $boolTargetMySQL);
define("conn_TargetMySQL", $connTargetMySQL);
define("conn_TargetAccess", $connTargetAccess);
*/
