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
    if (sx_radio_UseForumLoginCaptcha) {
        if (!isset($_POST['captcha_input']) || $_POST['captcha_input'] != $_SESSION['captcha_code']) {
            $radioContinue = false;
            $strError = LNG__CaptchaError;
        }
    }

    if (!isset($_POST['FormToken'])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Forum Login: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("ForumLogin", $_POST["FormToken"])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Forum Login: Wrong Token Hack-Attempt!");
    }

    if (isset($_POST["Email"]) && isset($_POST["Password"])) {
        $sEmail = trim($_POST["Email"]);
        $sPW = trim($_POST["Password"]);
        if (empty($sEmail) || empty($sPW) || filter_var($sEmail, FILTER_VALIDATE_EMAIL) == false) {
            $strError = lngWrongUserNameOrPassword;
            $radioContinue = False;
        }
    } else {
        $strError = lngWrongUserNameOrPassword;
        $radioContinue = False;
    }

    if ($radioContinue) {
        $sql = "SELECT UserID, FirstName, LastName, UserEmail, UserPassword, SentPassword
            FROM forum_members
            WHERE UserEmail = ?
            AND AllowAccess = True ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sEmail]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rs) {
            unset($_SESSION["Forum_" . sx_HOST]);
            unset($_SESSION["Forum_UserID"]);
            unset($_SESSION["Forum_FirstName"]);
            unset($_SESSION["Forum_LastName"]);
            unset($_SESSION["Forum_UserEmail"]);
            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        } elseif (password_verify($sPW, $rs["UserPassword"])) {
            $_SESSION["Forum_" . sx_HOST] = True;
            $_SESSION["Forum_UserID"] = $rs["UserID"];
            $_SESSION["Forum_FirstName"] = $rs["FirstName"];
            $_SESSION["Forum_LastName"] = $rs["LastName"];
            $_SESSION["Forum_UserEmail"] = $rs["UserEmail"];
            if ($rs["SentPassword"]) {
                header('Location: forum_login.php?pg=edit');
                exit();
            } else {
                header('Location: forum.php');
                exit();
            }
        } else {
            unset($_SESSION["Forum_" . sx_HOST]);
            unset($_SESSION["Forum_UserID"]);
            unset($_SESSION["Forum_FirstName"]);
            unset($_SESSION["Forum_LastName"]);
            unset($_SESSION["Forum_UserEmail"]);
            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        }
        $stmt = null;
        $rs = null;
    } else {
        sleep(3);
    }
} ?>

<h1><?= $strLoginTitle ?></h1>
<?php
if (!empty($strError)) { ?>
    <p class="bg_error"><?= $strError ?></p>
<?php
}
if (isset($_GET['welcome'])) { ?>
    <p class="bg_success"><?= lngWelcomeToForum ?></p>
<?php
}

?>
<section>
    <div class="formWrap">
        <form name="LoginForma" action="<?= sx_LOCATION ?>" METHOD="POST">
            <input type="hidden" name="FormToken" value="<?= sx_generate_form_token('ForumLogin', 64) ?>">
            <fieldset>
                <label><?= LNG__Email ?>:</label>
                <input TYPE="email" NAME="Email" VALUE="<?= $sEmail ?>" style="width: 50%" required>
                <label><?= lngPassword ?>:</label>
                <input TYPE="password" NAME="Password" VALUE="" style="width: 50%" MAXCHARS="25" required>
            </fieldset>
            <?php if (sx_radio_UseForumLoginCaptcha) { ?>
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
        <?= $memoLoginNote ?>
    </div>
</section>