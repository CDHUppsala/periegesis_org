<?php
header('Content-Type: text/html; charset=utf-8');

define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("PROJECT_PATH", realpath(dirname($_SERVER['DOCUMENT_ROOT'])));
define("PROJECT_PRIVATE", realpath(PROJECT_PATH . "/private"));
define("PROJECT_ADMIN", realpath(PROJECT_PATH . "/sx_Admin"));


/**
 * Obs! Important
 * Include Language and Administration Paths from the site configuration file
 */
require realpath(PROJECT_PATH . "/sx_SiteConfig/sx_languages.php");
require realpath(PROJECT_PATH . "/sx_SiteConfig/sx_adminPath.php");

/**
 * Admin design for every specific application
 */
include realpath(ROOT_PATH . "/dbAdmin/admin_design.php");

/**
 * All basic administration functions for the whole project
 */
include __DIR__ . "/sxLang/LangAdmin_" . sx_DefaultAdminLang . ".php";
include __DIR__ . "/sxLang/LangAdminShop_" . sx_DefaultAdminLang . ".php";

include __DIR__ . "/sxLang/Dates_" . sx_DefaultAdminLang . ".php";
include __DIR__ . "/sx_Functions/sx_FunctionsPHP.php";
include __DIR__ . "/sx_Functions/sx_FunctionsCleanText.php";
include __DIR__ . "/sx_Functions/sx_SanitizeStrings.php";


/**
 * Groups of Tables and the related Menu and Help information are saved by Language and Project
 * Administration language and Project Name are defined in the file sx_languages.php
 * Define a default Project Name to get default settings when creating a New Project
 * You can then save the New Project with a New Name and write it in file sx_languages.php.
 */

$str_ConfigProjectName = "Public Sphere";
if (!empty(sx_ConfigProjectName)) {
	$str_ConfigProjectName = sx_ConfigProjectName;
}

if (isset($_SESSION["SourceProjectName"]) && !empty($_SESSION["SourceProjectName"])) {
	$strSourceProjectName = $_SESSION["SourceProjectName"];
} else {
	$strSourceProjectName = $str_ConfigProjectName;
}

/**
 * Clean (unset) all sessions except login sessions and special sesion uncet manually:
 * - When a new table is requested
 * - When clean is explicitely requested (for other operations than table records)
 */

$boolNewTable = false;
$boolClearSessions = false;
if (isset($_GET["RequestTable"]) || isset($_POST["RequestTable"])) {
	$boolNewTable = true;
}
if (isset($_GET["clear"]) || isset($_POST["clear"])) {
	$boolClearSessions = true;
}

if ($boolNewTable || $boolClearSessions) {
	$arr_LoginSessionsToKeep = array("LoginAdminID", "LoginAdminLevel", "LoginUserIP", "UserFirstName", "UserAgent", "LastLogin", "SourceProjectName", "TargetProjectName");
	$arr_SpecialSessionsToKeep = array("CheckedOrderPKValues");
	foreach ($_SESSION as $key => $value) {
		if (
			!in_array($key, $arr_LoginSessionsToKeep) &&
			!in_array($key, $arr_SpecialSessionsToKeep) &&
			strpos($key, "/sxAdmin/") == 0
		) {
			unset($_SESSION[$key]);
		}
	}
}

