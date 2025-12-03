<?php

/**
 * Use PHPMailer without composer
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$mail = new PHPMailer;
$mail->isSMTP(); 
$mail->SMTPDebug = 2; 
$mail->Host = "your_smtp_host"; 
$mail->Port = "your_smtp_port"; // typically 587 
$mail->SMTPSecure = 'tls'; // ssl is depracated
$mail->SMTPAuth = true;
$mail->Username = "your_mail_username";
$mail->Password = "your_mail_password";
$mail->setFrom("your_email", "your_name");
$mail->addAddress("send_to_email_address", "send_to_Name");
$mail->Subject = 'Any_subject_of_your_choice';
$mail->msgHTML("test body"); // remove if you do not want to send HTML email
$mail->AltBody = 'HTML not supported';
$mail->addAttachment('docs/brochure.pdf'); //Attachment, can be skipped

$mail->send();


/*
A few things to note here.

$mail->SMTPDebug is for displaying errors and can be skipped.
$mail->SMTPAuth can be commented out if authentication is not required.
*/










?>