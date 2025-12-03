<?php
$strFirstName = $_SESSION["LoginFirstName"];
/*
unset($_SESSION["Students_" . sx_DefaultSiteLang]);
unset($_SESSION["LoginMemberUserID"]);
unset($_SESSION["LoginFirstName"]);
unset($_SESSION["LoginLastName"]);
unset($_SESSION["LoginUserEmail"]);
*/
// unset data in $_SESSION
$_SESSION[] = array();

// destroy the session
session_destroy();

header("Location: ". sx_PATH."?pg=message&logout=yes&name=".$strFirstName);
exit();
?>
