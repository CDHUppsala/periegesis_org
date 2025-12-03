<?php
$radioContinue = False;
$strError = "";
$sEmail = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $radioContinue = true;

    /**
     * Do not use captcha for login when Form Token is also used
     * Activate it only to prevent brute force attacks
     */
    if (sx_radio_UseUsersLoginCaptcha) {
        if (!isset($_POST['captcha_input']) || $_POST['captcha_input'] != $_SESSION['captcha_code']) {
            $radioContinue = false;
            $strError = LNG__CaptchaError;
        }
    }

    if (!isset($_POST['FormToken'])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Users Login: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("UsersLogin", $_POST["FormToken"])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Users Login: Wrong Token Hack-Attempt!");
    }

    if (isset($_POST["Email"]) && isset($_POST["Password"])) {
        $sEmail = trim($_POST["Email"]);
        $sPW = trim($_POST["Password"]);
        if (empty($sEmail) || empty($sPW) || strlen($sPW) < 8 || strlen($sPW) > 64 || filter_var($sEmail, FILTER_VALIDATE_EMAIL) == false) {
            $strError = lngWrongUserNameOrPassword;
            $radioContinue = False;
        }
    } else {
        $strError = lngWrongUserNameOrPassword;
        $radioContinue = False;
    }

    if ($radioContinue) {
        $strUserToken = return_Random_Alphanumeric(12);

        $sql = "SELECT UserID, FirstName, LastName, UserEmail, UserPassword, SentPassword
            FROM users
            WHERE UserEmail = ?
            AND AllowAccess = True ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sEmail]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rs) {
            $_SESSION[] = array();  // unset data in $_SESSION
            session_destroy();             // destroy the session
            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        } elseif (password_verify($sPW, $rs["UserPassword"])) {
            $_SESSION["User_Token"] = $strUserToken;
            $_SESSION["Users_" . $strUserToken] = True;
            $_SESSION["Users_UserID"] = $rs["UserID"];
            $_SESSION["Users_FirstName"] = $rs["FirstName"];
            $_SESSION["Users_LastName"] = $rs["LastName"];
            $_SESSION["Users_UserEmail"] = $rs["UserEmail"];
            if ($rs["SentPassword"]) {
                header('Location: login.php?pg=edit');
                exit();
            } else {
                header('Location: login.php?pg=message&welcome=login');
                exit();
            }
        } else {
            $_SESSION[] = array();  // unset data in $_SESSION
            session_destroy();             // destroy the session

            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        }
        $stmt = null;
        $rs = null;
    } else {
        sleep(3);
    }
} ?>

<h1><?= $strUsersLoginTitle ?></h1>
<?php
if (!empty($strError)) { ?>
    <p class="bg_error"><?= $strError ?></p>
<?php
}
if (isset($_GET['msg'])) { ?>
    <p class="bg_success"><?= $strUsersWelcomeTitle ?></p>
<?php
}

?>
<div class="formWrap">
    <form name="LoginForma" action="<?= sx_LOCATION ?>" METHOD="POST">
        <input type="hidden" name="FormToken" value="<?= sx_generate_form_token('UsersLogin', 64) ?>">
        <fieldset>
            <label><?= LNG__Email ?>:</label>
            <input TYPE="email" NAME="Email" VALUE="<?= $sEmail ?>" style="width: 50%" required>
            <label><?= lngPassword ?>:</label>
            <input TYPE="password" NAME="Password" VALUE="" style="width: 50%" MAXCHARS="25" required>
        </fieldset>
        <?php if (sx_radio_UseUsersLoginCaptcha) { ?>
            <fieldset>
                <?php include "../sxPlugins/captcha/include.php" ?>
                <br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
                <div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
            </fieldset>
        <?php } ?>
        <fieldset>
            <p class="align_right"><input TYPE="Submit" NAME="LoginAction" VALUE="<?= lngLogin ?>"></p>
        </fieldset>
    </form>
    <?= $memoUsersLoginNote ?>
</div>