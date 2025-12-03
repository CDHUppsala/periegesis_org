<?php

$strError = "";
$iUserID = 0;
$sEmail = "";
$radioContinue = False;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioValidToken = true;
	if (!isset($_POST['FormToken'])) {
		$radioValidToken = false;
		write_To_Log("Reset Forum Password: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("ResetForumPassword", $_POST["FormToken"])) {
		$radioValidToken = false;
		write_To_Log("Forum Send Password: Wrong Token Hack-Attempt!");
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
			$sql = "SELECT UserID 
			    FROM forum_members
			    WHERE UserEmail = ? 
				AND AllowAccess = 1 ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$sEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$iUserID = $rs["UserID"];
			} else {
				$radioContinue = false;
				$strError = LNG__EmailDoesNotExist;
			}
			$stmt = null;
			$rs = null;
		}
		if ($radioContinue) {
			if (intval($iUserID) == 0) {
				$iUserID = 0;
				$radioContinue = false;
			}
		}

		if ($radioContinue) {

			$sxToken = return_Random_Token(48);
			$sql = "INSERT INTO password_reset
				(MemberID, MemberEmail, RecoveryToken, IsValid, PageURL)
				VALUES (?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iUserID, $sEmail, $sxToken, 1, sx_PATH]);

			$sql = "UPDATE forum_members SET 
				SentPassword = 1
			WHERE UserID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iUserID]);

			/** 
			 * SEND MAILS using the included file sx_mail_template.php
			 * 		Check constant and global variables inluded in the mail template...
			 * Variables to be defined here for the mail template:
			 * 		$sx_send_to_email: The mail of the reciever
			 * 		$sx_mail_subject: The subject of mail
			 * 		$sx_mail_content: whatever with HTML formation
			 */

			$sx_mail_subject = 'Reset password';
			$sx_send_to_email = $sEmail;

			$sx_mail_content = '<h4>' . lngWhyThisRetrievEmail . '</h4>';
			$loginURL = sx_ROOT_HOST_PATH . '?pg=reset&token=' . $sxToken;
			$sx_mail_content .= '<p>' . lngClickNextLinkToSetNewPassword . '
				<a style="text-decoration: none;" href="' . $loginURL . '">' . lngClickHere . '</a></p>';

			require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
		}
	}
} ?>
<section>
	<?php
	if ($radioContinue) { ?>
		<h1><?= lngSuccessfulSending ?></h1>
		<p class="text"><?= LNG__EmailSentToChangePassword ?></p>
	<?php
	} else {
		$strFormToken = sx_generate_form_token('ResetForumPassword', 128);
	?>
		<h1><?= lngForgotPassword ?></h1>
		<p><?= lngIfForgetPasswordSendMail ?></p>
		<?php
		if (!empty($strError)) { ?>
			<p class="bg_error"><?= $strError ?></p>
		<?php
		} ?>
		<form name="SendPassword" action="<?= sx_PATH ?>?pg=forgot" method="post">
			<input type="hidden" name="FormToken" value="<?php echo $strFormToken ?>">
			<fieldset>
				<label><?= lngMyEmail ?>:</label>
				<input type="email" name="Email" value="<?= $sEmail ?>" style="width: 50%" required>
			</fieldset>
			<fieldset>
				<?php include "../sxPlugins/captcha/include.php" ?>
				<br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
				<div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
			</fieldset>
			<fieldset>
				<p class="align_center"><input type="submit" name="SendPW" value="<?= LNG_Form_Submit ?>"></p>
			</fieldset>
		</form>
	<?php
	} ?>
</section>