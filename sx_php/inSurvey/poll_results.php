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
	<?php
	$radioShowChart = false;
	if (intval($intQuestionID) > 0) {
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
		$iTotal = 0;
		$sql = "SELECT * FROM poll_questions 
		WHERE QuestionID = ?
		AND ShowInSite = True ";
		$stm = $conn->prepare($sql);
		$stm->execute([$intQuestionID]);
		$rs = $stm->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$radioShowChart = true;
			$intQuestionID = $rs["QuestionID"];
			$strInsertDate = $rs["InsertDate"];
			$strTitle = trim(strip_tags($rs["VoteQuestion"]));
			$arrTitle = get_Lines_From_Title($strTitle, 40);
			$title = "'" . implode("','", $arrTitle) . "'";
			$iTotal = $rs["Total"];
			if (intval($iTotal) > 0) {
				$iColumns = $rs["NumberOfChoices"];
				$labels = array();
				//$data = array();
				$percent = array();
				for ($i = 1; $i < $iColumns + 1; $i++) {
					$labels[] = $rs["Choice" . $i];
					//$data[] = $rs["Vote" . $i];
					$percent[] = round(($rs["Vote" . $i] / $iTotal) * 100, 2);
				}
				$arrBGColor = array_slice($arrBBColor, 0, $iColumns);
			}
			$stm = null;
			$rs = null;
		}
		$strBGColor = implode("','", $arrBGColor);
		$strLabels = implode("','", $labels);
		//$strData = implode(",", $data);
		$strPercent = implode(",", $percent);
	}

	if ($radioShowChart) { ?>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
		<h1 class="head"><span><?= $str_PollTitle .' '. $intQuestionID .': '. $strInsertDate?></span></h1>
		<h4><?= lngTheme . ": " . $strTitle ?></h4>
		<div class="chart_flex">
			<div class="chart_container">
				<canvas id="poll_bar"></canvas>
			</div>
			<div class="chart_container">
				<canvas id="poll_doughnut"></canvas>
			</div>
		</div>
		<script>
			const data_poll = {
				labels: ['<?= $strLabels ?>'],
				datasets: [{
					label: ' % ',
					backgroundColor: ['<?= $strBGColor ?>'],
					data: [<?= $strPercent ?>]
				}, ]
			};
			new Chart('poll_bar', {
				type: 'bar',
				data: data_poll,
				options: {
					aspectRatio: 1,
					maintainAspectRatio: false,
					responsive: true,
					plugins: {
						legend: {
							display: false
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
							text: 'Percent of Total Votes: <?= $iTotal ?>',
							padding: {
								bottom: 10
							}
						}
					}
				}
			});
			new Chart('poll_doughnut', {
				type: 'doughnut',
				data: data_poll,
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
		</script>
		<?php
	}
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