<?php
$iDaysCookieExpires = 7;

$intQuestionID = 0;
if (!empty($_POST["QuestionID"])) {
	$intQuestionID = (int) $_POST["QuestionID"];
} elseif (isset($_GET["questionID"])) {
	$intQuestionID = (int) $_GET["questionID"];
}

$intVote = 0;
if (!empty($_POST["vote"])) {
	$intVote = (int) $_POST["vote"];
}

if ($intVote > 0 && $intQuestionID > 0) {
	$sxCookieName = "ps_vote_poll_" . $intQuestionID;
	if (isset($_COOKIE[$sxCookieName]) && $_COOKIE[$sxCookieName] == "Voted") {
		$radioVoteBefore = true;
	} else {
		$radioVoteBefore = false;
	}

	if ($radioVoteBefore == false) {
		//Get current vote sums
		$iVoteTotal = 0;
		$sql = "SELECT QuestionID, Vote1, Vote2, Vote3, Vote4, Vote5, Vote6, Vote7, Total 
			FROM poll_questions 
			WHERE ShowInSite = True 
			AND QuestionID = ? ";
		$stm = $conn->prepare($sql);
		$stm->execute([$intQuestionID]);
		$rs = $stm->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$iChoiceTotal = $rs["Vote" . $intVote . ""] + 1;
			$iVoteTotal = $rs["Total"] + 1;
		}
		$stm = null;
		$rs = null;

		if ($iVoteTotal > 0) {
			$sql = "UPDATE poll_questions 
			SET Vote" . $intVote . " = ?, 
			Total = ?
			WHERE QuestionID = ? ";
			$stm = $conn->prepare($sql);
			$stm->execute([$iChoiceTotal, $iVoteTotal, $intQuestionID]);

			setcookie($sxCookieName, "Voted", time() + (86400 * $iDaysCookieExpires), "/");
		}
	}
} ?>

<section>
	<h1 class="head"><span><?= str_SiteTitle . ": " . $str_PollTitle ?></span></h1>
	<?php
	if (intval($intQuestionID) > 0) {
		$count = 0;
		$sql = "SELECT * FROM poll_questions 
		WHERE QuestionID = ?
		AND ShowInSite = True ";
		$stm = $conn->prepare($sql);
		$stm->execute([$intQuestionID]);
		$rs = $stm->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$count = $rs["Total"];
			if (empty($str_PollTitle)) {
				$str_PollTitle = lngGallup;
			} ?>
			<div class="vote_wrapper">
				<p><?= $rs["VoteQuestion"] ?></p>
				<?php
				if (intval($count) > 0) { ?>
					<table>
						<tr>
							<?php
							$iColumns = $rs["NumberOfChoices"];
							$iWidth = floor(100 / $iColumns);
							for ($i = 1; $i < $iColumns + 1; $i++) { ?>
								<td style="width: <?= $iWidth . '%' ?>">
									<?= number_format(($rs["Vote" . $i] / $count) * 100, 2) ?>%<br>
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
				<?php
				} ?>
				<p><b><?= lngTotalVotes ?>:</b> <?= $count ?></p>
			</div>
	<?php
		}
		$stm = null;
		$rs = null;
	}
	?>
	<?php
	if (intval($intVote) > 0 && intval($intQuestionID) > 0) {
		if ($radioVoteBefore) { ?>
			<p><?= lngVoteNotCountedCookieFound ?>: <b><?= $sxCookieName . "=" . $_COOKIE[$sxCookieName] ?></b></p>
		<?php
		} else { ?>
			<p><?= lngVoteCountedCookieAdded ?>: <b><?= $sxCookieName . "=Voted" ?></b></p>
		<?php
		}
	}
	if (strpos(sx_HOST_PATH, "surveys.php") == 0) { ?>
		<p><a href="JavaScript:window.close()"><?= lngCloseTheWindow ?></a></p>
	<?php
	} ?>
</section>