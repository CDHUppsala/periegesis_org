<?php
if ($radioAllowOnlineRegistration == False) {
	header('Location: index.php');
	exit();
}

/**
 * Empty form variables
 */

$sFirstName = "";
$sLastName = "";
$sEmail = "";
$sPhone = "";
$sAddress = "";
$sPostCode = "";
$sCity = "";
$sCountry = "";
$mBiography = "";
$strEmailList = "";

$arrError = array();
$radioContinue = false;
$radioBlackListed = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$radioContinue = True;

	$sxWhitelist = array(
		'JoinAction',
		'EmailList',
		'captcha_input',
		'Password2',
		'Password',
		'Email',
		'entered',
		'Biography',
		'Country',
		'City',
		'PostCode',
		'Address',
		'Phone',
		'LastName',
		'FirstName',
		'FormToken',
		'subscription'
	);
	foreach ($_POST as $key => $value) {
		if (!in_array($key, $sxWhitelist)) {
			$radioContinue = false;
			break;
		}
	}

	if ($radioContinue == false) {
		write_To_Log("Join Conference Site", "Wrong Whitelist Hack-Attempt!");
		header('Location: index.php');
		exit;
	}

	$radioValidToken = true;
	if (!isset($_POST['FormToken'])) {
		$radioValidToken = false;
		write_To_Log("Join Conference Site", "Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("ConferenceSiteRegistration", $_POST["FormToken"])) {
		$radioValidToken = false;
		write_To_Log("Join Conference Site", "Wrong Token Hack-Attempt!");
	}
	if ($radioValidToken == false) {
		header('Location: index.php');
		exit;
	}

	/**
	 * Compare Form Creation time with Post Request (Current) time
	 * If difference is less than 30 seconds, redirect
	 */

	$radioRedirect = false;
	if (isset($_SESSION['FormCreationTime'])) {
		$dFormTime = $_SESSION['FormCreationTime'];
		if (return_Is_Date($dFormTime, 'Y-m-d H:i:s')) {
			$seconds_passing = return_Date_Time_Total_Difference($dFormTime, date('Y-m-d H:i:s'), 'seconds');
			if ((int) $seconds_passing < 20) {
				$radioRedirect = true;
			}
		} else {
			$radioRedirect = true;
		}
	} else {
		$radioRedirect = true;
	}
	if ($radioRedirect) {
		write_To_Log("Join Conference Site", "Request with short Time interval!");
		sleep(5);
		header('Location: index.php');
		exit;
	}

	/**
	 * ================================================
	 *   just contunue to get and check input values
	 * ================================================
	 */
	$radioContinue = True;
	if (!empty($_POST["FirstName"])) {
		$sFirstName = sx_Sanitize_Input_Text(trim($_POST["FirstName"]));
	}
	if (!empty($_POST["LastName"])) {
		$sLastName = sx_Sanitize_Input_Text(trim($_POST["LastName"]));
	}
	if (empty($sFirstName) || empty($sLastName)) {
		$radioContinue = false;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}
	if ($radioContinue) {
		if (strlen($sFirstName) < 2 || strlen($sLastName) < 2) {
			$radioContinue = false;
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
		}
	}
	if ($radioContinue) {
		if ((strlen($sFirstName) > 40 || strlen($sLastName) > 40)) {
			$radioContinue = false;
			$arrError[] = LNG_Form_ExpectedLengthToLong;
		}
	}

	if (isset($_POST["Email"])) {
		$sEmail = trim($_POST["Email"]);
	}
	if (empty($sEmail) || strlen($sEmail) < 8) {
		$radioContinue = False;
		$arrError[] = lngWriteCorrectEmail;
	} else {
		$CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
		if ($CheckEmail == False) {
			$radioContinue = false;
			$arrError[] = lngWriteCorrectEmail;
		} elseif (sx_has_email_domain_mx($sEmail) === false) {
			$radioContinue = false;
			$arrError[] = lngWriteCorrectEmail;
		}
	}

	if (isset($_POST["Phone"])) {
		$sPhone = sx_GetSanitizedPhone($_POST["Phone"]);
	}
	if (!empty($sPhone) && strlen($sPhone) > 12) {
		$radioContinue = false;
		$arrError[] = LNG_Form_ExpectedLengthToLong;
	}

	if (isset($_POST["Address"])) {
		$sAddress = sx_Sanitize_Input_Text($_POST["Address"]);
	}
	if (isset($_POST["PostCode"])) {
		$sPostCode = sx_Sanitize_Input_Text($_POST["PostCode"]);
	}
	if (isset($_POST["City"])) {
		$sCity = sx_Sanitize_Input_Text($_POST["City"]);
	}
	if (isset($_POST["Country"])) {
		$sCountry = sx_Sanitize_Input_Text($_POST["Country"]);
	}

	if ((!empty($sAddress) && strlen($sAddress) > 40)
		|| (!empty($sPostCode) && strlen($sPostCode) > 10)
		|| (!empty($sCity) && strlen($sCity) > 30)
		|| (!empty($sCountry) && strlen($sCountry) > 30)
	) {
		$radioContinue = false;
		$arrError[] = LNG_Form_ExpectedLengthToLong;
	}

	$iEmailList = 0;
	if (isset($_POST["EmailList"])) {
		if (filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
			$iEmailList = 1;
		}
	}

	if (!empty($_POST['Biography'])) {
		$mBiography = sx_Sanitize_Text_Area($_POST['Biography']);
		if (strlen($mBiography) > 1200) {
			$radioContinue = false;
			$arrError[] = "The Biographical text is too long (" . strlen($mBiography) . ")!";
		}
	}

	/**
	 * Check Captcha after getting Form values
	 */
	if (!isset($_SESSION['captcha_code'])) {
		$radioContinue = false;
		$arrError[] = LNG__CaptchaError;
	} elseif (empty($_POST['captcha_input']) || ($_POST['captcha_input'] != $_SESSION['captcha_code'])) {
		$radioContinue = false;
		$arrError[] = LNG__CaptchaError;
	}

	// Check if mail exists
	if ($radioContinue) {
		$sql = "SELECT ParticipantID FROM conf_participants WHERE Email = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$sEmail]);
		$rs = $stmt->fetch(PDO::FETCH_NUM);
		if (($rs)) {
			$radioContinue = false;
			$arrError[] = LNG__EmailExists;
			$arrError[] = 'If you have applied for registration recently, please be patient, we will sent an email to you as soon as we can.';
		}
		$stmt = null;
		$rs = null;
	}

	/**
	 * Deal with Blacklisted IPs and SPAMS
	 * ==============================================================================
	 * Check if the IP is blacklisted. If true, 
	 *   - don't send email to the applicant (to avoid sending mails that might be defined as spams),
	 *   - send mail only to administrator that will approve the application,
	 *     with information about blacklisting
	 */

	$radio_SendMailToApplicant = true;
	$radioBlackListed = false;
	$radioBlackListedIP = 0;

	if ($radioContinue) {

		$radioBlackListed = sx_is_ip_blacklisted(sx_UserIP);

		$radioBlackListed = true;

		/**
		 * Check first if IP and Email are already in the table of blacklistes IPs
		 * and if the email is marked as valid
		 */
		if ($radioBlackListed) {
			$intBlackListID = 0;
			$intEndeavours = 1;
			$radioValidMailAddress = false;
			$sql = "SELECT BlackListID, Endeavours, ValidMailAddress
			FROM conf_blacklisted_ips
			WHERE Email = ? 
				AND IPAddress = ?
			ORDER BY BlackListID DESC LIMIT 1";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$sEmail, sx_UserIP]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$intBlackListID = (int) $rs['BlackListID'];
				$intEndeavours = (int) $rs['Endeavours'];
				$radioValidMailAddress = $rs['ValidMailAddress'];
			}
			$stmt = null;
			$rs = null;

			/**
			 * If the email is marked as valid by the administrator, 
			 * continue with normal registration
			 */
			if ((intval($intBlackListID) > 0) && $radioValidMailAddress) {
				$radioBlackListed = false;
			}
		}
	}

	/**
	 * If the IP is still blacklisted, Add to or update the tabel conf_blacklisted_ips
	 * - Dont't add the same IP and email in the table
	 * - Just increase the number of endeavours
	 */
	if ($radioContinue && $radioBlackListed) {

		// If IP and Email exists, update, else, insert
		if ((intval($intBlackListID) > 0)) {
			$intEndeavours++;
			if ($intEndeavours < 9999) {
				$sql = "UPDATE conf_blacklisted_ips SET 
						Endeavours = ?
					WHERE BlackListID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$intEndeavours, $intBlackListID]);
			}
		} else {
			$strFormSource = "Conference Registration";
			$stErrorTypy = "Blacklisted IP";

			$sql = "INSERT INTO conf_blacklisted_ips (
				FormSource, ErrorTypy, FirstName, LastName, Email, IPAddress)
				VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strFormSource, $stErrorTypy, $sFirstName, $sLastName, $sEmail, sx_UserIP]);
		}

		/**
		 * Check for multiple registrations from IPs with the first 6 left characters
		 *   equal to the current IP. If more than X, discontinue
		 * DEVELOP: Add a date in WHERE	conditions and send mail to administrator
		 */
		$sql = "SELECT SUM(Endeavours) AS SumEndeavours
				FROM conf_blacklisted_ips
				WHERE ValidMailAddress = 0
					AND DATE(UpdateDate) = CURDATE()
					AND LEFT(IPAddress, 6) = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([substr(sx_UserIP, 0, 6)]);
		$count = $stmt->fetchColumn();
		$stmt = null;
		if ((int) $count > 6) {
			$radioContinue = false;
			$arrError[] = 'Undefined error! Please try again in a few hours.
					If the problem recurs, contact the administration of the site!';
		} else {
			$radio_SendMailToApplicant = false;
			$radioBlackListedIP = 1;
		}
	}

	// Check passwords
	if ($radioContinue) {
		$sPassword = '';
		if (isset($_POST["Password"])) {
			$sPassword = trim($_POST["Password"]);
		}
		$sPassword2 = '';
		if (isset($_POST["Password"])) {
			$sPassword2 = trim($_POST["Password2"]);
		}

		if (empty($sPassword) || strlen($sPassword) < 8 || strlen($sPassword) > 64) {
			$radioContinue = false;
			$arrError[] = lngPasswordCharacters;
		}
		if ($sPassword != $sPassword2) {
			$radioContinue = false;
			$arrError[] = lngPasswordFieldsNotTheSame;
		}
	}

	// Add to database and send email
	if ($radioContinue) {

		$PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
		$sAllowToken = return_Random_Alphanumeric(72);
		$sApprovalToken = return_Random_Alphanumeric(72);
		$sDeleteToken = return_Random_Alphanumeric(72);

		$iParticipantID = 0;
		$sql = "INSERT INTO conf_participants (
				FirstName, LastName, 
				PostAddress, PostCode, City, Country, Phone, 
				Email, LoginPassword, 
				AllowToken, ApprovalToken, DeleteToken, 
				IPAddress, BlacklistedIP,
				EmailList, Biography)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([
			$sFirstName,
			$sLastName,
			$sAddress,
			$sPostCode,
			$sCity,
			$sCountry,
			$sPhone,
			$sEmail,
			$PW_Hash,
			$sAllowToken,
			$sApprovalToken,
			$sDeleteToken,
			sx_UserIP,
			$radioBlackListedIP,
			$iEmailList,
			$mBiography
		]);
		$iParticipantID = $conn->lastInsertId();

		include __DIR__ . '/join_email.php';

		unset($_SESSION['FormCreationTime']);
	}
}
?>

