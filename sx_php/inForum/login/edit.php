<?php
$intUserID = 0;
if (isset($_SESSION["Forum_UserID"]) && !empty($_SESSION["Forum_UserID"])) {
	$intUserID = (int)$_SESSION["Forum_UserID"];
}

/**
 * The member of forum must login in order to change the profile of its membership.
 * Keep the original session names/keys in this page
 *   in case Forum is also accessible by User login
 */

if ($_SESSION["Forum_" . sx_HOST] === false || intval($intUserID) == 0) {
	header('Location: ' . sx_PATH . '?pg=message&error=Timeout');
	exit();
}
$arrError = array();
$radioContinue = False;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioContinue = True;
	$strChangedData = "";

	$sql = "SELECT FirstName, LastName, UserEmail, IPAddress, ChangedData 
	FROM forum_members 
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
		$arrError[] = lngUserNameNotFound;
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
		if (!empty($_POST["Password"]) && !empty($_POST["Password2"])) {
			$sPassword = trim($_POST["Password"]);
			$sPassword2 = trim($_POST["Password2"]);
			if ($sPassword != $sPassword2) {
				$arrError[] = lngPasswordFieldsNotTheSame;
				$radioContinue = False;
			} elseif (empty($sPassword) || strlen($sPassword) < 8 || strlen($sPassword) > 64) {
				$arrError[] = lngPasswordCharacters;
				$radioContinue = False;
			} else {
				$PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
				if ($PW_Hash) {
					$radioChangedPassword = true;
				} else {
					$arrError[] = lngPasswordCharacters;
					$radioContinue = False;
				}
			}
		}

		if (!empty($_POST["Email"])) {
			$sEmail = trim($_POST["Email"]);
			$checkEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);
			if ($checkEmail == false) {
				$radioContinue = False;
				$arrError[] = lngWriteCorrectEmail;
			} elseif ($sEmail <> $strUserEmail) {
				$sql = "SELECT UserID FROM forum_members WHERE UserEmail = ?";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$sEmail]);
				$rs = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($rs) {
					$arrError[] = LNG__EmailExists;
					$radioContinue = false;
				}
				$stmt = null;
				$rs = null;
			}
		} else {
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
			$radioContinue = False;
		}

		if (!empty($_POST["FirstName"]) && !empty($_POST["LastName"])) {
			$sFirstName = sx_Sanitize_Input_Text($_POST["FirstName"]);
			$sLastName = sx_Sanitize_Input_Text($_POST["LastName"]);
			if (empty($sFirstName) || strlen($sFirstName) < 2 || empty($sLastName) || strlen($sLastName) < 2) {
				$arrError[] = LNG_Form_AsteriskFieldsRequired;
				$radioContinue = False;
			} elseif (strlen($sFirstName) > 45 || strlen($sLastName) > 45) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		} else {
			$arrError[] = LNG_Form_AsteriskFieldsRequired;
			$radioContinue = False;
		}

		$sPhone = "";
		if (!empty($_POST["Phone"])) {
			$sPhone = sx_Return_Number_Space($_POST["Phone"]);
			if (strlen($sPhone) > 12) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		}
		$sAddress = "";
		if (!empty($_POST["Address"])) {
			$sAddress = sx_Sanitize_Input_Text($_POST["Address"]);
			if (strlen($sAddress) > 45) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		}
		$sPostCode = "";
		if (!empty($_POST["PostCode"])) {
			$sPostCode = sx_Return_Number_Space($_POST["PostCode"], true);
			if (strlen($sPostCode) > 9) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		}
		$sCity = "";
		if (!empty($_POST["City"])) {
			$sCity = sx_Sanitize_Input_Text($_POST["City"]);
			if (strlen($sCity) > 45) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		}
		$sCountry = "";
		if (!empty($_POST["Country"])) {
			$sCountry = sx_Sanitize_Input_Text($_POST["Country"]);
			if (strlen($sCountry) > 45) {
				$arrError[] = LNG_Form_ExpectedLengthToLong;
				$radioContinue = False;
			}
		}

		$iEmailList = 0;
		if (!empty($_POST["EmailList"]) && filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
			$iEmailList = 1;
		}
	}

	if ($radioContinue) {

		/**
		 * Update all except password
		 */
		$sql = "UPDATE forum_members SET
			FirstName = ?, LastName = ?, UserAddress = ?, UserPostCode = ?,
			UserCity = ?, UserCountry = ?, UserPhone = ?, UserEmail = ?, EmailList = ?,
			IPAddress = ?, EditDate = ?, ChangedData = ?
			WHERE UserID = ? ";
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
			sx_UserIP,
			date('Y-m-d'),
			$strChangedData,
			$intUserID
		]);

		/**
		 * Update password, if any
		 */
		if ($radioChangedPassword) {
			$sql = "UPDATE forum_members SET
				UserPassword = ?,
				SentPassword = 0
				WHERE UserID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$PW_Hash, $intUserID]);
		}

		/**
		 * Get new records, Update sessions and exit
		 */
		$sql = "SELECT FirstName, LastName, UserEmail
			FROM forum_members
			WHERE UserID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intUserID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$_SESSION["Forum_FirstName"] = $rs["FirstName"];
			$_SESSION["Forum_LastName"] = $rs["LastName"];
			$_SESSION["Forum_UserEmail"] = $rs["UserEmail"];
		}
		$stmt = null;
		$rs = null;
		header("Location: " . sx_PATH . "?pg=message&request=change");
		exit();
	}
}

/**
 * ========================================
 * Get user profile to populate form inputs
 * ========================================
 */
$aResults = null;
$sql = "SELECT FirstName, LastName, UserEmail, 
		UserPhone, UserAddress, UserPostCode, UserCity, UserCountry, EmailList 
	FROM forum_members 
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
<section>
	<h1><?= lngChangeProfile ?>mm</h1>
	<?php if (!empty($arrError)) { ?>
		<div class="bg_error"><?php echo implode('<br>', $arrError) ?></div>
	<?php } ?>
	<p><?= LNG_Form_AsteriskFieldsRequired ?></p>
	<form name="subscription" action="<?= sx_PATH ?>?pg=edit" method="post">
		<fieldset>
			<label><?= lngName ?>: </label>
			<input type="text" name="FirstName" value="<?php echo $aResults[0] ?>" maxchars="30">*
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
		<p><?= lngPasswordCharacters  ?></p>
		<fieldset>
			<p><input type="checkbox" name="EmailList" value="ON" <?php if ($aResults[8]) { ?> checked<?php } ?>> <?= lngAddEmailToList ?></p>

			<input type="Submit" name="EditAction" value="<?= lngEdit ?>">
		</fieldset>
		<p>* <?= lngRequiredInfo ?></p>
	</form>
</section>
<?php
/*
echo '<pre>';
print_r($aResults);
echo '</pre>';

$mobile = '0046 763   765 44';
echo  preg_replace("/[^0-9]/", '', $mobile);
echo '<br>';
echo  preg_replace("/[^0-9]+\s/", '', $mobile);
*/

$aResults = null;

?>