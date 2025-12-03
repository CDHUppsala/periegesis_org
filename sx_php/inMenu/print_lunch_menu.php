<?php
$intWeekDay = 0;
if (isset($_GET['date']) && return_Is_Date($_GET['date'])) {
	$thisMonday = $_GET['date'];
	$intWeek = return_Week_In_Year($thisMonday);
	$intWeekDay = return_Week_Day_1_7($thisMonday);
}
if ($intWeekDay != 1) {
	$tempMondayObj = new DateTime('monday this week');
	$thisMonday = $tempMondayObj->format('Y-m-d');
	$intWeek = return_Week_In_Year($thisMonday);
}

$aResults = null;
$sql = "SELECT w.WeekDay, 
	l.GroupID, 
	l.LunchTitle" . str_LangNr . " AS LunchTitle, 
	l.LunchPrice, 
	l.LunchImage, 
	l.LunchNotes" . str_LangNr . " AS LunchNotes,
	g.GroupName" . str_LangNr . " AS GroupName
FROM (menu_lunch_groups AS g
	INNER JOIN menu_lunch AS l
		ON l.GroupID = g.GroupID)
    INNER JOIN menu_lunch_weekly AS w 
    	ON (l.LunchID = w.LunchID) 
WHERE w.Hidden=0 AND l.Hidden=0
ORDER BY w.WeekDay ASC, 
	w.Sorting DESC, 
	w.GroupID ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$aResults = $rs;
}
$stmt = null;
$rs = null;
?>

<body>
	<?php
	if (empty($strExport)) { ?>
		<a target="_top" href="sx_PrintPage.php?print=lunchmenu&date=<?= $requestDate ?>&export=print"><?= lngSavePrintInPDF ?></a> |
		<a target="_top" href="sx_PrintPage.php?print=lunchmenu&date=<?= $requestDate ?>&export=word"><?= lngSaveInWord ?></a> |
		<a target="_top" href="sx_PrintPage.php?print=lunchmenu&date=<?= $requestDate ?>&export=html"><?= lngSaveInHTML ?></a>
		<hr>
	<?php
	} ?>

	<h1><?= $str_LunchMenuLinkTitle ?></h1>
	<?php
	if ($memo_LunchTopNote != "") { ?>
		<p class="textSmal align_center bg_grey"><?= $memo_LunchTopNote ?></p>
	<?php
	} ?>

	<h3><?= lngWeek . " " . $intWeek ?></h3>
	<p><?= $sxEuDays[0] . " " . $thisMonday . " - " . $sxEuDays[6] . " " . return_Add_To_Date($thisMonday, 6) ?></p>

	<?php
	if (!is_array($aResults)) { ?>
		<h3><?= lngRecordsNotFound ?></h3>
		<?php
	} else {
		echo "<table>";
		$iRows = count($aResults);
		$loopDay = 0;
		for ($r = 0; $r < $iRows; $r++) {
			$iWeekDay = $aResults[$r][0];
			$iGroupID = $aResults[$r][1];
			$sLunchTitle = $aResults[$r][2];
			$sLunchPrice = $aResults[$r][3];
			$sLunchImage = $aResults[$r][4];
			$sLunchNotes = $aResults[$r][5];
			$sGroupName = $aResults[$r][6];

			if ($loopDay != $iWeekDay) {
				$strWeekDay = $sxEuDays[$iWeekDay - 1]; ?>
				<tr>
					<?php
					if ($radio_UseLunchImages) { ?>
						<th>&nbsp;</th>
					<?php
					} ?>
					<th><span><?= $strWeekDay . " " . return_Add_To_Date($thisMonday, ($iWeekDay - 1)) ?></span></th>
					<th>&nbsp;</th>
				</tr>
			<?php
			} ?>
			<tr>
				<?php
				if ($radio_UseLunchImages) { ?>
					<td style="width: 20%"><img alt="<?=$sLunchTitle?>" src="<?= sx_ROOT_HOST ?>/images/<?= $sLunchImage ?>"></td>
				<?php
				} ?>
				<td style="width: 100%">
					<b><?= $sGroupName . ": " . $sLunchTitle ?></b><br>
					<?= $sLunchNotes ?>
				</td>
				<td style="white-space:nowrap"><?= SX_usedCurrency . $sLunchPrice ?></td>
			</tr>
		<?php
			$loopDay = $iWeekDay;
		}
	}
	$aResults = null;


	if ($radio_UseDailyLunchMenu) {
		$sql = "SELECT 
			di.LunchTitle" . str_LangNr . " AS LunchTitle, 
			di.LunchPrice, 
			di.LunchImage, 
			di.LunchNotes" . str_LangNr . " AS LunchNotes,
			g.GroupName" . str_LangNr . " AS GroupName
		FROM (menu_lunch_daily AS da 
			INNER JOIN menu_lunch AS di
				ON da.LunchID = di.LunchID)
			INNER JOIN menu_lunch_groups as g
				ON di.GroupID = g.GroupID
		WHERE da.Hidden = False AND di.Hidden = False AND g.Hidden = False
		ORDER BY di.GroupID, da.Sorting DESC";
		$rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		if ($rs) { ?>
			<tr>
				<?php
				if ($radio_UseLunchImages) { ?>
					<th>&nbsp;</th>
				<?php
				} ?>
				<th><span><?= $str_DailyLunchMenuTitle ?></span></th>
				<th>&nbsp;</th>
			</tr>
			<?php
			$iRows = count($rs);
			for ($r = 0; $r < $iRows; $r++) {
				$sLunchTitle = $rs[$r]["LunchTitle"];
				$sLunchPrice = $rs[$r]["LunchPrice"];
				$sLunchImage = $rs[$r]["LunchImage"];
				$sLunchNotes = $rs[$r]["LunchNotes"];
				$sGroupName = $rs[$r]["GroupName"];
			?>
				<tr>
					<?php
					if ($radio_UseLunchImages) { ?>
						<td style="width: 20%"><img alt="<?=$sLunchTitle?>" src="<?= sx_ROOT_HOST ?>/images/<?= $sLunchImage ?>"></td>
					<?php
					} ?>
					<td style="width: 100%">
						<b><?= $sGroupName . ": " . $sLunchTitle ?></b><br>
						<?= $sLunchNotes ?>
					</td>
					<td>
						<?= SX_usedCurrency . $sLunchPrice ?>
					</td>
				</tr>
		<?php
			}
		}
		$rs = null;
	}

	echo "</table>";
	if ($memo_LunchBottomNote != "") { ?>
		<?= $memo_LunchBottomNote ?>
	<?php
	}
	?>