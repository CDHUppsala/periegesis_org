<?php
if (intval($intQuizThemeID) == 0) {
	header('Location: index.php');
	exit();
}

$iDaysCookieExpires = 30;
if (!empty($_POST["SendQuiz"])) {
	$sxCookieName = "ps_Quiz_" . $intQuizThemeID;
	$radioVotedBefore = False;
	/*
	if (isset($_COOKIE[$sxCookieName])) {
		$radioVotedBefore = True;
	}
	*/
	$arrChecked = array();
	$sql = "SELECT QuizQuestionID,
			Vote1, Vote2, Vote3, Vote4, Vote5, Vote6, Vote7, Total 
			FROM quiz_questions 
			WHERE QuizThemeID = ?
			ORDER BY QuizQuestionID ASC";
	//echo $sql ."<br>";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intQuizThemeID]);
	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;
	if ($rs) {
		$iRows = count($rs);
		for ($r = 0; $r < $iRows; $r++) {
			$intVote = @$_POST["Vote" . $rs[$r]["QuizQuestionID"]];
			$arrChecked[] = $intVote;
			if ($radioVotedBefore == false) {
				if (!empty($intVote)) {
					$iChoiceTotal = $rs[$r]["Vote" . $intVote . ""] + 1;
					$iVoteTotal = $rs[$r]["Total"] + 1;

					$sql = "UPDATE quiz_questions 
					SET Vote" . $intVote . " = ?, 
						Total = ?
					WHERE QuizQuestionID = ? ";
					$stmt = $conn->prepare($sql);
					$stmt->execute([$iChoiceTotal, $iVoteTotal, $rs[$r]["QuizQuestionID"]]);
				}
			}
		}
	}
	$stmt = null;
	$rs = null;
	/*
	if ($radioVotedBefore == False) {
		setcookie($sxCookieName, "Voted", time() + (86400 * $iDaysCookieExpires), "/");
	}
	*/
}

/**
 * To view results by both voters and non-voters
 */

$sql = "SELECT StartDate, QuizTheme" . str_LangNr . " AS QuizTheme, QuizNote" . str_LangNr . " AS QuizNote 
	FROM quiz_themes WHERE QuizThemeID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intQuizThemeID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$strDate = $rs["StartDate"];
	$strQuizTheme = $rs["QuizTheme"];
	$strQuizNotes = $rs["QuizNote"];
}
$stmt = null;
$rs = null;

if (empty($str_QuizTittle)) {
	$str_QuizTittle = lngQuiz;
} ?>
<section>
	<h1 class="head"><span><?= $str_QuizTittle . ": " . $intQuizThemeID . " | " . $strDate ?></span></h1>
	<h2 class="head"><span><?= lngTheme ?>: <?= $strQuizTheme ?></span></h2>
	<?php
	if ($strQuizNotes != "") { ?>
		<div class="vote_wrapper"><?= $strQuizNotes ?></div>
		<?php
	}

	$sql = "SELECT * FROM quiz_questions 
	WHERE QuizThemeID = ?
	ORDER BY QuizQuestionID ASC ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intQuizThemeID]);
	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (!is_array($rs)) {
		header("Location: ../index.php");
		exit();
	} else {
		$count = $rs[0]["Total"];
		if ($count == 0) { ?>
			<p><?= lngNoVotersYet ?></p>
			<?php
		} else {
			$iSumCorrect = 0;
			$iRows = count($rs);
			for ($r = 0; $r < $iRows; $r++) {
				$iCorrect = $rs[$r]["CorrectChoiceNumber"]
			?>
				<div class="vote_wrapper">
					<p class="indent"><span><?= $r + 1 . ". " ?></span> <?= $rs[$r]["QuizQuestion"] ?></p>
					<table>
						<tr>
							<?php
							$iColumns = $rs[$r]["NumberOfChoices"] + 1;
							$iWidth = number_format(100 / ($iColumns - 1), 2);
							for ($i = 1; $i < $iColumns; $i++) { ?>
								<td style="width: <?= $iWidth . '%' ?>">
									<?= number_format(($rs[$r]["Vote" . $i] / $count) * 100, 2) ?>%<br>
									<img src="../imgPG/barV.gif" style="width:64px; height:<?= floor(($rs[$r]["Vote" . $i] / $count) * 200) ?>px"><br>
									<?= $rs[$r]["Vote" . $i] ?>
								</td>
							<?php
							} ?>
						</tr>
						<tr>
							<?php
							if (isset($arrChecked) && $iCorrect == $arrChecked[$r]) {
								$iSumCorrect++;
							}
							for ($i = 1; $i < $iColumns; $i++) {
								$strClass = "";
								if (isset($arrChecked) && !empty($arrChecked)) {
									if ($arrChecked[$r] == $i) {
										$strClass = ' bg_warning';
									}
								}
								if ($iCorrect == $i) {
									$strClass = ' bg_success';
								} ?>
								<td class="text_xxsmall<?= $strClass ?>"><?= $rs[$r]["Choice" . $i] ?></td>
								<?php
							} ?>
						</tr>
					</table>
				</div>
			<?php
			} ?>

			<p><b><?= lngTotalVotes . ": " . $count ?></b></p>
	<?php
		}
		$iPerCent = number_format(($iSumCorrect / $iRows) * 100, 2);
		echo "<p>Correct unswers: " . $iSumCorrect . " of " . $iRows . " (" . $iPerCent . "%)</p>";
	}
	$rs = null;
?>
</section>