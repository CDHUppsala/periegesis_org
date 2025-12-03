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
	 * Delete Booking
	 */
	$count = 0;
	$iBookingID = (int) $_POST["BookingID"];
	if (intval($iBookingID) > 0) {
		$sql = "DELETE FROM room_bookings
				WHERE BookingID = ? ";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$iBookingID]);
		$count = $stmt->rowCount();

		if ($count > 0) {
			echo "Success";
		} else {
			echo "Error";
		}
	}
}