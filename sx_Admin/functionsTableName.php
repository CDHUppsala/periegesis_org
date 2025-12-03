<?php
/**
 * Gets the actual/requested Table Name 
 */

if (isset($_GET["RequestTable"])) {
	$request_Table = strtolower(trim($_GET["RequestTable"]));
	if (strpos($request_Table,"%20") > 0) {
		header("Location: main.php");
		exit;
	}
	if ($request_Table == "sys") {
		header("Location: main.php");
		exit;
	}
	if(sx_checkTableAndFieldNames($request_Table) == false) {
		header("Location: main.php");
		exit;
	}
 
	$_SESSION["Table"] = $request_Table;

} elseif (!empty($_SESSION["Table"])) {
	$request_Table = $_SESSION["Table"];
} else {
	$request_Table = "";
}
define("REQUEST_Table", $request_Table);


/**
 * Do Not open Tinymce when editing configuration tables
 */

$radio_useTinymce = true;
if($request_Table == "sx_config_groups" || $request_Table == "sx_config_tables" || $request_Table == "sx_help_by_group" || $request_Table == "sx_help_by_table") {
	$radio_useTinymce = false;
}


/**
 * RESTRICTIONS FOR ADMINISTRATION LEVELS
 */
/**
 * Define the name of all tables that will NOT be accessible  by Administrator Level > 1
 */

$arr_LevelRestrictionTables = array("admin_login", "admin_logs", "languages", 
    "site_setup", "site_config_basic", "site_config_texts", "site_config_apps", "text_groups", 
	"text_categories", "text_subcategories", "themes", 
	"about_groups", "about_categories", "about",
	"newsletters", "pdf_setup", "media_setup", "book_setup", "Report_setup", "faq_setup",
	"conf_setup", "conf_participants", "conf_to_participants", "conf_administrators", "conf_participants_setup");

/**
 * Define all tables that includ the field "LoginAdminID", witch contains the Login ID of the current administrator
 */
$arrTablesWithLoginAdminID = array("text", "text_news", "texts_blog", "about", 
	"conferences", "conf_sessions", "conf_papers");
 
/**
 * Check if current table includes the field "LoginAdminID"
 */
$radio_TablesWithLoginAdminID = False;
if (in_array($request_Table,$arrTablesWithLoginAdminID)) { 
	$radio_TablesWithLoginAdminID = True;
}

/**
 * GENERAL RESTRICTIONS FOR SELECTED TABLES
 * Define Copying, Editing, Deleting and Adding restriction for different tables
 */
$arr_NotCopyableTables = array("orders", "ordered_items", "visits_products", "visits_texts", "visits", "visits_events", "conf_rights");
$arr_NotEditableTables = array("visits_products", "visits_texts", "visits", "visits_events", "conf_contributors", "conf_rights");
$arr_NotDeleteableTables = array("orders", "ordered_items", "products", "product_groups", "product_categories", "visits_products", "visits_texts", "visits",
	"texts", "text_news", "texts_blog", "text_groups", "text_categories", "text_subcategories",
	"conferences", "conf_sessions");
$arr_NotAddableTables = array("orders", "ordered_items", "visits_products", "visits_texts", "visits");
$arr_NewsLetterTables = array("products", "news", "articles", "texts", "text_news", "texts_blog", "events", "conferences");

$arr_ConferenceTables = array("conferences", "conf_sessions", "conf_papers");

/**
 * Activate next include 
 * if you define different levels of administration rights (1 and 2) in the login table
 */
include "login/adminLevelTables.php";

