<?php
$strError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioContinue = true;
	if (!isset($_POST['FormToken'])) {
		$radioContinue = False;
		write_To_Log("Login Participants: Empty Token Hack-Attempt!");
	} elseif (!sx_valid_form_token("LoginParticipants", $_POST["FormToken"])) {
		$radioContinue = False;
		write_To_Log("Login Participants: Wrong Token Hack-Attempt!");
	}

	if ($radioContinue) {
		$sEmail = null;
		$sPW = null;
		if (isset($_POST["Email"])) {
			$sEmail = trim($_POST['Email']);
			$CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
			if ($CheckEmail == False) {
				$strError = lngWrongUserNameOrPassword;
				$radioContinue = False;
			}
		} else {
			$strError = lngWrongUserNameOrPassword;
			$radioContinue = False;
		}

		if (isset($_POST["Password"])) {
			$sPW = trim($_POST['Password']);
		} else {
			$strError = lngWrongUserNameOrPassword;
			$radioContinue = False;
		}

		if (empty($sEmail) || strlen($sEmail) < 8 || empty($sPW) || strlen($sPW) < 8) {
			$strError = lngWrongUserNameOrPassword;
			$radioContinue = False;
		}
	}

	if ($radioContinue) {
		$iLoginTimes = 0;
		$sql = "SELECT ParticipantID, 
				FirstName, LastName, LoginPassword, LoginTimes
			FROM conf_participants 
			WHERE Email = ? 
			AND AllowAccess = 1 ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$sEmail]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$sParticipantID = $rs["ParticipantID"];
			$sFirstName = $rs["FirstName"];
			$sLastName = $rs["LastName"];
			$sParticipantsPW = $rs["LoginPassword"];
			$iLoginTimes = $rs["LoginTimes"];
		} else {
			$strError = lngWrongUserNameOrPassword;
			$radioContinue = false;
		}
		$stmt = null;
		$rs = null;
	}


	if ($radioContinue) {
		if (password_verify($sPW, $sParticipantsPW)) {
			session_start();
			session_unset();
			$_SESSION["Participants_" . sx_HOST] = true;
			$_SESSION["ParticipantID_" . sx_HOST] = $sParticipantID;
			$_SESSION["Part_FirstName"] = $sFirstName;
			$_SESSION["Part_LastName"] = $sLastName;
		} else {
			$strError = lngWrongUserNameOrPassword;
			$radioContinue = false;
		}
	}

	if ($radioContinue) {
		$iLoginTimes = intval($iLoginTimes) + 1;
		$sql = "UPDATE conf_participants SET 
			LastLoginDate = ?,
			LoginTimes = ? 
			WHERE ParticipantID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([date("Y-m-d"), $iLoginTimes, $_SESSION["ParticipantID"]]);
	}

	if ($radioContinue) {
		header('Location: '. sx_PATH.'?pg=message&loged=yes');
	} else {
		sleep(3);
	}
}
?>
<h1 class="head"><span><?php echo $strLoginTitle ?></span></h1>
<?php if (!empty($strError)) { ?>
	<p class="bg_error"><?= $strError ?></p>
<?php
} ?>
<div class="text text_bg"><div class="text_max_width"><?= $memoLoginNote ?></div></div>
<form name="LoginParticipants" action="<?= sx_PATH ?>?pg=login" METHOD="POST">
	<input type="hidden" name="FormToken" value="<?= sx_generate_form_token('LoginParticipants', 64) ?>">
	<fieldset>
		<label><?= LNG__Email ?>:</label>
		<input type="email" name="Email" value="" required>
		<label><?= lngPassword ?>:</label>
		<input type="password" name="Password" value="" MAXCHARS="48" required>
	</fieldset>
	<fieldset>
		<p class="align_center"><input type="Submit" name="LoginAction" value="<?= lngLogin ?>"></p>
	</fieldset>
</form>
