<?php

/**
 * The Participant must login in order to deletes its Participantship.
 * See leavSelf.php for deregistration via mail 
 */

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
	header('Location: index.php');
	exit();
}

$tempFirstName = $_SESSION["Part_FirstName"];
$tempLastName = $_SESSION["Part_LastName"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
	$stmt->execute([sx_UserIP, date("Y-m-d"), $int_ParticipantID]);

	$strLastName = $_SESSION["Part_LastName"];

	/**
	 * Just clear all data of all session variable
	 * session_unset();
	 * 
	 * Remove all session:
	 */
	session_destroy();

	header("Location: " . sx_PATH . "?pg=message&leave=yes&name=" . $strLastName);
	exit();
}
?>
<h1 class="head"><span><?php echo lngLeaveRegistration ?></span></h1>
<p><?= lngClickToLeave ?></p>
<p><b><?= lngName ?>: </b><?= $tempFirstName . " " . $tempLastName ?></p>
<form action="<?= sx_PATH ?>?pg=leave" method="Post">
	<fieldset>
		<input type="hidden" name="leave" value="YES">
		<input type="Submit" name="LeaveAction" value="<?= lngLeave ?>" size="20" maxchars="20">
	</fieldset>
</form>