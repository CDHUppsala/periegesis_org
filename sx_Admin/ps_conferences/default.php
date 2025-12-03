<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/functionsTableName.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include __DIR__ . "/blueprint_functions.php";

$aResults = null;

$last_ConferenceID = sx_getLastConferenceID();
$last_SessionID = sx_getLastSessionID();
$last_PaperID = sx_getLastPaperID();

$radio_InsertBlueprint = false;

/**
 * Get from Blueprint Table
 */

$radio_Blueprint = true;

if ($radio_Blueprint) {
	$sql = "SELECT
		PaperID,
        ConferenceID,
        StartDate,
        EndDate,
        SessionID,
        SessionTitle,
        SessionDate,
        S_StartTime,
        S_EndTime,
        P_StartTime,
        P_EndTime,
        PaperTitle
	FROM conf_blueprint
	ORDER BY SessionID, SessionDate, S_StartTime, P_StartTime";
	$stmt = $conn->query($sql);
	$rs = $stmt->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults = $rs;
	}
	$stmt = null;
	$rs = null;
}


$str_msg = array();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Confirm"]) && $_POST["Confirm"] == "Yes") {
	if (is_array($aResults)) {
		$int_ConferenceID = $aResults[0][1];

		$str_check = sx_GetAnyFieldValue('conferences', 'Title', 'ConferenceID', $int_ConferenceID);
		if (empty($str_check)) {
			$date_StartDate = $aResults[0][2];
			$date_EndDate = $aResults[0][3];
			$str_Title = "New Conference " . $int_ConferenceID;

			$sql = "INSERT INTO conferences
			(ConferenceID, Hidden, StartDate, EndDate, Title)
		VALUES (?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$int_ConferenceID, 1, $date_StartDate, $date_EndDate, $str_Title]);
			$str_msg[] = "<p>A New Conference has been imported with ID: $int_ConferenceID and Title: $str_Title.</p>";
		} else {
			$str_msg[] = "<p>The Conference ID $int_ConferenceID allready exists in the Table conferences with the name: $str_check. No Conference has been inserted!</p>";
		}


		$r_count = count($aResults);
		$c_count = count($aResults[0]);

		$str_check = sx_GetAnyFieldValue('conf_sessions', 'SessionTitle', 'SessionID', $aResults[0][4]);
		if (empty($str_check)) {
			$loopID = 0;
			for ($r = 0; $r < $r_count; $r++) {
				$SessionID = $aResults[$r][4];
				if ($SessionID != $loopID) {
					$arrValues[] = $SessionID;
					$arrValues[] = $int_ConferenceID;
					$arrValues[] = 1;
					$arrValues[] = $aResults[$r][5];
					$arrValues[] = $aResults[$r][6];
					$arrValues[] = $aResults[$r][7];
					$arrValues[] = $aResults[$r][8];
					if (!empty($arrValues)) {
						$sql = "INSERT INTO conf_sessions
            				(SessionID, ConferenceID, Hidden, SessionTitle, SessionDate, StartTime, EndTime)
			            VALUES (?,?,?,?,?,?,?)";
						$stmt = $conn->prepare($sql);
						$stmt->execute($arrValues);
					}
				}
				$loopID = $SessionID;
				$arrValues = null;
			}
			$str_msg[] = "<p>New Sessions have been imported with the First ID: $SessionID.</p>";
		} else {
			$str_msg[] = "<p>At least one Session ID allready exists in the Table conf_sessions. No Sessions have been inserted!</p>";
		}

		$iFirstID = $aResults[0][0];
		$str_check = sx_GetAnyFieldValue('conf_papers', 'PaperTitle', 'PaperID', $iFirstID);
		if (empty($str_check)) {
			for ($r = 0; $r < $r_count; $r++) {
				$arrValues[] = $int_ConferenceID;
				$arrValues[] = $aResults[$r][4];
				$arrValues[] = 1;
				$arrValues[] = $aResults[$r][6];
				$arrValues[] = $aResults[$r][9];
				$arrValues[] = $aResults[$r][10];
				$arrValues[] = $aResults[$r][11];
				if (is_array($arrValues)) {
					$sql = "INSERT INTO conf_papers
				(ConferenceID, SessionID, Hidden, PresentationDate, StartTime, EndTime, PaperTitle)
				VALUES (?,?,?,?,?,?,?)";
					$stmt = $conn->prepare($sql);
					$stmt->execute($arrValues);
				}
				$arrValues = null;
			}
			$str_msg[] = "<p>New Papers have been imported with the First ID: $iFirstID.</p>";
		} else {
			$str_msg[] = "<p>At least on Paper ID allready exists in the Table conf_papers. No Papers have been inserted!</p>";
		}
	}
}

