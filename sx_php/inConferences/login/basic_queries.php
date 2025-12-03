<?php

/**
 * Check if participant is logged in is doen in check_sessions.php
 */

$str_ParticipantPortrait = null;

if ($radio_LoggedParticipant && $int_ParticipantID > 0) {
	$str_ParticipantPortrait = return_Field_Value_From_Table('conf_participants', 'Portrait', 'ParticipantID', $int_ParticipantID);
}

/**
 * Get general options
 */
$sql = "SELECT UseParticipantsLogin, 
	AllowOnlineRegistration, 
	UseAdministrationControl,
	LoginTitle, LoginNote, 
	RegistrationTitle, RegistrationNotes, 
	WelcomeTitle, WelcomeNotes, 
	ConditionsTitle, ConditionsNotes, 
	AllowAddProfile, AddProfileNotes,
    UsePaperAbstract,
    	PaperAbstractNotes,
	UseFileApload,
		FileAploadNotes
	FROM conf_participants_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($rs)) {
	$radio_UseParticipantsLogin = $rs["UseParticipantsLogin"];
	$radioAllowOnlineRegistration = $rs["AllowOnlineRegistration"];
	$radioUseAdministrationControl = $rs["UseAdministrationControl"];
	$strLoginTitle = $rs["LoginTitle"];
	$memoLoginNote = $rs["LoginNote"];
	$strRegistrationTitle = $rs["RegistrationTitle"];
	$memoRegistrationNotes = $rs["RegistrationNotes"];
	$strWelcomeTitle = $rs["WelcomeTitle"];
	$memoWelcomeNotes = $rs["WelcomeNotes"];
	$strConditionsTitle = $rs["ConditionsTitle"];
	$memoConditionsNotes = $rs["ConditionsNotes"];
	$radio_AllowAddProfile = $rs["AllowAddProfile"];
	$memo_AddProfileNotes = $rs["AddProfileNotes"];
	$radio_UsePaperAbstract = $rs["UsePaperAbstract"];
	$memo_PaperAbstractNotes = $rs["PaperAbstractNotes"];
	$radio_UseFileApload = $rs["UseFileApload"];
	$memo_FileAploadNotes = $rs["FileAploadNotes"];
}

$stmt = null;
$rs = null;

/**
 * If a participant is loged in:
 *  	1.	Find the first comming conference to wich (s)he is registered,
 * 			if any, and get the fields:
 * 				- Conference ID
 * 				- Conference Title
 * 				- AllowToSendPaperAbstracts
 * 				- AllowAskToUploadFiles
 * 		2. 	Check if the general options for File Upload and Abstracts are 
 * 			supported by the specific option of the conference in question
 * 				...but only if at least on of general options is true
 * 		3. 	Greate global variables according to the value 
 * 			of options to be used in menu and pages related to:
 * 		 		Rights to send Abstract
 * 				Rights to Upload Files
 */

$int_RightsConferenceID = 0;
$str_RightsConferenceTitle = "";
$radio_RightsToSendAbstracts = false;
$radio_RightsToUploadFiles = false;

if (
	$radio_LoggedParticipant &&
	$int_ParticipantID > 0 &&
	($radio_UsePaperAbstract || $radio_UseFileApload)
) {
	$sql = "SELECT 
			c.ConferenceID, 
			c.Title, 
			c.AllowToSendPaperAbstracts,
			c.AllowAskToUploadFiles
		FROM conferences AS c
		INNER JOIN conf_to_participants AS cp 
			ON c.ConferenceID = cp.ConferenceID
		WHERE cp.ParticipantID = ? 
			AND cp.Cancelled = 0
			AND c.StartDate >= CURDATE() 
		ORDER BY c.StartDate LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$int_ParticipantID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$int_RightsConferenceID = $rs["ConferenceID"];
		$str_RightsConferenceTitle = $rs["Title"];
		$radio_AllowToSendPaperAbstracts =  $rs["AllowToSendPaperAbstracts"];
		$radio_AllowAskToUploadFiles =  $rs["AllowAskToUploadFiles"];
	}
	$stmt = null;
	$rs = null;
	// Check allowed rights
	if ((int) $int_RightsConferenceID > 0) {
		if ($radio_UsePaperAbstract && $radio_AllowToSendPaperAbstracts) {
			$radio_RightsToSendAbstracts = true;
		}
		if ($radio_UseFileApload && $radio_AllowAskToUploadFiles) {
			$radio_RightsToUploadFiles = true;
		}
	}
}

/**
 * POTENTIAL PROBLEMS:
 * If a member is registered in two subsequent confrences, A and B,
 * and is given upload rights for conference B,
 * these right will not be activated untlill 
 * the next to startday of conference A
 * The problem ca be solved if the member temporally cancels
 * the participation in cconferences A.
 */

/**
 * If rights to upload files are allowed for this conference,
 * 		Check if the loged participant has these rights
 * 		and get the rights areas
 * Incase, check if participant has cancelled aks for right
 * 		in conf_to_participants table
 */
$radio_ToUploadImages = false;
$radio_ToUploadDocuments = false;
$radio_ToUploadMedia = false;

if (
	$radio_LoggedParticipant
	&& $radio_RightsToUploadFiles
	&& $int_RightsConferenceID > 0
	&& $int_ParticipantID > 0
) {
	$sql = "SELECT
			a.ToUploadImages,
			a.ToUploadDocuments,
			a.ToUploadMedia
		FROM conf_rights AS a
		INNER JOIN conf_to_participants AS b
			ON a.ConferenceID = b.ConferenceID AND a.ParticipantID = b.ParticipantID
		WHERE a.ConferenceID = ?
			AND a.ParticipantID = ?
			AND a.AllowRights = 1
			AND (a.WithdrawRightsDate >= CURDATE()
				OR a.WithdrawRightsDate IS NULL)
			AND b.AsksToUploadFiles = 1 LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$int_RightsConferenceID, $int_ParticipantID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radio_ToUploadImages = $rs["ToUploadImages"];
		$radio_ToUploadDocuments = $rs["ToUploadDocuments"];
		$radio_ToUploadMedia = $rs["ToUploadMedia"];
	}
	$stmt = null;
	$rs = null;

    /**
	 * For future development, as Upload is currently the only Right Level
	 */
	if (
		!$radio_ToUploadImages &&
		!$radio_ToUploadDocuments &&
		!$radio_ToUploadMedia
	) {
		$radio_RightsToUploadFiles = false;
	}
}

/**
 * And, so, the final check:
 * if both rights are false, no rights are used anywhere
 */

 if ($radio_RightsToSendAbstracts == false && $radio_RightsToUploadFiles == false) {
	$int_RightsConferenceID = 0;
	$str_RightsConferenceTitle = "";
}
