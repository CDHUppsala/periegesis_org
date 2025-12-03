<?php

/**
 * Variable $date_RequestedDate is defined in sx_config.php
 * It already includes the first of month of the searched date
 * SetupID, OpenTime, CloseTime, TimeInterval, OnlineTimeInterval
 * ReservationID, TableID, Seats, ReservationDate, StartTime, EndTime, CustomerName, Phone, Email, Cancelled, Notes
 */
if (isset($date_RequestedDate) && sx_isDate($date_RequestedDate)) {
    $sxCurrDate = $date_RequestedDate;
} else {
    $sxCurrDate = date("Y-m-d");
}

$date_time = new DateTime($sxCurrDate);
$iThisYear = $date_time->format("Y");
$iThisMonth = $date_time->format("n");
$iThisDay = $date_time->format("d");
$iThisWeekDay = $date_time->format("w");


$sql = "SELECT OpenTime, CloseTime, TimeInterval
    FROM rb_reservations_setup";
$rs = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
$timeOpen = (int) $rs['OpenTime'];
$timeClose = (int) $rs['CloseTime'];
$timeInterval = $rs['TimeInterval'];
$iColSpan = intval(60 / $timeInterval);
$iColumns = ($timeClose - $timeOpen) * $iColSpan;
$iMaxInterval = ($iColSpan - 1) * $timeInterval;
$rs = null;

$arrTables = "";
$sql = "SELECT t.TableID,
        t.TableName,
        t.LocationID,
        l.LocationName,
        t.Seats
    FROM rb_tables AS t
        INNER JOIN rb_locations AS l
        ON t.LocationID = l.LocationID
    ORDER BY l.Sorting DESC, t.Seats ASC";
$arrTables = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (is_array($arrTables)) { ?>

    <section class="calendar_bg">

        <table class="calendar_table reservations" id="jq_Reservations" data-date="<?= $sxCurrDate ?>" data-colspan="<?= $iColSpan ?>">
            <caption>
                <div class="row">
                    <span>Filter by Location and/or Number of Seats</span>
                    <span><?php echo $sxDays[$iThisWeekDay] . ', ' . intval($iThisDay) . ' ' . lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear ?></span>
                    <span></span>
                    <span><?= $sxCurrDate ?></span>
                </div>
            </caption>
            <tbody class="tbody_hours">
                <tr>
                    <th></th>
                    <th></th>
                    <?php
                    for ($i = $timeOpen; $i < $timeClose; $i++) { ?>
                        <th colspan="<?= $iColSpan ?>"><?= $i . ':00' ?></th>
                    <?php
                    } ?>
                </tr>
                <tr>
                    <th>Tables ID</th>
                    <th>Seats NR</th>
                    <?php
                    $z = 0;
                    for ($i = 0; $i < $iColumns; $i++) {
                        $suffix = "";
                        if ($z == 0) {
                            $suffix = 0;
                        } ?>
                        <th><?= $z . $suffix ?></th>
                    <?php
                        $z += $timeInterval;
                        if ($z > $iMaxInterval) {
                            $z = 0;
                        }
                    } ?>
                </tr>
            </tbody>
            <?php
            $iTables = count($arrTables);
            $LoopLocationID = 0;

            for ($t = 0; $t < $iTables; $t++) {
                $iLocationID = $arrTables[$t]['LocationID'];
                if ($LoopLocationID != $iLocationID) {
                    $strLocationName = $arrTables[$t]['LocationName'];
                    if ($t > 0) {
                        echo '</tbody>';
                    } ?>
                    <tbody class="tbody_filter">
                        <tr>
                            <th colspan="2">
                                <div class="row">
                                    <a title="Hide/Show this location" class="toggle_up jq_TogggleUpDpwn" href="javascript:void(0)"><?= $strLocationName ?></a>
                                    <a title="Filter by THIS or ALL locations" class="jq_FilterLocation" href="javascript:void(0)">THIS</a>
                                </div>
                            </th>
                            <td class="colspan_width" colspan="<?= $iColumns ?>"></td>
                        <tr>
                    </tbody>

                    <tbody class="tbody_items">
                    <?php
                }
                $LoopLocationID = $iLocationID;
                $iTableID = $arrTables[$t]['TableID'];
                $strTableName = $arrTables[$t]['TableName'];
                $iSeats = $arrTables[$t]['Seats'];

                    ?>
                    <tr data-table="<?= $iTableID ?>" data-seats="<?= $iSeats ?>">
                        <th><?= $strTableName ?></th>
                        <th><a title="Filter by THIS or ALL Number of Seats" class="jq_FilterSeats" href="javascript:void(0)">Seats <?= $iSeats ?></a></th>
                        <?php
                        $z = 0;
                        $time = $timeOpen;
                        for ($i = 0; $i < $iColumns; $i++) {
                            $suffix = "";
                            if ($z == 0) {
                                $suffix = 0;
                            } ?>
                            <td data-time="<?= $time . ':' . $z . $suffix ?>"></td>
                        <?php
                            $z += $timeInterval;
                            if ($z > $iMaxInterval) {
                                $z = 0;
                                $time++;
                            }
                        } ?>
                    </tr>
                <?php
            } ?>
                    </tbody>
        </table>

    </section>
<?php
    $arrTables = null;
} ?>

