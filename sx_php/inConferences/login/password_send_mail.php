<?php

$strError = "";
$iParticipantID = 0;
$sEmail = "";
$radioContinue = False;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioValidToken = true;
	if (!isset($_POST['FormToken'])) {
		$radioValidToken = False;
		write_To_Log("Participants Send PW: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("ResetParticipantPassword", $_POST["FormToken"])) {
		$radioValidToken = False;
		write_To_Log("Participants Send PW: Wrong Token Hack-Attempt!");
	}

	if ($radioValidToken == false) {
		header('Location: index.php');
		exit;
	}

	$radioValidCaptcha = False;
	if (isset($_POST['captcha_input']) && $_POST['captcha_input'] == $_SESSION['captcha_code']) {
		$radioValidCaptcha = True;
	} else {
		$strError = LNG__CaptchaError;
	}

	if ($radioValidCaptcha) {
		if (isset($_POST["Email"])) {
			$radioContinue = True;
			$sEmail = trim($_POST['Email']);
			$CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
			if ($CheckEmail == false) {
				$strError = lngPleaseWriteYourEmail;
				$radioContinue = false;
			}
		}
		if ($radioContinue) {
			$sql = "SELECT ParticipantID 
				FROM conf_participants 
				WHERE Email = ? 
				AND AllowAccess = 1 ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$sEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$iParticipantID = $rs["ParticipantID"];
			} else {
				$strError = LNG__EmailDoesNotExist;
				$radioContinue = false;
			}
			$stmt = null;
			$rs = null;
		}
		if ($radioContinue) {
			if (intval($iParticipantID) == 0) {
				$iParticipantID = 0;
				$radioContinue = false;
			}
		}
		if ($radioContinue) {
			$sxToken = return_Random_Token(48);
			$sql = "INSERT INTO password_reset
				(MemberID, MemberEmail, RecoveryToken, IsValid, PageURL)
				VALUES (?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iParticipantID, $sEmail, $sxToken, 1, sx_PATH]);

			$sql = "UPDATE conf_participants SET 
				ResetPassword = 1
			WHERE ParticipantID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iParticipantID]);

			/** 
			 * SEND MAILS using the included file sx_mail_template.php
			 * 		Check constant and global variables inluded in the mail template...
			 * Variables to be defined here for the mail template:
			 * 		$sx_send_to_email: The mail of the reciever
			 * 		$sx_mail_subject: The subject of mail
			 * 		$sx_mail_content: whatever with HTML formation
			 */

			$sx_mail_subject = "Reset password";
			$sx_send_to_email = $sEmail;

			$loginURL = sx_ROOT_HOST_PATH . "?pg=reset&token=" . $sxToken;

			$sx_mail_content = '<h4>This letter has been sent to you because you asked to reset your password.</h4>';
			$sx_mail_content .= '<p>Please <a style="text-decoration: none;" href="' . $loginURL . '">' . lngClickHere . '</a>
			and we will help you to set a new password.</p>';

			require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
		}
	}
}

if ($radioContinue) { ?>
	<h1 class="head"><span><?php echo lngSuccessfulSending ?></span></h1>
	<div class="text text_bg"><div class="text_max_width"><p><?= LNG__EmailSentToChangePassword ?></p></div></div>
<?php
} else {
	$strFormToken = sx_generate_form_token('ResetParticipantPassword', 128);
?>
	<h1 class="head"><span><?php echo lngForgotPassword ?></span></h1>
	<div class="text text_bg"><div class="text_max_width"><p><?= lngIfForgetPasswordSendMail ?></p></div></div>
	<?php
	if (!empty($strError)) { ?>
		<p class="bg_error"><?= $strError ?></p>
	<?php
	} ?>
	<form name="SendPassword" action="<?= sx_PATH ?>?pg=forgot" method="post">
		<input type="hidden" name="FormToken" value="<?php echo $strFormToken ?>">
		<fieldset>
			<label><?= lngMyEmail ?>:</label>
			<input type="email" name="Email" value="<?= $sEmail ?>" required />
		</fieldset>
		<fieldset>
			<?php include DOC_ROOT . "/sxPlugins/captcha/include.php" ?>
			<br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
			<p class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></p>
		</fieldset>

		<fieldset>
			<p class="align_center"><input type="submit" name="SendPW" value="<?= LNG_Form_Submit ?>"></p>
		</fieldset>
	</form>
<?php
} ?>