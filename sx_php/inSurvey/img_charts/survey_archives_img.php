<?php
if (empty($str_SurveyTittle)) {
	$str_SurveyTittle = lngSurvey;
} ?>
<section>
	<h1 class="head"><span><?= lngArchive . ' - ' . $str_SurveyTittle ?></span></h1>
	<?php
	$sql = "SELECT SurveyID, InsertDate, SurveyTheme, SurveyNote
    FROM surveys 
    WHERE ShowInArchive = True ";
	$stmt = $conn->query($sql);
	$recods = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;

	if (!is_array($recods)) { ?>
		<p><?= lngNoResultsFount ?></p>
		<?php
	} else {
		$iRows = count($recods);
		for ($r = 0; $r < $iRows; $r++) {
			$intSurveyID = $recods[$r]['SurveyID'];
			$strDate = $recods[$r]['InsertDate'];
			$strSurveyTheme = $recods[$r]['SurveyTheme'];
			$strSurveyNotes = $recods[$r]['SurveyNote'];
			$str_slide_up = "slide_down";
			$str_display = "none";
		if ($r == 0) {
                $str_slide_up = "slide_up";
                $str_display = "block";
            }
			?>
			<h2 class='head <?=$str_slide_up?> jqToggleNextRight'><span><?= lngSurvey ?>: <?= $intSurveyID ?> | <?= lngDate ?>: <?= $strDate ?></span></h2>
			<div class="overflow_hide" style="display: <?=$str_display?>">
				<h3><?= lngTheme ?>: <?= $strSurveyTheme ?></h3>
				<div class="text_normal"><?= $strSurveyNotes ?></div>
				<?php
				$sql = "SELECT * FROM survey_questions 
            		WHERE SurveyID = ? 
		            ORDER BY SurveyQuestionID ASC ";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$intSurveyID]);
				$x = 1;
				while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$count = $rs['Total'];
					if (intval($count) == 0) { ?>
						<p>
							<?= lngNoVotersYet ?>
						</p>
					<?php
					} else { ?>
						<div class='vote_wrapper'>
							<p class="indent"><span><?= $x . ". " ?></span> <?= $rs["SurveyQuestion"] ?></p>
							<table>
								<tr>
									<?php
									$iColumns = $rs["NumberOfChoices"] + 1;
									$iWidth = number_format(100 / ($iColumns - 1), 2);
									for ($i = 1; $i < $iColumns; $i++) { ?>
										<td style="width: <?= $iWidth . '%' ?>">
											<?= number_format(($rs["Vote" . $i] / $count) * 100, 2) ?>%<br>
											<img src="../imgPG/barV.gif" style="width:64px; height:<?= floor(($rs["Vote" . $i] / $count) * 200) ?>px"><br>
											<?= $rs["Vote" . $i] ?>
										</td>
									<?php
									} ?>
								</tr>
								<tr>
									<?php
									for ($i = 1; $i < $iColumns; $i++) { ?>
										<td><?= $rs["Choice" . $i] ?></td>
									<?php
									} ?>
								</tr>
							</table>
						</div>
					<?php
						$x++;
					} ?>
					<p><b><?= lngTotalVotes ?>:</b> <?= $count ?></p>
				<?php
				} ?>
			</div>
			<!--hide-->
	<?php }
		$stmt = null;
		$rs = null;
	}
	$recods = null;
	?>
</section>