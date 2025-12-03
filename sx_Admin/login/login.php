<?php

include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include realpath(PROJECT_PATH . "/sx_Conn/connMySQL.php");

$strHost = $_SERVER["HTTP_HOST"];
$sxSuffix = $strHost . "/sxAdmin/";

/*
    To prohibit connections from false sites
    - The constants sx_TrueSiteURL and sx_radioCheckTrueSiteURL is defined in sx_languages.php
    - Activated automatically if CONSTANT sx_SiteURL is defined
    Not used in test domains
*/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

if (defined('sx_TrueSiteURL') && !empty(sx_TrueSiteURL) && sx_radioCheckTrueSiteURL) {
	if (sx_TrueSiteURL != sx_ROOT_HOST) {
		sx_writeToLog('Login_Admin: Rong URL: ' . sx_ROOT_HOST);
		$_SESSION[] = array();
		session_unset();
		sleep(15);
		header("Location: " . sx_TrueSiteURL);
		exit();
	}
}

$strUserIP = sx_Get_UserIP();
$radioUserIP = filter_var($strUserIP, FILTER_VALIDATE_IP);

if ($radioUserIP === false) {
	$strUserIP = "";
}

//===================================================
//== Check the number of checkin endeavours
//===================================================

if (!isset($_SESSION["CountLogins"])) {
	$_SESSION["CountLogins"] = 1;
}

if ((intval($_SESSION["CountLogins"]) > 5 || $radioUserIP === false)) {
	sx_writeToLog('Login_Admin: More than Five Attempts ' . trim(@$_POST["UserPassword"]));
	$_SESSION[] = array();
	session_unset();
	sleep(15);
	header("Location: /");
	exit();
}

$radioGo = true;

if (isset($_POST["GoToLogin"])) {
	$radioGo = false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $radioGo) {
	$radioPass = True;
	$iLoginID = 0;

	if (!empty($_POST["UserEmail"])) {
		sx_writeToLog("Login_Admin: Hidden input: " . $_POST["UserEmail"] . " Whitelist Hack-Attempt!");
		$radioPass = False;
		sleep(10);
	}

	if (empty($_POST['FormToken'])) {
		$radioPass = False;
		sx_writeToLog("Login_Admin: Empty Form Token Hack-Attempt!");
		sleep(10);
	} elseif (!sx_valid_form_token("LoginForm", $_POST["FormToken"])) {
		$radioPass = False;
		sx_writeToLog("Login_Admin: Rong Form Token Hack-Attempt!");
		sleep(10);
	}

	$strUserName = "";
	if (!empty($_POST["UserName"])) {
		$strUserName = $_POST["UserName"];
		$checkUserName = sx_checkSanitizedPW($strUserName);
		if ($checkUserName == false) {
			$radioPass = False;
		}
	}

	if (
		empty($strUserName) ||
		strlen($strUserName) < 6 ||
		strlen($strUserName) > 32 ||
		strpos($strUserName, " ") > 0 ||
		strpos($strUserName, "--") > 0
	) {
		$radioPass = False;
	}

	// Do not snitze, used for check only
	$strUserPassword = "";
	if (!empty($_POST["UserPassword"])) {
		$strUserPassword = $_POST["UserPassword"];
	}

	if (
		empty($strUserPassword) ||
		strlen($strUserPassword) < 7 ||
		strlen($strUserPassword) > 32 ||
		strpos($strUserPassword, " ") > 0
	) {
		$radioPass = False;
	}


	/**
	 * For more security, the user can login only from a constant IP 
	 * - Activated automatically if UserIP is not Null or Not Empty
	 * $PW_Hash = password_hash($sPassword, PASSWORD_DEFAULT);
	 * if (password_verify($sPW, $sMemberPW)) {
	 */


	$openConn;
	if ($radioPass) {

		$strWhereIP = " AND (UserIP = ? OR UserIP Is Null OR UserIP = '') ";

		$sql = "SELECT LoginID, UserFirstName, UserPasswordHashed, UserIP, AdminLevel 
			FROM admin_login 
			WHERE UserName = ? "
			. $strWhereIP;
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strUserName, $strUserIP]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$rs) {
			$radioPass = False;
		} elseif (password_verify($strUserPassword, $rs["UserPasswordHashed"])) {
			$iLoginID = $rs["LoginID"];
			if (intval($iLoginID) == 0) {
				$iLoginID = 0;
			}
			$iAdminLevel = $rs["AdminLevel"];
			if (intval($iAdminLevel) == 0) {
				$iAdminLevel = 2;
			}
			session_regenerate_id();
			$_SESSION[$sxSuffix] = True;
			$_SESSION["LoginAdminID"] = $iLoginID;
			$_SESSION["LoginUserIP"] = $strUserIP;
			$_SESSION["UserFirstName"] = $rs["UserFirstName"];
			$_SESSION["LoginAdminLevel"] = $iAdminLevel;
			$_SESSION["UserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			$_SESSION['LastLogin'] = time();
		} else {
			$radioPass = False;
		}
		$stmt = null;
		$rs = null;
	}

	if ($radioPass) {
		$sql = "INSERT INTO admin_logs 
			(AdminLoginID, LogIP) 
			VALUES (?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iLoginID, $strUserIP]);

		unset($_SESSION["CountLogins"]);
		header("Location: ../default.php");
		exit();
	} else {
		//		$strLogInputs = substr($strUserName . " " . $strUserPassword, 0, 250);
		$strLogInputs = $strUserName;
		$sql = "INSERT INTO admin_logs 
			(AdminLoginID, LogInputs, LogIP) 
			VALUES (?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iLoginID, $strLogInputs, $strUserIP]);
		sleep(2);
		$_SESSION["CountLogins"]++;
		header("Location: login.php?err=err");
		exit();
	}
} ?>
<!DOCTYPE html>
<html id="studiox">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?= lngSiteTitle ?></title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body>
	<div style="width: 640px; margin: 50px auto;">
		<h1><?= lngLoginForWebMasters ?></h1>
		<?php if (!empty(@$_REQUEST["err"])) { ?>
			<p class="errMsg"><?= lngLogInError ?></p>
		<?php } ?>
		<hr>
		<div class="floatRight"><a href="../../<?= sx_DefaultSiteLang ?>">Home Page</a></div>
		<form name="LoginForm" action="login.php" method="post">
			<input type="hidden" name="FormToken" value="<?= sx_generate_form_token('LoginForm', 64) ?>">
			<table class="cleanTable">
				<tr>
					<td><?= lngEmail ?>: </td>
					<td><input type="text" name="UserEmail" value size="36"></td>
				</tr>
				<tr>
				<tr>
					<td><?= lngUsername ?>: </td>
					<td><input type="text" name="UserName" value size="36"></td>
				</tr>
				<tr>
					<td><?= lngPassword ?>: </td>
					<td><input type="password" name="UserPassword" value size="36"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input class="button" type="Submit" name="Action" value="<?= lngLoginButton ?>"></td>
				</tr>
			</table>
		</form>
		<hr>
		<p><?= lngProgramName ?> <br> <?= lngVersion ?> <br> Public Sphere</p>
	</div>
</body>

</html>