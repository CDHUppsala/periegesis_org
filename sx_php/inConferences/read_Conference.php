<?php
if (!isset($_GET["confid"]) || intval($int_ConferenceID) == 0) {
	header("Location: index.php");
	exit;
} else {
	$aResults = sx_getConferences($int_ConferenceID);

	if (is_array($aResults)) {
		$intConferenceID = $aResults['ConferenceID'];

		$dateStartDate = $aResults['StartDate'];
		$dateEndDate = $aResults['EndDate'];

		$strPlaceName = $aResults['PlaceName'];
		$strPlaceAddress = $aResults['PlaceAddress'];
		$strPlacePostalCode = $aResults['PlacePostalCode'];
		$strPlaceCity = $aResults['PlaceCity'];
		$strOrganizers = $aResults['Organizers'];
		$strSponsors = $aResults['Sponsors'];
		$strContactPhone = $aResults['ContactPhone'];
		$strTitle = $aResults['Title'];
		$strSubTitle = $aResults['SubTitle'];
		$strImageLinks = $aResults["ImageLinks"];
		$strMediaURL = $aResults['MediaURL'];
		$strPDFAttachements = $aResults['PDFAttachements'];

		$radioShowSessionsInConference = $aResults['ShowSessionsInConference'];

		$memoNotes = $aResults['Notes'];
	} ?>
	<section id="jq_CopyPrint">
		<header>
			<h1 class="head"><span><?= $strTitle ?></span></h1>
			<?php
			if (!empty($strSubTitle)) { ?>
				<h2 class="head"><span><?php echo $strSubTitle ?></span></h2>
			<?php
			} ?>
			<div class="text_bg align_center">
				<?php
				if (!empty($dateStartDate)) {
					echo return_Week_Day_Name($dateStartDate) . " " . $dateStartDate;
				}
				if (!empty($dateEndDate)) {
					echo " | " . return_Week_Day_Name($dateEndDate) . " " . $dateEndDate;
				}
				if (!empty($strPlaceName)) {
					echo "<br><b>" . lngPlace . "</b>: <span>" . $strPlaceName . "</span>";
				}
				if (!empty($strPlaceAddres)) {
					echo "<span>, " . $strPlaceAddress . "</span>";
				}
				if (!empty($strPlacePostalCode)) {
					echo "<span>, " . $strPlacePostalCode . "</span>";
				}
				if (!empty($strPlaceCity)) {
					echo "<span>, " . $strPlaceCity . "</span>";
				}
				if (!empty($strOrganizers)) {
					echo "<br><b>" . lngOrganizers . "</b>: <span>" . $strOrganizers . "</span>";
				}
				if (!empty($strContactPhone)) {
					echo "<br><b>" . lngPhone . "</b>: <span>" . $strContactPhone . "</span>";
				} ?>
			</div>

			<?php
			if (!empty($strSponsors)) {
				echo "<p><b>" . lngWithTheKindSupport . "</b>: <br><span>" . $strSponsors . "</span></p>";
			}

			if ($radio_LoggedParticipant) {
				if (!empty($str_WebinarURL)) {
					echo '<p class="text_small"><b>Live Connection URL:</b> <a href="' . $str_WebinarURL . '">' . lngClickHere . '</a></p>';
				}
			} ?>
		</header>
		<article>
			<?php
			$radioMediaLinks = false;
			if ($radio_ShowSocialMediaInText) {
				$radioMediaLinks = true;
			}

			include PROJECT_PHP . "/basic_PrintIncludes.php";

			$radioShowAttachments = false;
			if ($radio_LoginToViewConferenceAttachments == false || $radio_LoggedParticipant) {
				$radioShowAttachments = true;
			}


			if (!empty($strImageLinks)) {
				if (strpos($strImageLinks, ";")) {
					get_Manual_Image_Cycler($strImageLinks, "", "");
				} else {
					get_Any_Media($strImageLinks, "Center", "");
				}
			}

			if (!empty($strMediaURL) && $radioShowAttachments) {
				if (strpos($strMediaURL, ";") > 0) { ?>
					<div>
						<?php
						$arrMediaPath = explode(";", $strMediaURL);
						$lenth = count($arrMediaPath);
						for ($m = 0; $m < $lenth; $m++) {
							$sMediaPath = trim($arrMediaPath[$m]);
							$strMediaNotes = sx_getLinkNameFromFileName($sMediaPath);
							get_Any_Media($sMediaPath, 'Center', $strMediaNotes);
						} ?>
					</div>
				<?php
				} else {
					$strMediaNotes = sx_getLinkNameFromFileName($strMediaURL);
					get_Any_Media($strMediaURL, 'Center', $strMediaNotes);
				}
			}

			if (!empty($memoNotes)) { ?>
				<div class="text text_resizeable">
					<div class="text_max_width"><?= $memoNotes; ?></div>
				</div>
			<?php
			}

			if (!empty($strPDFAttachements && $radioShowAttachments)) { ?>
				<h3><?= lngDownloadConferenceFiles ?></h3>
				<div class=" text">
					<div class="text_max_width">
						<?php echo sx_getLinksToPDF($strPDFAttachements, false); ?>
					</div>
				</div>
				<?php
			}

			if ($radioShowSessionsInConference) {
				$arrSessions = sx_getSessions($int_ConferenceID, 0);
				if (is_array($arrSessions)) { ?>
					<h3 class="jq_PrintNext bg_grey slide_up jqToggleNextRight"><?= lgnConferenceSessions ?></h3>
					<div style="overflow: hidden;">
						<?php
						foreach ($arrSessions as $row) {
							$iSessionID = $row["SessionID"];
							$sSessionTitle = $row["SessionTitle"];
							$sSessionSubTitle = $row["SessionSubTitle"];
							$sBreak = $row["Break"];
							$dSessionDate = $row["SessionDate"];
							$dSessionStartTime = return_Time_Minutes($row["StartTime"]);
							$dSessionEndTime = return_Time_Minutes($row["EndTime"]);
							$sSessionPlaceName = $row["PlaceName"];
							$memoSessionNotes = $row["Notes"];
						?>
							<h4 class="jq_PrintNext slide_left_down jqToggleNextLeft"><?= return_Week_Day_Name($dSessionDate) . " " . $dSessionDate . " | " . $dSessionStartTime . " | " . $dSessionEndTime ?></h4>
							<div class="text text_resizeable" style="display: none; overflow: hidden;">
								<div class="text_max_width">
									<?php
									if ($sBreak) { ?>
										<h4><?= $sSessionTitle ?></h4>
									<?php
									} else { ?>
										<h4><a href="conferences.php?sesid=<?= $iSessionID ?>"><?= $sSessionTitle ?></a></h4>
									<?php
									} ?>
									<h5><?= $sSessionSubTitle ?></h5>
									<?php echo $memoSessionNotes ?>
								</div>
							</div>
						<?php
						} ?>
					</div>
			<?php
				}
				$arrSessions = null;
			} ?>
		</article>

		<div class="jq_NoPrint align_center">
			<a href="conferences.php?program=yes&confid=<?= $int_ConferenceID ?>">
				<button class="button button-shadow button-arrow"><span><?= lngConferenceProgram ?></span></button></a>
			<a href="conferences_login.php?pg=join">
				<button class="button button-shadow-border"><?= lngRegister ?></button></a>
			<a href="conferences_login.php">
				<button class="button button-shadow-white"><?= lngLogin ?></button></a>
		</div>
	</section>

	<section class="flex_between">
		<button class="button-grey button-gradient-border jq_CopyToClipboard" data-id="jq_CopyPrint">Copy to Clipboard as Text</button>
		<button class="button-grey button-gradient-border jq_PrintDivElement" data-id="jq_CopyPrint">Print as PDF</button>
	</section>
<?php
} ?>