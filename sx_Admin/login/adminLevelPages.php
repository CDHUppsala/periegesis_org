<?php
/**
* RESTRICTED ACCESS TO VARIOUS PAGES
* ===================================================================================
* 	This file is not used by default!
* 	If you define different levels of administration rights (1 and 2) in the login table,
* 	include this file in every PAGE that you wish to exclude from use by level 2,
* 	for example for the delete page (delete.php) or the upload page (sxUpload/sxUpload.php).
* 	It has no effects if you don't define administration right levels.
* ===================================================================================
* The code bellow presuposes that the admin program is in the subfolder "dbAdmin"
*/

if ($_SESSION["LoginAdminLevel"] > 1) {
	/**
	 * To avoid endless loop
	 */
	unset($_SESSION["Table"]);
	unset($_SESSION["TableView"]);

	$arrAccessPath = explode("/",$_SERVER["ORIG_PATH_INFO"]);
	$iAccessCount = count($arrAccessPath);

	if (intval($iAccessCount) == 5) {
		header("Location: ../../main.php?strMsg=userLevel&ps=1");
		exit();
	} elseif (intval($iAccessCount) == 4) {
		header("Location: ../main.php?strMsg=userLevel&ps=2");
		exit();
	} else {
		header("Location: main.php?strMsg=userLevel&ps=3");
		exit();
	}
}
?>