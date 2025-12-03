<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include_once __DIR__ . "/get_functions.php";

/**
 * The NewsLetter Return URL-string (for deregistration) goes by default to page: index.php
 * If you change it, you must also change the codes in page sx_php/sx_NewsLetter.php and in corresponing return pages
 * Verify subscription:	/index.php?nl=vd&lid=24&v=Q966-O398O777-U997
 * Verify unsubscription:	/index.php?nl=vd&lid=24&d=Q966-O398O777-U997
 */
$strNLReturnPage = "index.php";

/**
 * Get the site language from Sight Configuration (sx_languages.php)
 */
$str_CurrentLanguage = "en";
if (!empty(sx_DefaultSiteLang)) {
	$str_CurrentLanguage = sx_DefaultSiteLang;
}

/**
 * GET Language ID from LANGUAGE Code  
 */
$iLanguageID = sx_getLanguageID($str_CurrentLanguage);
$str_HomePage = sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/index.php";

/**
 * GET site information
 */
$aResults = sx_getSiteInformation($iLanguageID);
if (is_array($aResults)) {
	$strSiteTitle = $aResults[0];
	$strLogoTitle = $aResults[1];
	$strLogoSubTitle = $aResults[2];
	$strLogoImage = $aResults[3];
	$strLogoImageEmail = $aResults[4];
	$strSiteAddress = $aResults[5];
	$strSitePostalCode = $aResults[6];
	$strSiteCity = $aResults[7];
	$strSitePhone = $aResults[8];
	$strSiteMobile = $aResults[9];
	$str_SiteEmail = $aResults[10];
}
$aResults = null;

if (empty($strLogoImageEmail)) {
	$strLogoImageEmail = $strLogoImage;
}

$str_LogoImageEmail = sx_ROOT_HOST . '/images/' . $strLogoImageEmail;

$str_SiteInfo = $strSiteTitle .'<br>'. $strSiteAddress;
if ($str_SiteInfo != "" && $strSitePostalCode != "") {
	$str_SiteInfo .= ", " . $strSitePostalCode . " " . $strSiteCity;
}
if ($strSitePhone != "" || $strSiteMobile != "") {
	if ($str_SiteInfo != "") {
		$str_SiteInfo .= "<br>";
	}
	$str_SiteInfo .= $strSitePhone . " " . $strSiteMobile;
}

$str_SiteTitle = $strSiteTitle;
if(!empty($strLogoTitle)) {
	$str_SiteTitle = $strLogoTitle;
}

$sx_mail_subject = "Newsletter";
if(!empty($strLogoSubTitle)) {
	$sx_mail_subject = $strLogoSubTitle;
}

$sx_mail_content = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioTemp = False;
	if (!empty($_POST["EmailList"])) {
		$strEmailList = trim($_POST["EmailList"]);
	}
	if (!empty($_POST["EmailListSource"])) {
		$strEmailListSource = trim($_POST["EmailListSource"]);
	}
	if (!empty($_POST["TextBody"])) {
		$memoTextBody = trim($_POST["TextBody"]);
	}

	if (!empty($strEmailList) && !empty($strEmailListSource) && !empty($memoTextBody)) {
		$radioTemp = True;
		if (strpos($strEmailList, ";") == 0) {
			$strEmailList .= ";";
		}
		$arrEmailList = explode(";", $strEmailList);
	}

	$iFirstLetterID = 0;
	$iLetterID = 0;
	$iSent = 0;
	$r = 0;
	if ($radioTemp) {
		$iRows = count($arrEmailList);
		for ($r = 0; $r < $iRows; $r++) {
			$strUnscribeURL = '';
			$arrTeemp = $arrEmailList[$r];
			if (!empty($arrTeemp) && strpos($arrTeemp, ",") > 0) {
				$arr = explode(",", $arrTeemp);
				$iLetterID = trim($arr[0]);
				if (intval($iFirstLetterID) == 0) {
					$iFirstLetterID = $iLetterID;
				}
				$sEmail = trim($arr[1]);
				$sName = trim($arr[2]);
				if (!empty($sName)) {
					//$sEmail = $sName . " <" . $sEmail . ">";
				}
				$sDeregistrationCode = trim($arr[3]); 
				if ($strEmailListSource == "MembersList" || $strEmailListSource == "ForumMembersList" || $strEmailListSource == "MembersLogin" || $strEmailListSource == "StudentsList" || $strEmailListSource == "Participants") {
					$strLinkPage = sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/";
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">Login to Unsubscibe from the Email List</a>.</p>';
				} elseif ($strEmailListSource == "CustomersList") {
					// Obs! This must be develeoped to open the list of Customers, as it opens the list av Newsletters
					$strLinkPage = sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/" . $strNLReturnPage . "?nl=vd&src=cs&lid=" . $iLetterID . "&d=" . $sDeregistrationCode;
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">' . lngNewslettersUnsubscribe . "</a>.</p>";
				} elseif ($strEmailListSource == "Newsletters") {
					$strLinkPage = sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/" . $strNLReturnPage . "?nl=vd&src=nl&lid=" . $iLetterID . "&d=" . $sDeregistrationCode;
					$strUnscribeURL = '<p><a target="_blank" href="' . $strLinkPage . '">' . lngNewslettersUnsubscribe . "</a>.</p>";
				}

				$sx_send_to_email = $sEmail;
				$sx_mail_content = $memoTextBody . $strUnscribeURL;

				include __DIR__ . "/sx_MailTemplate.php";
				$iSent++;
			}
			//sleep(1);
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $str_CurrentLanguage ?>">

<head>
	<meta charset="utf-8">
	<title>Public Sphere Content Management System</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body style="padding: 10px">
	<?php if ($radioTemp) { ?>
		<h1>Succesful sending of <?= $iSent ?> letters from total <?= $r ?></h1>
		<h3>First ID: <?= $iFirstLetterID ?>, Last ID: <?= $iLetterID ?></h3>
		<p>Please, check the First and Last ID to avoid sending the same list of emails.</p>
		<h3>You can send the next email in 10 minutes</h3>
		<p>The content of the email, except information for unsubscription from the mail list.</p>
		<hr>
		<?= $memoTextBody ?>
	<?php } else { ?>
		<h1>An Error accured!</h1>
		<p>You must add a list of emails!</p>
	<?php } ?>
</body>

</html>