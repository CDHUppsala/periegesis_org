<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');
/**
 * Using environment variables with fallback to hardcoded constants
 */
define('sx_ServerName', getenv('DB_HOST') ?: "00.000.00.0");
define('sx_UserName', getenv('DB_USER') ?: "ps_uu_DigitalPeriegesis");
define('sx_Password', getenv('DB_PASS') ?: "ps_uu_V453-O821-D974");
define('sx_TABLE_SCHEMA', getenv('DB_NAME') ?: 'ps_uu_periegesis');
define('sx_Charset', getenv('DB_CHARSET') ?: "utf8mb4");

/**
 * @return PDO
 */
function dbconn(): PDO
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    static $conn;
    if ($conn === null) {
        $dsn = "mysql:host=" . sx_ServerName . ";dbname=" . sx_TABLE_SCHEMA . ";charset=" . sx_Charset;
        try {
            $conn = new PDO($dsn, sx_UserName, sx_Password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    return $conn;
}

$conn = dbConn();
