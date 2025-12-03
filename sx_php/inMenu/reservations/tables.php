<?php

/**
 * Variable $date_RequestedDate is defined in sx_config.php
 * It already includes the first of month of the searched date
 * SetupID, OpenTime, CloseTime, TimeInterval, OnlineTimeInterval
 * ReservationID, TableID, Seats, ReservationDate, StartTime, EndTime, CustomerName, Phone, Email, Cancelled, Notes
 */
if (return_Is_Date($date_RequestedDate)) {
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

if (is_array($arrTables)) {
?>
    <section class="calendar_wrapper">
        <table class="calendar_table reservations" id="jq_Reservations" data-date="<?= $sxCurrDate ?>">
            <caption>
                <div class="flex_between">
                    <span></span>
                    <span><?php echo $sxDays[$iThisWeekDay] . ', ' . intval($iThisDay) . ' ' . lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear ?></span>
                    <span><?= $sxCurrDate ?></span>
                </div>
            </caption>
            <tbody>
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
                        if($t > 0) {
                            echo '</tbody>';
                        } ?>
            <tbody>
                <tr>
                    <th colspan="2">
                        <div class="toggle_up jq_TogggleUpDpwn"> <?=$strLocationName?></div>
                    </th>
                    <td colspan="<?=$iColumns?>"></td>
                <tr>
            </tbody>

            <tbody class="tbody_tables">
                <?php
                    }
                $LoopLocationID = $iLocationID;
                $iTableID = $arrTables[$t]['TableID'];
                $strTableName = $arrTables[$t]['TableName'];
                $iSeats = $arrTables[$t]['Seats'];
                
                ?>
                    <tr data-table="<?= $iTableID ?>" data-seats="<?= $iSeats ?>">
                        <th><?= $strTableName ?></th>
                        <th>Seats <?= $iSeats ?></th>
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

<div id="add_reservation" class="modal_reservations">
    <form id="jq_add_reservation" class="modal_content" name="Reservations" action="ajax_reservations.php" method="post">
        <input type="text" name="TableID" value="" />
        <input type="text" name="ReservationDate" value="" />
        <input type="text" name="StartTime" value="" />
        <input type="text" name="EndTime" value="" />
        <input type="text" name="CustomerName" value="" placeholder="Name" />
        <input type="text" name="CustomerPhone" value="" placeholder="Phone" />
        <input type="text" name="Numbers" value="" placeholder="Number" />
        <button class="jq_add">ADD</button>
        <button class="jq_cancel">Cancel</button>
    </form>
</div>

<script>
    $sx(function() {
        var isMouseDown = false;
        var index_tr = -1;
        var index_start = -1;
        var index_end = -1;

        var table_ID = -1;
        var time_start = "";
        var time_end = "";
        var reservation_date = "<?= $sxCurrDate ?>";
        $sx("#jq_Reservations td")
            .mousedown(function() {
                isMouseDown = true;
                index_start = $sx(this).index() - 2 // substract th-elements;
                time_start = $sx(this).attr('data-time');
                index_tr = $sx(this).parent('tr').index();
                table_ID = $sx(this).parent('tr').attr('data-table');
                $sx(this).toggleClass("bg_cell");
                return false; // prevent text selection
            })
            .mouseover(function() {
                if (isMouseDown && index_tr == $sx(this).parent('tr').index()) {
                    $sx(this).toggleClass("bg_cell");
                    index_end = $sx(this).index() - 2 // substract th-elements;
                    time_end = $sx(this).attr('data-time');

                }
            })
            .mouseup(function() {
                var current_TR = $sx("#jq_Reservations tr[data-table=" + table_ID + "]");
                var first_TD = current_TR.find('td').eq(index_start);
                if (first_TD.attr('colspan') == undefined) {
                    if (index_start < index_end) {
                        var cspan = 2;
                        for (c = index_start; c < index_end; c++) {
                            current_TR.find('td').eq(index_start).attr('colspan', cspan);
                            current_TR.find('td').eq(c + 1).addClass('dipslay_none');
                            cspan++;
                        }
                        var form_el = $sx("#jq_add_reservation");
                        form_el.find('input[name=TableID]').val(table_ID);
                        form_el.find('input[name=ReservationDate]').val(reservation_date);
                        form_el.find('input[name=StartTime]').val(time_start);
                        form_el.find('input[name=EndTime]').val(time_end);

                        alert('Save Changes? \n' +
                            table_ID + ' ' + time_start + ' ' + time_end + '\n' +
                            index_tr + ' ' + index_start + ' ' + index_end);

                        var el_DIV = '<div>Perikles Athineos: <button class="jq_CancelReservation">x</button></div>';
                        //first_TD.html(el_DIV);
                        $sx("#add_reservation").fadeIn(300);
                    }
                }
                $sx("#jq_Reservations td").removeClass('bg_cell');
                index_tr = -1;
                index_start = -1;
                index_end = -1;

                isMouseDown = false;
                table_ID = -1;
                time_start = "";
                time_end = "";
            });



        $sx(".jq_TogggleUpDpwn").click(function() {
            $sx(this).toggleClass('toggle_up toggle_down')
                .closest('tbody')
                .next('tbody')
                .fadeToggle(400);
        });
    });


    $sx(document).ready(function() {
        // Get anly the months of a year that contain a text
        $sx(".jqSelectTextYear").change(function() {
            var $Data = "year=" + $sx(this).val();
            $sx.ajax({
                url: "ajax_Texts_SelectMonths.php",
                cache: false,
                data: $Data,
                dataType: "html",
                scriptCharset: "utf-8",
                type: "GET",
                success: function(result) {
                    $sx(".jqSelectMonth").html(result);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });

    });
</script>