?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Public Sphere Content Management System</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css">
    <style>
        <?php
        include __DIR__ . "/blueprint.css";
        ?>
    </style>

</head>

<body>
	<header id="header" class="flex_between">
		<h2>Export Blueprint to<br>Conference, Sessions and Papers</h2>
		<form class="flex_center" name="InserBlueprintInDatabase" action="default.php" method="post">
			<label>Confirm Export: 
            <input type="checkbox" name="Confirm" value="Yes" /></label>
			<input class="button" type="submit" name="InsertBlueprint" value="Export Conference Blueprint to Database" />
			<a title="Obs! Save changes before Export" class="button" href="default.php">Reload the Blueprint Table</a>
		</form>
		<div>Last ConferenceID: <?= $last_ConferenceID ?><br>Last SessionID: <?= $last_SessionID ?><br>Last PaperID: <?= $last_PaperID ?></div>
	</header>
	<div class="alignCenter">
		<h1>Please Check if the Blueprint is in congruence with your expectations.</h1>
		<h2>Otherwise go back to the <a href="index.php">Create Blueprint Page</a> to change it</h2>
	</div>
	<?php
	if (!empty($str_msg)) { ?>
		<div class="msgInfo">
			<?= implode(' ', $str_msg); ?>
		</div>
	<?php
	} ?>
	<form style="margin: 0 auto" id="CreateConference" name="CreateConference" action="index.php" method="post">
		<table id="upload" class="no_bg">
			<?php

			$iLast = -1;
			$dLast = null;

			if (is_array($aResults)) {
				/**
				 * NEW DATES Check if they are available
				 */
				$rCount = count($aResults);
				$cCount = count($aResults[0]);
				$radio_ChangeDates = false;
				if (isset($data_Start) && isset($data_Start)) {
					if ($data_Start > $aResults[0][2]) {
						$radio_ChangeDates = true;
					}
				} ?>
				<thead>
					<tr>
						<th>Paper ID</th>
						<th title="The Title of Conference Will be saved as: Conference <?= $last_ConferenceID + 1 ?>">Conference ID</th>
						<th title="Conference Start Date">C Start Date</th>
						<th title="Conference Start Date">C End Date</th>
						<th>Session ID</th>
						<th>Session Title</th>
						<th>Session Date</th>
						<th title="Session Start Time">S Start Time</th>
						<th title="Session End Time">S End Time</th>
						<th title="Paper Start Time - Click to Change">P Start Time</th>
						<th title="Paper End Time - Click to Change">P End Time</th>
						<th>Paper Title</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sClass = "";
					for ($z = 0; $z < $rCount; $z++) {
						$iLoop = $aResults[$z][4]; //SessionID
						$dLoop = $aResults[$z][6]; //SessionDate
						if ($iLoop != $iLast) {
							if ($sClass == "gray") {
								$sClass = "white";
							} else {
								$sClass = "gray";
							}
						} ?>

						<tr class="<?= $sClass ?>"">
					<?php
						for ($c = 0; $c < $cCount; $c++) {
							$val = $aResults[$z][$c];

							$dClass = "";
							if ($c == 6) { // SessionDate
								if ($val != $dLast) {
									if (empty($loopClass)) {
										$loopClass = ' class="yellow"';
									} else {
										$loopClass = "";
									}
								}
								if (empty($loopClass)) {
									$dClass = ' class="red"';
								} else {
									$dClass = $loopClass;
								}
							}

							if ($c == 9 || $c == 10) { //P_StartTime,P_EndTime
								echo '<td class="td_edit_time">' . $val . '</td>';
							} else {
								echo "<td$dClass>$val</td>";
							}
						}
						echo "</tr>";
						$iLast = $iLoop;
						$dLast = $dLoop;
					}
				} ?>
				</tbody>
		</table>
		<?php
		$aResults = null;
		?>
	</form>

	<div id=" Console">
							</div>
</body>

</html>