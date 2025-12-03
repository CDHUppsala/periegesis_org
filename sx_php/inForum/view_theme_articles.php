<?php
if (intval($intForumID) == 0) {
	header("Location: forum.php");
	exit();
} ?>
<section>
	<h1 class="head slide_up jqToggleNextRight"><span><?= lngSubject . " " . $intForumID . ": «" . $strForumTheme . "»" ?></span></h1>
	<div class="text text_bg"><div class="text_max_width"><?= $memoForumNote ?></div></div>
	<?php
	/**
	 * Create an extra field with the wished Sorting,
	 * - order DESC or ASC
	 * - insertID should always be in ASC order
	 * 	 to have every main artical (with lowest ID) first
	 */
	$aResults = [];
	$sql = "SELECT InsertID, ResponseID, FirstName, LastName, InsertDate, Title, 
		IF(ResponseID = 0, InsertID, ResponseID) as Ordering
	FROM forum_articles 
	WHERE ForumID = ? 
	AND Hidden = False " . str_LanguageAnd . "
	ORDER BY Ordering DESC, insertID";
	$query = $conn->prepare($sql);
	$query->execute([$intForumID]);
	$rs = $query->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults = $rs;
	}
	$rs = null;

	if (empty($aResults)) {
		header("Location: forum.php");
		exit;
	} else {
		$iRows = count($aResults);
	?>
		<div class="bar flex_between">
			<h3><?= lngArticlesAndComments ?>: <?= $iRows ?></h3>
			<button class="button-grey button-gradient" id="jqSortComments"><?= lngChangeOrder ?></button>
			<?php
			if ($radioLoginToParticipate == false || $radio___ForumMemberIsActive) {
				if ($radioShowAsActual) { ?>
					<button class="button-grey button-gradient" id="jqAddComments"><?= lngAddNewArticle ?></button>
			<?php }
			} ?>
		</div>

		<div class="nav_aside">
			<ul class="no_margin" id="jqSortComments_Target">
				<?php
				$radioResponse = false;
				$iLoop = $iRows;
				$iArticleID = 0;
				for ($iRow = 0; $iRow < $iRows; $iRow++) {
					$intInsertID = $aResults[$iRow][0];
					$intResponseID = $aResults[$iRow][1];
					$strFirstName = $aResults[$iRow][2];
					$strLastName = $aResults[$iRow][3];
					$dateInsertDate = $aResults[$iRow][4];
					$strTitle = $aResults[$iRow][5];
					if (sx_isValidDate($dateInsertDate) && sx_IsDateTime($dateInsertDate)) {
						$dateInsertDate = (new DateTime($dateInsertDate))->format('Y-m-d');
					}
					if (intval($intResponseID) == 0) {
						$iArticleID = $intInsertID;
						if ($iRow > 0) {
							if ($radioResponse) {
								echo "</ol></li>";
							} else {
								echo "</li>";
							}
						}

						$radioResponse = true;
						$jq = " jqToggleNextRight";
						$slideClass = "slide_down";
						if ($iRow == ($iRows - 1) || $aResults[$iRow + 1][1] == 0) {
							$jq = "";
							$slideClass = "";
							$radioResponse = false;
						}
						echo '<li>'; ?>
						<h4 class="<?= $slideClass . $jq ?>">
							<span><?= $iLoop ?></span>
							<a href="forum.php?forumID=<?= $intForumID ?>&articleID=<?= $iArticleID ?>"><?= $strTitle ?>
								<br><span><?= $strFirstName . " " . $strLastName . ", " . $dateInsertDate ?></span></a>
						</h4>
						<?php
						if ($radioResponse) {
							echo '<ol style="display: none">';
						}
						$iLoop--;
					} else { ?>
						<li><a href="forum.php?forumID=<?= $intForumID ?>&articleID=<?= $iArticleID ?>&anchor=<?= $intInsertID ?>#<?= $intInsertID ?>">
								<?= $strTitle ?>, <span><?= $strFirstName . " " . $strLastName . ", " . $dateInsertDate ?></span></a>
						</li>
				<?php
					}
				}
				if ($radioResponse) {
					echo "</ol></li>";
				} else {
					echo "</li>";
				}
				?>
			</ul>
		</div>
	<?php
	}
	$aResults = null;

	if ($radioLoginToParticipate && $radio___ForumMemberIsActive === false) { ?>
		<div class="bg_info align_center">
			<p><?= lngForumSubscribeToParticipate ?></p>
		</div>
		<?php
	} else {
		if ($radioShowAsActual) {
			$s_Title = "";
			include __DIR__ . "/add_new.php";
		} else { ?>
			<div class="bg_info align_center">
				<p><?= lngCanNotParicipateOldTheme ?></p>
			</div>
	<?php }
	} ?>
</section>