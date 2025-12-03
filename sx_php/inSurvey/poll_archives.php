<?php
if (strlen($str_PollTitle) == 0) {
	$str_PollTitle = lngGallup;
} ?>
<section>
	<h1 class="head"><span><?= $str_PollTitle ." ". lngArchives ?></span></h1>
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
		$strInsertDate = $rs["InsertDate"];
		$strTitle = trim(strip_tags($rs["VoteQuestion"]));
		$arrTitle = get_Lines_From_Title($strTitle, 40);
		$title = "'" . implode("','", $arrTitle) . "'";
		$iTotal = $rs["Total"];
	?>
		<h2><?= $str_PollTitle . ": " . $strInsertDate ?></h2>
		<h4><?= $strTitle ?></h4>

		<?php
		if ($iTotal == 0) { ?>
			<p><?= lngNoVotersYet ?></p>
		<?php
		} else { ?>
			<div class="chart_flex">
				<div class="chart_container">
					<canvas id="poll_bar_<?= $x ?>"></canvas>
				</div>
				<div class="chart_container">
					<canvas id="poll_doughnut_<?= $x ?>"></canvas>
				</div>
			</div>
			<?php
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

			$strBGColor = implode("','", $arrBGColor);
			$strLabels = implode("','", $labels);
			//$strData = implode(",", $data);
			$strPercent = implode(",", $percent);
			?>
			<script>
				const data_poll_<?= $x ?> = {
					labels: ['<?= $strLabels ?>'],
					datasets: [{
						label: ' % ',
						backgroundColor: ['<?= $strBGColor ?>'],
						data: [<?= $strPercent ?>]
					}, ]
				};
				new Chart('poll_bar_<?= $x ?>', {
					type: 'bar',
					data: data_poll_<?= $x ?>,
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
				new Chart('poll_doughnut_<?= $x ?>', {
					type: 'doughnut',
					data: data_poll_<?= $x ?>,
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
		$x++;
	}
	$sm = null;
	?>
</section>