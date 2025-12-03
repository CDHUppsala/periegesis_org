<?php

/**
 * Verification of NewsLetters subscription
 * This file opens by default in the page: contact.php
 * To change it, change the codes in page sx_NewsLetterSubmit.php and sx_Admin/email/sx_NewsLetterSender.php
 * Verify subscription:		/xxxxxxx.php?nl=vd&lid=24&v=Q966O398O77U997
 * Verify unsubscription:	/xxxxxxx.php?nl=vd&lid=24&d=Q966O398O777U997
 */
$strVerifyMsg = "";
if (isset($_GET["nl"]) && $_GET["nl"] == "vd") {
	$radioVerify = False;
	$radioDelete = False;

	$iLetterID = 0;
	if (isset($_GET["lid"])) {
		$iLetterID = (int) $_GET["lid"];
	}

	$strVerifyCode = "";
	if (isset($_GET["v"])) {
		$strVerifyCode = sx_Sanitize_Search_Text($_GET["v"]);
	}
	$strDeleteCode = "";
	if (isset($_GET["d"])) {
		$strDeleteCode = sx_Sanitize_Search_Text($_GET["d"]);
	}

	if (!empty($strVerifyCode)) {
		if (strlen($strVerifyCode) >= 12 && strpos(" ", $strVerifyCode) == 0) {
			$radioVerify = True;
		}
	} elseif (!empty($strDeleteCode)) {
		if (strlen($strDeleteCode) >= 12 && strpos(" ", $strDeleteCode) == 0) {
			$radioDelete = True;
		}
	}

	if (intval($iLetterID) > 0) {
		if ($radioVerify) {
			$sql = "UPDATE newsletters SET 
			Active = True,
			SubscribeCode = NULL
			WHERE LetterID = ? AND SubscribeCode = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iLetterID, $strVerifyCode]);
			$strVerifyMsg = lngThanksForNewsletter;
		}
		if ($radioDelete) {
			$sql = "DELETE FROM newsletters 
			WHERE LetterID = ? AND UnsubscribeCode = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iLetterID, $strDeleteCode]);
			$strVerifyMsg = lngThanksForNewsletterRemoval;
		}
	}
}
$str_FooterMessage = '';
if (!empty($strVerifyMsg)) {
	$str_FooterMessage = $strVerifyMsg;
}
