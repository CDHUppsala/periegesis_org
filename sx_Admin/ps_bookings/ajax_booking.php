<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

/**
 * Get the Form Source and open the corresponding file
 */

$iRoomID = (int) $_POST["RoomID"];
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
$radioAdd = false;
if (intval($iRoomID) > 0) {

	$sql = "SELECT BookingID
	FROM room_bookings
	WHERE RoomID = ?
	AND ((CheckinDate BETWEEN ? AND ?) OR (CheckoutDate BETWEEN ? AND ?))";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$iRoomID, $strCheckinDate, $strCheckoutDate, $strCheckinDate, $strCheckoutDate]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if (is_array($rs) && count($rs) > 0) {
		echo "Reserved";
		exit;
	} else {
		$radioAdd = true;
	}
}

if ($radioAdd) {
	$sql = "INSERT INTO room_bookings
	(AdminID, CustomerID, RoomID, CheckinDate, CheckoutDate, Persons, Price, NewPrice, Paid, Notes)
	VALUES (1,0,?,?,?,?,?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$iRoomID, $strCheckinDate, $strCheckoutDate, $strPersons, $iPrice, $iNewPrice, $iPaid, $strName . '; ' . $strPhone . '; ' . $strEmail]);
	$iBookingID = $conn->lastInsertId();
	$stmt = null;

	echo '<div data-bookingid="' . $iBookingID . '"
	data-adminid="1"
	data-customerid="' . $iCustomerID . '"
	data-roomID="' . $iRoomID . '"
	data-checkin="' . $strCheckinDate . '"
	data-checkout="' . $strCheckoutDate . '"
	data-persons="' . $strPersons . '"
	data-price="' . $iPrice . '"
	data-new_price="' . $iNewPrice . '"
	data-paid="' . $iPaid . '"
	data-name="' . $strName . '"
	data-phone="' . $strPhone . '"
	data-email="' . $strEmail . '">' . $strPersons . ' ' . $strName .'<br>' . $strPhone .  '
	<br><strong>Dates</strong>' . $strCheckinDate .' | '.$strCheckoutDate. '
	<br><strong>Price: </strong>' . $iPrice .'/'. $iNewPrice .'
	<strong>Paid:</strong> ' . $iPaid . '%
	<button title="Change or Delete" class="jq_EditReservation">i</button></div>';
}
