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
$arrBBColor = [
	'rgb(255, 99, 132)',
	'rgb(75, 192, 192)',
	'rgb(54, 162, 235)',
	'rgb(255, 159, 64)',
	'rgb(255, 205, 86)',
	'rgb(153, 102, 255)',
	'rgb(201, 203, 207)',
	'rgb(51, 204, 204)',
	'rgb(51, 204, 132)',
	'rgb(51, 204, 232)'
];

$sql = "SELECT SurveyID, 
		InsertDate, 
		SurveyTheme" . str_LangNr . " AS SurveyTheme,
    	SurveyNote" . str_LangNr . " AS SurveyNotes
	FROM surveys
	WHERE SurveyID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$intSurveyID = $rs["SurveyID"];
	$strInsertDate = $rs["InsertDate"];
	$strSurveyTheme = $rs["SurveyTheme"];
	$strSurveyNotes = $rs["SurveyNotes"];
}
$stmt = null;
$rs = null;

$sql = "SELECT * FROM survey_questions 
	WHERE SurveyID = ?
    $strLanguageAnd
	ORDER BY SurveyQuestionID ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (is_array($rs)) {
	$iTotal = $rs[0]["Total"];
	$iRows = count($rs);
	if ($iTotal > 0) {
		if (empty($str_SurveyTittle)) {
			$str_SurveyTittle = lngSurvey;
		} ?>
		<section>
			<h1 class="head"><span><?= $str_SurveyTittle . " " . $intSurveyID . ", " . lngDate . ": " . $strInsertDate ?></span></h1>
			<h2 class="head"><span><?= lngTheme ?>: <?= $strSurveyTheme ?></span></h2>
			<?php
			if ($strSurveyNotes != "") { ?>
				<div><?= $strSurveyNotes ?></div>
			<?php
			}
			for ($r = 0; $r < $iRows; $r++) { ?>
				<h4><?= $rs[$r]["SurveyQuestion"] ?></h4>
				<div class="chart_flex">
					<div class="chart_container">
						<canvas id="survey_bar_<?= $r ?>"></canvas>
					</div>
					<div class="chart_container">
						<canvas id="survey_doughnut_<?= $r ?>"></canvas>
					</div>
				</div>
			<?php
			} ?>
		</section>

		<script>
			<?php
			for ($r = 0; $r < $iRows; $r++) {
				$strTitle = trim(strip_tags($rs[$r]["SurveyQuestion"]));
				$arrTitle = get_Lines_From_Title($strTitle, 50);
				$title = "'" . implode("','", $arrTitle) . "'";
				$labels = array();
				$percent = array();
				$iColumns = $rs[$r]["NumberOfChoices"] + 1;
				for ($i = 1; $i < $iColumns; $i++) {
					$labels[] = $rs[$r]["Choice" . $i];
					$percent[] = round(($rs[$r]["Vote" . $i] / $iTotal) * 100, 2);
				}
				$arrBGColor = array_slice($arrBBColor, 0, $iColumns);
				$strBGColor = implode("','", $arrBGColor);
				$strLabels = implode("','", $labels);
				$strPercent = implode(",", $percent); ?>
				const data_<?= $r ?> = {
					labels: ['<?= $strLabels ?>'],
					datasets: [{
						label: 'Percent of Total Votes: <?= $iTotal ?>',
						backgroundColor: ['<?= $strBGColor ?>'],
						data: [<?= $strPercent ?>]
					}]
				};
				new Chart('survey_bar_<?= $r ?>', {
					type: 'bar',
					data: data_<?= $r ?>,
					options: {
						aspectRatio: 1,
						maintainAspectRatio: false,
						responsive: true,
						plugins: {
							legend: {
								position: 'top',
								display: false
							},
							title: {
								display: false,
								text: [<?= $title ?>],
								font: {
									size: 14,
									weight: 'bold',
									family: 'arial'
								}
							},
							subtitle: {
								display: true,
								text: 'Percent of Total Votes: <?= $iTotal ?>',
								padding: {
									bottom: 10
								}
							}
						}
					}
				});
				new Chart('survey_doughnut_<?= $r ?>', {
					type: 'doughnut',
					data: data_<?= $r ?>,
					options: {
						aspectRatio: 1,
						maintainAspectRatio: false,
						responsive: true,
						plugins: {
							legend: {
								position: 'top',
							},
							title: {
								display: false,
								text: [<?= $title ?>],
								font: {
									size: 15,
									weight: 'bold',
								}
							},
							subtitle: {
								display: true,
								text: 'Percent of Total Votes: <?= $iTotal ?>'
							}
						}
					}
				});
			<?php
			} ?>
		</script>
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