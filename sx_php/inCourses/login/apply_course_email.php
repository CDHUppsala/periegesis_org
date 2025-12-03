<?php

/**
 * SEND MAILS using the included file sx_mail_template.php
 * 		Check constant and global variables inluded in the mail template...
 * Variables to be defined here:
 * 		$sx_send_to_email: The mail of the reciever
 * 		$sx_mail_subject: The subject of mail
 * 		$sx_mail_content: whatever with HTML formation
 */

$sx_mail_subject = "Application for a Course";

/**
 * Email to the Applicant
 */

$sx_send_to_email = $_SESSION["Students_Email"];

$sx_mail_content = '<p>We have sent this email because you
    applied for registration in a course offered at the ' . str_SiteTitle . '.</p>';

$sx_mail_content .= '<p>Your application will now be processed. Once approved, you will 
    receive a new email with further information about your registration.</p>';

$courseURL = sx_LANGUAGE_PATH . "courses.php?courseid=" . $intCourseID;
$sx_mail_content .= '<p>Please click the link for details about the course:
    <a target="_blank" href="' . $courseURL . '">' . $str__CourseTitle . '</a></p>';


$loginURL = sx_ROOT_HOST_PATH . "?pg=login";
$sx_mail_content .= '<p>You can also log in to follow the status of your application:
    <a target="_blank" href="' . $loginURL . '">' . lngLogin . '</a></p>';

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";

/**
 * Email to Administration
 */

$sx_send_to_email = str_SiteTitle;

$strRole = 'Student';
if ($radioTeacher) {
    $strRole = 'Teacher';
}

$sTitle = return_Field_Value_From_Table("course_students","Title","StudentID",$intStudentID);
$sInstitution = return_Field_Value_From_Table("course_students","Institution","StudentID",$intStudentID);

$sx_mail_content = '<p>You are asked to approve the following application for registration in a course:
    <br><strong>Course:</strong> <a target="_blank" href="' . $courseURL . '">' . $str__CourseTitle . '</a>
    <br><strong>Role:</strong> ' . $strRole . '
    <br><strong>Name:</strong> ' . $_SESSION["Students_FirstName"] . ' ' . $_SESSION["Students_LastName"];
if (!empty($sTitle)) {
    $sx_mail_content .= '<br><strong>Title:</strong> ' . $sTitle;
}
if (!empty($sInstitution)) {
    $sx_mail_content .= '<br><strong>Institution:</strong> ' . $sInstitution;
}
$sx_mail_content .= '<br><strong>Email:</strong> ' . $_SESSION["Students_Email"];
$sx_mail_content .= '</p>';

$confirmAdminURL = sx_ROOT_HOST_PATH . "?pg=approve&aid=" . $intStudentID . "&acid=" . $intCourseID . "&ac=" . $sApprovalCode;
$sx_mail_content .= '<p>To approve the application, please click on the following link: 
    <a target="_blank" href="' . $confirmAdminURL . '">Approve the Application</a></p>';

require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
