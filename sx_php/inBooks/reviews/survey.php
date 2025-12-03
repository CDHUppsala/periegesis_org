<?php

$daysCookieExpires = 30;

$intVote = @$_POST["vote"];
if (return_Filter_Integer($intVote) == 0) {
	$intVote = 0;
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && intval($intVote) > 0 && intval($iBookID) > 0 && $strFormType == "survey") {

	$sxCookieName = "sx_Vote_BookID_" . $iBookID;
	$radioVote = true;
	if (isset($_COOKIE[$sxCookieName])) {
		$radioVote = false;
	}

	if ($radioVote) {
		// Get current vote sums
		$intChoiceTotal = 1;
		$intVoteTotal = 1;
		$intVoteTotalValues = $intVote;
		$radioUpdate = False;
		$sql = "SELECT vote1, vote2, vote3, vote4, vote5, vote6, vote7, vote8, vote9, vote10, TotalVotes, TotalValues 
		FROM book_survey 
		WHERE BookID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iBookID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$radioUpdate = True;
			$intChoiceTotal = $rs["vote" . $intVote . ""] + 1;
			$intVoteTotal = $rs["TotalVotes"] + 1;
			$intVoteTotalValues = $rs["TotalValues"] + $intVote;
		}
		$rs = null;
		$stmt = null;

		// Update vote sums
		if ($radioUpdate) {
			$sql = "UPDATE book_survey 
			SET Vote" . $intVote . " = ?, 
				TotalVotes = ?, 
				TotalValues = ?
			WHERE BookID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intChoiceTotal, $intVoteTotal, $intVoteTotalValues, $iBookID]);
		} else {
			$sql = "INSERT INTO book_survey 
				(BookID, InsertDate, vote" . $intVote . ", TotalVotes, TotalValues) 
				VALUES (?,?, 1, 1, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$iBookID, date("Y-m-d"), $intVote]);
		}
		// Add a cookies in the visitors computer
		setcookie($sxCookieName, "Voted", time() + (86400 * $daysCookieExpires), "/");
	}
}
$intNumberOfStars = sx_intNumberOfStars;
if (return_Filter_Integer($intNumberOfStars) == 0) {
	$intNumberOfStars = 5;
}
if (empty($strSurveyTitle)) {
	$strSurveyTitle = lngCurrentQuestion;
} ?>
<section class="survey" id="SurveyBooks">
	<div class="bar">
		<h3><?= $strSurveyTitle ?></h3>
	</div>
	<?php
	//== To view the results
	if (intval($iBookID) > 0) {
		$intTotalVotes = 0;
		$intAverage = 0;
		$sql = "SELECT * FROM book_survey 
			WHERE BookID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iBookID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$intTotalVotes = $rs["TotalVotes"];
			if (intval($intTotalVotes) > 0) { ?>
				<table class="no_borders">
					<tr>
						<?php
						for ($i = 1; $i < ($intNumberOfStars + 1); $i++) {
							$iVote = $rs["Vote" . $i];
							$iTemp = ($iVote / $intTotalVotes);
							$iImg = intval($iTemp * 200);
							if (intval($iImg) == 0) {
								$iImg = 1;
							} ?>
							<td style="white-space:nowrap; vertical-align:bottom; padding: 0 2px; text-align:center">
								<?= number_format($iTemp * 100, 2) ?>%<br>
								<img src="../../imgPG/barV.gif" style="width:40px; height:<?= $iImg ?>px;"><br>
								<?= $iVote ?><br>
								<?= $i ?>*
							</td>
						<?php
						} ?>
					</tr>
				</table>
		<?php
				$intAverage = number_format($rs["TotalValues"] / $intTotalVotes, 2);
			}
		}
		$rs = null;
		$stmt = null;
		?>
		<p><b><?= lngTotalVotes ?>:</b> <?= $intTotalVotes ?></p>
		<?php
		sx_getBookStarsImage($intAverage);

		if (intval($intVote) > 0) {
			if ($radioVote) { ?>
				<p class="bg_success"><?= lngVoteCountedCookieAdded ?>: <b><?= $sxCookieName . " = Voted" ?></b></p>
			<?php
			} else {
			?>
				<p class="bgWarning"><?= lngVoteNotCountedCookieFound ?>: <b><?= $sxCookieName . " = " . $_COOKIE[$sxCookieName] ?></b></p>
	<?php
			}
		}
	} ?>
	<form name="SurvayBook" method="post" target="xVote" action="books.php?bookID=<?= $iBookID ?>&frm=survey#SurveyBooks">
		<fieldset>
			<?php
			for ($i = 1; $i <= $intNumberOfStars; $i++) { ?>
				<input class="inRadio" type="radio" value="<?= $i ?>" name="vote" onClick="radioSelection='<?= $i ?>'">
				<div class="five_stars"><span style="width:<?= round($i * 20) ?>%"></span></div>&nbsp;<?= $i ?>*<br>
			<?php } ?>
		</fieldset>
		<fieldset>
			<p><input type="submit" value="<?= lngVote ?>" onclick="return radio();"></p>
		</fieldset>
	</form>
</section>