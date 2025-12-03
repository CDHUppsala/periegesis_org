<?php

/**
 * The mail template defined as variable: $sx_mail_body
 * Includes variables that must be defined in every page for sending mail
 * Include this file after the validation of form inputs and the definition of variables
 */

$sx_mail_body = '
<!DOCTYPE html>
<html lng="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . str_SiteTitle . '</title>
    <style type="text/css">
        @media only screen and (max-width: 640px) {
            table {
                width: 460px !important;
            }
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="600" style="font-family: Verdana, Georgia, Times New Roman; font-size: 13pt; color: #444444">
        <tr>
            <td bgcolor="#eeeeee" align="left" valign="top" style="padding:20px;">
                <h2 style="font-weight:normal">
			        <span style="font-size:11pt">' . LNG_Mail_SendingFromSite . '</span>
			        <br><a style="text-decoration: none; color: #0B4CB8;" target="_blank" href="' . sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php">' . str_SiteTitle . '</a>
                </h2>
                <h3>' . $sx_mail_subject . '</h3>
             </td>
        </tr>
        <tr>
            <td valign="top" style="padding:20px;">' . $sx_mail_content . '</td>
        </tr>
        <tr>
            <td align="left" valign="top">
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Verdana, Georgia, Times New Roman; font-size: 11pt;">
                    <tr>
                        <td width="140" valign="top">
                            <a target="_blank" href="' . sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php">
                            <img src="' . sx_ROOT_HOST . '/images/' . $str_LogoImageEmail . '" alt="' . str_SiteTitle . '" width="140" height="auto" style="width:140px; height: auto; margin: 0 auto">
                            </a>
                        </td>
                        <td bgcolor="#eeeeee" valign="center" style="padding:0 10px;">' . str_SiteInfo . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>';

/**
 * The Mail sender
 */

$headers = [
    'MIME-Version' => '1.0',
    'Content-type' => 'text/html; charset=UTF-8',
    'From' => str_SiteTitle . ' <' . strip_tags(str_SiteEmail) . '>',
    'Reply-To' => str_SiteTitle . ' <' . strip_tags(str_SiteEmail) . '>',
    'X-Mailer' => 'PHP/' . phpversion()
];

if (isset($radioCopyToAdmin) && $radioCopyToAdmin) {
    $headers['Bcc'] = strip_tags(str_SiteEmail);
}

if (strpos(sx_ROOT_HOST, "localhost:") == 0 && SX_radioTestEnvironment == false) {
    ini_set("sendmail_from", str_SiteEmail);
    mail($sx_send_to_email, $sx_mail_subject, $sx_mail_body, $headers, "-f " . str_SiteEmail);
} else {
    echo "<p><b>Local Environment:</b> - No mail is sent.</p>";
    /**
     * Use the next line only in development environment
     * Always comment or remove it in real production
     */
    echo "<p>" . $sx_send_to_email . "<br>" . $sx_mail_subject . "</p>" . $sx_mail_body;
}

/**
 * The Mail Template is hold in the variable: $sx_mail_body
 * Constant and global variables used in the template and by the mail sender: 
 * 		str_SiteTitle
 * 		str_SiteEmail
 * 		str_SiteInfo: Prepared footer information (site name, address, telephone, etc.)
 * 		LNG_Mail_SendingFromSite: A common information title for all mails
 * 		Link to logotype: sx_ROOT_HOST . '/images/' . $str_LogoImageEmail
 * 		Link to home page: sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php
 * Variables to be defined in every page:
 * 		$sx_send_to_email: The mail of the reciever
 * 		$sx_mail_subject: The subject of mail
 * 		$sx_mail_content: whatever
 * Include this file in every page for sending mail, 
 * under the definition of the above variables
 */

 /*
$email_to = "you@yourdomain.com";
$name = $_POST["name"];
$email_from = "sending_address@yourdomain.com";
$reply_to = $_POST["email"];
$message = $_POST["message"];
$email_subject = "Feedback from website";
$headers = "From: " . $email_from . "\n";
$headers .= "Reply-To: " . $reply_to . "\n";
The code above sets the email address you will send your email to and gets the users' name, email address, and message from the previous form. It also sets the variables that contain the email subject, and headers to specify the from and reply to email addresses. The $email_from variable must be a mailbox hosted on the Fasthosts email servers.

Now lets build the message of the email with the users' name and comments.

$message = "Name: ". $name . "\r\nMessage: " . $message;
Finally, let's send the email. If the email is sent we will go to a thank you page, if there is an error we will display a brief message.

ini_set("sendmail_from", $email_from);
$sent = mail($email_to, $email_subject, $message, $headers, "-f" .$email_from);
if ($sent)
{
header("Location: https://www.yourdomain.com/thankyou.html");
} else {
echo "There has been an error sending your comments. Please try later.";
}
*/
