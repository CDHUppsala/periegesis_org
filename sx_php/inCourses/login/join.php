<?php
if ($radio_AllowOnlineRegistration == false) {
	header('Location: index.php');
	exit();
}

/**
 * Empty form variables
 */
$sFirstName = "";
$sLastName = "";
$sTitle = "";
$sInstitution = "";
$sEmail = "";
$sEmail_2 = "";
$sPhone = "";
$sAddress = "";
$sPostCode = "";
$sCity = "";
$sCountry = "";
$strEmailList = "";

$arrError = array();
$radioContinue = false;
$radioBlackListed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioContinue = True;

	$sxWhitelist = array(
		'FormToken',
		'FirstName',
		'LastName',
		'Title',
		'Institution',
		'Email',
		'EmailRepeat',
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
	foreach ($_POST as $key => $value) {
		if (!in_array($key, $sxWhitelist)) {
			$radioContinue = false;
			break;
		}
	}
	if ($radioContinue == false) {
		write_To_Log("Students Registration: Wrong Whitelist Hack-Attempt!");
		header('Location: index.php');
		exit;
	}

	$radioValidToken = true;
	if (!isset($_POST['FormToken'])) {
		$radioValidToken = false;
		write_To_Log("Students Registration: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("StudentRegistrationForm", $_POST["FormToken"])) {
		$radioValidToken = false;
		write_To_Log("Students Registration: Wrong Token Hack-Attempt!");
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
			if ((int) $seconds_passing < 30) {
				$radioRedirect = true;
			}
		} else {
			$radioRedirect = true;
		}
	} else {
		$radioRedirect = true;
	}
	if ($radioRedirect) {
		write_To_Log("Student Registration: Frequent requests (30 seconds)!");
		sleep(5);
		header('Location: index.php');
		exit;
	}

	/**
	 * ================================================
	 *   just contunue to get and check input values
	 * ================================================
	 */

	// Check name
	if (!empty($_POST["FirstName"])) {
		$sFirstName = sx_Sanitize_Input_Text(trim($_POST["FirstName"]));
	}
	if (!empty($_POST["LastName"])) {
		$sLastName = sx_Sanitize_Input_Text(trim($_POST["LastName"]));
	}
	if (strlen($sFirstName) < 2 || strlen($sLastName) < 2) {
		$radioContinue = false;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}

	// Check title and institution
	if (!empty($_POST["Title"])) {
		$sTitle = sx_Sanitize_Input_Text(trim($_POST["Title"]));
	}
	if (!empty($_POST["Institution"])) {
		$sInstitution = sx_Sanitize_Input_Text(trim($_POST["Institution"]));
	}
	if (strlen($sTitle) < 2 || strlen($sInstitution) < 2) {
		$radioContinue = false;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}

	// Check email
	$CheckEmail = false;
	if (!empty($_POST["Email"])) {
		$sEmail = trim($_POST["Email"]);
		$strEmailRepeat = trim($_POST["EmailRepeat"]);
		$CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
	}

	if ($CheckEmail == false || $sEmail != $strEmailRepeat) {
		$radioContinue = false;
		$arrError[] = lngWriteCorrectEmail;
	} elseif (sx_has_email_domain_mx($sEmail) === false) {
		$radioContinue = false;
		$arrError[] = lngWriteCorrectEmail;
	} elseif (strlen($sEmail) < 8) {
		$radioContinue = false;
		$arrError[] = lngWriteCorrectEmail;
	}

	// Check other non-required inputs
	if (!empty($_POST["Phone"])) {
		$sPhone = sx_GetSanitizedPhone($_POST["Phone"]);
	}
	if (strlen($sPhone) > 16) {
		$radioContinue = false;
		$arrError[] = LNG_Form_ExpectedLengthToLong;
	}
	if (!empty($_POST["Address"])) {
		$sAddress = sx_Sanitize_Input_Text($_POST["Address"]);
	}
	if (!empty($_POST["PostCode"])) {
		$sPostCode = sx_Sanitize_Input_Text($_POST["PostCode"]);
	}
	if (!empty($_POST["City"])) {
		$sCity = sx_Sanitize_Input_Text($_POST["City"]);
	}
	if (!empty($_POST["Country"])) {
		$sCountry = sx_Sanitize_Input_Text($_POST["Country"]);
	}
	if (strlen($sAddress) > 40 || strlen($sPostCode) > 10 || strlen($sCity) > 30 || strlen($sCountry) > 30) {
		$radioContinue = false;
		$arrError[] = LNG_Form_ExpectedLengthToLong;
	}

	$iEmailList = 0;
	if (isset($_POST["EmailList"])) {
		if (filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
			$iEmailList = 1;
		}
	}

	/**
	 * Check Captcha after getting Form values
	 */
	if (sx_radio_UseStudentRegistratioCaptcha) {
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
		$sql = "SELECT StudentID FROM course_students WHERE Email = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$sEmail]);
		$rs = $stmt->fetch(PDO::FETCH_NUM);
		if (($rs)) {
			$radioContinue = false;
			$arrError[] = LNG__EmailExists;
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

		/**
		 * Check first if IP and Email are already in the table
		 * and if the email is marked as valid
		 */
		if ($radioBlackListed) {
			$intBlackListID = 0;
			$intEndeavours = 1;
			$radioValidMailAddress = false;
			$sql = "SELECT BlackListID, Endeavours, ValidMailAddress
			FROM course_blacklisted_ips
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
	 * If the IP is still blacklisted, Add to or update the tabel course_blacklisted_ips
	 * - Dont't add the same IP and email in the table
	 * - Just increase the number of endeavours
	 */
	if ($radioContinue && $radioBlackListed) {

		// If IP and Email exists, update, else, insert
		if ((intval($intBlackListID) > 0)) {
			$intEndeavours++;
			if ($intEndeavours < 9999) {
				$sql = "UPDATE course_blacklisted_ips SET 
						Endeavours = ?
					WHERE BlackListID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$intEndeavours, $intBlackListID]);
			}
		} else {
			$strFormSource = "Student Registration: ";
			$stErrorTypy = "Blacklisted IP";

			$sql = "INSERT INTO course_blacklisted_ips (
				FormSource, ErrorTypy, FirstName, LastName, Email, IPAddress)
				VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strFormSource, $stErrorTypy, $sFirstName, $sLastName, $sEmail, sx_UserIP]);
		}

		/**
		 * Check for multiple registrations from IPs with the first 6 left characters
		 *   equal to the current IP. If more than X, discontinue
		 * DEVELOP: Add a date in WHERE	conditions and send mail o administrator
		 */
		$sql = "SELECT SUM(Endeavours) AS SumEndeavours
				FROM course_blacklisted_ips
				WHERE ValidMailAddress = 0
					AND LEFT(IPAddress, 6) = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([substr(sx_UserIP, 0, 6)]);
		$count = $stmt->fetchColumn();
		$stmt = null;
		if ((int) $count >= 4) {
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
			}
		}
	}

	if ($radioContinue) {
		$PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
		$sActivationCode = return_Random_Alphanumeric(72);
		$sApprovalCode = return_Random_Alphanumeric(72);
		$sRemoveCode = return_Random_Alphanumeric(72);
		$radioAllowAccess = 0;

		$sql = "INSERT INTO course_students (
			AllowAccess, RegistrationDate, 
			FirstName, LastName, 
			Title, Institution,
			PostAddress, PostCode, City, Country, Phone, 
			Email, LoginPassword, ActivationCode, 
			ApprovalCode, RemoveCode, 
			IPAddress, BlacklistedIP, EmailList)
		VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$conn->beginTransaction();
		$stmt->execute([
			$radioAllowAccess,
			date('Y-m-d'),
			$sFirstName,
			$sLastName,
			$sTitle,
			$sInstitution,
			$sAddress,
			$sPostCode,
			$sCity,
			$sCountry,
			$sPhone,
			$sEmail,
			$PW_Hash,
			$sActivationCode,
			$sApprovalCode,
			$sRemoveCode,
			sx_UserIP,
			$radioBlackListedIP,
			$iEmailList
		]);
		$intStudentID = $conn->lastInsertId();
		$conn->commit();

		include __DIR__ . '/join_email.php';

		unset($_SESSION['FormCreationTime']);
	}
}

