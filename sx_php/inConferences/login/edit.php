<?php

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
	header('Location: index.php');
	exit();
}

$strError = "";
$radioContinue = False;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioContinue = True;
	$strChangedData = "";
	$sql = "SELECT FirstName, LastName, Email, ChangedData FROM conf_participants WHERE ParticipantID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$int_ParticipantID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if (($rs)) {
		$strFirstName = $rs["FirstName"];
		$strLastName = $rs["LastName"];
		$strEmail = $rs["Email"];
		$strChangedData = $rs["ChangedData"];
	} else {
		$strError = lngUserNameNotFound . " ";
		$radioContinue = False;
	}
	$stmt = null;
	$rs = null;

	if (!empty($strChangedData)) {
		$strChangedData .=  "<br>";
	}
	$strChangedData = $strChangedData . date("Y-m-d") . " " . $strFirstName . " " . $strLastName . " " . $strEmail . " " . sx_UserIP;

	$radioChangedPassword = False;
	if ($radioContinue) {
		if (isset($_POST["Password"]) && !empty($_POST["Password"])) {
			$sPassword = trim($_POST["Password"]);
			$sPassword2 = trim($_POST["Password2"]);
			if ($sPassword != $sPassword2) {
				$strError = lngPasswordFieldsNotTheSame . " ";
				$radioContinue = False;
			} elseif (strlen($sPassword) < 8 || strlen($sPassword) > 64) {
				$strError = lngPasswordCharacters . " ";
				$radioContinue = False;
			} else {
				$PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
				$radioChangedPassword = True;
			}
		}
	}

	$radioChangedEmail = False;
	if ($radioContinue) {
		$sEmail = trim($_POST["Email"]);
		$CheckEmail = filter_var($sEmail, FILTER_VALIDATE_EMAIL);
		if ($CheckEmail == False) {
			$radioContinue = False;
			$strError = lngWriteCorrectEmail . " ";
		} elseif ($sEmail <> $strEmail) {
			$sql = "SELECT ParticipantID FROM conf_participants WHERE Email = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$sEmail]);
			$rs = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($rs) {
				$strError = LNG__EmailExists . " ";
				$radioContinue = False;
			} else {
				if (strlen($sEmail) > 64) {
					$radioContinue = False;
					$strError = LNG_Form_ExpectedLengthToLong;
				} else {
					$radioChangedEmail = true;
				}
			}
			$stmt = null;
			$rs = null;
		}
	}

	if ($radioContinue) {
		$sFirstName = "";
		$sLastName = "";
		if (!empty($_POST["FirstName"])) {
			$sFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
		}
		if (!empty($_POST["LastName"])) {
			$sLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
		}
		if (!empty($sFirstName) && !empty($sLastName)) {
			if (strlen($sFirstName) < 2 || strlen($sLastName) < 2) {
				$strError = LNG_Form_AsteriskFieldsRequired . " ";
				$radioContinue = false;
			}
			if (strlen($sFirstName) > 50 || strlen($sLastName) > 50) {
				$radioContinue = false;
				$strError = LNG_Form_ExpectedLengthToLong;
			}
		} else {
			$radioContinue = false;
		}
	}
	if ($radioContinue) {
		if (isset($_POST['Biography'])) {
			$mBiography = sx_Sanitize_Text_Area($_POST['Biography']);
		} else {
			$mBiography = "";
		}

		if (!empty($mBiography) & strlen($mBiography) > 1200) {
			$radioContinue = false;
			$strError = "The Biography text is too long (" . strlen($mBiography) . ")!";
		}
	}

	if ($radioContinue) {
		$sPhone = filter_var(trim($_POST["Phone"]), FILTER_SANITIZE_NUMBER_INT);
		$sAddress = "";
		if (!empty($_POST["Address"])) {
			$sAddress = sx_Sanitize_Input_Text($_POST["Address"]);
		}
		$sPostCode = "";
		if (!empty($_POST["PostCode"])) {
			$sPostCode = sx_Sanitize_Input_Text($_POST["PostCode"]);
		}
		$sCity = "";
		if (!empty($_POST["City"])) {
			$sCity = sx_Sanitize_Input_Text($_POST["City"]);
		}
		$sCountry = "";
		if (!empty($_POST["Country"])) {
			$sCountry = sx_Sanitize_Input_Text($_POST["Country"]);
		}

		if ((!empty($sPostCode) && strlen($sPostCode) > 10)
			|| (!empty($sCity) && strlen($sCity) > 30)
			|| (!empty($sCountry) && strlen($sCountry) > 30)
		) {
			$radioContinue = false;
			$strError = LNG_Form_ExpectedLengthToLong;
		}
	}

	if ($radioContinue) {

		$iEmailList = 0;
		if ($_POST["EmailList"]) {
			$iEmailList = 1;
		}
		$sql = "UPDATE conf_participants SET
			FirstName = ?,
			LastName = ?,
			PostAddress = ?,
			PostCode = ?,
			City = ?,
			Country = ?,
			Phone = ?,
			Email = ?,
			EmailList = ?,
			ChangedData = ?,
			Biography = ?
		WHERE ParticipantID = ? ";
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
			$iEmailList,
			$strChangedData,
			$mBiography,
			$int_ParticipantID
		]);

		if ($radioChangedPassword) {
			$sql = "UPDATE conf_participants SET
				LoginPassword = ?
				WHERE ParticipantID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$PW_Hash, $int_ParticipantID]);
		}

		$sql = "SELECT FirstName, LastName, Email
			FROM conf_participants
			WHERE ParticipantID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$int_ParticipantID]);
		$rs = $stmt->fetch(PDO::FETCH_NUM);
		if (($rs)) {
			$_SESSION["Part_FirstName"] = $sFirstName;
			$_SESSION["Part_LastName"] = $sLastName;
		}
		$stmt = null;
		$rs = null;

		header("Location: " . sx_PATH . "?pg=message&change=yes");
		exit();
	}
}

