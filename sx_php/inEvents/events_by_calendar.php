<?php
$arrResults = null;
if ($radioCalendar) {
    $arrResults = sx_getEventByDatePeriod($date_FirstMonthDate, $date_LastMonthDate);
}
/*
echo '<pre>';
print_r($arrResults);
echo '</pre>';
*/

/**
 * Big calendar can only appear in the Events Page
 * Small Calendar can be shown in First page and in Events Page, 
 * if Big Calendar is Not shown
 *      $radio_showSmallCalendar = true
 *      $radio_showBigCalendar = false
 * - This is defined by lags in the includes_page_header.php (and include_first_page.php)
 * However, not if this page opens by ajax, which happens only with small calendar,
 *      the includes_page_header.php is not included, so redifine $radio_showSmallCalendar if not sett
 */

if(!isset($radio_showBigCalendar)) {
    $radio_showSmallCalendar = true;
    $radio_showBigCalendar = false;
}
 
/**
 * With Big Calendar Remove jQuery functions for DIV in small calendar
 */
$class_UniversalAjax = ' jqUniversalAjax';
$class_PopupCalendarEvents = " jqPopupCalendarEvents";
$class_popup = ' class="popup"';
$popup_close = '<p><span>Clcik on a date to Fix this popup!</span> <button class="popup_close">X</button> </p>';
if ($radio_UseEventsByBigCalendar && $radio_showBigCalendar) {
    $class_UniversalAjax = '';
    $class_PopupCalendarEvents = "";
    $class_popup = "";
    $popup_close = '';
}

