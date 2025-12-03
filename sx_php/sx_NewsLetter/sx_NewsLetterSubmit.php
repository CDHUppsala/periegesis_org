<?php

/**
 * The NewsLetter verification URL-string goes by default to page: index.php
 * If you change it, you must also change the codes in page sx_Admin/email/sx_NewsLetterSender.php and in corresponing return pages
 * Verify subscription:		/index.php?nl=vd&lid=24&v=Q966-O398O777-U997
 * Delete subscription:		/index.php?nl=vd&lid=24&d=Q966-O398O777-U997
 */
/**
 * CHECK for Ajax reuest, if any
 * CHECK EMAIL ADDRESSES WITH
 * 1. PHP Filter
 * 2. The function sx_has_email_domain_mx(), which includes PHP Function checkdnsrr() for MX Record Check (with 
 * 3. The function is_email_domain_disposable() wich checks against blacklist files with disposable email domains
 * 4. The function checkdnsrr() for blacklisted IP addresses BUT,
 * 		NOT for clients IP but for the email domain IP,
 * 		called by check_Blacklisted_emeil_domain_Ips()
 */

$strNLReturnPage = "index.php";
$radioValidToken = false;

$strFirstName = '';
$strLastName = '';
$strPostCode = '';
$strPhoneNumber = '';
$strCity = '';
$strCountry = '';

$radioContinue = false;
$arrError = array();
$strSuccessMsg = "";
$sendOnlyEmail = false;
$intLetterID = 0;

/**
 * One of the 2 sessions holding the form token must always be active
 * so, check it
 */

if (empty($_SESSION['FooterNewsLettersForm_sx_token']) && empty($_SESSION['NewsLettersForm_sx_token'])) {
	write_To_Log("Newsletter", "Empty Form Token Sessions Possible Hack-Attempt");
	echo '<h2>Session Timeout</h2>';
	echo '<p>Please reload the page and try again.</p>';
	exit;
}

/**
 * Check if the request is an Ajax request
 */
$radioAjaxRequest = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!$radioAjaxRequest || $_SERVER["REQUEST_METHOD"] !== "POST") {
	write_To_Log("Newsletter", "No Ajax Request Hack-Attempt!");
	echo '<h2>No Way Home 1!</h2>';
	exit;
}

/**
 * Check if the request comes from the footer form or the main (this) form
 * In both cases, check:
 * 	1.	the respective form token:
 * 		if not valide, ask the client to reload the page - then EXIT
 *  2.	the enail addres domain, against blacklists of disposable domains
 * 		if blacklisted, ask the client to check the email domain and reload the page - then EXIT
 */

$isSecondNewsForm = false;

// 1. Check form token

if (isset($_POST["FooterNewsForm"])) {
	$isFooteTokenValid = isset($_POST['FooterFormToken']) && sx_valid_form_token("FooterNewsLettersForm", $_POST["FooterFormToken"]);
	if (!$isFooteTokenValid) {
		$reason = !isset($_POST['FooterFormToken']) ? "Empty Token" : "Wrong Token";
		write_To_Log("Newsletter Footer", "{$reason} Hack-Attempt");
		echo '<h2>An Error Occurred</h2>';
		echo '<p>Please reload the page and try again.</p>';
		exit;
	}
	// To check and secure that the footer form has been used
	$_SESSION['FooterNewsFormUsed'] = true;
	$strNewsEmail = trim($_POST["FooterNewsEmail"] ?? '');
} elseif (isset($_POST["AjaxNewsForm"])) {
	if (!isset($_SESSION['FooterNewsFormUsed'])) {
		write_To_Log("Newsletter", "Empty Session for use of the Footer Form - possible Hack-Attempt");
		echo '<h2>An Error Occurred</h2>';
		echo '<p>Please reload the page and try again.</p>';
		exit;
	}
	$radioValidToken = isset($_POST['FormToken']) && sx_valid_form_token("NewsLettersForm", $_POST["FormToken"]);
	if (!$radioValidToken) {
		$reason = !isset($_POST['FormToken']) ? "Empty Token" : "Wrong Token";
		write_To_Log("Newsletter", "{$reason} Hack-Attempt");
		echo '<h2>An Error Occurred</h2>';
		echo '<p>Please reload the page and try again.</p>';
		exit;
	}
	unset($_SESSION['FooterNewsFormUsed']);
	$isSecondNewsForm = true;
	$strNewsEmail = trim($_POST["NewsEmail"] ?? '');
} else {
	write_To_Log("Newsletter", "Empty Hidden Form Names - Hack-Attempt");
	echo '<h2>No Way Home 2!</h2>';
	exit;
}

// 2. Check the email address domain againsr blacklists of disposable email domains

if (empty($strNewsEmail) || !filter_var($strNewsEmail, FILTER_VALIDATE_EMAIL)) {
	write_To_Log("Newsletter", "Empty or invalid Email Address - possible Hack-Attempt");
	echo '<h2>An Error Occurred</h2>';
	echo '<p>Please check your email address, reload the page and try again.</p>';
	exit;
}

