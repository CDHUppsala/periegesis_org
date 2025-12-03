<?php
$strFirstName = $_SESSION["Users_FirstName"];

$_SESSION[] = array();  // unset data in $_SESSION
session_destroy();             // destroy the session
/**
 * If you don't like to distroy all sesssions
 */
/*
unset($_SESSION["Users_" . $_SESSION["User_Token"]]);
unset($_SESSION["User_Token"]);
unset($_SESSION["Users_UserID"]);
unset($_SESSION["Users_FirstName"]);
unset($_SESSION["Users_LastName"]);
unset($_SESSION["Users_UserEmail"]);
*/
header("Location: " . sx_PATH . "?pg=message&logout=yes&name=" . $strFirstName);
exit();