?>
<div class="events_calendar_block">
    <div class="print float_right">
        <?php
        getTextPrinter("sx_PrintPage.php?print=events&pg=calendar&date=" . $date_FirstMonthDate, "calendar");
        ?>
    </div>
    <h2 class="head"><span><?= $str_EventsByCalendarTitle ?></span></h2>
    <table class="calendar_table<?= $class_PopupCalendarEvents ?>">
        <tr class="month_nav<?php echo $class_UniversalAjax ?>">
            <td><a data-query="load=calendar&date=<?= return_Add_To_Date($date_FirstMonthDate, -1, 'month') ?>" data-id="jqLoadCalendar" data-url="ajax_Events.php" title="<?= lngPreviousMonth ?>" href="events.php?load=calendar&date=<?= return_Add_To_Date($date_FirstMonthDate, -1, 'month') ?>">&#10094;&#10094;&#10094;</a></td>
            <td colspan="5"><span><?= lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></span></td>
            <td><a data-query="load=calendar&date=<?= return_Add_To_Date($date_LastMonthDate, 1, 'day') ?>" data-id="jqLoadCalendar" data-url="ajax_Events.php" title="<?= lngNextMonth ?>" href="events.php?load=calendar&date=<?= return_Add_To_Date($date_LastMonthDate, 1, 'day') ?>">&#10095;&#10095;&#10095;</a></td>
        </tr>
        <tr>
            <?php
            for ($i = 0; $i < 7; $i++) {
                $sTemp = $sxEuDays[$i];
                $strTemp = "auto";
                if ($i == 0 || $i == 6) {
                    $strTemp = "15%";
                } else {
                    $strTemp = "14%";
                } ?>
                <th style="width:<?= $strTemp ?>"><?= mb_substr($sTemp, 0, 2) ?></th>
            <?php
            } ?>
        </tr>
        <tr>
            <?php
            /**
             * To start new week efter iLoopWeekDay = 7 
             * and to add the last week days after the end of month
             */
            $iLoopWeekDay = $date_MonthsFirstWeekDay;
            $iTemp = 0;
            for ($iCurrent = 1; $iCurrent < $date_MonthsFirstWeekDay; $iCurrent++) {
                $iTemp = return_Month_Day_01(return_Add_To_Date($date_FirstMonthDate, - ($date_MonthsFirstWeekDay - $iCurrent))); ?>
                <td class="notDay"><span><?= $iTemp ?></span></td>
                <?php
            }

            for ($iCurrent = 1; $iCurrent < ($int_MonthDays + 1); $iCurrent++) {
                $sCurrent = strval($iCurrent);
                if (strlen($sCurrent) == 1) {
                    $sCurrent = "0" . $sCurrent;
                }
                $loopDate = date(return_Year($dDate) . "-" . return_Month_01($dDate) . "-" . $sCurrent);
                $aResults = null;
                if ($arrResults) {
                    // Filter the array to get rows where Loop Date is equal to or between start date and end date
                    $aResults = array_filter($arrResults, function ($event) use ($loopDate) {
                        $startDate = $event[2];
                        $endDate = $event[3] ?? $loopDate;
                        return $loopDate >= $startDate && $loopDate <= $endDate;
                    });

                    // Sort the filtered array in descending order by the start date
                    usort($aResults, function ($a, $b) {
                        return strcmp($b[2], $a[2]); // Compare start dates (array[2]) in reverse order
                    });
                }
                $strClass = '';
                if ($loopDate == date("Y-m-d")) {
                    $strClass = ' class="thisDay"';
                }
                echo "<td{$strClass}>";
                $radioTemp = false;
                if (is_array($aResults)) {
                    $iRows = count($aResults);

                    for ($r = 0; $r < $iRows; $r++) {
                        $dEventStartDate = $aResults[$r][2];
                        if (empty($dEventStartDate) || !return_Is_Date($dEventStartDate)) {
                            continue;
                        }
                        if ($loopDate < $dEventStartDate) {
                            break;
                        }
                        $dEventEndDate = $aResults[$r][3];
                        $radioEndDate = return_Is_Date($dEventEndDate);
                        $strFirstDay = "";
                        if ($radioEndDate == false) {
                            $dEventEndDate = $dEventStartDate;
                        }
                        if ($loopDate == $dEventStartDate || ($loopDate > $dEventStartDate && $loopDate <= $dEventEndDate)) {
                            $iEventID = $aResults[$r][0];
                            $radioRegisterToParticipate = $aResults[$r][1];
                            $sStartTime = $aResults[$r][4];
                            $sEndTime = $aResults[$r][5];
                            $sPlaceName = $aResults[$r][6];
                            $sPlaceAddress = $aResults[$r][7];
                            $sPlacePostalCode = $aResults[$r][8];
                            $sPlaceCity = $aResults[$r][9];
                            $sContactPhone = $aResults[$r][10];
                            $sOrganizers = $aResults[$r][11];
                            $sEventTitle = $aResults[$r][12];
                            $strParticipationMode = $aResults[$r][15];

                            if ($dEventStartDate < date('Y-m-d')) {
                                $radioRegisterToParticipate = false;
                            }

                            if ($radioTemp === false) {
                                if ($radio_showBigCalendar) {
                                    echo "<span>" . $iCurrent . "</span>";
                                } else { ?>
                                    <a href="events.php?eid=<?= $iEventID ?>&date=<?= $dEventStartDate ?>"><?= $iCurrent ?></a>
                            <?php
                                }
                                echo "<div {$class_popup}>{$popup_close}";
                            }
                            if ($radioTemp) {
                                echo "<br>";
                            }
                            $radioTemp = true;
                            ?>
                            <span><a href="events.php?eid=<?= $iEventID ?>&date=<?= $dEventStartDate ?>"><?= $sEventTitle ?></a></span>

                            <?php
                            $strGoogleMap = "";
                            $str_PlaceTime = "";
                            if ($strParticipationMode != 'Online') {
                                $str_PlaceTime = "<span><strong>" . lngPlace . ":</strong> ";
                                if (!empty($sPlaceName)) {
                                    $strGoogleMap = $sPlaceName;
                                    $str_PlaceTime .= $sPlaceName . ", ";
                                }
                                if (!empty($sPlaceAddress)) {
                                    if (!empty($strGoogleMap)) {
                                        $strGoogleMap .= ", ";
                                    }
                                    $strGoogleMap .= $sPlaceAddress;
                                    $str_PlaceTime .= $sPlaceAddress . ", ";
                                }
                                if (!empty($sPlacePostalCode)) {
                                    if (!empty($strGoogleMap)) {
                                        $strGoogleMap .= ", ";
                                    }
                                    $strGoogleMap .= $sPlacePostalCode . " ";
                                }
                                if (!empty($sPlaceCity)) {
                                    $strGoogleMap = $strGoogleMap . $sPlaceCity;

                                    $str_PlaceTime .= $sPlaceCity . ", ";
                                }
                                if (!empty($strGoogleMap)) {
                                    $strGoogleMap = trim(rtrim($strGoogleMap, ","));
                                    $strGoogleMap = urlencode($strGoogleMap);
                                    $str_PlaceTime .= '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query=' . $strGoogleMap . '">Map</a>';
                                }
                                $str_PlaceTime .= '</span>';
                            } else {
                                $str_PlaceTime = "<span><strong>" . lngPlace . ":</strong> Online</span>";
                            }
                            if (!empty($sStartTime)) {
                                $str_PlaceTime .= "<span><strong>" . lngTime . ":</strong> " . $sStartTime;
                                if (!empty($sEndTime)) {
                                    $str_PlaceTime .= " - " . $sEndTime;
                                }
                                $str_PlaceTime .=  "</span>";
                            }

                            echo $str_PlaceTime;

                            if ($radioRegisterToParticipate) { ?>
                                <span>
                                    <a href="events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to participate »»</a>
                                </span>
                    <?php
                            }
                        }
                    }
                }
                if ($radioTemp) {
                    echo "</div></td>";
                } else {
                    echo "<span>" . $iCurrent . "</span></td>";
                }
                if ($iLoopWeekDay == 7) { ?>
        </tr>
        <tr>
        <?php
                    $iLoopWeekDay = 0;
                }
                $iLoopWeekDay++;
            }
            if ($iLoopWeekDay != 1) {
                $z = 1;
                while ($iLoopWeekDay <= 7) { ?>
            <td class="notDay"><span><?= $z ?></span></td>
        <?php
                    $iLoopWeekDay++;
                    $z++;
                } ?>
        </tr>
    <?php
            }
    ?>
    </table>

    <form class="align_right" action="events.php" name="calendarForm" method="post">
        <div>
            <select class="jqSelectEventYear" name="year">
                <?php
                $sql = "SELECT DISTINCT Year(EventStartDate) AS AsYear 
                    FROM events " . str_LanguageWhere . "
                    ORDER BY Year(EventStartDate) DESC";
                $stmt = $conn->query($sql);
                $i = 0;
                while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $loopYear = $rs["AsYear"];
                    $strSelected = "";
                    if ($loopYear == date('Y')) {
                        $strSelected = " selected";
                    } ?>
                    <option value="<?= $loopYear ?>" <?= $strSelected ?>><?= $loopYear ?></option>
                <?php
                    $i = 1;
                }
                $stmt = null;
                $rs = null
                ?>
            </select>
            <select class="jqSelectMonth" name="month">
                <?php
                for ($i = 1; $i < 13; $i++) {
                    $strName = lng_MonthNames[$i - 1];
                    $strSelected = "";
                    if ($i == date("n")) {
                        $strSelected = " selected";
                    } ?>
                    <option value="<?= $i ?>" <?= $strSelected ?>><?= $strName ?></option>
                <?php
                    if (!empty($strSelected) && return_Year($dDate) == Date("Y")) {
                        break;
                    }
                }
                ?>
            </select>
        </div>
        <input title="<?= lngListMonthsArticles ?>" type="Submit" value="&#x25BA;" name="go">
    </form>

    <script>
        // Get anly the months of a year that contain an event
        $sx(document).ready(function() {
            function get_active_months(year) {
                var $Data = "year=" + year;
                $sx.ajax({
                    url: "ajax_Events_SelectMonths.php",
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

            }
            $sx(".jqSelectEventYear").on("change", function() {
                //var $Data = "year=" + $sx(this).val();
                get_active_months($sx(this).val())
            });
            get_active_months($sx(".jqSelectEventYear").val())

        });
    </script>
</div>