<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

/**
 * Get the Form Source and open the corresponding file
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	/**
	 * Delete reservation
	 */
	$count = 0;
	$intReservationID = (int) $_POST["ReservationID"];
	if (intval($intReservationID) > 0) {
		$sql = "DELETE FROM rb_reservations
				WHERE ReservationID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$intReservationID]);
		$count = $stmt->rowCount();

		if ($count > 0) {
			echo "Success";
		} else {
			echo "Error";
		}
	}
}