<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

/**
 * Get the Form Source and open the corresponding file
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$iBookingID = (int) $_POST["BookingID"];
	$iRoomID = (int) $_POST["RoomID"];
	$iCustomerID = $_POST["CustomerID"];
	$strCheckinDate = $_POST["CheckinDate"];
	$strCheckoutDate = $_POST["CheckoutDate"];
	$iPrice = $_POST['Price'];

	$iAdminID = 0;
	if (!empty($_POST["AdminID"])) {
		$iAdminID = (int) $_POST["AdminID"];
	}
	$iCustomerID = 0;
	if (!empty($_POST["CustomerID"])) {
		$iCustomerID = (int) $_POST["CustomerID"];
	}
	$iNewPrice = 0;

	if (!empty($_POST['NewPrice'])) {
		$iNewPrice = $_POST['NewPrice'];
	}

	$iPaid = 0;
	if (!empty($_POST['Paid'])) {
		$iPaid = $_POST['Paid'];
	}

	$strName = "";
	if (!empty($_POST["CustomerName"])) {
		$strName = $_POST["CustomerName"];
	}
	$strPhone = "";
	if (!empty($_POST["CustomerPhone"])) {
		$strPhone = $_POST["CustomerPhone"];
	}
	$strPersons = 0;
	if (!empty($_POST["Persons"])) {
		$strPersons = $_POST["Persons"];
	}
	$strEmail = "";
	if (!empty($_POST["CustomerEmail"])) {
		$strEmail = $_POST["CustomerEmail"];
	}

	/**
	 * Check if reservation period is free
	 */
	if (intval($iBookingID) > 0 && intval($iRoomID) > 0) {
		$sql = "UPDATE room_bookings SET
			AdminID = ?,
			CustomerID = ?,
			RoomID = ?,
			CheckinDate = ?,
			CheckoutDate = ?,
			Persons = ?,
			Price = ?,
			NewPrice = ?,
			Paid = ?,
			Notes = ?
		WHERE BookingID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([
			$iAdminID, $iCustomerID, $iRoomID, $strCheckinDate, $strCheckoutDate, $strPersons, $iPrice, $iNewPrice, $iPaid, $strName . '; ' . $strPhone . '; ' . $strEmail, $iBookingID
		]);
		$stmt = null;

		echo '<div data-bookingid="' . $iBookingID . '"
		data-customerid="' . $iCustomerID . '"
		data-adminid="' . $iAdminID . '"
		data-roomID="' . $iRoomID . '"
		data-checkin="' . $strCheckinDate . '"
		data-checkout="' . $strCheckoutDate . '"
		data-persons="' . $strPersons . '"
		data-price="' . $iPrice . '"
		data-new_price="' . $iNewPrice . '"
		data-paid="' . $iPaid . '"
		data-name="' . $strName . '"
		data-phone="' . $strPhone . '"
		data-email="' . $strEmail . '">' . $strPersons . ' ' . $strName . ' ' . $strPhone . '
		<br><strong>Dates: </strong>' . $strCheckinDate .' | '. $strCheckoutDate .'
		<br><strong>Price: </strong>' . $iPrice .'/'. $iNewPrice .'
		<strong>Paid:</strong> ' . $iPaid . '%
		<button title="Change or Delete" class="jq_EditReservation">i</button></div>';
	}
}
