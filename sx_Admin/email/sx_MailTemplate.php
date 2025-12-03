<?php

/**
 * The mail template defined as variable: $sx_mail_body
 * Includes variables that must be defined in every page for sending mail
 * Include this file after the validation of form inputs and the definition of variables
 */

$sx_mail_body = '
<!DOCTYPE html>
<html lng="'. $str_CurrentLanguage .'">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $str_SiteTitle . '</title>
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
			        <a style="text-decoration: none; color: #0B4CB8;" target="_blank" href="' . $str_HomePage . '">' . $str_SiteTitle . '</a>
                </h2>
                <h4>' . $sx_mail_subject . '</h4>
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
                            <img src="' . $str_LogoImageEmail . '" alt="' . $str_SiteTitle . '" width="140" height="auto" style="width:140px; height: auto; margin: 0 auto">
                        </td>
                        <td bgcolor="#eeeeee" valign="center" style="padding:0 10px;">' . $str_SiteInfo . '</td>
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
    'From' => $str_SiteTitle . ' <' . strip_tags($str_SiteEmail) . '>',
    'Reply-To' => $str_SiteTitle . ' <' . strip_tags($str_SiteEmail) . '>',
    'X-Mailer' => 'PHP/' . phpversion()
];

if (strpos(sx_ROOT_HOST, "localhost:") == 0 && SX_radioTestEnvironment == false) {
    ini_set("sendmail_from", $str_SiteEmail);
    mail($sx_send_to_email, $sx_mail_subject, $sx_mail_body, $headers, "-f " . $str_SiteEmail);
} else {
    echo "<h2>Local or Test Environment:</b> - No mail is sent.</h2>";
    /**
     * Use the next line only in development environment
     * Always comment or remove it in real production
     */
    echo "<p>" . $sx_send_to_email . "<br>" . $sx_mail_subject . "</p>" . $sx_mail_body;
}

/**
 * The Mail Template is hold in the variable: $sx_mail_body
 * Constant and global variables used in the template and by the mail sender: 
 * 		$str_SiteTitle
 * 		$str_SiteEmail
 * 		str_SiteInfo: Prepared footer information (site name, address, telephone, etc.)
 * 		lngSendingFromSite: A common information title for all mails
 * 		Link to logotype: sx_ROOT_HOST . '/images/' . $str_LogoImageEmail
 * 		Link to home page: sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php
 * Variables to be defined in every page:
 * 		$sx_send_to_email: The mail of the reciever
 * 		$sx_mail_subject: The subject of mail
 * 		$sx_mail_content: whatever
 * Include this file in every page for sending mail, 
 * under the definition of the above variables
 */
