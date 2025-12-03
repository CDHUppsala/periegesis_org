<?php
/**
 * This page opens first be a $_GET request from a 
 *   mail link including a Reset Token to reset password
 * - Save the Reset Token in session
 * - Use it with the second, $_POST request to check in password_reset
 *   table and get neccessary information about the user
 */
$strError = "";
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $strResetToken = "";
    if (isset($_GET["token"])) {
        $strResetToken = sx_Sanitize_Input_Text($_GET["token"]);
    }

    if (empty($strResetToken) || strlen($strResetToken) < 48 || strpos($strResetToken, " ") !== false) {
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['ResetToken'] = $strResetToken;
    }
} elseif (!empty($_SESSION['ResetToken'])) {

    $radioValidToken = true;
    if (empty($_POST['FormToken'])) {
        $radioValidToken = false;
        write_To_Log("Forum Reset Password: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("ForumResetPassword", $_POST["FormToken"])) {
        $radioValidToken = false;
        write_To_Log("Forum Reset Password: Wrong Token Hack-Attempt!");
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
        $sPassword = trim($_POST["Password"]);
        $sPassword2 = trim($_POST["Password2"]);
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
        // Reset Token is valid only one day after the request for reset
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
			FROM forum_members 
			WHERE AllowAccess = 1
                AND SentPassword = 1
    			AND UserID = ?
    			AND UserEmail = ?
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
        if (!empty($strChangedData)) {
            $strChangedData .=  "<br>";
        }
        $strChangedData .= date("Y-m-d") . " Resetting Password "  . sx_UserIP;

        $sql = "UPDATE forum_members SET 
			SentPassword = 0,
			UserPassWord = ?,
			EditDate = ?,
			ChangedData = ?
			WHERE UserID = ? ";
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
        header('Location: ' . sx_ROOT_HOST_PATH . '?pg=message&request=change');
        exit();
    } elseif (empty($strError)) {
        // record not found in database - redirect with general error message
        header('Location: ' . sx_ROOT_HOST_PATH . '?pg=message&error=UserNameNotFound');
        exit();
    }
    // if error in new password, just continue to reload the form

} else {
    $strError = lngSessionTimeout;
}
?>
<h1><?= lngSetNewPassword ?></h1>
<?php if (!empty($strError)) { ?>
    <p class="bg_error"><?= $strError ?></p>
<?php } ?>
<form name="ResetPasswordForm" action="<?= sx_PATH ?>?pg=reset" METHOD="POST">
    <input type="hidden" name="FormToken" value="<?= sx_generate_form_token('ForumResetPassword', 64) ?>">
    <fieldset>
        <label><?= lngPassword ?>:</label>
        <input TYPE="password" autocomplete="new-password" NAME="Password" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
        <label><?= lngRepeatPassword ?>:</label>
        <input TYPE="password" autocomplete="new-password" NAME="Password2" pattern=".{8,32}" title="Must contain at least 8 and max 32 characters" required MAXCHARS="32"> *
        <p><?= lngPasswordCharacters  ?></p>
    </fieldset>

    <fieldset>
        <p class="align_center"><input type="Submit" name="LoginAction" value="<?= lngLogin ?>"></p>
    </fieldset>
</form>