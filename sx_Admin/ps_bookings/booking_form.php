<div class="modal_reservations">
    <form id="jq_ReservationForm" class="modal_content" name="ReservationForm" action="" method="post">
        <fieldset>
            <label>Customer ID<br>
                <input type="text" name="CustomerID" value="" readonly />
            </label>
            <label>Room ID<br>
                <input type="text" name="RoomID" value="" readonly />
            </label>
        </fieldset>
        <fieldset>
            <label>Checkin Date<br>
                <input type="text" name="CheckinDate" value="" readonly />
            </label>
            <label>Last Night Date<br>
                <input type="text" name="CheckoutDate" value="" readonly />
            </label>
            <label>Price<br>
                <input type="text" title="Price" name="Price" value="" readonly />
            </label>
            <label>New Price<br>
                <input type="text" name="NewPrice" value="" placeholder="New Price" />
            </label>
            <label>Paid<br>
                <select name="Paid">
                    <option value="0">0%</option>
                    <option value="50">50%</option>
                    <option value="100">100%</option>
                </select>
            </label>
            <label>Persons<br>
                <input type="text" name="Persons" value="" placeholder="Persons" />
            </label>
            <label>Name<br>
                <input type="text" name="CustomerName" value="" />
            </label>
            <label>Phone<br>
                <input type="text" name="CustomerPhone" value="" placeholder="Phone" />
            </label>
        </fieldset>
        <fieldset>
            <label style="width: 100%">Email<br>
                <input style="width: 100%" type="text" name="CustomerEmail" value="" placeholder="Email" />
            </label>
        </fieldset>
        <fieldset>
            <input id="jq_add" name="Submit" type="submit" value="Add Reservation">
            <input id="jq_cancel" name="Reset" type="reset" value="Cancel">
        </fieldset>
    </form>
</div>
<div class="modal_reservations">
    <form id="jq_ReservationFormEdit" class="modal_content" name="ReservationFormEdit" method="post">
        <fieldset>
            <label>Booking ID<br>
                <input type="text" name="BookingID" value="" readonly />
            </label>
            <label>Admin ID<br>
                <input type="text" name="AdminID" value="" readonly />
            </label>
            <label>Customer ID<br>
                <input type="text" name="CustomerID" value="" readonly />
            </label>
            <label>Room ID<br>
                <input type="text" name="RoomID" value="" readonly />
            </label>
        </fieldset>
        <fieldset>
            <label>Checkin Date<br>
                <input type="text" name="CheckinDate" value="" />
            </label>
            <label>Last Night Date<br>
                <input type="text" name="CheckoutDate" value="" />
            </label>
            <label>Price<br>
                <input type="text" name="Price" value="" readonly />
            </label>
            <label>New Price<br>
                <input type="text" name="NewPrice" value="" />
            </label>
            <label>Paid<br>
                <select name="Paid">
                    <option value="0">0%</option>
                    <option value="50">50%</option>
                    <option value="100">100%</option>
                </select>
            </label>
            <label>Persons<br>
                <input type="text" name="Persons" value="" />
            </label>
            <label>Name<br>
                <input type="text" name="CustomerName" value="" />
            </label>
            <label>Phone<br>
                <input type="text" name="CustomerPhone" value="" />
            </label>
        </fieldset>
        <fieldset>
            <label>Email<br>
                <input style="width: 100%" type="text" name="CustomerEmail" value="" />
            </label>
        </fieldset>
        <fieldset>
            <input id="jq_update" name="Submit" type="submit" value="Update">
            <input id="jq_delete" name="Submit" type="submit" value="Delete">
            <input id="jq_close" name="Reset" type="reset" value="Close">
        </fieldset>
    </form>
</div>

<?php
/**
 * Add reservation to the table
 */

$sql = "SELECT BookingID,
    AdminID,
    CustomerID,
    RoomID,
    CheckinDate,
    CheckoutDate,
    Persons,
    Price,
    NewPrice,
    Paid,
    Notes
    FROM room_bookings
    WHERE ((CheckinDate BETWEEN ? AND ?) OR (CheckoutDate BETWEEN ? AND ?)) ";
