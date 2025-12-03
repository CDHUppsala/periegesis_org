<?php
if ($radioAllowOnlineRegistration == False) {
    header('Location: index.php');
    exit();
}

$sFirstName = "";
$sLastName = "";
$sEmail = "";
$sEmail_2 = "";
$sPhone = "";
$sAddress = "";
$sPostCode = "";
$sCity = "";
$sCountry = "";
$strEmailList = "";
$PW_Hash = "";

$radioContinue = false;
$arrError = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $radioContinue = True;

    $sxWhitelist = array(
        'FormToken',
        'FirstName',
        'LastName',
        'Email',
        'Email_2',
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
            break;
        }
    }
    if ($radioContinue == false) {
        write_To_Log("Users Registration: Wrong Whitelist Hack-Attempt!");
        header('Location: index.php');
        exit;
    }

    $radioValidToken = true;
    if (empty($_POST['FormToken'])) {
        $radioValidToken = false;
        write_To_Log("Users Registration: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("UsersRegistration", $_POST["FormToken"])) {
        $radioValidToken = false;
        write_To_Log("Users Registration: Wrong Token Hack-Attempt!");
    }

    /**
     * Continue only if token is valid
     */
    if ($radioValidToken == false) {
        header('Location: index.php');
        exit;
    }

    /**
     * Get and chaek all form inputs
     */

    if (!empty($_POST["FirstName"])) {
        $sFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
    }
    if (!empty($_POST["LastName"])) {
        $sLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
    }
    if (empty($sFirstName) || strlen($sFirstName) < 2 || empty($sLastName) || strlen($sLastName) < 2) {
        $radioContinue = false;
        $arrError[] = LNG_Form_AsteriskFieldsRequired;
    } elseif (strlen($sFirstName) > 45 || strlen($sLastName) > 45) {
        $radioContinue = false;
        $arrError[] = LNG_Form_ExpectedLengthToLong;
    }

    $CheckEmail = false;
    if (!empty($_POST["Email"])) {
        $sEmail = $_POST["Email"];
        $CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
    }
    if (!empty($_POST["Email_2"])) {
        $sEmail_2 = $_POST["Email_2"];
    }

    if ($CheckEmail == false || strlen($sEmail) < 8 || $sEmail != $sEmail_2) {
        $radioContinue = False;
        $arrError[] = lngWriteCorrectEmail;
    } elseif (sx_has_email_domain_mx($sEmail) === false) {
        $radioContinue = False;
        $arrError[] = lngWriteCorrectEmail;
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
    if (!empty($_POST["EmailList"])) {
        if (filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
            $iEmailList = 1;
        }
    }

    /**
     * Check Captcha after getting Form values
     */
    if (sx_radio_UseUserRegistratioCaptcha) {
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
        $sql = "SELECT UserID FROM users WHERE UserEmail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sEmail]);
        $rs = $stmt->fetch(PDO::FETCH_NUM);
        if (($rs)) {
            $radioContinue = False;
            $arrError[] = LNG__EmailExists;
        }
        $stmt = null;
        $rs = null;
    }

    /** Deal with SPAMS
     * ==============================================================================
     * Check if the IP is blacklisted. If true, 
     *   - don't send email to the applicant (to avoid sending mails that might be defined as spams),
     *   - send always mail only to administrator, independently of control form,
     *     including information about blacklisting,
     *     so it is up to administration to approve the application,
     */

    $radio_SendMailToApplicant = true;
    $radioBlackListed = false;
    $radioBlackListedIP = 0;

    if ($radioContinue) {

        $radioBlackListed = sx_is_ip_blacklisted(sx_UserIP);
        if ($radioBlackListed) {
            $radio_SendMailToApplicant = false;
            $radioBlackListedIP = 1;
        }

        /**
         * Compare Form Creation time with Post Request (Current) time
         * If difference is less than 30 seconds, redirect
         */

        $radioRedirect = false;
        if ($radioBlackListed) {
            if (isset($_SESSION['FormCreationTime'])) {
                $dFormTime = $_SESSION['FormCreationTime'];
                if (return_Is_Date($dFormTime, 'Y-m-d H:i:s')) {
                    $seconds_passing = return_Date_Time_Total_Difference($dFormTime, date('Y-m-d H:i:s'), 'seconds');
                    if ((int) $seconds_passing < 30) {
                        $radioRedirect = true;
                    }
                } else {
                    $radioRedirect = true;
                }
            } else {
                $radioRedirect = true;
            }
        }
        if ($radioRedirect) {
            write_To_Log("User Registration: Repeated blacklisted IP!");
            sleep(5);
            header('Location: index.php');
            exit;
        }
    }

    // Check passwords
    if ($radioContinue) {
        if (empty($_POST["Password"]) || empty($_POST["Password2"])) {
            $radioContinue = false;
            $arrError[] = lngPasswordCharacters;
        } else {
            $sPassword = trim($_POST["Password"]);
            if ($sPassword != trim($_POST["Password2"])) {
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
        $sEmailListRemoveCode = return_Random_Alphanumeric(64);
        $radioAllowAccess = 0;

        $sql = "INSERT INTO users (
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
            $sEmail,
            $PW_Hash,
            $sAllowCode,
            $sApprovalCode,
            sx_UserIP,
            $radioBlackListedIP,
            $iEmailList,
            $sEmailListRemoveCode
        ]);
        $intUserID = $conn->lastInsertId();
        $conn->commit();


        /**
         * SEND MAILS using the included file sx_mail_template.php
         * 		Check constant and global variables inluded in the mail template...
         * Variables to be defined here:
         * 		$sx_send_to_email: The mail of the reciever
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
            $sx_send_to_email = $sEmail;

            $sx_mail_content = '<h4>' . LNG__EmailSentUponRequest . '.</h4>';
            if ($radioUseAdministrationControl) {
                $sx_mail_content .= '<p>' . lngRegistrationByAdminControl . '</p>';
            } else {
                $confirmURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $intUserID . '&ac=' . $sAllowCode;
                $sx_mail_content .= '<p>' . lngToLoginGoTo . ' <a href="' . $confirmURLpath . '">' . lngClickHere . '</a>.</p>';
            }

            require dirname(__DIR__) . "/sx_Mail/sx_mail_template.php";
        }

        /**
         * If membership is controlled or IP is blacklisted, send Email to the administrator
         */
        if ($radioUseAdministrationControl || $radioBlackListed) {
            $sx_send_to_email = str_SiteEmail;

            $sx_mail_content = '<h4>The following visitor applied for registration as member of the website:</h4>';

            $sx_mail_content .= '<p>' . $sFirstName . ' ' . $sLastName . ', ' . $sEmail . '
			<br><b>Optional infomation:</b> ' . $sAddress . ', ' . $sCity . ', ' . $sCountry . ', ' . $sPhone . '</p>';

            if ($radioBlackListed) {
                $sx_mail_content .= '<p>Please notice that the IP (' . sx_UserIP . ') of the applicant with User ID (' . $intUserID . ') is <b>blacklisted</b>.<br>
					This might not neccassary mean that the application or the email address are invalid.</p>';
            }

            $confirmAdminURLpath = sx_ROOT_HOST_PATH . '?pg=allow&aid=' . $intUserID . '&ac=' . $sApprovalCode;
            $sx_mail_content .= '<p><a style="text-decoration: none;" href="' . $confirmAdminURLpath . '">
				Click here to Approve the Subscription Application</a>.</p>';
            $sx_mail_content .= '<p>A new mail will be sent to the applicant with a link to verify the email address and activate the subscription</p>';

            require dirname(__DIR__) . "/sx_Mail/sx_mail_template.php";
        }

        unset($_SESSION['FormCreationTime']);

        if ($radioUseAdministrationControl) {
            header("Location: " . sx_ROOT_HOST_PATH . "?pg=message&welcome=ac");
            exit();
        } else {
            header("Location: " . sx_ROOT_HOST_PATH . "?pg=message&welcome=mc");
            exit();
        }
    }
}

/**
 * Greate the token and its session here, on the server,
 *   and a time stamp to account requests intervals
 *   don't uppdate time stamp on error
 */

if (empty($arrError)) {
    $_SESSION['FormCreationTime'] = date('Y-m-d H:i:s');
}
$str_FormToken = sx_generate_form_token('UsersRegistration', 128);

?>
<section>
    <h1><?= $strUsersRegistrationTitle  ?></h1>
    <?php if (!empty($arrError)) { ?>
        <p class="bg_error"><?php echo implode("<br>", $arrError) ?></p>
    <?php
    } ?>
    <div class="text">
        <p><?= lngNoCapitalInfo ?></p>
    </div>
    <div class="formWrap">
        <form name="MemberRegistration" id="MemberRegistration" action="login.php?pg=join" METHOD="POST">
            <input type="hidden" name="FormToken" value="<?php echo $str_FormToken ?>">
            <fieldset>
                <label><?= lngName ?>:</label>
                <input TYPE="text" NAME="FirstName" VALUE="<?= $sFirstName ?>" MAXCHARS="30" required> *
                <label><?= LNG__LastName ?>:</label>
                <input TYPE="text" NAME="LastName" VALUE="<?= $sLastName ?>" MAXCHARS="30" required> *
                <label><?= LNG__Email ?>:</label>
                <input TYPE="email" autocomplete="off" NAME="Email" VALUE="<?= $sEmail ?>" MAXCHARS="48" required> *
                <label><?= LNG__EmailRepeat ?>:</label>
                <input TYPE="email" autocomplete="off" NAME="Email_2" VALUE="<?= $sEmail_2 ?>" MAXCHARS="48" required> *
            </fieldset>
            <fieldset>
                <label><?= lngPassword ?>:</label>
                <input TYPE="password" autocomplete="new-password" NAME="Password" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
                <label><?= lngRepeatPassword ?>:</label>
                <input TYPE="password" autocomplete="new-password" NAME="Password2" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
                <p class="text_small"><?= lngPasswordCharacters  ?></p>
            </fieldset>
            <fieldset>
                <label><?= lngPhone ?>:</label>
                <input TYPE="tel" NAME="Phone" VALUE="<?= $sPhone ?>" MAXCHARS="30">
                <label><?= lngAddress ?>:</label>
                <input TYPE="text" NAME="Address" VALUE="<?= $sAddress ?>" MAXCHARS="30">
                <label><?= lngPostalCode ?>:</label>
                <input TYPE="text" NAME="PostCode" VALUE="<?= $sPostCode ?>" MAXCHARS="10">
                <label><?= LNG__City ?>:</label>
                <input TYPE="text" NAME="City" VALUE="<?= $sCity ?>" MAXCHARS="20">
                <label><?= lngCountry ?>:</label>
                <input TYPE="text" NAME="Country" VALUE="<?= $sCountry ?>" MAXCHARS="20">
            </fieldset>
            <?php if (sx_radio_UseUserRegistratioCaptcha) { ?>
                <fieldset>
                    <?php include "../sxPlugins/captcha/include.php" ?>
                    <br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required /> *
                    <div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
                </fieldset>
            <?php } ?>
            <fieldset>
                <input class="float_right" TYPE="Submit" NAME="Action" VALUE="<?= lngJoin ?>">
                <p><input type="checkbox" name="EmailList" value="ON" checked> <span><?= lngAddEmailToList ?></span></p>
                <div> * <?= lngRequiredInfo ?></div>
            </fieldset>
        </form>
    </div>
</section>
<section>
    <div class="text">
        <div class="text_max_width">
            <?= $memoUsersRegistrationNote ?>
        </div>
    </div>
</section>