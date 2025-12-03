<?php

/**
 * SEND MAILS using the included file sx_mail_template.php
 * 		Check constant and global variables inluded in the mail template...
 * Variables to be defined here:
 * 		$sx_send_to_email: The mail of the reciever
 * 		$sx_mail_subject: The subject of mail
 * 		$sx_mail_content: whatever with HTML formation
 */

$sx_mail_subject = lngSubjectParticipation;
$sx_send_to_email = $strEmail;

$strLinkPage = sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/events.php?eid=' . $iEventID;
$sx_mail_content = '<h4>Thank you for your registration!</h4>';
$sx_mail_content .= '<p>You have registered to participate in the following event:</p>';
$sx_mail_content .= '<p><strong><a style="text-decoration: none; color: #0B4CB8;" 
    target="_blank" href="' . $strLinkPage . '">' . $sEventTitle . '</a>.</strong></p>';

$sx_mail_content .= '<p>';
if (!empty($str_DatePeriod)) {
    $sx_mail_content .= '<b>' . lngDate . ':</b> ' . $str_DatePeriod . '<br>';
}
if (!empty($str_EmailPlaceTime)) {
    $sx_mail_content .= $str_EmailPlaceTime . '<br>';
}
$str__Mode = $strMode;
if ($strMode == "Live") {
    if(empty($sPlaceCity)) {
        $sPlaceCity = "Athens";
    }
    $str__Mode = "In-person in ". $sPlaceCity;
}
$sx_mail_content .= '<b>' . lngParticipationMode . ':</b> ' . $str__Mode;
$sx_mail_content .= '</p>';

if (!empty($strOnlineParticipationLink) && $strMode == "Online") {
    $strLink = '<a style="text-decoration: none; color: #0B4CB8;" target="_blank" 
        href="' . $strOnlineParticipationLink . '">Link to access the event</a>';
    $sx_mail_content .= '<p>To <strong>access</strong> the event please use the following link, 
        which will open about 10 minutes before the start of the event: ' . $strLink . '</p>';
}

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
