<?php
if (intval($intQuizThemeID) == 0) {
	header('Location: index.php');
	exit();
}

$iDaysCookieExpires = 30;
$radioNewQuiz = false;
$iRows = 0;
if (!empty($_POST["SendQuiz"])) {
	$iSumCorrect = 0;
	$radioNewQuiz = true;
	$sxCookieName = "ps_Quiz_" . $intQuizThemeID;
	$radioVotedBefore = False;
	if (isset($_COOKIE[$sxCookieName])) {
		$radioVotedBefore = True;
	}

	$arrCheckedVotes = array();
	$sql = "SELECT QuizQuestionID, CorrectChoiceNumber,
			Vote1, Vote2, Vote3, Vote4, Vote5, Vote6, Vote7, Total 
			FROM quiz_questions 
			WHERE QuizThemeID = ?
			ORDER BY QuizQuestionID ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intQuizThemeID]);
	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;
	if ($rs) {
		$iRows = count($rs);
		for ($r = 0; $r < $iRows; $r++) {
			$intVote = $_POST["Vote" . $rs[$r]["QuizQuestionID"]] ?? 0;
			$arrCheckedVotes[] = $intVote;
			$iCorrect = $rs[$r]["CorrectChoiceNumber"];
			if ($intVote == $iCorrect) {
				$iSumCorrect++;
			}
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

	if ($radioVotedBefore == False) {
		setcookie($sxCookieName, "Voted", time() + (86400 * $iDaysCookieExpires), "/");
	}
}

/**
 * To view results by both voters and non-voters
 */
$arrScores = array();
$sql = "SELECT StartDate, ShowOnTop,
		QuizTheme" . str_LangNr . " AS QuizTheme, 
		QuizNote" . str_LangNr . " AS QuizNote,
		Scores
	FROM quiz_themes WHERE QuizThemeID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intQuizThemeID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$strDate = $rs["StartDate"];
	$radioShowOnTop = $rs["ShowOnTop"];
	$strQuizTheme = $rs["QuizTheme"];
	$strQuizNotes = $rs["QuizNote"];
	$strScores = $rs["Scores"];
}
$stmt = null;
$rs = null;

/**
 * If Scores is empty, create the array for the first json score information
 * Take into account the case results are open
 * for the first time without a form submit
 */
if (!empty($strScores)) {
	$arrScores = json_decode($strScores, true);
} elseif ($iRows > 0) {
	// Set all array values to 0
	for ($r = 0; $r < $iRows + 1; $r++) {
		$arrScores[$r] = 0;
	}
} else {
	// None vote and Results are open withought sent form
	header('Location: surveys.php');
	exit();
}

/**
 * Add new scores if new vote
 */
if ($radioNewQuiz && $radioVotedBefore == False) {
	$arrScores[$iSumCorrect] = $arrScores[$iSumCorrect] + 1;
	$jsonScores = json_encode($arrScores, 1);
	$sql = "UPDATE quiz_themes 
		SET Scores = ?
		WHERE QuizThemeID = ? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$jsonScores, $intQuizThemeID]);
}

if (empty($str_QuizTittle)) {
	$str_QuizTittle = lngQuiz;
}

