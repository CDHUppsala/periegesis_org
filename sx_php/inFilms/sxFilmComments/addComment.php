<?php

// Hidden text area must be empty
if (!empty($_POST["TextMessage"])) {
    header("Location: index.php?sent=yes");
    exit;
}

$radioFormError = False;
$checkMsg = "";
$strReadOnly = "";
$strAddFirstName = "";
$strAddLastName = "";
$strAddEmail = "";
$strAddTitle = "";
$strAddMainText = "";

if ($radio__UserSessionIsActive) {
    $strReadOnly = "readonly ";
    $strAddFirstName = $_SESSION["Users_FirstName"];
    $strAddLastName = $_SESSION["Users_LastName"];
    $strAddEmail = $_SESSION["Email"];
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['FirstName'])) {
        $strAddFirstName = htmlspecialchars(trim($_POST['FirstName']));
    }
    if (isset($_POST['LastName'])) {
        $strAddLastName = htmlspecialchars(trim($_POST['LastName']));
    }
    if (isset($_POST['Email'])) {
        $strAddEmail = trim($_POST["Email"]);
        $CheckFrom = filter_var($strAddEmail, FILTER_VALIDATE_EMAIL);
        if ($CheckFrom == False) {
            $strAddEmail = "";
        }
    }
}

$radioValidCaptcha = true;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Title'])) {
        $strAddTitle = htmlspecialchars($_POST['Title']);
    }
    if (isset($_POST['TextBody'])) {
        $strAddMainText = sx_Sanitize_Text_Area_Rows($_POST['TextBody']);
    }
    if ($radio_UseCaptcha) {
        $radioValidCaptcha = false;
        if (isset($_POST['captcha_input'])) {
            if ($_POST['captcha_input'] == $_SESSION['captcha_code']) {
                $radioValidCaptcha = true;
            }
        }
    }
}


$radioSent = False;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $radioValidCaptcha && intval($iFilmID) > 0 && $strFormType == "blog") {

    $checkMsg = null;
    if ($radio__UserSessionIsActive === false) {
        $strAddFirstName = strtoupper(substr($strAddFirstName, 1)) . strtolower(substr($strAddFirstName, 1, (strlen($strAddFirstName) - 1)));
        $strAddLastName = strtoupper(substr($strAddLastName, 1)) . strtolower(substr($strAddLastName, 1, (strlen($strAddLastName) - 1)));
    }
    $strAddMainText = sx_ParagraphBreaks($strAddMainText);
    $insertDate = date("Y-m-d");

    if (strlen($strAddFirstName) == 0 || strlen($strAddLastName) == 0 || strlen($strAddEmail) < 6 || strlen($strAddTitle) < 5 || strlen($strAddMainText) < 5) {
        $radioFormError = True;
        $checkMsg = LNG_Form_AsteriskFieldsRequired;
    }
    if (strlen($strAddMainText) > intval($i_MaxCommentLength + 100)) {
        $radioFormError = True;
        $checkMsg = lngMsgContains . " " . strlen($strAddMainText) . " " . lngOfMaxCharactersAllowed . " " . $i_MaxCommentLength;
    }
    if (strpos($strAddEmail, "@") == 0 || strpos($strAddEmail, ".") == 0) {
        $radioFormError = True;
        $checkMsg = lngWriteCorrectEmail;
    }

    if (!$radioFormError) {

        $strCommentCode = return_Random_Alphanumeric(48);

        $radioVisible = True;
        if ($radioControlCommentsByEmail) {
            $radioVisible = False;
        }

        $sql = "INSERT INTO film_comments
			(FilmID, FirstName, LastName, InsertDate, Visible, Email, Title, MainText, CommentCode) 
			VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$iFilmID, $strAddFirstName, $strAddLastName, $insertDate, $radioVisible, $strAddEmail, $strAddTitle, $strAddMainText, $strCommentCode]);
        $intCommentID = $conn->lastInsertId();

        if ($radioControlCommentsByEmail && strpos(sx_ROOT_HOST, "localhost:") == 0) {
            $sxBody = sx_GetEmailBodyFilm($iFilmID, $strAddTitle, $intCommentID, $strCommentCode, $strAddMainText);
            $headers  = 'MIME-Version: 1.0' . "\r\n"
                . 'Content-type: text/html; charset=utf-8' . "\r\n"
                . 'From: ' . $str_SiteEmail . "\r\n";

            mail($str_SiteEmail, LNG_Comments_AddsToArticle, $sxBody, $headers);
            $radioSent = True;
        } else {
            header("Location: films.php?filmID=" . $iFilmID . "&anchor=" . $intCommentID . "#" . $intCommentID);
            exit;
        }
        $strAddFirstName = null;
        $strAddLastName = null;
        $strAddEmail = null;
        $strAddTitle = null;
        $strAddMainText = null;
    }
}

