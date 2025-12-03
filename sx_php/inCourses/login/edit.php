<?php
$intStudentID = 0;
if (isset($_SESSION["Students_StudentID"]) && !empty($_SESSION["Students_StudentID"])) {
	$intStudentID = (int)$_SESSION["Students_StudentID"];
}

if (!isset($_SESSION["Students_" . sx_DefaultSiteLang]) || intval($intStudentID) == 0) {
	header('Location: ' . sx_PATH . '?pg=message&error=Timeout');
	exit();
}
$strError = "";
$radioContinue = False;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$radioContinue = True;
	$strChangedData = "";
	$sql = "SELECT FirstName, LastName, Email, IPAddress, ChangedData 
	FROM course_students 
	WHERE StudentID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intStudentID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if (($rs)) {
		$strFirstName = $rs["FirstName"];
		$strLastName = $rs["LastName"];
		$strEmail = $rs["Email"];
		$strIPAddress = $rs["IPAddress"];
		$strChangedData = $rs["ChangedData"];
	} else {
		$strError = lngUserNameNotFound . " ";
		$radioContinue = False;
	}
	$stmt = null;
	$rs = null;

	if (strlen($strChangedData) > 0) {
		$strChangedData .=  "<br>";
	}
	$strChangedData = $strChangedData . date("Y-m-d") . " " . $strFirstName . " " . $strLastName . " " . $strEmail . " " . $strIPAddress;

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
			} elseif ($sEmail <> $strEmail) {
				$sql = "SELECT StudentID
					FROM course_students
					WHERE Email = ?";
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
			if (strlen($sFirstName) < 2 || strlen($sLastName) < 2) {
				$strError = LNG_Form_AsteriskFieldsRequired . " ";
				$radioContinue = False;
			}
		} else {
			$strError = LNG_Form_AsteriskFieldsRequired . " ";
			$radioContinue = False;
		}
	}

	if ($radioContinue) {
		if (!empty($_POST["Title"]) && !empty($_POST["Institution"])) {
			$sTitle = sx_Sanitize_Input_Text($_POST["Title"]);
			$sInstitution = sx_Sanitize_Input_Text($_POST["Institution"]);
			if (strlen($sTitle) < 2 || strlen($sInstitution) < 2) {
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
		if (!empty($_POST["EmailList"]) && filter_var($_POST["EmailList"], FILTER_VALIDATE_BOOL)) {
			$iEmailList = 1;
		}
		$dEditDate = date('Y-m-d');

		$sql = "UPDATE course_students SET
			FirstName = ?, LastName = ?, 
			Title = ?, Institution = ?, 
			PostAddress = ?, PostCode = ?,
			City = ?, Country = ?, Phone = ?, Email = ?, EmailList = ?,
			IPAddress = ?, EditDate = ?, ChangedData = ?
			WHERE StudentID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([
			$sFirstName, $sLastName,
			$sTitle, $sInstitution,
			$sAddress, $sPostCode,
			$sCity, $sCountry, $sPhone, $sEmail, $iEmailList,
			sx_UserIP,  $dEditDate,  $strChangedData,
			$intStudentID
		]);

		if ($radioChangedPassword) {
			$sql = "UPDATE course_students SET
				LoginPassword = ?,
				ResetPassword = 0
				WHERE StudentID = ? ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$PW_Hash, $intStudentID]);
		}

		$sql = "SELECT FirstName, LastName, Email
			FROM course_students
			WHERE StudentID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intStudentID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$_SESSION["Students_" . sx_DefaultSiteLang] = True;
			$_SESSION["Students_StudentID"] = $intStudentID;
			$_SESSION["Students_FirstName"] = $rs["FirstName"];
			$_SESSION["Students_LastName"] = $rs["LastName"];
			$_SESSION["Students_Email"] = $rs["Email"];
		}
		$stmt = null;
		$rs = null;
		header("Location: " . sx_PATH . "?pg=message&change=yes");
		exit();
	}
}

$aResults = null;
$sql = "SELECT FirstName, LastName, Email, Title, Institution,
		PostAddress, PostCode, City, Country, Phone, EmailList 
	FROM course_students 
	WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$intStudentID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$aResults = $rs;
} else {
	/**
	 * Just in case someone is logged in while the account 
	 * has been deleted or dissactivated
	 */
	$_SESSION[] = array();
	session_destroy();
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
		<input type="text" name="FirstName" value="<?= $aResults['FirstName'] ?>" maxchars="30"> *
		<label><?= LNG__LastName ?>: </label>
		<input type="text" name="LastName" value="<?= $aResults['LastName'] ?>" maxchars="30"> *
		<label><?= LNG__Title ?>:</label>
		<input TYPE="text" NAME="Title" VALUE="<?= $aResults['Title']  ?>" MAXCHARS="60" required> *
		<label><?= lngInstitution ?>:</label>
		<input TYPE="text" NAME="Institution" VALUE="<?= $aResults['Institution']  ?>" MAXCHARS="60" required> *
		<label><?= LNG__Email ?>: </label>
		<input type="text" name="Email" value="<?= $aResults['Email'] ?>" maxchars="30"> *

		<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
		<input style="display:none" type="text" autocomplete="off" name="fakeusernameremembered" />
		<input style="display:none" type="password" autocomplete="off" name="fakepasswordremembered" />
	</fieldset>
	<fieldset>
		<label><?= lngAddress ?>: </label>
		<input type="text" name="Address" value="<?= $aResults['PostAddress'] ?>" maxchars="30">
		<label><?= lngPostalCode ?>: </label>
		<input type="text" name="PostCode" value="<?= $aResults['PostCode'] ?>" maxchars="30">
		<label><?= LNG__City ?>: </label>
		<input type="text" name="City" value="<?= $aResults['City'] ?>" maxchars="30">
		<label><?= lngCountry ?>: </label>
		<input type="text" name="Country" value="<?= $aResults['Country'] ?>" maxchars="30">
		<label><?= lngPhone ?>: </label>
		<input type="text" name="Phone" value="<?= $aResults['Phone'] ?>" maxchars="30">
	</fieldset>

	<fieldset>
		<p class="text_small"><?= lngWritePasswordOnlyIfChanged ?></p>
		<label><?= lngPassword ?>: </label>
		<input type="password" name="Password" VALUE="" autocomplete="off" maxchars="36" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
		<label><?= lngRepeatPassword ?>:</label>
		<input type="password" name="Password2" VALUE="" autocomplete="off" maxchars="36" required readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
		<p class="text_xsmall"><?= lngPasswordCharacters  ?></p>
	</fieldset>
	<fieldset>
		<p><input type="checkbox" name="EmailList" value="ON" <?php if ($aResults['EmailList']) { ?> checked<?php } ?>> <?= lngAddEmailToList ?></p>

		<input type="Submit" name="EditAction" value="<?= lngEdit ?>">
	</fieldset>
	<p>* <?= lngRequiredInfo ?></p>
</form>
<?php
$aResults = null;
?>