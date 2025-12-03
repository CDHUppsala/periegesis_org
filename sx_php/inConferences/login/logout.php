<?php
$strLastName = $_SESSION["Part_FirstName"];

// unset data in $_SESSION
$_SESSION[] = array();

// destroy the session
session_destroy();

header("Location: " . sx_PATH . "?pg=message&logout=yes&name=" . $strLastName);
exit();
