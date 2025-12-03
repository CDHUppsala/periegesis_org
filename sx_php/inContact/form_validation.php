<?php

$strSenderName = "";
$strFrom = "";
$intRecipient = 0;
$strPhone = "";
$strMailTitle = "";
$strSenderMessage = "";

$radioValidCaptcha = false;
$radioSent = false;
$radioSentError = false;

if (!empty($_POST["senderLastName"])) {
    header("Status: 301 Moved Permanently");
    header("Location: contact.php?sent=yes");
    exit;
}

if (isset($_GET['sent'])) {
    if ($_GET['sent'] == "yes") {
        $radioSent = true;
    } else {
        $radioSentError = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /**
     * You might not use captcha when Form Token is also used
     * Activate it only to prevent brute force attacks
     */
    if (sx_radio_UseContactCaptcha) {
        if (isset($_POST['captcha_input']) && $_POST['captcha_input'] == $_SESSION['captcha_code']) {
            $radioValidCaptcha = true;
        }
    } else {
        $radioValidCaptcha = true;
    }

    if ($radioValidCaptcha) {
        $radioSent = true;
        if (!isset($_POST['FormToken']) || empty($_POST['FormToken'])) {
            $radioSent = false;
            write_To_Log("Contact: Empty Token Hack-Attempt!");
        } elseif (!sx_valid_form_token("EmailForm", $_POST["FormToken"])) {
            $radioSent = false;
            write_To_Log("Contact: Wrong Token Hack-Attempt!");
        }
    }

    /**
     * Get the form inputs even if radioSent = false;
     * Do not validate here the email address and the message
     */
    if (!empty($_POST['SenderName'])) {
        $strSenderName = sx_Sanitize_Input_Text($_POST['SenderName']);
    }
    if (!empty($_POST['SenderEmail'])) {
        $strFrom = trim($_POST['SenderEmail']);
    }
    if (!empty($_POST['Recipient'])) {
        $intRecipient = (int) $_POST['Recipient'];
    }

    if (!empty($_POST['SenderPhone'])) {
        $strPhone = sx_GetSanitizedPhone($_POST["SenderPhone"]);
    }
    if (!empty($_POST['Title'])) {
        $strMailTitle = sx_Sanitize_Input_Text($_POST['Title']);
    }
    if (!empty($_POST['Message'])) {
        $strSenderMessage = $_POST['Message'];
    }

    /**
     * If radioSent = true Validate the email address and the message
     */
    if ($radioSent) {
        $str__SenderMessage = '';
        if (!empty($strSenderMessage)) {
            $str__SenderMessage = sx_Sanitize_Text_Area_Rows($strSenderMessage);
        }
        if (strlen($str__SenderMessage) > floor($i_MaxEmailLength + 100)) {
            $str__SenderMessage = substr($str__SenderMessage, 0, $i_MaxEmailLength + 100);
        }
        if (strlen($str__SenderMessage) > 10) {
            $str__SenderMessage = sx_ParagraphBreaks($str__SenderMessage);
        } else {
            $radioSent = false;
        }

        if (filter_var($strFrom, FILTER_VALIDATE_EMAIL) === False) {
            $strFrom = "";
            $radioSent = false;
        }

        if (strlen($strSenderName) < 6) {
            $radioSent = false;
        }

        if (intval($intRecipient) == 0) {
            $strTo = $str_SiteEmail;
        } else {
            $strTo = return_Field_Value_From_Table("site_setup", "SiteEmail", "SiteID", $intRecipient);
            $checkEmail = filter_var($strTo, FILTER_VALIDATE_EMAIL);
            if ($checkEmail === false) {
                $radioSent = false;
            }
        }
    }

    if ($radioSent) {
        if (empty($strMailTitle)) {
            $strMailTitle = lngContact;
        }

        $Header = "<div style='font-family: Verdana, Arial; font-size: 18px'>";
        $Header = $Header . "<p><b>" . lngContact . "</b><br>";
        $Header = $Header . LNG_Mail_SendingFromSite . " " . str_SiteTitle . ": " . sx_LANGUAGE_PATH . "</p><hr>";
        $Footer = "</div>";

        //The content of the mail
        $sxBody = $Header;
        $sxBody = $sxBody . "<p><b>" . lngSender . ":</b> " . $strSenderName . "<br>";
        $sxBody = $sxBody . "<b>" . LNG__Email . ":</b> " . $strFrom . "<br>";
        $sxBody = $sxBody . "<b>" . lngPhone . ":</b> " . $strPhone . "<br>";
        $sxBody = $sxBody . "<b>" . lngSubject . ":</b> " . $strMailTitle . "</p>";
        $sxBody = $sxBody . "<p><b>" . lngAttachedMsg . ":</b></p>";
        $sxBody = $sxBody . $str__SenderMessage;
        $sxBody = $sxBody . $Footer;

        $headers = [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=UTF-8',
            'From' => str_SiteTitle . ' <' . strip_tags(str_SiteEmail) . '>',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        if ($radio_UseEmail && strpos(sx_ROOT_HOST, "localhost:") == 0) {
            ini_set("sendmail_from", str_SiteEmail);
            if (mail($strTo, lngContact, $sxBody, $headers, "-f " . str_SiteEmail)) {
                header("Location: contact.php?sent=yes");
                exit;
            } else {
                header("Location: contact.php?sent=no");
                exit;
            }
        } else {
            header("Location: contact.php?sent=local");
            exit;
            /*
            echo "<b>Local Environment:</b> ". lngContact ."<hr>". $sxBody;
            */
        }
    }
}
