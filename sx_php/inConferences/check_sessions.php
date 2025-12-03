<?php

if (
	isset($_SESSION["Participants_" . sx_HOST]) &&
	$_SESSION["Participants_" . sx_HOST] == true &&
	isset($_SESSION["ParticipantID_" . sx_HOST]) &&
	$_SESSION["ParticipantID_" . sx_HOST] > 0
) {
	$radio_LoggedParticipant = true;
	$int_ParticipantID = $_SESSION["ParticipantID_" . sx_HOST];
} else {
	$radio_LoggedParticipant = false;
	$int_ParticipantID = 0;
	unset($_SESSION["Participants_" . sx_HOST]);
	unset($_SESSION["ParticipantID_" . sx_HOST]);
	unset($_SESSION["Part_FirstName"]);
	unset($_SESSION["Part_LastName"]);
}

/*
if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
	header('Location: index.php');
	exit();
}
*/
