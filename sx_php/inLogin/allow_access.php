<?php

/**
 * The activation of user (Allow) is depended by teh level of registration control and can be made:
 * 		By the applicant itself, through a link sent by email (see join.php), or
 * 		By the administrator only, through a link sent by email (see join.php)
 * If activation is made by the administrator, sent an information email to the member.
 */


$radioAllowByAdmin = False;
$intUserID = 0;
$strRequestCode = "";
$radioContinue = true;

if (isset($_GET["aid"])) {
	$intUserID = (int)($_GET["aid"]);
}
if (isset($_GET["ac"])) {
	//Don't clean, just for compare
	$strRequestCode = $_GET["ac"];
}

if (intval($intUserID) == 0) {
	$radioContinue = False;
}
if (empty($strRequestCode) || strlen($strRequestCode) < 9) {
	$radioContinue = False;
}

if ($radioContinue == False) {
	echo "<h1>No way out!</h1>";
	exit;
} else {
	$radioContinue = false;
	$strSendUserEmail = "";
	$sql = "SELECT UserEmail, AllowCode, ApprovalCode
		FROM users 
		WHERE AllowAccess = 0
		AND UserID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intUserID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioContinue = true;
		$strSendUserEmail = $rs["UserEmail"];
		$strAllowCode = $rs["AllowCode"];
		$strApprovalCode = $rs["ApprovalCode"];
	}
	$stmt = null;
	$rs = null;

	if ($radioContinue == false || empty($strSendUserEmail) || (empty($strAllowCode) && empty($strSendUserEmail))) {
		$radioContinue = false;
	}

	/**
	 * Check if request comes from administrator or the applicant
	 */
	if ($radioContinue) {
		if ($radioUseAdministrationControl && $strApprovalCode == $strRequestCode) {
			$radioAllowByAdmin = true;
		} elseif ($strAllowCode != $strRequestCode) {
			/**
			 * Activation Code not the same
			 */
			$radioContinue = false;
		}
	}

	if ($radioContinue == false) {
		echo "<h1>No way Home!</h1>";
		exit;
	} else {
		if ($radioAllowByAdmin == false) {
			$sql = "UPDATE users SET 
				AllowAccess = 1, 
				AllowCode = '' 
				WHERE UserID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intUserID]);

			header("location: " . sx_PATH . "?pg=login&msg=yes");
			exit();
		} else {
			/**
			 * If checked by the administrator, sent a mail 
			 * to activate the registration by the user
			 */
			//== URL to login
			$confirmURLpath = sx_ROOT_HOST_PATH . "?pg=allow&aid=" . $intUserID . "&ac=" . $strAllowCode;

			/**
			 * Email headers and footers
			 */
			$htmlHeader = "<div style='font-family: Verdana, Arial; font-size: 18px'>";
			$htmlFooter = "</div>";

			$strHeader = "<h1>" . lngSubscriptionInfor . "</h1>";
			$strHeader .= "<h2>" . LNG_Mail_SendingFromSite . ": <a href='" . sx_ROOT_HOST . "'>" . str_SiteTitle . "</a></h2>";
			$strFooter = "<p>" . str_SiteInfo . "</p>";

			//== Email content
			$sxSubject = lngSubscriptionInfor;

			$sxBody = $htmlHeader;
			$sxBody .= $strHeader;
			$sxBody .= "<p>" . LNG__EmailSentUponRequest . " " . str_SiteTitle . ".</p>";
			$sxBody .= "<p>" . lngYourMembershipIsConfirmed . "</p>";
			$sxBody .= "<p>" . lngToLoginGoTo . " <a href='" . $confirmURLpath . "'>" . lngClickHere . "</a>.</p>";

			$sxBody .= $strFooter . $htmlFooter;

			//== Send the Email
			$headers = [
				'MIME-Version' => '1.0',
				'Content-type' => 'text/html; charset=UTF-8',
				'From' => str_SiteTitle . ' <' . strip_tags(str_SiteEmail) . '>',
				'X-Mailer' => 'PHP/' . phpversion()
			];

			if (strpos(sx_ROOT_HOST, "localhost:") == 0) {
				ini_set("sendmail_from", str_SiteEmail);
				mail($strSendUserEmail, $sxSubject, $sxBody, $headers, "-f " . str_SiteEmail);
			} else {
			/**
			 * Important for safety reasons:
			 * Use the next line only in development environment
			 * Always comment or remove it in real production
			 */
			//echo "<p><b>Local Environment:</b><br>" . $strSendUserEmail . "<br>" . $sxSubject . "</p>" . $sxBody;
		}
		}
	}
}
/**
 * Uncomment in real site
 */
echo "<script>window.close();</script>";