<section>
	<h1 class="head"><span>Sign up as User of the Website</span></h1>
	<?php
	if ($radioContinue) { ?>
		<h2>Thank you for your application!</h2>
		<p class="bg_success">Your application will now be processed by the administration of the site.
			Once completed, you will receive a mail containing a confirmation link.
			Please click on that link to complete your registration.
			You can then log in by using your e-mail and password.</p>
	<?php
		/***
		 * Multiple registrations by the same set of IPs: Registration is stopped
		 */
	} elseif (!empty($arrError) && $radioBlackListed) { ?>
		<p class="bg_error"><?php echo implode("<br>", $arrError) ?></p>
	<?php
	} else {

		/**
		 * Greate the token and its session here, on the server,
		 *   and a time stamp to account requests intervals
		 *   don't uppdate time stamp on error
		 */

		$str_FormToken = sx_generate_form_token('ConferenceSiteRegistration', 128);
		if (empty($arrError)) {
			$_SESSION['FormCreationTime'] = date('Y-m-d H:i:s');
		} ?>

		<h2><?= $strRegistrationTitle ?></h2>
		<div class="text text_bg">
			<div class="text_max_width">
				<?= $memoRegistrationNotes ?>
			</div>
		</div>
		<?php
		if (!empty($arrError)) { ?>
			<p class="bg_error">- <?php echo implode("<br>- ", $arrError) ?></p>
		<?php
		} ?>
		<form name="subscription" id="subscription" action="<?= sx_PATH ?>?pg=join" METHOD="POST" autocomplete="off">
			<input type="hidden" name="FormToken" value="<?php echo $str_FormToken ?>">
			<p><?= LNG_Form_AsteriskFieldsRequired . " " . lngNoCapitalInfo ?></p>

			<fieldset>
				<label><?= lngName ?>:</label>
				<input TYPE="text" NAME="FirstName" VALUE="<?= $sFirstName ?>" MAXCHARS="32" required> * <span class="text_xxsmall"><?= lngNoCapitals ?></span>
				<label><?= LNG__LastName ?>:</label>
				<input TYPE="text" NAME="LastName" VALUE="<?= $sLastName ?>" MAXCHARS="32" required> * <span class="text_xxsmall"><?= lngNoCapitals ?></span>
			</fieldset>

			<fieldset>
				<label><?= lngPhone ?>:</label>
				<input TYPE="text" NAME="Phone" VALUE="<?= $sPhone ?>" MAXCHARS="30">
				<label><?= lngAddress ?>:</label>
				<input TYPE="text" NAME="Address" VALUE="<?= $sAddress ?>" MAXCHARS="40">
				<label><?= lngPostalCode ?>:</label>
				<input TYPE="text" NAME="PostCode" VALUE="<?= $sPostCode ?>" MAXCHARS="10">
				<label><?= LNG__City ?>:</label>
				<input TYPE="text" NAME="City" VALUE="<?= $sCity ?>" MAXCHARS="30">
				<label><?= lngCountry ?>:</label>
				<input TYPE="text" NAME="Country" VALUE="<?= $sCountry ?>" MAXCHARS="30">
			</fieldset>

			<?php
			if ($radio_AllowAddProfile) {
				if (!empty($mBiography)) {
					$mBiography = strip_tags(str_replace("</p>", "\r\n\r\n", $mBiography));
				} ?>
				<div class="text_small"><?= $memo_AddProfileNotes ?></div>
				<fieldset>
					<label><?= lngBiography ?>: <input name="entered" disabled type="text" size="4">
						<?= LNG_Form_EnterMaxCharacters . ": " . $i_MaxEmailLength ?> </label>
					<textarea spellcheck name="Biography" rows="8" onFocus="countEntries('subscription','Biography',<?= $i_MaxEmailLength ?>);"><?= $mBiography ?></textarea>
					<p class="text_xsmall"><?= LNG_Form_WritePureText ?></p>
				</fieldset>
			<?php
			} ?>

			<fieldset>
				<label><?= LNG__Email ?>:</label>
				<input TYPE="email" NAME="Email" VALUE="<?= $sEmail ?>" MAXCHARS="54" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" /> *
				<label><?= lngPassword ?>:</label>
				<input TYPE="password" NAME="Password" pattern=".{8,32}" MAXCHARS="32" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" /> *
				<label><?= lngRepeatPassword ?>:</label>
				<input TYPE="password" NAME="Password2" pattern=".{8,32}" MAXCHARS="32" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" /> *
				<p class="text_small"><?= lngPasswordCharacters  ?></p>
			</fieldset>

			<fieldset>
				<?php include DOC_ROOT . "/sxPlugins/captcha/include.php"; ?>
				<br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
				<p class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></p>
			</fieldset>
			<fieldset>
				<p><input type="checkbox" name="EmailList" value="ON" checked> <span><?= lngAddEmailToList ?></span></p>
				<input TYPE="Submit" NAME="JoinAction" VALUE="<?= lngJoin ?>">
			</fieldset>
			<p> * <?= lngRequiredInfo ?></p>
		</form>
	<?php
	} ?>
</section>