$sql = "SELECT * FROM quiz_questions 
WHERE QuizThemeID = ?
ORDER BY QuizQuestionID ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intQuizThemeID]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section>
	<h1 class="head"><span><?= $str_QuizTittle . ": " . $intQuizThemeID . " | " . $strDate ?></span></h1>
	<h2 class="head"><span><?= lngTheme ?>: <?= $strQuizTheme ?></span></h2>
	<?php
	if ($strQuizNotes != "") { ?>
		<div class="vote_wrapper"><?= $strQuizNotes ?></div>
		<?php
	}
	if ($radioShowOnTop && $radioNewQuiz) {


		if (!is_array($rs)) {
			header("Location: ../index.php");
			exit();
		} else {
			$iTotal = $rs[0]["Total"];
			if ($iTotal == 0) { ?>
				<p><?= lngNoVotersYet ?></p>
				<?php
			} else {
				$iRows = count($rs);
				for ($r = 0; $r < $iRows; $r++) {
					$iCorrect = $rs[$r]["CorrectChoiceNumber"]
				?>
					<div class="vote_wrapper">
						<h4 class="indent"><span><?= $r + 1 . ". " ?></span> <?= $rs[$r]["QuizQuestion"] ?></h4>
						<table>
							<tr>
								<?php
								$iColumns = $rs[$r]["NumberOfChoices"] + 1;
								$iWidth = number_format(100 / ($iColumns - 1), 2);
								for ($i = 1; $i < $iColumns; $i++) {
									$strClass = "#ccc";
									if (!empty($arrCheckedVotes)) {
										if ($arrCheckedVotes[$r] == $i) {
											$strClass = '#fc0';
										}
									}
									if ($iCorrect == $i) {
										$strClass = '#2a4';
									} ?>
									<td style="vertical-align: bottom; width: <?= $iWidth . '%' ?>">
										<?= $rs[$r]["Vote" . $i] ?> | <?= number_format(($rs[$r]["Vote" . $i] / $iTotal) * 100, 2) ?>%
										<div style="background-color:<?= $strClass ?>;width:80%; margin: 0 auto; height:<?= floor(($rs[$r]["Vote" . $i] / $iTotal) * 200) ?>px">
										</div>
									</td>
								<?php
								} ?>
							</tr>
							<tr>
								<?php
								for ($i = 1; $i < $iColumns; $i++) {  ?>
									<td class="text_xxsmall"><?= $rs[$r]["Choice" . $i] ?></td>
								<?php
								} ?>
							</tr>
						</table>
					</div>
				<?php
				}
				if ($radioNewQuiz) { ?>
					<p>Correct unswers: <?= $iSumCorrect . " of " . $iRows . " (" . number_format(($iSumCorrect / $iRows) * 100, 2) . "%)" ?></p>
		<?php
				}
			}
		}
		$rs = null;
		?>
		<?php
		if (!empty($_POST["SendQuiz"])) {
			if ($radioVotedBefore) { ?>
				<p><?= lngVoteNotCountedCookieFound ?>: <?= $sxCookieName . "=" . $_COOKIE[$sxCookieName] ?></p>
			<?php
			} else { ?>
				<p><?= lngVoteCountedCookieAdded ?>: <?= $sxCookieName . "=Voted" ?></p>
			<?php
			}
		}
	} else {



		if (!is_array($rs)) {
			header("Location: ../index.php");
			exit();
		} else {
			$iTotal = $rs[0]["Total"];
			if ($iTotal == 0) { ?>
				<p><?= lngNoVotersYet ?></p>
	<?php
			} else {
				$iRows = count($rs);
			}
		}
	} ?>
</section>

<section>
	<h2><?= lngTotalVotes . ": " . $iTotal ?></h2>
	<?php
	if (!empty($arrScores)) {
		echo '<table class="no_styles">';

		for ($r = 0; $r < $iRows + 1; $r++) {
			$loopSum = $arrScores[$r];
			$width = number_format(($loopSum / $iTotal) * 100, 2);
			$bgColor = "transparent";
			if ($loopSum > 0) {
				$bgColor = "#2a4";
			}
			$rowBorders = "";
			if (isset($iSumCorrect) && ($r) === $iSumCorrect) {
				$rowBorders = ' style="border: 1px solid #2a4"';
			}
			echo '<tr>
				<td' . $rowBorders . '>' . $r . '</td><td' . $rowBorders . '>Correct: </td>
				<td style="text-align: right">' . $loopSum . '</td>
				<td style="width: 100%"><div style="width:' . $width . '%; background-color:' . $bgColor . '">&nbsp;</div></td>
				<td>' . $width . '%</td>
				</tr>';
		}
		echo '</table>';
	} ?>
</section>