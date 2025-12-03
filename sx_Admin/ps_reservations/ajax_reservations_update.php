<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

/**
 * Get the Form Source and open the corresponding file
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$intReservationID = (int) $_POST["ReservationID"];
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
	if (intval($intReservationID) > 0 && intval($intTableID) > 0) {
		$sql = "UPDATE rb_reservations SET
			Seats = ?,
			CustomerName = ?,
			Phone = ?
		WHERE ReservationID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$strSeeats, $strName, $strPhone, $intReservationID]);
		$stmt = null;

		echo '<div data-resid="' . $intReservationID . '"
		data-tableid="' . $intTableID . '"
		data-date="' . $ReservationDate . '"
		data-start="' . $strStartTime . '"
		data-end="' . $strEndTime . '"
		data-name="' . $strName . '"
		data-phone="' . $strPhone . '"
		data-seats="' . $strSeeats . '">'
		 . $strSeeats . ' ' . $strName . ' ' . $strPhone . '
		<button title="Change or Delete" class="jq_EditReservation">x</button></div>';
	}
}
