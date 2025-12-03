<?php

/**
 * If one day has passed since last login, remove sessions and log in again
 * @return bool
 */
function last_login_is_recent()
{
	$recent_limit = 60 * 60 * 24 * 1; // 1 day
	if (!isset($_SESSION['LastLogin'])) {
		return false;
	}
	return (($_SESSION['LastLogin'] + $recent_limit) >= time());
}

/**
 * Controlls login session from inner pages
 */
$sxHTTP_HOST = $_SERVER["HTTP_HOST"];
$sxSuffix = $sxHTTP_HOST . "/sxAdmin/";

$radio_UnsetSessions = false;
if (!isset($_SESSION[$sxSuffix]) || $_SESSION[$sxSuffix] == False) {
	$radio_UnsetSessions = true;
}

if (!isset($_SESSION["UserAgent"]) || $_SESSION["UserAgent"] !== $_SERVER['HTTP_USER_AGENT']) {
	$radio_UnsetSessions = true;
}

if(last_login_is_recent() === false) {
	$radio_UnsetSessions = true;
}

if ($radio_UnsetSessions) {
	$_SESSION[] = array();
	session_unset();
	session_destroy();
	header("Location: " . sx_ROOT_HOST . "/dbAdmin/login/accessDenied.php");
	exit();
}

/**
 * Identify the ID and the User Level of the administrator
 */

$intLoginAdminID = $_SESSION["LoginAdminID"];
if (intval($intLoginAdminID) == 0) {
	$intLoginAdminID = 0;
}
$intLoginUserLevel = $_SESSION["LoginAdminLevel"];
if (intval($intLoginUserLevel) == 0) {
	$intLoginUserLevel = 2;
}
