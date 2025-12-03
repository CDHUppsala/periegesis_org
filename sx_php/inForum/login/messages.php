<section>
	<?php
	$strError = $_GET["error"] ?? '';
	if (!empty($strError)) {
		if ($strError == "Timeout") {
			$strError = lngSessionTimeout;
		} elseif ($strError == "UserNameNotFound") {
			$strError = lngUserNameNotFound;
		} ?>
		<h1><?= lngErrorInfo ?></h1>
		<p><?= $strError ?></p>
	<?php
	}

	$strRequest = $_GET["request"] ?? '';

	if ($strRequest == 'welcome') { ?>
		<h1><?= lngWelcomeToForum ?></h1>
		<?php
		if ($radioUseAdministrationControl) { ?>
			<p><?= lngRegistrationByAdminControl ?></p>
		<?php
		} else { ?>
			<p><?= lngRegistrationByMailControl ?></p>
		<?php
		}
	} elseif ($strRequest == 'change') { ?>
		<h1><?= lngProfileChanged ?></h1>
		<p><?= lngRememberInformation ?></p>
	<?php
	} elseif ($strRequest == 'leave') { ?>
		<h1><?= lngByebye  . ' ' . $_SESSION["Forum_GreetName"] ?>! </h1>
		<p><?= lngSubscriptionDiscontinued ?></p>
	<?php
		unset($_SESSION["Forum_GreetName"]);
	} elseif ($strRequest == 'logout') { ?>
		<h1><?= lngByebye . ", " . $_SESSION["Forum_GreetName"] ?></h1>
		<div class="text"><?= lngThanksYouAreDisconnected ?></div>
	<?php
		unset($_SESSION["Forum_GreetName"]);
	} ?>
</section>