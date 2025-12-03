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
    if (sx_radio_UseStudentsLoginCaptcha) {
        if (empty($_POST['captcha_input']) || $_POST['captcha_input'] != $_SESSION['captcha_code']) {
            $radioContinue = false;
            $strError = LNG__CaptchaError;
        }
    }

    if (empty($_POST['FormToken'])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Students Login: Empty Token Hack-Attempt!");
    } elseif (!sx_valid_form_token("StudentsLogin", $_POST["FormToken"])) {
        $radioContinue = false;
        $strError = LNG__CaptchaError;
        write_To_Log("Students Login: Wrong Token Hack-Attempt!");
    }

    if (!empty($_POST["Email"]) && !empty($_POST["Password"])) {
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
        $radioLogged = false;
        $sql = "SELECT StudentID, FirstName, LastName, Email, 
                LoginPassword, ResetPassword
            FROM course_students
            WHERE Email = ?
            AND AllowAccess = 1 ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sEmail]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rs) {
            unset($_SESSION["Students_" . sx_DefaultSiteLang]);
            unset($_SESSION["Students_StudentID"]);
            unset($_SESSION["Students_FirstName"]);
            unset($_SESSION["Students_LastName"]);
            unset($_SESSION["Students_Email"]);
            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        } elseif (password_verify($sPW, $rs["LoginPassword"])) {
            $_SESSION["Students_" . sx_DefaultSiteLang] = True;
            $_SESSION["Students_StudentID"] = $rs["StudentID"];
            $_SESSION["Students_FirstName"] = $rs["FirstName"];
            $_SESSION["Students_LastName"] = $rs["LastName"];
            $_SESSION["Students_Email"] = $rs["Email"];
            $radioLogged = true;
            $radioResetPassword = $rs["ResetPassword"];
        } else {
            unset($_SESSION["Students_" . sx_DefaultSiteLang]);
            unset($_SESSION["Students_StudentID"]);
            unset($_SESSION["Students_FirstName"]);
            unset($_SESSION["Students_LastName"]);
            unset($_SESSION["Students_Email"]);
            $strError = lngWrongUserNameOrPassword;
            sleep(3);
        }
        $stmt = null;
        $rs = null;
        if ($radioLogged) {
            if ($radioResetPassword) {
                $sql = "UPDATE course_students SET
				    ResetPassword = 0
    				WHERE StudentID = ? ";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["Students_StudentID"]]);
                header('Location: courses_login.php?pg=edit');
                exit();
            } else {
                header('Location: courses_login.php?pg=message&welcome=login');
                exit();
            }
        }
    } else {
        sleep(3);
    }
} ?>

<h1><?= $str_StudentsLoginTitle ?></h1>
<?php
if (!empty($strError)) { ?>
    <p class="bg_error"><?= $strError ?></p>
<?php
}
if (isset($_GET['msg'])) { ?>
    <p class="bg_success"><?= $str_StudentsWelcomeTitle ?></p>
<?php
}

?>
<div class="formWrap">
    <form name="LoginForma" action="<?= sx_LOCATION ?>" METHOD="POST">
        <input type="hidden" name="FormToken" value="<?= sx_generate_form_token('StudentsLogin', 64) ?>">
        <fieldset>
            <label><?= LNG__Email ?>:</label>
            <input TYPE="email" NAME="Email" VALUE="<?= $sEmail ?>" style="width: 50%" required>
            <label><?= lngPassword ?>:</label>
            <input TYPE="password" NAME="Password" VALUE="" style="width: 50%" MAXCHARS="25" required>
        </fieldset>
        <?php if (sx_radio_UseStudentsLoginCaptcha) { ?>
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
    <article class="text">
        <div class="text_max_width">
            <?= $memo_StudentsLoginNotes ?>
        </div>
    </article>
</div>