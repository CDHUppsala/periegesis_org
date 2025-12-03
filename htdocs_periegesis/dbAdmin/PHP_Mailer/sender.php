<?php
include "../functionsLanguage.php";
include "../login/lockPage.php";
include "../functionsDBConn.php";

/**
 * The NewsLetter Return URL-string goes by default to page: contact.php
 * If you change it, you must also change the codes in page .php/sx_NewsLetter.php and in corresponing return pages
 * Verify subscription:	/contact.php?nl=vd&lid=24&v=Q966-O398O777-U997
 * Verify unsubscription:	/contact.php?nl=vd&lid=24&d=Q966-O398O777-U997
 */

$radioUseEmail = True;
$strNLReturnPage = "contact.php";

if (!empty(sx_Socket)) {
	$strSocket = sx_Socket;
} else {
	$strSocket = "http://";
}

$strHost = $_SERVER["HTTP_HOST"];
$sLinkHost = $strSocket . $strHost;
define("sx_ROOT_HOST", $sLinkHost);

require $_SERVER['DOCUMENT_ROOT'] . '/dbAdmin/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dbAdmin/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$strPopupMsg = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_GET["news"] == "yes") {
	$radioTemp = False;
	$strSiteTitle = trim(@$_POST["SiteTitle"]);
	$strSiteEmail = trim(@$_POST["SiteEmail"]);
	$iLanguageID = trim(@$_POST["LanguageID"]);
	$strCurrentLanguage = trim(@$_POST["CurrentLanguage"]);
	$strEmailList = trim(@$_POST["EmailList"]);
	$strEmailListSource = trim(@$_POST["EmailListSource"]);
	$memoTextBody = trim(@$_POST["TextBody"]);

	if (strlen($strEmailList) > 0) {
		$radioTemp = True;
		if (strpos($strEmailList, ";") == 0) {
			$strEmailList .= ";";
		}
		$arrEmailList = explode(";", $strEmailList);
	}

	if ($radioTemp) {
		ini_set("sendmail_from", $strSiteEmail);
		$sxSubject = lngNewsletters;

		//== Email headers and footers
		$htmlHeader = "<html><head>";
		$htmlHeader = $htmlHeader . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		$htmlHeader = $htmlHeader . '<meta http-equiv="Content-Language" content="' . $strCurrentLanguage . '">';
		$htmlHeader = '</head><body style="font-family: Verdana, Arial; font-size: 13pt">';
		$htmlFooter = "</body></html>";

		//== Email content
		$sxBody = "<h2>" . $sxSubject . "</h2>";
		$sxBody = $sxBody . "<p>" . lngSendingFromSite . " " . $strSiteTitle . "</p>";
		$sxBody = $sxBody . $memoTextBody;
		$sxBody = $sxBody . "<hr>";


		$mail = new PHPMailer();
		$mail->setFrom($strSiteEmail, $strSiteTitle);
		$mail->addReplyTo($strSiteEmail, $strSiteTitle);

		$mail->Subject = $sxSubject;

		//$mail->addAttachment('images/phpmailer_mini.png');


		$iRows = count($arrEmailList);
		$iFirstLetterID = 0;
		$iSent = 0;
		for ($r = 0; $r < $iRows; $r++) {
			$arrTeemp = $arrEmailList[$r];
			if (strlen($arrTeemp) > 0 && strpos($arrTeemp, ",") > 0) {
				$arr = explode(",", $arrTeemp);
				$iLetterID = trim($arr[0]);
				if (intval($iFirstLetterID) == 0) {
					$iFirstLetterID = $iLetterID;
				}
				$sEmail = trim($arr[1]);
				$sName = trim($arr[2]);
				$sDeregistrationCode = trim($arr[3]);
				if ($strEmailListSource == "MembersList" || $strEmailListSource == "MembersLogin") {
					$strLinkPage = $sLinkHost . "/" . $strCurrentLanguage . "/";
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">Login to Unsubscibe from our Newsletters</a>.</p>';
				} elseif ($strEmailListSource = "CustomersList") {
					$strLinkPage = $sLinkHost . "/" . $strCurrentLanguage . "/" . $strNLReturnPage . "?nl=vd&src=cs&lid=" . $iLetterID . "&d=" . $sDeregistrationCode;
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">' . lngNewslettersUnsubscribe . "</a>.</p>";
				} elseif ($strEmailListSource = "Newsletters") {
					$strLinkPage = $sLinkHost . "/" . $strCurrentLanguage . "/" . $strNLReturnPage . "?nl=vd&src=nl&lid=" . $iLetterID . "&d=" . $sDeregistrationCode;
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">' . lngNewslettersUnsubscribe . "</a>.</p>";
				}
				$sx_Body = $htmlHeader . $sxBody . $strUnscribeURL;
				$sx_Body = $sx_Body . $htmlFooter;

				$mail->msgHTML($sx_Body);
				//$mail->AltBody = 'This is a plain-text message body';
				$mail->addAddress($sEmail, $sName);


				if (!empty($sEmail) && strpos(sx_ROOT_HOST, "localhost:") == 0) {
					try {
						$mail->send();
						$iSent++;
					} catch (Exception $e) {
						echo 'Mailer Error ' . $mail->ErrorInfo . '<br>';
						$iSent--;
						$mail->this->reset();
						continue;
					}
				}
				$mail->clearAddresses();
				$mail->clearAttachments();
			}
			/**
			 * Uncomment to delay the sending of each mail for X seconds
			 */
			//sleep(1);
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $strCurrentLanguage ?>">

<head>
	<meta charset="utf-8">
	<title>Public Sphere Content Management System</title>
	<link rel="stylesheet" type="text/css" href="../css/sxCMS.css">
</head>

<body style="padding: 10px">
	<?php if ($radioTemp) { ?>
		<h1>Succesful sending of <?= $iSent ?> letters from total <?= $r ?></h1>
		<h3>First ID: <?= $iFirstLetterID ?>, Last ID: <?= $iLetterID ?></h3>
		<p>Please, check the First and Last ID to avoid sending the same list of emails.</p>
		<h3>You can send the next email in 10 minutes</h3>
		<hr>
		<?= $sxBody ?>
	<?php } else { ?>
		<h1>An Error accured!</h1>
		<p>You must add a list of emails!</p>
	<?php } ?>
</body>

</html>