<?php

$toDay = date('Y-m-d');
$date = new DateTime($toDay);
$iWeekDay = $date->format("N");

$aResults = null;
$sql = "SELECT 
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
WHERE w.Hidden=0 
	AND l.Hidden=0
	AND w.WeekDay = ?
ORDER BY w.WeekDay ASC, w.Sorting DESC, w.GroupID ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([$iWeekDay]);
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$iRows = count($rs);
?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head"><span><?= $str_TodaysMenuTitle ?></span></h2>
		<div class="listWrap">
			<ol>
				<?php
				for ($r = 0; $r < $iRows; $r++) {
					$sLunchTitle = $rs[$r][0];
					$iLunchPrice = $rs[$r][1];
					$sLunchImage = $rs[$r][2];
					$sLunchNotes = $rs[$r][3];
					$sGroupName = $rs[$r][4];
					if ($sLunchTitle != "") {
						$sLunchTitle = $sLunchTitle . " (" . $iLunchPrice . "€)";
				?>
						<li><b><?= $sGroupName . ": " . $sLunchTitle ?></b>
							<?php
							if ($sLunchNotes != "") { ?>
								<div><?= $sLunchNotes ?></div>
							<?php
							} ?>
						</li>
				<?php
					}
				}
				?>
			</ol>
		</div>
	</section>
<?php
}
$stmt = null;
$rs = null;
?>