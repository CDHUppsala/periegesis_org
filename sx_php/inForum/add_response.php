<?php
$sResponseTitle = "";
$sResponseBody = "";
$addContinue = true;
$arrErrors = array();
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["SubmitResponse"])) {
    if ($radio___ForumMemberIsActive === false) {
        if (!empty($_POST['FirstName'])) {
            $s__FirstName = sx_Sanitize_Input_Text($_POST['FirstName']);
            $s__FirstName = strtoupper(substr($s__FirstName, 0, 1)) . strtolower(substr($s__FirstName, 1));
        } else {
            $addContinue = false;
        }
        if (!empty($_POST['LastName'])) {
            $s__LastName = sx_Sanitize_Input_Text($_POST['LastName']);
            $s__LastName = strtoupper(substr($s__LastName, 0, 1)) . strtolower(substr($s__LastName, 1));
        } else {
            $addContinue = false;
        }

        if ($addContinue) {
            if (strlen($s__FirstName) > 45 || strlen($s__LastName) > 45) {
                $addContinue = false;
                $arrErrors[] = LNG_Form_ExpectedLengthToLong;
            }
        }
    }

    if (!empty($_POST['Title'])) {
        $sResponseTitle = sx_Sanitize_Input_Text($_POST['Title']);
    } else {
        $addContinue = false;
    }
    if (!empty($_POST['TextBody'])) {
        $sResponseBody = sx_Sanitize_Text_Area($_POST['TextBody']);
    } else {
        $addContinue = false;
    }

    if ($addContinue) {
        if (strlen($sResponseTitle) > 255 || strlen($sResponseBody) > ($intMaxResponseCharacters + 50)) {
            $addContinue = false;
            $arrErrors[] = LNG_Form_ExpectedLengthToLong;
        }
    }

    if (!empty($_POST['ResponseID'])) {
        $intResponseID = (int) $_POST["ResponseID"];
        if (intval($intResponseID) == 0) {
            $addContinue = false;
        }
    } else {
        $addContinue = false;
    }

    if ($addContinue) {
        $dInsertDate = date('Y-m-d');
        $sql = "INSERT INTO forum_articles 
		(ForumID, LanguageID, UserID, FirstName, LastName, InsertDate, ResponseID, IPAddress, Title, Textbody) 
		VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $intForumID, $int_LanguageID,
            $i__UserID, $s__FirstName, $s__LastName, $dInsertDate, $intResponseID,
            $currentVisitorIP, $sResponseTitle, $sResponseBody
        ]);
        $intInsertID = $conn->lastInsertId();

        header("Location: forum.php?forumID=" . $intForumID . "&anchor=" . $intInsertID . "&articleID=" . $intArticleID . "#" . $intInsertID);
        exit();
    }
}

$strReadOnly = "";
if ($radio___ForumMemberIsActive) {
    $strReadOnly = "readonly ";
} ?>

<div class="formWrap" id="jqAddComments_Targer">
    <h2><?= LNG_Comments_Add ?></h2>
    <?php
    if ($addContinue == false) { ?>
        <div class="bg_warning">
            <?php
            if (!empty($arrErrors)) {
                echo implode('<br>', $arrErrors);
            } else {
                echo lngAsteriskFieldsRequired;
            }
            ?>
        </div>
    <?php
    } ?>
    <form name="forumArticles" action="forum.php?forumID=<?= $intForumID ?>&articleID=<?= $intArticleID ?>&send=yes" method="post" onsubmit="return validateForum(<?= $intMaxResponseCharacters ?>)">
        <input type="hidden" name="ResponseID" value="<?= $intArticleID ?>">
        <fieldset>
            <input type="text" <?= $strReadOnly ?>name="FirstName" value="<?= $s__FirstName ?>" maxlength="50" size="28" placeholder="<?= lngName ?>" required> *<br>
            <input type="text" <?= $strReadOnly ?>name="LastName" value="<?= $s__LastName ?>" maxlength="50" size="28" placeholder="<?= LNG__LastName ?>" required> *<br>
            <input type="text" name="Title" value="<?= $sResponseTitle ?>" maxlength="200" size="40" placeholder="<?= LNG__Title ?>" required> *<br>
        </fieldset>
        <fieldset>
            <label><?= LNG_Form_Text ?>: <input name="entered" type="text" size="4"> <?= LNG_Form_EnterMaxCharacters . " " . $intMaxResponseCharacters ?> *</label>
            <p><textarea name="TextBody" cols="44" rows="16" onFocus="countEntries('forumArticles','TextBody',<?= $intMaxResponseCharacters ?>);" required><?php echo $sResponseBody; ?></textarea></p>
        </fieldset>
        <p class="text_xsmall"><?= LNG_Form_WritePureText ?></p>
        <fieldset>
            <p class="align_center"><input type="submit" name="SubmitResponse" value="<?= LNG_Form_Submit ?>"></p>
        </fieldset>
    </form>
</div>