if (is_email_domain_disposable($strNewsEmail)) {
	write_To_Log("Newsletter", "Disposable email addresses: {$strNewsEmail} Hack-Attempt ");
	echo '<h2>Access Denied</h2>';
	echo "<p>Disposable email addresses are not allowed.</p>";
	echo "<p>Please check your email domain, reload the page and try again.</p>";
	exit;
}

/**
 * If email addres comes from the second, final form, contineou checking
 * 	1.	If email address has MX record
 * 	2.	If IP addresses are blacklisted
 * 			- both REMOTE_ADDR IP and HTTP_X_FORWARDED_FOR IP
 * 			- the email domain's IP
 * Decision to be made:
 * 	-	to EXIT, or
 * 	-	inform the client and show the form asking for changes
 */

if ($_SERVER["REQUEST_METHOD"] == "POST" && $isSecondNewsForm) {
	$radioContinue = true;

	/**
	 * Prepare variables for the including file that checks email address and IPs
	 * Include $s_ClientIP and $is_ClientIPBlackListed in registration (any account) for future checks
	 * Define $s_ClientIP with Priority: 1. $domainIP, 2 $remoteIP (not use $forwardedIP)
	 */
	$s_EmailToCheck = $strNewsEmail;
	$s_SentFormName = 'Newsletter';
	$s_TimeSessionName = 'TimeNewsLettersForm';
	$s_ClientIP = NULL;
	$is_ClientIPBlackListed = 0;

	include PROJECT_PATH . "/sx_Security/include_check_email_ips.php";

	/**
	 * Validation of other form inputs
	 */

	// 1. Required: Validate First and Last Name
	$strFirstName = sx_Sanitize_Input_Text($_POST['FirstName'] ?? '');
	$strLastName = sx_Sanitize_Input_Text($_POST['LastName'] ?? '');
	if (empty($strFirstName) || strlen($strFirstName) < 2 || strlen($strFirstName) > 45) {
		$radioContinue = false;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}
	if (empty($strLastName) || strlen($strLastName) < 2 || strlen($strLastName) > 45) {
		$radioContinue = false;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}

	if (defined('SX_RequireNameAndPostalCodeInNewsLeters') && SX_RequireNameAndPostalCodeInNewsLeters) {

		// 2. Required: Validate Phone Number and Post Code
		$strPhoneNumber = sx_GetSanitizedPhone($_POST["PhoneNumber"] ?? '');
		$strPostCode = sx_Sanitize_Input_Text($_POST["PostCode"] ?? '');
		if (empty($strPhoneNumber) || strlen($strPhoneNumber) > 15) {
			$radioContinue = false;
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
		}
		if (empty($strPostCode) || strlen($strPostCode) > 12) {
			$radioContinue = false;
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
		}
	}

	// 3. Optional Fields: City and Country
	$strCity = sx_Sanitize_Input_Text($_POST["City"] ?? '');
	$strCountry = sx_Sanitize_Input_Text($_POST["Country"] ?? '');
	if (strlen($strCity) > 45 || strlen($strCountry) > 45) {
		$radioContinue = false;
		$arrError[] = LNG_Form_ExpectedLengthToLong;
	}


	// 4. Check for Existing Subscription
	if ($radioContinue) {
		$stmt = $conn->prepare("SELECT LetterID, 
			Active, BlacklistedIP, SubscribeCode, InsertDate 
            FROM newsletters 
            WHERE Email = ? 
				AND (LanguageID = ? OR LanguageID = 0)");
		$stmt->execute([$strNewsEmail, $int_LanguageID]);
		$rs = $stmt->fetch();

		if ($rs) {
			if ($rs["Active"]) {
				$radioContinue = false;
				$strSuccessMsg = lngThanksForNewsletterAlreadyActive;
			} elseif (return_Date_Difference($rs["InsertDate"], date("Y-m-d")) < 5) {
				$radioContinue = false;
				$strSuccessMsg = lngThanksForNewsletterAlreadyRequested;
			} elseif ($rs["BlacklistedIP"]) {
				// Give thanks and Do nothing
				$radioContinue = false;
				$strSuccessMsg = lngThanksForNewsletter;
			} elseif (!empty($rs["SubscribeCode"]) && strlen($rs["SubscribeCode"]) > 24) {
				// Resend email with existing activation codes
				$sendOnlyEmail = true;
				$intLetterID = $rs["LetterID"];
				$strSubCode = $rs["SubscribeCode"];
				$strSuccessMsg = lngThanksForNewsletter;
			} else {
				// Give thanks and Do nothing
				$radioContinue = false;
				$strSuccessMsg = lngThanksForNewsletter;
			}
		}
	}

	// 5. Insert New Subscription
	if ($radioContinue) {
		unset($_SESSION[$s_TimeSessionName]);
		if (!$sendOnlyEmail) {
			try {
				$conn->beginTransaction();
				$strSubCode = return_Random_Alphanumeric(72);
				$strUnsubCode = return_Random_Alphanumeric(72);

				$stmt = $conn->prepare("INSERT INTO newsletters (LanguageID, Email, FirstName, LastName, Phone, PostCode, City, Country, IPAddress, BlacklistedIP, SubscribeCode, UnsubscribeCode) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute([$int_LanguageID, $strNewsEmail, $strFirstName, $strLastName, $strPhoneNumber, $strPostCode, $strCity, $strCountry, $s_ClientIP, $is_ClientIPBlackListed, $strSubCode, $strUnsubCode]);

				$intLetterID = $conn->lastInsertId();
				$conn->commit();

				$strSuccessMsg = lngThanksForNewsletter;
			} catch (Exception $e) {
				$conn->rollBack();
				$radioContinue = false;
				// Hide the $e in real production
				$arrError[] = lngInfoErrorTryAgain . ' sx2' . $e;
			}
		}

		// Send Email if Necessary
		if (!empty($strSubCode) && (int) $intLetterID > 0) {
			$sx_mail_subject = lngSubscriptionInfor;
			$sx_send_to_email = $strNewsEmail;
			$sx_mail_content = "<h4>" . lngThanksForNewsletter . "</h4>";
			$sx_mail_content .= LNG__EmailSentUponNewsletterRequest;
			$sx_mail_content .= "<p><a target='_blank' href='" . sx_ROOT_HOST . "/" . sx_CurrentLanguage . "/" . $strNLReturnPage . "?nl=vd&lid=" . $intLetterID . "&v=" . $strSubCode . "'>" . lngClickHere . "</a>.</p>";

			require dirname(__DIR__) . "/sx_Mail/sx_mail_template.php";
		} else {
			$radioContinue = false;
			$arrError[] = lngInfoErrorTryAgain;
		}
	}
}

if ($radioContinue === false) { ?>
	<h2><?php echo str_SiteTitle ?></h2>
	<h4><?php echo lngNewsletterSubscribe ?></h4>
	<?php
	if (!empty($arrError)) { ?>
		<div class="bg_error"><?= implode('<br>', $arrError) ?></div>
	<?php
	}
	if (!empty($strSuccessMsg)) { ?>
		<div class="bg_info"><?= $strSuccessMsg ?></div>
	<?php
		exit;
	}
	/**
	 * Greate the token and its session here, on the server,
	 *   and a time stamp to account requests intervals
	 *   don't uppdate time stamp on error
	 */

	//if (empty($arrError)) {
	$_SESSION['TimeNewsLettersForm'] = date('Y-m-d H:i:s');
	//}
	$strSetFormToken = sx_generate_form_token('NewsLettersForm', 128);
	?>
	<form class="jq_load_modal_window" name="NewsLetter" method="post">
		<input type="hidden" name="FormToken" value="<?= $strSetFormToken ?>">
		<input type="hidden" name="FormName" value="NewsLetter" />
		<input type="hidden" name="AjaxNewsForm" value="yes" />
		<fieldset>
			<label><?= LNG__Email ?>: *<br>
				<input type="email" name="NewsEmail" value="<?= $strNewsEmail ?>" style="width: 100%" placeholder="<?php echo LNG__Email ?>" required />
			</label>
		</fieldset>
		<fieldset>
			<div class="flex_between">
				<label class="flex_items">
					<input type="text" name="FirstName" value="<?php echo $strFirstName ?>" style="width: 100%" placeholder="<?= LNG__FirstName ?> *" required />
				</label>
				<label class="flex_items">
					<input type="text" name="LastName" value="<?php echo $strLastName ?>" style="width: 100%" placeholder="<?= LNG__LastName ?> *" required />
				</label>
			</div>
			<?php
			if (defined('SX_RequireNameAndPostalCodeInNewsLeters') && SX_RequireNameAndPostalCodeInNewsLeters) { ?>
				<div class="flex_between">
					<label class="flex_items">
						<input type="text" name="PhoneNumber" value="<?php echo $strPhoneNumber ?>" style="width: 100%" placeholder="<?= lngPhone ?> *" required />
					</label>
					<label class="flex_items">
						<input type="text" name="PostCode" value="<?php echo $strPostCode ?>" style="width: 100%" placeholder="<?= lngPostalCode ?> *" required />
					</label>
				</div>
			<?php
			} ?>
			<div class="flex_between">
				<label class="flex_items">
					<input type="text" name="City" value="<?php echo $strCity ?>" style="width: 100%" placeholder="<?= LNG__City ?>" title="<?php echo lngOptional ?>" />
				</label>
				<label class="flex_items">
					<input type="text" name="Country" value="<?php echo $strCountry ?>" style="width: 100%" placeholder="<?= lngCountry ?>" title="<?php echo lngOptional ?>" />
				</label>
			</div>

		</fieldset>
		<div class="text_xxsamll"><b>*</b> <?php echo lngRequiredInformation ?></div>

		<fieldset class="align_center">
			<input class="jq_submit" type="submit" value="<?= LNG_Form_Submit ?>" name="Submit" />
		</fieldset>
	</form>
<?php
} else { ?>
	<h3><?= $strSuccessMsg ?></h3>
	<?php
	if ($radioContinue) { ?>
		<p><?= lngNewsletterVarificationSentWithEmail ?></p>
	<?php
	} ?>
<?php
} ?>