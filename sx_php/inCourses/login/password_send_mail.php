<?php

$strError = "";
$iStudentID = 0;
$sEmail = "";
$radioContinue = False;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioValidToken = true;
	if (!isset($_POST['FormToken'])) {
		$radioValidToken = false;
		write_To_Log("Students Send Password: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("UsserResetPassword", $_POST["FormToken"])) {
		$radioValidToken = false;
		write_To_Log("Students Send Password: Wrong Token Hack-Attempt!");
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
			$sql = "SELECT StudentID 
			    FROM course_students
			    WHERE Email = ? 
				AND AllowAccess = True ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$sEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$iStudentID = $rs["StudentID"];
			} else {
				$strError = LNG__EmailDoesNotExist;
				$radioContinue = True;
			}
			$stmt = null;
			$rs = null;
		}
		if ($radioContinue) {
			if (intval($iStudentID) == 0) {
				$iStudentID = 0;
				$radioContinue = false;
			}
		}
		if ($radioContinue) {

			$sxToken = return_Random_Token(48);
			$sql = "INSERT INTO password_reset
				(MemberID, MemberEmail, RecoveryToken, IsValid, PageURL)
				VALUES (?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iStudentID, $sEmail, $sxToken, 1, sx_PATH]);

			$sql = "UPDATE course_students SET 
				ResetPassword = 1
			WHERE StudentID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iStudentID]);

			//Send mail

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

$strFormToken = sx_generate_form_token('UsserResetPassword', 128);

if ($radioContinue) { ?>
	<h1><?= lngSuccessfulSending ?></h1>
	<p class="text"><?= LNG__EmailSentToChangePassword ?></p>
<?php
} else { ?>
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
			<input type="email" name="Email" value="<?= $sEmail ?>" required />
		</fieldset>
		<fieldset>
			<?php
			include "../sxPlugins/captcha/include.php"
			?>
			<br><input class="captcha_input" type="text" name="captcha_input" value="" required />
			<p class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></p>
		</fieldset>
		<fieldset>
			<p class="align_center"><input type="submit" name="SendPW" value="<?= LNG_Form_Submit ?>"></p>
		</fieldset>
	</form>
<?php
} ?>