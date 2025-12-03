<?php

$strNavPath = "about.php?";

$strWhere = "";
if (
	intval($int_AboutGroupID) > 0 &&
	sx_ShowAboutAsideMenuByCurrentGroup &&
	(sx_AboutHeaderMenuByGroups || sx_AboutHeaderMenuByGroupsInList)
) :
	$strWhere = " AND a.AboutGroupID = $int_AboutGroupID ";
endif;

$aResults = "";
$sql = "SELECT a.AboutID, 
	a.AboutGroupID, g.GroupName{$strLangNr} AS GroupName,
	a.AboutCategoryID, c.CategoryName{$strLangNr} AS CategoryName,
	a.Title 
	FROM (about AS a 
		LEFT JOIN about_groups AS g ON a.AboutGroupID = g.AboutGroupID )
		LEFT JOIN about_categories AS c ON a.AboutCategoryID = c.AboutCategoryID
	WHERE a.Hidden = False 
		AND (g.Hidden = False OR g.Hidden IS NULL) 
		AND (c.Hidden = False OR c.Hidden IS NULL)
		{$strWhere} {$strLanguageAnd}
	ORDER BY g.Sorting DESC, g.AboutGroupID ASC, 
		c.Sorting DESC, c.AboutCategoryID ASC,
		a.Sorting DESC, a.InsertDate DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
$stmt = null;

if (is_array($aResults)) { ?>
	<section class="jqNavMainToBeCloned">
		<?php
		//if ($radio_TextAboutHeaderMenuByGroup) { 
		?>
		<h2 class="head"><span><?= $str_TextsAboutTitle ?></span></h2>
		<?php
		//} 
		?>
		<nav class="sxAccordionNav jqAccordionNav">
			<?php
			$iRows = count($aResults);
			$iLoopGroup = 0;
			$radioGroups = true;
			$iLoopCat = 0;
			$radioCat = false;

			for ($r = 0; $r < $iRows; $r++) {
				$iAboutID = $aResults[$r][0];
				$iAboutGroupID = $aResults[$r][1];
				if (intval($iAboutGroupID) == 0) {
					$iAboutGroupID = 0;
				}
				$sGroupName = $aResults[$r][2];
				$iAboutCategoryID = $aResults[$r][3];
				if (intval($iAboutCategoryID) == 0) {
					$iAboutCategoryID = 0;
				}
				$sCategoryName = $aResults[$r][4];
				$sTitle = $aResults[$r][5];
				if ($r == 0 && intval($iAboutGroupID) == 0) {
					echo "<ul>";
					$radioGroups = false;
				}
				if ($radioGroups) {
					if ($iAboutGroupID != $iLoopGroup) {
						if ($radioCat) {
							echo "</ul></li>";
						}
						$radioCat = False;
						if (intval($iLoopGroup) > 0) {
							echo "</ul>";
						} ?>
						<h3 class="slide_up jqToggleNextRight"><span><?= $sGroupName ?></span></h3>
						<ul>
					<?php
					}
					if (intval($iAboutCategoryID) > 0 && $iAboutCategoryID != $iLoopCat) {
						if ($radioCat) {
							echo "</ul></li>";
						}
						echo '<li><div class="open"><span>' . $sCategoryName . '</span></div><ul>';
						$radioCat = True;
					}
				}
				$strClass = "";
				if ($int_AboutID == $iAboutID) {
					$strClass = 'class="open" ';
				} ?>
					<li><a <?= $strClass ?>href="<?= $strNavPath ?>agid=<?= $iAboutGroupID ?>&aboutid=<?= $iAboutID ?>"><?= $sTitle ?></a></li>
				<?php
				$iLoopGroup = $iAboutGroupID;
				$iLoopCat = $iAboutCategoryID;
			}
			if ($iLoopCat > 0) {
				echo "</ul></li>";
			} ?>
						</ul>
		</nav>
	</section>
<?php
}
$aResults = null;
?>