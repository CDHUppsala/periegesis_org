<?php
if ($radioUseForumRegistration === False) {
    header('Location: index.php');
    exit();
}

$sFirstName = "";
$sLastName = "";
$sForumEmail = "";
$sForumEmail_2 = "";
$sPhone = "";
$sAddress = "";
$sPostCode = "";
$sCity = "";
$sCountry = "";
$strAddToForum = "";
$strEmailList = "";

$radioContinue = false;
$arrError = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $radioContinue = True;

    $sxWhitelist = array(
        'FormToken',
        'FirstName',
        'LastName',
        'ForumEmail',
        'ForumEmail_2',
        'Password',
        'Password2',
        'Phone',
        'Address',
        'PostCode',
        'City',
        'Country',
        'captcha_input',
        'Action',
        'EmailList'
    );
    foreach ($_POST as $key => $item) {
        if (!in_array($key, $sxWhitelist)) {
            $radioContinue = false;
            write_To_Log("Forum Registration", "Wrong Whitelist Hack-Attempt!");
            echo "<h1>No way Home 1!</h1>";
            exit;
        }
    }

    $radioValidToken = true;
    if (empty($_POST['FormToken'])) {
        $radioValidToken = false;
        write_To_Log("Forum Registration", "Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("ForumRegistration", $_POST["FormToken"])) {
        $radioValidToken = false;
        write_To_Log("Forum Registration", "Wrong Token Hack-Attempt!");
    }

    /**
     * Continue only if token is valid
     */
    if ($radioValidToken == false) {
        //header('Location: /');
        echo '<h2>An Error Occurred</h2>';
        echo '<p>Please reload the page and try again.</p>';
        exit;
    }

    /**
     * Initial check of the email address
     */

    $sForumEmail = trim($_POST["ForumEmail"] ?? '');
    $sForumEmail_2 = trim($_POST["ForumEmail_2"] ?? '');

    // 1. Check if email addressis valide

    if (empty($sForumEmail) || !filter_var($sForumEmail, FILTER_VALIDATE_EMAIL)) {
        write_To_Log("Newsletter", "Empty or invalid Email Address - possible Hack-Attempt");
        echo '<h2>An Error Occurred</h2>';
        echo '<p>Please check your email address, reload the page and try again.</p>';
        exit;
    }

    // 2. Check the email against blacklists of disposable email domains

    if (is_email_domain_disposable($sForumEmail)) {
        write_To_Log("Newsletter", "Disposable email addresses: {$sForumEmail} Hack-Attempt ");
        echo '<h2>Access Denied</h2>';
        echo "<p>Disposable email addresses are not allowed.</p>";
        echo "<p>Please check your email domain, reload the page and try again.</p>";
        exit;
    }

    // 3. Check if emails are equal
    if ($sForumEmail != $sForumEmail_2) {
        $radioContinue = false;
        $arrError[] = lngWriteCorrectEmail;
    }

    /**
     * Check if email address has MX Record 
     * Check all 3 IPs
     * - the email domain's IP
     * - the Remote IP
     * - the Forworded IP, if any
     */

    /**
     * Prepare variables for the including file that checks email address and IPs
     * Include $s_ClientIP and $is_ClientIPBlackListed in registration (any account) for future checks
     * Define $s_ClientIP with Priority: 1. $domainIP, 2 $remoteIP (not use $forwardedIP)
     */
    $s_EmailToCheck = $sForumEmail;
    $s_SentFormName = 'ForumRegistration';
    $s_TimeSessionName = 'TimeForumRegistration';
    $s_ClientIP = NULL;
    $is_ClientIPBlackListed = 0;

    include PROJECT_PATH . "/sx_Security/include_check_email_ips.php";

    /**
     * Deal with Blacklisted IPs
     * ==============================================================================
     * Check if the IP is blacklisted. If true, 
     *   - don't send email to the applicant (to avoid sending mails that might be defined as spams),
     *   - send mail only to administrator that will approve the application,
     *     with information about blacklisting
     */

    $radio_SendMailToApplicant = true;
    if ($is_ClientIPBlackListed) {
        $radio_SendMailToApplicant = false;
    }

    /**
     * Get and check all other form inputs
     */

    if (!empty($_POST["FirstName"]) && !empty($_POST["LastName"])) {
        $sFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
        $sLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
        if (empty($sFirstName) || strlen($sFirstName) < 2 || empty($sLastName) || strlen($sLastName) < 2) {
            $radioContinue = False;
            $arrError[] = LNG_Form_AsteriskFieldsRequired;
        } elseif (strlen($sFirstName) > 45 || strlen($sLastName) > 45) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    } else {
        $radioContinue = False;
        $arrError[] = LNG_Form_AsteriskFieldsRequired;
    }


    if (!empty($_POST["Phone"])) {
        $sPhone = sx_GetSanitizedPhone($_POST["Phone"]);
        if (strlen($sPhone) > 12) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    }
    if (!empty($_POST["Address"])) {
        $sAddress = sx_Sanitize_Input_Text($_POST["Address"]);
        if (strlen($sAddress) > 45) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    }
    if (!empty($_POST["PostCode"])) {
        $sPostCode = sx_Sanitize_Input_Text($_POST["PostCode"]);
        if (strlen($sPostCode) > 9) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    }
    if (!empty($_POST["City"])) {
        $sCity = sx_Sanitize_Input_Text($_POST["City"]);
        if (strlen($sCity) > 45) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    }
    if (!empty($_POST["Country"])) {
        $sCountry = sx_Sanitize_Input_Text($_POST["Country"]);
        if (strlen($sCountry) > 45) {
            $arrError[] = LNG_Form_ExpectedLengthToLong;
            $radioContinue = False;
        }
    }

    $iEmailList = 0;
    if (isset($_POST["EmailList"]) && filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
        $iEmailList = 1;
    }

    /**
     * Check Captcha after getting Form values
     */
    if (sx_radio_UseForumRegistratioCaptcha) {
        if (!isset($_SESSION['captcha_code'])) {
            $radioContinue = false;
            $arrError[] = LNG__CaptchaError;
        } elseif (empty($_POST['captcha_input']) || ($_POST['captcha_input'] != $_SESSION['captcha_code'])) {
            $radioContinue = false;
            $arrError[] = LNG__CaptchaError;
        }
    }

    // Check if mail exists
    if ($radioContinue) {
        $sql = "SELECT RegisterDate, AllowAccess, AllowCode, ApprovalCode 
            FROM forum_members WHERE UserEmail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sForumEmail]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs) {
            $radioContinue = False;
            if ($rs['AllowAccess']) {
                $arrError[] = 'You have already an activated account in our database.';
            } elseif (!empty($rs['AllowCode']) && empty($rs['ApprovalCode'])) {
                $arrError[] = 'You have already applied for registration. Please check if you get an email from the administration of the site.';
            } elseif (return_Date_Difference($rs["RegisterDate"], date("Y-m-d")) < 5) {
                $arrError[] = 'You have already applied for registration. Please wait for an email from the administration of the site.';
            } else {
                $arrError[] = LNG__EmailExists;
            }
        }
        $stmt = null;
        $rs = null;
    }

    // Check passwords
    if ($radioContinue) {
        if (empty($_POST["Password"]) || empty($_POST["Password2"])) {
            $radioContinue = false;
            $arrError[] = lngPasswordCharacters;
        } else {
            $sPassword = trim($_POST["Password"]);
            $sPassword2 = trim($_POST["Password2"]);
            if ($sPassword != $sPassword2) {
                $radioContinue = false;
                $arrError[] = lngPasswordFieldsNotTheSame;
            } elseif (empty($sPassword) || strlen($sPassword) < 8 || strlen($sPassword) > 64) {
                $radioContinue = false;
                $arrError[] = lngPasswordCharacters;
            } else {
                $PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
            }
        }
    }

    if ($radioContinue) {
        $sAllowCode = return_Random_Alphanumeric(64);
        $sApprovalCode = return_Random_Alphanumeric(64);
        $sForumEmailListRemoveCode = return_Random_Alphanumeric(64);
        $radioAllowAccess = 0;

        $sql = "INSERT INTO forum_members (
			AllowAccess, FirstName, LastName, 
			UserAddress, UserPostCode, UserCity, UserCountry, UserPhone, 
			UserEmail, UserPassWord, AllowCode, ApprovalCode, 
			IPAddress, BlacklistedIP,
			EmailList, EmailListRemoveCode)
		VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $conn->beginTransaction();
        $stmt->execute([
            $radioAllowAccess,
            $sFirstName,
            $sLastName,
            $sAddress,
            $sPostCode,
            $sCity,
            $sCountry,
            $sPhone,
            $sForumEmail,
            $PW_Hash,
            $sAllowCode,
            $sApprovalCode,
            $s_ClientIP,
            $is_ClientIPBlackListed,
            $iEmailList,
            $sForumEmailListRemoveCode
        ]);
        $intUserID = $conn->lastInsertId();
        $conn->commit();

        /** 
         * SEND MAILS using the included file sx_mail_template.php
         * 		Check constant and global variables inlcuded in the mail template...
         * Variables to be defined here for the mail template:
         * 		$sx_send_to_email: The mail address of the reciever
         * 		$sx_mail_subject: The subject of mail
         * 		$sx_mail_content: whatever with HTML formation
         */

        /** Obs! CHANGES:
         * All registrations should be controlled by the administrator
         * If membership is controlled, send Email to the administrator
         */

        $radioUseAdministrationControl = true;

        $sx_mail_subject = lngSubscriptionInfor;

        if ($radio_SendMailToApplicant) {
            $sx_send_to_email = $sForumEmail;

            $sx_mail_content = '<p>' . LNG__EmailSentUponForumRequest . ' ' . str_SiteTitle . '.</p>';

            if ($radioUseAdministrationControl) {
                $sx_mail_content .= '<p>' . lngRegistrationByAdminControl . '</p>';
            } else {
                $confirmURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $intUserID . '&ac=' . $sAllowCode;
                $sx_mail_content .= '<p>' . lngToLoginGoTo . ' 
				<a style="text-decoration: none;" href="' . $confirmURLpath . '">' . lngClickHere . '</a>.</p>';
            }

            $sx_mail_content .= '<p>' . lngIfNotExpectedNeglectThisEmail . '</p>';

            require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
        }


        if ($radioUseAdministrationControl) {
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
             */

            $sx_send_to_email = str_SiteEmail;

            $sx_mail_content = '<h4>Approve Membership to website forum</h4>';
            $sx_mail_content .= "<p>{$sFirstName} {$sLastName}, {$sForumEmail}";
            $sx_mail_content .= "<br><b>Optional infomation:</b> {$sAddress}, {$sCity}, {$sCountry},  {$sPhone}</p>";
            if ($is_ClientIPBlackListed) {
                $strIPs = '';
                if ($isForwardedIP_blacklisted) {
                    $strIPs .= "Client IP: {$forwardedIP}";
                }
                if ($isRemoteIP_Blacklisted) {
                    $strIPs .= " Remote IP: {$remoteIP}";
                }
                $sx_mail_content .= "<p><strong>Warning</strong>: Please note that the applicant's IP Address is Blacklisted ({$strIPs}).
                    However, this does not mean that the above email address is invalid. 
                    Check the name and the address and approve the application, if it seems serious.</p>";
                $sx_mail_content .= "<p>See closely the case when multiple application of this kind frequently follow each other.</p>";
            }

            $confirmAdminURLpath = sx_ROOT_HOST_PATH . "?pg=allow&aid=" . $intUserID . "&ac=" . $sApprovalCode;
            $sx_mail_content .= '<p><a href="' . $confirmAdminURLpath . '">Click here to Approve the Subscription.</a>.</p>';
            $sx_mail_content .= '<p>A new mail will be sent to the applicant with a link to be used to verify the email address and activate the subscription</p>';

            // Send the mail
            require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
        }

        unset($_SESSION['TimeForumRegistration']);

        //header("Location: " . sx_ROOT_HOST_PATH . "?pg=message&request=welcome");
        //exit();
    }
}