/**
 * Link sent to administrator by mail to activate a comment after check
 */
$icid = 0;
if (!empty($_GET["cid"])) {
    $icid = $_GET["cid"];
}
if (intval($icid) == 0) {
    $icid = 0;
}
$strcc = '';
if (!empty($_GET["cc"])) {
    $strcc = $_GET["cc"];
}

if (intval($icid) > 0 && strlen($strcc) >= 9) {
    $sql = "UPDATE film_comments 
		SET Visible = ?
		WHERE CommentID = ? AND CommentCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([1, $icid, $strcc]);
}

define("radio_UseCaptcha", $radio_UseCaptcha);
define("radio_ValidCaptcha", $radioValidCaptcha);
define("radio_FormError", $radioFormError);
define("check_Msg", $checkMsg);
define("radio_Sent", $radioSent);
define("str_FormType", $strFormType);
define("i_FilmID", $iFilmID);
define("i_MaxCommentLength", $i_MaxCommentLength);
define("radio_ControlCommentsByEmail", $radioControlCommentsByEmail);

define("str_ReadOnly", $strReadOnly);
define("str_AddFirstName", $strAddFirstName);
define("str_AddLastName", $strAddLastName);
define("str_AddEmail", $strAddEmail);
define("str_AddTitle", $strAddTitle);
define("str_AddMainText", $strAddMainText);


function sx_addComments()
{ ?>
    <section class="comment_odddd" id="jqAddComments_Targer">
        <div class="bar">
            <h3><?= LNG_Comments_Add ?></h3>
        </div>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && str_FormType == "blog") {
            if (radio_Sent) { ?>
                <div class="bg_success"><?= LNG__ThanksForParticipation ?></div>
            <?php } elseif (!radio_ValidCaptcha) { ?>
                <div class="bg_error"><?= LNG__CaptchaError ?></div>
            <?php } elseif (radio_FormError) { ?>
                <div class="bg_error"><?= check_Msg ?></div>
        <?php }
        } ?>
        <p><?= LNG_Form_AsteriskFieldsRequired . " " . LNG_Form_MailNotDisplayedInSite . " " . LNG_Form_FillGuidelines ?></p>
        <form name="forumArticles" action="films.php?filmID=<?= i_FilmID ?>&frm=blog#jqAddComments_Targer" method="post" onsubmit="return validateForum(<?= i_MaxCommentLength ?>);">
            <fieldset>
                <input <?= str_ReadOnly ?>type="text" placeholder="<?= LNG__FirstName ?>" name="FirstName" value="<?= str_AddFirstName ?>" size="34"> *
                <input <?= str_ReadOnly ?>type="text" placeholder="<?= LNG__LastName ?>" name="LastName" value="<?= str_AddLastName ?>" size="34"> *
                <input <?= str_ReadOnly ?>type="text" placeholder="<?= LNG__Email ?>" name="Email" value="<?= str_AddEmail ?>" size="34"> *
                <input type="text" name="Title" placeholder="<?= LNG__Title ?>" maxlength="54" value="<?= str_AddTitle ?>" size="48"> *
            </fieldset>
            <p><?= LNG_Form_WritePureText ?></p>
            <fieldset>
                <label><?= LNG_Form_Text ?>: <input name="entered" style="width: 40px" type="text" size="4"> <?= LNG_Form_EnterMaxCharacters . " " . i_MaxCommentLength ?> *</label>
                <textarea class="input_text" name="TextMessage" rows="9" cols="20"></textarea>
                <textarea id="textBody" name="TextBody" rows="18" onFocus="countEntries('forumArticles','textBody',<?= i_MaxCommentLength ?>);"><?= str_AddMainText ?></textarea>
            </fieldset>
            <?php if (radio_UseCaptcha) { ?>
                <fieldset>
                    <?php include "../sxPlugins/captcha/include.php"; ?>
                    <br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
                    <div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
                </fieldset>
            <?php } ?>
            <fieldset>
                <input type="submit" name="addNewComment" value="<?= LNG_Form_Submit ?>">
            </fieldset>
        </form>
        <?php
        if (radio_ControlCommentsByEmail) {
            echo "<p>" . LNG_Comments_VisibleAfterCheck . "</p>";
        } ?>
    </section>
<?php
}
?>