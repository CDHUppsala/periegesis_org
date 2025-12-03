<?php
/*
 * ===================================================================================
 * 	This file is not used by default!
 * 	If you define different levels of administration rights (1 and 2) in the login table,
 * 	include this file in the functionsTableName.php to exclude access for the level 2 
 * 	to the specified TABLES, e.g. the AdminLogn table.
 * 	It has no effects if you don't define administration right levels.
 * ===================================================================================
 
 * == Add to the if-statement all tables that will not be accessiable by level 2 adminstrators
 * == In this example, level 2 cannot change the table for addministrators of the site
*/

if (isset($arr_LevelRestrictionTables) && isset($intLoginUserLevel) && intval($intLoginUserLevel) > 1) {
    $accessPermision = True;
    if (in_array($request_Table,$arr_LevelRestrictionTables)) {
        $accessPermision = false;
    }
 
    if ($accessPermision == false) {
        // == To avoid endless loop
        unset($_SESSION["Table"]);
        unset($_SESSION["TableView"]);
        header("Location: main.php?strMsg=userLevel");
        exit();
    }
}