$stmt = $conn->prepare($sql);
$stmt->execute([$dFirstRequestDay, $dLastRequestDay, $dFirstRequestDay, $dLastRequestDay]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (is_array($results)) {
    $json = json_encode($results, JSON_UNESCAPED_UNICODE);
?>
    <script>
        const dataArray = <?= $json ?>;
        length = dataArray.length;

        for (i = 0; i < length; i++) {
            iBookingID = dataArray[i].BookingID;
            iAdminID = dataArray[i].AdminID;
            iCustomerID = dataArray[i].CustomerID;
            iRoomID = dataArray[i].RoomID;
            strCheckinDate = dataArray[i].CheckinDate;
            strCheckoutDate = dataArray[i].CheckoutDate;
            strPersons = dataArray[i].Persons;
            iPrice = dataArray[i].Price;
            iNewPrice = dataArray[i].NewPrice;
            iPaid = dataArray[i].Paid;

            strNotes = dataArray[i].Notes;
            arrNotes = strNotes.split(';')
            iLength = arrNotes.length;
            strName = arrNotes[0];
            if (strName != undefined && strName.length > 0) {
                strName.trim();
            }
            strPhone = arrNotes[1];
            if (strPhone != undefined && strPhone.length > 0) {
                strPhone.trim();
            }
            strEmail = arrNotes[2];
            if (strEmail != undefined && strEmail.length > 0) {
                strEmail.trim();
            }

            div_el = '<div data-bookingid="' + iBookingID + '" ' +
                'data-adminid="' + iAdminID + '" ' +
                'data-customerid="' + iCustomerID + '" ' +
                'data-roomid="' + iRoomID + '" ' +
                'data-checkin="' + strCheckinDate + '" ' +
                'data-checkout="' + strCheckoutDate + '" ' +
                'data-persons="' + strPersons + '" ' +
                'data-price="' + iPrice + '" ' +
                'data-new_price="' + iNewPrice + '" ' +
                'data-paid="' + iPaid + '" ' +

                'data-name="' + strName + '" ' +
                'data-phone="' + strPhone + '" ' +
                'data-email="' + strEmail + '">' + strPersons + ' ' + strName + ' ' + strPhone +
                '<br><strong>Dates:</strong> ' + strCheckinDate + ' | ' + strCheckoutDate +
                '<br><strong>Price:</strong> ' + iPrice + '/' + iNewPrice +
                ' <strong>Paid:</strong> ' + iPaid + '%' +
                '<button title="Change or Delete" class="jq_EditReservation">i</button></div>';
            loop_TR = $("#jq_Reservations tr[data-roomid='" + iRoomID + "']");
            td_last = loop_TR.find("td:last-child").index() - 3;
            td_start = loop_TR.find("td[data-date='" + strCheckinDate + "']").index() - 3;
            td_end = loop_TR.find("td[data-date='" + strCheckoutDate + "']").index() - 3;
            var cspan = 2;
            //alert(iRoomID +' -- '+td_start +'/'+ strCheckinDate + ' ' + td_end +'/'+ strCheckoutDate);
            if (td_start > -1 || td_end > -1) {
                bgClass = "";
                if (td_start < 0) {
                    bgClass = "mark_booking";
                    td_start = 0;
                }
                if (td_end < 0) {
                    bgClass = "mark_booking";
                    td_end = td_last;
                }
                for (c = td_start; c < td_end; c++) {
                    loop_TR.find('td').eq(td_start).attr('colspan', cspan);
                    loop_TR.find('td').eq(c + 1).addClass('dipslay_none');
                    cspan++;
                }
                loop_TR.find('td').eq(td_start).html(div_el);
                if (bgClass.length) {
                    //loop_TR.find('td').eq(td_start).find('div').addClass('mark_booking');
                    loop_TR.find('td').eq(td_start).addClass('mark_booking');
                }
            }
        }
    </script>
<?php
}
$stmt = null;
$results = null;
?>