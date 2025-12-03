<?php
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
if (empty($str_SurveyTittle)) {
	$str_SurveyTittle = lngSurvey;
} ?>
<section>
	<h1 class="head"><span><?= lngArchive . ' - ' . $str_SurveyTittle ?></span></h1>
	<?php
	$sql = "SELECT SurveyID, 
		InsertDate, 
		SurveyTheme" . str_LangNr . " AS SurveyTheme,
		SurveyNote" . str_LangNr . " AS SurveyNotes
    FROM surveys 
    WHERE ShowInArchive = True ";
	$stmt = $conn->query($sql);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;

	if (!is_array($result)) { ?>
		<p><?= lngNoResultsFount ?></p>
		<?php
	} else {
		$iRows = count($result);
		for ($row = 0; $row < $iRows; $row++) {
			$intSurveyID = $result[$row]["SurveyID"];
			$strInsertDate = $result[$row]["InsertDate"];
			$strSurveyTheme = $result[$row]["SurveyTheme"];
			$strSurveyNotes = $result[$row]["SurveyNotes"];

			$str_slide_up = "slide_down";
			$str_display = "none";
			if ($row == 0) {
				$str_slide_up = "slide_up";
				$str_display = "block";
			}


			$sql = "SELECT * FROM survey_questions 
			WHERE SurveyID = ?
			$strLanguageAnd
			ORDER BY SurveyQuestionID ASC ";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intSurveyID]);
			$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (is_array($rs)) {
				$iTotal = $rs[0]["Total"];
				$iRcount = count($rs);
				if (intval($iTotal) == 0) { ?>
					<p>
						<?= lngNoVotersYet ?>
					</p>
				<?php
				} else {
				?>
					<h2 class='head <?= $str_slide_up ?> jqToggleNextRight'><span><?= lngSurvey ?>: <?= $intSurveyID ?> | <?= lngDate ?>: <?= $strInsertDate ?></span></h2>
					<div class="overflow_hide" style="display: <?= $str_display ?>">
						<h3><?= lngTheme ?>: <?= $strSurveyTheme ?></h3>
						<div class="text_normal"><?= $strSurveyNotes ?></div>
						<?php
						for ($r = 0; $r < $iRcount; $r++) { ?>
							<h4><?= $rs[$r]["SurveyQuestion"] ?></h4>
							<div class="chart_flex">
								<div class="chart_container">
									<canvas id="survey_bar_<?= $row . $r ?>"></canvas>
								</div>
								<div class="chart_container">
									<canvas id="survey_doughnut_<?= $row . $r ?>"></canvas>
								</div>
							</div>
						<?php
						} ?>
					</div>

					<script>
						<?php
						for ($r = 0; $r < $iRcount; $r++) {
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
							const data_<?= $row . $r ?> = {
								labels: ['<?= $strLabels ?>'],
								datasets: [{
									label: 'Percent of Total Votes: <?= $iTotal ?>',
									backgroundColor: ['<?= $strBGColor ?>'],
									data: [<?= $strPercent ?>]
								}]
							};
							new Chart('survey_bar_<?= $row . $r ?>', {
								type: 'bar',
								data: data_<?= $row . $r ?>,
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
							new Chart('survey_doughnut_<?= $row . $r ?>', {
								type: 'doughnut',
								data: data_<?= $row . $r ?>,
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
				$stmt = null;
				$rs = null;
			}
		}
	}
	$result = null;
	?>
</section>