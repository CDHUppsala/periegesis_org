<?php
$sql = "SELECT * 
    FROM poll_questions 
    WHERE ShowInSite = True 
    AND (StartDate <= '" . date('Y-m-d') . "' OR StartDate Is Null) 
    AND (EndDate >= '" . date('Y-m-d') . "' OR EndDate Is Null) 
    " . str_LanguageAnd . "
	ORDER BY StartDate desc";
$stm = $conn->query($sql);
if ($stm->rowcount() > 0) {
	if (strlen($str_PollTitle) == 0) {
		$str_PollTitle = lngGallup;
	} ?>
	<section>
		<h2 class="head"><span><?= $str_PollTitle ?></span></h2>
		<?php
		while ($rs = $stm->fetch(PDO::FETCH_ASSOC)) {
			$questionID = $rs["QuestionID"];
			$numberOfChoices = $rs["NumberOfChoices"] + 1; ?>
			<div class="vote_wrapper">
				<form name="Form<?= $questionID ?>" method="post" action="surveys.php?p=p">
					<input type="hidden" name="QuestionID" value="<?= $questionID ?>" />
					<h4><?= $rs["VoteQuestion"] ?></h4>
					<?php
					for ($i = 1; $i < $numberOfChoices; $i++) { ?>
						<p class="indent">
							<span><input class="inRadio" type="radio" value="<?= $i ?>" name="vote" onClick="radioSelection='<?= $i ?>'"></span>
							<?= $rs["Choice" . $i] ?>
						</p>
					<?php
					} ?>
					<p><input class="button-grey button-gradient" type="submit" value="<?= lngVote ?>" onclick="return radio();"></p>
					<p>
						<a href="surveys.php?p=p&questionID=<?= $questionID ?>"> » <?= lngViewResults ?></a><br>
					</p>
				</form>
			</div>
		<?php
		} ?>
		<div class="vote_wrapper">
			<a href="surveys.php?p=pa"> » <?= $str_PollTitle . ": " . lngViewArchives ?></a>
		</div>
	</section>
<?php
}
$stm = null;
?>