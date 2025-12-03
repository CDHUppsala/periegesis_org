<?php

/** 
 * SEND MAILS using the included file sx_mail_template.php
 * 		Check constant and global variables inluded in the mail template...
 * Variables to be defined here for the mail template:
 * 		$sx_send_to_email: The mail of the reciever
 * 		$sx_mail_subject: The subject of mail
 * 		$sx_mail_content: whatever with HTML formation
 */

$sx_mail_subject = lngSubscriptionInfor;

if ($radio_SendMailToApplicant) {
    $sx_send_to_email = $sEmail;

    $sx_mail_content = '<h4>This mail has been sent to you because you applied for a user account in our Website.</h4>';
    $sx_mail_content .= '<p>' . lngRegistrationByAdminControl_SentMail . '</p>';

    include dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
}

$sx_send_to_email = str_SiteEmail;

$sx_mail_content = '<h4>Approve Member Registration to Conference Website</h4>';
$sx_mail_content .= '<p><b>Name:</b> ' . $sFirstName . ' ' . $sLastName . '
        <br><b>Email:</b> ' . $sEmail . '
        <br><b>Optional infomation: </b>
        Address: ' . $sAddress . ', 
        City: ' . $sCity . ',
        Postal Code: ' . $sPostCode . ',
        Country: ' . $sCountry . ', 
        Phone: ' . $sPhone . '</p>';

if ($radioBlackListed) {
    $sx_mail_content .= '<p>Please notice that the IP of the applicant (' . sx_UserIP . ') is <b>Blacklisted</b>.<br>
        This might not neccassary mean that the application or the email address are invalid.<br>
        No mail has been sent to the applicant.</p>';
}

//== Confirmation queries for the administration
$confirmURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $iParticipantID . '&ac=' . $sApprovalToken;
$sx_mail_content .= '<p><a style="text-decoration: none;" href="' . $confirmURLpath . '">
    Click to approve the application.</a></p>';
$sx_mail_content .= '<p>A new mail will be sent to the applicant
    with a link to verify the email address and activate the registration.</p>';

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
