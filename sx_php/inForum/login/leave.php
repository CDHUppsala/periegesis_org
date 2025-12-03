<?php

/**
 * The user of forum must login in order to deletes its membership.
 * See leavSelf.php for deregistration via mail 
 * Keep the original session names/keys in this page
 *   in case Forum is also accessible for User login
 */

$intUserID = 0;
if (isset($_SESSION["Forum_UserID"]) && !empty($_SESSION["Forum_UserID"])) {
	$intUserID = (int)$_SESSION["Forum_UserID"];
}

if ($_SESSION["Forum_" . sx_HOST] === false || intval($intUserID) == 0) {
	header("Location: " . sx_PATH . "?pg=message&error=Timeout");
	exit();
}

$tempFirstName = $_SESSION["Forum_FirstName"] ?? '';
$tempLastName = $_SESSION["Forum_LastName"] ?? '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$strChangedData = "";
	$sql = "SELECT UserEmail, IPAddress, ChangedData
		FROM forum_members 
		WHERE UserID = ? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intUserID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$strEmail = $rs["UserEmail"];
		$strIPAddress = $rs["IPAddress"];
		$strChangedData = $rs["ChangedData"];
	} else {
		header("Location: " . sx_PATH . "?pg=message&error=UserNameNotFound");
		exit();
	}
	$stmt = null;
	$rs = null;

	if (!empty($strChangedData)) {
		$strChangedData = $strChangedData . "<br>";
	}
	$strChangedData = trim($strChangedData . date("Y-m-d") . " " . $strEmail) . " " . $strIPAddress;
	/**
	 * Do Not remove All Student Information: 
	 * 		Students must be able to identify, with information added in the field ChangeData
	 * 		Else, remove or comment the first part of the SQL-statement, from IF to ELSE
	 */

	$sql = "UPDATE forum_members SET 
			AllowAccess = 0, 
			UserAddress = '', 
			UserPostCode = '', 
			UserCity = '', 
			UserCountry = '', 
			UserPhone = '', 
			UserEmail = '', 
			UserPassword = '', 
			AllowCode = '', 
			IPAddress = ?, 
			EmailList = 0, 
			EditDate = ?,
			ChangedData = ?
			WHERE UserID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([sx_UserIP, date("Y-m-d"), $strChangedData, $intUserID]);

	$_SESSION["Forum_GreetName"] = $_SESSION["Forum_LastName"];

	unset($_SESSION["Forum_" . sx_HOST]);
	unset($_SESSION["Forum_UserID"]);
	unset($_SESSION["Forum_FirstName"]);
	unset($_SESSION["Forum_LastName"]);
	unset($_SESSION["Forum_UserEmail"]);

	header("Location: " . sx_PATH . "?pg=message&request=leave");
	exit();
}
?>
<section>
	<h4><?= str_SiteTitle ?></h4>
	<h1><?= lngLeaveRegistration ?></h1>
	<p><?= lngClickToLeave ?></p>
	<p><b><?= lngName ?>: </b><?= $tempFirstName . " " . $tempLastName ?></p>
	<form action="<?= sx_PATH ?>?pg=leave" method="Post">
		<fieldset>
			<input type="hidden" name="userName" value="exit" size="20">
			<input type="Submit" name="LeaveAction" value="<?= lngLeave ?>" size="20" maxchars="20">
		</fieldset>
	</form>
</section>