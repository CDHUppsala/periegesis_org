<?php

/**
 * The activation of forum membership always requires the authentication
 * of the applicant's email address. This is always made by the applicant, 
 * though in 2 alternative forms (determined in table Forum Setup):
 * 1.	Only by the applicant itself, through a link sent by email (see join.php),
 * 			- Not recomended, or
 * 2.	Through a first approval by the administrator, through a link sent by email (see join.php)
 * 			- Recomended and default alternative
 * 			- After the approval, an email is sent to the member with a link for the verification
 * 			  of the email address.
 * 
 * This file is included in the file config_forum_login.php, 
 * at the top of the forum_login.php page, because
 * - it closes the window, if request comes from administrator
 * - it redirects to the login page, if it comes from the user
 */

$radioAdminRequest = false;
$intUserID = 0;
$strRequestCode = "";
$radioContinue = true;

$intUserID = $_GET["aid"] ? (int) $_GET["aid"] : 0;
if (intval($intUserID) === 0) {
	$radioContinue = false;
}

$strRequestCode = trim($_GET["ac"] ?? '');

if (empty($strRequestCode) || strlen($strRequestCode) < 36) {
	$radioContinue = false;
}

if ($radioContinue === false) {
	header('Location: index.php?sx=1');
	exit;
} else {

	/**
	 * Get the Allow Code (for user) and the Approva codes (for admin)
	 */
	$strSendUserEmail = "";
	$strAllowCode = "";
	$strApprovalCode = "";
	$sql = "SELECT UserEmail, AllowCode, ApprovalCode
		FROM forum_members 
		WHERE AllowAccess = 0
		AND UserID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intUserID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$strSendUserEmail = $rs["UserEmail"];
		$strAllowCode = $rs["AllowCode"];
		$strApprovalCode = $rs["ApprovalCode"];
	}
	$stmt = null;
	$rs = null;

	if (empty($strSendUserEmail)
		|| (empty($strAllowCode) && empty($strApprovalCode))
	) {
		$radioContinue = false;
	}

	/**
	 * Check if request comes from administrator or the applicant
	 */
	if ($radioContinue) {
		if ($radioUseAdministrationControl && $strApprovalCode == $strRequestCode) {
			$radioAdminRequest = true;
		} elseif ($strAllowCode != $strRequestCode) {
			/**
			 * Activation Code not the same
			 */
			$radioContinue = false;
		}
	}

	if ($radioContinue === false) {
		header('Location: index.php?sx=2');
		exit;
	} else {
		if ($radioAdminRequest === false) {
			/**
			 * The request comes from the user, so activate
			 * the registration, delete the Allow Code and redirect
			 */
			$sql = "UPDATE forum_members SET 
				AllowAccess = 1, 
				AllowCode = '' 
				WHERE UserID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intUserID]);

			header("location: forum_login.php?pg=login&welcome=yes");
			exit();
		} else {
			/**
			 * The registration is approved by the administrator, sent a mail 
			 * to activate the registration by the user
			 */

			/** 
			 * SEND MAILS using the included file sx_mail_template.php
			 * 		Check constant and global variables inlcuded in the mail template...
			 * Variables to be defined here for the mail template:
			 * 		$sx_send_to_email: The mail address of the reciever
			 * 		$sx_mail_subject: The subject of mail
			 * 		$sx_mail_content: whatever with HTML formation
			 */
			$sx_send_to_email = $strSendUserEmail;
			$sx_mail_subject = lngSubscriptionInfor;

			$confirmURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $intUserID . '&ac=' . $strAllowCode;

			$sx_mail_content = '<p>' . LNG__EmailSentUponForumRequest . ' ' . str_SiteTitle . '.</p>';
			$sx_mail_content .= '<p>' . lngYourMembershipIsConfirmed . '</p>';
			$sx_mail_content .= '<p>' . lngToLoginGoTo . ' <a href="' . $confirmURLpath . '">' . lngClickHere . '</a>.</p>';

			require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
		}
	}
}
/**
 * Uncomment in real site
 */
//echo "<script>window.close();</script>";