?>

<section>
	<h1><?= $str_StudentsAreaRegistrationTitle  ?></h1>
	<?php
	if ($radioContinue) { ?>
		<h2>Thank you for your application!</h2>
		<p class="bg_success">Your application will now be processed.
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
		 *   don't uppdate time stamp on error,
		 */

		$str_FormToken = sx_generate_form_token('StudentRegistrationForm', 128);
		if (empty($arrError)) {
			$_SESSION['FormCreationTime'] = date('Y-m-d H:i:s');
		}

		if (!empty($arrError)) { ?>
			<p class="bg_error"><?php echo implode("<br>", $arrError) ?></p>
		<?php
		} ?>
		<div class="text">
			<p><?= lngNoCapitalInfo ?></p>
		</div>
		<div class="formWrap">
			<form name="StudentRegistration" id="StudentRegistration" action="courses_login.php?pg=join" METHOD="POST">
				<input type="hidden" name="FormToken" value="<?php echo $str_FormToken ?>">
				<fieldset>
					<label><?= LNG__FirstName ?>:</label>
					<input TYPE="text" NAME="FirstName" VALUE="<?= $sFirstName ?>" MAXCHARS="30" required> *
					<label><?= LNG__LastName ?>:</label>
					<input TYPE="text" NAME="LastName" VALUE="<?= $sLastName ?>" MAXCHARS="30" required> *
					<label><?= LNG__Title ?>:</label>
					<input TYPE="text" NAME="Title" VALUE="<?= $sTitle ?>" MAXCHARS="60" required> *
					<label><?= lngInstitution ?>:</label>
					<input TYPE="text" NAME="Institution" VALUE="<?= $sInstitution ?>" MAXCHARS="60" required> *
				</fieldset>

				<fieldset>
					<label><?= LNG__Email ?>:</label>
					<input TYPE="email" NAME="Email" VALUE="<?= $sEmail ?>" MAXCHARS="54" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" /> *
					<label><?= LNG__EmailRepeat ?>:</label>
					<input TYPE="email" NAME="EmailRepeat" VALUE="<?= $sEmail_2 ?>" MAXCHARS="54" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" /> *
					<label><?= lngPassword ?>:</label>
					<input TYPE="password" NAME="Password" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" MAXCHARS="32"> *
					<label><?= lngRepeatPassword ?>:</label>
					<input TYPE="password" NAME="Password2" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" MAXCHARS="32"> *
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
				<?php if (sx_radio_UseStudentRegistratioCaptcha) { ?>
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
	<?php
	} ?>
</section>
<section>
	<article class="text">
		<div class="text_max_width">
			<?= $memo_StudentsAreaRegistrationNotes ?>
		</div>
	</article>
</section>