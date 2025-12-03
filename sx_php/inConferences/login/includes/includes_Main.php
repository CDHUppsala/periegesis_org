<?php
/**
 * The include bellow checks login sessions
 */
include dirname(dirname(__DIR__)) ."/check_sessions.php";
include dirname(__DIR__) ."/basic_queries.php";

$strPG = "";
if (isset($_GET["pg"])) {
	$strPG = $_GET["pg"];
}

if ($strPG == "message") {
    include dirname(__DIR__) ."/messages.php";
} elseif ($strPG == "leavemail") {
    include dirname(__DIR__) ."/leaveMail.php";
} elseif ($strPG == "allow") {
    include dirname(__DIR__) ."/allowAccess.php";
} elseif ($strPG == "logout") {
    include dirname(__DIR__) ."/logout.php";
} elseif ($strPG == "edit") {
    include dirname(__DIR__) ."/edit.php";
} elseif ($strPG == "leave") {
    include dirname(__DIR__) ."/leave.php";
} elseif ($strPG == "forgot") {
    include dirname(__DIR__) ."/password_send_mail.php";
} elseif ($strPG == "reset") {
    include dirname(__DIR__) ."/password_reset.php";
} elseif ($strPG == "media" && $radio_ToUploadMedia) {
    include dirname(__DIR__) ."/upload//media_form.php";
} elseif ($strPG == "docs" && $radio_ToUploadDocuments) {
    include dirname(__DIR__) ."/upload/documments.php";
} elseif ($strPG == "images" && $radio_ToUploadImages) {
    include dirname(__DIR__) ."/upload/images.php";
} elseif ($strPG == "portrait" && $radio_AllowAddProfile) {
    include dirname(__DIR__) ."/upload/portrait.php";
} elseif ($strPG == "abstract" && $radio_RightsToSendAbstracts) {
    include dirname(__DIR__) ."/upload/abstract.php";
} elseif ($strPG == "conference") {
    include dirname(__DIR__) ."/join_conference.php";
} elseif ($strPG == "join") {
    if($radio_LoggedParticipant && (int) $int_ParticipantID > 0) {
        include dirname(__DIR__) ."/join_conference.php";
    }else{
        include dirname(__DIR__) ."/join.php";
    }
} else {
    if($radio_LoggedParticipant && (int) $int_ParticipantID > 0) {
        header('Location: conferences_login.php?pg=message&loged=yes');
        exit();
    }else{
        include dirname(__DIR__) ."/login.php";
    }
}