<?php
define("DOC_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("PROJECT_PATH", realpath(dirname($_SERVER['DOCUMENT_ROOT'])));

define("PROJECT_PHP", realpath(PROJECT_PATH . "/sx_php"));
define("PROJECT_PHP_SHOP", realpath(PROJECT_PATH . "/sx_php_shop"));
define("PROJECT_PRIVATE", realpath(PROJECT_PATH . "/private"));
define("PROJECT_CONFIG", realpath(PROJECT_PATH . "/sx_SiteConfig"));

// Include basic PHP functions
require realpath(PROJECT_PATH . "/sx_Conn/connMySQL.php");
require realpath(PROJECT_PATH . "/sx_Functions/basic_php.php");
require realpath(PROJECT_PATH . "/sx_Functions/forms_emails.php");
require realpath(PROJECT_PATH . "/sx_Functions/apps.php");
require realpath(PROJECT_PATH . "/sx_Functions/sxGaze.php");
require realpath(PROJECT_PATH . "/sx_Functions/sxCleanText.php");

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

define("sx_HOST", $_SERVER["HTTP_HOST"]);
define("sx_ROOT_HOST", $protocol . sx_HOST);

define("sx_PATH", $_SERVER["SCRIPT_NAME"]);
define("sx_HOST_PATH", sx_HOST . sx_PATH);

define("sx_ROOT_HOST_PATH", sx_ROOT_HOST . sx_PATH);
define("sx_LANGUAGE_PATH", sx_ROOT_HOST . "/" . sx_CurrentLanguage . "/");

define("sx_QUERY", $_SERVER["QUERY_STRING"]);

/**
 * Clean messages (for javascripts) from current location
 */
$strREQUEST = $_SERVER["REQUEST_URI"];
if (isset($_GET["msg"])) {
    $strREQUEST = remove_Right_Query_From_Key("msg", $strREQUEST);
}
if (isset($_GET["strMsg"])) {
    $strREQUEST = remove_Right_Query_From_Key("strMsg", $strREQUEST);
}
if (isset($_GET["page"])) {
    $strREQUEST = remove_Right_Query_From_Key("page", $strREQUEST);
}
define("sx_LOCATION", sx_ROOT_HOST . $strREQUEST);

/*
    To prohibit connections from false sites
*/
if (!empty(sx_TrueSiteURL) && sx_radioCheckTrueSiteURL) {
    if (sx_ROOT_HOST != sx_TrueSiteURL) {
        header("Location: " . sx_TrueSiteURL);
    }
}

$sx_UserIP = return_User_IP();
$sx_radioValidIP = filter_var($sx_UserIP, FILTER_VALIDATE_IP);

define("sx_UserIP", $sx_UserIP);
define("sx_radioValidIP", $sx_radioValidIP);

/**
 * Check if the site is in Update Mode
 * and redirect the visitor to the update page.
 */
$sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
$stmt = $conn->prepare($sql);
$stmt->execute(['SiteMode']);
$strCachedData = $stmt->fetchColumn();
if($strCachedData === 'Update') {
    header('Location: update.php');
    exit();
}

CONST sx_ROOT_DEV = '..';
