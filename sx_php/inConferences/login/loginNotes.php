<section>
	<?php
	if (empty($strWelcomeTitle)) {
		$strWelcomeTitle = lngWelcome;
	}
	if ($radio_LoggedParticipant) {
		if (isset($_SESSION["Part_FirstName"])) {
			$sFirstName = $_SESSION["Part_FirstName"];
		}
		if (isset($_SESSION["Part_LastName"])) {
			$sLastName = $_SESSION["Part_LastName"];
		} ?>
		<h2 class="head"><?= $strWelcomeTitle ?></h2>
		<p><?= $sFirstName . " " . $sLastName ?></p>
		<?php
		if (!empty($str_ParticipantPortrait)) {
			get_Any_Media($str_ParticipantPortrait, "Center", $sFirstName . " " . $sLastName);
		} ?>
		<!--div class="text_small"><?php //echo $memoWelcomeNotes ?></div-->
	<?php
	} else { ?>
		<h2 class="head"><?= $strConditionsTitle ?></h2>
		<div class="text text_small">
			<?= $memoConditionsNotes ?>
		</div>
	<?php
	} ?>
</section>