/**
 * Greate the token and its session here, on the server,
 *   and a time stamp to account requests intervals
 */

$_SESSION['TimeForumRegistration'] = date('Y-m-d H:i:s');
$str_FormToken = sx_generate_form_token('ForumRegistration', 64);
?>
<section>
    <h1><?= $strRegistrationTitle  ?></h1>
    <?php if (!empty($arrError)) { ?>
        <p class="bg_error"><?php echo implode("<br>", $arrError) ?></p>
    <?php
    } ?>
    <div class="text">
        <p><?= lngNoCapitalInfo ?></p>
    </div>
    <div class="formWrap">
        <form name="ForumRegistration" id="ForumRegistration" action="forum_login.php?pg=join" METHOD="POST">
            <input type="hidden" name="FormToken" value="<?php echo $str_FormToken ?>">
            <fieldset class="fieldsets_grid">
                <label>
                    <input TYPE="text" NAME="FirstName" VALUE="<?= $sFirstName ?>" placeholder="<?= lngName ?> *" MAXCHARS="30" required></label>
                <label>
                    <input TYPE="text" NAME="LastName" VALUE="<?= $sLastName ?>" placeholder="<?= LNG__LastName ?> *" MAXCHARS="30" required></label>
                <label>
                    <input TYPE="tel" NAME="Phone" VALUE="<?= $sPhone ?>" placeholder="<?= lngPhone ?> *" MAXCHARS="30" required></label>
                <label>
                    <input TYPE="text" NAME="PostCode" VALUE="<?= $sPostCode ?>" placeholder="<?= lngPostalCode ?>" MAXCHARS="10"></label>
                <label>
                    <input TYPE="text" NAME="City" VALUE="<?= $sCity ?>" placeholder="<?= LNG__City ?>" MAXCHARS="30"></label>
                <label>
                    <input TYPE="text" NAME="Country" VALUE="<?= $sCountry ?>" placeholder="<?= lngCountry ?>" MAXCHARS="30"></label>
                <label>
                    <input TYPE="text" NAME="Address" VALUE="<?= $sAddress ?>" placeholder="<?= lngAddress ?>" MAXCHARS="30"></label>
            </fieldset>
            <fieldset>
                <label>
                    <input TYPE="email" autocomplete="off" NAME="ForumEmail" placeholder="<?= LNG__Email ?>" VALUE="<?= $sForumEmail ?>" MAXCHARS="48" required> *</label>
                <label>
                    <input TYPE="email" autocomplete="off" NAME="ForumEmail_2" placeholder="<?= LNG__EmailRepeat ?>" VALUE="<?= $sForumEmail_2 ?>" MAXCHARS="48" required> *</label>
            </fieldset>
            <fieldset>
                <label><?= lngPassword ?>: </label>
                <input TYPE="password" autocomplete="new-password" NAME="Password" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
                <label><?= lngRepeatPassword ?>: </label>
                <input TYPE="password" autocomplete="new-password" NAME="Password2" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
                <div class="text_xsmall"><?= lngPasswordCharacters  ?></div>
            </fieldset>
            <fieldset>
                <p><input type="checkbox" name="EmailList" value="ON" checked> <span><?= lngAddEmailToList ?></span></p>
            </fieldset>
            <?php if (sx_radio_UseForumRegistratioCaptcha) { ?>
                <fieldset>
                    <?php include "../sxPlugins/captcha/include.php" ?>
                    <br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required /> *
                    <div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
                </fieldset>
            <?php } ?>
            <fieldset>
                <input class="float_right" TYPE="Submit" NAME="Action" VALUE="<?= lngJoin ?>">
                <div> * <?= lngRequiredInfo ?></div>
            </fieldset>
        </form>
    </div>
</section>
<section>
    <div class="text">
    <div class="text_max_width">
        <?= $memoRegistrationNote ?>
        </div>
    </div>
</section>