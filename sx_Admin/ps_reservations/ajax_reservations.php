<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

/**
 * Get the Form Source and open the corresponding file
 */

$intTableID = (int) $_POST["TableID"];
$ReservationDate = $_POST["ReservationDate"];
$strStartTime = $_POST["StartTime"];
$strEndTime = $_POST["EndTime"];

$strName = "";
if (!empty($_POST["CustomerName"])) {
	$strName = $_POST["CustomerName"];
}
$strPhone = "";
if (!empty($_POST["CustomerPhone"])) {
	$strPhone = $_POST["CustomerPhone"];
}
$strSeeats = 0;
if (!empty($_POST["SeeatsNumber"])) {
	$strSeeats = $_POST["SeeatsNumber"];
}

/**
 * Check if reservation period is free
 */
$radioAdd = false;
if (intval($intTableID) > 0) {

	$sql = "SELECT ReservationID
	FROM rb_reservations
	WHERE TableID = ? AND ReservationDate = ? 
	AND ((StartTime BETWEEN ? AND ?) OR (EndTime BETWEEN ? AND ?))";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intTableID, $ReservationDate, $strStartTime, $strEndTime, $strStartTime, $strEndTime]);

	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!is_array($rs)) {
		$radioAdd = true;
	} else {
		echo "Reserved";
		exit;
	}
}

if ($radioAdd) {
	$sql = "INSERT INTO rb_reservations
	(TableID, Seats, ReservationDate, StartTime, EndTime, CustomerName, Phone)
	VALUES (?,?,?,?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intTableID, $strSeeats, $ReservationDate, $strStartTime, $strEndTime, $strName, $strPhone]);
	$iReservationID = $conn->lastInsertId();
	$stmt = null;

	echo '<div data-resid="' . $iReservationID . '"
	data-tableid="' . $intTableID . '"
	data-date="' . $ReservationDate . '"
	data-start="' . $strStartTime . '"
	data-end="' . $strEndTime . '"
	data-name="' . $strName . '"
	data-phone="' . $strPhone . '"
	data-seats="' . $strSeeats . '">' . $strSeeats . ' ' . $strName . '
	<button title="Change or Delete" class="jq_EditReservation">x</button></div>';
}
