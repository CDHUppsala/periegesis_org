<section>
	<h1 class="head"><span><?= $strWelcomeTitle ?></span></h1>
	<div class="text"><div class="text_max_width"><?= $memoWelcomeNote ?></div></div>
	<h2><?php echo LNG_Forum_Actual_Themes; ?></h2>
	<table>
		<tr>
			<th colspan="2" style="width: 100%"><?= LNG_Forum_Themes ?></th>
			<th><?= lngContributions ?></th>
			<th><?= lngRecent ?></th>
		</tr>
		<?php
		$aResults = forum_get_active_themes();
		if (!empty($aResults)) {
			$iRows = count($aResults);
			for ($r = 0; $r < $iRows; $r++) {
				$iForumID = $aResults[$r][0];
				$arrArticles = forum_countItemsByTheme($iForumID);
				if (!empty($arrArticles)) {
					$Number = $arrArticles[0];
					$Date = $arrArticles[1];
				} else {
					$Number = 0;
					$Date = $aResults[$r][2];
				} ?>
				<tr>
					<td><?= $iForumID ?>.&nbsp;</td>
					<td class="width_100"><a href=" forum.php?forumID=<?= $iForumID ?>"><?= $aResults[$r][1] ?></a></td>
					<td class="align_right"><?= $Number ?></td>
					<td class="white_space_nowrap"><?= (new DateTime($Date))->format('Y-m-d') ?></td>
				</tr>
			<?php
			}
		} else { ?>
			<tr>
				<td colspan="4"><?= lngNoRecords ?></td>
			</tr>
		<?php
		} ?>
	</table>

	<h2><?php echo LNG_Forum_Previous_Themes ?></h2>
	<table>
		<tr>
			<th colspan="2" style="width: 100%"><?= LNG_Forum_Themes ?></th>
			<th><?= lngContributions ?></th>
			<th><?= lngRecent ?></th>
		</tr>
		<?php
		$aResults = forum_get_previous_themes();
		if (!empty($aResults)) {
			$iRows = count($aResults);
			for ($r = 0; $r < $iRows; $r++) {
				$iForumID = $aResults[$r][0];
				$arrArticles = forum_countItemsByTheme($iForumID);
				if (!empty($arrArticles)) {
					$Number = $arrArticles[0];
					$Date = $arrArticles[1];
				} else {
					$Number = 0;
					$Date = $aResults[$r][2];
				} ?>
				<tr>
					<td><?= $iForumID ?>.&nbsp;</td>
					<td class="width_100"><a href=" forum.php?forumID=<?= $iForumID ?>"><?= $aResults[$r][1] ?></a></td>
					<td class="align_right"><?= $Number ?></td>
					<td class="white_space_nowrap"><?= (new DateTime($Date))->format('Y-m-d') ?></td>
				</tr>
			<?php
			}
		} else { ?>
			<tr>
				<td colspan="4"><?= lngNoRecords ?></td>
			</tr>
		<?php
		} ?>
	</table>


	<?php
	$aResults = null;
	?>
</section>
