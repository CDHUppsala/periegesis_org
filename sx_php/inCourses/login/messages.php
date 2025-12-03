<?php
$strError = "";
if (isset($_GET["error"]) && !empty($_GET["error"])) {
	$strError = $_GET["error"];
	if ($strError = "EmailNotFound") {
		$strError = LNG__EmailDoesNotExist;
	} elseif ($strError == "EmailExists") {
		$strError = LNG__EmailExists;
	} elseif ($strError == "Timeout") {
		$strError = lngSessionTimeout;
	} elseif ($strError == "UserNameNotFound") {
		$strError = lngUserNameNotFound;
	} ?>
	<h1><?= lngErrorInfo ?></h1>
	<p><?= $strError ?></p>
<?php
} elseif (isset($_GET["welcome"]) && !empty($_GET["welcome"])) {
	if (empty($str_StudentsWelcomeTitle)) {
		$str_StudentsWelcomeTitle = lngWelcomeToMembersArea;
	} ?>
	<h1><?= $str_StudentsWelcomeTitle ?></h1>
	<?php
	if ($_GET["welcome"] == "login") { 
		echo $memo_StudentsWelcome;
	} elseif ($_GET["welcome"] == "ac") { ?>
		<p><?= lngRegistrationByAdminControl ?></p>
	<?php
	} else { ?>
		<p><?= lngRegistrationByMailControl ?></p>
	<?php
	}
} elseif (isset($_GET["change"]) && !empty($_GET["change"])) { ?>
	<h1><?= lngProfileChanged ?></h1>
	<p><?= lngRememberInformation ?></p>
<?php
} elseif (isset($_GET["leave"]) && !empty($_GET["leave"])) { ?>
	<h1><?= lngByebye  . ' ' . $_GET["name"] ?>! </h1>
	<p><?= lngSubscriptionDiscontinued ?></p>
<?php
} elseif (isset($_GET["logout"]) && !empty($_GET["logout"])) { ?>
	<h1><?= lngByebye . ", " . $_GET["name"] ?></h1>
	<div class="text"><?= lngThanksYouAreDisconnected ?></div>
<?php
} ?>