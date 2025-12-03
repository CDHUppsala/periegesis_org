<?php

function sx_getParticipationEventByID($id)
{
	$conn = dbconn();
	$sql = "SELECT EventID,
		EventStartDate,
		EventEndDate,
		StartTime,
		EndTime,
		PlaceName, 
		PlaceAddress, 
		PlaceCity, 
		EventTitle, 
        ParticipationMode,
        OnlineParticipationLink
	FROM events 
	WHERE EventID = ?
		AND EventStartDate >= ?
		AND RegisterToParticipate = 1
		AND Hidden = 0 ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id, date('Y-m-d')]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		return $rs;
	} else {
		return null;
	}
}

/**
 * The variable $radio_StopMailWithBlacklistedIP comes from config_events.php
 * and is defined in the table events_setup
 * It stopps (manually) sending mails from blacklisted IPs in case of spam attacks
 */

if (!isset($radio_StopMailWithBlacklistedIP)) {
	$radio_StopMailWithBlacklistedIP = false;
}

$radioBlackListed = false;
$radio_DisplayWarning = false;

$arrError = array();
$radioContinue = false;
$radioValidToken = false;

$strFirstName = "";
$strLastName = "";
$strAffiliation = "";
$strEmail = "";
$strEmailRepeat = "";
$strMode = "Live";

$radioAjaxRequest = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if ($radioAjaxRequest == false) {
	write_To_Log("Newsletter: No Ajax Request Hack-Attempt!");
	echo '<h2>No Way Home!</h2>';
	exit;
}

/**
 * This page opens either from footer Form or from its own Form
 * So, it must always have active Form variables
 */
$iEventID = 0;
if (isset($_POST["FirstEventForm"])) {
	$radioValidFirstEventToken = true;
	if (!isset($_POST['FirstEventFormToken'])) {
		$radioValidFirstEventToken = false;
		write_To_Log("Event Participation First Form: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("FirstEventFormToken", $_POST["FirstEventFormToken"])) {
		$radioValidFirstEventToken = false;
		write_To_Log("Event Participation First Form: Wrong Token Hack-Attempt!");
	}
	if ($radioValidFirstEventToken == false) {
		echo '<h2>Submit Error!</h2>
		<p>Please, reload the initial Event Page and try again.</p>';
		exit;
	}

	if (!empty($_POST["EventID"])) {
		$iEventID = (int) ($_POST["EventID"]);
	}
	if (!empty($_POST["Mode"])) {
		$strMode = $_POST["Mode"];
	}
} else {
	$radioValidToken = true;
	if (!isset($_POST['EventFormToken'])) {
		$radioValidToken = false;
		write_To_Log("Event Participation Ajax Form: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("EventFormToken", $_POST["EventFormToken"])) {
		$radioValidToken = false;
		write_To_Log("Event Participation Ajax Form: Wrong Token Hack-Attempt!");
	}
	if ($radioValidToken == false) {
		echo '<h2>No Way Home!</h2>';
		exit;
	}
	if (!empty($_POST["EventID"])) {
		$iEventID = (int) ($_POST["EventID"]);
	}
}

/**
 * So, if no active Form variables, exit
 */
if (intval($iEventID) == 0) {
	write_To_Log("Event Participation: Zero Event ID Hack-Attempt!");
	echo '<h2>No Way Home!</h2>';
	exit;
}

$arrEvent = sx_getParticipationEventByID($iEventID);
if (!is_array($arrEvent)) {
	write_To_Log("Event Participation: Invalide Event ID Hack-Attempt!");
	echo '<h2>No Way Home!</h2>';
	exit;
} else {
	$dEventStartDate = $arrEvent["EventStartDate"];
	$dEventEndDate = $arrEvent["EventEndDate"];
	$sStartTime = $arrEvent["StartTime"];
	$sEndTime = $arrEvent["EndTime"];
	$sPlaceName = $arrEvent["PlaceName"];
	$sPlaceAddress = $arrEvent["PlaceAddress"];
	$sPlaceCity = $arrEvent["PlaceCity"];
	$sEventTitle = $arrEvent["EventTitle"];
	$strParticipationMode = $arrEvent["ParticipationMode"];
	$strOnlineParticipationLink = $arrEvent["OnlineParticipationLink"];
}

$dt = return_Date_From_Datetime($dEventStartDate);
$str_DatePeriod = lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt);
if (!empty($dEventEndDate)) {
	$dt = return_Date_From_Datetime($dEventEndDate);
	$str_DatePeriod .= " - " . lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt);
} else {
	$str_DatePeriod .= "({$dt})";
}

