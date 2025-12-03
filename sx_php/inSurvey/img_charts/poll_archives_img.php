<?php
if (strlen($str_PollTitle) == 0) {
	$str_PollTitle = lngGallup;
} ?>
<section>
	<h1 class="head"><span><?= lngArchives . " - " . $str_PollTitle ?></span></h1>
	<?php
	/**
	 * Display all vote results except surveys
	 */

	$sql = "SELECT * FROM poll_questions 
		WHERE ShowInArchive = True 
		" . str_LanguageAnd . "
		ORDER BY StartDate DESC";
	$sm = $conn->query($sql);

	$x = 0;
	while ($rs = $sm->fetch(PDO::FETCH_ASSOC)) {
		$count = $rs["Total"];
		$strBG = "";
		if ($x == 1) {
			$strBG = " whiteBG";
		}
	?>
		<div class="vote_wrapper">
		<h3><?= $rs["StartDate"] ?></h3>
		<p><?= "<b>" . lngTheme . "</b>: " . $rs["VoteQuestion"] ?></p>
			<?php
			if ($count == 0) { ?>
				<p><?= lngNoVotersYet ?></p>
			<?php
			} else { ?>
					<table>
						<tr>
						<?php
							$iColumns = $rs["NumberOfChoices"];
							$iWidth = floor(100/$iColumns);
							for ($i = 1; $i < $iColumns + 1; $i++) { ?>
								<td style="width: <?=$iWidth .'%'?>"><?= number_format(($rs["Vote" . $i] / $count) * 100, 2) ?>%<br>
									<img src="../../imgPG/barV.gif" style="width: 64px; height: <?= floor(($rs["Vote" . $i] / $count) * 300) ?>px;"><br>
									<?= $rs["Vote" . $i] ?>
								</td>
							<?php } ?>
						</tr>
						<tr>
							<?php
							for ($i = 1; $i < $iColumns + 1; $i++) { ?>
								<td><?= $rs["Choice" . $i] ?></td>
							<?php } ?>
						</tr>
					</table>
				<p><b><?= lngTotalVotes ?>:</b>: <b><?= $count ?></b></p>
			<?php
			} ?>
		</div>
	<?php
		if ($x == 1) {
			$x = 0;
		} else {
			$x = 1;
		}
	}
	$sm = null;
	?>
	<p><a href="JavaScript:window.close()"><?= lngCloseTheWindow ?></a></p>
</section>