<div class="modal_reservations">
    <form id="jq_ReservationForm" class="modal_content" name="ReservationForm" action="" method="post">
        <input type="text" title="Table ID" name="TableID" value="" readonly />
        <input type="text" title="Reservation Date" name="ReservationDate" value="" readonly />
        <input type="text" title="Start Time" name="StartTime" value="" readonly />
        <input type="text" title="End Time" name="EndTime" value="" readonly />
        <input type="text" title="Customer Name" name="CustomerName" value="" placeholder="Name" />
        <input type="text" title="Customer Phone" name="CustomerPhone" value="" placeholder="Phone" />
        <input type="text" title="Reserved Seeats Number" name="SeeatsNumber" value="" placeholder="Reserved Seats Number" />
        <input id="jq_add" name="Submit" type="submit" value="Add Reservation">
        <input id="jq_cancel" name="Reset" type="reset" value="Cancel">
    </form>
</div>
<div class="modal_reservations">
    <form id="jq_ReservationFormEdit" class="modal_content" name="ReservationFormEdit" method="post">
        <input type="text" title="Reservation ID" name="ReservationID" value="" readonly />
        <input type="text" title="Table ID" name="TableID" value="" readonly />
        <input type="text" title="Reservation Date" name="ReservationDate" value="" readonly />
        <input type="text" title="Start Time" name="StartTime" value="" readonly />
        <input type="text" title="End Time" name="EndTime" value="" readonly />
        <input type="text" title="Customer Name" name="CustomerName" value="" placeholder="Name" />
        <input type="text" title="Customer Phone" name="CustomerPhone" value="" placeholder="Phone" />
        <input type="text" title="Reserved Seeats Number" name="SeeatsNumber" value="" placeholder="Reserved Seats Number" />
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
$sql = "SELECT ReservationID,
        TableID,
        Seats,
        ReservationDate,
        StartTime,
        EndTime,
        CustomerName,
        Phone
    FROM rb_reservations
    WHERE ReservationDate = ?
        AND Cancelled = 0";
$stmt = $conn->prepare($sql);
$stmt->execute([$sxCurrDate]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (is_array($results)) {
    $json = json_encode($results, JSON_UNESCAPED_UNICODE);
?>
    <script>
        dataArray = <?= $json ?>;
        for (i = 0; i < dataArray.length; i++) {
            int_ReservationID = dataArray[i].ReservationID;
            int_TableID = dataArray[i].TableID;
            str_Seats = dataArray[i].Seats;
            str_ReservationDate = dataArray[i].ReservationDate;
            str_StartTime = dataArray[i].StartTime;
            str_EndTime = dataArray[i].EndTime;
            str_CustomerName = dataArray[i].CustomerName;
            str_Phone = dataArray[i].Phone;

            div_el = '<div data-resid="' + int_ReservationID + '" ' +
                'data-tableid="' + int_TableID + '" ' +
                'data-date="' + str_ReservationDate + '" ' +
                'data-start="' + str_StartTime + '" ' +
                'data-end="' + str_EndTime + '" ' +
                'data-name="' + str_CustomerName + '" ' +
                'data-phone="' + str_Phone + '" ' +
                'data-seats="' + str_Seats + '">' + str_Seats + ' ' + str_CustomerName + ' ' +
                '<button title="Change or Delete" class="jq_EditReservation">x</button></div>';

            loop_TR = $("#jq_Reservations tr[data-table='" + int_TableID + "']");
            td_start = loop_TR.find("td[data-time='" + str_StartTime + "']").index() - 2;
            td_end = loop_TR.find("td[data-time='" + str_EndTime + "']").index() - 2;
            var cspan = 2;
            for (c = td_start; c < td_end; c++) {
                loop_TR.find('td').eq(td_start).attr('colspan', cspan);
                loop_TR.find('td').eq(c + 1).addClass('dipslay_none');
                cspan++;
            }
            loop_TR.find('td').eq(td_start).html(div_el);
        }
    </script>
<?php
}
$stmt = null;
$results = null;
?>