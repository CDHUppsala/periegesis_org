<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/functionsTableName.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

$iConferenceID = 0;
$iParticipantID = 0;
$aAbstract = null;
$aParts = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["ConferenceID"])) {
		$iConferenceID = (int)$_POST["ConferenceID"];
	}
	if (intval($iConferenceID) > 0) {
		$sql = "SELECT p.ParticipantID, p.LastName, p.FirstName 
		FROM conf_abstracts AS a
			INNER JOIN conf_participants AS p
			ON a.ParticipantID = p.ParticipantID 
		WHERE a.ConferenceID = ?
		ORDER BY p.LastName";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iConferenceID]);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aParts = $rs;
		}
		$rs = null;
	}

	if (isset($_POST["ParticipantID"])) {
		$iParticipantID = (int)$_POST["ParticipantID"];
	}
	if (intval($iParticipantID) > 0 && intval($iConferenceID) > 0) {
		$sql = "SELECT AbstractID,
			Title,
			SubTitle,
			Coauthors,
			Abstract,
			InsertDate,
			UpdateDate
		FROM conf_abstracts
		WHERE ConferenceID = ?
			AND ParticipantID = ?
		LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iConferenceID, $iParticipantID]);
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rs) {
			$aAbstract = $rs;
		}
		$rs = null;
	}
}

$aConfs = null;
$sql = "SELECT ConferenceID, Title 
	FROM conferences 
	ORDER BY ConferenceID DESC";
$rs = $conn->query($sql)->fetchAll();
if ($rs) {
	$aConfs = $rs;
}
$rs = null;

?>
<section id="bodyUpload" class="jqInsertText">
	<h3><span class="info floatRight jqInfoToggle">?</span>Copy Paper Abstracts</span></h3>
	<div class="bg_gray" style="display: none;">
		<ol class="nerrow">
			<li>Select first a Conference to get a list with all participants that have sent an Abstract.</li>
			<li>Select then a Paricipant to get the Abstract.</li>
			<li>Mark a field on the abstract and double click within the corresponding field of the Paper to copy the content.</li>
			<li><b>Obs!</b> If you are edditing a Paper, you must copy the <b>Textareas</b> from the Abstract manually:
				<ol>
					<li>Copy the <b>Textareas</b> from the Abstract.</li>
					<li>Click on the icon <code><></code> in edditinga area of the Paper.</li>
					<li>Paste the copied text in the window that opens and click on Save.</li>
				</ol>
			</li>
		</ol>
	</div>
	<form action="ps_conferences/ajax_paperAbstracts.php" method="post" name="PaperAbstracts" class="jqLoadSelectForm">
		<label>Select Conference:<br>
			<select name="ConferenceID">
				<option value="0">Select Conference</option>
				<?php
				if (is_array($aConfs)) {
					$count = count($aConfs);
					for ($c = 0; $c < $count; $c++) {
						$selected = "";
						if ($iConferenceID == $aConfs[$c][0]) {
							$selected = "selected ";
						} ?>
						<option <?= $selected ?>value="<?= $aConfs[$c][0] ?>"><?= $aConfs[$c][1] ?></option>
				<?php
					}
					$aConfs = null;
				} ?>
			</select>
		</label>
		<label>Select a Participant:<br>
			<select name="ParticipantID">
				<option value="0">Select Participant</option>
				<?php
				if (is_array($aParts)) {
					$count = count($aParts);
					for ($c = 0; $c < $count; $c++) {
						$selected = "";
						if ($iParticipantID == $aParts[$c][0]) {
							$selected = "selected ";
						} ?>
						<option <?= $selected ?>value="<?= $aParts[$c][0] ?>"><?= $aParts[$c][1] . " " . $aParts[$c][2] ?></option>
				<?php
					}
					$aParts = null;
				} ?>
			</select>
		</label>
		<label><input type="submit" value="Get Selection" name="Submit"></label>
	</form>
	<?php
	if (is_array($aAbstract)) { ?>
		<p class="text_small"><b>Check an Abstract Field and Double Click in the corresponding Paper Field.</b><br>
			Abstract ID: <?= $aAbstract["AbstractID"] ?><br>
			<?= $aAbstract["InsertDate"] ?> Insert Date<br>
			<?= $aAbstract["UpdateDate"] ?> Update Date
		</p>
		<table class="no_bg">
			<tr>
				<td><input type="radio" name="Abstract" value="Title"></td>
				<td>Title:<br><input type="text" id="Title" name="Title" value="<?= $aAbstract["Title"] ?>"></td>
			</tr>
			<tr>
				<td><input type="radio" name="Abstract" value="SubTitle"></td>
				<td>SubTitle:<br><input type="text" id="SubTitle" name="SubTitle" value="<?= $aAbstract["SubTitle"] ?>"></td>
			</tr>
			<tr>
				<td><input type="radio" name="Abstract" value="Coauthors"></td>
				<td>Coauthors:<br><input type="text" id="Coauthors" name="Coauthors" value="<?= $aAbstract["Coauthors"] ?>"></td>
			</tr>
			<tr>
				<td><input type="radio" name="Abstract" value="Abstract"></td>
				<td>Abstract:<br><textarea id="Abstract" name="Abstract"><?= $aAbstract["Abstract"] ?></textarea></td>
			</tr>
		</table>
	<?php
	} ?>
</section>
<script>
	sxAjaxLoadArchives();
	sxReloadInfoToggle();
</script>