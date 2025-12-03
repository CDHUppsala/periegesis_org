<?php

/**
 * A. If the registration to the site does not require administration approval
 * 	- the activation of the registration is made by the applicant itself, through a link sent by email (see join.php).
 * 		- in that case, the requested code refers to the field ActivationCode
 * 		  thereby, the applicant verifies the authenticity of email address and activates the registration 
 * B. If the registration requires the approval be the administration, through a link sent by email (see join.php)
 *	- the request code refers to the field ApprovalCode, if approved
 * 		- sent an information email to the applicant with a link 
 * 		  to activate the registration as in the above case
 * The program determines here which of the two is requested by comparing 
 *   the requested code with ActivationCode and ApprovalCode
 */

$radioAdminApprove = False;
$intStudentID = 0;
$strRequestCode = "";
$radioContinue = true;

if (isset($_GET["aid"])) {
	$intStudentID = (int)($_GET["aid"]);
}
if (intval($intStudentID) == 0) {
	$radioContinue = False;
}

//Don't clean the requested code, it's used just for compare
if (isset($_GET["ac"])) {
	$strRequestCode = $_GET["ac"];
}
if (strlen($strRequestCode) < 24) {
	$radioContinue = False;
}

if ($radioContinue) {
	$radioContinue = false;
	$strApplicantEmail = "";
	$sql = "SELECT Email, ActivationCode, ApprovalCode
		FROM course_students 
		WHERE AllowAccess = 0
		AND StudentID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intStudentID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioContinue = true;
		$strApplicantEmail = $rs["Email"];
		$strActivationCode = $rs["ActivationCode"];
		$strApprovalCode = $rs["ApprovalCode"];
	}
	$stmt = null;
	$rs = null;

	/**
	 * The email and the ActivationCode codes should not be empty
	 *   as the ActivationCode can be used only once and is deleted efter activation
	 */
	if (empty($strApplicantEmail) || empty($strActivationCode)) {
		$radioContinue = false;
	}
}

/**
 * Check if request code comes from administrator or the applicant
 */
if ($radioContinue) {
	if ($strApprovalCode == $strRequestCode) {
		// request comes from administrator
		$radioAdminApprove = true;
	} elseif ($strActivationCode != $strRequestCode) {
		// request comes from the applicant, but Codes are not equal
		$radioContinue = false;
	}
}

if ($radioContinue) {
	if ($radioAdminApprove == false) {
		// request comes from the applicant, so activate the registration and exit
		$sql = "UPDATE course_students SET 
				AllowAccess = 1, 
				ActivationCode = '' 
				WHERE StudentID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intStudentID]);

		header("location: " . sx_PATH . "?pg=login&msg=yes");
		exit();
	} else {
		/**
		 * Request comes from administrator and the registration is approved
		 * So, sent a mail to the applicant with a link to verify mail address 
		 *   and activate the registration as in case A above
		 */

		/**
		 * SEND MAILS using the included file sx_mail_template.php
		 * 		Check constant and global variables inluded in the mail template...
		 * Variables to be defined here:
		 * 		$sx_send_to_email: The mail of the reciever
		 * 		$sx_mail_subject: The subject of mail
		 * 		$sx_mail_content: whatever with HTML formation
		 */
		$sx_mail_subject = 'Registration in Students Platform';
		$sx_send_to_email = $strApplicantEmail;


		//== URL to activate the registration
		$confirmURLpath = sx_ROOT_HOST_PATH . "?pg=allow&aid=" . $intStudentID . "&ac=" . $strActivationCode;

		//== Email content

		$sx_mail_content = '<h4>Thank you for your application!</h4>';
		$sx_mail_content .= '<p>This letter has been sent to you because you
			applied for registration in the Students\' Platform of the '. str_SiteTitle .' website.</p>';

		$sx_mail_content .= '<h4>Your application has been approved</h4>';
		$sx_mail_content .= '<p>Please, <a href="' . $confirmURLpath . '">Click Here</a> 
			to <b>verify</b> your email address and <b>activate</b> your account.
			You can then <b>login</b> with your email and password.</p>';

		require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
	}
}
/**
 * Uncomment in real site
 */
echo "<script>window.close();</script>";