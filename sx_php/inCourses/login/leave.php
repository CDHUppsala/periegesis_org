<?php

/**
 * The user must login in order to deletes its membership.
 * See leavSelf.php for deregistration via mail 
 */

$intStudentID = 0;
if (isset($_SESSION["Students_StudentID"]) && !empty($_SESSION["Students_StudentID"])) {
	$intStudentID = (int)$_SESSION["Students_StudentID"];
}

if (!isset($_SESSION["Students_" . sx_DefaultSiteLang]) || intval($intStudentID) == 0) {
	header("Location: " . sx_PATH . "?pg=message&error=Timeout");
	exit();
}

$tempFirstName = @$_SESSION["Students_FirstName"];
$tempLastName = @$_SESSION["Students_LastName"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$strChangedData = "";
	$sql = "SELECT Email, IPAddress, ChangedData
		FROM course_students 
		WHERE StudentID = ? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intStudentID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$strEmail = $rs["rEmail"];
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

	$sql = "UPDATE course_students SET 
			AllowAccess = 0, 
			PostAddress = '', 
			PostCode = '', 
			City = '', 
			Country = '', 
			Phone = '', 
			Email = '', 
			LoginPassword = '', 
			ActivationCode = '', 
			IPAddress = ?, 
			EmailList = 0, 
			EditDate = ?,
			ChangedData = ?
			WHERE StudentID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([sx_UserIP, date("Y-m-d"), $strChangedData, $intStudentID]);

	$strLastName = $_SESSION["Students_LastName"];

	// unset data in $_SESSION
	$_SESSION[] = array();

	// destroy the session
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