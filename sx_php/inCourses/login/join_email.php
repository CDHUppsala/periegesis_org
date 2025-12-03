<?php
/**
* SEND MAILS using the included file sx_mail_template.php
* Check constant and global variables inluded in the mail template...
* Variables to be defined here:
* $sx_send_to_email: The mail of the reciever
* $sx_mail_subject: The subject of mail
* $sx_mail_content: whatever with HTML formation
*/

$sx_mail_subject = "Registration in Student's Platform";

if ($radio_SendMailToApplicant) {
$sx_send_to_email = $sEmail;

//== Email content
$sx_mail_content = '<h4>Thank you for your application!</h4>';
$sx_mail_content .= '<p>This letter has been sent to you
	because you applied for an account in the Students Platform of the '. str_SiteTitle .' website.</p>';
$sx_mail_content .= '<p>Your application will now be processed.
	Once completed, you will receive a new e-mail containing a confirmation link.
	Please click on that link to complete your registration.
	You can then log in by using your e-mail and password.</p>';

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
}

/** Obs! CHANGES:
* All registrations should be controlled by the administrator
*/

$sx_send_to_email = str_SiteEmail;
$confirmAdminURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $intStudentID . '&ac=' . $sApprovalCode;

$sx_mail_content = '<h3>The following person applied for registration in Students\' Platform:</h3>';
$sx_mail_content .= '<p><b>Name:</b>' . $sFirstName . ' ' . $sLastName . '
	<br><b>Email:</b> ' . $sEmail . '
	<br><b>Title:</b> ' . $sTitle . '
	<br><b>Institution:</b> ' . $sInstitution . '
	<br><b>Optional infomation:</b>
	Adderess: ' . $sAddress . ',
	City: ' . $sCity . ',
	Postal Code: ' . $sPostCode . ',
	Counry: ' . $sCountry . ',
	Phone: ' . $sPhone . '
</p>';
if ($radioBlackListed) {
$sx_mail_content .= '<p>Please notice that the IP of the applicant (' . sx_UserIP . ') is <b>Blacklisted</b>.<br>
	This might not neccassary mean that the application or the email address are invalid.</p>';
}
$sx_mail_content .= '<p><a style="text-decoration: none;" href="' . $confirmAdminURLpath . '">
		Click here to Approve the Application</a>.</p>';
$sx_mail_content .= '<p>A new mail will be sent to the applicant
	with a link to verify the email address and activate the account.</p>';

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
