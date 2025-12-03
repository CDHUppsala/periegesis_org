<?php

/**
 * Get Initial articles by theme
 */
$aResults = null;
if (intval($intForumID) > 0) {
	$sql = "SELECT InsertID, Title, FirstName, LastName, InsertDate
	FROM forum_articles 
	WHERE ForumID = ?
		AND ResponseID = 0 {$str_LanguageAnd}
	ORDER BY InsertDate DESC, InsertID DESC ";
	$query = $conn->prepare($sql);
	$query->execute([$intForumID]);
	$rs = $query->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults = $rs;
	}
	$query = null;
	$rs = null;
	if (is_array($aResults)) {
		$iRows = count($aResults); ?>
		<section class="jqNavSideToBeCloned">
			<h2 class="head slide_up jqToggleNextRight"><span><?= lngSubject . " " . $intForumID ?></span></h2>
			<nav class="nav_aside">
				<h5><?php echo lngInitialArticles ?></h5>
				<ul class="max_height">
					<?php
					for ($r = 0; $r < $iRows; $r++) {
						$iArticleID = (int) $aResults[$r][0];
						$strClass = '';
						if ($intArticleID === $iArticleID) {
							$strClass = 'class="open" ';
						} ?>
						<li>
							<a <?php echo $strClass ?>href="forum.php?forumID=<?= $intForumID ?>&articleID=<?php echo $iArticleID ?>">
								<?= $aResults[$r][1] ?>, <span><?= $aResults[$r][2] . ' ' . $aResults[$r][3] . ', ' . $aResults[$r][4] ?></span></a>
						</li>
					<?php
					} ?>
				</ul>
			</nav>
		</section>
	<?php
	}
}

/**
 * Get recent contributions by date
 */
$aResults = null;
if (intval($intForumID) > 0) {
	$sql = "SELECT a.InsertID, a.ResponseID, 
		a.ForumID, 
		a.Title, a.FirstName, a.LastName, a.InsertDate, 
		f.ForumTheme{$str_LangNr} AS ForumTheme 
	FROM forum AS f
		INNER JOIN forum_articles AS a
		ON f.ForumID = a.ForumID 
	WHERE f.Publish = True 
		AND a.Hidden = False 
		AND a.InitialArticle = False 
		AND a.ForumID = ? {$str_LanguageAnd}
	ORDER BY a.InsertDate DESC, a.InsertID DESC LIMIT 8";
	$query = $conn->prepare($sql);
	$query->execute([$intForumID]);
	$rs = $query->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults = $rs;
	}
	$rs = null;
}
if (is_array($aResults)) {
	$iRows = count($aResults);
	?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_up jqToggleNextRight"><span><?= lngRecentContributions ?></span></h2>
		<nav class="nav_aside">
			<ul class="max_height">
				<?php
				for ($r = 0; $r < $iRows; $r++) {
					$iInsertID = $aResults[$r][0] ? (int) $aResults[$r][0] : 0;

					$iResponseID = $aResults[$r][1] ? (int) $aResults[$r][1] : 0;

					$iForumID = $aResults[$r][2] ? (int) $aResults[$r][2] : 0;

					$sTitle = $aResults[$r][3];
					$sFirstName = $aResults[$r][4];
					$sLastName = $aResults[$r][5];
					$dInsertDate = $aResults[$r][6];
					$sForumTheme = $aResults[$r][7];

					$strClass = '';
					if ((int) $intAnchor === $iInsertID) {
						$strClass = 'class="open" ';
					}

					if (floor($iResponseID) > 0) {
						$intResponseID = $iResponseID;
						$iAnchor = $iInsertID;
					} else {
						$intResponseID = $iInsertID;
						$iAnchor = 0;
					} ?>
					<li>
						<a <?php echo $strClass ?>href="forum.php?forumID=<?= $iForumID ?>&anchor=<?= $iAnchor ?>&articleID=<?= $intResponseID ?>#<?= $iInsertID ?>">
							<?= $sTitle ?> <span><?= $sFirstName . " " . $sLastName . ", " . $dInsertDate ?></span>
							<span class="light">[<?= LNG_Forum_Theme . ": " . $sForumTheme ?>]</span></a>
					</li>
				<?php
				} ?>
			</ul>
		</nav>
	</section>
<?php
}
$aResults = null;
?>