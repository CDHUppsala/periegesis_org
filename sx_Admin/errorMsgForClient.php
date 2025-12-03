<?php

/**
 * Error messages for the Client's Version of the program
 * The message her comes always from a querystring
 */
$strMsg = "";
$textMsg = "";
if (!empty($_GET["strMsg"])) {
	$strMsg = $_GET["strMsg"];
} elseif (!empty($_GET["msg"])) {
	$textMsg = $_GET["msg"];
}

$strTextMsg = "";
if (!empty($strMsg)) {
	$strTextInfoMsg = "INFORMATION MESSAGE";
	$strLine = '\n------------------------------------------\n';
	if ($strMsg == "userLevel") {
		$strTextMsg = "Sorry! You have no access to this function!";
	} elseif ($strMsg = "noPK") {
		$strTextMsg = "Sorry! The first field of the table must be a Primary Key, an autoincremented, long integer!";
	} elseif ($strMsg = "err") {
		$strTextMsg = "Error in adding/updating records!\n\nPlease check the last record added or updated.";
	} else {
		$strTextMsg = "An unspecified error of type " . $strMsg . " occured. Please consider the following possible reason: 
			\n\n1. You have change the Table fields in the main database without
			\nadjustments in the configuration table.
			\n\nOpen the current Table from the Visible Fields in the group Configuration and save the 
			\nTable\'s Field Names to remove old once from the configuration database. 
			\n\n2. Else, there must be an error in configuration of table relations.
			\nPlease contact the administrator.";
	}
	$strTextMsg = $strTextInfoMsg . $strLine . $strTextMsg . $strLine;
} elseif (!empty($textMsg)) {
	$strTextMsg = $textMsg;
}

if (!empty($strTextMsg)) { ?>
	<script>
		alert("<?= $strTextMsg ?>");
	</script>
<?php
} ?>