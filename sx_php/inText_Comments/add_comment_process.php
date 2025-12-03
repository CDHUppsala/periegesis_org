<?php
$strAddFirstName = "";
$strAddLastName = "";
$strAddEmail = "";
$strAddTitle = "";
$strAddMainText = "";

$radioContinue = False;
$arrError = array();
if ($_SERVER["REQUEST_METHOD"] == "POST" && intval($int_TextID) > 0) {
	$radioContinue = true;

	// Hidden text area must be empty
	if (!empty($_POST["City"])) {
		write_To_Log("Text Comments: Hidden input is filledd!");
		header("Location: index.php?sx=0");
		exit();
	}

	// Check the validity of input names
	$sxWhitelist = array(
		'TextID', 'CommentsFormToken', 'FirstName', 'LastName', 'Email',
		'City', 'Title', 'entered', 'TextBody', 'captcha_input', 'addNewComment'
	);

	foreach ($_POST as $key => $item) {
		if (!in_array($key, $sxWhitelist)) {
			$radioContinue = false;
			break;
		}
	}

	if ($radioContinue == false) {
		write_To_Log("Text Comments: Wrong Whitelist Hack-Attempt!");
		header('Location: index.php');
		exit;
	}

	// The global $int_TextID must be equal to the ID sent by the form
	if (empty($_POST["TextID"]) || $int_TextID != intval($_POST["TextID"])) {
		write_To_Log("Text Comments: Default TextID not equal to Input ID !");
		header("Location: index.php?sx=0");
		exit();
	}

	foreach ($_POST as $key => $value) {
		if (!empty($key)) {
			$value = strip_tags($value);
			$_POST[$key] = sx_Sanitize_Input_Text($value);
		}
		//echo $key . ", ";
	}

	/**
	 * Check if User is logged in and recall the user identity
	 */
	$intUserID = 0;
	if ($radio__UserSessionIsActive) {
		$intUserID = $_SESSION["Users_UserID"];
		$strAddFirstName = $_SESSION["Users_FirstName"];
		$strAddLastName = $_SESSION["Users_LastName"];
		$strAddEmail = $_SESSION["Users_UserEmail"];
	} else {
		/**
		 * If no user is logged in, make all neccessary checkes:
		 * 1. Get and Sanitize user information inputs for reuse, in case of error
		 *    Title and Main Text must be sanitized even if user is logged ine
		 * 2. Form Token, after sanitization, in case session is timed out,
		 *    to reuse inputs
		 * 3. Captcha (if any), after sanitization, to reuse inputs
		 */

		/**
		 * 1. Get and check form inputs for user identity
		 */

		if (!empty($_POST["FirstName"])) {
			$strAddFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
		}
		if (!empty($strAddFirstName) && (strlen($strAddFirstName) < 2 || strlen($strAddFirstName) > 48)) {
			$radioContinue = false;
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
		}

		if (!empty($_POST["LastName"])) {
			$strAddLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
		}
		if (!empty($strAddLastName) && (strlen($strAddLastName) < 2 || strlen($strAddLastName) > 48)) {
			$radioContinue = false;
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
		}

		if (!empty($_POST["Email"])) {
			$strAddEmail = $_POST["Email"];
		}
		if (!empty($strAddEmail) && strlen($strAddEmail) < 8) {
			$radioContinue = false;
			$arrError[] = lngWriteCorrectEmail;
		} else {
			$CheckEmail = filter_var($strAddEmail, FILTER_VALIDATE_EMAIL);
			if ($CheckEmail == false) {
				$radioContinue = false;
				$arrError[] = lngWriteCorrectEmail;
			} elseif (sx_has_email_domain_mx($strAddEmail) === false) {
				$radioContinue = false;
				$arrError[] = lngWriteCorrectEmail;
			}
		}

		/**
		 * 2. Form Token: is active for only non logged users
		 * Usually, if Form Token is not valid, you will write message to
		 *   a log file, redirect to default page and exit.
		 * However, the session might have been timed out, so,
		 *   give the user in this case the possibility to try every 20 min.
		 */
		if (empty($_POST['CommentsFormToken'])) {
			// No mercy in this case!
			write_To_Log("Adding Text Comment: Empty Token Hack-Attempt!");
			header('Location: index.php?sx=1');
			exit;
		} elseif (!empty($_SESSION['CommentsFormToken_sx_token'])) {
			// If session is active
			if (!sx_valid_form_token("CommentsFormToken", $_POST["CommentsFormToken"])) {
				// No mercy in this case either:
				write_To_Log("Adding Text Comment: Wrong Token Hack-Attempt!");
				header('Location: index.php?sx=2');
				exit;
			}
		} else {
			$radioContinue = false;
			$arrError[] = 'Sorry! Some sessions have been timed out. Please try again.';
		}

		/**
		 * 3. Captcha: The use of Captcha is defined in sx_Config.php
		 * It is set to False, if the user is logged in,
		 *   but might also be removed generally, so check it here again
		 */
		if ($radio_UseCaptcha) {
			if ($_POST['captcha_input'] != $_SESSION["captcha_code"]) {
				$radioContinue = false;
				$arrError[] = LNG__CaptchaError;
			}
			unset($_SESSION["captcha_code"]);
		}
	}

	/**
	 * Sanitize the inputs that are common to all participents, logged in or not
	 * - Title and Main Text from textarea
	 */

	if (!empty($_POST["Title"])) {
		$strAddTitle = sx_Sanitize_Input_Text($_POST["Title"]);
	}
	if (!empty($strAddTitle) && (strlen($strAddTitle) < 6 || strlen($strAddTitle) > 200)) {
		$radioContinue = False;
		$arrError[] = LNG_Form_AsteriskFieldsRequired;
	}

	/**
	 * Textarea:
	 * Sanitize only rows (sx_Sanitize_Text_Area_Rows()), in case of reuse. 
	 * If no errors in form, add also paragraphs (sx_ParagraphBreaks()), before uploading,
	 */
	if (!empty($_POST["TextBody"])) {
		$strAddMainText = sx_Sanitize_Text_Area_Rows($_POST["TextBody"]);
	}
	if (!empty($strAddMainText) && strlen($strAddMainText) > intval($i_MaxCommentLength) + 100) {
		$radioContinue = false;
		$arrError[] = lngMsgContains . " " . strlen($strAddMainText) . " " . lngOfMaxCharactersAllowed . " " . $i_MaxCommentLength;
	}

	/**
	 * Check if User IP (Visitors User IP Address) is blacklisted.
	 * Add the result to database and inform in that case the 
	 *   administrator in the email, to be careful
	 */
	$radioBlackListedIP = 0;
	if ($radioContinue) {
		$radioBlackListed = sx_is_ip_blacklisted(sx_UserIP);
		if ($radioBlackListed) {
			$radioBlackListedIP = 1;
		}
	}

	if ($radioContinue) {
		$strAddMainText = sx_ParagraphBreaks($strAddMainText);
		$strCommentCode = return_Random_Alphanumeric(72);

		$radioVisible = 1;
		if ($send_MailFor_AdminApproval) {
			$radioVisible = 0;
		}

		$sql = "INSERT INTO text_comments
		(TextID, UserID, Title, Visible, FirstName, LastName, 
		Email, IPAddress, BlacklistedIP, CommentCode, MainText)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([
			$int_TextID, $intUserID, $strAddTitle, $radioVisible, $strAddFirstName, $strAddLastName,
			$strAddEmail, sx_UserIP, $radioBlackListedIP, $strCommentCode, $strAddMainText
		]);
		$intInsertID = $conn->lastInsertId();

		if ($send_MailFor_AdminApproval) {
			/** 
			 * SEND MAILS using the included file sx_mail_template.php
			 * 		Check constant and global variables inlcuded in the mail template...
			 * Variables to be defined here for the mail template:
			 * 		$sx_send_to_email: The mail address of the reciever
			 * 		$sx_mail_subject: The subject of mail
			 * 		$sx_mail_content: whatever with HTML formation
			 */
			$sx_mail_subject = 'Approve a text comment';
			$sx_send_to_email = str_SiteEmail;

			$sx_mail_content = '<h4>Approve a comment about a text of the website</h4>';
			$sx_mail_content .= '<p>The comment is added by ' . $strAddFirstName . ' ' . $strAddLastName . ', ' . $strAddEmail . '</p>';

			if ($radioBlackListed) {
				$sx_mail_content .= '<p>Please notices that the <b>IP Address</b> of the sender is <b>Blacklisted</d>.
					However, this might not mean that the sender\'s eamil address is invalid.</p>';
			}

			$confirmAdminURLpath = sx_ROOT_HOST_PATH . "?tid=" . $int_TextID . "&cid=" . $intInsertID . "&cc=" . $strCommentCode;
			$sx_mail_content .= '<p><a style="text-decoration: none;" href="' . $confirmAdminURLpath . '">Click here to Approve the Comment</a>.</p>';

			$sx_mail_content .= '<p>The Title and Content of the comment:</p>';
			$sx_mail_content .= '<h4>' . $strAddTitle . '</h4>';
			$sx_mail_content .= $strAddMainText;

			// Send the mail
			require dirname(__DIR__) . "/sx_Mail/sx_mail_template.php";

			$radioContinue = True;
			$strAddFirstName = "";
			$strAddLastName = "";
			$strAddEmail = "";
			$strAddTitle = "";
			$strAddMainText = "";
		} else {
			/**
			 * The added comment is visible even without redirection
			 * Redirect only to anchor the page to the position of the inserted comment
			 */
			header("Location: texts.php?tid=" . $int_TextID . "&anchor=" . $intInsertID . "#" . $intInsertID);
			exit();
		}
	}
}
