<?php

/**
 * The activation of membership (Allow Access) is depended by the level of membership control and can be made:
 * 		By the applicant itself, through a link sent by email (see join.php), or
 * 		By the administrator only, through a link sent by email (see join.php)
 * If activation is made by the administrator, sent an information email to the member.
 */

$radioAdminRequest = false;
$iParticipantID = 0;
$strRequestToken = "";
$radioContinue = true;

if (isset($_GET["aid"])) {
	$iParticipantID = (int)($_GET["aid"]);
}

//Don't clean, just for compare
if (isset($_GET["ac"])) {
	$strRequestToken = trim($_GET["ac"]);
}

if (intval($iParticipantID) == 0) {
	$radioContinue = false;
}

if (!empty($strRequestToken) && (strlen($strRequestToken) < 24 || strpos($strRequestToken, " ") > 0)) {
	$radioContinue = false;
}

if ($radioContinue) {
	$radioContinue = false;
	$strParticipantEmail = "";
	$strAllowToken = "";
	$strApprovalToken = "";

	$sql = "SELECT Email, AllowToken, ApprovalToken, DeleteToken 
		FROM conf_participants 
		WHERE ParticipantID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$iParticipantID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioContinue = True;
		$strParticipantEmail = $rs["Email"];
		$strAllowToken = $rs["AllowToken"];
		$strApprovalToken = $rs["ApprovalToken"];
		$strDeleteToken = $rs["DeleteToken"];
	}
	$stmt = null;
	$rs = null;

	/**
	 * Unpropable, but just in case: 
	 * 		AllowToken can be used only once, and then deleted
	 * 		Manually inactivated participants cannot activate their account again
	 * 		  if $strAllowToken is empty!
	 */
	if (empty($strParticipantEmail) || empty($strAllowToken)) {
		$radioContinue == false;
	}

	/**
	 * Check if request comes from administrator or the applicant
	 */
	$radioUseAdministrationControl = true;
	if ($radioContinue) {
		if ($radioUseAdministrationControl && $strApprovalToken == $strRequestToken) {
			$radioAdminRequest = true;
		} elseif ($strAllowToken != $strRequestToken) {
			/**
			 * Activation Tokens are not the same
			 *   or the administrator approves the application multiple times
			 *   while ApprovalToken is empty after the first time!
			 */
			$radioContinue = false;
		}
	}

	if ($radioContinue) {
		if ($radioAdminRequest) {
			/**
			 * Application is aproved by the administrator.
			 * Empty ApprovalToken - to be used only once
			 * Sent a mail to the applicant and ask him/her to activate the account
			 */
			$sql = "UPDATE conf_participants SET 
				ApprovalToken = ''
			WHERE ParticipantID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iParticipantID]);

			/** 
			 * SEND MAILS using the included file sx_mail_template.php
			 * 		Check constant and global variables inluded in the mail template...
			 * Variables to be defined here for the mail template:
			 * 		$sx_send_to_email: The mail of the reciever
			 * 		$sx_mail_subject: The subject of mail
			 * 		$sx_mail_content: whatever with HTML formation
			 */

			$sx_send_to_email = $strParticipantEmail;
			$sx_mail_subject = lngSubscriptionInfor;

			/*

			LNG__EmailSentUponRequest: This letter has been sent to you because you applied for a User Account in our website.
			lngYourMembershipIsConfirmed: Your application has been approved by the administration of the site.
			lngRegistrationClickToConfirm: Please, Click on the following link to verify your email address and activate your account:
			lngRegistrationByMailControl_SentMail: You can then log in with the email address and the password you entered with your application.

			*/

			$sx_mail_content = '<h4>This letter has been sent to you because you applied for a User Account in our website.</h4>';
			$sx_mail_content .= '<p>' . lngYourMembershipIsConfirmed . '</p>';

			$confirmURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $iParticipantID . '&ac=' . $strAllowToken;
			$sx_mail_content .= '<p>Please, Click on the following link to verify your email address and activate your account: <a href="' . $confirmURLpath . '"><b>' . lngClickHere . '</b></a>.</p>';
			$sx_mail_content .= '<p>You can then log in with the email address and the password you entered with your application</p>';

			// Send the mail

			include dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
		} else {
			/**
			 * Activate membership
			 * Allow Token cannot be used twice
			 */
			$sql = "UPDATE conf_participants SET 
				AllowAccess = 1, 
				AllowToken = ''
			WHERE ParticipantID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iParticipantID]);

			header("Location: " . sx_PATH . "?pg=message&active=yes");
			exit();
		}
	}
}
/**
 * Uncomment in real site
 */
echo "<script>window.close();</script>";
