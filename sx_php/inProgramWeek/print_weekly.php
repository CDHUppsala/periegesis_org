<?php

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

function get_programWeekFast($con)
{
	$sql = "SELECT ProgramID, WeekDayNumber, WeekDayName, StartTime, EndTime, 
        ProgramTitle, ProgramSubTitle,  Notes 
    FROM week_program 
    WHERE Hidden = False " . str_LanguageAnd
		. " ORDER BY WeekDayNumber ASC, StartTime ASC ";
	$stmt = $con->query($sql);
	$rs = $stmt->fetchAll();
	$stmt = null;
	if ($rs) {
		return  $rs;
	} else {
		return null;
	}
}
$aResults = null;
if ($radioUseWeekProgram) {
	$aResults = get_programWeekFast($conn);
} ?>

<body>
	<?php
	if ($strExport == "") { ?>
		<div style="margin: 20px;">
			<?php
		}

		if (!is_array($aResults)) {
			$conn = null;
			echo '<h2>' . lngRecordsNotFound . '</h2>';
			exit();
		} else {
			if ($strExport == "") { ?>
				<p>
					<a href="default.php"><?= lngHomePage ?></a> |
					<a target="_top" href="<?= sx_LOCATION ?>&export=print"><?= lngPrintText ?></a> |
					<a target="_top" href="<?= sx_LOCATION ?>&export=word"><?= lngSaveInWord ?></a> |
					<a target="_top" href="<?= sx_LOCATION ?>&export=html"><?= lngSaveInHTML ?></a>
				</p>
				<hr>
			<?php
			} ?>

			<h4><a href="<?= sx_TrueSiteURL . "/" . sx_CurrentLanguage . "/" ?>"><?= str_SiteTitle ?></a></h4>
			<h1><?= $strWeekProgramTitle ?></h1>
			<?php
			if (!empty($strNotes)) { ?>
				<p><?= $strNotes ?></p>
				<?php
			}
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
				$memNotes = $aResults[$r][7];

				$iDOW = intval($iWeekDayNumber);
				//==== If first week days are empty
				if (intval($loopWeek) == 0  && intval($loopWeek) < intval($iDOW - 1)) {
					for ($l = 1; $l < $iDOW; $l++) {
						if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
							$strDispaly = "block";
						} else {
							$strDispaly = "none";
						}
						$loopWeek = $loopWeek + 1;
				?>
						<h3><?= $sxEuDays[$l] ?></h3>
						<p><?= lngNoEvents ?></p>
					<?php
					}
				}

				//==== If middle week days are empty
				if (intval($loopNext) > 0  && intval($loopNext) < intval($iDOW)) {
					$intLoop = intval($loopWeek) + (intval($iDOW) - intval($loopNext));
					for ($l = intval($loopWeek); $l <= $intLoop - 1; $l++) {
						if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
							$strDispaly = "block";
						} else {
							$strDispaly = "none";
						}
						$loopWeek = $loopWeek + 1 ?>
						<h3><?= $sxEuDays[$l]  ?></h3>
						<p><?= lngNoEvents ?></p>
					<?php
					}
				}

				//==== Incert new week day
				if (intval($loopWeek) < intval($iDOW)) {
					if (intval($loopWeek + 1) == intval($intDaysWeekDay)) {
						$strDispaly = "block";
					} else {
						$strDispaly = "none";
					} ?>
					<hr>
					<h3><?= $sxEuDays[$iDOW - 1] ?></h3>
				<?php
				}
				//==== Incert the programs of the week day 
				?>
				<h4><?= $strProgramTitle ?></h4>
				<p><?= $strStartTime . " - " . $strEndTime ?></p>
				<p><?= $strProgramSubTitle ?></p>
				<?php
				if (!empty($memNotes)) {
					echo $memNotes;
				}
				$loopWeek = $iDOW;
				$loopNext = $iDOW + 1;
			}

			//=== If last week days are empty
			if (intval($loopWeek) < 7) {
				for ($l = intval($loopWeek); $l <= 6; $l++) {
					if (intval($loopWeek) == intval($intDaysWeekDay)) {
						$strDispaly = "block";
					} else {
						$strDispaly = "none";
					} ?>
					<h4><?= $sxEuDays[$l] ?></h4>
					<p><?= lngNoEvents ?></p>
		<?php
					$loopWeek = $loopWeek + 1;
				}
			}
		}
		$aResults = null;
		?>
		<hr>
		<p style="text-align: center;">
			<?= lngPrintedDate ?>&nbsp;<?= Date('Y-m-d') ?><br>
			<?= lngFromWebPage ?>&nbsp;<b><?= str_SiteTitle ?></b><br>
			<?= sx_LOCATION ?>
		</p>
		<?php
		if ($strExport == "") {
			echo "</div>";
		}
		?>
</body>

</html>
<?php
if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php
} ?>