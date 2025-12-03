<?php

/**
 * Approve students application for registration in a course:
 * 		By the administrator only, through a link sent by email (see join_course_email.php)
 * As activation is made by the administrator, sent an information email to the student.
 */

function sx_getCourseTitle($cid)
{
	$conn = dbconn();
	$sql = "SELECT CourseTitle 
		FROM courses 
		WHERE CourseID = ?
			AND ShowInSite = 1 ";
	$stmtf = $conn->prepare($sql);
	$stmtf->execute([$cid]);
	$title = $stmtf->fetchColumn();
	if ($title) {
		return $title;
	} else {
		return null;
	}
}

$intStudentID = 0;
$intCourseID = 0;
$strRequestCode = "";
$radioContinue = true;

if (isset($_GET["aid"])) {
	$intStudentID = (int)($_GET["aid"]);
}
if (intval($intStudentID) == 0) {
	$radioContinue = False;
}

if (isset($_GET["acid"])) {
	$intCourseID = (int)($_GET["acid"]);
}
if (intval($intCourseID) == 0) {
	$radioContinue = False;
}

//Don't clean, just for compare
if (isset($_GET["ac"])) {
	$strRequestCode = $_GET["ac"];
}
if (strlen($strRequestCode) < 24) {
	$radioContinue = False;
}

if ($radioContinue) {
	$radioContinue = false;
	$strStudentEmail = "";

	$sql = "SELECT Email
		FROM course_students 
		WHERE AllowAccess = 1
			AND StudentID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intStudentID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioContinue = true;
		$strStudentEmail = $rs["Email"];
	}
	$stmt = null;
	$rs = null;
}

if ($radioContinue) {
	$CheckEmail = filter_var($strStudentEmail, FILTER_VALIDATE_EMAIL);
	if ($CheckEmail == false) {
		$radioContinue = false;
	}
}

if ($radioContinue) {
	$strApprovalCode = "";
	$sql = "SELECT ApprovalCode  
		FROM course_to_students 
		WHERE CourseID = ?
			AND StudentID = ?
			AND Cancelled = 0 ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intCourseID, $intStudentID]);
	$rs = $stmt->fetchColumn();
	if ($rs) {
		$strApprovalCode =  $rs;
	} else {
		$radioContinue = false;
	}
	$stmt = null;
	$rs = null;
}

if ($radioContinue) {
	if ($strApprovalCode != $strRequestCode) {
		//Approval Code not the same
		$radioContinue = false;
	}
}

if ($radioContinue) {
	$sql = "UPDATE course_to_students SET 
			Approved = 1, 
			ApprovalCode = NULL
		WHERE CourseID = ?
			AND StudentID = ?
			AND Cancelled = 0 ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intCourseID, $intStudentID]);

	/**
	 * SEND MAILS using the included file sx_mail_template.php
	 * 		Check constant and global variables inluded in the mail template...
	 * Variables to be defined here:
	 * 		$sx_send_to_email: The mail of the reciever
	 * 		$sx_mail_subject: The subject of mail
	 * 		$sx_mail_content: whatever with HTML formation
	 */

	$sx_mail_subject = "Application for a Course";
	$sx_send_to_email = $strStudentEmail;

	$sx_mail_content = '<p>We have sent this email because you
    applied for registration in a course offered at the ' . str_SiteTitle . '.</p>';

	$sx_mail_content .= '<h3>Your Application has been Approved</h3>';

	$courseURL = sx_LANGUAGE_PATH . "courses.php?courseid=" . $intCourseID;
	$sCourseTitle = sx_getCourseTitle($intCourseID);
	$loginURL = sx_ROOT_HOST_PATH . "?pg=login";

	$sx_mail_content .= '<p>You are now registered in the course: <a target="_blank" href="' . $courseURL . '">' . $sCourseTitle . '</a></p>';
	$sx_mail_content .= '<p>Log in for details: <a target="_blank" href="' . $loginURL . '">' . lngLogin . '</a></p>';

	require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
}
/**
 * Uncomment in real site
 */
echo "<script>window.close();</script>";
