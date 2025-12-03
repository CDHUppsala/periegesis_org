<section class="jqNavMainToBeCloned">
	<h2 class="head"><span><?= lngNavigation ?></span></h2>
	<div class="sxAccordionNav">
		<ul>
			<?php
			if ($radio_LoggedParticipant) { ?>
				<li><a href="<?= sx_PATH ?>?pg=conference"><?= lngRegisterForConferences ?></a></li>
				<li><a href="<?= sx_PATH ?>?pg=edit"><?= lngEditProfile ?></a></li>
				<?php
				if ($radio_AllowAddProfile) { ?>
					<li><a href="<?= sx_PATH ?>?pg=portrait"><?= lngAddChangePortrait ?></a></li>
				<?php
				}
				if ($radio_RightsToSendAbstracts) { ?>
					<li><a href="<?= sx_PATH ?>?pg=abstract"><?= lngSendEditPaperAbstract ?></a></li>
				<?php
				} ?>
				<li><a href="<?= sx_PATH ?>?pg=leave"><?= lngLeave ?></a></li>
				<li><a href="<?= sx_PATH ?>?pg=logout"><?= lngLogout ?></a></li>
			<?php
			} else { ?>
				<li><a href="<?= sx_PATH ?>?pg=login"><?= lngLogin ?></a></li>
				<?php
				if ($radioAllowOnlineRegistration) { ?>
					<li><a href="<?= sx_PATH ?>?pg=join"><?= lngJoin ?></a></li>
				<?php
				} ?>
				<li><a href="<?= sx_PATH ?>?pg=forgot"><?= lngForgotPassword ?></a></li>
			<?php
			} ?>
		</ul>
	</div>
	<?php
	if ($radio_RightsToUploadFiles) { ?>
		<h2 class="head"><span><?= lngUploadFilesToTheServer ?></span></h2>
		<div class="sxAccordionNav">
			<ul>
				<?php
				if ($radio_ToUploadImages) { ?>
					<li><a href="<?= sx_PATH ?>?pg=images"><?= lngUploadConferenceImages ?></a></li>
				<?php
				}
				if ($radio_ToUploadDocuments) { ?>
					<li><a href="<?= sx_PATH ?>?pg=docs"><?= lngUploadConferenceDocumments ?></a></li>
				<?php
				}
				if ($radio_ToUploadMedia) { ?>
					<li><a href="<?= sx_PATH ?>?pg=media"><?= lngUploadConferenceMedia ?></a></li>
				<?php
				} ?>
			</ul>
		</div>
	<?php
	} ?>
</section>