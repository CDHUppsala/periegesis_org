<section id="ParticipationForm">
	<?php

	/**
	 * Greate the token and its session here, on the server, 
	 */
	$str_FirstEventFormToken = sx_generate_form_token('FirstEventFormToken', 128);

	/**
	 * Get the place of the event
	 */
	$s_PlaceName = $sPlaceName;
	if (!empty($sPlaceAddress)) {
		$s_PlaceName .= ", " . $sPlaceAddress;
	}
	$s_City = 'Athens';
	if (!empty($sPlaceCity)) {
		$s_City = $sPlaceCity;
		$s_PlaceName .= ", " . $sPlaceCity;
	} ?>

	<h2><?= lngRegisterToParticipate ?></h2>
	<div class="overflow_hidden">
		<form class="jq_load_modal_window" name="FirstEventParticipationForm" method="post">
			<input type="hidden" name="FirstEventFormToken" value="<?php echo $str_FirstEventFormToken ?>">
			<input type="hidden" name="FormName" value="EventParticipation" />
			<input type="hidden" name="EventID" value="<?= $iEventID ?>" />
			<input type="hidden" name="FirstEventForm" value="yes" />
			<fieldset>
				<label><?= lngParticipationMode ?>:<br>
					<?php
					if ($strParticipationMode == "Both") { ?>

						<div class="flex_start flex_align_start flex_nowrap">
							<div><input type="radio" name="Mode" value="Live" checked /></div>
							<div><b>In-person in <?php echo $s_City ?>:</b><br>
								<?= $s_PlaceName  ?>
							</div>
						</div>
						<div class="flex_start flex_align_start flex_nowrap">

							<div><input type="radio" name="Mode" value="Online" /></div>
							<div><b>Online via Zoom:</b><br>
								An <b>access link</b> will be sent to your email address.</div>
						</div>
					<?php
					} elseif ($strParticipationMode == "Online") { ?>
						<div class="flex_start flex_align_start flex_nowrap">
							<div><input type="radio" name="Mode" value="Online" checked readonly /></div>
							<div><b>Online via Zoom:</b><br>
								An <b>access link</b> will be sent to your email address.</div>
						</div>
					<?php
					} else { ?>
						<div class="flex_start flex_align_start flex_nowrap">
							<div>
                                <input type="radio" name="Mode" value="Live" checked readonly />
                            </div>
							<div>
								<b>In-person in  <?php echo $s_City ?>:</b><br>
								At <?= $s_PlaceName ?><br>
								<b>Please notice</b>: Online participation is not available for this event.
							</div>
						</div>
					<?php
					} ?>
				</label>
			</fieldset>
			<fieldset class="align_center">
				<input type="submit" name="Submit" value="Sign Up" />
			</fieldset>
		</form>
	</div>

</section>