$str_EmailPlaceTime = "";
if ($strParticipationMode != 'Online') {
	$str_EmailPlaceTime = "<b>" . lngPlace . ":</b> ";
	if (!empty($sPlaceName)) {
		$str_EmailPlaceTime .= $sPlaceName;
	}
	if ($sPlaceAddress != "") {
		$str_EmailPlaceTime .= ', ' . $sPlaceAddress;
	}
	if ($sPlaceCity != "") {
		$str_EmailPlaceTime .= ', ' . $sPlaceCity;
	}
} else {
	$str_EmailPlaceTime = "<b>" . lngPlace . ":</b> Online";
}
if ($sStartTime != "") {
	$str_EmailPlaceTime .= "<br><b>" . lngTime . ":</b> " . $sStartTime;
	if ($sEndTime != "") {
		$str_EmailPlaceTime .= " - " . $sEndTime;
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $radioValidToken && $radioAjaxRequest) {
	$radioContinue = True;

	/**
	 * Check the frequency of Form Requests, in relation to Form Creation Time
	 * - if less than X second, exit
	 */
	$radioRedirect = false;
	if (isset($_SESSION['EventFormCreationTime'])) {
		$dFormTime = $_SESSION['EventFormCreationTime'];
		if (return_Is_Date($dFormTime, 'Y-m-d H:i:s')) {
			$seconds_passing = return_Date_Time_Total_Difference($dFormTime, date('Y-m-d H:i:s'), 'seconds');
			if ((int) $seconds_passing < 8) {
				$radioRedirect = true;
			}
		} else {
			$radioRedirect = true;
		}
	} else {
		$radioRedirect = true;
	}

	if ($radioRedirect) {
		write_To_Log("Event Registration: Frequent form requests!");
		sleep(5);
		echo '<h2>No Way Home!</h2>';
		exit;
	}

	if (!empty($_POST['City']) || !empty($_POST['Country'])) {
		write_To_Log("Event Registration: Hidden fields with value!");
		sleep(5);
		echo '<h2>No Way Home!</h2>';
		exit;
	}

	/**
	 * ================================================
	 * Just contunue to get and check input values
	 * ================================================
	 */

	$checkApplicantEmail = false;
	if (!empty($_POST["Email"])) {
		$strEmail = trim($_POST["Email"]);
		$strEmailRepeat = trim($_POST["EmailRepeat"]);
		$checkApplicantEmail = filter_var($strEmail, FILTER_VALIDATE_EMAIL);
	}

	if ($checkApplicantEmail == false || $strEmail != $strEmailRepeat) {
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	}

	// Check if emale has MX Record Check, using PHP function checkdnsrr()
	if (sx_has_email_domain_mx($strEmail) === false) {
		write_To_Log("Event Registration: Email address with no MX Record: {$strEmail} Hack-Attempt ");
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	}

	// Check against files with list of blacklisted disposable email domains
	if (!empty($strEmail)) {
		if (is_email_domain_disposable($strEmail)) {
			write_To_Log("Newsletter: Disposable email addresses: {$strEmail} Hack-Attempt ");
			$radioContinue = false;
			$arrError[] = "Disposable email addresses are not allowed. Please check your email domain.";
		}
	}


	if (empty($_POST['FirstName']) || empty($_POST['LastName'])) {
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	} else {
		$strFirstName = sx_Sanitize_Input_Text($_POST['FirstName']);
		$strLastName = sx_Sanitize_Input_Text($_POST['LastName']);
	}

	if (!empty($_POST["Affiliation"])) {
		$strAffiliation = sx_Sanitize_Input_Text($_POST["Affiliation"]);
	}

	$strMode = "";
	if (empty($_POST["Mode"])) {
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	} else {
		$strMode = $_POST["Mode"];
	}

	if (sx_radioValidIP == false) {
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	}

	$arrMode = ["Online", "Live"];
	if (!in_array($strMode, $arrMode)) {
		$radioContinue = false;
		$arrError[] = lngInfoErrorTryAgain;
	}

	/**
	 * Check Captcha after form inputs
	 */
	if (!isset($_SESSION['captcha_code'])) {
		$radioContinue = false;
		$arrError[] = LNG__CaptchaError;
	} elseif (empty($_POST['captcha_input']) || ($_POST['captcha_input'] != $_SESSION['captcha_code'])) {
		$radioContinue = false;
		$arrError[] = LNG__CaptchaError;
	}


	if ($radioContinue) {
		/**
		 * Check if IP is blacklisted
		 * HOWEVER, NOT the client's IP but the email domain's IP
		 */

		//$radioBlackListed = sx_is_ip_blacklisted(sx_UserIP);
		$radioBlackListed = check_Blacklisted_emeil_domain_Ips($strEmail);

		/**
		 * Check if mail exist in event_participants
		 * and if it is marked valid (by the administration)
		 */
		if ($radioBlackListed) {
			$sql = "SELECT ParticipantID
			FROM event_participants
			WHERE Verified = 1
				AND Email = ? LIMIT 1";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$radioBlackListed = false;
			}
			$stmt = null;
			$rs = null;
		}

		/**
		 * Check if mail exist in Blacklist table
		 * and if it is marked valid by the administration
		 */
		if ($radioBlackListed) {
			$sql = "SELECT BlackListID
			FROM event_blacklisted_ips
			WHERE ValidMailAddress = 1
				AND Email = ? LIMIT 1";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$radioBlackListed = false;
			}
			$stmt = null;
			$rs = null;
		}
	}

	/**
	 * ====================================================
	 * Obs! the rest of code related to Blacklisted IPs
	 *   concerns only New Emails, that not exist in the above two
	 *   tables or have not been marked as valid by the administration
	 * ====================================================
	 */

	/**
	 * Add to or update the tabel event_blacklisted_ips
	 * - Dont't add the same IP and email in the table
	 * - Just increase the number of endeavours
	 */
	if ($radioContinue && $radioBlackListed) {
		/**
		 * Check first if IP and Email are already in the table
		 */
		$intBlackListID = 0;
		$intEndeavours = 1;
		$sql = "SELECT BlackListID, Endeavours
			FROM event_blacklisted_ips
			WHERE ValidMailAddress = 0
				AND Email = ? 
				AND IPAddress = ? LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strEmail, sx_UserIP]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$intBlackListID = (int) $rs['BlackListID'];
			$intEndeavours = (int) $rs['Endeavours'];
		}
		$stmt = null;
		$rs = null;

		// If IP and Email exists, update, else, insert
		if ((intval($intBlackListID) > 0)) {
			$intEndeavours++;
			if ($intEndeavours < 9999) {
				$sql = "UPDATE event_blacklisted_ips SET 
						Endeavours = ?
					WHERE BlackListID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$intEndeavours, $intBlackListID]);
			}
		} else {
			$strFormSource = "EventID: " . $iEventID . ' ' . $strMode;
			$stErrorTypy = "Blacklisted IP";

			$sql = "INSERT INTO event_blacklisted_ips (
				FormSource, ErrorTypy, FirstName, LastName, Email, IPAddress)
				VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strFormSource, $stErrorTypy, $strFirstName, $strLastName, $strEmail, sx_UserIP]);
		}
	}

	/**
	 * Two alternative methosd to Deal with Blacklisted IPs and SPAMS
	 * ==============================================================================
	 * A. with use of the table event_black_lis - Not used by defaultt
	 * B. without use of the table event_blacklisted_ips (default)
	 */

	/**
	 * A. Stop all activities for blacklisted IPs
	 * ==========================================================
	 * Not used by default
	 * Use it on brute attacks only, by setting $radio_StopMailWithBlacklistedIP = true
	 * BASICALLY, this method stops sending emails to participants
	 *   to protect the site's IP from being blacklisted
	 *   and don't register participants with propably fake emails
	 */

	if ($radioContinue && $radioBlackListed && $radio_StopMailWithBlacklistedIP) {
		$radioContinue = false;
		$radio_DisplayWarning = true;

		sleep(5);
	}

	/**
	 * Continue only if the first method is not used
	 */
	if ($radioContinue && $radioBlackListed) {

		/**
		 * B. Use the table event_participants to detect spam attacks  
		 * ==========================================================
		 * Check for multiple registrations for the current Event have been made by
		 *  1. the current email, 
		 *  2. the set of IPs which have the first 6 characters equal to the current IP
		 *  3. If multiple registration are encountered:
		 * 		- send warnings to administration
		 * 		- don't add the registration to the table event_participants,
		 * 		  and don't send mail to the applicant (to protect the IP of the website)
		 * 		- show a warning to the visitor (alternatively, redirect and exit)
		 */

		/**
		 * 1. Check the number of registration for an Event by the current email 
		 * - if more than 3 times, send warnings and stop the registration
		 */

		$intParticipantID = 0;
		$intRegistrationTimes = 0;
		$sql = "SELECT ParticipantID, RegistrationTimes
			FROM event_participants
			WHERE EventID = ?
				AND Email = ? LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iEventID, $strEmail]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$intParticipantID = (int) $rs['ParticipantID'];
			$intRegistrationTimes = (int) $rs['RegistrationTimes'];
		}
		$stmt = null;
		$rs = null;

		$radioSendWarningForEmail = false;
		if ($intRegistrationTimes >= 2) {
			$radioSendWarningForEmail = true;
		}

		/**
		 * 2. Check the number of registration for an Event by IPs 
		 * wich have the 6 left characters equal to the current IP
		 * - if more than X, send warnings and stop the registration
		 */
		$radioSendWarningForIP = false;
		$sql = "SELECT SUM(RegistrationTimes) AS SumRegistrationTimes
				FROM event_participants
				WHERE Verified = 0
					AND EventID = ?
					AND LEFT(IPAddress, 6) = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iEventID, substr(sx_UserIP, 0, 6)]);
		$count = $stmt->fetchColumn();
		$stmt = null;
		if ((int) $count >= 4) {
			$radioSendWarningForIP = true;
		}

		/**
		 * 3. Send warning email to administration and stop the registration
		 */

		require __DIR__ . "/administration_mail.php";
	}

	if ($radioContinue) {

		/**
		 * Don't add the participant if already registrated for the same EventID
		 *  just increase registration times
		 *  and change the participation mode
		 */
		$intParticipantID = 0;
		$intRegistrationTimes = 0;
		$sql = "SELECT ParticipantID, RegistrationTimes
		FROM event_participants
		WHERE EventID = ?
			AND Email = ? LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iEventID, $strEmail]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$intParticipantID = (int) $rs['ParticipantID'];
			$intRegistrationTimes = (int) $rs['RegistrationTimes'];
		}
		$stmt = null;
		$rs = null;

		if ($intParticipantID > 0) {
			$intRegistrationTimes++;
			$sql = "UPDATE event_participants SET
				ParticipationMode = ?,
				RegistrationTimes = ?
			WHERE ParticipantID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strMode, $intRegistrationTimes, $intParticipantID]);
		} else {
			$radioVerified = 1;
			$radioBlacklistedIP = 0;
			if ($radioBlackListed) {
				$radioVerified = 0;
				$radioBlacklistedIP = 1;
			}
			$sql = "INSERT INTO event_participants
				(EventID,
				Verified,
				ParticipationMode,
				FirstName, LastName, Affiliation, Email,
				IPAddress, BlacklistedIP)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([
				$iEventID,
				$radioVerified,
				$strMode,
				$strFirstName,
				$strLastName,
				$strAffiliation,
				$strEmail,
				sx_UserIP,
				$radioBlacklistedIP
			]);
		}

		/**
		 * Send mail to the applicant
		 */

		require __DIR__ . "/participation_mail.php";

		/**
		 * Show messages depending on the mode of participation
		 */

		$str__ParticipationNote = 'A verification email has been sent to you 
				with information about the event and your registration.';
		if ($strMode == 'Online' && !empty($strOnlineParticipationLink)) {
			$str__ParticipationNote = 'A confirmation email has been sent to you
					with information and an access link. If you do not receive the confirmation
					email within a few minutes of signing up, please check your spam folder.';
		}
		unset($_SESSION['EventFormCreationTime']);
	}
}
