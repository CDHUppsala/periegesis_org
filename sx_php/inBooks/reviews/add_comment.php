<?php

$str_AddTitle = "";
$str_AddMainText = "";

$radioContinue = False;
$arrErrors = array();

$radioReviewAnabled = false;
if ($radio__UserSessionIsActive && intval($iBookID) > 0) {
	$radioReviewAnabled = true;
	$int_AddUserID = $_SESSION["Users_UserID"];
	$str_AddFirstName = $_SESSION["Users_FirstName"];
	$str_AddLastName = $_SESSION["Users_LastName"];
	$str_AddEmail = $_SESSION["Users_UserEmail"];
}

if ($radioReviewAnabled) {
	/**
	 * Since 2 forms can be sent from the same page,
	 * we must identify the active form
	 */
	$radioReviewPosted = false;
	if (!empty($_POST['Review']) && $_POST['Review'] == "Review") {
		$radioReviewPosted = true;
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST" && $radioReviewPosted) {
		$radioContinue = true;
		if (isset($_POST['Title'])) {
			$str_AddTitle = sx_Sanitize_Input_Text($_POST['Title']);
		}
		/**
		 * Fix only rows, in case of reuse with form errors
		 * Add paragraphs before sending (sx_ParagraphBreaks)
		 */
		if (isset($_POST['TextBody'])) {
			$str_AddMainText = sx_Sanitize_Text_Area_Rows($_POST['TextBody']);
		}

		if (!empty($str_AddTitle) && strlen($str_AddTitle) < 5 || strlen($str_AddMainText) < 15) {
			$radioContinue = false;
			$arrErrors[] = LNG_Form_AsteriskFieldsRequired;
		}
		if (!empty($str_AddMainText) && strlen($str_AddMainText) > intval($i_MaxCommentLength + 100)) {
			$radioContinue = false;
			$arrErrors[] = lngMsgContains . " " . strlen($str_AddMainText) . " " . lngOfMaxCharactersAllowed . " " . $i_MaxCommentLength;
		}

		if ($radioContinue) {
			/**
			 * Add paragraphs to main text before sending
			 */
			$str_AddMainText = sx_ParagraphBreaks($str_AddMainText);

			/**
			 * Since review presupposes login users, the review must be directly visible.
			 * Administration control in that case is to hide the review, if not appropriate
			 */
			$radioVisible = true;
			if ($radioControlCommentsByEmail) {
				$radioVisible = 1;
			}

			$strCommentCode = return_Random_Alphanumeric(48);
			$sql = "INSERT INTO book_comments
			(BookID, UserID, Title, Visible, FirstName, LastName, 
				Email, CommentCode, MainText) 
			VALUES(?,?,?,?,?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([
				$iBookID, $int_AddUserID, $str_AddTitle, $radioVisible, $str_AddFirstName, $str_AddLastName,
				$str_AddEmail, $strCommentCode, $str_AddMainText
			]);
			$intCommentID = $conn->lastInsertId();

			if ($radioControlCommentsByEmail) {
				/**
				 * The Mail Template is hold in the variable: $sx_mail_body
				 * Constant and global variables used in the template and by the mail sender:
				 * 		str_SiteTitle
				 * 		str_SiteEmail
				 * 		str_SiteInfo: Prepared footer information (site name, address, telephone, etc.)
				 * 		LNG_Mail_SendingFromSite: A common information title for all mails
				 * 		Link to logotype: sx_ROOT_HOST . '/images/' . $str_LogoImageEmail
				 * 		Link to home page: sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php
				 * Variables to be defined in every page:
				 * 		$sx_send_to_email: The mail of the reciever
				 * 		$sx_mail_subject: The subject of mail
				 * 		$sx_mail_content: whatever
				 */

				$sx_send_to_email = str_SiteEmail;
				$sx_mail_subject = "Check the review of a book";

				$sx_mail_content = '<h4>Check the Review of a Book Added to the Website</h4>';
				$sx_mail_content .= '<p>The review added by the user: ' . $str_AddFirstName . ' ' . $str_AddLastName . ', ' . $str_AddEmail;

				$confirmAdminURLpath = sx_ROOT_HOST_PATH . "?bookID=" . $iBookID . "&cid=" . $intCommentID . "&cc=" . $strCommentCode;
				$sx_mail_content .= '<p>If the review is not proper, <a style="text-decoration: none;" href="' . $confirmAdminURLpath . '"><b>click here to Hide it from the site</b></a>.</p>';
				$sx_mail_content .= '<p>The title and content of the review:</p>';

				$sx_mail_content .= '<h4>' . $str_AddTitle . '</h4>';
				$sx_mail_content .= $str_AddMainText;

				// Send the mail
				require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
			}
			$str_AddTitle = "";
			$str_AddMainText = "";
		}
	}
}

if ($radioReviewAnabled) { ?>

	<section class="comments" id="jqAddComments_Targer">
		<div class="bar">
			<h3><?= LNG_Comments_Add ?></h3>
		</div>
		<?php
		if ($radioReviewPosted) {
			if ($radioContinue) { ?>
				<div class="bg_success"><?= LNG__ThanksForParticipation ?></div>
			<?php
			} else { ?>
				<div class="bg_error"><?php echo implode('<br>', $arrErrors) ?></div>
		<?php
			}
		} ?>
		<p><?= LNG_Form_AsteriskFieldsRequired . " " . LNG_Form_MailNotDisplayedInSite . " " . LNG_Form_FillGuidelines ?></p>
		<form name="ReviewForm" action="books.php?bookID=<?php echo $iBookID; ?>#jqAddComments_Targer" method="post">
			<input type="hidden" name="BookID" value="<?= $iBookID ?>">
			<input type="hidden" name="Review" value="Review">
			<fieldset>
				<input readonly type="text" placeholder="<?= LNG__FirstName ?>" name="FirstName" value="<?= $str_AddFirstName ?>" size="34"> *
				<input readonly type="text" placeholder="<?= LNG__LastName ?>" name="LastName" value="<?= $str_AddLastName ?>" size="34"> *
				<input readonly type="text" placeholder="<?= LNG__Email ?>" name="Email" value="<?= $str_AddEmail ?>" size="34"> *
				<input type="text" name="Title" placeholder="<?= LNG__Title ?>" maxlength="54" value="<?= $str_AddTitle ?>" size="48"> *
			</fieldset>
			<p><?= LNG_Form_WritePureText ?></p>
			<fieldset>
				<label><?= LNG_Form_Text ?>: <input name="entered" style="width: 40px" type="text" size="4"> <?= LNG_Form_EnterMaxCharacters . " " . $i_MaxCommentLength ?> *</label>
				<textarea id="textBody" name="TextBody" rows="18" onFocus="countEntries('ReviewForm','textBody',<?= $i_MaxCommentLength ?>);"><?= $str_AddMainText ?></textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="addNewComment" value="<?= LNG_Form_Submit ?>">
			</fieldset>
		</form>
		<?php
		if ($radioControlCommentsByEmail) { ?>
			<div class="bg_info">
				<p><?php echo LNG_Comments_VisibleAfterCheck; ?></p>
			</div>
		<?php
		} ?>
	</section>
<?php
} ?>