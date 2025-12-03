<?php

/**
 * Variable $dateSearchByDate is defined in sx_config.php
 * It already includes the first of month of the searched date
 * day, week, month, year
 */

$arrRooms = "";
$sql = "SELECT r.RoomID,
        r.RoomName,
        r.GroupID,
        g.GroupName,
        r.Beds,
        r.BasicPrice
    FROM rooms AS r
        INNER JOIN room_groups AS g
        ON r.GroupID = g.GroupID
    ORDER BY g.Sorting DESC, g.GroupID ASC, r.RoomID ASC";
$arrRooms = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (is_array($arrRooms)) { ?>

    <section class="calendar_bg">
        <table class="calendar_table reservations" id="jq_Reservations">
            <caption>
                <div class="row">
                    <span>Filter by Group and/or Number of Beds</span>
                    <span>Select a Month and move 1-3 weeks forewards by clicking on Week <?= ($iFirstMonthWeek + 1) - $iWeeksForward . ", " . ($iFirstMonthWeek + 2) - $iWeeksForward . ", " . ($iFirstMonthWeek + 3) - $iWeeksForward ?> or above.</span>
                    <span>Click on Days to mark Checkin and Checkout Dates</span>
                </div>
            </caption>

            <tbody class="tbody_month_weeks">
                <tr>
                    <th rowspan="3" colspan="3"></th>
                    <?php
                    $colspanPrevious = $iWeekStartDay - $iFrom;
                    $colspanThis = $iMonthDays;
                    if ($iFrom > 0) {
                        $colspanThis = $iMonthDays - ($iFrom - $iWeekStartDay);
                    }
                    $colspanNext = $iTotalCalendarDays - ($iWeekStartDay + ($iMonthDays - $iFrom));
                    if ($colspanPrevious > 0) { ?>
                        <th class="bg_colspan" colspan="<?= $colspanPrevious ?>"><a href="<?= $strCurrentURL . "?month=" . (int) $iPrevMonth . "&year=" . $iPrevYear ?>"><?= substr(lng_MonthNames[$iPrevMonth - 1], 0, 3) . '<br>' . $iPrevYear ?></a></th>
                    <?php
                    } ?>
                    <th colspan="<?= $colspanThis ?>"><a href="<?= $strCurrentURL . "?month=" . $iThisMonth . "&year=" . $iThisYear ?>"><?= lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear ?></a></th>
                    <?php
                    if ($colspanNext > 0) { ?>
                        <th class="bg_colspan" colspan="<?= $colspanNext ?>"><a href="<?= $strCurrentURL . "?month=" . (int) $iNextMonth . "&year=" . $iNextYear ?>"><?= substr(lng_MonthNames[$iNextMonth - 1], 0, 3) . '<br>' . $iNextYear ?></a></th>
                    <?php
                    } ?>
                </tr>
                <tr>
                    <?php
                    for ($i = $iFrom; $i < ($iTotalCalendarDays); $i++) {
                        if (($i % 7) == 0) {
                            $iWeek = ($iFirstMonthWeek++);
                            if ($iFirstMonthWeek > 52 && $iThisMonth == 1) {
                                $iFirstMonthWeek = 1;
                            }
                            if ($iWeek == 53 && $iThisMonth == 12 && $iWeekStartDayNext < 4) {
                                $iWeek = 1;
                            } ?>
                            <th class="border_cell" colspan="7"><a href="<?= $strCurrentURL . "?week=" . $iWeek . "&month=" . $iThisMonth . "&year=" . $iThisYear ?>">Week <?= $iWeek ?></a></th>
                    <?php
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    for ($i = $iFrom; $i < $iTotalCalendarDays; $i++) {
                        if (($i % 7) == 0) {
                            for ($w = 0; $w < 7; $w++) {
                                $class = "";
                                if ($w == 6) {
                                    $class = 'class="border_cell"';
                                } ?>
                                <th <?= $class ?>><?= mb_substr(lng_DayNames[$w], 0, 1) ?></th>
                    <?php
                            }
                        }
                    }
                    ?>
                </tr>
            </tbody>
            <tbody class="tbody_month_days">
                <tr>
                    <th>Room ID</th>
                    <th>Beds</th>
                    <th>Status</th>
                    <?php
                    for ($i = $iFrom; $i < ($iTotalCalendarDays); $i++) {
                        if ($i < $iWeekStartDay) {
                            $iDay = ($iPrevMonthDays - ($iWeekStartDay - $i) + 1); ?>
                            <td class="notDay"><?= $iDay ?></td>
                            <?php
                        } else {
                            $iDay = ($i - $iWeekStartDay + 1);
                            $iDayZero = $iDay;
                            if(strlen($iDayZero) == 1) {
                                $iDayZero = "0".$iDayZero;
                            }
                            $LoopDate = date($iThisYear . '-' . $iThisMonthZero . '-' . $iDayZero);
                            $LinkLeft = "";
                            $LinkRight = "";
                            $Class = "";
                            if ($LoopDate == $date_RequestedDate) {
                                $Class = 'class="selected_day" ';
                            } elseif ($LoopDate == date("Y-m-d")) {
                                $Class = 'class="thisDay" ';
                            }
                            if ($iDay > $iMonthDays) {
                                $iDay -= $iMonthDays;
                                $Class = 'class="notDay" ';
                                $iDayZero = $iDay;
                                if(strlen($iDayZero) == 1) {
                                    $iDayZero = "0".$iDayZero;
                                } ?>
                                <td <?= $Class ?>data-select_date="<?= date($iNextYear . "-" . $iNextMonth . "-" . $iDayZero) ?>"><a href="javascript:void(0)" class="jq_SelectDate"><?= $iDay ?></td>
                            <?php
                            } else { ?>
                                <td <?= $Class ?>data-select_date="<?= date($iThisYear . "-" . $iThisMonthZero . "-" . $iDayZero) ?>"><a href="javascript:void(0)" class="jq_SelectDate"><?= $iDay ?></td>
                    <?php
                            }
                        }
                    } ?>
                </tr>
            </tbody>
            <?php
            $iRooms = count($arrRooms);
            $LoopGroupID = 0;

            for ($r = 0; $r < $iRooms; $r++) {
                $iGroupID = $arrRooms[$r]['GroupID'];
                if ($LoopGroupID != $iGroupID) {
                    $strGroupName = $arrRooms[$r]['GroupName'];
                    if ($r > 0) {
                        echo '</tbody>';
                    } ?>
                    <tbody class="tbody_filter">
                        <tr>
                            <th colspan="3">
                                <div class="row">
                                    <a title="Hide/Show this Group" class="toggle_up jq_TogggleUpDpwn" href="javascript:void(0)"><?= $strGroupName ?></a>
                                    <a title="Filter by THIS or ALL Groups" class="jq_FilterLocation" href="javascript:void(0)">THIS</a>
                                </div>
                            </th>
                            <td colspan="<?= $iTotalCalendarDays ?>"></td>
                        <tr>
                    </tbody>
                    <tbody class="tbody_rooms">
                    <?php
                }
                $LoopGroupID = $iGroupID;
                $iRoomID = $arrRooms[$r]['RoomID'];
                $strRoomName = $arrRooms[$r]['RoomName'];
                $iBeds = $arrRooms[$r]['Beds'];
                $iBasicPrice = $arrRooms[$r]['BasicPrice'];

                    ?>
                    <tr data-roomid="<?= $iRoomID ?>" data-price="<?= $iBasicPrice ?>" data-beds="<?= $iBeds ?>">
                        <th>Room <?= $iRoomID ?></th>
                        <th><a title="Filter by THIS or ALL Number of Beds" class="jq_FilterBeds" href="javascript:void(0)">Beds <?= $iBeds ?></a></th>
                        <th><?= $strRoomName ?></th>

                        <?php
                        for ($i = $iFrom; $i < ($iTotalCalendarDays); $i++) {
                            $iLoopMonth = $iThisMonthZero;
                            if ($i < $iWeekStartDay) {
                                $iLoopMonth = $iPrevMonth;
                                $iDay = ($iPrevMonthDays - ($iWeekStartDay - $i) + 1);
                                $LoopDate = date($iThisYear . '-' . $iLoopMonth . '-' . $iDay);
                        ?>
                                <td data-date="<?= $LoopDate ?>" title="<?= $iDay ?>" class="notDay"></td>
                            <?php
                            } else {
                                $Class = "";
                                $iDay = ($i - $iWeekStartDay + 1);
                                if ($iDay > $iMonthDays) {
                                    $iDay -= $iMonthDays;
                                    $iLoopMonth = $iNextMonth;
                                    $Class = ' class="notDay"';
                                }
                                $iDayZero = $iDay;
                                if (strlen($iDayZero) == 1) {
                                    $iDayZero = "0" . $iDayZero;
                                }

                                $LoopDate = date($iThisYear . '-' . $iLoopMonth . '-' . $iDayZero);

                                if ($LoopDate == $date_RequestedDate) {
                                    $Class = ' class="selected_day"';
                                } elseif ($LoopDate == date("Y-m-d")) {
                                    $Class = ' class="thisDay"';
                                }  ?>
                                <td data-date="<?= $LoopDate ?>" title="<?= $iDay ?>" <?= $Class ?>></td>
                        <?php
                            }
                        } ?>

                    </tr>
                <?php
            } ?>
                    </tbody>

        </table>

    </section>
<?php
    $arrRooms = null;
} ?>