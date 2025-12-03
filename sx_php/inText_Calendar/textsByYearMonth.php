<?php
$strCurrentURL = "texts.php";
 
$aResults = "";
$sql = "SELECT Year(t.PublishedDate) AS AsYear, 
	    Month(t.PublishedDate) AS AsMonth, 
    	COUNT(*) AS AsCount 
	FROM ".sx_TextTableVersion." AS t 
    	INNER JOIN text_groups AS g ON t.GroupID = g.GroupID 
	WHERE (t.PublishedDate IS NOT NULL) AND  g.Hidden = False ". str_LoginToReadAnd_Grupp. str_LanguageAnd ." 
	GROUP BY Year(PublishedDate), Month(PublishedDate) 
	ORDER BY Year(PublishedDate) DESC, Month(PublishedDate) DESC ";
    $stmt = $conn->prepare($sql);
	$stmt->execute();
    if ($stmt->rowcount() > 0) {
        $aResults = $stmt->fetchAll(PDO::FETCH_NUM);
    }
    $stmt = null;

	/**
	 * This calendar might be included in Accordion or Tab menus, 
	 * where the value of $displayYearMonth is defined in relation to other alternative archives of text.
	 * If it is displayed separately, check and redifine the value of the variable. 
	 */
	If (!isset($displayYearMonth)) {
		$displayYearMonth = 'block';
	}
 
if (is_array($aResults)) { ?>
	<ul style="display: <?=$displayYearMonth?>">
	<?php
	$iRows = count($aResults);
	$loopYear = -1;
	for ($r=0; $r < $iRows; $r++) {
		$iYear = $aResults[$r][0];
		$iMonth = $aResults[$r][1];
		$iCount = $aResults[$r][2];
		if (intval($loopYear) != intval($iYear)) {
			if (intval($loopYear) > 0) {echo "</ul></li>";}
			$loopYear = $iYear;
			$strClass = "";
			$strStyle = "none";
			if (intval($int_Year) > 0 && intval($int_Year) == intval($iYear)) {
				$strClass = ' class="open"';
				$strStyle = "block";
			} ?>
			<li><div<?=$strClass?>><?=$iYear?></div>
				<ul style="display: <?=$strStyle?>"> 
			<?php
		} 
		$strClass = "";
		if (intval($int_Month) > 0 && intval($int_Month) == intval($iMonth)) {
			$strClass = ' class="open"';
		} ?>
					<li><a<?=$strClass?> title="<?=lngViewArchives.": ".lngMonthsArticles." ".$sxMonthsGen[$iMonth-1]." ".$iYear?>" 
					href="<?=$strCurrentURL?>?month=<?= $iMonth?>&year=<?= $iYear?>">
					<?=$sxMonths[$iMonth-1]?> <span>(<?=$iCount?>)</span></a></li> 
			<?php
		$loopYear = $iYear;
	} ?>
				</ul>
			</li>
		</ul>
<?php }
$aResults = "";
?>