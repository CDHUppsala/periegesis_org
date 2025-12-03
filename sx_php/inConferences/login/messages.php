<?php
if (isset($_GET["error"])) {
	$strError = $_GET["error"];
	if ($strError == "strTimeout") {
		$strError = lngSessionTimeout;
	} elseif ($strError == "UserNameNotFound") {
		$strError = lngUserNameNotFound;
	} ?>
	<h2 class="head"><?= lngErrorInfo ?></h2>
	<p><?= $strError ?></p>
<?php
} elseif (isset($_GET["leave"])) { ?>
	<h2 class="head"><?= lngByebye ?>&nbsp;<?= @$_GET["name"] ?>! </h2>
	<p><?= lngSubscriptionDiscontinued ?></p>
<?php
} elseif (isset($_GET["logout"])) { ?>
	<h2 class="head"><?= lngByebye . ", " . @$_GET["name"] ?></h2>
	<div class="bg_grey"><?= lngThanksYouAreDisconnected ?></div>
<?php
} elseif (isset($_GET["welcome"])) { ?>
	<h2 class="head"><?= lngThanksForRegistration ?></h2>
	<?php
	$strWelcome = $_GET["welcome"];
	if ($strWelcome == "mc") { ?>
		<p><?= lngRegistrationByMailControl ?></p>
	<?php
	} elseif ($strWelcome == "ac") { ?>
		<p><?= lngRegistrationByAdminControl ?></p>
	<?php
	}
} elseif (isset($_GET["change"])) { ?>
	<h2 class="head"><?= lngProfileChanged ?></h2>
	<p><?= lngRememberInformation ?></p>
<?php
} elseif (isset($_GET["loged"])) { ?>
	<h2 class="head"><?= $strWelcomeTitle ?></h2>
	<div class="bg_grey"><?= $memoWelcomeNotes ?></div>
<?php
} elseif (isset($_GET["active"])) { ?>
	<h2 class="head"><?= $strWelcomeTitle ?></h2>
	<div class="bg_grey"><?= lngYouCanLoginNow ?></div>
<?php
} else {
	header('Location: index.php');
	exit();
}
?>