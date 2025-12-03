<?php
$intUserID = 0;
if (isset($_SESSION["Users_UserID"]) && !empty($_SESSION["Users_UserID"])) {
    $intUserID = (int)$_SESSION["Users_UserID"];
}

if ($radio__UserSessionIsActive === false || intval($intUserID) == 0) {
	header('Location: ' . sx_PATH . '?pg=message&error=Timeout');
	exit();
}
$strError = "";
$radioContinue = False;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioContinue = True;
	$strChangedData = "";
	$sql = "SELECT FirstName, LastName, UserEmail, IPAddress, ChangedData 
	FROM users 
	WHERE UserID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intUserID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if (($rs)) {
		$strFirstName = $rs["FirstName"];
		$strLastName = $rs["LastName"];
		$strUserEmail = $rs["UserEmail"];
		$strIPAddress = $rs["IPAddress"];
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
	$strChangedData = $strChangedData . date("Y-m-d") . " " . $strFirstName . " " . $strLastName . " " . $strUserEmail . " " . $strIPAddress;

	$radioChangedPassword = False;
	if ($radioContinue) {
		if (isset($_POST["Password"]) && !empty($_POST["Password"])) {
			$sPassword = trim($_POST["Password"]);
			$sPassword2 = trim($_POST["Password2"]);
			if ($sPassword != $sPassword2) {
				$strError = lngPasswordFieldsNotTheSame . " ";
				$radioContinue = False;
			} elseif (empty($sPassword) || strlen($sPassword) < 8 || strlen($sPassword) > 64) {
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
		if (!empty($_POST["Email"])) {
			$sEmail = trim($_POST["Email"]);
			$checkEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);
			if ($checkEmail == false) {
				$radioContinue = False;
				$strError = lngWriteCorrectEmail . " ";
			} elseif ($sEmail <> $strUserEmail) {
				$sql = "SELECT UserID FROM users WHERE UserEmail = ?";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$sEmail]);
				$rs = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($rs) {
					$strError = LNG__EmailExists . " ";
					$radioContinue = false;
				} else {
					$radioChangedEmail = true;
				}
				$stmt = null;
				$rs = null;
			}
		} else {
			$strError = LNG_Form_AsteriskFieldsRequired . " ";
			$radioContinue = False;
		}
	}

	if ($radioContinue) {
		if (!empty($_POST["FirstName"]) && !empty($_POST["LastName"])) {
			$sFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
			$sLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
			if (empty($sFirstName) || strlen($sFirstName) < 2 || empty($sLastName) || strlen($sLastName) < 2) {
				$strError = LNG_Form_AsteriskFieldsRequired . " ";
				$radioContinue = False;
			}
		} else {
			$strError = LNG_Form_AsteriskFieldsRequired . " ";
			$radioContinue = False;
		}
	}

	if ($radioContinue) {
		$sPhone = "";
		if (!empty($_POST["Phone"])) {
			$sPhone = sx_GetSanitizedPhone($_POST["Phone"]);
		}

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

		$iEmailList = 0;
		if (!empty($_POST["EmailList"]) && filter_var($_POST["EmailList"],FILTER_VALIDATE_BOOL)) {
			$iEmailList = 1;
		}
		$dEditDate = date('Y-m-d');

		$sql = "UPDATE users SET
			FirstName = ?, LastName = ?, UserAddress = ?, UserPostCode = ?,
			UserCity = ?, UserCountry = ?, UserPhone = ?, UserEmail = ?, EmailList = ?,
			IPAddress = ?, EditDate = ?, ChangedData = ?
			WHERE UserID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([
			$sFirstName, $sLastName, $sAddress, $sPostCode,
			$sCity, $sCountry, $sPhone, $sEmail, $iEmailList,
			sx_UserIP,  $dEditDate,  $strChangedData,
			$intUserID
		]);

		if ($radioChangedPassword) {
			$sql = "UPDATE users SET
				UserPassword = ?,
				SentPassword = 0
				WHERE UserID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$PW_Hash, $intUserID]);
		}

		$sql = "SELECT FirstName, LastName, UserEmail
			FROM users
			WHERE UserID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intUserID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
            $_SESSION["Users_" . $_SESSION["User_Token"]] = True;
			$_SESSION["Users_UserID"] = $intUserID;
            $_SESSION["Users_FirstName"] = $rs["FirstName"];
            $_SESSION["Users_LastName"] = $rs["LastName"];
            $_SESSION["Users_UserEmail"] = $rs["UserEmail"];
		}
		$stmt = null;
		$rs = null;
		header("Location: " . sx_PATH . "?pg=message&change=yes");
		exit();
	}
}

$aResults = null;
$sql = "SELECT FirstName, LastName, UserEmail, 
		UserPhone, UserAddress, UserPostCode, UserCity, UserCountry, EmailList 
	FROM users 
	WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$intUserID]);
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


<h1><?= lngChangeProfile ?></h1>
<?php if ($strError != "") { ?>
	<div class="bg_error"><?= $strError ?></div>
<?php } ?>
<p><?= LNG_Form_AsteriskFieldsRequired ?></p>

<form name="subscription" action="<?= sx_PATH ?>?pg=edit" method="post">
	<fieldset>
		<label><?= lngName ?>: </label>
		<input type="text" name="FirstName" value="<?= $aResults[0] ?>" maxchars="30"> *
		<label><?= LNG__LastName ?>: </label>
		<input type="text" name="LastName" value="<?= $aResults[1] ?>" maxchars="30"> *
		<label><?= LNG__Email ?>: </label>
		<input type="text" name="Email" value="<?= $aResults[2] ?>" maxchars="30"> *

		<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
		<input style="display:none" type="text" autocomplete="off" name="fakeusernameremembered" />
		<input style="display:none" type="password" autocomplete="off" name="fakepasswordremembered" />
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
	<fieldset>
		<label><?= lngPassword ?>: </label>
		<input type="password" autocomplete="new-password" name="Password" VALUE="" autocomplete="off" maxchars="36">
		<label><?= lngRepeatPassword ?>:</label>
		<input type="password" autocomplete="new-password" name="Password2" VALUE="" autocomplete="off" maxchars="36">
	</fieldset>
	<p class="text_xsmall"><?= lngPasswordCharacters  ?></p>
	<fieldset>
		<p><input type="checkbox" name="EmailList" value="ON" <?php if ($aResults[8]) { ?> checked<?php } ?>> <?= lngAddEmailToList ?></p>

		<input type="Submit" name="EditAction" value="<?= lngEdit ?>">
	</fieldset>
	<p>* <?= lngRequiredInfo ?></p>
</form>
<?php
$aResults = null;
?>