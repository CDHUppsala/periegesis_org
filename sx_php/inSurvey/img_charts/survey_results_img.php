<?php
if (intval($intSurveyID) == 0) {
	header('Location: index.php');
	exit();
}

$iDaysCookieExpires = 30;
if (!empty($_POST["SendSurvey"])) {
	$sxCookieName = "ps_survey_" . $intSurveyID;
	if (isset($_COOKIE[$sxCookieName])) {
		$radioVotedBefore = True;
	} else {
		$radioVotedBefore = False;
	}

	if ($radioVotedBefore == False) {
		$sql = "SELECT SurveyQuestionID, SurveyID, 
			Vote1,Vote2,Vote3,Vote4,Vote5,Vote6,Vote7,Vote8,Vote9,Vote10,Total 
			FROM survey_questions 
			WHERE SurveyID = ?
			ORDER BY SurveyQuestionID ASC";
		//echo $sql ."<br>";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intSurveyID]);
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = null;
		if ($rs) {
			$iRows = count($rs);
			for ($r = 0; $r < $iRows; $r++) {
				$intVote = @$_POST["vote" . $rs[$r]["SurveyQuestionID"]];
				if (!empty($intVote)) {
					$iChoiceTotal = $rs[$r]["Vote" . $intVote . ""] + 1;
					$iVoteTotal = $rs[$r]["Total"] + 1;

					$sql = "UPDATE survey_questions
					SET Vote" . $intVote . " = ?, 
						Total = ?
					WHERE SurveyQuestionID = ? ";
					$stmt = $conn->prepare($sql);
					$stmt->execute([$iChoiceTotal, $iVoteTotal, $rs[$r]["SurveyQuestionID"]]);
				}
			}
		}
		$stmt = null;
		$rs = null;
		setcookie($sxCookieName, "Voted", time() + (86400 * $iDaysCookieExpires), "/");
	}
}

/**
 * To view results by both voters and non-voters
 */

$sql = "SELECT * FROM surveys WHERE SurveyID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$strDate = $rs["InsertDate"];
	$strSurveyTheme = $rs["SurveyTheme"];
	$strSurveyNotes = $rs["SurveyNote"];
}
$stmt = null;
$rs = null;

if (empty($str_SurveyTittle)) {
	$str_SurveyTittle = lngSurvey;
} ?>
<section>
	<h1 class="head"><span><?= $str_SurveyTittle . ": " . $intSurveyID . " | " . lngDate . ": " . $strDate ?></span></h1>
	<h2 class="head"><span><?= lngTheme ?>: <?= $strSurveyTheme ?></span></h2>
	<?php
	if ($strSurveyNotes != "") { ?>
		<div class="vote_wrapper"><?= $strSurveyNotes ?></div>
		<?php
	}

	$sql = "SELECT * FROM survey_questions 
	WHERE SurveyID = ?
	ORDER BY SurveyQuestionID ASC ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intSurveyID]);
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
			$iRows = count($rs);
			for ($r = 0; $r < $iRows; $r++) { ?>
				<div class="vote_wrapper">
					<p class="indent"><span><?= $r + 1 . ". " ?></span> <?= $rs[$r]["SurveyQuestion"] ?></p>
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
							for ($i = 1; $i < $iColumns; $i++) { ?>
								<td><?= $rs[$r]["Choice" . $i] ?></td>
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
	}
	$rs = null;

	/**
	 * To hide following comments for non voters
	 */
	if (!empty($_POST["SendSurvey"])) {
		if ($radioVotedBefore) { ?>
			<p><?= lngVoteNotCountedCookieFound ?>: <?= $sxCookieName . "=" . $_COOKIE[$sxCookieName] ?></p>
		<?php
		} else { ?>
			<p><?= lngVoteCountedCookieAdded ?>: <?= $sxCookieName . "=Voted" ?></p>
	<?php
		}
	} ?>
</section>