<?php

/**
 * The user must login in order to deletes its membership.
 * See leavSelf.php for deregistration via mail 
 */

$intUserID = 0;
if (isset($_SESSION["Users_UserID"]) && !empty($_SESSION["Users_UserID"])) {
	$intUserID = (int)$_SESSION["Users_UserID"];
}

if ($radio__UserSessionIsActive === false || intval($intUserID) == 0) {
	$_SESSION[] = array();
	session_destroy();
	header("Location: " . sx_PATH . "?pg=message&error=Timeout");
	exit();
}

$tempFirstName = @$_SESSION["Users_FirstName"];
$tempLastName = @$_SESSION["Users_LastName"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$strChangedData = "";
	$sql = "SELECT UserEmail, IPAddress, ChangedData
		FROM users 
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

	$sql = "UPDATE users SET 
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

	$strLastName = $_SESSION["Users_LastName"];
	$_SESSION[] = array();
	session_destroy();

	header("Location: " . sx_PATH . "?pg=message&leave=yes&name=" . $strLastName);
	exit();
}
?>
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