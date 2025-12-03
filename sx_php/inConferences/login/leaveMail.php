<?php

/**
 * The member deletes its membership (deregistrates) from a link sent by email .
 * See leav.php for deregistration via login
 * Ask the member if (s)he is sure!
 */
$iParticipantID = 0;
$strDeregCode = "";

$strFirstName = "";
$strLastName = "";

$radioContinue = False;
if (isset($_GET["aid"])) {
	$iParticipantID = (int)($_GET["aid"]);
	$_SESSION['aid'] = $iParticipantID;
} elseif (isset($_POST["leave"]) && isset($_SESSION['aid'])) {
	$iParticipantID = $_SESSION['aid'];
	unset($_SESSION['aid']);
}

if (isset($_GET["dc"])) {
	$strDeregCode = sx_Sanitize_Search_Text($_GET["dc"]);
	$_SESSION['dc'] = $strDeregCode;
} elseif (isset($_POST["leave"]) && isset($_SESSION['dc'])) {
	$strDeregCode = $_SESSION['dc'];
	unset($_SESSION['dc']);
}

/**
 * The length of deregistration code is usually >= 36
 * See join.php for the actual length, but just use a minimum here
 */
if (intval($iParticipantID) == 0 || empty($strDeregCode) || strlen($strDeregCode) < 12) {
	header('Location: ' . sx_PATH . '?pg=message&error=UserNameNotFound');
	exit();
} else {
	$sql = "SELECT FirstName, LastName 
		FROM conf_participants 
		WHERE ParticipantID = ? 
		AND DeleteToken = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$iParticipantID, $strDeregCode]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioContinue = true;
		$strFirstName = $rs["FirstName"];
		$strLastName = $rs["LastName"];
	} else {
		header("Location: " . $strPathInfo . "?pg=message&error=UserNameNotFound");
		exit();
	}
	$stmt = null;
	$rs = null;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && $radioContinue) {
	sleep(3);
	$sql = "UPDATE conf_participants SET 
		AllowAccess = 0, 
		FirstName = '', 
		LastName = '', 
		Photo = '', 
		Title = '', 
		PostAddress = '', 
		PostCode = '', 
		City = '', 
		Country = '', 
		Phone = '', 
		Email = '', 
		LoginPassword = '', 
		AllowToken = '', 
		DeleteToken = '', 
		IPAddress = ?, 
		EmailList = 0, 
		EditDate = ?, 
		ChangedData = '', 
		Biography = '' 
	WHERE ParticipantID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([sx_UserIP, date("Y-m-d"), $iParticipantID]);

	$strKeys = array_keys($_SESSION);
	foreach ($strKeys as $key) {
		unset($_SESSION[$key]);
	}
	header("Location: " . sx_PATH . "?pg=message&leave=yes&name=" . $strFirstName);
	exit();
}
?>
<h1><?= lngLeaveRegistration ?></h1>
<p><?= lngClickToLeave ?></p>
<p><b><?= lngName ?>: </b><?= $strFirstName . " " . $strLastName ?></p>
<form action="<?= sx_PATH ?>?pg=leavemail" method="Post">
	<fieldset>
		<input type="hidden" name="leave" value="YES">
		<input type="Submit" name="Action" value="<?= lngLeave ?>" size="20" maxchars="20">
	</fieldset>
</form>