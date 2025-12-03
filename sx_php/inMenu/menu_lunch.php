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
$prevMonday = return_Add_To_Date($thisMonday, -7);
$nextMonday = return_Add_To_Date($thisMonday, 7);

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
            ON (l.LunchID=w.LunchID) 
    WHERE w.Hidden=0 
        AND l.Hidden=0 
	ORDER BY w.WeekDay ASC,
		w.Sorting DESC, 
		g.GroupID ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$aResults = $rs;
}
$stmt = null;
$rs = null;
?>
<section class="menu_wrapper">
	<h1 class="head align_center"><span><?= $str_LunchMenuLinkTitle ?></span></h1>
	<?php
	if (!is_array($aResults)) { ?>
		<h3><?= lngRecordsNotFound ?></h3>
	<?php
	} else { ?>

		<div class="print">
			<?php
			getTextPrinter("sx_printPage.php?print=lunchmenu&date=" . $thisMonday, "lunchmenu" . $thisMonday);
			getLocalEmailSender("", $str_LunchMenuLinkTitle, $memo_LunchTopNote, "");
			?>
		</div>
		<?php
		if ($memo_LunchTopNote != "") { ?>
			<div class="text_normal bg_grey align_center"><?= $memo_LunchTopNote ?></div>
		<?php
		} ?>

		<div class="pagination">
			<ul>
				<li><a title="<?= lngPreviousWeek ?>" class="leftButton" href="menu.php?LunchMenu=yes&date=<?= $prevMonday ?>">&#10094;&#10094;</a></li>
				<li>
					<h3><?= lngWeek . " " . $intWeek ?></h3>
					<p><?= $sxEuDays[0] . " " . $thisMonday . " - " . $sxEuDays[6] . " " . return_Add_To_Date($thisMonday, 6) ?></p>
				</li>
				<li><a title="<?= lngNextWeek ?>" class="rightButton" href="menu.php?LunchMenu=yes&date=<?= $nextMonday ?>">&#10095;&#10095;</a></li>
		</div>
		<table>
			<?php
			$iRows = count($aResults);
			$loopDay = 0;
			for ($r = 0; $r < $iRows; $r++) {
				$iWeekDay = $aResults[$r][0];
				$iGroupID = $aResults[$r][1];
				$sLunchTitle = $aResults[$r][2];
				$iLunchPrice = $aResults[$r][3];
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
						<th colspan="2" style="width:100%"><span><?= $strWeekDay . " " . return_Add_To_Date($thisMonday, ($iWeekDay - 1)) ?></span></th>
					</tr>
				<?php
				} ?>
				<tr>
					<?php
					if ($radio_UseLunchImages) { ?>
						<td class="width_20"><img alt="" src="../images/<?= $sLunchImage ?>"></td>
					<?php
					} ?>
					<td class="width_100">
						<p><b><?= $sGroupName . ":</b> " . $sLunchTitle ?></p>
						<div class="text_small"><?= $sLunchNotes ?></div>
					</td>
					<td><?= SX_usedCurrency . $iLunchPrice ?></td>
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
			WHERE da.Hidden = False 
				AND di.Hidden = False 
				AND g.Hidden = False
			ORDER BY di.GroupID, da.Sorting DESC";
			$rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			if ($rs) { ?>
				<tr>
					<?php
					if ($radio_UseLunchImages) { ?>
						<th>&nbsp;</th>
					<?php
					} ?>
					<th colspan="2" style="width:100%"><span><?= $str_DailyLunchMenuTitle ?></span></th>
				</tr>
				<?php
				$iRows = count($rs);
				for ($r = 0; $r < $iRows; $r++) {
					$strLunchTitle = $rs[$r]["LunchTitle"];
					$strLunchPrice = $rs[$r]["LunchPrice"];
					$strLunchImage = $rs[$r]["LunchImage"];
					$strLunchNotes = $rs[$r]["LunchNotes"];
					$strGroupName = $rs[$r]["GroupName"];
				?>
					<tr>
						<?php
						if ($radio_UseLunchImages) { ?>
							<td class="width_20"><img alt="" src="../images/<?= $strLunchImage ?>"></td>
						<?php
						} ?>
						<td class="width_100">
							<p><b><?= $strGroupName . ":</b> " . $strLunchTitle ?></p>
							<div class="text_small"><?= $strLunchNotes ?></div>
						</td>
						<td>
							<?= SX_usedCurrency . $strLunchPrice ?>
						</td>
					</tr>
		<?php
				}
			}
			$rs = null;
		} ?>
		</table>
		<?php
		if ($memo_LunchBottomNote != "") { ?>
			<div class="text_normal text_margin"><?= $memo_LunchBottomNote ?></div>
		<?php
		} ?>
</section>