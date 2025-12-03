<?php
if (!isset($int_PaperID) || intval($int_PaperID) == 0) {
	header("Location: index.php");
	exit;
}

$radioTemp = false;

if (intval($int_PaperID) > 0) {
	$rs = sx_getPapers(0, $int_PaperID);
	if ($rs) {
		$radioTemp = true;
		$iConferenceID = $rs['ConferenceID'];
		$iSessionID = $rs['SessionID'];
		$dPresentationDate = $rs['PresentationDate'];
		$strStartTime = return_Time_Minutes($rs['StartTime']);
		$strEndTime = return_Time_Minutes($rs['EndTime']);
		$strPaperTitle = $rs['PaperTitle'];
		$strPaperSubTitle = $rs['PaperSubTitle'];
		$strPaperAuthors = $rs['PaperAuthors'];
		$strSpeakers = $rs['Speakers'];
		$strAuthorPortraits = $rs["AuthorPortraits"];
		$strImageLinks = $rs["ImageLinks"];
		$strMediaURL = $rs['MediaURL'];
		$strPDFAttachements = $rs['PDFAttachements'];
		$memoAboutAuthors = $rs['AboutAuthors'];
		$memoAbstract = $rs['Abstract'];
		$memoMainText = $rs['MainText'];
	}
	$rs = null;
}

if ($radioTemp) { ?>

	<section id="jq_CopyPrint">
		<header>
			<h5><?= $str_ConferenceTitle ?></h5>
			<h1 class="head"><span><?= lngConferencePaper ?></span></h1>
			<?php
			if (!empty($str_SessionTitle)) { ?>
				<h5>
					<?php
					echo $str_SessionTitle . "<br>";
					echo return_Week_Day_Name($date_SessionDate) . " " . $date_SessionDate . "<br>" . $date_SessionStartTime . " | " . $date_SessionEndTime;
					if (!empty($str_SessionPlaceName)) {
						echo "<br>" . lngPlace . ": " . $str_SessionPlaceName;
					}
					?>
				</h5>
			<?php
			} ?>
			<?php
			if ($radio_LoggedParticipant) {
				if (!empty($str_WebinarURL)) {
					echo '<p class="text_small"><b>Live Connection URL:</b> <a target="_blank" href="' . $str_WebinarURL . '">' . lngClickHere . '</a></p>';
				}
			} ?>
		</header>
		<article>
			<h2 class="head"><span><?= $strPaperTitle ?></span></h2>
			<?php
			if (!empty($strPaperSubTitle)) { ?>
				<h3><?= $strPaperSubTitle ?></h3>
			<?php }
			$strAuthors = "";

			if (!empty($strPaperAuthors)) {
				$strAuthors = $strPaperAuthors;
			}
			if (!empty($strSpeakers)) {
				if (!empty($strAuthors)) {
					$strAuthors .= ", ";
				}
				$strAuthors .= $strSpeakers;
			}
			if (!empty($dPresentationDate)) {
				if (!empty($strAuthors)) {
					$strAuthors .= ", ";
				}
				$strAuthors .= $dPresentationDate;
			}
			$strTime = null;
			if (!empty($strStartTime)) {
				$strTime = ', ' . lngTime . ': ' . $strStartTime;
				if (!empty($strEndTime)) {
					$strTime .= ' - ' . $strEndTime;
				}
				$strAuthors .= $strTime;
			}
			if (!empty($strAuthors)) { ?>
				<h4><?= $strAuthors ?></h4>
				<?php }

			$radioMediaLinks = false;
			if ($radio_ShowSocialMediaInText) {
				$radioMediaLinks = true;
			}

			include PROJECT_PHP . "/basic_PrintIncludes.php";

			/**
			 * Check if paper attachments should be alos shown
			 * The same check is repeated in attachments.php
			 */
			$radioShowAttachments = false;
			if ($radio_RegisterToViewPaperAttachments == false) {
				if ($radio_LoginToViewPaperAttachments == false || $radio_LoggedParticipant) {
					$radioShowAttachments = true;
				}
			} elseif ($radio_LoggedParticipant) {
				$radioIsRegistered = sx_RegisteredForThisConference($int_ConferenceID, $int_ParticipantID);
				if ($radioIsRegistered) {
					$radioShowAttachments = true;
				}
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


			if (!empty($memoAbstract)) { ?>
				<h3 class="jq_PrintNext bg_grey slide_up jqToggleNextRight"><?= lngAbstract ?></h3>
				<div class="text text_resizeable">
					<div class="text_max_width"><?= $memoAbstract; ?></div>
				</div>
			<?php  }

			if (!empty($memoMainText) && $radioShowAttachments) { ?>
				<h3 class="jq_PrintNext slide_down jqToggleNextRight"><?= lngConferencePaper ?></h3>
				<div style="display: none" class="text text_resizeable">
					<div class="text_max_width"><?= $memoMainText ?></div>
				</div>
			<?php
			}

			if (!empty($strPDFAttachements && $radioShowAttachments)) { ?>
				<h3><?= lngDownloadPaperFiles ?></h3>
				<div class=" text">
					<div class="text_max_width">
						<?php echo sx_getLinksToPDF($strPDFAttachements, false); ?>
					</div>
				</div>
			<?php
			}

			if (!empty($memoAboutAuthors)) { ?>
				<h3><?= lngAboutTheAuthors ?></h3>
				<div class="text text_resizeable">
					<div class="text_max_width">
						<footer><?= $memoAboutAuthors ?></footer>
					</div>
				</div>
			<?php }
			if (!empty($strAuthorPortraits)) {
				get_Photo_Portrates($strAuthorPortraits);
			} ?>

		</article>
	</section>
	<section class="flex_between">
		<button class="button-grey button-gradient-border jq_CopyToClipboard" data-id="jq_CopyPrint">Copy to Clipboard as Text</button>
		<button class="button-grey button-gradient-border jq_PrintDivElement" data-id="jq_CopyPrint">Print as PDF</button>
	</section>
<?php
} ?>