<div id="jqAddComments_Targer">
	<div class="bar">
		<h2><?php echo LNG_Comments_Add ?></h2>
	</div>
	<?php
	if ($radioContinue) { ?>
		<div class="bg_success">
			<p><?php echo LNG__ThanksForParticipation ?></p>
			<?php
			if ($send_MailFor_AdminApproval) { ?>
				<p><?php echo LNG_Comments_VisibleAfterCheck ?></p>
			<?php
			} ?>
		</div>
	<?php
	} elseif (!empty($arrError)) { ?>
		<div class="bg_error">
			<?php echo implode("<br>", $arrError)  ?>
		</div>
	<?php
	}

	$skata = true;

	$strReadOnly = "";
	if ($radio__UserSessionIsActive) {
		$strReadOnly = "readonly ";
		$strAddFirstName = $_SESSION["Users_FirstName"];
		$strAddLastName = $_SESSION["Users_LastName"];
		$strAddEmail = $_SESSION["Users_UserEmail"];
	} else {
		$str_EventFormToken = sx_generate_form_token('CommentsFormToken', 128);
	} ?>

	<p><?php echo LNG_Form_AsteriskFieldsRequired . " " . LNG_Form_MailNotDisplayedInSite . " " . LNG_Form_FillGuidelines ?></p>
	<form name="forumArticles" action="texts.php?tid=<?php echo $int_TextID ?>#jqAddComments_Targer" method="post" onsubmit="return validateForum(<?php echo $i_MaxCommentLength ?>);">
		<input type="hidden" name="TextID" value="<?php echo $int_TextID ?>">
		<?php
		if ($radio__UserSessionIsActive == false) { ?>
			<input type="hidden" name="CommentsFormToken" value="<?php echo $str_EventFormToken ?>">
		<?php
		} ?>
		<fieldset>
			<input <?php echo $strReadOnly ?>type="text" name="FirstName" value="<?php echo $strAddFirstName ?>" placeholder="<?php echo LNG__FirstName ?>" size="34"> *<br>
			<input <?php echo $strReadOnly ?>type="text" name="LastName" value="<?php echo $strAddLastName ?>" placeholder="<?php echo LNG__LastName ?>" size="34"> *<br>
			<input <?php echo $strReadOnly ?>type="text" name="Email" value="<?php echo $strAddEmail ?>" placeholder="<?php echo LNG__Email ?>" size="34"> *
			<input class="input_text" type="text" name="City" value="" placeholder="<?php echo LNG__City ?>" size="34"><br>
			<input type="text" name="Title" maxlength="54" value="<?php echo $strAddTitle ?>" placeholder="<?php echo LNG__Title ?>" size="48"> *
		</fieldset>
		<fieldset>
			<label><?php echo LNG_Form_Text ?>:<input name="entered" type="text" size="4">
				<?php echo LNG_Form_EnterMaxCharacters . " " . $i_MaxCommentLength ?> *</label>
			<textarea id="TextBody" name="TextBody" rows="18" onFocus="countEntries('forumArticles','TextBody',<?php echo $i_MaxCommentLength ?>);"><?php echo $strAddMainText ?></textarea>
			<div class="text_xsmall"><?php echo LNG_Form_WritePureText ?></div>
		</fieldset>
		<?php if ($radio_UseCaptcha) { ?>
			<fieldset>
				<?php require_once DOC_ROOT . "/sxPlugins/captcha/include.php"; ?>
				<br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
				<div class="text_xsmall"><?php echo LNG_Form_EnterCaptcha ?></div>
			</fieldset>
		<?php } ?>
		<fieldset>
			<input class=float_right type="submit" name="addNewComment" value="<?php echo LNG_Form_Submit ?>">
		</fieldset>
	</form>
	<?php
	if ($send_MailFor_AdminApproval) { ?>
		<div class="bg_info">
			<p><?php echo LNG_Comments_VisibleAfterCheck ?></p>
		</div>
	<?php
	} ?>
</div>