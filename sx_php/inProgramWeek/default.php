<?php

function get_programWeekFast($con)
{
	$sql = "SELECT ProgramID, WeekDayNumber, WeekDayName, StartTime, EndTime, 
        ProgramTitle, ProgramSubTitle,  TextID 
    FROM week_program 
    WHERE Hidden = False " . str_LanguageAnd
		. " ORDER BY WeekDayNumber ASC, StartTime ASC ";
	$stmt = $con->query($sql);
	$rs = $stmt->fetchAll(PDO::FETCH_NUM);
	$stmt = null;
	if ($rs) {
		return  $rs;
	} else {
		return null;
	}
}

if ($radio_IncludeWeekProgram) {
	$sql = "SELECT UseWeekProgram, WeekProgramTitle, Notes 
        FROM week_program_setup " . str_LanguageWhere;
	$stmt = $conn->query($sql);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioUseWeekProgram = $rs["UseWeekProgram"];
		$strWeekProgramTitle = $rs["WeekProgramTitle"];
		$strNotes = $rs["Notes"];
	}
	$stmt = null;
	$rs = null;
}

$aResults = null;
if (isset($radioUseWeekProgram) && $radioUseWeekProgram) {
	$aResults = get_programWeekFast($conn);
}

if (is_array($aResults)) { ?>
	<section>
		<div class="print float_right">
			<?php
			getTextPrinter("sx_PrintPage.php?print=weekly", "week");
			?>
		</div>
		<h2 class="head"><span><?= $strWeekProgramTitle ?></span></h2>
		<?php
		if (!empty($strNotes)) { ?>
			<div class="text_normal text_xsmall"><p><?= $strNotes ?></p></div>
		<?php } ?>
		<div class="pagination">
			<ul>
				<li>
					<!--Neccessary to use the class pagination-->
				</li>
				<li class="remove_styles">
					<?php if (is_array($aResults)) { ?>
						<ul class="jqWeekTabs">
							<?php
							for ($i = 1; $i <= 7; $i++) {
								$strClass = "";
								if ($i == return_Week_Day_1_7(date('Y-m-d'))) {
									$strClass = 'class="active"';
								} ?>
								<li <?= $strClass ?>><span title="<?= $sxEuDays[$i - 1] . " " . return_Week_Day_0_6(date('Y-m-d')) . " " . return_Week_Day_1_7(date('Y-m-d')) ?>"><?= trim(mb_substr($sxEuDays[$i - 1], 0, 1)) ?></span></li>
							<?php } ?>
							<li title="<?= lngWeekProgram ?>"><span><?= mb_substr($sxEuDays[0], 0, 1) . "-" . mb_substr($sxEuDays[6], 0, 1) ?></span></li>
						</ul>
					<?php } ?>
				</li>
				<li>
					<!--Neccessary to use the class pagination-->
				</li>
			</ul>
		</div>
		<ul class="events_by_week">
			<?php
			$iRows = count($aResults);
			$intDaysWeekDay = return_Week_Day_1_7(date('Y-m-d'));
			$loopWeek = 0; //Loops to 7 week days
			$loopNext = 0; //Loops to the Next week day from the current loop'
			for ($r = 0; $r < $iRows; $r++) {
				$intProgramID = $aResults[$r][0];
				$iWeekDayNumber = $aResults[$r][1];
				$strWeekDayName =  $aResults[$r][2];
				$strStartTime = $aResults[$r][3];
				$strEndTime = $aResults[$r][4];
				$strProgramTitle = $aResults[$r][5];
				$strProgramSubTitle = $aResults[$r][6];
				$iTextID = $aResults[$r][7];

				$iDOW = intval($iWeekDayNumber);
				//==== If first week days are empty
				if (intval($loopWeek) == 0  && intval($loopWeek) < intval($iDOW - 1)) {
					for ($l = 1; $l < $iDOW; $l++) {
						if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
							$strDispaly = "block";
						} else {
							$strDispaly = "none";
						}
						$loopWeek = $loopWeek + 1; ?>
						<li style="display:<?= $strDispaly ?>;">
							<h4><?= $sxEuDays[$l] ?></h4>
							<div><?= lngNoEvents ?></div>
						</li>
					<?php
					}
				}

				//==== If middle week days are empty
				if (intval($loopNext) > 0  && intval($loopNext) < intval($iDOW)) { ?>
					</li>
					<?php
					$intLoop = intval($loopWeek) + (intval($iDOW) - intval($loopNext));
					for ($l = intval($loopWeek); $l <= $intLoop - 1; $l++) {
						if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
							$strDispaly = "block";
						} else {
							$strDispaly = "none";
						}
						$loopWeek = $loopWeek + 1 ?>
						<li style="display:<?= $strDispaly ?>;">
							<h4><?= $sxEuDays[$l]  ?></h4>
							<div><?= lngNoEvents ?></div>
						</li>
					<?php
					}
				}

				//==== Incert new week day
				if (intval($loopWeek) < intval($iDOW)) {
					if (intval($loopNext) == intval($iDOW)) { ?>
						</li>
					<?php  }
					if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
						$strDispaly = "block";
					} else {
						$strDispaly = "none";
					} ?>
					<li style="display:<?= $strDispaly ?>">
						<h4><?= $sxEuDays[$iDOW - 1] ?></h4>
					<?php
				} ?>
					<div>
						<?php
						$strLeft = "";
						$strRight = "";
						if (intval($iTextID) > 0) {
							$strLeft = '<a href="articles.php?tid=' . $iTextID . '">';
							$strRight = "</a>";
						} ?>
						<h5><?= $strLeft . $strProgramTitle . $strRight ?></h5>
						<span><?= $strStartTime . " - " . $strEndTime ?></span>
						<div class="text_normal text_xsmall"><?= $strProgramSubTitle ?></div>
					</div>
				<?php
				$loopWeek = $iDOW;
				$loopNext = $iDOW + 1;
			} ?>
					</li>
					<?php
					//=== If last week days are empty
					if (intval($loopWeek) < 7) {
						for ($l = intval($loopWeek); $l <= 6; $l++) {
							if (intval($loopWeek) == intval($intDaysWeekDay)) {
								$strDispaly = "block";
							} else {
								$strDispaly = "none";
							} ?>
							<li style="display:<?= $strDispaly ?>;">
								<h4><?= $sxEuDays[$l] ?></h4>
								<div><?= lngNoEvents ?></div>
							</li>
					<?php
							$loopWeek = $loopWeek + 1;
						}
					} ?>
		</ul>
	</section>

<?php }
$aResults = null;
?>