<?php
$strError = "";
if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $strResetToken = "";
    if (isset($_GET["token"])) {
        $strResetToken = sx_Sanitize_Search_Text($_GET["token"]);
    }

    if (
        empty($strResetToken)
        || strlen($strResetToken) > 512
        || strlen($strResetToken) < 48
        || strpos($strResetToken, " ") > 0
    ) {
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['ResetToken'] = $strResetToken;
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['ResetToken'])) {

    $radioValidToken = true;
    if (empty($_POST['FormToken'])) {
        $radioValidToken = false;
        write_To_Log("Students Reset Password: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("ParticipantPasswordReset", $_POST["FormToken"])) {
        $radioValidToken = false;
        write_To_Log("Students Reset Password: Wrong Token Hack-Attempt!");
    }

    if ($radioValidToken == false) {
        header('Location: index.php');
        exit;
    }

    $radioContinue = true;
    if (empty($_POST["Password"]) || empty($_POST["Password2"])) {
        $radioContinue = false;
        $strError = lngPasswordCharacters;
    } else {
        $sPassword = trim(@$_POST["Password"]);
        $sPassword2 = trim(@$_POST["Password2"]);
        if ($sPassword != $sPassword2) {
            $radioContinue = false;
            $strError = lngPasswordFieldsNotTheSame;
        } elseif (empty($sPassword) || strlen($sPassword) < 8 || strlen($sPassword) > 64) {
            $radioContinue = false;
            $strError = lngPasswordCharacters;
        } else {
            $PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
        }
    }

    if ($radioContinue) {
        // Rest Token is valid only one day after the request for reset
        $dCompareDate = return_Add_To_Date(date('Y-m-d H:i:s'), -1);
        $intResetID = 0;
        $sql = "SELECT ResetID, MemberID, MemberEmail
			FROM password_reset 
			WHERE IsValid = 1
			AND RecoveryToken = ?
			AND InsertDate >= ?
            AND PageURL = ?
			LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['ResetToken'], $dCompareDate, sx_PATH]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs) {
            $intResetID = $rs["ResetID"];
            $i_MemberID = $rs["MemberID"];
            $s_MemberEmail = $rs["MemberEmail"];
        } else {
            $radioContinue = false;
        }
        $stmt = null;
        $rs = null;

        /**
         * Unset session here, if records are not found above or bellow,
         *   it will not be reused (withought resending the link from email).
         */
        unset($_SESSION["ResetToken"]);
    }

    if ($radioContinue) {
        $strChangedData = "";
        $sql = "SELECT ChangedData
			FROM conf_participants 
			WHERE AllowAccess = 1
			AND ParticipantID = ?
			AND Email = ?
			LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$i_MemberID, $s_MemberEmail]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs) {
            $strChangedData = $rs["ChangedData"];
        } else {
            $radioContinue = false;
        }
        $stmt = null;
        $rs = null;
    }

    if ($radioContinue) {
        if (!empty($strChangedData) > 0) {
            $strChangedData .=  "<br>";
        }
        $strChangedData .= date("Y-m-d") . " Resetting Password "  . sx_UserIP;

        $sql = "UPDATE conf_participants SET 
			ResetPassword = 0,
			LoginPassword = ?,
			EditDate = ?,
			ChangedData = ?
			WHERE ParticipantID = ? ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$PW_Hash, date('Y-m-d'), $strChangedData, $i_MemberID]);

        $sql = "UPDATE password_reset SET
                IsValid = 0,
			    RecoveryToken = ''
			WHERE ResetID = ? ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$intResetID]);
    }

    if ($radioContinue) {
        header('Location: conferences_login.php?pg=message&change=yes');
        exit();
    } elseif (empty($strError)) {
        // record not found in database - redirect with general error message
        sleep(3);
        header('Location: conferences_login.php?pg=message&error=UserNameNotFound');
        exit();
    }
    // if error in checking password reset, just continue to reload the form
} else {
    $strError = lngSessionTimeout;
}

$strFormToken = sx_generate_form_token('ParticipantPasswordReset', 128);

?>
<h1><?= lngSetNewPassword ?></h1>
<?php if (!empty($strError)) { ?>
    <p class="bg_error"><?= $strError ?></p>
<?php } ?>
<form name="ResetPasswordForm" action="<?= sx_PATH ?>?pg=reset" METHOD="POST">
    <input type="hidden" name="FormToken" value="<?php echo $strFormToken ?>">
    <fieldset>
        <label><?= lngPassword ?>:</label>
        <input TYPE="password" NAME="Password" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
        <label><?= lngRepeatPassword ?>:</label>
        <input TYPE="password" NAME="Password2" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
        <p><?= lngPasswordCharacters  ?></p>
    </fieldset>

    <fieldset>
        <p class="align_center"><input type="Submit" name="LoginAction" value="<?= lngLogin ?>"></p>
    </fieldset>
</form>