$aResults = null;
$sql = "SELECT FirstName, LastName, Email, Phone, 
		PostAddress, PostCode, City, Country, EmailList, Biography
	FROM conf_participants 
	WHERE ParticipantID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$int_ParticipantID]);
$rs = $stmt->fetch(PDO::FETCH_NUM);
if ($rs) {
	$aResults = $rs;
} else {
	header('Location: ' . sx_PATH . '?pg=message&error=UserNameNotFound');
	exit();
}
$stmt = null;
$rs = null;

?>

<h1 class="head"><span><?php echo lngEditProfile ?></span></h1>

<?php if ($strError != "") { ?>
	<div class="bg_error"><?= $strError ?></div>
<?php } ?>
<p><?= LNG_Form_AsteriskFieldsRequired . " " . lngNoCapitalInfo ?></p>

<form name="EditSubscription" action="<?= sx_PATH ?>?pg=edit" method="post" autocomplete="off">
	<fieldset>
		<label><?= lngName ?>: </label>
		<input type="text" name="FirstName" value="<?= $aResults[0] ?>" maxchars="30"> *
		<label><?= LNG__LastName ?>: </label>
		<input type="text" name="LastName" value="<?= $aResults[1] ?>" maxchars="30"> *
		<label><?= LNG__Email ?>: </label>
		<input type="text" name="Email" value="<?= $aResults[2] ?>" maxchars="30"> *
	</fieldset>

	<fieldset>
		<label><?= lngPhone ?>: </label>
		<input type="text" name="Phone" value="<?= $aResults[3] ?>" maxchars="30">
		<label><?= lngAddress ?>: </label>
		<input type="text" name="Address" value="<?= $aResults[4] ?>" maxchars="30">
		<label><?= lngPostalCode ?>: </label>
		<input type="text" name="PostCode" value="<?= $aResults[5] ?>" maxchars="30">
		<label><?= LNG__City ?>: </label>
		<input type="text" name="City" value="<?= $aResults[6] ?>" maxchars="30">
		<label><?= lngCountry ?>: </label>
		<input type="text" name="Country" value="<?= $aResults[7] ?>" maxchars="30">
	</fieldset>

	<?php if ($radio_AllowAddProfile) {
		$memoBiography = $aResults[9];
		if (!empty($memoBiography)) {
			$memoBiography = strip_tags(str_replace("</p>", "\r\n\r\n", $memoBiography));
		}
	?>
		<fieldset>
			<label><?= lngBiography ?>: <input name="entered" disabled type="text" size="4">
				<?= LNG_Form_EnterMaxCharacters . ": " . $i_MaxEmailLength ?> </label>
			<textarea spellcheck name="Biography" rows="8" onFocus="countEntries('EditSubscription','Biography',<?= $i_MaxEmailLength ?>);"><?= $memoBiography ?></textarea>
			<p class="text_xsmall"><?= LNG_Form_WritePureText ?></p>
		</fieldset>
	<?php
	} ?>

	<fieldset>
		<p class="text_small"><?= lngWritePasswordOnlyIfChanged ?></p>
		<label><?= lngPassword ?>: </label>
		<input type="password" name="Password" VALUE="" autocomplete="off" maxchars="20" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" />
		<label><?= lngRepeatPassword ?>:</label>
		<input type="password" name="Password2" VALUE="" autocomplete="off" maxchars="20" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" />
		<p class="text_small"><?= lngPasswordCharacters  ?></p>
	</fieldset>

	<fieldset>
		<p><input type="checkbox" name="EmailList" value="ON" <?php if ($aResults[8]) { ?> checked<?php } ?>> <?= lngAddEmailToList ?></p>
		<input type="Submit" name="EditAction" value="<?= lngUpdate ?>">
	</fieldset>
	<p>* <?= lngRequiredInfo ?></p>
</form>
<?php
$aResults = null;
?>